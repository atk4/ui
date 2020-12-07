<?php

declare(strict_types=1);

namespace Atk4\Ui;

use Atk4\Core\Factory;
use Atk4\Data\Model;
use Atk4\Data\Reference\ContainsMany;

/**
 * Implements a form.
 */
class Form extends View
{
    use \Atk4\Core\HookTrait;

    /** @const string Executed when form is submitted */
    public const HOOK_SUBMIT = self::class . '@submit';
    /** @const string Executed when form is submitted */
    public const HOOK_DISPLAY_ERROR = self::class . '@displayError';
    /** @const string Executed when form is submitted */
    public const HOOK_DISPLAY_SUCCESS = self::class . '@displaySuccess';
    /** @const string Executed when self::loadPost() method is called. */
    public const HOOK_LOAD_POST = self::class . '@loadPost';

    // {{{ Properties

    public $ui = 'form';
    public $defaultTemplate = 'form.html';

    /** @var Callback Callback handling form submission. */
    public $cb;

    /**
     * Set this to false in order to
     * prevent from leaving
     * page if form is not submit.
     *
     * Note:
     * When using your own change handler
     * on an input field, set useDefault parameter to false.
     * ex: $input->onChange('console.log(), false)
     * Otherwise, change event is not propagate to all event handler
     * and leaving page might not be prevent.
     *
     * Form using Calendar field
     * will still leave page when a calendar
     * input value is changed.
     *
     * @var bool
     */
    public $canLeave = true;

    /**
     * Html <form> element, all inner form controls are linked to it on render
     * with html form="form_id" attribute.
     *
     * @var View
     */
    public $formElement;

    /**
     * A current layout of a form, needed if you call $form->addControl().
     *
     * @var \Atk4\Ui\Form\Layout
     */
    public $layout;

    /**
     * List of form controls currently registered with this form.
     *
     * @var array Array of Form\Control objects
     */
    public $controls = [];

    public $content = false;

    /**
     * Will point to the Save button. If you don't want to have save button, then set this to false
     * or destroy it. Initialized by initLayout().
     *
     * @var Button|array|false Button object, seed or false to not show button at all
     */
    public $buttonSave = [Button::class, 'Save', 'primary'];

    /**
     * When form is submitted successfully, this template is used by method
     * success() to replace form contents.
     *
     * WARNING: may be removed in the future as we refactor into using Message class
     *
     * @var string
     */
    public $successTemplate = 'form-success.html';

    /**
     * Collection of field's conditions for displaying a target field on the form.
     *
     * Specifying a condition for showing a target field required the name of the target field
     * and the rules to show that target field. Each rule contains a source field's name and a condition for the
     * source field. When each rule is true, then the target field is show on the form.
     *
     *  Combine multiple rules for showing a field.
     *   ex: ['target' => ['source1' => 'notEmpty', 'source2' => 'notEmpty']]
     *   Show 'target' if 'source1' is not empty AND 'source2' is notEmpty.
     *
     *  Combine multiple condition to the same source field.
     *   ex: ['target' => ['source1' => ['notEmpty','number']]
     *   Show 'target' if 'source1 is notEmpty AND is a number.
     *
     *  Combine multiple arrays of rules will OR the rules for the target field.
     *  ex: ['target' => [['source1' => ['notEmpty', 'number']], ['source1' => 'isExactly[5]']
     *  Show "target' if 'source1' is not empty AND is a number
     *      OR
     *  Show 'target' if 'source1' is exactly 5.
     *
     * @var array
     */
    public $controlDisplayRules = [];

    /**
     * Default css selector for JsConditionalForm.
     * Should match the css class name of the control.
     * Fomantic-UI use the class name "field".
     *
     * @var string
     */
    public $controlDisplaySelector = '.field';

    /**
     * Use this apiConfig variable to pass API settings to Semantic UI in .api().
     *
     * @var array
     */
    public $apiConfig = [];

    /**
     * Use this formConfig variable to pass settings to Semantic UI in .from().
     *
     * @var array
     */
    public $formConfig = [];

    // }}}

    // {{{ Base Methods

    /**
     * Constructor.
     *
     * @param mixed $defaults CSS class or seed array
     *
     * @todo this should also call parent::__construct, but we have to refactor View::__construct method parameters too
     */
    public function __construct($defaults = [])
    {
        if (!is_array($defaults)) {
            $defaults = [$defaults];
        }

        // CSS class
        if (array_key_exists(0, $defaults)) {
            $this->addClass($defaults[0]);
            unset($defaults[0]);
        }

        $this->setDefaults($defaults);
    }

