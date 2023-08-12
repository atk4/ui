<?php

namespace atk4\ui\TableColumn;

use atk4\ui\Exception;
use atk4\ui\jQuery;
use atk4\ui\jsExpression;
use atk4\ui\Popup;

/**
 * Implements Column helper for table.
 */
class Generic
{
    use \atk4\core\AppScopeTrait;
    use \atk4\core\InitializerTrait;
    use \atk4\core\TrackableTrait;
    use \atk4\core\DIContainerTrait;

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
    public $caption = null;

    /**
     * Include header action tag in rendering or not.
     *
     * @var bool
     */
    public $hasHeaderAction = false;

    public $headerAction = null;

    /**
     * The tag value required for getTag when using an header action.
     *
     * @var array|null
     */
    public $headerActionTag = null;

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
     *
     * @param Popup  $popup
     * @param string $id
     * @param string $icon
     *
     * @throws Exception
     *
     * @return mixed
     */
    public function addPopup($popup = null, $icon = 'caret square down')
    {
        if (!$this->app) {
            throw new Exception('Columns\'s popup need to have a layout.');
        }
        if (!$popup) {
            $popup = $this->app->add('Popup')->setHoverable();
        }

        $this->setHeaderPopup($popup, $icon);

        return $popup;
    }

    /**
     * Setup popup header action.
     *
     * @param Popup $popup
     * @param $icon
     */
    public function setHeaderPopup($popup, $icon = 'caret square down')
    {
        $this->headerAction = true;
        $id = $this->name.'_ac';

        $this->headerActionTag = ['div',  ['class'=>'atk-table-dropdown'],
            [
                ['i', ['id' => $id, 'class' => $icon.' icon']],
            ],
        ];
        $popup->triggerBy = '#'.$id;
        $popup->popOptions = array_merge($popup->popOptions, ['on' =>'click', 'position' => 'bottom right', 'movePopup' => false]);
        $popup->stopClickEvent = true;

        if (@$_GET['__atk_reload']) {
            //This is part of a reload, need to reactivate popup.
            $this->table->js(true, $popup->jsPopup());
        }
    }

    /**
     * Add a dropdown header menu.
     *
     * @param array       $items
     * @param callable    $fx
     * @param string      $icon
     * @param string|null $menuId The menu name.
     *
     * @throws Exception
     */
    public function addDropdown($items, $fx, $icon = 'caret square down', $menuId = null)
    {
        $menuITems = [];
        foreach ($items as $key => $item) {
            if (is_int($key)) {
                $menuITems[] = ['name' => $item, 'value' => $item];
            } else {
                $menuITems[] = ['name' => $key, 'value' => $item];
            }
        }

        $cb = $this->setHeaderDropdown($menuITems, $icon, $menuId);

        $cb->onSelectItem(function ($menu, $item) use ($fx) {
            return call_user_func($fx, $item, $menu);
        });
    }

    /**
     * Setup dropdown header action.
     * This method return a callback where you can detect
     * menu item change via $cb->onMenuItem($item) function.
     *
     * @param             $items
     * @param string      $icon
     * @param string|null $menuId The id of the menu.
     *
     * @throws Exception
     *
     * @return \atk4\ui\jsCallback
     */
    public function setHeaderDropdown($items, $icon = 'caret square down', $menuId = null)
    {
        $this->headerAction = true;
        $id = $this->name.'_ac';
        $this->headerActionTag = ['div',  ['class'=>'atk-table-dropdown'],
            [
                [
                    'div', ['id' => $id, 'class'=>'ui top right pointing dropdown', 'data-menu-id' => $menuId],
                    [['i', ['class' => $icon.' icon']]],
                ],
            ],
        ];

        $cb = $this->table->add(new jsHeader());

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

        $chain = new jQuery('#'.$id);
        $chain->dropdown([
                             'action'   => 'hide',
                             'values'   => $items,
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
        if (isset($this->attr['all'])) {
            $attr = array_merge_recursive($attr, $this->attr['all']);
        }

        // specific position classes
        if (isset($this->attr[$position])) {
            $attr = array_merge_recursive($attr, $this->attr[$position]);
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

        return $this->app->getTag($position == 'body' ? 'td' : 'th', $attr, $value);
    }

    /**
     * Provided with a field definition (from a model) will return a header
     * cell, fully formatted to be included in a Table. (<th>).
     *
     * @param \atk4\data\Field $f
     *
     * @return string
     */
    public function getHeaderCellHTML(\atk4\data\Field $f = null, $value = null)
    {
        if (!$this->table) {
            throw new \atk4\ui\Exception(['How $table could not be set??', 'f' => $f, 'value' => $value]);
        }
        if ($f === null) {
            return $this->getTag('head', $this->caption ?: '', $this->table->sortable ? ['class' => ['disabled']] : []);
        }

        // If table is being sorted by THIS column, set the proper class
        $attr = [];
        if ($this->table->sortable) {
            $attr['data-column'] = $f->short_name;

            if ($this->table->sort_by === $f->short_name) {
                $attr['class'][] = 'sorted '.$this->table->sort_order;

                if ($this->table->sort_order === 'ascending') {
                    $attr['data-column'] = '-'.$f->short_name;
                } elseif ($this->table->sort_order === 'descending') {
                    $attr['data-column'] = '';
                }
            }
        }

        if ($this->headerAction) {
            $attr = array_merge($attr, ['id' => $this->name.'_th']);
            $tag = $this->getTag(
                'head',
                [$f->getCaption(),
                    $this->headerActionTag,
                ],
                $attr
            );
        } else {
            $tag = $this->getTag(
                'head',
                $f->getCaption(),
                $attr
            );
        }

        return $tag;
    }

    /**
     * Return HTML for a total value of a specific field.
     *
     * @param \atk4\data\Field $f
     * @param mixed            $value
     * @param bool             $typecast Should we typecast value
     *
     * @return string
     */
    public function getTotalsCellHTML(\atk4\data\Field $f, $value, $typecast = true)
    {
        if ($typecast) {
            $value = $this->app->ui_persistence->typecastSaveField($f, $value);
        }

        return $this->getTag('foot', $value);
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
     * you should use $this->table->addHook('formatRow');
     *
     * @param \atk4\data\Field $f
     *
     * @return string
     */
    public function getDataCellHTML(\atk4\data\Field $f = null, $extra_tags = [])
    {
        return $this->getTag('body', [$this->getDataCellTemplate($f)], $extra_tags);
    }

    /**
     * Provided with a field definition will return a string containing a "Template"
     * that would produce CONTENS OF <td> cell when rendered. Example output:.
     *
     *   <b>{$name}</b>
     *
     * The tag that corresponds to the name of the field (e.g. {$name}) may be substituted
     * by another template returned by getDataCellTemplate when multiple formatters are
     * applied to the same column. The first one to be applied is executed first, then
     * a subsequent ones are executed.
     *
     * @param \atk4\data\Field $f
     *
     * @return string
     */
    public function getDataCellTemplate(\atk4\data\Field $f = null)
    {
        if ($f) {
            return '{$'.$f->short_name.'}';
        } else {
            return '{_$'.$this->short_name.'}';
        }
    }

    /**
     * Return associative array of tags to be filled with pre-rendered HTML on
     * a column-basis. Will not be invoked if html-output is turned off for the table.
     *
     * @param array  $row   link to row data
     * @param string $field field being rendered
     *
     * @return array Associative array with tags and their HTML values.
     */
    public function getHTMLTags($row, $field)
    {
        return [];
    }
}
