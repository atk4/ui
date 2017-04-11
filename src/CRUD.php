<?php

// vim:ts=4:sw=4:et:fdm=marker:fdl=0

namespace atk4\ui;

/**
 * Implements a more sophisticated and interractive Data-Table component.
 */
class CRUD extends Grid
{
    public $formDefault = null;
    public $fieldsDefault = null;

    public $fieldsGrid = null;

    public $columnEdit = null;
    public $fieldsEdit = null;
    public $formEdit = null;
    public $pageEdit = null;

    public $itemCreate = null;
    public $fieldsCreate = null;
    public $formCreate = null;
    public $pageCreate = null;

    /**
     * Permitted operatios. You can add more of your own and you don't need to keep
     * them 1-character long. Use full words such as 'archive' if you run out of
     * letters.
     */
    public $ops = ['c'=>true, 'r'=>true, 'u'=>true, 'd'=>true];

    public function init()
    {
        parent::init();

        if (!$this->can('r')) {
            throw new Exception(['You cannot disable "r" operation']);
        }

        if ($this->can('u')) {
            $this->pageEdit = $this->add($this->pageEdit ?: 'VirtualPage');
            $this->formEdit = $this->pageEdit->add($this->formEdit ?: ['Form', 'layout'=>'FormLayout/Columns']);
        }

        if ($this->can('c')) {
            $this->pageCreate = $this->add($this->pageCreate ?: 'VirtualPage');

            $this->itemCreate = $this->menu->addItem(
                $this->itemCreate ?: ['Add new', 'icon'=>'plus'],
                new jsModal('Add new', $this->pageCreate)
            );
        }
    }

    public function can($operation)
    {
        return isset($this->ops[$operation]) && $this->ops[$operation];
    }

    public function setModel(\atk4\data\Model $m, $defaultFields = null)
    {
        if ($defaultFields !== null) {
            $this->fieldsDefault = $defaultFields;
        }

        if ($this->can('c')) {
            $this->itemCreate->set('Add New '.(isset($m->title) ? $m->title : get_class($m)));

            $this->pageCreate->set(function ($page) use ($m) {
                $form = $page->add($this->formCreate ?:  ['Form', 'layout'=>'FormLayout/Columns']);
                $form->setModel($m, $this->fieldsCreate ?: $this->fieldsDefault);
                $form->onSubmit(function ($form) {
                    $form->model->save();

                    return [
                        new jsExpression('$($(".atk-dialog-content").data("opener")).closest(".atk-reloadable-crud").trigger("reload")'),
                        new jsExpression('$(".atk-dialog-content").trigger("close")'),
                    ];
                });
            });
        }

        $m = parent::setModel($m, $this->fieldsGrid ?: $this->fieldsDefault);

        if ($this->can('u')) {
            $this->addAction(new Icon('pencil'), new jsModal('Edit', $this->pageEdit, [$this->name=>$this->table->jsRow()->data('id')]));

            $this->pageEdit->set(function() {
                $this->model->load($_POST[$this->name]);
                $this->formEdit->setModel($this->model);
                $this->formEdit->onSubmit(function($form) {
                    $form->save();
                    return [
                        new jsExpression('$($(".atk-dialog-content").data("opener")).closest(".atk-reloadable-crud").trigger("reload")'),
                        new jsExpression('$(".atk-dialog-content").trigger("close")'),
                    ];
                });
            });

            /*
            $this->pageEdit = $this->add($this->pageEdit ?: 'VirtualPage');
            $this->formEdit = $this->pageEdit->add($this->formEdit ?: 'Form');
             */
        }

        if ($this->can('d')) {
            $this->addColumn(new TableColumn\Delete());
        }

        return $m;
    }
}
