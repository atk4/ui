<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

// Re-usable component implementing counter

/** @var \Atk4\Ui\Columns $finderClass */
$finderClass = get_class(new class() extends \Atk4\Ui\Columns {
    public $route = [];

    public function setModel(\Atk4\Data\Model $model, $route = [])
    {
        parent::setModel($model);

        $this->addClass('internally celled');

        // lets add our first table here
        $table = \Atk4\Ui\Table::addTo($this->addColumn(), ['header' => false, 'very basic selectable'])->addStyle('cursor', 'pointer');
        $table->setModel($model, [$model->title_field]);

        $selections = explode(',', $_GET[$this->name] ?? '');

        if (!empty($selections[0])) {
            $table->js(true)->find('tr[data-id=' . $selections[0] . ']')->addClass('active');
        }

        $path = [];
        $jsReload = new \Atk4\Ui\JsReload($this, [$this->name => new \Atk4\Ui\JsExpression('[]+[]', [
            $path ? (implode(',', $path) . ',') : '',
            new \Atk4\Ui\JsExpression('$(this).data("id")'),
        ])]);
        $table->on('click', 'tr', $jsReload);

        while ($selections && $id = array_shift($selections)) {
            $path[] = $id;
            $pushModel = $model->newInstance();
            $pushModel->tryLoad($id);
            if (!$pushModel->loaded()) {
                break;
            }
            $ref = array_shift($route);
            if (!$route) {
                $route[] = $ref; // repeat last route
            }

            if (!$pushModel->hasRef($ref)) {
                break; // no such route
            }

            $pushModel = $pushModel->ref($ref);

            $table = \Atk4\Ui\Table::addTo($this->addColumn(), ['header' => false, 'very basic selectable'])->addStyle('cursor', 'pointer');
            $table->setModel($pushModel->setLimit(10), [$pushModel->title_field]);

            if ($selections) {
                $table->js(true)->find('tr[data-id=' . $selections[0] . ']')->addClass('active');
            }

            $jsReload = new \Atk4\Ui\JsReload($this, [$this->name => new \Atk4\Ui\JsExpression('[]+[]', [
                $path ? (implode(',', $path) . ',') : '',
                new \Atk4\Ui\JsExpression('$(this).data("id")'),
            ])]);
            $table->on('click', 'tr', $jsReload);
        }

        return $this->model;
    }
});

$model = new File($app->db);
$model->addCondition('parent_folder_id', null);
$model->setOrder(['is_folder' => 'desc', 'name']);

\Atk4\Ui\Header::addTo($app, ['MacOS File Finder', 'subHeader' => 'Component built around Table, Columns and JsReload']);

$vp = \Atk4\Ui\VirtualPage::addTo($app)->set(function ($vp) use ($model) {
    $model->action('delete')->execute();
    $model->importFromFilesystem('.');
    \Atk4\Ui\Button::addTo($vp, ['Import Complete', 'big green fluid'])->link('multitable.php');
    $vp->js(true)->closest('.modal')->find('.header')->remove();
});

\Atk4\Ui\Button::addTo($app, ['Re-Import From Filesystem', 'top attached'])->on('click', new \Atk4\Ui\JsModal('Now importing ... ', $vp));

$finderClass::addTo($app, ['bottom attached'])
    ->addClass('top attached segment')
    ->setModel($model->setLimit(5), ['SubFolder']);
