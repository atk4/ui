<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Data\Model;
use Atk4\Ui\App;
use Atk4\Ui\Button;
use Atk4\Ui\Columns;
use Atk4\Ui\Header;
use Atk4\Ui\Js\JsExpression;
use Atk4\Ui\Js\JsModal;
use Atk4\Ui\Js\JsReload;
use Atk4\Ui\Table;
use Atk4\Ui\VirtualPage;

/** @var App $app */
require_once __DIR__ . '/../init-app.php';

// re-usable component implementing counter

$finderClass = AnonymousClassNameCache::get_class(fn () => new class extends Columns {
    public array $route = [];

    /**
     * @return list<mixed>
     */
    private function explodeSelectionValue(string $value): array
    {
        $res = [];
        foreach ($value === '' ? [] : explode(',', $value) as $v) {
            $res[] = $this->getApp()->uiPersistence->typecastAttributeLoadField($this->model->getIdField(), $v);
        }

        return $res;
    }

    #[\Override]
    public function setModel(Model $model, array $route = []): void
    {
        parent::setModel($model);

        $this->addClass('internally celled');

        // lets add our first table here
        $table = Table::addTo($this->addColumn(), ['header' => false, 'class.very basic selectable' => true])->setStyle('cursor', 'pointer');
        $table->setModel($model, [$model->titleField]);

        $selectionIds = $this->explodeSelectionValue($this->getApp()->tryGetRequestQueryParam($this->name) ?? '');

        $makeJsReloadFx = function (array $path): JsReload {
            return new JsReload($this, [$this->name => new JsExpression('[] + []', [
                count($path) > 0 ? implode(',', $path) . ',' : '',
                new JsExpression('$(this).data(\'id\')'),
            ])]);
        };

        $path = [];
        $jsReload = $makeJsReloadFx($path);
        $table->on('click', 'tr', $jsReload);

        foreach ($selectionIds as $id) {
            $table->js(true)->find('tr[data-id=' . $this->getApp()->uiPersistence->typecastAttributeSaveField($this->model->getIdField(), $id) . ']')->addClass('active');

            $path[] = $this->getApp()->uiPersistence->typecastAttributeSaveField($this->model->getIdField(), $id);
            $pushModel = new $model($model->getPersistence());
            $pushModel = $pushModel->load($id);

            $ref = array_shift($route);
            if ($route === []) {
                $route[] = $ref; // repeat last route
            }

            $pushModel = $pushModel->ref($ref);
            $pushModel->setOrder([File::hinting()->fieldName()->is_folder => 'desc', File::hinting()->fieldName()->name]);

            $table = Table::addTo($this->addColumn(), ['header' => false, 'class.very basic selectable' => true])->setStyle('cursor', 'pointer');
            $table->setModel($pushModel->setLimit(10), [$pushModel->titleField]);

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
    ->setModel($model->setLimit(10), [$model->fieldName()->subFolder]);
