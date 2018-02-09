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

    public $fieldsRead = null;

    public $columnUpdate = null;
    public $fieldsUpdate = null;
    public $formUpdate = null;
    public $pageUpdate = null;

    public $itemCreate = null;
    public $fieldsCreate = null;
    public $formCreate = null;
    public $pageCreate = null;
    public $notify = null;

    /**
     * Permitted operatios. You can add more of your own and you don't need to keep
     * them 1-character long. Use full words such as 'archive' if you run out of
     * letters.
     */
    public $canCreate = true;
    public $canUpdate = true;
    public $canDelete = true;

    public function init()
    {
        parent::init();

        $this->on('reload', new jsReload($this));

        if ($this->canUpdate) {
            $this->pageUpdate = $this->add([$this->pageUpdate ?: 'VirtualPage', 'short_name'=>'edit']);
            $this->formUpdate = $this->pageUpdate->add($this->formUpdate ?: ['Form', 'layout' => 'FormLayout/Columns']);
        }

        if ($this->canCreate) {
            $this->pageCreate = $this->add([$this->pageCreate ?: 'VirtualPage', 'short_name'=>'add']);

            $this->itemCreate = $this->menu->addItem(
                $this->itemCreate ?: ['Add new', 'icon' => 'plus'],
                new jsModal('Add new', $this->pageCreate)
            );
        }

        if (!$this->notify) {
            $this->notify = new jsNotify([
                'content' => 'Data is saved!',
                'color'   => 'green',
            ]);
        }
    }

    public function can($operation)
    {
        throw new Exception('Please simply check $crud->canCreate or similar property directly');
    }

    public function setModel(\atk4\data\Model $m, $defaultFields = null)
    {
        if ($defaultFields !== null) {
            $this->fieldsDefault = $defaultFields;
        }

        $m = parent::setModel($m, $this->fieldsRead ?: $this->fieldsDefault);

        if ($this->canCreate) {
            $this->itemCreate->set('Add New '.(isset($m->title) ? $m->title : preg_replace('|.*\\\|', '', get_class($m))));

            $this->pageCreate->set(function ($page) use ($m) {
                $form = $page->add($this->formCreate ?: ['Form', 'layout' => 'FormLayout/Columns']);
                $form->setModel($m, $this->fieldsCreate ?: $this->fieldsDefault);
                $form->onSubmit(function ($form) {
                    $form->model->save();

                    return [
                        (new jQuery($this))->trigger('reload'),
                        new jsExpression('$(".atk-dialog-content").trigger("close")'),
                        $this->notify,
                    ];
                });
            });
        }

        if ($this->canUpdate) {
            $this->addAction(['icon' => 'edit'], new jsModal('Edit', $this->pageUpdate, [$this->name => $this->jsRow()->data('id')]));

            $this->pageUpdate->set(function () {
                $this->model->load($this->app->stickyGet($this->name));
                $this->formUpdate->setModel($this->model);
                $this->formUpdate->onSubmit(function ($form) {
                    $form->model->save();

                    return [
                        (new jQuery($this))->trigger('reload'),
                        new jsExpression('$(".atk-dialog-content").trigger("close")'),
                        $this->notify,
                    ];
                });
            });
        }

        if ($this->canDelete) {
            $this->addAction(['icon' => 'red trash'], function ($j, $id) {
                $this->model->load($id)->delete();

                return $j->closest('tr')->transition('fade left');
            }, 'Are you sure?');
        }

        return $m;
    }
}
