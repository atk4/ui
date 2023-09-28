<?php

declare(strict_types=1);

namespace Atk4\Ui\Form\Control;

use Atk4\Core\Factory;
use Atk4\Core\HookTrait;
use Atk4\Data\Model;
use Atk4\Ui\Button;
use Atk4\Ui\CallbackLater;
use Atk4\Ui\Form;
use Atk4\Ui\Js\Jquery;
use Atk4\Ui\Js\JsBlock;
use Atk4\Ui\Js\JsExpression;
use Atk4\Ui\Js\JsExpressionable;
use Atk4\Ui\Js\JsFunction;
use Atk4\Ui\Js\JsModal;
use Atk4\Ui\Js\JsToast;
use Atk4\Ui\VirtualPage;

class Lookup extends Input
{
    use HookTrait;

    public $defaultTemplate = 'form/control/lookup.html';

    public string $inputType = 'hidden';

    /** @var array Declare this property so Lookup is consistent as decorator to replace Form\Control\Dropdown. */
    public $values = [];

    /** @var CallbackLater Object used to capture requests from the browser. */
    public $callback;

    /** @var string Set this to true, to permit "empty" selection. If you set it to string, it will be used as a placeholder for empty value. */
    public $empty = "\u{00a0}"; // Unicode NBSP

    /**
     * Either set this to array of fields which must be searched (e.g. "name", "surname"), or define this
     * as a callback to be executed callback($model, $query);.
     *
     * If left null, then search will be performed on a model's title field
     *
     * @var list<string>|\Closure(Model, string): void|null
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
     * @var \Closure(Model, array<string, mixed>): void|null
     */
    public $dependency;

    /**
     * Set this to create right-aligned button for adding a new a new record.
     *
     * true = will use "Add new" label
     * string = will use your string
     *
     * @var bool|string|array|null
     */
    public $plus = false;

    /** @var int Sets the max. amount of records that are loaded. */
    public $limit = 100;

    /** @var string|null Set custom model field here to use it's value as ID in dropdown instead of default model ID field. */
    public $idField;

    /** @var string|null Set custom model field here to display it's value in dropdown instead of default model title field. */
    public $titleField;

    /**
     * Fomantic-UI uses cache to remember choices. For dynamic sites this may be dangerous, so
     * it's disabled by default. To switch cache on, set 'cache' => 'local'.
     *
     * Use this apiConfig variable to pass API settings to Fomantic-UI in .dropdown()
     *
     * @var array
     */
    public $apiConfig = ['cache' => false];

    /**
     * Fomantic-UI dropdown module settings.
     * Use this setting to configure various dropdown module settings
     * to use with Lookup.
     *
     * For example, using this setting will automatically submit
     * form when field value is changes.
     * $form->addControl('field', [Form\Control\Lookup::class, 'settings' => [
     *     'allowReselection' => true,
     *     'selectOnKeydown' => false,
     *     'onChange' => new JsExpression('function (value, t, c) {
     *         if ($(this).data("value") !== value) {
     *             $(this).parents(\'.form\').form(\'submit\');
     *             $(this).data(\'value\', value);
     *         }
     *     }'),
     * ]]);
     *
     * @var array
     */
    public $settings = [];

    /**
     * Define callback for generating the row data
     * If left empty default callback Lookup::defaultRenderRow is used.
     *
     * @var \Closure($this, Model): array{value: mixed, title: mixed}|null
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
            'placeholder' => $this->placeholder,
        ]);

        $this->initQuickNewRecord();

        $this->callback = CallbackLater::addTo($this);
        $this->callback->set(function () {
            $this->outputApiResponse();
        });
    }

    /**
     * @param bool|string      $when
     * @param JsExpressionable $action
     *
     * @return Jquery
     */
    protected function jsDropdown($when = false, $action = null): JsExpressionable
    {
        return $this->js($when, $action, 'div.ui.dropdown:has(> #' . $this->name . '_input)');
    }

    /**
     * Returns URL which would respond with first 50 matching records.
     */
    protected function getCallbackUrl(): string
    {
        return $this->callback->getJsUrl();
    }

    /**
     * Generate API response.
     *
     * @return never
     */
    public function outputApiResponse(): void
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
     *
     * @return array<int, array{value: mixed, title: mixed}>
     */
    public function getData($limit = true): array
    {
        $this->applyLimit($limit);

        $this->applySearchConditions();

        $this->applyDependencyConditions();

        $data = [];
        foreach ($this->model as $row) {
            $data[] = $this->renderRow($row);
        }

        if (!$this->multiple && $this->empty) {
            array_unshift($data, ['value' => '', 'title' => $this->empty]);
        }

        return $data;
    }

    /**
     * Renders the Lookup row depending on properties set.
     *
     * @return array{value: mixed, title: mixed}
     */
    public function renderRow(Model $row): array
    {
        if ($this->renderRowFunction !== null) {
            return ($this->renderRowFunction)($this, $row);
        }

        return $this->defaultRenderRow($row);
    }

