<?php

namespace atk4\ui\FormField;

use atk4\ui\Exception;
use atk4\ui\Template;
use atk4\ui\View;

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
     * ex: ['.jpg', '.jpeg', '.png'] or ['images/*']
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
            $this->action = new \atk4\ui\Button(['icon' => 'upload']);
        }
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
                    $this->fileId = $files['file']['name'];
                }
                if ($action === 'upload' && !$files['file']['error']) {
                    $this->cb->set(function () use ($fx, $files) {
                        $this->addJsAction(call_user_func_array($fx, $files));
                        $this->addJsAction([
                            $this->js()->data('fileId', $this->fileId),
                            $this->jsInput()->val($this->fileId),
                        ]);

                        return $this->jsActions;
                    });
                } elseif ($action === null || $files['file']['error']) {
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

        $this->js(true)->atkFileUpload([
            'uri'      => $this->cb->getURL(),
            'action'   => $this->action->name,
            'hasFocus' => $this->hasFocusEnable,
            'submit'   => ($this->form->buttonSave) ? $this->form->buttonSave->name : null,
        ]);
    }
}
