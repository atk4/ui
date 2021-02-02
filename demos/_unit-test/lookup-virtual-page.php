<?php

declare(strict_types=1);
/**
 * Test for Lookup inside VirtualPage.
 */

namespace Atk4\Ui\Demos;

use Atk4\Ui\Form;
use Atk4\Ui\Grid;
use Atk4\Ui\JsModal;
use Atk4\Ui\JsToast;
use Atk4\Ui\VirtualPage;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

$product = new ProductLock($app->db);

$vp = VirtualPage::addTo($app);

$vp->set(function ($page) {
    $form = Form::addTo($page);
    $form->addControl('category', [Form\Control\Lookup::class, 'model' => new Category($page->getApp()->db)]);
    $form->onSubmit(function ($f) {
        $category = $f->getControl('category')->model->load($f->model->get('category'));

        return new JsToast($category->getTitle());
    });
});

$g = Grid::addTo($app, ['menu' => ['class' => ['atk-grid-menu']]]);
$g->setModel($product);

$g->menu->addItem(
    ['Add Category'],
    new JsModal('New Category', $vp)
);