    /**
     * Default callback for generating data row.
     *
     * @param string $key
     *
     * @return array{value: mixed, title: mixed}
     */
    public function defaultRenderRow(Model $row, $key = null)
    {
        $idField = $this->idField ?? $row->idField;
        $titleField = $this->titleField ?? $row->titleField;

        return ['value' => $row->get($idField), 'title' => $row->get($titleField)];
    }

    /**
     * Add button for new record.
     */
    protected function initQuickNewRecord(): void
    {
        if (!$this->plus) {
            return;
        }

        if ($this->plus === true) {
            $this->plus = 'Add New';
        }

        if (is_string($this->plus)) {
            $this->plus = ['button' => $this->plus];
        }

        $buttonSeed = $this->plus['button'] ?? [];
        if (is_string($buttonSeed)) {
            $buttonSeed = ['content' => $buttonSeed];
        }

        $defaultSeed = [Button::class, 'class.disabled' => $this->disabled || $this->readOnly];
        $this->action = Factory::factory(array_merge($defaultSeed, $buttonSeed));

        $vp = VirtualPage::addTo($this->form ?? $this->getOwner());
        $vp->set(function (VirtualPage $p) {
            $form = Form::addTo($p);

            $entity = $this->model->createEntity();
            $form->setModel($entity, $this->plus['fields'] ?? null);

            $form->onSubmit(function (Form $form) {
                $msg = $form->model->getUserAction('add')->execute();

                $res = new JsBlock();
                if (is_string($msg)) {
                    $res->addStatement(new JsToast($msg));
                }
                $res->addStatement((new Jquery())->closest('.atk-modal')->modal('hide'));

                $row = $this->renderRow($form->model);
                $chain = $this->jsDropdown();
                $chain->dropdown('set value', $row['value'])->dropdown('set text', $row['title']);
                $res->addStatement($chain);

                return $res;
            });
        });

        $caption = $this->plus['caption'] ?? 'Add New ' . $this->model->getModelCaption();
        $this->action->on('click', new JsModal($caption, $vp));
    }

    /**
     * Apply limit to model.
     *
     * @param int|bool $limit
     */
    protected function applyLimit($limit = true): void
    {
        if ($limit !== false) {
            $this->model->setLimit($limit === true ? $this->limit : $limit);
        }
    }

    /**
     * Apply conditions to model based on search string.
     */
    protected function applySearchConditions(): void
    {
        $query = $this->getApp()->tryGetRequestQueryParam('q') ?? '';
        if ($query === '') {
            return;
        }

        if ($this->search instanceof \Closure) {
            ($this->search)($this->model, $query);
        } elseif (is_array($this->search)) {
            $scope = Model\Scope::createOr();
            foreach ($this->search as $field) {
                $scope->addCondition($field, 'like', '%' . $query . '%');
            }
            $this->model->addCondition($scope);
        } else {
            $titleField = $this->titleField ?? $this->model->titleField;

            $this->model->addCondition($titleField, 'like', '%' . $query . '%');
        }
    }

    /**
     * Apply conditions to model based on dependency.
     */
    protected function applyDependencyConditions(): void
    {
        if (!$this->dependency instanceof \Closure) {
            return;
        }

        $data = [];
        if ($this->getApp()->hasRequestQueryParam('form')) {
            parse_str($this->getApp()->getRequestQueryParam('form'), $data);
        } elseif ($this->form) {
            $data = $this->form->model->get();
        } else {
            return;
        }

        ($this->dependency)($this->model, $data);
    }

    /**
     * Override this method if you want to add more logic to the initialization of the auto-complete field.
     *
     * @param Jquery $chain
     */
    protected function initDropdown($chain): void
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
            $this->template->dangerouslySetHtml('multipleClass', 'multiple');
        }

        if ($this->disabled) {
            $this->template->set('disabledClass', 'disabled');
            $this->template->dangerouslySetHtml('disabled', 'disabled="disabled"');
        } elseif ($this->readOnly) {
            $this->template->set('disabledClass', 'read-only');
            $this->template->dangerouslySetHtml('disabled', 'readonly="readonly"');

            $this->settings['apiSettings'] = null;
            $this->settings['onShow'] = new JsFunction([], [new JsExpression('return false')]);
        }

        if ($this->dependency) {
            $this->apiConfig['data'] = array_merge([
                'form' => new JsFunction([], [new JsExpression('return []', [$this->form->formElement->js()->serialize()])]),
            ], $this->apiConfig['data'] ?? []);
        }

        $chain = $this->jsDropdown();

        $this->initDropdown($chain);

        if ($this->entityField && $this->entityField->get()) {
            $idField = $this->idField ?? $this->model->idField;

            $this->model = $this->model->loadBy($idField, $this->entityField->get());

            $row = $this->renderRow($this->model);
            $chain->dropdown('set value', $row['value'])->dropdown('set text', $row['title']);
        }

        $this->js(true, $chain);

        parent::renderView();
    }
}
