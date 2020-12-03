<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

\Atk4\Ui\Header::addTo($app, ['Modal View']);

$session = new Session();
// Re-usable component implementing counter

\Atk4\Ui\Header::addTo($app, ['Static Modal Dialog']);

$bar = \Atk4\Ui\View::addTo($app, ['ui' => 'buttons']);

$modal = \Atk4\Ui\Modal::addTo($app, ['title' => 'Add a name']);
\Atk4\Ui\LoremIpsum::addTo($modal);
\Atk4\Ui\Button::addTo($modal, ['Hide'])->on('click', $modal->hide());

$noTitle = \Atk4\Ui\Modal::addTo($app, ['title' => false]);
\Atk4\Ui\LoremIpsum::addTo($noTitle);
\Atk4\Ui\Button::addTo($noTitle, ['Hide'])->on('click', $noTitle->hide());

$scrolling = \Atk4\Ui\Modal::addTo($app, ['title' => 'Long Content that Scrolls inside Modal']);
$scrolling->addScrolling();
\Atk4\Ui\LoremIpsum::addTo($scrolling);
\Atk4\Ui\LoremIpsum::addTo($scrolling);
\Atk4\Ui\LoremIpsum::addTo($scrolling);
\Atk4\Ui\Button::addTo($scrolling, ['Hide'])->on('click', $scrolling->hide());

\Atk4\Ui\Button::addTo($bar, ['Show'])->on('click', $modal->show());
\Atk4\Ui\Button::addTo($bar, ['No Title'])->on('click', $noTitle->show());
\Atk4\Ui\Button::addTo($bar, ['Scrolling Content'])->on('click', $scrolling->show());

// Modal demos.

// REGULAR

$simpleModal = \Atk4\Ui\Modal::addTo($app, ['title' => 'Simple modal']);
\Atk4\Ui\Message::addTo($simpleModal)->set('Modal message here.');
ViewTester::addTo($simpleModal);

$menuBar = \Atk4\Ui\View::addTo($app, ['ui' => 'buttons']);
$button = \Atk4\Ui\Button::addTo($menuBar)->set('Show Modal');
$button->on('click', $simpleModal->show());

// DYNAMIC

\Atk4\Ui\Header::addTo($app, ['Three levels of Modal loading dynamic content via callback']);

// vp1Modal will be render into page but hide until $vp1Modal->show() is activate.
$vp1Modal = \Atk4\Ui\Modal::addTo($app, ['title' => 'Lorem Ipsum load dynamically']);

// vp2Modal will be render into page but hide until $vp1Modal->show() is activate.
$vp2Modal = \Atk4\Ui\Modal::addTo($app, ['title' => 'Text message load dynamically'])->addClass('small');

$vp3Modal = \Atk4\Ui\Modal::addTo($app, ['title' => 'Third level modal'])->addClass('small');
$vp3Modal->set(function ($modal) {
    \Atk4\Ui\Text::addTo($modal)->set('This is yet another modal');
    \Atk4\Ui\LoremIpsum::addTo($modal, ['size' => 2]);
});

// When $vp1Modal->show() is activate, it will dynamically add this content to it.
$vp1Modal->set(function ($modal) use ($vp2Modal) {
    ViewTester::addTo($modal);
    \Atk4\Ui\View::addTo($modal, ['Showing lorem ipsum']); // need in behat test.
    \Atk4\Ui\LoremIpsum::addTo($modal, ['size' => 2]);
    $form = \Atk4\Ui\Form::addTo($modal);
    $form->addControl('color', null, ['enum' => ['red', 'green', 'blue'], 'default' => 'green']);
    $form->onSubmit(function (\Atk4\Ui\Form $form) use ($vp2Modal) {
        return $vp2Modal->show(['color' => $form->model->get('color')]);
    });
});

// When $vp2Modal->show() is activate, it will dynamically add this content to it.
$vp2Modal->set(function ($modal) use ($vp3Modal) {
    // ViewTester::addTo($modal);
    \Atk4\Ui\Message::addTo($modal, ['Message', @$_GET['color']])->text->addParagraph('This text is loaded using a second modal.');
    \Atk4\Ui\Button::addTo($modal)->set('Third modal')->on('click', $vp3Modal->show());
});

$bar = \Atk4\Ui\View::addTo($app, ['ui' => 'buttons']);
$button = \Atk4\Ui\Button::addTo($bar)->set('Open Lorem Ipsum');
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

\Atk4\Ui\Header::addTo($app, ['Modal Animation']);

$transitionModal = \Atk4\Ui\Modal::addTo($app, ['title' => 'Animated modal']);
\Atk4\Ui\Message::addTo($transitionModal)->set('A lot of animated transition available');
$transitionModal->duration(1000);

