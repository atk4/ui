<?php

declare(strict_types=1);

namespace Atk4\Ui\Form\Control;

use Atk4\Ui\Button;
use Atk4\Ui\Exception;
use Atk4\Ui\Js\JsBlock;
use Atk4\Ui\Js\JsExpressionable;
use Atk4\Ui\JsCallback;

/**
 * @phpstan-type PhpFileArray array{error: int, name: string}
 */
class Upload extends Input
{
    public $defaultTemplate = 'form/control/upload.html';

    public string $inputType = 'hidden';

    /**
     * The uploaded file ID.
     * This ID is return on form submit.
     * If not set, will default to file name.
     * file ID is also sent with onDelete Callback.
     *
     * @var string|null
     */
    public $fileId;

    /** @var JsCallback Callback is use for onUpload or onDelete. */
    public $cb;

    /**
     * Allow multiple file or not.
     * CURRENTLY NOT SUPPORTED.
     *
     * @var bool
     */
    public $multiple = false;

    /**
     * An array of string value for accept file type.
     * ex: ['.jpg', '.jpeg', '.png'] or ['images/*'].
     *
     * @var array
     */
    public $accept = [];

    /** Whether callback has been defined or not. */
    public bool $hasUploadCb = false;
    /** Whether callback has been defined or not. */
    public bool $hasDeleteCb = false;

    /** @var list<JsExpressionable> */
    public $jsActions = [];

    public const UPLOAD_ACTION = 'upload';
    public const DELETE_ACTION = 'delete';

    protected function init(): void
    {
        parent::init();

        $this->cb = JsCallback::addTo($this);

        if ($this->action === null) {
            $this->action = new Button([
                'icon' => 'upload',
                'class.disabled' => $this->disabled || $this->readOnly,
            ]);
        }
    }

    /**
     * Allow to set file ID and file name
     *  - fileId will be the file ID sent with onDelete callback.
     *  - fileName is the field value display to user.
     *
     * @param string      $fileId   Field ID for onDelete Callback
     * @param string|null $fileName Field name display to user
     *
     * @return $this
     */
    public function set($fileId = null, $fileName = null)
    {
        $this->setFileId($fileId);

        if ($fileName === null) {
            $fileName = $fileId;
        }

        return $this->setInput($fileName);
    }

    /**
     * Set input field value.
     *
     * @param mixed $value
     *
     * @return $this
     */
    public function setInput($value)
    {
        return parent::set($value);
    }

    /**
     * Get input field value.
     *
     * @return mixed
     */
    public function getInputValue()
    {
        return $this->entityField ? $this->entityField->get() : $this->content;
    }

    /**
     * @param string|null $id
     */
    public function setFileId($id): void
    {
        $this->fileId = $id;
    }

    /**
     * Add a JS action to be returned to server on callback.
     */
    public function addJsAction(JsExpressionable $action): void
    {
        $this->jsActions[] = $action;
    }

    /**
     * Call when user is uploading a file.
     *
     * @param \Closure(PhpFileArray, PhpFileArray, PhpFileArray, PhpFileArray, PhpFileArray, PhpFileArray, PhpFileArray, PhpFileArray, PhpFileArray, PhpFileArray): JsExpressionable $fx
     */
    public function onUpload(\Closure $fx): void
    {
        $this->hasUploadCb = true;
        if ($this->getApp()->tryGetRequestPostParam('fUploadAction') === self::UPLOAD_ACTION) {
            $this->cb->set(function () use ($fx) {
                $postFiles = [];
                for ($i = 0;; ++$i) {
                    $k = 'file' . ($i > 0 ? '-' . $i : '');
                    $uploadFile = $this->getApp()->tryGetRequestUploadedFile($k);
                    if ($uploadFile === null) {
                        break;
                    }

                    $postFile = [
                        'name' => $uploadFile->getClientFilename(),
                        'error' => $uploadFile->getError(),
                    ];
                    if ($uploadFile->getError() === \UPLOAD_ERR_OK) {
                        $postFile = array_merge($postFile, [
                            'type' => $uploadFile->getClientMediaType(),
                            'tmp_name' => $uploadFile->getStream()->getMetadata('uri'),
                            'size' => $uploadFile->getSize(),
                        ]);
                    }
                    $postFiles[] = $postFile;
                }

                if (count($postFiles) > 0) {
                    $fileId = reset($postFiles)['name'];
                    $this->setFileId($fileId);
                    $this->setInput($fileId);
                }

                $jsRes = $fx(...$postFiles);
                if ($jsRes !== null) { // @phpstan-ignore-line https://github.com/phpstan/phpstan/issues/9388
                    $this->addJsAction($jsRes);
                }

                if (count($postFiles) > 0 && reset($postFiles)['error'] === 0) {
                    $this->addJsAction(
                        $this->js()->atkFileUpload('updateField', [$this->fileId, $this->getInputValue()])
                    );
                }

                return new JsBlock($this->jsActions);
            });
        }
    }

    /**
     * Call when user is removing an already upload file.
     *
     * @param \Closure(string): JsExpressionable $fx
     */
    public function onDelete(\Closure $fx): void
    {
        $this->hasDeleteCb = true;
        if ($this->getApp()->tryGetRequestPostParam('fUploadAction') === self::DELETE_ACTION) {
            $this->cb->set(function () use ($fx) {
                $fileId = $this->getApp()->getRequestPostParam('fUploadId');

                $jsRes = $fx($fileId);
                if ($jsRes !== null) { // @phpstan-ignore-line https://github.com/phpstan/phpstan/issues/9388
                    $this->addJsAction($jsRes);
                }

                return new JsBlock($this->jsActions);
            });
        }
    }

    protected function renderView(): void
    {
        parent::renderView();

        if ($this->cb->canTerminate()) {
            $uploadActionRaw = $this->getApp()->tryGetRequestPostParam('fUploadAction');
            if (!$this->hasUploadCb && ($uploadActionRaw === self::UPLOAD_ACTION)) {
                throw new Exception('Missing onUpload callback');
            } elseif (!$this->hasDeleteCb && ($uploadActionRaw === self::DELETE_ACTION)) {
                throw new Exception('Missing onDelete callback');
            }
        }

        if ($this->accept !== []) {
            $this->template->set('accept', implode(', ', $this->accept));
        }

        if ($this->disabled || $this->readOnly) {
            $this->template->dangerouslySetHtml('disabled', 'disabled="disabled"');
        }

        if ($this->multiple) {
            $this->template->dangerouslySetHtml('multiple', 'multiple="multiple"');
        }

        $this->template->set('placeholderReadonly', $this->disabled ? 'disabled="disabled"' : 'readonly="readonly"');

        if ($this->placeholder) {
            $this->template->set('Placeholder', $this->placeholder);
        }

        $this->js(true)->atkFileUpload([
            'url' => $this->cb->getJsUrl(),
            'action' => $this->action->name,
            'file' => ['id' => $this->fileId ?? $this->entityField->get(), 'name' => $this->getInputValue()],
            'submit' => ($this->form->buttonSave) ? $this->form->buttonSave->name : null,
        ]);
    }
}
