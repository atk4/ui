<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Data\Model;
use Atk4\Data\Persistence;
use Atk4\Ui\Button;
use Atk4\Ui\Form;
use Atk4\Ui\Header;
use Atk4\Ui\JsExpression;
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
Button::addTo($modal, ['Hide'])->on('click', $modal->hide());

$noTitle = Modal::addTo($app, ['title' => false]);
LoremIpsum::addTo($noTitle);
Button::addTo($noTitle, ['Hide'])->on('click', $noTitle->hide());

$scrolling = Modal::addTo($app, ['title' => 'Long Content that Scrolls inside Modal']);
$scrolling->addScrolling();
LoremIpsum::addTo($scrolling);
LoremIpsum::addTo($scrolling);
LoremIpsum::addTo($scrolling);
Button::addTo($scrolling, ['Hide'])->on('click', $scrolling->hide());

Button::addTo($bar, ['Show'])->on('click', $modal->show());
Button::addTo($bar, ['No Title'])->on('click', $noTitle->show());
Button::addTo($bar, ['Scrolling Content'])->on('click', $scrolling->show());

// Modal demos.

// REGULAR

$simpleModal = Modal::addTo($app, ['title' => 'Simple modal']);
Message::addTo($simpleModal)->set('Modal message here.');
ViewTester::addTo($simpleModal);

$menuBar = View::addTo($app, ['ui' => 'buttons']);
$button = Button::addTo($menuBar)->set('Show Modal');
$button->on('click', $simpleModal->show());

// DYNAMIC

Header::addTo($app, ['Three levels of Modal loading dynamic content via callback']);

// vp1Modal will be render into page but hide until $vp1Modal->show() is activate.
$vp1Modal = Modal::addTo($app, ['title' => 'Lorem Ipsum load dynamically']);

// vp2Modal will be render into page but hide until $vp1Modal->show() is activate.
$vp2Modal = Modal::addTo($app, ['title' => 'Text message load dynamically'])->addClass('small');

$vp3Modal = Modal::addTo($app, ['title' => 'Third level modal'])->addClass('small');
$vp3Modal->set(function (View $p) {
    Text::addTo($p)->set('This is yet another modal');
    LoremIpsum::addTo($p, ['size' => 2]);
});

// When $vp1Modal->show() is activate, it will dynamically add this content to it.
$vp1Modal->set(function (View $p) use ($vp2Modal) {
    ViewTester::addTo($p);
    View::addTo($p, ['Showing lorem ipsum']); // need in behat test.
    LoremIpsum::addTo($p, ['size' => 2]);
    $form = Form::addTo($p);
    $form->addControl('color', [], ['enum' => ['red', 'green', 'blue'], 'default' => 'green']);
    $form->onSubmit(function (Form $form) use ($vp2Modal) {
        return $vp2Modal->show(['color' => $form->model->get('color')]);
    });
});

// When $vp2Modal->show() is activate, it will dynamically add this content to it.
$vp2Modal->set(function (View $p) use ($vp3Modal) {
    ViewTester::addTo($p);
    Message::addTo($p, [$_GET['color'] ?? 'No color'])->text->addParagraph('This text is loaded using a second modal.');
    Button::addTo($p)->set('Third modal')->on('click', $vp3Modal->show());
});

$bar = View::addTo($app, ['ui' => 'buttons']);
$button = Button::addTo($bar)->set('Open Lorem Ipsum');
$button->on('click', $vp1Modal->show());

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
$denyApproveModal->addDenyAction('No', new JsExpression('function() { window.alert("Can\'t do that."); return false; }'));
$denyApproveModal->addApproveAction('Yes', new JsExpression('function() { window.alert("You\'re good to go!"); }'));
$denyApproveModal->notClosable();

$menuBar = View::addTo($app, ['ui' => 'buttons']);
$button = Button::addTo($menuBar)->set('Show Deny/Approve');
$button->on('click', $denyApproveModal->show());

// MULTI STEP

Header::addTo($app, ['Multiple page modal']);

// Add modal to layout.
$stepModal = Modal::addTo($app, ['title' => 'Multi step actions']);

// Add buttons to modal for next and previous actions.
$action = new View(['ui' => 'buttons']);
$prevAction = new Button(['Prev', 'class.labeled' => true, 'icon' => 'left arrow']);
$nextAction = new Button(['Next', 'iconRight' => 'right arrow']);

$action->add($prevAction);
$action->add($nextAction);

$stepModal->addButtonAction($action);

// Set modal functionality. Will changes content according to page being displayed.
$stepModal->set(function (View $p) use ($stepModal, $session, $prevAction, $nextAction) {
    $page = $session->recall('page', 1);
    $success = $session->recall('success', false);
    if (isset($_GET['move'])) {
        if ($_GET['move'] === 'next' && $success) {
            ++$page;
        }
        if ($_GET['move'] === 'prev' && $page > 1) {
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
        $p->js(true, $prevAction->js(true)->show());
        $p->js(true, $nextAction->js(true)->show());
        $p->js(true, $prevAction->js()->addClass('disabled'));
        $p->js(true, $nextAction->js(true)->removeClass('disabled'));
    } elseif ($page === 2) {
        $modelRegister = new Model(new Persistence\Array_());
        $modelRegister->addField('name', ['caption' => 'Please enter your name (John)']);

        $form = Form::addTo($p, ['class.segment' => true]);
        $form->setModel($modelRegister->createEntity());

        $form->onSubmit(function (Form $form) use ($nextAction, $session) {
            if ($form->model->get('name') !== 'John') {
                return $form->error('name', 'Your name is not John! It is "' . $form->model->get('name') . '". It should be John. Pleeease!');
            }

            $session->memorize('success', true);
            $session->memorize('name', $form->model->get('name'));

            $js = [];
            $js[] = $form->success('Thank you, ' . $form->model->get('name') . ' you can go on!');
            $js[] = $nextAction->js()->removeClass('disabled');

            return $js;
        });
        $p->js(true, $prevAction->js()->removeClass('disabled'));
        $p->js(true, $nextAction->js(true)->addClass('disabled'));
    } elseif ($page === 3) {
        $name = $session->recall('name');
        Message::addTo($p)->set("Thank you {$name} for visiting us! We will be in touch");
        $session->memorize('success', true);
        $p->js(true, $prevAction->js(true)->hide());
        $p->js(true, $nextAction->js(true)->hide());
    }
    $stepModal->js(true)->modal('refresh');
});

// Bind next action to modal next button.
$nextAction->on('click', $stepModal->js()->atkReloadView(
    ['uri' => $stepModal->cb->getJsUrl(), 'uriOptions' => ['move' => 'next']]
));

// Bin prev action to modal previous button.
$prevAction->on('click', $stepModal->js()->atkReloadView(
    ['uri' => $stepModal->cb->getJsUrl(), 'uriOptions' => ['move' => 'prev']]
));

// Bind display modal to page display button.
$menuBar = View::addTo($app, ['ui' => 'buttons']);
$button = Button::addTo($menuBar)->set('Multi Step Modal');
$button->on('click', $stepModal->show());