    /**
     * Initialization.
     */
    protected function init(): void
    {
        parent::init();

        $this->formElement = View::addTo($this, ['element' => 'form', 'short_name' => 'form'], ['FormElementOnly']);

        // Initialize layout, so when you call addControl / setModel next time, form will know
        // where to add your fields.
        $this->initLayout();

        // set css loader for this form
        $this->setApiConfig(['stateContext' => '#' . $this->name]);

        $this->cb = $this->add(new JsCallback(), ['desired_name' => 'submit']);
    }

    /**
     * initialize form layout. You can inject custom layout
     * if you 'layout'=>.. to constructor.
     */
    protected function initLayout()
    {
        if ($this->layout === null) {
            $this->layout = [Form\Layout::class];
        }

        if (is_string($this->layout) || is_array($this->layout)) {
            $this->layout = Factory::factory($this->layout, ['form' => $this]);
            $this->layout = $this->add($this->layout);
        } elseif (is_object($this->layout)) {
            $this->layout->form = $this;
            $this->add($this->layout);
        } else {
            throw (new Exception('Unsupported specification of form layout. Can be array, string or object'))
                ->addMoreInfo('layout', $this->layout);
        }

        // allow to submit by pressing an enter key when child control is focused
        $this->on('submit', new JsExpression('if (event.target === this) { $([name]).form("submit"); }', ['name' => '#' . $this->formElement->name]));

        // Add save button in layout
        if ($this->buttonSave) {
            $this->buttonSave = $this->layout->addButton($this->buttonSave);
            $this->buttonSave->setAttr('tabindex', 0);
            $this->buttonSave->on('click', $this->js(null, null, $this->formElement)->form('submit'));
            $this->buttonSave->on('keypress', new JsExpression('if (event.keyCode === 13){ $([name]).form("submit"); }', ['name' => '#' . $this->formElement->name]));
        }
    }

    /**
     * Setter for control display rules.
     *
     * @param array $rules
     *
     * @return $this
     */
    public function setControlsDisplayRules($rules = [])
    {
        $this->controlDisplayRules = $rules;

        return $this;
    }

    /**
     * Set display rule for a group collection.
     *
     * @param array         $rules
     * @param string|object $selector
     *
     * @return $this
     */
    public function setGroupDisplayRules($rules = [], $selector = '.atk-form-group')
    {
        if (is_object($selector) && isset($selector->name)) {
            $selector = '#' . $selector->name;
        }

        $this->controlDisplayRules = $rules;
        $this->controlDisplaySelector = $selector;

        return $this;
    }

    /**
     * Associates form with the model but also specifies which of Model
     * fields should be added automatically.
     *
     * If $actualFields are not specified, then all "editable" fields
     * will be added.
     *
     * @param array $fields
     *
     * @return \Atk4\Data\Model
     */
    public function setModel(Model $model, $fields = null)
    {
        // Model is set for the form and also for the current layout
        try {
            $model = parent::setModel($model);
            $this->layout->setModel($model, $fields);

            return $model;
        } catch (Exception $e) {
            throw $e->addMoreInfo('model', $model);
        }
    }

    /**
     * Adds callback in submit hook.
     *
     * @return $this
     */
    public function onSubmit(\Closure $callback)
    {
        $this->onHook(self::HOOK_SUBMIT, $callback);

        $this->cb->set(function () {
            try {
                $this->loadPost();
                $response = $this->hook(self::HOOK_SUBMIT);

                if (!$response) {
                    if (!$this->model instanceof \Atk4\Ui\Misc\ProxyModel) {
                        $this->model->save();

                        return $this->success('Form data has been saved');
                    }

                    return new JsExpression('console.log([])', ['Form submission is not handled']);
                }

                return $response;
            } catch (\Atk4\Data\ValidationException $val) {
                $response = [];
                foreach ($val->errors as $field => $error) {
                    $response[] = $this->error($field, $error);
                }

                return $response;
            }
        });

        return $this;
    }

    /**
     * Return form control associated with the field.
     *
     * @param string $name Name of the control
     */
    public function getControl(string $name): Form\Control
    {
        return $this->controls[$name];
    }

