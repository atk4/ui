<?php

declare(strict_types=1);

namespace Atk4\Ui\VueComponent;

use Atk4\Data\Model;
use Atk4\Data\ValidationException;
use Atk4\Ui\Exception;
use Atk4\Ui\JsCallback;
use Atk4\Ui\JsToast;
use Atk4\Ui\View;

/**
 * A Simple inline editable text Vue component.
 */
class InlineEdit extends View
{
    public $defaultTemplate = 'inline-edit.html';

    /** @var JsCallback JsCallback for saving data. */
    public $cb;

    /** @var mixed Input initial value. */
    public $initValue;

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
     * @var string|null the name of the field
     */
    public $fieldName;

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

    /** @var string Default css for the input div. */
    public $inputCss = 'ui right icon input';

    /**
     * The validation error msg function.
     * This function is call when a validation error occur and
     * give you a chance to format the error msg display inside
     * errorNotifier.
     *
     * A default one is supply if this is null.
     * It receive the error ($e) as parameter.
     *
     * @var \Closure|null
     */
    public $formatErrorMsg;

    protected function init(): void
    {
        parent::init();

        $this->cb = JsCallback::addTo($this);

        // Set default validation error handler.
        if (!$this->formatErrorMsg) {
            $this->formatErrorMsg = function (ValidationException $e, string $value) {
                $caption = $this->model->getField($this->fieldName)->getCaption();

                return $caption . ' - ' . $e->getMessage() . '. <br>Trying to set this value: "' . $value . '"';
            };
        }
    }

    public function setModel(Model $model): void
    {
        parent::setModel($model);

        if ($this->fieldName === null) {
            $this->fieldName = $this->model->titleField;
        }

        if ($this->autoSave && $this->model->isLoaded()) {
            $value = $_POST['value'] ?? null;
            $this->cb->set(function () use ($value) {
                try {
                    $this->model->set($this->fieldName, $this->getApp()->uiPersistence->typecastLoadField($this->model->getField($this->fieldName), $value));
                    $this->model->save();

                    return $this->jsSuccess('Update successfully');
                } catch (ValidationException $e) {
                    $this->getApp()->terminateJson([
                        'success' => true,
                        'hasValidationError' => true,
                        'atkjs' => $this->jsError(($this->formatErrorMsg)($e, $value))->jsRender(),
                    ]);
                }
            });
        }
    }

    /**
     * You may supply your own function to handle update.
     * The function will receive one param:
     *  value: the new input value.
     */
    public function onChange(\Closure $fx): void
    {
        if (!$this->autoSave) {
            $value = $this->getApp()->uiPersistence->typecastLoadField($this->model->getField($this->fieldName), $_POST['value'] ?? null);
            $this->cb->set(function () use ($fx, $value) {
                return $fx($value);
            });
        }
    }

    /**
     * On success notifier.
     *
     * @return JsToast
     */
    public function jsSuccess(string $message)
    {
        return new JsToast([
            'title' => 'Success',
            'message' => $message,
            'class' => 'success',
        ]);
    }

    /**
     * On validation error notifier.
     *
     * @param string $message
     *
     * @return JsToast
     */
    public function jsError($message)
    {
        return new JsToast([
            'title' => 'Validation error:',
            'displayTime' => 8000,
            'showIcon' => 'exclamation',
            'message' => $message,
            'class' => 'error',
        ]);
    }

    /**
     * Renders View.
     */
    protected function renderView(): void
    {
        parent::renderView();

        $type = $this->model && $this->fieldName ? $this->model->getField($this->fieldName)->type : 'string';
        $type = $type === 'string' ? 'text' : $type;

        if ($type !== 'text' && $type !== 'integer') {
            throw new Exception('Only string or number field can be edited inline. Field Type = ' . $type);
        }

        if ($this->model && $this->model->isLoaded()) {
            $initValue = $this->model->get($this->fieldName);
        } else {
            $initValue = $this->initValue;
        }

        $fieldName = $this->fieldName ?? 'name';

        $this->vue('atk-inline-edit', [
            'initValue' => $initValue,
            'url' => $this->cb->getJsUrl(),
            'saveOnBlur' => $this->saveOnBlur,
            'options' => ['fieldName' => $fieldName, 'fieldType' => $type, 'inputCss' => $this->inputCss],
        ]);
    }
}
