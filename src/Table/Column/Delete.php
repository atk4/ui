<?php

declare(strict_types=1);

namespace Atk4\Ui\Table\Column;

use Atk4\Data\Field;
use Atk4\Ui\CallbackLater;
use Atk4\Ui\Jquery;
use Atk4\Ui\Table;

/**
 * Formatting action buttons column.
 */
class Delete extends Table\Column
{
    /** @var CallbackLater */
    protected $vp;

    protected function init(): void
    {
        parent::init();

        $this->vp = CallbackLater::addTo($this->table);
        $this->vp->set(function () {
            $this->table->model->load($_POST[$this->name])->delete();

            $reload = $this->table->reload ?? $this->table;

            $this->table->getApp()->terminateJson($reload);
        });
    }

    public function getDataCellTemplate(Field $field = null): string
    {
        $this->table->on('click', 'a.' . $this->shortName, null, ['confirm' => (new Jquery())->attr('title')])->atkAjaxec([
            'url' => $this->vp->getJsUrl(),
            'urlOptions' => [$this->name => $this->table->jsRow()->data('id')],
        ]);

        return $this->getApp()->getTag('a', [
            'href' => '#',
            'title' => 'Delete {$' . $this->table->model->titleField . '}?',
            'class' => $this->shortName,
        ], [
            ['i', ['class' => 'ui red trash icon'], ''],
            'Delete',
        ]);
    }
}
