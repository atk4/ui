<?php

// vim:ts=4:sw=4:et:fdm=marker:fdl=0

namespace atk4\ui;

/**
 * Card class displays a single record data.
 *
 * IMPORTANT: Although the purpose of the "Card" component will remain the same, we do plan to
 * improve implementation of a card to to use https://semantic-ui.com/views/card.html.
 */
class CardTable extends Table
{
    protected $_bypass = false;

    public function setModel(\atk4\data\Model $m, $columndef = null)
    {
        if ($this->_bypass) {
            return parent::setModel($m);
        }

        if (!$m->loaded()) {
            throw new Exception(['Model must be loaded', 'model'=>$m]);
        }

        $data = [];

        $ui_values = $this->app ? $this->app->ui_persistence->typecastSaveRow($m, $m->get()) : $m->get();

        foreach ($m->get() as $key => $value) {
            if (!$columndef || ($columndef && in_array($key, $columndef))) {
                $data[] = [
                    'id'   => $key,
                    'field'=> $m->getElement($key)->getCaption(),
                    'value'=> $ui_values[$key],
                ];
            }
        }

        $this->_bypass = true;
        $mm = parent::setSource($data);
        $this->addDecorator('value', ['Multiformat', function ($row, $field) use ($m) {
            $field = $m->getElement($row->data['id']);
            $ret = $this->decoratorFactory($field);
            if ($ret instanceof \atk4\ui\TableColumn\Money) {
                $ret->attr['all']['class'] = ['single line'];
            }

            return $ret;
        }]);
        $this->_bypass = false;

        return $mm;
    }
}
