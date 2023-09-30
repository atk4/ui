<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Data\Model;
use Atk4\Ui\Button;
use Atk4\Ui\Columns;
use Atk4\Ui\Header;
use Atk4\Ui\Js\JsExpression;
use Atk4\Ui\Js\JsModal;
use Atk4\Ui\Js\JsReload;
use Atk4\Ui\Table;
use Atk4\Ui\VirtualPage;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

// re-usable component implementing counter

$finderClass = AnonymousClassNameCache::get_class(fn () => new class() extends Columns {
    public array $route = [];

    public function setModel(Model $model, array $route = []): void
    {
        parent::setModel($model);

        $this->addClass('internally celled');

        // lets add our first table here
        $table = Table::addTo($this->addColumn(), ['header' => false, 'class.very basic selectable' => true])->setStyle('cursor', 'pointer');
        $table->setModel($model, [$model->titleField]);

        $selections = explode(',', $this->getApp()->tryGetRequestQueryParam($this->name) ?? '');

        if ($selections[0]) {
            $table->js(true)->find('tr[data-id=' . $selections[0] . ']')->addClass('active');
        }

        $makeJsReloadFx = function (array $path): JsReload {
            return new JsReload($this, [$this->name => new JsExpression('[] + []', [
                count($path) > 0 ? implode(',', $path) . ',' : '',
                new JsExpression('$(this).data(\'id\')'),
            ])]);
        };

        $path = [];
        $jsReload = $makeJsReloadFx($path);
        $table->on('click', 'tr', $jsReload);

        while ($id = array_shift($selections)) {
            $path[] = $id;
            $pushModel = new $model($model->getPersistence());
            $pushModel = $pushModel->tryLoad($id);
            if ($pushModel === null) {
                break;
            }
            $ref = array_shift($route);
            if (!$route) {
                $route[] = $ref; // repeat last route
            }

            if (!$pushModel->hasReference($ref)) {
                break; // no such route
            }

            $pushModel = $pushModel->ref($ref);
            $pushModel->setOrder([File::hinting()->fieldName()->is_folder => 'desc', File::hinting()->fieldName()->name]);

            $table = Table::addTo($this->addColumn(), ['header' => false, 'class.very basic selectable' => true])->setStyle('cursor', 'pointer');
            $table->setModel($pushModel->setLimit(10), [$pushModel->titleField]);

            if ($selections) {
                $table->js(true)->find('tr[data-id=' . $selections[0] . ']')->addClass('active');
            }

            $jsReload = $makeJsReloadFx($path);
            $table->on('click', 'tr', $jsReload);
        }
    }
});

$model = new File($app->db);
$model->addCondition($model->fieldName()->parent_folder_id, null);
$model->setOrder([$model->fieldName()->is_folder => 'desc', $model->fieldName()->name]);

Header::addTo($app, ['File Finder', 'subHeader' => 'Component built around Table, Columns and JsReload']);

$vp = VirtualPage::addTo($app)->set(static function (VirtualPage $vp) use ($model) {
    $model->importFromFilesystem('.');
    Button::addTo($vp, ['Import Complete', 'class.big green fluid' => true])->link('multitable.php');
    $vp->js(true)->closest('.modal')->find('.header')->remove();
});

Button::addTo($app, ['Re-Import From Filesystem', 'class.top attached' => true])
    ->on('click', new JsModal('Now importing ... ', $vp));

$finderClass::addTo($app, ['bottom attached segment'])
    ->setModel($model->setLimit(10), [$model->fieldName()->SubFolder]);
