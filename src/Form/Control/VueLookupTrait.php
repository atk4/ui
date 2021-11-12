<?php
/**
 * Trait for Control that use Vue Lookup component.
 */

declare(strict_types=1);

namespace Atk4\Ui\Form\Control;

use Atk4\Ui\Callback;

trait VueLookupTrait
{
    /** @var Callback */
    public $dataCb;

    public function initVueLookupCallback(): void
    {
        if (!$this->dataCb) {
            $this->dataCb = Callback::addTo($this);
        }
        $this->dataCb->set(\Closure::fromCallable([$this, 'outputApiResponse']));
    }

    /**
     * Output lookup search query data.
     *
     * @return never
     */
    public function outputApiResponse()
    {
        $fieldName = $_GET['atk_vlookup_field'] ?? null;
        $query = $_GET['atk_vlookup_q'] ?? null;
        $data = [];
        if ($fieldName) {
            $ref = $this->getModel()->getField($fieldName)->getReference();
            $model = $ref->refModel($this->model);
            $refFieldName = $ref->getTheirFieldName();
            if (!empty($query)) {
                $model->addCondition($model->title_field, 'like', '%' . $query . '%');
            }
            foreach ($model as $row) {
                $data[] = ['key' => $row->get($refFieldName), 'text' => $row->getTitle(), 'value' => $row->get($refFieldName)];
            }
        }

        $this->getApp()->terminateJson([
            'success' => true,
            'results' => $data,
        ]);
    }
}
