<?php

declare(strict_types=1);

namespace Atk4\Ui\Form\Control;

use Atk4\Core\Factory;
use Atk4\Data\Model;
use Atk4\Ui\Jquery;
use Atk4\Ui\JsExpression;
use Atk4\Ui\JsFunction;

class Lookup extends Input
{
    public $defaultTemplate = 'form/control/lookup.html';
    public $ui = 'input';

    /**
     * Declare this property so Lookup is consistent as decorator to replace Form\Control\Dropdown.
     *
     * @var array
     */
    public $values = [];

    /**
     * Object used to capture requests from the browser.
     *
     * @var \Atk4\Ui\Callback
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
     * If a dependency callback is declared Lookup collects the current (dirty) form values
     * and passes them on to the dependency callback so conditions on the field model can be applied.
     * This allows for generating different option lists depending on dirty form values
     * E.g if we have a dropdown field 'country' we can add to the form an Lookup field 'state'
     * with dependency
     * Then model of the 'state' field can be limited to states of the currently selected 'country'.
     *
     * @var \Closure
     */
    public $dependency;

    /**
     * Set this to create right-aligned button for adding a new a new record.
     *
     * true = will use "Add new" label
     * string = will use your string
     *
     * @var bool|string|null
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
     * to use with Lookup.
     *
     * For example, using this setting will automatically submit
     * form when field value is changes.
     * $form->addControl('field', [\Atk4\Ui\Form\Control\Lookup::class, 'settings'=>['allowReselection' => true,
     *                           'selectOnKeydown' => false,
     *                           'onChange'        => new Atk4\Ui\JsExpression('function(value,t,c){
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
     * If left empty default callback Lookup::defaultRenderRow is used.
     *
     * @var \Closure|null
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

    protected function init(): void
    {
        parent::init();

        $this->template->set([
            'input_id' => $this->name . '-ac',
            'placeholder' => $this->placeholder,
        ]);

        $this->initQuickNewRecord();

        $this->settings['forceSelection'] = false;

        $this->callback = \Atk4\Ui\Callback::addTo($this);
        $this->callback->set([$this, 'outputApiResponse']);
    }

    /**
     * Returns URL which would respond with first 50 matching records.
     */
    protected function getCallbackUrl()
    {
        return $this->callback->getJsUrl();
    }

    /**
     * Generate API response.
     */
    public function outputApiResponse()
    {
        $this->getApp()->terminateJson([
            'success' => true,
            'results' => $this->getData(),
        ]);
    }

    /**
     * Generate Lookup data.
     *
     * @param int|bool $limit
     */
    public function getData($limit = true): array
    {
        if (!$this->model) {
            return [['value' => '-1', 'title' => 'Model must be set for Lookup']];
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
     * Renders the Lookup row depending on properties set.
     */
    public function renderRow(Model $row): array
    {
        $renderRowFunction = $this->renderRowFunction ?? \Closure::fromCallable([static::class, 'defaultRenderRow']);

        return $renderRowFunction($this, $row);
    }

    /**
     * Default callback for generating data row.
     *
     * @param Lookup $field
     * @param string $key
     *
     * @return string[]
     */
    public static function defaultRenderRow($field, Model $row, $key = null)
    {
        $id_field = $field->id_field ?: $row->id_field;
        $title_field = $field->title_field ?: $row->title_field;

        return [
            'value' => $row->get($id_field),
            'title' => $row->get($title_field),
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

        $defaultSeed = [\Atk4\Ui\Button::class, 'disabled' => ($this->disabled || $this->readonly)];

        $this->action = Factory::factory(array_merge($defaultSeed, (array) $buttonSeed));

        if ($this->form) {
            $vp = \Atk4\Ui\VirtualPage::addTo($this->form);
        } else {
            $vp = \Atk4\Ui\VirtualPage::addTo($this->getOwner());
        }

        $vp->set(function ($page) {
            $form = \Atk4\Ui\Form::addTo($page);

            $model = clone $this->model;

            $form->setModel($model->onlyFields($this->plus['fields'] ?? []));

            $form->onSubmit(function (\Atk4\Ui\Form $form) {
                $form->model->save();

                $ret = [
                    (new Jquery('.atk-modal'))->modal('hide'),
                ];

                if ($row = $this->renderRow($form->model)) {
                    $chain = new Jquery('#' . $this->name . '-ac');
                    $chain->dropdown('set value', $row['value'])->dropdown('set text', $row['title']);

                    $ret[] = $chain;
                }

                return $ret;
            });
        });

        $caption = $this->plus['caption'] ?? 'Add New ' . $this->model->getModelCaption();

        $this->action->js('click', new \Atk4\Ui\JsModal($caption, $vp));
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
        if (empty($_GET['q'])) {
            return;
        }

        if ($this->search instanceof \Closure) {
            $this->search($this->model, $_GET['q']);
        } elseif (is_array($this->search)) {
            $scope = Model\Scope::createOr();
            foreach ($this->search as $field) {
                $scope->addCondition($field, 'like', '%' . $_GET['q'] . '%');
            }
            $this->model->addCondition($scope);
        } else {
            $title_field = $this->title_field ?: $this->model->title_field;

            $this->model->addCondition($title_field, 'like', '%' . $_GET['q'] . '%');
        }
    }

    /**
     * Apply conditions to model based on dependency.
     */
    protected function applyDependencyConditions()
    {
        if (!($this->dependency instanceof \Closure)) {
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

        ($this->dependency)($this->model, $data);
    }

    /**
     * returns <input .../> tag.
     */
    public function getInput()
    {
        return $this->getApp()->getTag('input', array_merge([
            'name' => $this->short_name,
            'type' => 'hidden',
            'id' => $this->id . '_input',
            'value' => $this->getValue(),
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
     * @param Jquery $chain
     */
    protected function initDropdown($chain)
    {
        $settings = array_merge([
            'fields' => ['name' => 'title'],
            'apiSettings' => array_merge(['url' => $this->getCallbackUrl() . '&q={query}'], $this->apiConfig),
        ], $this->settings);

        $chain->dropdown($settings);
    }

    protected function renderView(): void
    {
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
            $this->settings['onShow'] = new JsFunction([new JsExpression('return false')]);
            $this->template->set('readonly', 'readonly');
        }

        if ($this->dependency) {
            $this->apiConfig['data'] = array_merge([
                'form' => new JsFunction([new JsExpression('return []', [$this->form->formElement->js()->serialize()])]),
            ], $this->apiConfig['data'] ?? []);
        }

        $chain = new Jquery('#' . $this->name . '-ac');

        $this->initDropdown($chain);

        if ($this->field && $this->field->get()) {
            $id_field = $this->id_field ?: $this->model->id_field;

            $this->model->tryLoadBy($id_field, $this->field->get());

            if ($this->model->loaded()) {
                $row = $this->renderRow($this->model);

                $chain->dropdown('set value', $row['value'])->dropdown('set text', $row['title']);
            } else {
                $this->field->setNull();
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
     * @see \Atk4\Ui\Form\Control::set()
     */
    public function set($value = null, $junk = null)
    {
        $value = implode(',', (array) $value);

        return parent::set($value, $junk);
    }
}
