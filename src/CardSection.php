<?php

declare(strict_types=1);
/**
 * Display a card section within a Card View.
 */

namespace Atk4\Ui;

use Atk4\Data\Model;

class CardSection extends View
{
    public $card;

    public $useTableField = false;

    public $glue = ': ';

    public $tableClass = ['ui', 'fixed', 'small'];

    protected function init(): void
    {
        parent::init();
        $this->addClass('content');
    }

    /**
     * Add Description to card section.
     *
     * @param string|View $description
     *
     * @return View|string|null the description to add
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
     */
    public function addFields(Model $model, array $fields, bool $useLabel = false, bool $useTable = false)
    {
        if (!$model->loaded()) {
            throw new Exception('Model need to be loaded.');
        }

        if ($useTable) {
            $this->addTableSection($model, $fields);
        } else {
            $this->addSectionFields($model, $fields, $useLabel);
        }
    }

    /**
     * Add fields label and value to section.
     */
    private function addSectionFields(Model $model, array $fields, bool $useLabel = false)
    {
        foreach ($fields as $field) {
            $label = $model->getField($field)->getCaption();
            $value = $this->issetApp() ? $this->getApp()->ui_persistence->typecastSaveField($model->getField($field), $model->get($field)) : $model->get($field);
            if ($useLabel) {
                $value = $label . $this->glue . $value;
            }

            $this->addDescription($value);
        }
    }

    /**
     * Add field into section using a CardTable View.
     */
    private function addTableSection(Model $model, array $fields)
    {
        $cardTable = CardTable::addTo($this, ['class' => $this->tableClass]);
        $cardTable->setModel($model, $fields);
    }
}
