<?php

namespace atk4\ui\FormField;

use atk4\ui\jQuery;

class AutoComplete extends Input
{
    public $defaultTemplate = 'formfield/autocomplete.html';
    public $ui = 'input';
    public $searchClassName = 'search';
    public $callback;

    public $plus = false; // set this to cerate right-aligned button for adding a new a new record

    public function init()
    {
        parent::init();

        $this->callback = $this->add('CallbackLater');
        $this->callback->set([$this, 'getData']);

        $this->template->set('input_id', $this->name.'-ac');

        $this->template->set('place_holder', $this->placeholder);

        $chain = new jQuery('#'.$this->name.'-ac');

        $chain->dropdown([
            'fields'      => ['name' => 'name', 'value' => 'id'/*, 'text' => 'description'*/],
            'apiSettings' => [
                'url' => $this->getCallbackURL().'&q={query}',
            ],
            /*'filterRemoteData'  => true,*/
        ]);
        $this->js(true, $chain);

        if ($this->plus) {
            $this->action = $this->factory(['Button', 'Add new']);
        }
        //var_Dump($this->model->get());
        $vp = $this->app->add('VirtualPage');
        $vp->set(function ($p) {
            $f = $p->add('Form');
            $f->setModel($this->model);

            $f->onSubmit(function ($f) {
                $id = $f->model->save()->id;

                $modal_chain = new jQuery('.atk-modal');
                $modal_chain->modal('hide');
                $ac_chain = new jQuery('#'.$this->name.'-ac');
                $ac_chain->dropdown('set value', $id)->dropdown('set text', $f->model['name']);

                return [
                    $modal_chain,
                    $ac_chain,
                    ];
            });
        });
        $this->action->js('click', new \atk4\ui\jsModal('hello', $vp));
    }

    /**
     * Returns URL which would respond with first 50 matching records.
     */
    public function getCallbackURL()
    {
        return $this->callback->getURL();
    }

    public function getData()
    {
        if (!$this->model) {
            $this->app->terminate(json_encode([['id'=>'-1', 'name'=>'Model must be set for AutoComplete']]));
        }
        $this->model->setLimit(50);
        if (isset($_GET['q'])) {
            $this->model->addCondition($this->model->title_field, 'like', '%'.$_GET['q'].'%');
        }
        $this->app->terminate(json_encode([
            'success' => true,
            'results' => $this->model->export(['id', 'name']),
        ]));
    }

    /**
     * returns <input .../> tag.
     */
    public function getInput()
    {
        return $this->app->getTag('input', [
            'name'       => $this->short_name,
            'type'       => 'hidden',
            /*'placeholder'=> $this->placeholder,*/
            'id'         => $this->id.'_input',
            'value'      => $this->getValue(),
        ]);
    }
}
