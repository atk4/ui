<?php
/**
 * A Simple inline editable text Vue component.
 */

namespace atk4\ui\Component;

use atk4\data\ValidationException;
use atk4\ui\Exception;
use atk4\ui\jsToast;
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
     * Input initial value.
     *
     * @var null
     */
    public $initValue = null;

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
    public $field = null;

    /**
     * Whether component should save it's value when input get blur.
     * Using this option will trigger callback when user is moving out of the
     * inline edit field, like pressing tab for example.
     *
     *  Otherwise, callback is fire when pressing Enter key,
     *  while inside the inline input field, only.
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
     *
     * The validation error msg function.
     * This function is call when a validation error occur and
     * give you a chance to format the error msg display inside
     * errorNotifier.
     *
     * A default one is supply if this is null.
     * It receive the error ($e) as parameter.
     *
     * @var null | callable
     */
    public $formatErrorMsg = null;

    /**
     * Initialization.
     */
    public function init()
    {
        parent::init();
        $this->cb = $this->add('jsCallback');

        // Set default validation error handler.
        if (!$this->formatErrorMsg || !is_callable($this->formatErrorMsg)) {
            $this->formatErrorMsg = function($e, $value) {
                $caption = $this->model->getElement($this->field)->getCaption();

                return "{$caption} - {$e->getMessage()}. <br>Trying to set this value: '{$value}'";
            };
        }
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
        $this->field = $this->field ? $this->field : $this->model->title_field;
        if ($this->autoSave && $this->model->loaded()) {
            if ($this->cb->triggered()) {
                $value = $_POST['value'] ? $_POST['value'] : null;
                $this->cb->set(function () use ($value) {
                    try {
                        $this->model[$this->field] = $this->app->ui_persistence->typecastLoadField($this->model->getElement($this->field), $value);
                        $this->model->save();

                        return $this->jsSuccess('Update successfully');
                    } catch (ValidationException $e) {
                        $this->app->terminate(json_encode([
                              'success'            => true,
                              'hasValidationError' => true,
                              'atkjs'              => $this->jsError(call_user_func($this->formatErrorMsg, $e, $value))->jsRender()
                          ]));
                    }
                });
            }
        }

        return $this->model;
    }

    /**
     * onChange handler.
     * You may supply your own function to handle update.
     * The function will receive one param:
     *  value:  the new input value.
     *
     * @param callable $fx
     */
    public function onChange($fx)
    {
        if (is_callable($fx) && !$this->autoSave) {
            if ($this->cb->triggered()) {
                $value = $_POST['value'] ? $_POST['value'] : null;
                $this->cb->set(function () use ($fx, $value) {
                    return call_user_func($fx, $value);
                });
            }
        }
    }

    /**
     * On success notifier.
     *
     * @param string $message
     *
     * @return \atk4\ui\jsToast
     */
    public function jsSuccess($message)
    {

        return new jsToast([
           'title'   => 'Success',
           'message' => $message,
           'class'   => 'success',
       ]);
    }

    /**
     * On validation error notifier.
     *
     * @param string $message
     *
     * @return \atk4\ui\jsToast
     */
    public function jsError($message)
    {

        return new jsToast([
           'title'          => 'Validation error:',
           'displayTime'    => 8000,
           'showIcon'       => 'exclamation',
           'message'        => $message,
           'class'          => 'error',
       ]);
    }

    /**
     * Renders View.
     */
    public function renderView()
    {
        parent::renderView();

        $type = ($this->model && $this->field) ? $this->model->elements[$this->field]->type : 'text';
        $type = ($type === 'string') ? 'text' : $type;

        if ($type != 'text' && $type != 'number') {
            throw new Exception('Error: Only string or number field can be edited inline. Field Type = '.$type);
        }

        if ($this->model && $this->model->loaded()) {
            $initValue = $this->model->get($this->field);
        } else {
            $initValue = $this->initValue;
        }

        $fieldName = $this->field ? $this->field : 'name';

        $this->template->set('inputCss', $this->inputCss);
        $this->template->trySet('fieldName', $fieldName);
        $this->template->trySet('fieldType', $type);

        $this->js(true, (new jsVueService())->createAtkVue(
            '#'.$this->name,
            'atk-inline-edit',
            [
                'initValue'     => $initValue,
                'url'           => $this->cb->getJSURL(),
                'saveOnBlur'    => $this->saveOnBlur,
            ]
        ));
    }
}
