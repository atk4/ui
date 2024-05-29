<?php

declare(strict_types=1);

namespace Atk4\Ui\Table;

use Atk4\Core\AppScopeTrait;
use Atk4\Core\DiContainerTrait;
use Atk4\Core\InitializerTrait;
use Atk4\Core\NameTrait;
use Atk4\Core\TrackableTrait;
use Atk4\Data\Field;
use Atk4\Data\Model;
use Atk4\Ui\Exception;
use Atk4\Ui\Js\Jquery;
use Atk4\Ui\Js\JsExpression;
use Atk4\Ui\Js\JsExpressionable;
use Atk4\Ui\JsCallback;
use Atk4\Ui\Popup;
use Atk4\Ui\Table;
use Atk4\Ui\View;

/**
 * Implements Column helper for table.
 *
 * @method Table getOwner()
 */
class Column
{
    use AppScopeTrait;
    use DiContainerTrait;
    use InitializerTrait;
    use NameTrait;
    use TrackableTrait;

    public const HOOK_GET_HTML_TAGS = self::class . '@getHtmlTags';
    public const HOOK_GET_HEADER_CELL_HTML = self::class . '@getHeaderCellHtml';

    /** @var Table Link back to the table, where column is used. */
    public $table;

    /** @var array<'head'|'body'|'foot'|'all', array<string, string|list<string>>> Contains any custom attributes that may be applied on head, body or foot. */
    public array $attr = [];

    /** @var string|null If set, will override column header value. */
    public $caption;

    /** @var bool Is column sortable? */
    public $sortable = true;

    /** @var string|null The data-column attribute value for Table th tag. */
    public $columnData;

    /** @var bool Include header action tag in rendering or not. */
    public $hasHeaderAction = false;

    /** @var array|null The tag value required for getTag when using an header action. */
    public $headerActionTag;

    private string $nameInTableCache;

    public function __construct(array $defaults = [])
    {
        $this->setDefaults($defaults);
    }

    /**
     * Cloning View is unwanted as some references might be not cloned/set as expected,
     * remove this method once https://github.com/atk4/ui/issues/1365 is implemented.
     *
     * @internal
     */
    protected function assertColumnViewNotInitialized(View $view): void
    {
        if ($view->isInitialized() || ($view->name ?? null) !== null) {
            throw (new Exception('Unexpected initialized View instance'))
                ->addMoreInfo('view', $view);
        }
    }

    /**
     * @template T of View
     *
     * @param T $view
     *
     * @return T
     *
     * @internal
     */
    protected function cloneColumnView(View $view, Model $row, string $nameSuffix): View
    {
        $this->assertColumnViewNotInitialized($view);

        $cloneViewWithAddLaterFx = static function (View $view) use (&$cloneViewWithAddLaterFx) {
            $view = clone $view;

            \Closure::bind(static function () use ($view, $cloneViewWithAddLaterFx) {
                foreach ($view->_addLater as $k => [$obj]) {
                    $view->_addLater[$k][0] = $cloneViewWithAddLaterFx($obj); // @phpstan-ignore argument.type
                }
            }, null, View::class)();

            return $view;
        };

        if (!isset($this->nameInTableCache)) {
            foreach ($this->table->columns as $n => $columns) {
                foreach (is_array($columns) ? $columns : [$columns] as $k => $column) {
                    if ($this === $column) {
                        $this->nameInTableCache = $n . '_' . $k;

                        break;
                    }
                }
            }
        }

        $view = $cloneViewWithAddLaterFx($view);
        $view->shortName = 'c' . $this->nameInTableCache . '_' . $nameSuffix . '_r'
            . $this->getApp()->uiPersistence->typecastAttributeSaveField($row->getIdField(), $row->getId());
        $view->name = \Closure::bind(static fn (Table $table) => $view->_shorten($table->name, $view->shortName, null), null, Table::class)($this->table);

        return $view;
    }

    /**
     * Add popup to header.
     * Use ColumnName for better popup positioning.
     *
     * @param string $icon CSS class for filter icon
     *
     * @return mixed
     */
    public function addPopup(?Popup $popup = null, $icon = 'table-filter-off')
    {
        $id = $this->name . '_ac';

        $popup = $this->table->getOwner()->add($popup ?? [Popup::class])->setHoverable();

        $this->setHeaderPopup($icon, $id);

        $popup->triggerBy = '#' . $id;
        $popup->popOptions = array_merge(
            $popup->popOptions,
            [
                'on' => 'click',
                'position' => 'bottom left',
                'movePopup' => $this->columnData ? true : false,
                'target' => $this->columnData ? 'th[data-column=' . $this->columnData . ']' : false,
                'distanceAway' => -12,
            ]
        );
        $popup->stopClickEvent = true;

        return $popup;
    }

