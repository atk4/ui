<?php

require 'Session.php';
require 'init.php';

$app->add(['Header', 'Modal View']);

$session = new Session();

/*
 * Modal demos.
 */

/********** REGULAR ******************/

$modal_simple = $app->add(['Modal', 'title' => 'Simple modal']);
$modal_simple->add('Message')->set('Modal message here.');
$modal_simple->add(new \atk4\ui\tests\ViewTester());

$menu_bar = $app->add(['View', 'ui' => 'buttons']);
$b = $menu_bar->add('Button')->set('Show Modal');
$b->on('click', $modal_simple->show());

/********** DYNAMIC ******************/

$app->add(['Header', 'Three levels of Modal loading dynamic content via callback']);

//modal_vp1 will be render into page but hide until $modal_vp1->show() is activate.
$modal_vp1 = $app->add(['Modal', 'title' => 'Lorem Ipsum load dynamically']);

//modal_vp2 will be render into page but hide until $modal_vp1->show() is activate.
$modal_vp2 = $app->add(['Modal', 'title' => 'Text message load dynamically'])->addClass('small');

$modal_vp3 = $app->add(['Modal', 'title' => 'Third level modal'])->addClass('small');
$modal_vp3->set(function ($modal) {
    $modal->add(['Text'])->set('This is yet another modal');
    //$modal->add(['LoremIpsum', 'size' => 2]);
});

//When $modal_vp1->show() is activate, it will dynamically add this content to it.
$modal_vp1->set(function ($modal) use ($modal_vp2) {
    $modal->add(new \atk4\ui\tests\ViewTester());
    $modal->add(['View', 'Showing lorem ipsum']); //need in behat test.
    $modal->add(['LoremIpsum', 'size' => 2]);
    $form = $modal->add('Form');
    $form->addField('color', null, ['enum' => ['red', 'green', 'blue'], 'default' => 'green']);
    $form->onSubmit(function ($form) use ($modal_vp2) {
        return $modal_vp2->show(['color' => $form->model['color']]);
    });
});

//When $modal_vp2->show() is activate, it will dynamically add this content to it.
$modal_vp2->set(function ($modal) use ($modal_vp3) {
    //$modal->add(new \atk4\ui\tests\ViewTester());
    $modal->add(['Message', 'Message', @$_GET['color']])->text->addParagraph('This text is loaded using a second modal.');
    $modal->add('Button')->set('Third modal')->on('click', $modal_vp3->show());
});

$bar = $app->add(['View', 'ui' => 'buttons']);
$b = $bar->add('Button')->set('Open Lorem Ipsum');
$b->on('click', $modal_vp1->show());

/********** ANIMATION ***************/

$menu_items = [
    'scale'  => [],
    'flip'   => ['horizontal flip', 'vertical flip'],
    'fade'   => ['fade up', 'fade down', 'fade left', 'fade right'],
    'drop'   => [],
    'fly'    => ['fly left', 'fly right', 'fly up', 'fly down'],
    'swing'  => ['swing left', 'swing right', 'swing up', 'swing down'],
    'slide'  => ['slide left', 'slide right', 'slide up', 'slide down'],
    'browse' => ['browse', 'browse right'],
    'static' => ['jiggle', 'flash', 'shake', 'pulse', 'tada', 'bounce'],
];

$app->add(['Header', 'Modal Animation']);

$modal_transition = $app->add(['Modal', 'title' => 'Animated modal']);
$modal_transition->add('Message')->set('A lot of animated transition available');
$modal_transition->duration(1000);

$menu_bar = $app->add(['View', 'ui' => 'buttons']);
$main = $menu_bar->add('Menu');
$tm = $main->addMenu('Select Transition');

foreach ($menu_items as $key => $items) {
    if (!empty($items)) {
        $sm = $tm->addMenu($key);
        foreach ($items as $item) {
            $smi = $sm->addItem($item);
            $smi->on('click', $modal_transition->js()->modal('setting', 'transition', $smi->js()->text())->modal('show'));
        }
    } else {
        $mi = $tm->addItem($key);
        $mi->on('click', $modal_transition->js()->modal('setting', 'transition', $mi->js()->text())->modal('show'));
    }
}

/************** DENY APPROVE *********/

$app->add(['Header', 'Modal Options']);

