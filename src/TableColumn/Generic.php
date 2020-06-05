<?php

namespace atk4\ui\TableColumn;

use atk4\data\Field;
use atk4\data\Model;
use atk4\ui\Exception;
use atk4\ui\jQuery;
use atk4\ui\jsExpression;
use atk4\ui\Popup;

/**
 * Implements Column helper for table.
 *
 * @property \atk4\ui\Table $owner
 */
class Generic
{
    use \atk4\core\AppScopeTrait;
    use \atk4\core\InitializerTrait;
    use \atk4\core\TrackableTrait;
    use \atk4\core\DIContainerTrait;

    /** @const string */
    public const HOOK_GET_HTML_TAGS = self::class . '@getHTMLTags';
    /** @const string not used, make it public if needed or drop it */
    private const HOOK_GET_HEADER_CELL_HTML = self::class . '@getHeaderCellHTML';

    /**
     * Link back to the table, where column is used.
     *
     * @var \atk4\ui\Table
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
     * @var null
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

        $popup = $this->table->owner->add($popup ?: 'Popup')->setHoverable();

        $this->setHeaderPopup($icon, $id);

        $popup->triggerBy = '#' . $id;
        $popup->popOptions = array_merge(
            $popup->popOptions,
            [
                'on' => 'click',
                'position' => 'bottom left',
                'movePopup' => $this->columnData ? true : false,
                'target' => $this->columnData ? "th[data-column={$this->columnData}]" : false,
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
     * @param array       $items
     * @param callable    $fx
     * @param string      $icon
     * @param string|null $menuId the menu name
     *
     * @throws Exception
     */
    public function addDropdown($items, $fx, $icon = 'caret square down', $menuId = null)
    {
        $menuItems = [];
        foreach ($items as $key => $item) {
            $menuItems[] = ['name' => is_int($key) ? $item : $key, 'value' => $item];
        }

        $cb = $this->setHeaderDropdown($menuItems, $icon, $menuId);

        $cb->onSelectItem(function ($menu, $item) use ($fx) {
            return call_user_func($fx, $item, $menu);
        });
    }

    /**
     * Setup dropdown header action.
     * This method return a callback where you can detect
     * menu item change via $cb->onMenuItem($item) function.
     *
     * @throws Exception
     *
     * @return \atk4\ui\jsCallback
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

        $cb = jsHeader::addTo($this->table);

        $function = "function(value, text, item){
                            if (value === undefined || value === '' || value === null) return;
                            $(this)
                            .api({
                                on:'now',
                                url:'{$cb->getJSURL()}',
                                data:{item:value, id:$(this).data('menu-id')}
                                }
                            );
                     }";

        $chain = new jQuery('#' . $id);
        $chain->dropdown([
            'action' => 'hide',
            'values' => $items,
            'onChange' => new jsExpression($function),
        ]);

        //will stop grid column from being sorted.
        $chain->on('click', new jsExpression('function(e){e.stopPropagation();}'));

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

    public function getTagAttributes($position, $attr = [])
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
     * @param string $position - 'head', 'body' or 'tail'
     * @param string $value    - what is inside? either html or array defining HTML structure, see App::getTag help
     * @param array  $attr     - extra attributes to apply on the tag
     *
     * @return string
     */
    public function getTag($position, $value, $attr = [])
    {
        $attr = $this->getTagAttributes($position, $attr);

        if (isset($attr['class'])) {
            $attr['class'] = implode(' ', $attr['class']);
        }

        return $this->app->getTag($position === 'body' ? 'td' : 'th', $attr, $value);
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
    public function getHeaderCellHTML(Field $field = null, $value = null)
    {
        if (!$this->table) {
            throw new \atk4\ui\Exception(['How $table could not be set??', 'field' => $field, 'value' => $value]);
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

            //add the action tag to the caption
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

        return $this->getTag('head', [['div', compact('class'), $caption]], $attr);
    }

    /**
     * Return HTML for a total value of a specific field.
     *
     * @param mixed $value
     *
     * @return string
     */
    public function getTotalsCellHTML(Field $field, $value)
    {
        return $this->getTag('foot', $this->app->ui_persistence->typecastSaveField($field, $value));
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
    public function getDataCellHTML(Field $field = null, $extra_tags = [])
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
    public function getHTMLTags(Model $row, $field)
    {
        return [];
    }
}
