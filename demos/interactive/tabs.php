<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Data\Model;
use Atk4\Data\Persistence;
use Atk4\Ui\Button;
use Atk4\Ui\Form;
use Atk4\Ui\HelloWorld;
use Atk4\Ui\LoremIpsum;
use Atk4\Ui\Message;
use Atk4\Ui\Modal;
use Atk4\Ui\Tabs;
use Atk4\Ui\View;
use Atk4\Ui\VirtualPage;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

$tabs = Tabs::addTo($app);

// static tab
HelloWorld::addTo($tabs->addTab('Hello'));
$tab = $tabs->addTab('Static Tab');
Message::addTo($tab, ['Content of this tab will refresh only if you reload entire page']);
LoremIpsum::addTo($tab);

// set the default active tab
$tabs->addTab('Default Active Tab', static function (VirtualPage $vp) {
    Message::addTo($vp, ['This is the active tab by default']);
})->setActive();

// dynamic tab
$tabs->addTab('Dynamic Lorem Ipsum', static function (VirtualPage $vp) {
    Message::addTo($vp, ['Every time you come to this tab, you will see a different text']);
    LoremIpsum::addTo($vp, ['size' => (int) ($vp->getApp()->tryGetRequestQueryParam('size') ?? 1)]);
}, ['apiSettings' => ['data' => ['size' => random_int(1, 4)]]]);

// modal tab
$tabs->addTab('Modal popup', static function (VirtualPage $vp) {
    Button::addTo($vp, ['Load Lorem'])
        ->on('click', Modal::addTo($vp)->set(static function (View $p) {
            LoremIpsum::addTo($p, ['size' => 2]);
        })->jsShow());
});

// dynamic tab
$tabs->addTab('Dynamic Form', static function (VirtualPage $vp) {
    Message::addTo($vp, ['It takes 2 seconds for this tab to load', 'type' => 'warning']);
    sleep(2);
    $modelRegister = new Model(new Persistence\Array_());
    $modelRegister->addField('name', ['caption' => 'Please enter your name (John)']);

    $form = Form::addTo($vp, ['class.segment' => true]);
    $form->setModel($modelRegister->createEntity());
    $form->onSubmit(static function (Form $form) {
        if ($form->model->get('name') !== 'John') {
            return $form->jsError('name', 'Your name is not John! It is "' . $form->model->get('name') . '". It should be John. Pleeease!');
        }
    });
});

$tabs->addTabUrl('Any other page', './');
