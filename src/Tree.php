<?php

namespace atk4\ui;

class Tree extends View
{
    public $item_template = null;
    public $children_ref = 'Children';
    public $is_folder = null;
    public $template_file = 'tree.html';

    public function init()
    {
        parent::init();

        // dissect the template and pull out various tempaltes
        if (!$this->item_tempalte) {
            $this->item_template = $this->template->cloneRegion('Item');
        }
        $this->template->tryDel('Item');
    }

    public function renderSubTree(\atk4\data\Model $model)
    {
        $output = '';
        $item = clone $this->item_template;
        foreach ($this->model as $row) {
            $item->set($row);

            // true, false or null if unsure
            $folder = $this->is_folder === false ? false :
                ($this->is_folder ? $this->model[$this->is_folder] : null);

            $sub_output = null;

            if ($folder === true || $folder === null) {
                $sub_output = $this->renderSubTree($this->model->ref($this->children_ref));

                // Not sure if this was a sub-folder, so would have to check
                if ($folder === null) {
                    $folder = (bool) $sub_output;
                }
            }

            $item['icon'] = $folder ? 'file' : 'folder';
            $item['Content'] = $sub_output;

            $output .= $item->render();
        }

        return $output;
    }

    public function render()
    {
        $this->output = $this->renderSubTree($this->model);
    }
}
