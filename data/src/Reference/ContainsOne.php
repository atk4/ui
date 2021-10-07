<?php

declare(strict_types=1);

namespace Atk4\Data\Reference;

use Atk4\Data\Model;
use Atk4\Data\Persistence;
use Atk4\Data\Reference;

/**
 * ContainsOne reference.
 */
class ContainsOne extends Reference
{
    /**
     * Field type.
     *
     * @var string
     */
    public $type = 'json';

    /**
     * Is it system field?
     *
     * @var bool
     */
    public $system = true;

    /**
     * Array with UI flags like editable, visible and hidden.
     *
     * By default hasOne relation ID field should be editable in forms,
     * but not visible in grids. UI should respect these flags.
     *
     * @var array
     */
    public $ui = [];

    /**
     * Required! We need table alias for internal use only.
     *
     * @var string
     */
    protected $table_alias = 'tbl';

    /**
     * Reference\ContainsOne will also add a field corresponding
     * to 'our_field' unless it exists of course.
     */
    protected function init(): void
    {
        parent::init();

        if (!$this->our_field) {
            $this->our_field = $this->link;
        }

        $ourModel = $this->getOurModel();
        $ourField = $this->getOurFieldName();

        if (!$ourModel->hasElement($ourField)) {
            $ourModel->addField($ourField, [
                'type' => $this->type,
                'referenceLink' => $this->link,
                'system' => $this->system,
                'caption' => $this->caption, // it's ref models caption, but we can use it here for field too
                'ui' => array_merge([
                    'visible' => false, // not visible in UI Table, Grid and Crud
                    'editable' => true, // but should be editable in UI Form
                ], $this->ui),
            ]);
        }
    }

    protected function getDefaultPersistence(Model $theirModel): Persistence
    {
        return new Persistence\Array_([
            $this->table_alias => $this->getOurModel()->isEntity() && $this->getOurFieldValue() !== null ? [1 => $this->getOurFieldValue()] : [],
        ]);
    }

    /**
     * Returns referenced model with loaded data record.
     */
    public function ref(array $defaults = []): Model
    {
        $ourModel = $this->getOurModel();

        $theirModel = $this->createTheirModel(array_merge($defaults, [
            'contained_in_root_model' => $ourModel->contained_in_root_model ?: $ourModel,
            'table' => $this->table_alias,
        ]));

        foreach ([Model::HOOK_AFTER_SAVE, Model::HOOK_AFTER_DELETE] as $spot) {
            $this->onHookToTheirModel($theirModel, $spot, function ($theirModel) {
                $row = $theirModel->persistence->getRawDataByTable($theirModel, $this->table_alias);
                $row = $row ? array_shift($row) : null; // get first and only one record from array persistence
                $this->getOurModel()->save([$this->getOurFieldName() => $row]);
            });
        }

        $theirModel = $theirModel->tryLoadOne();

        return $theirModel;
    }
}
