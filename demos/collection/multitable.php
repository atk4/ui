<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Data\Model;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

// Re-usable component implementing counter

$finderClass = AnonymousClassNameCache::get_class(fn () => new class() extends \Atk4\Ui\Columns {
    public $route = [];

    public function setModel(Model $model, $route = []): void
    {
        parent::setModel($model);

        $this->addClass('internally celled');

        // lets add our first table here
        $table = \Atk4\Ui\Table::addTo($this->addColumn(), ['header' => false, 'class.very basic selectable' => true])->addStyle('cursor', 'pointer');
        $table->setModel($model, [$model->title_field]);

        $selections = explode(',', $_GET[$this->name] ?? '');

        if (!empty($selections[0])) {
            $table->js(true)->find('tr[data-id=' . $selections[0] . ']')->addClass('active');
        }

        $path = [];
        $jsReload = new \Atk4\Ui\JsReload($this, [$this->name => new \Atk4\Ui\JsExpression('[] + []', [
            $path ? (implode(',', $path) . ',') : '',
            new \Atk4\Ui\JsExpression('$(this).data("id")'),
        ])]);
        $table->on('click', 'tr', $jsReload);

        while ($selections && $id = array_shift($selections)) {
            $path[] = $id;
            $pushModel = new $model($model->persistence);
            $pushModel = $pushModel->tryLoad($id);
            if (!$pushModel->isLoaded()) {
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

            $table = \Atk4\Ui\Table::addTo($this->addColumn(), ['header' => false, 'class.very basic selectable' => true])->addStyle('cursor', 'pointer');
            $table->setModel($pushModel->setLimit(10), [$pushModel->title_field]);

            if ($selections) {
                $table->js(true)->find('tr[data-id=' . $selections[0] . ']')->addClass('active');
            }

            $jsReload = new \Atk4\Ui\JsReload($this, [$this->name => new \Atk4\Ui\JsExpression('[] + []', [
                $path ? (implode(',', $path) . ',') : '',
                new \Atk4\Ui\JsExpression('$(this).data("id")'),
            ])]);
            $table->on('click', 'tr', $jsReload);
        }
    }
});

$model = new File($app->db);
$model->addCondition($model->fieldName()->parent_folder_id, null);
$model->setOrder([$model->fieldName()->is_folder => 'desc', $model->fieldName()->name]);

\Atk4\Ui\Header::addTo($app, ['File Finder', 'subHeader' => 'Component built around Table, Columns and JsReload']);

$vp = \Atk4\Ui\VirtualPage::addTo($app)->set(function (\Atk4\Ui\VirtualPage $vp) use ($model) {
    $model->importFromFilesystem('.');
    \Atk4\Ui\Button::addTo($vp, ['Import Complete', 'class.big green fluid' => true])->link('multitable.php');
    $vp->js(true)->closest('.modal')->find('.header')->remove();
});

\Atk4\Ui\Button::addTo($app, ['Re-Import From Filesystem', 'class.top attached' => true])->on('click', new \Atk4\Ui\JsModal('Now importing ... ', $vp));

$finderClass::addTo($app, ['bottom attached'])
    ->addClass('top attached segment')
    ->setModel($model->setLimit(5), [$model->fieldName()->SubFolder]);
