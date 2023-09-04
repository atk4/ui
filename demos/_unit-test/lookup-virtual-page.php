<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\Form;
use Atk4\Ui\Grid;
use Atk4\Ui\Js\JsModal;
use Atk4\Ui\Js\JsToast;
use Atk4\Ui\VirtualPage;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

$product = new Product($app->db);

$vp = VirtualPage::addTo($app);

$vp->set(static function (VirtualPage $vp) {
    $form = Form::addTo($vp);
    $form->addControl('category', [Form\Control\Lookup::class, 'model' => new Category($vp->getApp()->db)]);
    $form->onSubmit(static function (Form $form) {
        $category = $form->getControl('category')->model->load($form->model->get('category'));

        return new JsToast($category->getTitle());
    });
});

$g = Grid::addTo($app, ['menu' => ['class' => ['atk-grid-menu']]]);
$g->setModel($product);

$g->menu->addItem(
    ['Add Category'],
    new JsModal('New Category', $vp)
);
