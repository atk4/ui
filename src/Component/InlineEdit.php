<?php
/**
 * A Simple inline editable text Vue component.
 */

namespace atk4\ui\Component;

use atk4\ui\Exception;
use atk4\ui\jsVueService;
use atk4\ui\View;

class InlineEdit extends View
{
    public $defaultTemplate = 'inline-edit.html';

    /**
     * jsCallback for saving data.
     *
     * @var null
     */
    public $cb = null;

    /**
     * Whether callback should save value to db automatically or not.
     * Default to using onChange handler.
     * If set to true, then saving to db will be done when model get set
     * and if model is loaded already.
     *
     * @var bool
     */
    public $autoSave = false;

    /**
     * The actual db field name that need to be saved.
     * Default to title field when model is set.
     *
     * @var null|string The name of the field.
     */
    public $modelField = null;

    /**
     * Whether component should save it's value when input get blur.
     *
     * @var bool
     */
    public $saveOnBlur = true;

    /**
     * Default css for the input div.
     *
     * @var string
     */
    public $inputCss = 'ui right icon input';

    /**
     * Initialization.
     */
    public function init()
    {
        parent::init();
        $this->cb = $this->add('jsCallback');
    }

    /**
     * Set Model of this View.
     *
     * @param \atk4\data\Model $model
     *
     * @return \atk4\data\Model
     */
    public function setModel(\atk4\data\Model $model)
    {
        parent::setModel($model);
        $this->modelField = $this->modelField ? $this->modelField : $this->model->title_field;
        if ($this->autoSave && $this->model->loaded()) {
            if ($this->cb->triggered()) {
                $value = $_POST['value'] ? $_POST['value'] : null;
                $this->cb->set(function () use ($value) {
                    try {
                        $this->model[$this->modelField] = $value;
                        $this->model->save();

                        return $this->jsSuccess('Update successfully');
                    } catch (\atk4\data\Exception $e) {
                        return $this->jsError($e->getMessage());
                    } catch (\Error $e) {
                        return $this->jsError($e->getMessage());
                    }
                });
            }
        }

        return $this->model;
    }

    /**
     * onChange handler.
     * You may supply your own function to handle update.
     * The function will receive two params:
     *  id: The record id;
     *  value:  the new input value.
     *
     * @param callable $fx
     */
    public function onChange($fx)
    {
        if (is_callable($fx) && !$this->autoSave) {
            if ($this->cb->triggered()) {
                $id = $_POST['id'] ? $_POST['id'] : null;
                $value = $_POST['value'] ? $_POST['value'] : null;
                $this->cb->set(function () use ($fx, $id, $value) {
                    return call_user_func($fx, $id, $value);
                });
            }
        }
    }

    /**
     * On success message.
     *
     * @param string $message
     *
     * @return \atk4\ui\jsToast
     */
    public function jsSuccess($message)
    {
        return new \atk4\ui\jsToast([
                                        'title'   => 'Success',
                                        'message' => $message,
                                        'class'   => 'success',
                                    ]);
    }

    /**
     * On error message.
     *
     * @param string $message
     *
     * @return \atk4\ui\jsToast
     */
    public function jsError($message)
    {
        return new \atk4\ui\jsToast([
                                        'title'   => 'Error',
                                        'message' => $message,
                                        'class'   => 'error',
                                    ]);
    }

    /**
     * Renders View.
     */
    public function renderView()
    {
        parent::renderView();

        $type = ($this->model && $this->modelField) ? $this->model->elements[$this->modelField]->type : 'text';
        $type = ($type === 'string') ? 'text' : $type;

        if ($type != 'text' && $type != 'number') {
            throw new Exception('Error: Only string or number field can be edited inline. Field Type = '.$type);
        }

        $this->template->set('inputCss', $this->inputCss);
        $this->template->trySet('fieldName', $this->modelField);
        $this->template->trySet('fieldType', $type);

        $this->js(true, (new jsVueService())->createAtkVue(
            '#'.$this->name,
            'atk-inline-edit',
            [
                'initialValue' => $this->model->loaded() ? $this->model->get($this->modelField) : '',
                'id'           => $this->model->loaded() ? intval($this->model['id']) : null,
                'url'          => $this->cb->getJSURL(),
                'saveOnBlur'   => $this->saveOnBlur,
            ]
        ));
    }
}
