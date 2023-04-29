<?php

declare(strict_types=1);

namespace Atk4\Ui\Form\Layout;

use Atk4\Data\Model;
use Atk4\Ui\Columns as UiColumns;
use Atk4\Ui\Form;
use Atk4\Ui\View;

/**
 * Layout that automatically arranges itself into multiple columns.
 * Well suitable for large number of fields on a form.
 */
class Columns extends Form\Layout
{
    /** @var int count of columns */
    public $col;

    /** @var string size CSS class */
    public $size = '';

    /**
     * Sets form model and adds form controls.
     *
     * @param array<int, string>|null $fields
     */
    public function setModel(Model $entity, array $fields = null): void
    {
        // dont add any fields automatically
        parent::setModel($entity, []);

        if ($fields === null) {
            $fields = $this->getModelFields($entity);
        }

        $cnt = count($fields);

        if ($this->col !== null) {
            $col = $this->col;
            $size = $this->size;
        } elseif ($cnt < 10) {
            $col = 1;
            $size = '';
        } elseif ($cnt < 15) {
            $col = 2;
            $size = '';
        } elseif ($cnt < 20) {
            $col = 2;
            $size = 'small';
        } elseif ($cnt < 32) {
            $col = 3;
            $size = 'small';
        } else {
            $col = 4;
            $size = 'tiny';
        }

        if ($size) {
            $this->form->addClass($size);
        }

        $c = UiColumns::addTo($this);

        $chunks = array_chunk($fields, (int) ceil($cnt / $col));
        foreach ($chunks as $chunk) {
            $cc = $c->addColumn();
            Form\Layout::addTo($cc, ['form' => $this->form])
                ->setModel($entity, $chunk);
        }

        View::addTo($this, ['ui' => 'clearing hidden divider']);
    }
}
