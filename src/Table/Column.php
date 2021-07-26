<?php

declare(strict_types=1);

namespace Atk4\Ui\Table;

use Atk4\Data\Field;
use Atk4\Data\Model;
use Atk4\Ui\Exception;
use Atk4\Ui\Jquery;
use Atk4\Ui\JsExpression;
use Atk4\Ui\Popup;

/**
 * Implements Column helper for table.
 *
 * @method \Atk4\Ui\Table getOwner()
 */
class Column
{
    use \Atk4\Core\AppScopeTrait;
    use \Atk4\Core\DiContainerTrait;
    use \Atk4\Core\InitializerTrait;
    use \Atk4\Core\TrackableTrait;

    /** @const string */
    public const HOOK_GET_HTML_TAGS = self::class . '@getHtmlTags';
    /** @const string */
    public const HOOK_GET_HEADER_CELL_HTML = self::class . '@getHeaderCellHtml';

    /**
     * Link back to the table, where column is used.
     *
     * @var \Atk4\Ui\Table
     */
    public $table;

    /**
     * Contains any custom attributes that may be applied on head, body or foot.
     *
     * @var array
     */
    public $attr = [];

    /**
     * If set, will override column header value.
     *
     * @var string
     */
    public $caption;

    /**
     * Is column sortable?
     *
     * @var bool
     */
    public $sortable = true;

    /**
     * The data-column attribute value for Table th tag.
     *
     * @var string|null
     */
    public $columnData;

    /**
     * Include header action tag in rendering or not.
     *
     * @var bool
     */
    public $hasHeaderAction = false;

    /**
     * The tag value required for getTag when using an header action.
     *
     * @var array|null
     */
    public $headerActionTag;

    /**
     * Constructor.
     *
     * @param array $defaults
     */
    public function __construct($defaults = [])
    {
        $this->setDefaults($defaults);
    }

    /**
     * Add popup to header.
     * Use ColumnName for better popup positioning.
     *
     * @param string $icon CSS class for filter icon
     *
     * @return mixed
     */
    public function addPopup(Popup $popup = null, $icon = 'table-filter-off')
    {
        $id = $this->name . '_ac';

        $popup = $this->table->getOwner()->add($popup ?: [Popup::class])->setHoverable();

        $this->setHeaderPopup($icon, $id);

        $popup->triggerBy = '#' . $id;
        $popup->popOptions = array_merge(
            $popup->popOptions,
            [
                'on' => 'click',
                'position' => 'bottom left',
                'movePopup' => $this->columnData ? true : false,
                'target' => $this->columnData ? 'th[data-column=' . $this->columnData . ']' : false,
                'distanceAway' => 10,
                'offset' => -2,
            ]
        );
        $popup->stopClickEvent = true;

        return $popup;
    }

    /**
     * Setup popup header action.
     *
     * @param string $class the css class for filter icon
     */
    public function setHeaderPopup($class, $id)
    {
        $this->hasHeaderAction = true;

        $this->headerActionTag = ['div',  ['class' => 'atk-table-dropdown'],
            [
                ['i', ['id' => $id, 'class' => $class . ' icon'], ''],
            ],
        ];
    }

    /**
     * Set header popup icon.
     */
    public function setHeaderPopupIcon($icon)
    {
        $this->headerActionTag = ['div',  ['class' => 'atk-table-dropdown'],
            [
                ['i', ['id' => $this->name . '_ac', 'class' => $icon . ' icon'], ''],
            ],
        ];
    }

    /**
     * Add a dropdown header menu.
     *
     * @param string      $icon
     * @param string|null $menuId the menu name
     */
    public function addDropdown(array $items, \Closure $fx, $icon = 'caret square down', $menuId = null)
    {
        $menuItems = [];
        foreach ($items as $key => $item) {
            $menuItems[] = ['name' => is_int($key) ? $item : $key, 'value' => $item];
        }

        $cb = $this->setHeaderDropdown($menuItems, $icon, $menuId);

        $cb->onSelectItem(function ($menu, $item) use ($fx) {
            return $fx($item, $menu);
        });
    }