    /**
     * Setup popup header action.
     *
     * @param string $class the CSS class for filter icon
     * @param string $id
     */
    public function setHeaderPopup($class, $id): void
    {
        $this->hasHeaderAction = true;

        $this->headerActionTag = ['div', ['class' => 'atk-table-dropdown'],
            [
                ['i', ['id' => $id, 'class' => $class . ' icon'], ''],
            ],
        ];
    }

    /**
     * Set header popup icon.
     *
     * @param string $icon
     */
    public function setHeaderPopupIcon($icon): void
    {
        $this->headerActionTag = ['div', ['class' => 'atk-table-dropdown'],
            [
                ['i', ['id' => $this->name . '_ac', 'class' => $icon . ' icon'], ''],
            ],
        ];
    }

    /**
     * Add a dropdown header menu.
     *
     * @param \Closure(string, string): (JsExpressionable|View|string|void) $fx
     * @param string                                                        $icon
     * @param string|null                                                   $menuId the menu name
     */
    public function addDropdown(array $items, \Closure $fx, $icon = 'caret square down', $menuId = null): void
    {
        $menuItems = [];
        foreach ($items as $key => $item) {
            $menuItems[] = ['name' => is_int($key) ? $item : $key, 'value' => $item];
        }

        $cb = $this->setHeaderDropdown($menuItems, $icon, $menuId);

        $cb->onSelectItem(static function (string $menu, string $item) use ($fx) {
            return $fx($item, $menu);
        });
    }

