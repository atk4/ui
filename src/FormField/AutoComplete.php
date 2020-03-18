<?php

namespace atk4\ui\FormField;

use atk4\ui\jQuery;
use atk4\ui\jsExpression;
use atk4\ui\jsFunction;

class AutoComplete extends Input
{
    public $defaultTemplate = 'formfield/autocomplete.html';
    public $ui = 'input';

    /**
     * Declare this property so AutoComplete is consistent as decorator to replace FormField\DropDown.
     *
     * @var array
     */
    public $values = [];

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
    public $empty = "\u{00a0}"; // Unicode NBSP

    /**
     * Either set this to array of fields which must be searched (e.g. "name", "surname"), or define this
     * as a callback to be executed callback($model, $search_string);.
     *
     * If left null, then search will be performed on a model's title field
     *
     * @var array|\Closure
     */
    public $search;

    /**
     * If a dependency callback is declared AutoComplete collects the current (dirty) form values
     * and passes them on to the dependency callback so conditions on the field model can be applied.
     * This allows for generating different option lists depending on dirty form values
     * E.g if we have a dropdown field 'country' we can add to the form an AutoComplete field 'state'
     * with dependency
     * Then model of the 'state' field can be limited to states of the currently selected 'country'.
     *
     * @var callable
     */
    public $dependency;

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
     * Sets the max. amount of records that are loaded.
     *
     * @var int
     */
    public $limit = 100;

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

    /**
     * Define callback for generating the row data
     * If left empty default callback AutoComplete::defaultRenderRow is used.
     *
     * @var null|callable
     */
    public $renderRowFunction;

    /**
     * Whether or not to accept multiple value.
     *   Multiple values are sent using a string with comma as value delimiter.
     *   ex: 'value1,value2,value3'.
     *
     * @var bool
     */
    public $multiple = false;

    public function init()
    {
        parent::init();

        $this->template->set([
            'input_id'    => $this->name.'-ac',
            'placeholder' => $this->placeholder,
        ]);

        $this->initQuickNewRecord();

        $this->settings['forceSelection'] = false;
    }

    /**
     * Returns URL which would respond with first 50 matching records.
     */
    public function getCallbackURL()
    {
        return $this->callback->getJSURL();
    }

    /**
     * Generate API response.
     */
    public function outputApiResponse()
    {
        $this->app->terminate(json_encode([
            'success' => true,
            'results' => $this->getData(),
        ]));
    }

    /**
     * Generate autocomplete data.
     *
     * @param bool $limit
     *
     * @return array
     */
    public function getData($limit = true)
    {
        if (!$this->model) {
            return [['value' => '-1', 'title' => 'Model must be set for AutoComplete']];
        }

        $this->applyLimit($limit);

        $this->applySearchConditions();

        $this->applyDependencyConditions();

        $data = [];
        foreach ($this->model as $row) {
            $data[] = $this->renderRow($row);
        }

        if (!$this->multiple && $this->empty) {
            array_unshift($data, ['value' => '0', 'title' => (string) $this->empty]);
        }

        return $data;
    }

    /**
     * Renders the autocomplete row depending on properties set.
     *
     * @param array $row
     *
     * @return mixed
     */
    public function renderRow($row)
    {
        $renderRowFunction = is_callable($this->renderRowFunction) ? $this->renderRowFunction : [__CLASS__, 'defaultRenderRow'];

        return call_user_func($renderRowFunction, $this, $row);
    }

    /**
     * Default callback for generating data row.
     *
     * @param AutoComplete     $field
     * @param \atk4\data\Model $row
     * @param string           $key
     *
     * @return string[]
     */
    public static function defaultRenderRow($field, $row, $key = null)
    {
        $id_field = $field->id_field ?: $row->id_field;
        $title_field = $field->title_field ?: $row->title_field;

        // IMPORTANT: always convert data to string, otherwise numbers can be rounded by JS
        return [
            'value' => (string) $row[$id_field],
            'title' => (string) $row[$title_field],
        ];
    }

