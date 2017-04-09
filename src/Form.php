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

        // When form is submitted, will perform POST field loading.
        $this->addHook('submit', [$this, 'loadPOST']);
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
     * @return Form\Field\Generic
     */
    public function fieldFactory(...$args)
    {
        if (is_string($args[0]) && ($modelField = $this->model->hasElement($args[0]))) {
            // $modelField is set above
        } elseif ($args[0] instanceof \atk4\data\Field) {
            $modelField = $args[0];
        } else {
            $modelField = $this->model->addField(...$args);
            $modelField->never_persist = true;
        }

        return $this->_fieldFactory($modelField);
    }

    /**
     * Will come up with a column object based on the field object supplied.
     *
     * @param \atk4\data\Field $f
     *
     * @return FormField\Generic
     */
    public function _fieldFactory(\atk4\data\Field $f)
    {
        switch ($f->type) {
        case 'boolean':
            return new FormField\Checkbox(['form'=>$this, 'field'=>$f, 'short_name'=>$f->short_name]);

        default:
            return new FormField\Line(['form'=>$this, 'field'=>$f, 'short_name'=>$f->short_name]);

        }
    }

    /**
     * Looks inside the POST of the request and loads it into a current model.
     */
    public function loadPOST()
    {
        $data = $_POST;

        $this->hook('loadPOST', [&$data]);

        $data = array_intersect_key($data, $this->fields);


        $this->model->set($this->app->ui_persistence->typecastLoadRow($this->model, $data));
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
            ->setAttr('type', 'hidden');

        $cb->set(function () {
            $response = $this->hook('submit');
            if (!$response) {
                return new jsExpression('console.log([])', ['Form submission is not handled']);
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
