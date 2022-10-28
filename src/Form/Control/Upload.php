<?php

declare(strict_types=1);

namespace Atk4\Ui\Form\Control;

use Atk4\Ui\Button;
use Atk4\Ui\Exception;
use Atk4\Ui\JsCallback;
use Atk4\Ui\JsExpressionable;

class Upload extends Input
{
    public $defaultTemplate = 'form/control/upload.html';

    public string $inputType = 'hidden';

    /** @var Button|array|null The action button to open file browser dialog. */
    public $action;

    /**
     * The uploaded file id.
     * This id is return on form submit.
     * If not set, will default to file name.
     * file id is also sent with onDelete Callback.
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

    /** @var array<int, JsExpressionable> */
    public $jsActions = [];

    public const UPLOAD_ACTION = 'upload';
    public const DELETE_ACTION = 'delete';

    protected function init(): void
    {
        parent::init();

        $this->cb = JsCallback::addTo($this);

        if (!$this->action) {
            $this->action = new Button(['icon' => 'upload', 'class.disabled' => $this->disabled || $this->readOnly]);
        }
    }

    /**
     * Allow to set file id and file name
     *  - fileId will be the file id sent with onDelete callback.
     *  - fileName is the field value display to user.
     *
     * @param string      $fileId   Field id for onDelete Callback
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
        return parent::set($value, null);
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
     *
     * @param JsExpressionable $action
     */
    public function addJsAction($action): void
    {
        $this->jsActions[] = $action;
    }

    /**
     * Call when user is uploading a file.
     */
    public function onUpload(\Closure $fx): void
    {
        $this->hasUploadCb = true;
        if (($_POST['fUploadAction'] ?? null) === self::UPLOAD_ACTION) {
            $this->cb->set(function () use ($fx) {
                $postFiles = [];
                for ($i = 0;; ++$i) {
                    $k = 'file' . ($i > 0 ? '-' . $i : '');
                    if (!isset($_FILES[$k])) {
                        break;
                    }

                    $postFile = $_FILES[$k];
                    if ($postFile['error'] !== 0) {
                        // unset all details on upload error
                        $postFile = array_intersect_key($postFile, array_flip(['error', 'name']));
                    }
                    $postFiles[] = $postFile;
                }

                if (count($postFiles) > 0) {
                    $fileId = reset($postFiles)['name'];
                    $this->setFileId($fileId);
                    $this->setInput($fileId);
                }

                $this->addJsAction($fx(...$postFiles));

                if (count($postFiles) > 0 && reset($postFiles)['error'] === 0) {
                    $this->addJsAction(
                        $this->js()->atkFileUpload('updateField', [$this->fileId, $this->getInputValue()])
                    );
                }

                return $this->jsActions;
            });
        }
    }

    /**
     * Call when user is removing an already upload file.
     */
    public function onDelete(\Closure $fx): void
    {
        $this->hasDeleteCb = true;
        if (($_POST['fUploadAction'] ?? null) === self::DELETE_ACTION) {
            $this->cb->set(function () use ($fx) {
                $fileId = $_POST['fUploadId'] ?? null;
                $this->addJsAction($fx($fileId));

                return $this->jsActions;
            });
        }
    }

    protected function renderView(): void
    {
        // need before parent rendering.
        if ($this->disabled) {
            $this->addClass('disabled');
        }
        parent::renderView();

        if ($this->cb->canTerminate()) {
            $uploadActionRaw = $_POST['fUploadAction'] ?? null;
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

        if ($this->placeholder) {
            $this->template->trySet('PlaceHolder', $this->placeholder);
        }

        $this->js(true)->atkFileUpload([
            'url' => $this->cb->getJsUrl(),
            'action' => $this->action->name,
            'file' => ['id' => $this->fileId ?? $this->entityField->get(), 'name' => $this->getInputValue()],
            'submit' => ($this->form->buttonSave) ? $this->form->buttonSave->name : null,
        ]);
    }
}
