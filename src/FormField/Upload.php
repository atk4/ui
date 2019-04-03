<?php

namespace atk4\ui\FormField;

use atk4\ui\Exception;
use atk4\ui\Template;
use atk4\ui\View;

/**
 * Class Upload.
 */
class Upload extends Input
{
    public $inputType = 'hidden';
    /**
     * The action button to open file browser dialog.
     *
     * @var null
     */
    public $action = null;

    /**
     * The uploaded file id.
     * This id is return on form submit.
     * If not set, will default to file name.
     * file id is also sent with onDelete Callback.
     *
     * @var null
     */
    public $fileId = null;

    /**
     * Whether you need to open file browser dialog using input focus or not.
     * default to true.
     *
     * @var bool
     */
    public $hasFocusEnable = true;

    /**
     * The input default template.
     *
     * @var string
     */
    public $defaultTemplate = 'formfield/upload.html';

    /**
     * The jsCallback.
     * Same callback is use for onUpload or onDelete.
     *
     * @var null
     */
    public $cb = null;

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

    public function init()
    {
        parent::init();

        //$this->inputType = 'hidden';

        $this->cb = $this->add('jsCallback');

        if (!$this->action) {
            $this->action = new \atk4\ui\Button(['icon' => 'upload', 'disabled'=> ($this->disabled || $this->readonly)]);
        }
    }

    /**
     * Allow to set file id and file name
     *  - fileId will be the file id sent with onDelete callback.
     *  - fileName is the field value display to user.
     *
     * @param string      $fileId   // Field id for onDelete Callback.
     * @param string|null $fileName // Field name display to user.
     * @param mixed       $junk
     *
     * @return $this|void
     */
    public function set($fileId = null, $fileName = null, $junk = null)
    {
        $this->setFileId($fileId);

        if (!$fileName) {
            $fileName = $fileId;
        }

        return $this->setInput($fileName, $junk);
    }

    /**
     * Set input field value.
     *
     * @param $value // The field input value.
     * @param $junk
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
     * @return array|false|mixed|null|string
     */
    public function getInputValue()
    {
        return $this->field ? $this->field->get() : $this->content;
    }

    /**
     * Set file id.
     *
     * @param $id
     */
    public function setFileId($id)
    {
        $this->fileId = $id;
    }

    /**
     * Add a js action to be return to server on callback.
     *
     * @param $action
     */
    public function addJSAction($action)
    {
        if (is_array($action)) {
            $this->jsActions = array_merge($action, $this->jsActions);
        } else {
            $this->jsActions[] = $action;
        }
    }

    /**
     * onDelete callback.
     * Call when user is removing an already upload file.
     *
     * @param callable $fx
     */
    public function onDelete($fx = null)
    {
        if (is_callable($fx)) {
            $this->hasDeleteCb = true;
            if ($this->cb->triggered() && @$_POST['action'] === 'delete') {
                $fileName = @$_POST['f_name'];
                $this->cb->set(function () use ($fx, $fileName) {
                    $this->addJsAction(call_user_func_array($fx, [$fileName]));

                    return $this->jsActions;
                });
            }
        }
    }

    /**
     * onUpload callback.
     * Call when user is uploading a file.
     *
     * @param callable $fx
     */
    public function onUpload($fx = null)
    {
        if (is_callable($fx)) {
            $this->hasUploadCb = true;
            if ($this->cb->triggered()) {
                $action = @$_POST['action'];
                if ($files = @$_FILES) {
                    //set fileId to file name as default.
                    $this->fileId = $files['file']['name'];
                    // display file name to user as default.
                    $this->setInput($this->fileId);
                }
                if ($action === 'upload' && !$files['file']['error']) {
                    $this->cb->set(function () use ($fx, $files) {
                        $this->addJsAction(call_user_func_array($fx, $files));
                        //$value = $this->field ? $this->field->get() : $this->content;
                        $this->addJsAction([
                            $this->js()->atkFileUpload('updateField', [$this->fileId, $this->getInputValue()]),
                        ]);

                        return $this->jsActions;
                    });
                } elseif ($action === null || isset($files['file']['error'])) {
                    $this->cb->set(function () use ($fx, $files) {
                        return call_user_func($fx, 'error');
                    });
                }
            }
        }
    }

    /**
     * Rendering view.
     */
    public function renderView()
    {
        //need before parent rendering.
        if ($this->disabled) {
            $this->addClass('disabled');
        }
        parent::renderView();

        if (!$this->hasUploadCb || !$this->hasDeleteCb) {
            throw new Exception('onUpload and onDelete callback must be called to use file upload');
        }
        if (!empty($this->accept)) {
            $this->template->trySet('accept', implode(',', $this->accept));
        }
        if ($this->multiple) {
            //$this->template->trySet('multiple', 'multiple');
        }

        if ($this->placeholder) {
            $this->template->trySet('PlaceHolder', $this->placeholder);
        }

        //$value = $this->field ? $this->field->get() : $this->content;
        $this->js(true)->atkFileUpload([
            'uri'      => $this->cb->getJSURL(),
            'action'   => $this->action->name,
            'file'     => ['id' => $this->fileId ?: $this->field->get(), 'name' => $this->getInputValue()],
            'hasFocus' => $this->hasFocusEnable,
            'submit'   => ($this->form->buttonSave) ? $this->form->buttonSave->name : null,
        ]);

    }
}
