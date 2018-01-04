<?php

namespace atk4\ui\FormField;

use atk4\ui\Exception;
use atk4\ui\jsChain;
use atk4\ui\jsExpression;
use atk4\ui\Template;
use atk4\ui\View;

class Upload extends Input
{
    /**
     * The action button to open file browser dialog.
     *
     * @var null
     */
    public $action = null;

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
     * CURRENTLY NOT SUPPORTED
     *
     * @var bool
     */
    public $multiple = false;

    /**
     * An array of string value for accept file type.
     *
     * @var array
     */
    public $accept = [];

    /**
     * The
     * @var null
     */
    public $fieldIdName = null;

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

        $this->cb = $this->add('jsCallback');

        if (!$this->action) {
            $this->action = new \atk4\ui\Button(['icon' => 'upload']);
        }

        if (!$this->fieldIdName) {
            $this->fieldIdName = $this->field->short_name.'_id';
        }

        if ($this->form) {
            $this->form->addField( $this->fieldIdName, ['Hidden']);
        }
    }

    /**
     * Set an id to the uploaded file.
     * When id is added during the upload callback,
     * the same id will be returned on delete callback
     * instead of the file name.
     *
     * @param $id
     */
    public function setFileId($id)
    {
        $this->addJsAction($this->js()->data('fileId', $id));
        $this->addJsAction(new jsExpression("$('input[name=[field_id]]').val([field_value])", ['field_id' => $this->fieldIdName, 'field_value' => $id]));
        $this->addJsAction(new jsExpression("$('this').parents('form').form('set value', [field_id], [field_value])", ['field_id' => $this->fieldIdName, 'field_value' => $id]));
    }

    /**
     * Add a js action to be return to server on callback.
     *
     * @param $action
     */
    public function addJsAction($action)
    {
        $this->jsActions[] = $action;
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
                $this->cb->set(function() use ($fx, $fileName) {
                    $actions[] = call_user_func_array($fx, [$fileName]);
                    if (!empty($this->jsActions)) {
                        $actions = array_merge($actions, $this->jsActions);
                    }
                    return $actions;
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
                $files = @$_FILES;
                if ($action === 'upload' && !$files['file']['error']) {
                    $this->cb->set(function() use ($fx, $files) {
                        $actions[] = call_user_func_array($fx, $files);
                        if (!empty($this->jsActions)) {
                            $actions = array_merge($actions, $this->jsActions);
                        }
                        return $actions;
                    });
                } elseif ($action === null || $files['file']['error']) {
                    $this->cb->set(function() use ($fx, $files) {
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
            $this->template->trySet('multiple', 'multiple');
        }

        $this->js(true)->atkFileUpload([
            'uri'      => $this->cb->getURL(),
            'action'   => $this->action->name,
            'hasFocus' => $this->hasFocusEnable,
            'submit'   => ($this->form->buttonSave) ? $this->form->buttonSave->name : null,
        ]);
    }
}
