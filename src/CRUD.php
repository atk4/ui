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

    public $operations = 'CRUD';
    protected $_can = [];

    public function init()
    {
        parent::init();

        $this->addClass('atk-reloadable-crud')->on('reload', new jsReload($this));

        foreach (str_split($this->operations) as $op) {
            $this->_can[$op] = true;
        }

        if (!isset($this->_can['R'])) {
            throw new Exception(['You cannot disable "R" operation']);
        }

        if (isset($this->_can['U'])) {
            $this->pageEdit = $this->add($this->pageEdit ?: 'VirtualPage');
            $this->formEdit = $this->pageEdit->add($this->formEdit ?: 'Form');
        }

        if (isset($this->_can['C'])) {
            $this->pageCreate = $this->add($this->pageCreate ?: 'VirtualPage');

            $this->itemCreate = $this->menu->addItem(
                $this->itemCreate ?: ['Add new', 'icon'=>'plus'],
                new jsModal('Add new', $this->pageCreate)
            );
        }
    }

    public function setModel(\atk4\data\Model $m, $defaultFields = null)
    {
        if ($defaultFields !== null) {
            $this->fieldsDefault = $defaultFields;
        }

        $this->itemCreate->set('Add New '.(isset($m->title) ? $m->title : get_class($m)));

        $this->pageCreate->set(function ($page) use ($m) {
            $form = $page->add($this->formCreate ?: 'Form');
            $form->setModel($m, $this->fieldsCreate ?: $this->fieldsDefault);
            $form->onSubmit(function ($form) {
                $form->model->save();

                return [
                    new jsExpression('$($(".atk-dialog-content").data("opener")).closest(".atk-reloadable-crud").trigger("reload")'),
                    new jsExpression('$(".atk-dialog-content").trigger("close")'),
                ];
            });
        });

        return parent::setModel($m, $this->fieldsGrid ?: $this->fieldsDefault);
    }
}
