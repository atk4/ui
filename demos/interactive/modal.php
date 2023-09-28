<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Data\Model;
use Atk4\Data\Persistence;
use Atk4\Ui\Button;
use Atk4\Ui\Form;
use Atk4\Ui\Header;
use Atk4\Ui\Js\JsExpression;
use Atk4\Ui\LoremIpsum;
use Atk4\Ui\Menu;
use Atk4\Ui\Message;
use Atk4\Ui\Modal;
use Atk4\Ui\Text;
use Atk4\Ui\View;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

Header::addTo($app, ['Modal View']);

$session = new Session($app);

// Re-usable component implementing counter

Header::addTo($app, ['Static Modal Dialog']);

$bar = View::addTo($app, ['ui' => 'buttons']);

$modal = Modal::addTo($app, ['title' => 'Add a name']);
LoremIpsum::addTo($modal);
Button::addTo($modal, ['Hide'])
    ->on('click', $modal->jsHide());

$modalNoTitle = Modal::addTo($app, ['title' => false]);
LoremIpsum::addTo($modalNoTitle);
Button::addTo($modalNoTitle, ['Hide'])
    ->on('click', $modalNoTitle->jsHide());

$modalScrolling = Modal::addTo($app, ['title' => 'Long Content that Scrolls inside Modal']);
$modalScrolling->addScrolling();
LoremIpsum::addTo($modalScrolling);
LoremIpsum::addTo($modalScrolling);
LoremIpsum::addTo($modalScrolling);
Button::addTo($modalScrolling, ['Hide'])
    ->on('click', $modalScrolling->jsHide());

Button::addTo($bar, ['Show'])
    ->on('click', $modal->jsShow());
Button::addTo($bar, ['No Title'])
    ->on('click', $modalNoTitle->jsShow());
Button::addTo($bar, ['Scrolling Content'])
    ->on('click', $modalScrolling->jsShow());

// Modal demos

// REGULAR

$simpleModal = Modal::addTo($app, ['title' => 'Simple modal']);
Message::addTo($simpleModal)->set('Modal message here.');
ViewTester::addTo($simpleModal);

$menuBar = View::addTo($app, ['ui' => 'buttons']);
$button = Button::addTo($menuBar)->set('Show Modal');
$button->on('click', $simpleModal->jsShow());

// DYNAMIC

Header::addTo($app, ['Three levels of Modal loading dynamic content via callback']);

// vp1Modal will be render into page but hide until $vp1Modal->jsShow() is activate
$vp1Modal = Modal::addTo($app, ['title' => 'Lorem Ipsum load dynamically']);

// vp2Modal will be render into page but hide until $vp1Modal->jsShow() is activate
$vp2Modal = Modal::addTo($app, ['title' => 'Text message load dynamically'])->addClass('small');

$vp3Modal = Modal::addTo($app, ['title' => 'Third level modal'])->addClass('small');
$vp3Modal->set(static function (View $p) {
    Text::addTo($p)->set('This is yet another modal');
    LoremIpsum::addTo($p, ['size' => 2]);
});

// when $vp1Modal->jsShow() is activate, it will dynamically add this content to it
$vp1Modal->set(static function (View $p) use ($vp2Modal) {
    ViewTester::addTo($p);
    View::addTo($p, ['Showing lorem ipsum']); // need in behat test
    LoremIpsum::addTo($p, ['size' => 2]);
    $form = Form::addTo($p);
    $form->addControl('color', [], ['enum' => ['red', 'green', 'blue'], 'default' => 'green']);
    $form->onSubmit(static function (Form $form) use ($vp2Modal) {
        return $vp2Modal->jsShow(['color' => $form->model->get('color')]);
    });
});

// when $vp2Modal->jsShow() is activate, it will dynamically add this content to it
$vp2Modal->set(static function (View $p) use ($vp3Modal) {
    ViewTester::addTo($p);
    Message::addTo($p, [$p->getApp()->tryGetRequestQueryParam('color') ?? 'No color'])->text->addParagraph('This text is loaded using a second modal.');
    Button::addTo($p)->set('Third modal')
        ->on('click', $vp3Modal->jsShow());
});

$bar = View::addTo($app, ['ui' => 'buttons']);
$button = Button::addTo($bar)->set('Open Lorem Ipsum');
$button->on('click', $vp1Modal->jsShow());

// ANIMATION

$menuItems = [
    'scale' => [],
    'flip' => ['horizontal flip', 'vertical flip'],
    'fade' => ['fade up', 'fade down', 'fade left', 'fade right'],
    'drop' => [],
    'fly' => ['fly left', 'fly right', 'fly up', 'fly down'],
    'swing' => ['swing left', 'swing right', 'swing up', 'swing down'],
    'slide' => ['slide left', 'slide right', 'slide up', 'slide down'],
    'browse' => ['browse', 'browse right'],
    'static' => ['jiggle', 'flash', 'shake', 'pulse', 'tada', 'bounce'],
];

Header::addTo($app, ['Modal Animation']);