$menuBar = \Atk4\Ui\View::addTo($app, ['ui' => 'buttons']);
$main = \Atk4\Ui\Menu::addTo($menuBar);
$transitionMenu = $main->addMenu('Select Transition');

foreach ($menuItems as $key => $items) {
    if (!empty($items)) {
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

\Atk4\Ui\Header::addTo($app, ['Modal Options']);

$denyApproveModal = \Atk4\Ui\Modal::addTo($app, ['title' => 'Deny / Approve actions']);
\Atk4\Ui\Message::addTo($denyApproveModal)->set('This modal is only closable via the green button');
$denyApproveModal->addDenyAction('No', new \Atk4\Ui\JsExpression('function(){window.alert("Can\'t do that."); return false;}'));
$denyApproveModal->addApproveAction('Yes', new \Atk4\Ui\JsExpression('function(){window.alert("You\'re good to go!");}'));
$denyApproveModal->notClosable();

$menuBar = \Atk4\Ui\View::addTo($app, ['ui' => 'buttons']);
$button = \Atk4\Ui\Button::addTo($menuBar)->set('Show Deny/Approve');
$button->on('click', $denyApproveModal->show());

// MULTI STEP

\Atk4\Ui\Header::addTo($app, ['Multiple page modal']);

// Add modal to layout.
$stepModal = \Atk4\Ui\Modal::addTo($app, ['title' => 'Multi step actions']);
$stepModal->setOption('observeChanges', true);

// Add buttons to modal for next and previous actions.
$action = new \Atk4\Ui\View(['ui' => 'buttons']);
$prevAction = new \Atk4\Ui\Button(['Prev', 'labeled', 'icon' => 'left arrow']);
$nextAction = new \Atk4\Ui\Button(['Next', 'iconRight' => 'right arrow']);

$action->add($prevAction);
$action->add($nextAction);

$stepModal->addButtonAction($action);

// Set modal functionality. Will changes content according to page being displayed.
$stepModal->set(function ($modal) use ($stepModal, $session, $prevAction, $nextAction) {
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
        \Atk4\Ui\Message::addTo($modal)->set('Thanks for choosing us. We will be asking some questions along the way.');
        $session->memorize('success', true);
        $modal->js(true, $prevAction->js(true)->show());
        $modal->js(true, $nextAction->js(true)->show());
        $modal->js(true, $prevAction->js()->addClass('disabled'));
        $modal->js(true, $nextAction->js(true)->removeClass('disabled'));
    } elseif ($page === 2) {
        $modelRegister = new \Atk4\Data\Model(new \Atk4\Data\Persistence\Array_());
        $modelRegister->addField('name', ['caption' => 'Please enter your name (John)']);

        $form = \Atk4\Ui\Form::addTo($modal, ['segment' => true]);
        $form->setModel($modelRegister);

        $form->onSubmit(function (\Atk4\Ui\Form $form) use ($nextAction, $session) {
            if ($form->model->get('name') !== 'John') {
                return $form->error('name', 'Your name is not John! It is "' . $form->model->get('name') . '". It should be John. Pleeease!');
            }

            $session->memorize('success', true);
            $session->memorize('name', $form->model->get('name'));
            $js[] = $form->success('Thank you, ' . $form->model->get('name') . ' you can go on!');
            $js[] = $nextAction->js()->removeClass('disabled');

            return $js;
        });
        $modal->js(true, $prevAction->js()->removeClass('disabled'));
        $modal->js(true, $nextAction->js(true)->addClass('disabled'));
    } elseif ($page === 3) {
        $name = $session->recall('name');
        \Atk4\Ui\Message::addTo($modal)->set("Thank you {$name} for visiting us! We will be in touch");
        $session->memorize('success', true);
        $modal->js(true, $prevAction->js(true)->hide());
        $modal->js(true, $nextAction->js(true)->hide());
    }
    $stepModal->js(true)->modal('refresh');
});

// Bind next action to modal next button.
$nextAction->on('click', $stepModal->js()->atkReloadView(
    ['uri' => $stepModal->cb->getJsUrl(), 'uri_options' => ['move' => 'next']]
));

// Bin prev action to modal previous button.
$prevAction->on('click', $stepModal->js()->atkReloadView(
    ['uri' => $stepModal->cb->getJsUrl(), 'uri_options' => ['move' => 'prev']]
));

// Bind display modal to page display button.
$menuBar = \Atk4\Ui\View::addTo($app, ['ui' => 'buttons']);
$button = \Atk4\Ui\Button::addTo($menuBar)->set('Multi Step Modal');
$button->on('click', $stepModal->show());