    /**
     * Add button for new record.
     */
    protected function initQuickNewRecord()
    {
        if (!$this->plus) {
            return;
        }

        $this->plus = is_bool($this->plus) ? 'Add New' : $this->plus;

        $this->plus = is_string($this->plus) ? ['button' => $this->plus] : $this->plus;

        $buttonSeed = $this->plus['button'] ?? [];

        $buttonSeed = is_string($buttonSeed) ? ['content' => $buttonSeed] : $buttonSeed;

        $defaultSeed = ['Button', 'disabled' => ($this->disabled || $this->readonly)];

        $this->action = $this->factory(array_merge($defaultSeed, (array) $buttonSeed), null, 'atk4\ui');

        if ($this->form) {
            $vp = $this->form->add('VirtualPage');
        } else {
            $vp = $this->owner->add('VirtualPage');
        }

        $vp->set(function ($page) {
            $form = $page->add('Form');

            $model = clone $this->model;

            $form->setModel($model->onlyFields($this->plus['fields'] ?? []));

            $form->onSubmit(function ($form) {
                $form->model->save();

                $ret = [
                    (new jQuery('.atk-modal'))->modal('hide'),
                ];

                if ($row = $this->renderRow($form->model)) {
                    $chain = new jQuery('#'.$this->name.'-ac');
                    $chain->dropdown('set value', $row['value'])->dropdown('set text', $row['title']);

                    $ret[] = $chain;
                }

                return $ret;
            });
        });

        $caption = $this->plus['caption'] ?? 'Add New '.$this->model->getModelCaption();

        $this->action->js('click', new \atk4\ui\jsModal($caption, $vp));
    }

    /**
     * Apply limit to model.
     */
    protected function applyLimit($limit = true)
    {
        if (!$limit) {
            return;
        }

        $this->model->setLimit(is_numeric($limit) ? $limit : $this->limit);
    }

    /**
     * Apply conditions to model based on search string.
     */
    protected function applySearchConditions()
    {
        if (!isset($_GET['q'])) {
            return;
        }

        if ($this->search instanceof \Closure) {
            $this->search($this->model, $_GET['q']);
        } elseif ($this->search && is_array($this->search)) {
            $this->model->addCondition(array_map(function ($field) {
                return [$field, 'like', '%'.$_GET['q'].'%'];
            }, $this->search));
        } else {
            $title_field = $this->title_field ?: $this->model->title_field;

            $this->model->addCondition($title_field, 'like', '%'.$_GET['q'].'%');
        }
    }

    /**
     * Apply conditions to model based on dependency.
     */
    protected function applyDependencyConditions()
    {
        if (!is_callable($this->dependency)) {
            return;
        }

        $data = [];
        if (isset($_GET['form'])) {
            parse_str($_GET['form'], $data);
        } elseif ($this->form) {
            $data = $this->form->model->get();
        } else {
            return;
        }

        call_user_func($this->dependency, $this->model, $data);
    }

    /**
     * returns <input .../> tag.
     */
    public function getInput()
    {
        return $this->app->getTag('input', array_merge([
            'name'     => $this->short_name,
            'type'     => 'hidden',
            'id'       => $this->id.'_input',
            'value'    => $this->getValue(),
            'readonly' => $this->readonly ? 'readonly' : false,
            'disabled' => $this->disabled ? 'disabled' : false,
        ], $this->inputAttr));
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
     * Override this method if you want to add more logic to the initialization of the auto-complete field.
     *
     * @param jQuery
     */
    protected function initDropdown($chain)
    {
        $settings = array_merge([
            'fields'      => ['name' => 'title'],
            'apiSettings' => array_merge(['url' => $this->getCallbackURL().'&q={query}'], $this->apiConfig),
        ], $this->settings);

        $chain->dropdown($settings);
    }

    public function renderView()
    {
        $this->callback = $this->add('Callback');
        $this->callback->set([$this, 'outputApiResponse']);

        if ($this->multiple) {
            $this->template->set('multiple', 'multiple');
        }

        if ($this->disabled) {
            $this->settings['showOnFocus'] = false;
            $this->settings['allowTab'] = false;

            $this->template->set('disabled', 'disabled');
        }

        if ($this->readonly) {
            $this->settings['showOnFocus'] = false;
            $this->settings['allowTab'] = false;
            $this->settings['apiSettings'] = null;
            $this->settings['onShow'] = new jsFunction([new jsExpression('return false')]);
            $this->template->set('readonly', 'readonly');
        }

        if ($this->dependency) {
            $this->apiConfig['data'] = array_merge([
                'form' => new jsFunction([new jsExpression('return []', [$this->js()->closest('form')->serialize()])]),
            ], $this->apiConfig['data'] ?? []);
        }

        $chain = new jQuery('#'.$this->name.'-ac');

        $this->initDropdown($chain);

        if ($this->field && $this->field->get()) {
            $id_field = $this->id_field ?: $this->model->id_field;

            $this->model->tryLoadBy($id_field, $this->field->get());

            if ($this->model->loaded()) {
                $row = $this->renderRow($this->model);

                $chain->dropdown('set value', $row['value'])->dropdown('set text', $row['title']);
            } else {
                $this->field->set(null);
            }
        }

        $this->js(true, $chain);

        parent::renderView();
    }

    /**
     * Convert value to expected comma separated list before setting it.
     *
     * {@inheritdoc}
     *
     * @see \atk4\ui\FormField\Generic::set()
     */
    public function set($value = null, $junk = null)
    {
        $value = implode(',', (array) $value);

        return parent::set($value, $junk);
    }
}
