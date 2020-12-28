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

    public function initVueLookupCallback()
    {
        if (!$this->dataCb) {
            $this->dataCb = Callback::addTo($this);
        }
        $this->dataCb->set([$this, 'outputApiResponse']);
    }

    /**
     * Output lookup search query data.
     */
    public function outputApiResponse()
    {
        $fieldName = $_GET['atk_vlookup_field'] ?? null;
        $query = $_GET['atk_vlookup_q'] ?? null;
        $data = [];
        if ($fieldName) {
            $model = $this->getModel()->getField($fieldName)->reference->refModel();
            $refFieldName = $this->getModel()->getField($fieldName)->reference->getTheirFieldName();
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
