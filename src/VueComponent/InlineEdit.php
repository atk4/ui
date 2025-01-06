<?php

declare(strict_types=1);

namespace Atk4\Ui\VueComponent;

use Atk4\Data\Model;
use Atk4\Data\ValidationException;
use Atk4\Ui\Js\Jquery;
use Atk4\Ui\Js\JsCallbackLoadableValue;
use Atk4\Ui\Js\JsExpressionable;
use Atk4\Ui\Js\JsToast;
use Atk4\Ui\JsCallback;
use Atk4\Ui\View;
use Atk4\Ui\View\EntityTrait;

/**
 * A Simple inline editable text Vue component.
 */
class InlineEdit extends View
{
    use EntityTrait {
        setEntity as private _setEntity;
    }

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

    /** @var string Default CSS for the input div. */
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
     * @var \Closure(ValidationException, mixed): string|null
     */
    public $formatErrorMsg;

    #[\Override]
    protected function init(): void
    {
        parent::init();

        $this->cb = JsCallback::addTo($this);

        // set default validation error handler
        if ($this->formatErrorMsg === null) {
            $this->formatErrorMsg = function (ValidationException $e, $value) {
                $caption = $this->entity->getField($this->fieldName)->getCaption();

                return $this->getApp()->encodeHtml($caption) . ' - ' . $this->getApp()->encodeHtml($e->getMessage())
                    . '. <br>Trying to set this value: "' . $this->getApp()->encodeHtml($value) . '"';
            };
        }
    }

    public function setEntity(Model $entity): void
    {
        $this->_setEntity($entity);

        if ($this->fieldName === null) {
            $this->fieldName = $this->entity->titleField;
        }

        if ($this->autoSave && $this->entity->isLoaded()) {
            $this->cb->set(function (Jquery $j, $value) {
                try {
                    $this->entity->set($this->fieldName, $value);
                    $this->entity->save();

                    return $this->jsSuccess('Update saved');
                } catch (ValidationException $e) {
                    $this->getApp()->terminateJson([
                        'success' => true,
                        'hasValidationError' => true,
                        'atkjs' => $this->jsError(($this->formatErrorMsg)($e, $value))->jsRender(),
                    ]);
                }
            }, ['value' => new JsCallbackLoadableValue(null, function ($v) {
                return $this->getApp()->uiPersistence->typecastLoadField(
                    $this->entity->getField($this->fieldName),
                    $v
                );
            })]);
        }
    }

    /**
     * You may supply your own function to handle update.
     * The function will receive one param:
     *  value: the new input value.
     *
     * @param \Closure(mixed): (JsExpressionable|View|string|void) $fx
     */
    public function onChange(\Closure $fx): void
    {
        if (!$this->autoSave) {
            $this->cb->set(static function (Jquery $j, $value) use ($fx) {
                return $fx($value);
            }, ['value' => new JsCallbackLoadableValue(null, function ($v) {
                return $this->getApp()->uiPersistence->typecastLoadField(
                    $this->entity->getField($this->fieldName),
                    $v
                );
            })]);
        }
    }

    public function jsSuccess(string $message): JsExpressionable
    {
        return new JsToast([
            'title' => 'Success',
            'message' => $message,
            'class' => 'success',
        ]);
    }

    /**
     * @param string $message
     */
    public function jsError($message): JsExpressionable
    {
        return new JsToast([
            'title' => 'Validation error:',
            'displayTime' => 8000,
            'showIcon' => 'exclamation',
            'message' => $message,
            'class' => 'error',
        ]);
    }

    #[\Override]
    protected function renderView(): void
    {
        parent::renderView();

        if ($this->entity !== null && $this->entity->isLoaded()) {
            $initValue = $this->entity->get($this->fieldName);
        } else {
            $initValue = $this->initValue;
        }

        $fieldName = $this->fieldName ?? 'name';

        $this->vue('AtkInlineEdit', [
            'initValue' => $initValue,
            'url' => $this->cb->getJsUrl(),
            'saveOnBlur' => $this->saveOnBlur,
            'options' => ['fieldName' => $fieldName, 'inputCss' => $this->inputCss],
        ]);
    }
}
