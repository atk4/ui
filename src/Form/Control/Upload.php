<?php

declare(strict_types=1);

namespace Atk4\Ui\Form\Control;

use Atk4\Ui\Exception;
use Atk4\Ui\View;

/**
 * Class Upload.
 */
class Upload extends Input
{
    public $inputType = 'hidden';
    /**
     * The action button to open file browser dialog.
     *
     * @var View
     */
    public $action;

    /**
     * The uploaded file id.
     * This id is return on form submit.
     * If not set, will default to file name.
     * file id is also sent with onDelete Callback.
     *
     * @var string
     */
    public $fileId;

    /**
     * Whether you need to open file browser dialog using input focus or not.
     * default to true.
     *
     * @var bool
     * @obsolete
     * hasFocusEnable has been disable in js plugin and this property will be removed.
     * Upload field is only using click handler now.
     */
    public $hasFocusEnable = false;

    /**
     * The input default template.
     *
     * @var string
     */
    public $defaultTemplate = 'form/control/upload.html';

    /**
     * Callback is use for onUpload or onDelete.
     *
     * @var \Atk4\Ui\JsCallback
     */
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

    /**
     * Whether cb has been define or not.
     *
     * @var bool
     */
    public $hasUploadCb = false;
    public $hasDeleteCb = false;

    public $jsActions = [];

    public const UPLOAD_ACTION = 'upload';
    public const DELETE_ACTION = 'delete';

    /** @var bool check if callback is trigger by one of the action. */
    private $_isCbRunning = false;

    protected function init(): void
    {
        parent::init();

        //$this->inputType = 'hidden';

        $this->cb = \Atk4\Ui\JsCallback::addTo($this);

        if (!$this->action) {
            $this->action = new \Atk4\Ui\Button(['icon' => 'upload', 'disabled' => ($this->disabled || $this->readonly)]);
        }
    }

    /**
     * Allow to set file id and file name
     *  - fileId will be the file id sent with onDelete callback.
     *  - fileName is the field value display to user.
     *
     * @param string      $fileId   // Field id for onDelete Callback
     * @param string|null $fileName // Field name display to user
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
     * @param mixed $value the field input value
     *
     * @return $this
     */
    public function setInput($value, $junk = null)
    {
        return parent::set($value, $junk);
    }

    /**
     * Get input field value.
     *
     * @return array|false|mixed|string|null
     */
    public function getInputValue()
    {
        return $this->field ? $this->field->get() : $this->content;
    }

    public function setFileId($id)
    {
        $this->fileId = $id;
    }

    /**
     * Add a js action to be return to server on callback.
     */
    public function addJsAction($action)
    {
        if (is_array($action)) {
            $this->jsActions = array_merge($action, $this->jsActions);
        } else {
            $this->jsActions[] = $action;
        }
    }

    /**
     * Call when user is uploading a file.
     */
    public function onUpload(\Closure $fx)
    {
        $this->hasUploadCb = true;
        if (($_POST['f_upload_action'] ?? null) === self::UPLOAD_ACTION) {
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
                    $this->addJsAction([
                        $this->js()->atkFileUpload('updateField', [$this->fileId, $this->getInputValue()]),
                    ]);
                }

                return $this->jsActions;
            });
        }
    }

    /**
     * Call when user is removing an already upload file.
     */
    public function onDelete(\Closure $fx)
    {
        $this->hasDeleteCb = true;
        if (($_POST['f_upload_action'] ?? null) === self::DELETE_ACTION) {
            $this->cb->set(function () use ($fx) {
                $fileId = $_POST['f_upload_id'] ?? null;
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
            $uploadAction = $_POST['f_upload_action'] ?? null;
            if (!$this->hasUploadCb && ($uploadAction === self::UPLOAD_ACTION)) {
                throw new Exception('Missing onUpload callback.');
            } elseif (!$this->hasDeleteCb && ($uploadAction === self::DELETE_ACTION)) {
                throw new Exception('Missing onDelete callback.');
            }
        }

        if (!empty($this->accept)) {
            $this->template->trySet('accept', implode(',', $this->accept));
        }
        if ($this->multiple) {
            $this->template->trySet('multiple', 'multiple');
        }

        if ($this->placeholder) {
            $this->template->trySet('PlaceHolder', $this->placeholder);
        }

        $this->js(true)->atkFileUpload([
            'uri' => $this->cb->getJsUrl(),
            'action' => $this->action->name,
            'file' => ['id' => $this->fileId ?: $this->field->get(), 'name' => $this->getInputValue()],
            'hasFocus' => $this->hasFocusEnable,
            'submit' => ($this->form->buttonSave) ? $this->form->buttonSave->name : null,
        ]);
    }
}
