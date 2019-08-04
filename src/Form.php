<?php

// vim:ts=4:sw=4:et:fdm=marker:fdl=0

namespace atk4\ui;

/**
 * Implements a form.
 */
class Form extends View //implements \ArrayAccess - temporarily so that our build script dont' complain
{
    use \atk4\core\HookTrait;

    // {{{ Properties

    public $ui = 'form';
    public $defaultTemplate = 'form.html';

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
     * A current layout of a form, needed if you call $form->addField().
     *
     * @var \atk4\ui\FormLayout\Generic
     */
    public $layout = null;

    /**
     * List of fields currently registered with this form.
     *
     * @var array Array of FormField objects
     */
    public $fields = [];

    public $content = false;

    /**
     * Will point to the Save button. If you don't want to have save button, then set this to false
     * or destroy it. Initialized by setLayout().
     *
     * @var Button|array|false Button object, seed or false to not show button at all
     */
    public $buttonSave = ['Button', 'Save', 'primary'];

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
    public $fieldsDisplayRules = [];

    /**
     * Default selector for jsConditionalForm.
     *
     * @var string
     */
    public $fieldDisplaySelector = '.field';

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
    public function init()
    {
        parent::init();

        // Initialize layout, so when you call addField / setModel next time, form will know
        // where to add your fields.
        $this->initLayout();
    }

    /**
     * initialize form layout. You can inject custom layout
     * if you 'layout'=>.. to constructor.
     */
    protected function initLayout()
    {
        if ($this->layout === null) {
            $this->layout = 'Generic';
        }

        if (is_string($this->layout) || is_array($this->layout)) {
            $this->layout = $this->factory($this->layout, ['form'=>$this], 'atk4\ui\FormLayout');
            $this->layout = $this->add($this->layout);
        } elseif (is_object($this->layout)) {
            $this->layout->form = $this;
            $this->add($this->layout);
        } else {
            throw new Exception(['Unsupported specification of form layout. Can be array, string or object', 'layout' => $this->layout]);
        }

        // Add save button in layout
        if ($this->buttonSave) {
            $this->buttonSave = $this->layout->addButton($this->buttonSave);
            $this->buttonSave->setAttr('tabindex', 0);
            $this->buttonSave->on('click', $this->js()->form('submit'));
            $this->buttonSave->on('keypress', new jsExpression('if (event.keyCode === 13){$([name]).form("submit");}', ['name' => '#'.$this->name]));
        }
    }

    /**
     * Setter for field display rules.
     *
     * @param array $rules
     *
     * @return $this
     */
    public function setFieldsDisplayRules($rules = [])
    {
        $this->fieldsDisplayRules = $rules;

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
            $selector = '#'.$selector->name;
        }

        $this->fieldsDisplayRules = $rules;
        $this->fieldDisplaySelector = $selector;