    /**
     * Causes form to generate error.
     *
     * @param string $fieldName Field name
     * @param string $str       Error message
     *
     * @return JsChain|array
     */
    public function error($fieldName, $str)
    {
        // by using this hook you can overwrite default behavior of this method
        if ($this->hookHasCallbacks(self::HOOK_DISPLAY_ERROR)) {
            return $this->hook(self::HOOK_DISPLAY_ERROR, [$fieldName, $str]);
        }

        $jsError = [$this->js()->form('add prompt', $fieldName, $str)];

        return $jsError;
    }

    /**
     * Causes form to generate success message.
     *
     * @param View|string $success     Success message or a View to display in modal
     * @param string      $sub_header  Sub-header
     * @param bool        $useTemplate Backward compatibility
     *
     * @return JsChain
     */
    public function success($success = 'Success', $sub_header = null, $useTemplate = true)
    {
        $response = null;
        // by using this hook you can overwrite default behavior of this method
        if ($this->hookHasCallbacks(self::HOOK_DISPLAY_SUCCESS)) {
            return $this->hook(self::HOOK_DISPLAY_SUCCESS, [$success, $sub_header]);
        }

        if ($success instanceof View) {
            $response = $success;
        } elseif ($useTemplate) {
            $response = $this->getApp()->loadTemplate($this->successTemplate);
            $response->set('header', $success);

            if ($sub_header) {
                $response->set('message', $sub_header);
            } else {
                $response->del('p');
            }

            $response = $this->js()->html($response->renderToHtml());
        } else {
            $response = new Message([$success, 'type' => 'success', 'icon' => 'check']);
            $response->setApp($this->getApp());
            $response->invokeInit();
            $response->text->addParagraph($sub_header);
        }

        return $response;
    }

    // }}}

    // {{{ Layout Manipulation

    /**
     * Add form control into current layout. If no layout, create one. If no model, create blank one.
     *
     * @param array|string|object|null $control
     * @param array|string|object|null $field
     *
     * @return Form\Control
     */
    public function addControl(?string $name, $control = null, $field = null)
    {
        if (!$this->model) {
            $this->model = new \Atk4\Ui\Misc\ProxyModel();
        }

        return $this->layout->addControl($name, $control, $field);
    }

    /**
     * Add more than one field in one shot.
     *
     * @param array $controls
     *
     * @return $this
     */
    public function addControls($controls)
    {
        foreach ($controls as $control) {
            $this->addControl(...(array) $control);
        }

        return $this;
    }

    /**
     * Add header into the form, which appears as a separator.
     *
     * @param string $title
     *
     * @return Form\Layout
     */
    public function addHeader($title = null)
    {
        return $this->layout->addHeader($title);
    }

    /**
     * Creates a group of fields and returns layout.
     *
     * @param string|array $title
     *
     * @return Form\Layout
     */
    public function addGroup($title = null)
    {
        return $this->layout->addGroup($title);
    }

    /**
     * Returns JS Chain that targets INPUT element of a specified field. This method is handy
     * if you wish to set a value to a certain field.
     *
     * @param string $name Name of control
     *
     * @return JsChain
     */
    public function jsInput($name)
    {
        return $this->layout->getControl($name)->js()->find('input');
    }

    /**
     * Returns JS Chain that targets INPUT of a specified element. This method is handy
     * if you wish to set a value to a certain field.
     *
     * @param string $name Name of control
     *
     * @return JsChain
     */
    public function jsControl($name)
    {
        return $this->layout->getControl($name)->js();
    }

    // }}}

    // {{{ Internals

    /**
     * Provided with a Agile Data Model Field, this method have to decide
     * and create instance of a View that will act as a form-control. It takes
     * various input and looks for hints as to which class to use:.
     *
     * 1. The $seed argument is evaluated
     * 2. $f->ui['form'] is evaluated if present
     * 3. $f->type is converted into seed and evaluated
     * 4. lastly, falling back to Line, Dropdown (based on $reference and $enum)
     *
     * @param \Atk4\Data\Field $field Data model field
     * @param array            $seed  Defaults to pass to Factory::factory() when control object is initialized
     *
     * @return Form\Control
     */
    public function controlFactory(\Atk4\Data\Field $field, $seed = [])
    {
        if ($field && !$field instanceof \Atk4\Data\Field) {
            throw (new Exception('Argument 1 for controlFactory must be \Atk4\Data\Field or null'))
                ->addMoreInfo('field', $field);
        }

        $fallbackSeed = [Form\Control\Line::class];

        if ($field->type === 'array' && $field->reference) {
            $limit = ($field->reference instanceof ContainsMany) ? 0 : 1;
            $model = $field->reference->refModel();
            $fallbackSeed = [Form\Control\Multiline::class, 'model' => $model, 'rowLimit' => $limit, 'caption' => $model->getModelCaption()];
        } elseif ($field->type !== 'boolean') {
            if ($field->enum) {
                $fallbackSeed = [Form\Control\Dropdown::class, 'values' => array_combine($field->enum, $field->enum)];
            } elseif ($field->values) {
                $fallbackSeed = [Form\Control\Dropdown::class, 'values' => $field->values];
            } elseif (isset($field->reference)) {
                $fallbackSeed = [Form\Control\Lookup::class, 'model' => $field->reference->refModel()];
            }
        }

        if (isset($field->ui['hint'])) {
            $fallbackSeed['hint'] = $field->ui['hint'];
        }

        if (isset($field->ui['placeholder'])) {
            $fallbackSeed['placeholder'] = $field->ui['placeholder'];
        }

        $seed = Factory::mergeSeeds(
            $seed,
            $field->ui['form'] ?? null,
            $this->typeToControl[$field->type] ?? null,
            $fallbackSeed
        );

        $defaults = [
            'form' => $this,
            'field' => $field,
            'short_name' => $field->short_name,
        ];

        return Factory::factory($seed, $defaults);
    }

