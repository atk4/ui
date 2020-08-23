<?php

declare(strict_types=1);

namespace atk4\ui\demo;

/** @var \atk4\ui\App $app */
require_once __DIR__ . '/../init-app.php';

\atk4\ui\Header::addTo($app, ['Modal View']);

$session = new Session();
// Re-usable component implementing counter

\atk4\ui\Header::addTo($app, ['Static Modal Dialog']);

$bar = \atk4\ui\View::addTo($app, ['ui' => 'buttons']);

$modal = \atk4\ui\Modal::addTo($app, ['title' => 'Add a name']);
\atk4\ui\LoremIpsum::addTo($modal);
\atk4\ui\Button::addTo($modal, ['Hide'])->on('click', $modal->hide());

$noTitle = \atk4\ui\Modal::addTo($app, ['title' => false]);
\atk4\ui\LoremIpsum::addTo($noTitle);
\atk4\ui\Button::addTo($noTitle, ['Hide'])->on('click', $noTitle->hide());

$scrolling = \atk4\ui\Modal::addTo($app, ['title' => 'Long Content that Scrolls inside Modal']);
$scrolling->addScrolling();
\atk4\ui\LoremIpsum::addTo($scrolling);
\atk4\ui\LoremIpsum::addTo($scrolling);
\atk4\ui\LoremIpsum::addTo($scrolling);
\atk4\ui\Button::addTo($scrolling, ['Hide'])->on('click', $scrolling->hide());

\atk4\ui\Button::addTo($bar, ['Show'])->on('click', $modal->show());
\atk4\ui\Button::addTo($bar, ['No Title'])->on('click', $noTitle->show());
\atk4\ui\Button::addTo($bar, ['Scrolling Content'])->on('click', $scrolling->show());

// Modal demos.

// REGULAR

$simpleModal = \atk4\ui\Modal::addTo($app, ['title' => 'Simple modal']);
\atk4\ui\Message::addTo($simpleModal)->set('Modal message here.');
ViewTester::addTo($simpleModal);

$menuBar = \atk4\ui\View::addTo($app, ['ui' => 'buttons']);
$button = \atk4\ui\Button::addTo($menuBar)->set('Show Modal');
$button->on('click', $simpleModal->show());

// DYNAMIC

\atk4\ui\Header::addTo($app, ['Three levels of Modal loading dynamic content via callback']);

// vp1Modal will be render into page but hide until $vp1Modal->show() is activate.
$vp1Modal = \atk4\ui\Modal::addTo($app, ['title' => 'Lorem Ipsum load dynamically']);

// vp2Modal will be render into page but hide until $vp1Modal->show() is activate.
$vp2Modal = \atk4\ui\Modal::addTo($app, ['title' => 'Text message load dynamically'])->addClass('small');

$vp3Modal = \atk4\ui\Modal::addTo($app, ['title' => 'Third level modal'])->addClass('small');
$vp3Modal->set(function ($modal) {
    \atk4\ui\Text::addTo($modal)->set('This is yet another modal');
    \atk4\ui\LoremIpsum::addTo($modal, ['size' => 2]);
});

// When $vp1Modal->show() is activate, it will dynamically add this content to it.
$vp1Modal->set(function ($modal) use ($vp2Modal) {
    ViewTester::addTo($modal);
    \atk4\ui\View::addTo($modal, ['Showing lorem ipsum']); // need in behat test.
    \atk4\ui\LoremIpsum::addTo($modal, ['size' => 2]);
    $form = \atk4\ui\Form::addTo($modal);
    $form->addControl('color', null, ['enum' => ['red', 'green', 'blue'], 'default' => 'green']);
    $form->onSubmit(function (\atk4\ui\Form $form) use ($vp2Modal) {
        return $vp2Modal->show(['color' => $form->model->get('color')]);
    });
});

// When $vp2Modal->show() is activate, it will dynamically add this content to it.
$vp2Modal->set(function ($modal) use ($vp3Modal) {
    // ViewTester::addTo($modal);
    \atk4\ui\Message::addTo($modal, ['Message', @$_GET['color']])->text->addParagraph('This text is loaded using a second modal.');
    \atk4\ui\Button::addTo($modal)->set('Third modal')->on('click', $vp3Modal->show());
});

$bar = \atk4\ui\View::addTo($app, ['ui' => 'buttons']);
$button = \atk4\ui\Button::addTo($bar)->set('Open Lorem Ipsum');
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

\atk4\ui\Header::addTo($app, ['Modal Animation']);

$transitionModal = \atk4\ui\Modal::addTo($app, ['title' => 'Animated modal']);
\atk4\ui\Message::addTo($transitionModal)->set('A lot of animated transition available');
$transitionModal->duration(1000);

$menuBar = \atk4\ui\View::addTo($app, ['ui' => 'buttons']);
$main = \atk4\ui\Menu::addTo($menuBar);
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

\atk4\ui\Header::addTo($app, ['Modal Options']);

$denyApproveModal = \atk4\ui\Modal::addTo($app, ['title' => 'Deny / Approve actions']);
\atk4\ui\Message::addTo($denyApproveModal)->set('This modal is only closable via the green button');
$denyApproveModal->addDenyAction('No', new \atk4\ui\JsExpression('function(){window.alert("Can\'t do that."); return false;}'));
$denyApproveModal->addApproveAction('Yes', new \atk4\ui\JsExpression('function(){window.alert("You\'re good to go!");}'));
$denyApproveModal->notClosable();

$menuBar = \atk4\ui\View::addTo($app, ['ui' => 'buttons']);
$button = \atk4\ui\Button::addTo($menuBar)->set('Show Deny/Approve');
$button->on('click', $denyApproveModal->show());

// MULTI STEP

\atk4\ui\Header::addTo($app, ['Multiple page modal']);

// Add modal to layout.
$stepModal = \atk4\ui\Modal::addTo($app, ['title' => 'Multi step actions']);
$stepModal->setOption('observeChanges', true);

// Add buttons to modal for next and previous actions.
$action = new \atk4\ui\View(['ui' => 'buttons']);
$prevAction = new \atk4\ui\Button(['Prev', 'labeled', 'icon' => 'left arrow']);
$nextAction = new \atk4\ui\Button(['Next', 'iconRight' => 'right arrow']);

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
        \atk4\ui\Message::addTo($modal)->set('Thanks for choosing us. We will be asking some questions along the way.');
        $session->memorize('success', true);
        $modal->js(true, $prevAction->js(true)->show());
        $modal->js(true, $nextAction->js(true)->show());
        $modal->js(true, $prevAction->js()->addClass('disabled'));
        $modal->js(true, $nextAction->js(true)->removeClass('disabled'));
    } elseif ($page === 2) {
        $modelRegister = new \atk4\data\Model(new \atk4\data\Persistence\Array_());
        $modelRegister->addField('name', ['caption' => 'Please enter your name (John)']);

        $form = \atk4\ui\Form::addTo($modal, ['segment' => true]);
        $form->setModel($modelRegister);

        $form->onSubmit(function (\atk4\ui\Form $form) use ($nextAction, $session) {
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
        \atk4\ui\Message::addTo($modal)->set("Thank you {$name} for visiting us! We will be in touch");
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
$menuBar = \atk4\ui\View::addTo($app, ['ui' => 'buttons']);
$button = \atk4\ui\Button::addTo($menuBar)->set('Multi Step Modal');
$button->on('click', $stepModal->show());