$transitionModal = Modal::addTo($app, ['title' => 'Animated modal']);
Message::addTo($transitionModal)->set('A lot of animated transition available');
$transitionModal->setOption('duration', 1000);

$menuBar = View::addTo($app, ['ui' => 'buttons']);
$main = Menu::addTo($menuBar);
$transitionMenu = $main->addMenu('Select Transition');

foreach ($menuItems as $key => $items) {
    if ($items !== []) {
        $sm = $transitionMenu->addMenu($key);
        foreach ($items as $item) {
            $smi = $sm->addItem($item);
            $smi->on('click', $transitionModal->js()->modal('setting', 'transition', $smi->js()->text())->modal('show'));
        }
    } else {
        $mi = $transitionMenu->addItem($key);
        $mi->on('click', $transitionModal->js()->modal('setting', 'transition', $mi->js()->text())->modal('show'));
    }
}

// DENY APPROVE

Header::addTo($app, ['Modal Options']);

$denyApproveModal = Modal::addTo($app, ['title' => 'Deny / Approve actions']);
Message::addTo($denyApproveModal)->set('This modal is only closable via the green button');
$denyApproveModal->addDenyAction('No', new JsExpression('function () { window.alert(\'Cannot do that.\'); return false; }'));
$denyApproveModal->addApproveAction('Yes', new JsExpression('function () { window.alert(\'You are good to go!\'); }'));
$denyApproveModal->notClosable();

$menuBar = View::addTo($app, ['ui' => 'buttons']);
$button = Button::addTo($menuBar)->set('Show Deny/Approve');
$button->on('click', $denyApproveModal->jsShow());

// MULTI STEP

Header::addTo($app, ['Multiple page modal']);

// add modal to layout
$stepModal = Modal::addTo($app, ['title' => 'Multi step actions']);

// add buttons to modal for next and previous actions
$action = new View(['ui' => 'buttons']);
$previousAction = new Button(['Previous', 'icon' => 'left arrow']);
$nextAction = new Button(['Next', 'iconRight' => 'right arrow']);

$action->add($previousAction);
$action->add($nextAction);

$stepModal->addButtonAction($action);

// Set modal functionality. Will changes content according to page being displayed.
$stepModal->set(static function (View $p) use ($session, $previousAction, $nextAction) {
    $page = $session->recall('page', 1);
    $success = $session->recall('success', false);
    if ($p->getApp()->hasRequestQueryParam('move')) {
        $move = $p->getApp()->getRequestQueryParam('move');
        if ($move === 'next' && $success) {
            ++$page;
        } elseif ($move === 'previous' && $page > 1) {
            --$page;
        }
        $session->memorize('success', false);
    } elseif ($page === 2) {
    } else {
        $page = 1;
    }
    $session->memorize('page', $page);
    if ($page === 1) {
        Message::addTo($p)->set('Thanks for choosing us. We will be asking some questions along the way.');
        $session->memorize('success', true);
        $p->js(true, $previousAction->js()->show());
        $p->js(true, $nextAction->js()->show());
        $p->js(true, $previousAction->js()->addClass('disabled'));
        $p->js(true, $nextAction->js()->removeClass('disabled'));
    } elseif ($page === 2) {
        $modelRegister = new Model(new Persistence\Array_());
        $modelRegister->addField('name', ['caption' => 'Please enter your name (John)']);

        $form = Form::addTo($p, ['class.segment' => true]);
        $form->setModel($modelRegister->createEntity());

        $form->onSubmit(static function (Form $form) use ($nextAction, $session) {
            if ($form->model->get('name') !== 'John') {
                return $form->jsError('name', 'Your name is not John! It is "' . $form->model->get('name') . '". It should be John. Pleeease!');
            }

            $session->memorize('success', true);
            $session->memorize('name', $form->model->get('name'));

            $js = [];
            $js[] = $form->jsSuccess('Thank you, ' . $form->model->get('name') . ' you can go on!');
            $js[] = $nextAction->js()->removeClass('disabled');

            return $js;
        });
        $p->js(true, $previousAction->js()->removeClass('disabled'));
        $p->js(true, $nextAction->js()->addClass('disabled'));
    } elseif ($page === 3) {
        $name = $session->recall('name');
        Message::addTo($p)->set("Thank you {$name} for visiting us! We will be in touch");
        $session->memorize('success', true);
        $p->js(true, $previousAction->js()->hide());
        $p->js(true, $nextAction->js()->hide());
    }
});

$previousAction->on('click', $stepModal->js()->atkReloadView(
    ['url' => $stepModal->cb->getJsUrl(), 'urlOptions' => ['move' => 'previous']]
));

$nextAction->on('click', $stepModal->js()->atkReloadView(
    ['url' => $stepModal->cb->getJsUrl(), 'urlOptions' => ['move' => 'next']]
));

// bind display modal to page display button
$menuBar = View::addTo($app, ['ui' => 'buttons']);
$button = Button::addTo($menuBar)->set('Multi Step Modal');
$button->on('click', $stepModal->jsShow());
