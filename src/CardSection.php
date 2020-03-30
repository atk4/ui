<?php
/**
 * Display a card section within a Card View.
 */

namespace atk4\ui;

use atk4\data\Model;

class CardSection extends View
{
    public $card = null;

    public $useTableField = false;

    public $glue = ': ';

    public $tableClass = 'ui fixed small';

    public function init()
    {
        parent::init();
        $this->addClass('content');
    }

    /**
     * Add Description to card section.
     *
     * @param string|View $description
     *
     * @throws Exception
     *
     * @return View|string|null The description to add.
     */
    public function addDescription($description)
    {
        $view = null;

        if (is_scalar($description)) {
            $view = View::addTo($this, [$description, 'class' => ['description']]);
        } elseif ($description instanceof View) {
            $view = $this->add($description)->addClass('description');
        }

        return $view;
    }

    /**
     * Add Model fields to a card section.
     *
     * @param Model $m
     * @param array $fields
     * @param bool  $useLabel
     * @param bool  $useTable
     *
     * @throws Exception
     * @throws \atk4\data\Exception
     */
    public function addFields(Model $m, array $fields, $useLabel = false, $useTable = false)
    {
        if (!$m->loaded()) {
            throw new Exception('Model need to be loaded.');
        }

        if ($useTable) {
            $this->addTableSection($m, $fields);
        } else {
            $this->addSectionFields($m, $fields, $useLabel);
        }
    }

    /**
     * Add fields label and value to section.
     *
     * @param Model $m
     * @param array $fields
     * @param bool  $useLabel
     *
     * @throws Exception
     * @throws \atk4\data\Exception
     */
    private function addSectionFields(Model $m, array $fields, $useLabel = false)
    {
        foreach ($fields as $field) {
            $label = $m->getField($field)->getCaption();
            $value = $this->app ? $this->app->ui_persistence->typecastSaveField($m->getField($field), $m->get($field)) : $m->get($field);
            if ($useLabel) {
                $value = $label . $this->glue . $value;
            }

            $this->addDescription($value);
        }
    }

    /**
     * Add field into section using a CardTable View.
     *
     * @param Model $m
     * @param array $fields
     *
     * @throws Exception
     */
    private function addTableSection(Model $m, array $fields)
    {
        $c = new CardTable(['class' => $this->tableClass]);
        $c->init();
        $m = $c->setModel($m, $fields);
        $this->add($c);
    }
}
