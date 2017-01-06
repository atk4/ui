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

    public $layout = null;

    public function addField(...$args)
    {
        if (!$this->model) {
            $this->model = new \atk4\ui\misc\ProxyModel();
        }

        if (!$this->layout) {
            $this->setLayout(new \atk4\ui\FormLayout\Vertical(['form'=>$this]));
        }

        if ($modelField = $this->model->hasElement($args[0])) {
            $formField = $this->layout->addField($this->fieldFactory($modelField));
        } else {
            $modelField = $this->model->addField(...$args);
            $formField = $this->layout->addField($this->fieldFactory($modelField));
        }

        return $formField;
    }

    public function setLayout($layout)
    {
        $this->layout = $this->add($layout);
        $this->layout->addButton($button = new Button(['Save', 'primary']));
        $button->on('click', $this->js()->form('submit'));
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
    public function fieldFactory(\atk4\data\Field $f)
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
            $this->setLayout(new \atk4\ui\FormLayout\Vertical(['form'=>$this]));
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
    }

    public function error($field, $str)
    {
        return $this->js()->form('add prompt', $field, $str);
    }

    public function renderView()
    {
        $this->ajaxSubmit();

        return parent::renderView();
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
            ->form(['inline'=>true]);

        $this->on('change', 'input', $this->js()->form('remove prompt', new jsExpression('$(this).attr("name")')));
    }
}
