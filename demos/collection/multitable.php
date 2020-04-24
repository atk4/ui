<?php

chdir('..');
require_once dirname(__DIR__ ) . '/atk-init.php';

// Re-usable component implementing counter
class Finder extends \atk4\ui\Columns
{
    public $route = [];

    public function setModel(\atk4\data\Model $model, $route = [])
    {
        parent::setModel($model);

        $this->addClass('internally celled');

        // lets add our first table here
        $table = \atk4\ui\Table::addTo($this->addColumn(), ['header' => false, 'very basic selectable'])->addStyle('cursor', 'pointer');
        $table->setModel($model, [$model->title_field]);

        $selections = explode(',', $_GET[$this->name] ?? '');

        if (!empty($selections[0])) {
            $table->js(true)->find('tr[data-id=' . $selections[0] . ']')->addClass('active');
        }

        $path = [];
        $js_reload = new \atk4\ui\jsReload($this, [$this->name => new \atk4\ui\jsExpression('[]+[]', [
            $path ? (implode(',', $path) . ',') : '',
            new \atk4\ui\jsExpression('$(this).data("id")'),
        ])]);
        $table->on('click', 'tr', $js_reload);

        while ($selections && $id = array_shift($selections)) {
            $path[] = $id;
            $model->tryLoad($id);
            if (!$model->loaded()) {
                break;
            }
            $ref = array_shift($route);
            if (!$route) {
                $route[] = $ref; // repeat last route
            }

            if (!$model->hasRef($ref)) {
                break; // no such route
            }

            $model = $model->ref($ref);

            $table = \atk4\ui\Table::addTo($this->addColumn(), ['header' => false, 'very basic selectable'])->addStyle('cursor', 'pointer');
            $table->setModel($model->setLimit(25), [$model->title_field]);

            if ($selections) {
                $table->js(true)->find('tr[data-id=' . $selections[0] . ']')->addClass('active');
            }

            $js_reload = new \atk4\ui\jsReload($this, [$this->name => new \atk4\ui\jsExpression('[]+[]', [
                $path ? (implode(',', $path) . ',') : '',
                new \atk4\ui\jsExpression('$(this).data("id")'),
            ])]);
            $table->on('click', 'tr', $js_reload);
        }

        return $this->model;
    }
}

$m = new File($db);
$m->addCondition('parent_folder_id', null);
$m->setOrder('is_folder desc, name');

\atk4\ui\Header::addTo($app, ['MacOS File Finder', 'subHeader' => 'Component built around Table, Columns and jsReload']);

$vp = \atk4\ui\VirtualPage::addTo($app)->set(function ($vp) use ($m) {
    $m->action('delete')->execute();
    $m->importFromFilesystem(dirname(dirname(__FILE__)));
    \atk4\ui\Button::addTo($vp, ['Import Complete', 'big green fluid'])->link('multitable.php');
    $vp->js(true)->closest('.modal')->find('.header')->remove();
});

\atk4\ui\Button::addTo($app, ['Re-Import From Filesystem', 'top attached'])->on('click', new \atk4\ui\jsModal('Now importing ... ', $vp));

Finder::addTo($app, 'bottom attached')
    ->addClass('top attached segment')
    ->setModel($m->setLimit(5), ['SubFolder']);
