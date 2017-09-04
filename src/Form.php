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
     * A current layout of a form, needed if you call $form->addField().
     *
     * @var \atk4\ui\FormLayout\Generic
     */
    public $layout = null;

    /**
     * List of fields currently registered with this form.
     */
    public $fields = [];

    /**
     * Disables form contents.
     *
     * {@inheritdoc}
     */
    public $content = false;

    /**
     * Will point to the Save button. If you don't want to have save, destroy
     * it. Initialized by setLayout().
     *
     * @var Button
     */
    public $buttonSave;

    /**
     * When form is submitted successfully, this template is used by method
     * success() to replace form contents.
     *
     * WARNING: may be removed in the future as we refactor into using Message class
     *
     * @var string
     */
    public $successTemplate = 'form-success.html';

    // }}}

    // {{{ Base Methods
    public function init()
    {
        parent::init();

        // Initialize layout, so when you call addField / setModel next time, form will know
        // where to add your fields.
        $this->initLayout();

        //$this->addField('empty', new FormField\Hidden());

        // When form is submitted, will perform POST field loading.
        /*
        $this->addHook('submit', function () {

            // Field validation
            $result = $this->hook('validate');

            $errors = [];

            foreach ($result as $er) {
                if (!is_array($er)) {
                    continue;
                }

                foreach ($er as $field => $error) {
                    var_dump($error);
                    if ($error === null || $error === false) {
                        continue;
                    }

                    if (isset($errors[$field])) {
                        continue;
                    }
                    $errors[$field] = is_string($error) ? $error : 'Incorrect value specified';
                }
            }

            $return = [];

            if ($errors) {
                foreach ($errors as $field=>$error) {
                    $return[] = $this->error($field, $error);
                }

                return $return;
            }
        });
         */
    }

    /**
     * initialize form layout. You can inject custom layout
     * if you 'layout'=>.. to constructor.
     */
    protected function initLayout()
    {
        if (is_string($this->layout)) {
            $this->layout = [$this->layout];
        } elseif ($this->layout === null) {
            $this->layout = ['FormLayout/Generic'];
        }

        if (is_array($this->layout)) {
            $this->layout['form'] = $this;
            $this->layout = $this->add($this->layout);
        } elseif (is_object($this->layout)) {
            $this->layout->form = $this;
            $this->add($this->layout);
        } else {
            throw new Exception(['Unsupported specification of form layout. Can be array, string or object', 'layout'=>$this->layout]);
        }

        // Layout needs to have a save button
        $this->layout->addButton($this->buttonSave = new Button(['Save', 'primary']));
        $this->buttonSave->on('click', $this->js()->form('submit'));
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
        $model = parent::setModel($model);
        $this->layout->setModel($model, $fields);

        return $model;
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
     * Causes form to generate error.
     *
     * @param string $field Field name
     * @param string $str   Error message
     *
     * @return jsChain
     */
    public function error($field, $str)
    {
        return $this->js()->form('add prompt', $field, $str);
    }

    /**
     * Causes form to generate success message.
     *
     * @param string $str        Success message
     * @param string $sub_header Sub-header
     *
     * @return jsChain
     */
    public function success($str = 'Success', $sub_header = null)
    {
        /*
         * below code works, but polutes output with bad id=xx
        $success = new Message([$str, 'id'=>false, 'type'=>'success', 'icon'=>'check']);
        $success->app = $this->app;
        $success->init();
        $success->text->addParagraph($sub_header);
         */
        $success = $this->app->loadTemplate($this->successTemplate);
        $success['header'] = $str;

        if ($sub_header) {
            $success['message'] = $sub_header;
        } else {
            $success->del('p');
        }

        $js = $this->js()
            ->html($success->render());

        return $js;
    }

    // }}}

    // {{{ Layout Manipulation

    /**
     * Add field into current layout. If no layout, create one. If no model, create blank one.
     *
     * @param mixed ...$args
     *
     * @return FormField\Generic
     */
    public function addField(...$args)
    {
        if (!$this->model) {
            $this->model = new \atk4\ui\misc\ProxyModel();
        }

        return $this->layout->addField(...$args); //$this->fieldFactory($modelField));
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
     * and create instance of a View that will act as a form-field.
     *
     * @param mixed ...$args
     *
     * @return FormField\Generic
     */
    public function decoratorFactory(\atk4\data\Field $f, $defaults = [])
    {
        if (isset($defaults[0])) {
            $class = $defaults[0];
            unset($defaults[0]);
        } else {
            $class = null;
        }

        $defaults = array_merge(
            ['form'=>$this, 'field'=>$f, 'short_name'=>$f->short_name],
            $defaults
        );

        if (isset($f->ui['form'])) {
            $display = $f->ui['form'];

            if (is_string($display) || is_object($display)) {
                $display = [$display];
            }

            if (isset($display[0])) {
                $class = $class ?: $display[0];
                unset($display[0]);
            }

            $defaults = array_merge($display, $defaults);
        }

        if (!$class && $f->enum) {
            $defaults['values'] = array_combine($f->enum, $f->enum);

            $class = 'Dropdown';
        }

        // Field values can be picked from the model.
        if (isset($f->reference)) {
            $class = 'Dropdown';
            $defaults['model'] = $f->reference->refModel();
            //$dd = new FormField\Dropdown($arg);
            //$dd->setModel($f->reference->refModel());
            //return $dd;
        }

        if (isset($this->typeToDecorator[$f->type])) {
            $class = $this->typeToDecorator[$f->type];
        }

        if (!$class) {
            $class = 'Line';
        }

        return $this->factory($class, $defaults, 'FormField');
    }

    protected $typeToDecorator = [
        'boolean' => 'Checkbox',
        'text'    => 'Textarea',
        'string'  => 'Line',
        'password'=> 'Password',
        'datetime'=> 'Datetime',
        'date'    => 'Date',
        'time'    => 'Time',
        'money'   => 'Money',
    ];

    /*

        switch ($f->type) {
        case 'boolean':
            return new FormField\Checkbox($arg);

        case 'text':
            return new FormField\Textarea($arg);

        case 'string':
            return new FormField\Line($arg);

        case 'password':
            return new FormField\Password($arg);

        case 'datetime':
            $arg['options']['ampm'] = false;

            return new FormField\Calendar($arg);

        case 'date':
            $arg['type'] = 'date';

            return new FormField\Calendar($arg);

        case 'time':
            $arg['type'] = 'time';
            $arg['options']['ampm'] = false;

            return new FormField\Calendar($arg);

        case 'money':
            return new FormField\Money($arg);

        case null:
            return new FormField\Line($arg);

        default:
            return new FormField\Line($arg);

        }
     */

    /**
     * Looks inside the POST of the request and loads it into a current model.
     */
    public function loadPOST()
    {
        $post = $_POST;

        $this->hook('loadPOST', [&$post]);
        $data = [];
        $errors = [];

        foreach ($this->fields as $key=>$field) {
            try {
                $value = isset($post[$key]) ? $post[$key] : null;

                $this->model[$key] = $this->app->ui_persistence->typecastLoadField($field->field, $value);
            } catch (\atk4\core\Exception $e) {
                $errors[$key] = $e->getMessage();
            }
        }

        if ($errors) {
            throw new \atk4\data\ValidationException($errors);
        }
    }

    public function renderView()
    {
        $this->ajaxSubmit();

        return parent::renderView();
    }

    /**
     * Does ajax submit.
     */
    public function ajaxSubmit()
    {
        $this->_add($cb = new jsCallback(), ['desired_name'=>'submit', 'POST_trigger'=>true]);

        $this->add(new View(['element'=>'input']))
            ->setAttr('name', $cb->name)
            ->setAttr('value', 'submit')
            ->setStyle(['display'=>'none']);

        $cb->set(function () {
            try {
                $this->loadPOST();
                $response = $this->hook('submit');
                if (!$response) {
                    if (!$this->model instanceof \atk4\ui\misc\ProxyModel) {
                        $this->model->save();

                        return $this->success('Form data has been saved');
                    } else {
                        return new jsExpression('console.log([])', ['Form submission is not handled']);
                    }
                }
            } catch (\atk4\data\ValidationException $val) {
                $response = [];
                foreach ($val->errors as $field=>$error) {
                    $response[] = $this->error($field, $error);
                }

                return $response;
            }

            return $response;
        });

        $this->js(true)
            ->api(['url'=>$cb->getURL(),  'method'=>'POST', 'serializeForm'=>true])
            ->form(['inline'=>true, 'on'=>'blur']);

        $this->on('change', 'input', $this->js()->form('remove prompt', new jsExpression('$(this).attr("name")')));
    }

    // }}}
}
