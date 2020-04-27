<?php

require_once __DIR__ . '/../atk-init.php';
require_once __DIR__ . '/../_includes/Demo.php';
require_once __DIR__ . '/../_includes/PromotionText.php';

// require for embeded coded
$app->db = $db;

/** @var \atk4\ui\View $wizard */
$wizard = $app->add('Wizard');
$app->stickyGet($wizard->name);

$wizard->addStep('Define User Action', function ($page) {
    // @var \atk4\ui\Text $t

    \atk4\ui\Header::addTo($page, ['What are Actions?']);

    $t = $page->add('Text');
    $t->addParagraph(
        <<< 'EOF'
Since the early version ATK UI was about building generic UI capable of automatically read information about 
model Fields and visualising them correctly. Version 2.0 introduces support for "Actions" which can be declared
in Data layer and can use generic UI for visualising and triggering. Models of Agile Data has always supported 3
basic actions: "save" (for new and existing records) and "delete". Historically any other interaction required
tinkering with UI layer. Now ATK implements a generic support for arbitrary actions and then re-implements
"save", "delete" and "add" on top.
EOF
    );

    $t->addParagraph(
        <<< 'EOF'
This enables developer to easily add more actions in the Data layers and have the rest of ATK recognise
and respect those actions. Actions can be added into the model just like you are adding fields:
EOF
    );

    $page->add(new Demo())->setCode(
        <<<'CODE'
$country = new CountryLock($app->db);

$country->addAction('send_message');
CODE
    );

    $t = $page->add('Text');
    $t->addParagraph(
        <<< 'EOF'
Once defied - actions will be visualised in the Form, Grid, CRUD and CardDeck. Additionally add-ons will recognise
your actions - for example 'Login' add-on introduces ACL system capable of enabling/disabling fields or actions
on per-user basis.
EOF
    );

    $t->addParagraph(
        <<< 'EOF'
Any actions you define will automatically appear in the UI. This is consistent with your field definitions. You can
also "disable" or mark actions as "system". When action is executed, the response will appear to the user as a 
toast message, but this can also be customised.
EOF
    );

    $page->add(new Demo())->setCode(
        <<<'CODE'

$country = new CountryLock($app->db);

$country->addAction('send_message', function() { 
    return 'sent'; 
});
$country->tryLoadAny();

$card = $app->add('Card');
$card->setModel($country, ['iso']);
$card->addClickAction($country->getAction('send_message'));

CODE
    );
});

$wizard->addStep('UI Integration', function ($page) {
    /** @var \atk4\ui\Text $t */
    $t = $page->add('Text');
    $t->addParagraph(
        <<< 'EOF'
Agile UI introduces a new set of views called "Action Executors". Their job is to recognise all that meta-information
that you have specified for the action and requesting it from the user. "edit" action is defined for models by default
and you can trigger it on button-click with a very simple code:
EOF
    );

    $page->add(new Demo())->setCode(
        <<<'CODE'
$country = new CountryLock($app->db);
$country->loadAny();

$app->add(['Button', 'Edit some country'])
    ->on('click', $country->getAction('edit'));
CODE
    );

    $t = $page->add('Text');
    $t->addParagraph(
        <<< 'EOF'
It is not only the button, but any view can have "Action" passed as a second step of the on() call. Here the action
is executed when you click on "World" menu item:
EOF
    );

    $page->add(new Demo())->setCode(
        <<<'CODE'
$country = new CountryLock($app->db);
$country->loadAny();

$menu = $app->add('Menu');
$menu->addItem('Hello');
$menu->addItem('World', $country->getAction('edit'));
CODE
    );
});

$wizard->addStep('Arguments', function ($page) {
    $t = $page->add('Text');
    $t->addParagraph(
        <<< 'EOF'
Next demo defines an action that requires arguments. You can specify action when the action is invoked, but if not
defined - user will be asked to supply an argument. Action will automatically validate argument types and it uses
same type system as fields.
EOF
    );

    $page->add(new Demo())->setCode(
        <<<'CODE'
$model = new \atk4\data\Model($app->db, 'test');

$model->addAction('greet', [
    'scope' => \atk4\data\UserAction\Generic::NO_RECORDS,
    'args'=> [
        'age'=>[
            'type'=>'string'
        ]
    ], 
    'callback'=>function ($m, $name) {
        return 'Hi '.$name;
    },
    'ui' => ['executor' => \atk4\ui\ActionExecutor\jsUserAction::class],
]);

$model->addAction('ask_age', [
    'scope' => \atk4\data\UserAction\Generic::NO_RECORDS,
    'args'=> [
        'age'=>[
            'type'=>'integer', 
            'required' => true
        ]
    ], 
    'callback'=>function ($m, $age) {
        return 'Age is '.$age;
    }
]);

$app->add(new \atk4\ui\FormField\Line([
    'action' => $model->getAction('greet'),
]));

$app->add(['ui'=>'divider']);

$app->add(['Button', 'Ask Age'])
    ->on('click', $model->getAction('ask_age'));
CODE
    );
});

/*
$wizard->addStep('More Ways', function ($page) {
    $page->add(new Demo(['left_width'=>5, 'right_width'=>11]))->setCode(
        <<<'CODE'
$m = new Stat($app->db);
$m->addAction('mail', [
    'fields'      => ['currency_field'],
    'scope'       => \atk4\data\UserAction\Generic::SINGLE_RECORD,
    'callback'    => function() { return 'testing'; },
    'description' => 'Email testing',
]);
$app->add('CardDeck')
    ->setModel(
        $m,
        ['description']
    );
CODE
    );
});
*/

$wizard->addStep('CRUD integration', function ($page) {
    $t = $page->add('Text');
    $t->addParagraph(
        <<< 'EOF'
Compared to 1.x versions CRUD implementation has became much more lightweight, however you retain all the same
functionality and more. Next example shows how you can disable action (add) entirely, or on per-row basis (delete)
and how you could add your own action with a custom trigger button and even a preview.
EOF
    );

    $page->add(new Demo())->setCode(
        <<<'CODE'
$country = new CountryLock($app->db);
$country->getAction('add')->enabled = false;
$country->getAction('delete')->enabled = function() { return rand(1,2)>1; };
$country->addAction('mail', [
    'scope'       => \atk4\data\UserAction\Generic::SINGLE_RECORD,
    'preview'    => function($m) { return 'here is email preview for '.$m['name']; },
    'callback'    => function($m) { return 'email sent to '.$m['name']; },
    'description' => 'Email testing',
    'ui'       => ['icon'=>'mail', 'button'=>[null, 'icon'=>'green mail']],
]);
$crud = $app->add(['CRUD', 'ipp'=>5]);
$crud->setModel($country, ['name','iso']);
CODE
    );
});

$wizard->addFinish(function ($page) use ($wizard, $app) {
    PromotionText::addTo($page);
    \atk4\ui\Button::addTo($wizard, ['Exit demo', 'primary', 'icon' => 'left arrow'], ['Left'])
        ->link('/demos/index.php');
});
