<?php

namespace atk4\ui\FormLayout;

use atk4\ui\Form;

/**
 * Layout that automatically arranges itself into multiple columns.
 * Well suitable for large number of fields on a form.
 */
class Columns extends Generic
{
    public function setModel(\atk4\data\Model $model, $fields = null)
    {
        // dont add any fields automatically
        parent::setModel($model, false);

        if ($fields === null) {
            $fields = [];
            foreach ($model->elements as $f) {
                if (!$f instanceof \atk4\data\Field) {
                    continue;
                }

                if (!$f->isEditable()) {
                    continue;
                }
                $fields[] = $f->short_name;
            }
        }

        $cnt = count($fields);

        if ($cnt < 10) {
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

        $c = $this->add('Columns');

        $chunks = array_chunk($fields, ceil($cnt / $col));
        foreach($chunks as $chunk) {
            $cc = $c->addColumn();
            $cc->add(['FormLayout/Generic', 'form'=>$this->form])->setModel($model, $chunk);
        }

        return $model;
    }
}
