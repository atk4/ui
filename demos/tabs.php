<?php
/**
 * Demonstrates how to use tabs.
 */
require_once __DIR__ . '/init.php';

$t = \atk4\ui\Tabs::addTo($app);

// static tab
\atk4\ui\HelloWorld::addTo($t->addTab('Hello'));
$tab = $t->addTab('Static Tab');
\atk4\ui\Message::addTo($tab, ['Content of this tab will refresh only if you reload entire page']);
\atk4\ui\LoremIpsum::addTo($tab);

// set the default active tab
$t->addTab('Default Active Tab', function ($tab) {
    \atk4\ui\Message::addTo($tab, ['This is the active tab by default']);
})->setActive();

// dynamic tab
$t->addTab('Dynamic Lorem Ipsum', function ($tab) {
    \atk4\ui\Message::addTo($tab, ['Every time you come to this tab, you will see a different text']);
    \atk4\ui\LoremIpsum::addTo($tab, ['size' => 2]);
});

// modal tab
$t->addTab('Modal popup', function ($tab) {
    \atk4\ui\Button::addTo($tab, ['Load Lorem'])->on('click', \atk4\ui\Modal::addTo($tab)->set(function ($p) {
        \atk4\ui\LoremIpsum::addTo($p, ['size' => 2]);
    })->show());
});

// dynamic tab
$t->addTab('Dynamic Form', function ($tab) {
    \atk4\ui\Message::addTo($tab, ['It takes 2 seconds for this tab to load', 'warning']);
    sleep(2);
    $m_register = new \atk4\data\Model(new \atk4\data\Persistence\Array_($a));
    $m_register->addField('name', ['caption' => 'Please enter your name (John)']);

    $f = \atk4\ui\Form::addTo($tab, ['segment' => true]);
    $f->setModel($m_register);
    $f->onSubmit(function ($f) {
        if ($f->model['name'] != 'John') {
            return $f->error('name', 'Your name is not John! It is "' . $f->model['name'] . '". It should be John. Pleeease!');
        }
    });
});

$t->addTabURL('Any other page', $app->url(['index']));
