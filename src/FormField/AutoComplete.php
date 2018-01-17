<?php

namespace atk4\ui\FormField;

use atk4\ui\jQuery;

class AutoComplete extends Input
{
    public $defaultTemplate = 'formfield/autocomplete.html';
    public $ui = 'input';

    /**
     * Object used to capture requests from the browser.
     *
     * @var Callback
     */
    public $callback;

    /**
     * Set this to true, to permit "empty" selection. If you set it to string, it will be used as a placeholder for empty value.
     *
     * @var string
     */
    public $empty = '...';

    /**
     * Either set this to array of fields which must be searched (e.g. "name", "surname"), or define this
     * as a callback to be executed callback($model, $search_string);.
     *
     * If left null, then search will be performed on a model's title field
     *
     * @var array|Closure
     */
    public $search;

    /**
     * Set this to create right-aligned button for adding a new a new record.
     *
     * true = will use "Add new" label
     * string = will use your string
     *
     * @var null|bool|string
     */
    public $plus = false;

    /**
     * Semantic UI uses cache to remmber choices. For dynamic sites this may be dangerous, so
     * it's disabled by default. To switch cache on, set 'cache'=>'local'.
     *
     * Use this apiConfig variable to pass API settings to Semantic UI in .dropdown()
     *
     * @var array
     */
    public $apiConfig = ['cache' => false];

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
            'apiSettings' => array_merge($this->apiConfig, [
                'url' => $this->getCallbackURL().'&q={query}',
            ]),
        ]);
        $this->js(true, $chain);

        if ($this->plus) {
            $this->action = $this->factory(['Button', is_string($this->plus) ? $this->plus : 'Add new']);
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
        if ($this->action) {
            $this->action->js('click', new \atk4\ui\jsModal('Adding New Record', $vp));
        }
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
            $this->app->terminate(json_encode([['id' => '-1', 'name' => 'Model must be set for AutoComplete']]));
        }
        $this->model->setLimit(10);
        if (isset($_GET['q'])) {
            if ($this->search instanceof Closure) {
                $this->search($this->model, $_GET['q']);
            } elseif ($this->search && is_array($this->search)) {
                $this->model->addCondition(array_map(function ($field) {
                    return [$field, 'like', '%'.$_GET['q'].'%'];
                }, $this->search));
            } else {
                $this->model->addCondition($this->model->title_field, 'like', '%'.$_GET['q'].'%');
            }
        }

        //fix #327: https://github.com/atk4/ui/issues/327
        $data = [];
        $res = $this->model->export([$this->model->id_field, $this->model->title_field]);
        foreach ($res as $item) {
            $data[] = ['id' => $item[$this->model->id_field], 'name' => $item[$this->model->title_field]];
        }

        if ($this->empty) {
            array_unshift($data, ['id' => 0, 'name' => $this->empty]);
        }

        $this->app->terminate(json_encode([
            'success' => true,
            'results' => $data,
        ]));
    }

    /**
     * returns <input .../> tag.
     */
    public function getInput()
    {
        return $this->app->getTag('input', [
            'name' => $this->short_name,
            'type' => 'hidden',
            /*'placeholder'=> $this->placeholder,*/
            'id'    => $this->id.'_input',
            'value' => $this->getValue(),
        ]);
    }
}
