<?php

declare(strict_types=1);

namespace Atk4\Ui;

use Atk4\Data\Model;

/**
 * Display a card section within a Card View.
 */
class CardSection extends View
{
    /** @var Card */
    public $card;

    /** @var string */
    public $glue = ': ';

    /** @var array<int, string> */
    public $tableClass = ['fixed', 'small'];

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
     * @return View
     */
    public function addDescription($description)
    {
        $view = null;

        if (is_string($description)) {
            $view = View::addTo($this, [$description, 'class' => ['description']]);
        } else {
            $view = $this->add($description)->addClass('description');
        }

        return $view;
    }

    /**
     * Add Model fields to a card section.
     */
    public function addFields(Model $model, array $fields, bool $useLabel = false, bool $useTable = false): void
    {
        $model->assertIsLoaded();

        if ($useTable) {
            $this->addTableSection($model, $fields);
        } else {
            $this->addSectionFields($model, $fields, $useLabel);
        }
    }

    /**
     * Add fields label and value to section.
     */
    private function addSectionFields(Model $model, array $fields, bool $useLabel = false): void
    {
        foreach ($fields as $field) {
            if ($model->titleField === $field) {
                continue;
            }

            $value = $this->getApp()->uiPersistence->typecastSaveField($model->getField($field), $model->get($field));
            if ($useLabel) {
                $label = $model->getField($field)->getCaption();
                $value = $label . $this->glue . $value;
            }

            if ($value) {
                $this->addDescription($value);
            }
        }
    }

    /**
     * Add field into section using a CardTable View.
     */
    private function addTableSection(Model $model, array $fields): void
    {
        $cardTable = CardTable::addTo($this, ['class' => $this->tableClass]);
        $cardTable->setModel($model, $fields);
    }
}
