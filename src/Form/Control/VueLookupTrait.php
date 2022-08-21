<?php

declare(strict_types=1);

namespace Atk4\Ui\Form\Control;

use Atk4\Ui\Callback;

trait VueLookupTrait
{
    /** @var Callback|null */
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
            $reference = $this->getModel()->getField($fieldName)->getReference();
            $model = $reference->refModel($this->model);
            $referenceFieldName = $reference->getTheirFieldName();
            if (!empty($query)) {
                $model->addCondition($model->titleField, 'like', '%' . $query . '%');
            }
            foreach ($model as $row) {
                $data[] = ['key' => $row->get($referenceFieldName), 'text' => $row->getTitle(), 'value' => $row->get($referenceFieldName)];
            }
        }

        $this->getApp()->terminateJson([
            'success' => true,
            'results' => $data,
        ]);
    }
}
