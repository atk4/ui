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

    /**
     * Returns a suitable cell tag with the supplied value. Applies modifiers
     * added through addClass and setAttr.
     *
     * @param string $tag
     * @param string $position
     * @param string $value
     *
     * @return string
     */
    public function getTag($tag, $position, $value)
    {
        $attr = [];

        // "all" applies on all positions
        if (isset($this->attr['all'])) {
            $attr = array_merge_recursive($attr, $this->attr['all']);
        }

        // specific position classes
        if (isset($this->attr[$position])) {
            $attr = array_merge_recursive($attr, $this->attr[$position]);
        }

        if (isset($attr['class'])) {
            $attr['class'] = implode(' ', $attr['class']);
        }

        return $this->app->getTag($position == 'body' ? 'td' : 'th', $attr, $value);
    }

    /**
     * Provided with a field definition (from a model) will return a header
     * cell, fully formatted to be included in a Table. (<th>).
     *
     * Potentially may include elements for sorting.
     *
     * @param \atk4\data\Field $f
     *
     * @return string
     */
    public function getHeaderCellHTML(\atk4\data\Field $f = null)
    {
        if ($f === null) {
            return $this->getTag('th', 'head', '');
        }

        return $this->getTag('th', 'head', $f->getCaption());
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
        return $this->getTag('th', 'foot', $this->app->ui_persistence->typecastSaveField($f, $value));
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
    public function getDataCellHTML(\atk4\data\Field $f = null)
    {
        if ($f === null) {
            return $this->getTag('td', 'body', '{$c_'.$this->short_name.'}');
        }

        return $this->getTag('td', 'body', '{$'.$f->short_name.'}');
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