        return $this;
    }

    /**
     * Associates form with the model but also specifies which of Model
     * fields should be added automatically.
     *
     * If $actualFields are not specified, then all "editable" fields
     * will be added.
     *
     * @param \atk4\data\Model $model
     * @param array            $fields
     *
     * @return \atk4\data\Model
     */
    public function setModel(\atk4\data\Model $model, $fields = null)
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
     * @param callable $callback
     */
    public function onSubmit($callback)
    {
        $this->addHook('submit', $callback);

        return $this;
    }

    /**
     * Return Field decorator associated with
     * the field.
     *
     * @param string $name Name of the field
     */
    public function getField($name)
    {
        return $this->fields[$name];
    }

    /**
     * Causes form to generate error.
     *
     * @param string $fieldName Field name
     * @param string $str       Error message
     *
     * @return jsChain|array
     */
    public function error($fieldName, $str)
    {
        // by using this hook you can overwrite default behavior of this method
        if ($this->hookHasCallbacks('displayError')) {
            return $this->hook('displayError', [$fieldName, $str]);
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
     * @throws Exception
     * @throws \atk4\core\Exception
     *
     * @return jsChain
     */
    public function success($success = 'Success', $sub_header = null, $useTemplate = true)
    {
        $response = null;
        // by using this hook you can overwrite default behavior of this method
        if ($this->hookHasCallbacks('displaySuccess')) {
            return $this->hook('displaySuccess', [$success, $sub_header]);
        }

        if ($success instanceof View) {
            $response = $success;
        } elseif ($useTemplate) {
            $response = $this->app->loadTemplate($this->successTemplate);
            $response['header'] = $success;

            if ($sub_header) {
                $response['message'] = $sub_header;
            } else {
                $response->del('p');
            }

            $response = $this->js()->html($response->render());
        } else {
            $response = new Message([$success, 'type'=>'success', 'icon'=>'check']);
            $response->app = $this->app;
            $response->init();
            $response->text->addParagraph($sub_header);
        }

        return $response;
    }

    // }}}

    // {{{ Layout Manipulation

    /**
     * Add field into current layout. If no layout, create one. If no model, create blank one.
     *
     * @param string|null              $name
     * @param array|string|object|null $decorator
     * @param array|string|object|null $field
     *
     * @return FormField\Generic
     */
    public function addField($name, $decorator = null, $field = null)
    {
        if (!$this->model) {
            $this->model = new \atk4\ui\misc\ProxyModel();
        }

        return $this->layout->addField($name, $decorator, $field);
    }

    /**
     * Add more than one field in one shot.
     *
     * @param array $fields
     *
     * @return $this
     */
    public function addFields($fields)
    {
        foreach ($fields as $field) {
            if (is_array($field)) {
                $this->addField(...$field);
            } else {
                $this->addField($field);
            }
        }

        return $this;
    }

    /**
     * Add header into the form, which appears as a separator.
     *
     * @param string $title
     *
     * @return \atk4\ui\FormLayout\Generic
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
     * @return \atk4\ui\FormLayout\Generic
     */
    public function addGroup($title = null)
    {
        return $this->layout->addGroup($title);
    }

    /**
     * Returns JS Chain that targets INPUT element of a specified field. This method is handy
     * if you wish to set a value to a certain field.
     *
     * @param string $name Name of element
     *
     * @return jsChain
     */
    public function jsInput($name)
    {
        return $this->layout->getElement($name)->js()->find('input');
    }

    /**
     * Returns JS Chain that targets INPUT element of a specified field. This method is handy
     * if you wish to set a value to a certain field.
     *
     * @param string $name Name of element
     *
     * @return jsChain
     */
    public function jsField($name)
    {
        return $this->layout->getElement($name)->js();
    }

    // }}}

    // {{{ Internals

    /**
     * Provided with a Agile Data Model Field, this method have to decide
     * and create instance of a View that will act as a form-field. It takes
     * various input and looks for hints as to which class to use:.
     *
     * 1. The $seed argument is evaluated
     * 2. $f->ui['form'] is evaluated if present
     * 3. $f->type is converted into seed and evaluated
     * 4. lastly, falling back to Line, DropDown (based on $reference and $enum)
     *
     * @param \atk4\data\Field $f    Data model field
     * @param array            $seed Defaults to pass to factory() when decorator is initialized
     *
     * @return FormField\Generic
     */
    public function decoratorFactory(\atk4\data\Field $f, $seed = [])
    {
        if ($f && !$f instanceof \atk4\data\Field) {
            throw new Exception(['Argument 1 for decoratorFactory must be \atk4\data\Field or null', 'f' => $f]);
        }

        $fallback_seed = ['Line'];

        if ($f->type != 'boolean') {
            if ($f->enum) {
                $fallback_seed = ['DropDown', 'values' => array_combine($f->enum, $f->enum)];
            } elseif ($f->values) {
                $fallback_seed = ['DropDown', 'values' => $f->values];
            } elseif (isset($f->reference)) {
                $fallback_seed = ['Lookup', 'model' => $f->reference->refModel()];
            }
        }

        if (isset($f->ui['hint'])) {
            $fallback_seed['hint'] = $f->ui['hint'];
        }

        if (isset($f->ui['placeholder'])) {
            $fallback_seed['placeholder'] = $f->ui['placeholder'];
        }

        $seed = $this->mergeSeeds(
            $seed,
            isset($f->ui['form']) ? $f->ui['form'] : null,
            isset($this->typeToDecorator[$f->type]) ? $this->typeToDecorator[$f->type] : null,
            $fallback_seed
        );

        $defaults = [
            'form'       => $this,
            'field'      => $f,
            'short_name' => $f->short_name,
        ];

        /* @var TYPE_NAME $this */
        return $this->factory($seed, $defaults, 'atk4\ui\FormField');
    }

    /**
     * Provides decorator seeds for most common types.
     *
     * @var array Describes how factory converts type to decorator seed
     */
    protected $typeToDecorator = [
        'boolean'  => 'CheckBox',
        'text'     => 'TextArea',
        'string'   => 'Line',
        'password' => 'Password',
        'datetime' => 'Calendar',
        'date'     => ['Calendar', 'type' => 'date'],
        'time'     => ['Calendar', 'type' => 'time', 'ampm' => false],
        'money'    => 'Money',
    ];

    /**
     * Looks inside the POST of the request and loads it into a current model.
     */
    public function loadPOST()
    {
        $post = $_POST;

        $this->hook('loadPOST', [&$post]);
        $errors = [];

        foreach ($this->fields as $key => $field) {
            try {
                $value = isset($post[$key]) ? $post[$key] : null;

                // save field value only if field was editable in form at all
                if (!$field->readonly && !$field->disabled) {
                    $this->model[$key] = $this->app->ui_persistence->typecastLoadField($field->field, $value);
                }
            } catch (\atk4\core\Exception $e) {
                $errors[$key] = $e->getMessage();
            }
        }

        if ($errors) {
            throw new \atk4\data\ValidationException($errors);
        }
    }

    /**
     * Renders view.
     */
    public function renderView()
    {
        $this->ajaxSubmit();
        if (!empty($this->fieldsDisplayRules)) {
            $this->js(true, new jsConditionalForm($this, $this->fieldsDisplayRules, $this->fieldDisplaySelector));
        }

        return parent::renderView();
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
        $this->_add($cb = new jsCallback(), ['desired_name' => 'submit', 'postTrigger' => true]);

        $this->add(new View(['element' => 'input']))
            ->setAttr('name', $cb->postTrigger)
            ->setAttr('value', 'submit')
            ->setStyle(['display' => 'none']);

        $cb->set(function () {
            $caught = function ($e, $useWindow) {
                $html = '<div class="header"> '.
                        htmlspecialchars(get_class($e)).
                        ' </div> <div class="content"> '.
                        ($e instanceof \atk4\core\Exception ? $e->getHTML() : nl2br(htmlspecialchars($e->getMessage()))).
                        ' </div>';
                $this->app->terminate(json_encode(['success' => false, 'message' => $html, 'useWindow' => $useWindow]));
            };

            try {
                $this->loadPOST();
                ob_start();
                $response = $this->hook('submit');
                $output = ob_get_clean();

                if ($output) {

                    $message = new Message('Direct Output Detected');
                    $message->init();
                    $message->addClass('error');
                    $message->text->set($output);
                    return $message;
                }

                if (!$response) {
                    if (!$this->model instanceof \atk4\ui\misc\ProxyModel) {
                        $this->model->save();

                        return $this->success('Form data has been saved');
                    }

                    return new jsExpression('console.log([])', ['Form submission is not handled']);
                }
            } catch (\atk4\data\ValidationException $val) {
                $response = [];
                foreach ($val->errors as $field => $error) {
                    $response[] = $this->error($field, $error);
                }

                return $response;
            }

            return $response;
        });

        //var_dump($cb->getURL());
        $this->js(true)
            ->api(array_merge(['url' => $cb->getJSURL(), 'method' => 'POST', 'serializeForm' => true], $this->apiConfig))
            ->form(array_merge(['inline' => true, 'on' => 'blur'], $this->formConfig));

        $this->on('change', 'input', $this->js()->form('remove prompt', new jsExpression('$(this).attr("name")')));

        if (!$this->canLeave) {
            $this->js(true, (new jsChain('atk.formService'))->preventFormLeave($this->name));
        }
    }

    // }}}
}