    /**
     * Setup dropdown header action.
     * This method return a callback where you can detect
     * menu item change via $cb->onMenuItem($item) function.
     *
     * @param array<int, array> $items
     *
     * @return Column\JsHeaderDropdownCallback
     */
    public function setHeaderDropdown($items, string $icon = 'caret square down', ?string $menuId = null): JsCallback
    {
        $this->hasHeaderAction = true;
        $id = $this->name . '_ac';
        $this->headerActionTag = ['div', ['class' => 'atk-table-dropdown'], [
            [
                'div', ['id' => $id, 'class' => 'ui top left pointing dropdown', 'data-menu-id' => $menuId],
                [['i', ['class' => $icon . ' icon'], '']],
            ],
        ]];

        $cb = Column\JsHeaderDropdownCallback::addTo($this->table);

        $function = new JsExpression('function (value, text, item) {
            if (value === undefined || value === \'\' || value === null) {
                return;
            }
            $(this).api({
                on: \'now\',
                url: \'' . $cb->getJsUrl() . '\',
                data: { item: value, id: $(this).data(\'menu-id\') }
            });
        }');

        $chain = new Jquery('#' . $id);
        $chain->dropdown([
            'action' => 'hide',
            'values' => $items,
            'onChange' => $function,
        ]);

        // will stop grid column from being sorted
        $chain->on('click', new JsExpression('function (e) { e.stopPropagation(); }'));

        $this->table->js(true, $chain);

        return $cb;
    }

    /**
     * Adds a new class to the cells of this column.
     *
     * @param string                     $class
     * @param 'head'|'body'|'foot'|'all' $position
     *
     * @return $this
     */
    public function addClass($class, $position = 'body')
    {
        $this->attr[$position]['class'][] = $class;

        return $this;
    }

    /**
     * Adds a new attribute to the cells of this column.
     *
     * You can also use the "{$name}" value if you wish to specific row value:
     *
     *    $table->column['name']->setAttr('data', '{$id}');
     *
     * @param string                     $attr
     * @param string                     $value
     * @param 'head'|'body'|'foot'|'all' $position
     *
     * @return $this
     */
    public function setAttr($attr, $value, $position = 'body')
    {
        $this->attr[$position][$attr] = $value;

        return $this;
    }

    /**
     * @param array<string, string|list<string>> ...$attributesArr
     */
    protected function mergeTagAttributes(array ...$attributesArr): array
    {
        $res = [];
        foreach ($attributesArr as $attributes) {
            foreach ($attributes as $k => $v) {
                if (is_string($v)) {
                    $res[$k] = $v;
                } else {
                    $res[$k] = array_merge($res[$k] ?? [], $v);
                }
            }
        }

        return $res;
    }

    /**
     * @param 'head'|'body'|'foot' $position
     */
    public function getTagAttributes(string $position, array $attr = []): array
    {
        return $this->mergeTagAttributes(
            $this->attr['all'] ?? [],
            $this->attr[$position] ?? [],
            $attr
        );
    }

    /**
     * Returns a suitable cell tag with the supplied value. Applies modifiers
     * added through addClass and setAttr.
     *
     * @param 'head'|'body'|'foot'                                                                                     $position
     * @param array<string, string|bool|array<string>>                                                                 $attr
     * @param string|array<int, array{0: string, 1?: array<0|string, string|bool>, 2?: string|array|null}|string>|null $value
     */
    public function getTag(string $position, $attr, $value): string
    {
        $attr = $this->getTagAttributes($position, $attr);

        if (isset($attr['class'])) {
            $attr['class'] = implode(' ', $attr['class']);
        }

        return $this->getApp()->getTag($position === 'body' ? 'td' : 'th', $attr, $value);
    }

    /**
     * Provided with a field definition (from a model) will return a header
     * cell, fully formatted to be included in a Table. (<th>).
     *
     * @param mixed $value
     */
    public function getHeaderCellHtml(?Field $field = null, $value = null): string
    {
        $tags = $this->table->hook(self::HOOK_GET_HEADER_CELL_HTML, [$this, $field, $value]);
        if ($tags) {
            return reset($tags);
        }

        if ($field === null) {
            return $this->getTag('head', $this->table->sortable ? ['class' => ['disabled']] : [], $this->caption ?? '');
        }

        // if $this->caption is empty, header caption will be overridden by linked field definition
        $caption = $this->caption ?? $field->getCaption();

        $attr = [
            'data-column' => $this->columnData,
        ];

        $class = 'atk-table-column-header';

        if ($this->hasHeaderAction) {
            $attr['id'] = $this->name . '_th';

            // add the action tag to the caption
            $caption = [$this->headerActionTag, $this->getApp()->encodeHtml($caption)];
        }

        if ($this->table->sortable) {
            $attr['data-sort'] = $field->shortName;

            if ($this->sortable) {
                $attr['class'] = ['sortable'];
            }

            // if table is being sorted by THIS column, set the proper class
            if ($this->table->sortBy === $field->shortName) {
                $class .= ' sorted ' . ['asc' => 'ascending', 'desc' => 'descending'][$this->table->sortDirection];

                if ($this->table->sortDirection === 'asc') {
                    $attr['data-sort'] = '-' . $attr['data-sort'];
                } elseif ($this->table->sortDirection === 'desc') {
                    $attr['data-sort'] = '';
                }
            }
        }

        return $this->getTag('head', $attr, [['div', ['class' => $class], $caption]]);
    }

    /**
     * Return HTML for a total value of a specific field.
     *
     * @param mixed $value
     */
    public function getTotalsCellHtml(Field $field, $value): string
    {
        return $this->getTag('foot', [], $this->getApp()->uiPersistence->typecastSaveField($field, $value));
    }

    /**
     * Provided with a field definition will return a string containing a "Template"
     * that would produce <td> cell when rendered. Example output:.
     *
     *   <td><b>{$name}</b></td>
     *
     * The must correspond to the name of the field, although you can also use multiple tags. The tag
     * will also be formatted before inserting, see UI Persistence formatting in the documentation.
     *
     * If you need to format data manually, you can use $this->table->onHook(Lister::HOOK_BEFORE_ROW or Lister::HOOK_AFTER_ROW, ...);
     */
    public function getDataCellHtml(?Field $field = null, array $attr = []): string
    {
        return $this->getTag('body', $attr, [$this->getDataCellTemplate($field)]);
    }

    /**
     * Provided with a field definition will return a string containing a "Template"
     * that would produce CONTENTS OF <td> cell when rendered. Example output:.
     *
     *   <b>{$name}</b>
     *
     * The tag that corresponds to the name of the field (e.g. {$name}) may be substituted
     * by another template returned by getDataCellTemplate when multiple formatters are
     * applied to the same column. The first one to be applied is executed first, then
     * a subsequent ones are executed.
     */
    public function getDataCellTemplate(?Field $field = null): string
    {
        if ($field) {
            return '{$' . $field->shortName . '}';
        }

        return '{_$' . $this->shortName . '}';
    }

    /**
     * Return associative array of tags to be filled with pre-rendered HTML on
     * a column-basis. Will not be invoked if HTML output is turned off for the table.
     *
     * @return array<string, string>
     */
    public function getHtmlTags(Model $row, ?Field $field): array
    {
        return [];
    }
}
