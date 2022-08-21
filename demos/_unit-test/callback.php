<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\Button;
use Atk4\Ui\Form;
use Atk4\Ui\Jquery;
use Atk4\Ui\JsModal;
use Atk4\Ui\JsToast;
use Atk4\Ui\Table;
use Atk4\Ui\VirtualPage;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

$m = (new Country($app->db))->setLimit(5);

$vp = VirtualPage::addTo($app);
$vp->cb->triggerOnReload = false;

$form = Form::addTo($vp);
$form->setModel($m->loadAny(), [$m->fieldName()->name]);
$form->getControl($m->fieldName()->name)->caption = 'TestName';

$table = Table::addTo($app);
$table->setModel($m);

$button = Button::addTo($app, ['First', 'class.atk-test' => true]);
$button->on('click', new JsModal('Edit First Record', $vp));

$form->onSubmit(function (Form $form) use ($table) {
    $form->model->save();

    return [
        $table->jsReload(),
        new JsToast('Save'),
        (new Jquery('.ui.modal.visible.active.front'))->modal('hide'),
    ];
});
