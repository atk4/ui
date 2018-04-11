<?php

namespace atk4\ui\TableColumn;

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
     * Constructor.
     *
     * @param array $defaults
     */
    public function __construct($defaults = [])
    {
        $this->setDefaults($defaults);
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

        if (!empty($this->table->columMenus)) {
            $tag =  $this->getTag(
                'head',
                [ $f->getCaption(),
                    ['div',  ['class'=>'atk-table-dropdown'],
                        [
                            [
                                'div', ['class'=>'ui top right pointing dropdown'],
                                [['i', ['class' => 'caret square down icon']]]
                            ]
                        ]
                    ],
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
     *
     * @return string
     */
    public function getTotalsCellHTML(\atk4\data\Field $f, $value)
    {
        return $this->getTag('foot', $this->app->ui_persistence->typecastSaveField($f, $value));
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
