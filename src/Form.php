<?php

namespace atk4\ui;

/**
 * Implements a form.
 */
class Form extends View //implements \ArrayAccess - temporarily so that our build script dont' complain
{
    use \atk4\core\HookTrait;

    public $ui = 'form';

    public $defaultTemplate = 'form.html';

    /**
     * When form is submitted successfully, this template is used by method success() to replace form contents.
     */
    public $successTemplate = 'form-success.html';

    /**
     * A current layout of a form, needed if you call $form->addField().
     */
    public $layout = null;

    /**
     * Disables form contents.
     */
    public $content = false;

    /**
     * Will point to the Save button. If you don't want to have save, destroy it. Initialized by setLayout().
     */
    public $buttonSave;

    /**
     * Add field into current layout. If no layout, create one. If no model, create blank one.
     */
    public function addField(...$args)
    {
        if (!$this->model) {
            $this->model = new \atk4\ui\misc\ProxyModel();
        }

        if (!$this->layout) {
            $this->setLayout();
        }

        return $this->layout->addField(...$args); //$this->fieldFactory($modelField));

        return $formField;
    }

    /**
     * Add header into the form, which appears as a separator.
     */
    public function addHeader($title = null)
    {
        if (!$this->layout) {
            $this->setLayout();
        }

        return $this->layout->addHeader($title);
    }

    /**
     * Creates a group of fields and returts layout.
     */
    public function addGroup($title = null)
    {
        if (!$this->layout) {
            $this->setLayout();
        }

        return $this->layout->addGroup($title);
    }

    public function setLayout($layout = null)
    {
        if (!$layout) {
            $layout = new \atk4\ui\FormLayout\Generic(['form'=>$this]);
        }

        $this->layout = $this->add($layout);
        $this->layout->addButton($this->buttonSave = new Button(['Save', 'primary']));
        $this->buttonSave->on('click', $this->js()->form('submit'));
    }

    public function onSubmit($callback)
    {
        $this->addHook('submit', $callback);
    }

    /**
     * Provided with a Agile Data Model Field, this method have to decide
     * and create instance of a View that will act as a form-field.
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
        }

        return $this->_fieldFactory($modelField);
    }

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
     * Associates form with the model but also specifies which of Model
     * fields should be added automatically.
     *
     * If $actualFields are not specified, then all "editable" fields
     * will be added.
     */
    public function setModel(\atk4\data\Model $model, $fields = null)
    {
        $model = parent::setModel($model);

        // Will not try to populate any fields
        if ($fields === false) {
            return $model;
        }

        if (!$this->layout) {
            $this->setLayout(new \atk4\ui\FormLayout\Generic(['form'=>$this]));
        }

        if ($fields === null) {
            $fields = [];
            foreach ($model->elements as $f) {
                if (!$f instanceof \atk4\data\Field) {
                    continue;
                }

                if (!$f->isEditable()) {
                    continue;
                }
                $fields[] = $f->short_name;
            }
        }

        if (is_array($fields)) {
            foreach ($fields as $field) {
                $modelField = $model->getElement($field);

                $formField = $this->layout->addField($this->fieldFactory($modelField));
            }
        } else {
            throw new Exception(['Incorrect value for $fields', 'fields'=>$fields]);
        }
    }

    public function init()
    {
        parent::init();

        $this->addHook('submit', [$this, 'loadPOST']);
    }

    /**
     * Looks inside the POST of the request and loads it into a current model.
     */
    public function loadPOST()
    {
        $post = new \atk4\ui\Persistence\POST($_POST);
        $this->model->load(0, $post);
    }

    /**
     * Causes form to generate error.
     */
    public function error($field, $str)
    {
        return $this->js()->form('add prompt', $field, $str);
    }

    public function success($str = 'Success', $sub_header = null)
    {
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

    public function renderView()
    {
        $this->ajaxSubmit();

        return parent::renderView();
    }

    /**
     * Returns JS Chain that targets INPUT element of a specified field. This method is handy
     * if you wish to set a value to a certain field.
     */
    public function jsInput($name)
    {
        return $this->layout->getElement($name)->js()->find('input');
    }

    /**
     * Returns JS Chain that targets INPUT element of a specified field. This method is handy
     * if you wish to set a value to a certain field.
     */
    public function jsField($name)
    {
        return $this->layout->getElement($name)->js();
    }

    public function ajaxSubmit()
    {
        $this->_add($cb = new jsCallback(), ['desired_name'=>'submit']);

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
}
