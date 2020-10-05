<?php

declare(strict_types=1);
/**
 * Behat testing.
 * Test for triggerOnReload = false for Callback.
 */

namespace atk4\ui\demo;

use atk4\ui\Button;
use atk4\ui\Form;
use atk4\ui\Jquery;
use atk4\ui\JsToast;

/** @var \atk4\ui\App $app */
require_once __DIR__ . '/../init-app.php';

$m = (new CountryLock($app->db))->setLimit(5);

$vp = $app->add(new \atk4\ui\VirtualPage());
$vp->cb->triggerOnReload = false;

$form = Form::addTo($vp);
$form->setModel((clone $m)->tryLoadAny(), ['name']);
$form->getControl('name')->caption = 'TestName';

$table = $app->add(new \atk4\ui\Table());
$table->setModel($m);

$button = Button::addTo($app, ['First', ['ui' => 'atk-test']]);
$button->on('click', new \atk4\ui\JsModal('Edit First Record', $vp));

$form->onSubmit(function ($form) use ($table) {
    $form->model->save();

    return [
        $table->jsReload(),
        new JsToast('Save'),
        (new Jquery('.ui.modal.visible.active.front'))->modal('hide'),
    ];
});