    /**
     * Setup dropdown header action.
     * This method return a callback where you can detect
     * menu item change via $cb->onMenuItem($item) function.
     *
     * @return \Atk4\Ui\JsCallback
     */
    public function setHeaderDropdown($items, string $icon = 'caret square down', string $menuId = null)
    {
        $this->hasHeaderAction = true;
        $id = $this->name . '_ac';
        $this->headerActionTag = ['div',  ['class' => 'atk-table-dropdown'],
            [
                [
                    'div', ['id' => $id, 'class' => 'ui top left pointing dropdown', 'data-menu-id' => $menuId],
                    [['i', ['class' => $icon . ' icon'], '']],
                ],
            ],
        ];

        $cb = Column\JsHeader::addTo($this->table);

        $function = 'function(value, text, item){
                            if (value === undefined || value === \'\' || value === null) return;
                            $(this)
                            .api({
                                on:\'now\',
                                url:\'' . $cb->getJsUrl() . '\',
                                data:{item:value, id:$(this).data(\'menu-id\')}
                                }
                            );
                     }';

        $chain = new Jquery('#' . $id);
        $chain->dropdown([
            'action' => 'hide',
            'values' => $items,
            'onChange' => new JsExpression($function),
        ]);

        // will stop grid column from being sorted.
        $chain->on('click', new JsExpression('function(e){e.stopPropagation();}'));

        $this->table->js(true, $chain);

        return $cb;
    }

    /**
     * Adds a new class to the cells of this column. The optional second argument may be "head",
     * "body" or "foot". If position is not defined, then class will be applied on all cells.
     *
     * @param string $class
     * @param string $position
     *
     * @return $this
     */
    public function addClass($class, $position = 'body')
    {
        $this->attr[$position]['class'][] = $class;

        return $this;
    }

    /**
     * Adds a new attribute to the cells of this column. The optional second argument may be "head",
     * "body" or "foot". If position is not defined, then attribute will be applied on all cells.
     *
     * You can also use the "{$name}" value if you wish to specific row value:
     *
     *    $table->column['name']->setAttr('data', '{$id}');
     *
     * @param string $attr
     * @param string $value
     * @param string $position
     *
     * @return $this
     */
    public function setAttr($attr, $value, $position = 'body')
    {
        $this->attr[$position][$attr] = $value;

        return $this;
    }

    public function getTagAttributes($position, array $attr = []): array
    {
        // "all" applies on all positions
        // $position is for specific position classes
        foreach (['all', $position] as $key) {
            if (isset($this->attr[$key])) {
                $attr = array_merge_recursive($attr, $this->attr[$key]);
            }
        }

        return $attr;
    }

    /**
     * Returns a suitable cell tag with the supplied value. Applies modifiers
     * added through addClass and setAttr.
     *
     * @param string       $position 'head', 'body' or 'tail'
     * @param string|array $value    either html or array defining HTML structure, see App::getTag help
     * @param array        $attr     extra attributes to apply on the tag
     */
    public function getTag($position, $value, $attr = []): string
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
     * @param Field $field
     * @param mixed $value
     *
     * @return string
     */
    public function getHeaderCellHtml(Field $field = null, $value = null)
    {
        if (!$this->table) {
            throw (new Exception('How $table could not be set??'))
                ->addMoreInfo('field', $field)
                ->addMoreInfo('value', $value);
        }

        if ($tags = $this->table->hook(self::HOOK_GET_HEADER_CELL_HTML, [$this, $field, $value])) {
            return reset($tags);
        }

        if ($field === null) {
            return $this->getTag('head', $this->caption ?: '', $this->table->sortable ? ['class' => ['disabled']] : []);
        }

        // if $this->caption is empty, header caption will be overriden by linked field definition
        $caption = $this->caption ?: $field->getCaption();

        $attr = [
            'data-column' => $this->columnData,
        ];

        $class = 'atk-table-column-header';

        if ($this->hasHeaderAction) {
            $attr['id'] = $this->name . '_th';

            // add the action tag to the caption
            $caption = [$this->headerActionTag, $caption];
        }

        if ($this->table->sortable) {
            $attr['data-sort'] = $field->short_name;

            if ($this->sortable) {
                $attr['class'] = ['sortable'];
            }

            // If table is being sorted by THIS column, set the proper class
            if ($this->table->sort_by === $field->short_name) {
                $class .= ' sorted ' . $this->table->sort_order;

                if ($this->table->sort_order === 'ascending') {
                    $attr['data-sort'] = '-' . $field->short_name;
                } elseif ($this->table->sort_order === 'descending') {
                    $attr['data-sort'] = '';
                }
            }
        }

        return $this->getTag('head', [['div', ['class' => $class], $caption]], $attr);
    }

    /**
     * Return HTML for a total value of a specific field.
     *
     * @param mixed $value
     *
     * @return string
     */
    public function getTotalsCellHtml(Field $field, $value)
    {
        return $this->getTag('foot', $this->getApp()->ui_persistence->typecastSaveField($field, $value));
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
     * This method will be executed only once per table rendering, if you need to format data manually,
     * you should use $this->table->onHook('beforeRow' or 'afterRow', ...);
     *
     * @param Field $field
     * @param array $extra_tags
     *
     * @return string
     */
    public function getDataCellHtml(Field $field = null, $extra_tags = [])
    {
        return $this->getTag('body', [$this->getDataCellTemplate($field)], $extra_tags);
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
     *
     * @param Field $field
     *
     * @return string
     */
    public function getDataCellTemplate(Field $field = null)
    {
        if ($field) {
            return '{$' . $field->short_name . '}';
        }

        return '{_$' . $this->short_name . '}';
    }

    /**
     * Return associative array of tags to be filled with pre-rendered HTML on
     * a column-basis. Will not be invoked if html-output is turned off for the table.
     *
     * @param Model      $row   link to row data
     * @param Field|null $field field being rendered
     *
     * @return array associative array with tags and their HTML values
     */
    public function getHtmlTags(Model $row, $field)
    {
        return [];
    }
}