$modal_da = $app->add(['Modal', 'title' => 'Deny / Approve actions']);
$modal_da->add('Message')->set('This modal is only closable via the green button');
$modal_da->addDenyAction('No', new \atk4\ui\jsExpression('function(){window.alert("Can\'t do that."); return false;}'));
$modal_da->addApproveAction('Yes', new \atk4\ui\jsExpression('function(){window.alert("You\'re good to go!");}'));
$modal_da->notClosable();

$menu_bar = $app->add(['View', 'ui' => 'buttons']);
$b = $menu_bar->add('Button')->set('Show Deny/Approve');
$b->on('click', $modal_da->show());

/************** MULTI STEP *********/

$app->add(['Header', 'Multiple page modal']);

//Add modal to layout.
$modal_step = $app->add(['Modal', 'title' => 'Multi step actions']);
$modal_step->setOption('observeChanges', true);

//Add buttons to modal for next and previous actions.
$action = new \atk4\ui\View(['ui' => 'buttons']);
$prev_action = new \atk4\ui\Button(['Prev', 'labeled', 'icon' => 'left arrow']);
$next_action = new \atk4\ui\Button(['Next', 'iconRight' => 'right arrow']);

$action->add($prev_action);
$action->add($next_action);

$modal_step->addButtonAction($action);

//Set modal functionality. Will changes content according to page being displayed.
$modal_step->set(function ($modal) use ($modal_step, $session, $prev_action, $next_action) {
    $page = $session->recall('page', 1);
    $success = $session->recall('success', false);
    if (isset($_GET['move'])) {
        if ($_GET['move'] === 'next' && $success) {
            $page++;
        }
        if ($_GET['move'] === 'prev' && $page > 1) {
            $page--;
        }
        $session->memorize('success', false);
    } elseif ($page === 2) {
    } else {
        $page = 1;
    }
    $session->memorize('page', $page);
    if ($page === 1) {
        $modal->add('Message')->set('Thanks for choosing us. We will be asking some questions along the way.');
        $session->memorize('success', true);
        $modal->js(true, $prev_action->js(true)->show());
        $modal->js(true, $next_action->js(true)->show());
        $modal->js(true, $prev_action->js()->addClass('disabled'));
        $modal->js(true, $next_action->js(true)->removeClass('disabled'));
    } elseif ($page === 2) {
        $a = [];
        $m_register = new \atk4\data\Model(new \atk4\data\Persistence\Array_($a));
        $m_register->addField('name', ['caption' => 'Please enter your name (John)']);

        $f = $modal->add(new \atk4\ui\Form(['segment' => true]));
        $f->setModel($m_register);

        $f->onSubmit(function ($f) use ($next_action, $session) {
            if ($f->model['name'] != 'John') {
                return $f->error('name', 'Your name is not John! It is "'.$f->model['name'].'". It should be John. Pleeease!');
            } else {
                $session->memorize('success', true);
                $session->memorize('name', $f->model['name']);
                $js[] = $f->success('Thank you, '.$f->model['name'].' you can go on!');
                $js[] = $next_action->js()->removeClass('disabled');

                return $js;
            }
        });
        $modal->js(true, $prev_action->js()->removeClass('disabled'));
        $modal->js(true, $next_action->js(true)->addClass('disabled'));
    } elseif ($page === 3) {
        $name = $session->recall('name');
        $modal->add('Message')->set("Thank you ${name} for visiting us! We will be in touch");
        $session->memorize('success', true);
        $modal->js(true, $prev_action->js(true)->hide());
        $modal->js(true, $next_action->js(true)->hide());
    }
    $modal_step->js(true)->modal('refresh');
});

//Bind next action to modal next button.
$next_action->on('click', $modal_step->js()->atkReloadView(
    ['uri' => $modal_step->cb->getJSURL(), 'uri_options' => ['move' => 'next']]
));

//Bin prev action to modal previous button.
$prev_action->on('click', $modal_step->js()->atkReloadView(
    ['uri' => $modal_step->cb->getJSURL(), 'uri_options' => ['move' => 'prev']]
));

//Bind display modal to page display button.
$menu_bar = $app->add(['View', 'ui' => 'buttons']);
$b = $menu_bar->add('Button')->set('Multi Step Modal');
$b->on('click', $modal_step->show());
