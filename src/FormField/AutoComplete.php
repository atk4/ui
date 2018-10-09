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
     * @var callable
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
     * Sets the max. amount of records that are loaded. The default 10
     * displays nicely in UI.
     *
     * @var int
     */
    public $limit = 10;

    /**
     * Set custom model field here to use it's value as ID in dropdown instead of default model ID field.
     *
     * @var string
     */
    public $id_field;

    /**
     * Set custom model field here to display it's value in dropdown instead of default model title field.
     *
     * @var string
     */
    public $title_field;

    /**
     * Semantic UI uses cache to remember choices. For dynamic sites this may be dangerous, so
     * it's disabled by default. To switch cache on, set 'cache'=>'local'.
     *
     * Use this apiConfig variable to pass API settings to Semantic UI in .dropdown()
     *
     * @var array
     */
    public $apiConfig = ['cache' => false];

    /**
     * Semantic UI dropdown module settings.
     * Use this setting to configure various dropdown module settings
     * to use with Autocomplete.
     *
     * For example, using this setting will automatically submit
     * form when field value is changes.
     * $form->addField('field', ['AutoComplete', 'settings'=>['allowReselection' => true,
     *                           'selectOnKeydown' => false,
     *                           'onChange'        => new atk4\ui\jsExpression('function(value,t,c){
     *                                                          if ($(this).data("value") !== value) {
     *                                                            $(this).parents(".form").form("submit");
     *                                                            $(this).data("value", value);
     *                                                          }
     *                                                         }'),
     *                          ]]);
     *
     * @var array
     */
    public $settings = [];

    public function init()
    {
        parent::init();

        $this->template->set('input_id', $this->name.'-ac');

        $this->template->set('place_holder', $this->placeholder);

        if ($this->plus) {
            $this->action = $this->factory(['Button', is_string($this->plus) ? $this->plus : 'Add new', 'disabled' => ($this->disabled || $this->readonly)]);
        }
        //var_Dump($this->model->get());
        if ($this->form) {
            $vp = $this->form->add('VirtualPage');
        } else {
            $vp = $this->owner->add('VirtualPage');
        }

        $vp->set(function ($p) {
            $f = $p->add('Form');
            $f->setModel($this->model);

            $f->onSubmit(function ($f) {
                $id = $f->model->save()->id;

                $modal_chain = new jQuery('.atk-modal');
                $modal_chain->modal('hide');
                $ac_chain = new jQuery('#'.$this->name.'-ac');
                $ac_chain->dropdown('set value', $id)->dropdown('set text', $f->model->getTitle());

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
        return $this->callback->getJSURL();
    }

    public function getData()
    {
        if (!$this->model) {
            $this->app->terminate(json_encode([['id' => '-1', 'name' => 'Model must be set for AutoComplete']]));
        }

        $id_field = $this->id_field ?: $this->model->id_field;
        $title_field = $this->title_field ?: $this->model->title_field;

        $this->model->setLimit($this->limit);

        if (isset($_GET['q'])) {
            if ($this->search instanceof Closure) {
                $this->search($this->model, $_GET['q']);
            } elseif ($this->search && is_array($this->search)) {
                $this->model->addCondition(array_map(function ($field) {
                    return [$field, 'like', '%'.$_GET['q'].'%'];
                }, $this->search));
            } else {
                $this->model->addCondition($title_field, 'like', '%'.$_GET['q'].'%');
            }
        }

        $data = [];
        foreach ($this->model as $junk) {
            $data[] = ['id' => $this->model[$id_field], 'name' => $this->model[$title_field]];
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
            'name'        => $this->short_name,
            'type'        => 'hidden',
            'id'          => $this->id.'_input',
            'value'       => $this->getValue(),
            'readonly'    => $this->readonly ? 'readonly' : false,
            'disabled'    => $this->disabled ? 'disabled' : false,
        ]);
    }

    /**
     * Set Semantic-ui Api settings to use with dropdown.
     *
     * @param array $config
     *
     * @return $this
     */
    public function setApiConfig($config)
    {
        $this->apiConfig = array_merge($this->apiConfig, $config);

        return $this;
    }

    /**
     * Override this method if you want to add more logic to the initialization of the
     * auto-complete field.
     *
     * @param jQuery
     */
    protected function initDropdown($chain)
    {
        $settings = array_merge([
            'fields'      => ['name' => 'name', 'value' => 'id'/*, 'text' => 'description'*/],
            'apiSettings' => array_merge(['url' => $this->getCallbackURL().'&q={query}'], $this->apiConfig),
        ], $this->settings);

        $chain->dropdown($settings);
    }

    public function renderView()
    {
        $this->callback = $this->add('Callback');
        $this->callback->set([$this, 'getData']);

        if ($this->disabled) {
            $this->settings['showOnFocus'] = false;
            $this->settings['allowTab'] = false;

            $this->template->set('disabled', 'disabled');
        }

        if ($this->readonly) {
            $this->settings['showOnFocus'] = false;
            $this->settings['allowTab'] = false;
            $this->template->set('readonly', 'readonly');
        }

        $chain = new jQuery('#'.$this->name.'-ac');

        $this->initDropdown($chain);

        $this->js(true, $chain);

        if ($this->field && $this->field->get()) {
            $id_field = $this->id_field ?: $this->model->id_field;
            $title_field = $this->title_field ?: $this->model->title_field;

            $this->model->tryLoadBy($id_field, $this->field->get());

            if (!$this->model->loaded()) {
                $this->field->set(null);
            } else {
                $chain->dropdown('set value', $this->model[$id_field])->dropdown('set text', $this->model[$title_field]);
                $this->js(true, $chain);
            }
        }

        parent::renderView();
    }
}
