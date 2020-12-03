<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\View;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

$wizard = \Atk4\Ui\Wizard::addTo($app);
$app->stickyGet($wizard->name);

$wizard->addStep('Define User Action', function ($page) {
    \Atk4\Ui\Header::addTo($page, ['What are User Actions?']);

    $t = \Atk4\Ui\Text::addTo($page);
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

    $page->add(new Demo())->setCodeAndCall(function (View $owner) {
        $country = new \Atk4\Ui\Demos\CountryLock($owner->getApp()->db);

        $country->addUserAction('send_message');
    });

    $t = \Atk4\Ui\Text::addTo($page);
    $t->addParagraph(
        <<< 'EOF'
            Once defied - actions will be visualised in the Form, Grid, Crud and CardDeck. Additionally add-ons will recognise
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

    $page->add(new Demo())->setCodeAndCall(function (View $owner) {
        $country = new \Atk4\Ui\Demos\CountryLock($owner->getApp()->db);

        $country->addUserAction('send_message', function () {
            return 'sent';
        });
        $country->tryLoadAny();

        $card = \Atk4\Ui\Card::addTo($owner);
        $card->setModel($country, ['iso']);
        $card->addClickAction($country->getUserAction('send_message'));
    });
});

$wizard->addStep('UI Integration', function ($page) {
    $t = \Atk4\Ui\Text::addTo($page);
    $t->addParagraph(
        <<< 'EOF'
            Agile UI introduces a new set of views called "User Action Executors". Their job is to recognise all that meta-information
            that you have specified for the user action and requesting it from the user. "edit" user action is defined for models by default
            and you can trigger it on button-click with a very simple code:
            EOF
    );

    $page->add(new Demo())->setCodeAndCall(function (View $owner) {
        $country = new \Atk4\Ui\Demos\CountryLock($owner->getApp()->db);
        $country->loadAny();

        \Atk4\Ui\Button::addTo($owner, ['Edit some country'])
            ->on('click', $country->getUserAction('edit'));
    });

    $t = \Atk4\Ui\Text::addTo($page);
    $t->addParagraph(
        <<< 'EOF'
            It is not only the button, but any view can have "User Action" passed as a second step of the on() call. Here the user action
            is executed when you click on "World" menu item:
            EOF
    );

    $page->add(new Demo())->setCodeAndCall(function (View $owner) {
        $country = new \Atk4\Ui\Demos\CountryLock($owner->getApp()->db);
        $country->loadAny();

        $menu = \Atk4\Ui\Menu::addTo($owner);
        $menu->addItem('Hello');
        $menu->addItem('World', $country->getUserAction('edit'));
    });
});

$wizard->addStep('Arguments', function ($page) {
    $t = \Atk4\Ui\Text::addTo($page);
    $t->addParagraph(
        <<< 'EOF'
            Next demo defines an user action that requires arguments. You can specify arguments when the user action is invoked, but if not
            defined - user will be asked to supply an argument. User action will automatically validate argument types and it uses
            same type system as fields.
            EOF
    );

    $page->add(new Demo())->setCodeAndCall(function (View $owner) {
        $model = new \Atk4\Data\Model($owner->getApp()->db, 'test');

        $model->addUserAction('greet', [
            'appliesTo' => \Atk4\Data\Model\UserAction::APPLIES_TO_NO_RECORDS,
            'args' => [
                'age' => [
                    'type' => 'string',
                ],
            ],
            'callback' => function ($model, $name) {
                return 'Hi ' . $name;
            },
            'ui' => ['executor' => [\Atk4\Ui\UserAction\JsCallbackExecutor::class]],
        ]);

        $model->addUserAction('ask_age', [
            'appliesTo' => \Atk4\Data\Model\UserAction::APPLIES_TO_NO_RECORDS,
            'args' => [
                'age' => [
                    'type' => 'integer',
                    'required' => true,
                ],
            ],
            'callback' => function ($model, $age) {
                return 'Age is ' . $age;
            },
        ]);

        $owner->add(new \Atk4\Ui\Form\Control\Line([
            'action' => $model->getUserAction('greet'),
        ]));

        \Atk4\Ui\View::addTo($owner, ['ui' => 'divider']);

        \Atk4\Ui\Button::addTo($owner, ['Ask Age'])
            ->on('click', $model->getUserAction('ask_age'));
    });
});

/*
$wizard->addStep('More Ways', function ($page) {
    $page->add(new Demo(['left_width' => 5, 'right_width' => 11]))->setCodeAndCall(function (View $owner) {
        $model = new Stat($owner->getApp()->db);
        $model->addUserAction('mail', [
            'fields' => ['currency_field'],
            'appliesTo' => \Atk4\Data\Model\UserAction::APPLIES_TO_SINGLE_RECORD,
            'callback' => function() { return 'testing'; },
            'description' => 'Email testing',
        ]);
        $owner->add('CardDeck')
            ->setModel(
                $model,
                ['description']
            );
    });
});
*/

$wizard->addStep('Crud integration', function ($page) {
    $t = \Atk4\Ui\Text::addTo($page);
    $t->addParagraph(
        <<< 'EOF'
            Compared to 1.x versions Crud implementation has became much more lightweight, however you retain all the same
            functionality and more. Next example shows how you can disable user action (add) entirely, or on per-row basis (delete)
            and how you could add your own action with a custom trigger button and even a preview.
            EOF
    );

    $page->add(new Demo())->setCodeAndCall(function (View $owner) {
        $country = new \Atk4\Ui\Demos\CountryLock($owner->getApp()->db);
        $country->getUserAction('add')->enabled = false;
        $country->getUserAction('delete')->enabled = function () { return random_int(1, 2) > 1; };
        $country->addUserAction('mail', [
            'appliesTo' => \Atk4\Data\Model\UserAction::APPLIES_TO_SINGLE_RECORD,
            'preview' => function ($model) { return 'here is email preview for ' . $model->get('name'); },
            'callback' => function ($model) { return 'email sent to ' . $model->get('name'); },
            'description' => 'Email testing',
            'ui' => ['icon' => 'mail', 'button' => [null, 'icon' => 'green mail']],
        ]);

        \Atk4\Ui\Crud::addTo($owner, ['ipp' => 5])->setModel($country, ['name', 'iso']);
    });
});

$wizard->addFinish(function ($page) use ($wizard) {
    PromotionText::addTo($page);
    \Atk4\Ui\Button::addTo($wizard, ['Exit demo', 'primary', 'icon' => 'left arrow'], ['Left'])
        ->link('/demos/index.php');
});
