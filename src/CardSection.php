<?php

declare(strict_types=1);

namespace Atk4\Ui;

use Atk4\Data\Model;

/**
 * Display a card section within a Card View.
 *
 * @property false $model use $entity property instead
 */
class CardSection extends View
{
    /** @var Card */
    public $card;

    /** @var string */
    public $glue = ': ';

    /** @var array<int, string> */
    public $tableClass = ['fixed', 'small'];

    #[\Override]
    protected function init(): void
    {
        parent::init();

        $this->addClass('content');
    }

    #[\Override]
    public function setModel(Model $entity, ?array $fields = null): void
    {
        $entity->assertIsEntity();

        parent::setModel($entity);
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
    public function addFields(Model $entity, array $fields, bool $useLabel = false, bool $useTable = false): void
    {
        $entity->assertIsLoaded();

        if ($useTable) {
            $this->addTableSection($entity, $fields);
        } else {
            $this->addSectionFields($entity, $fields, $useLabel);
        }
    }

    /**
     * Add fields label and value to section.
     */
    private function addSectionFields(Model $entity, array $fields, bool $useLabel = false): void
    {
        foreach ($fields as $field) {
            if ($entity->titleField === $field) {
                continue;
            }

            $value = $this->getApp()->uiPersistence->typecastSaveField($entity->getField($field), $entity->get($field));
            if ($useLabel) {
                $label = $entity->getField($field)->getCaption();
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
    private function addTableSection(Model $entity, array $fields): void
    {
        $cardTable = CardTable::addTo($this, ['class' => $this->tableClass]);
        $cardTable->setModel($entity, $fields);
    }
}