    /**
     * Provides control seeds for most common types.
     *
     * @var array Describes how factory converts type to control seed
     */
    protected $typeToControl = [
        'boolean' => [Form\Control\Checkbox::class],
        'text' => [Form\Control\Textarea::class],
        'string' => [Form\Control\Line::class],
        'password' => [Form\Control\Password::class],
        'datetime' => [Form\Control\Calendar::class, ['type' => 'datetime']],
        'date' => [Form\Control\Calendar::class, ['type' => 'date']],
        'time' => [Form\Control\Calendar::class, ['type' => 'time']],
        'money' => [Form\Control\Money::class],
    ];

    /**
     * Looks inside the POST of the request and loads it into a current model.
     */
    protected function loadPost()
    {
        $post = $_POST;

        $this->hook(self::HOOK_LOAD_POST, [&$post]);
        $errors = [];

        foreach ($this->controls as $key => $field) {
            try {
                // save field value only if field was editable in form at all
                if (!$field->readonly && !$field->disabled) {
                    $field->set($post[$key] ?? null);
                }
            } catch (\Atk4\Core\Exception $e) {
                $errors[$key] = $e->getMessage();
            }
        }

        if ($errors) {
            throw new \Atk4\Data\ValidationException($errors);
        }
    }

    protected function renderView(): void
    {
        $this->ajaxSubmit();
        if (!empty($this->controlDisplayRules)) {
            $this->js(true, new JsConditionalForm($this, $this->controlDisplayRules, $this->controlDisplaySelector));
        }

        parent::renderView();
    }

    protected function renderTemplateToHtml(string $region = null): string
    {
        $output = parent::renderTemplateToHtml($region);

        return $this->fixFormInRenderedHtml($output);
    }

    public function fixFormInRenderedHtml(string $html): string
    {
        $innerFormTags = ['button', 'datalist', 'fieldset', 'input', 'keygen', 'label', 'legend',
            'meter', 'optgroup', 'option', 'output', 'progress', 'select', 'textarea', ];

        return preg_replace('~<(' . implode('|', $innerFormTags) . ')(?!\w| form=")~i', '$0 form="' . $this->formElement->name . '"', $html);
    }

    /**
     * Set Semantic-ui Api settings to use with form. A complete list is here:
     * https://semantic-ui.com/behaviors/api.html#/settings.
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
     * Set Semantic-ui From settings to use with form. A complete list is here:
     * https://fomantic-ui.com/behaviors/form.html#/settings.
     *
     * @param array $config
     *
     * @return $this
     */
    public function setFormConfig($config)
    {
        $this->formConfig = array_merge($this->formConfig, $config);

        return $this;
    }

    /**
     * Does ajax submit.
     */
    public function ajaxSubmit()
    {
        $this->js(true)->form(array_merge(['inline' => true, 'on' => 'blur'], $this->formConfig));

        $this->js(true, null, $this->formElement)
            ->api(array_merge(['url' => $this->cb->getJsUrl(), 'method' => 'POST', 'serializeForm' => true], $this->apiConfig));

        $this->on('change', 'input, textarea, select', $this->js()->form('remove prompt', new JsExpression('$(this).attr("name")')));

        if (!$this->canLeave) {
            $this->js(true, (new JsChain('atk.formService'))->preventFormLeave($this->name));
        }
    }

    // }}}
}
