<?php
/**
 * A collection of Card set from a model.
 *
 */

namespace atk4\ui;

use atk4\data\Model;

class CardDeck extends View
{
    public $ui = 'cards';
    public $card = Card::class;

    public $useTable = false;

    public $useLabel = false;

    public $extraGlue = null;

    public $useAction = true;

    public function setModel(Model $model, array $fields, array $extra = null)
    {
        $model->each(function ($m) use ($fields, $extra) {
            $c = $this->add($this->card);
            $c->addSection($m->getTitle(), $m, $fields, $this->useLabel, $this->useTable);
            if ($extra) {
                $c->addExtraFields($m, $extra, $this->extraGlue);
            }
            if ($this->useAction) {
                $c->addModelActions($m);
            }
        });

        return parent::setModel($model);
    }
}
