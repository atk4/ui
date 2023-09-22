<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Data\Model;
use Atk4\Ui\Button;
use Atk4\Ui\Card;
use Atk4\Ui\CardDeck;
use Atk4\Ui\Crud;
use Atk4\Ui\Form;
use Atk4\Ui\Header;
use Atk4\Ui\Menu;
use Atk4\Ui\Text;
use Atk4\Ui\UserAction\ExecutorFactory;
use Atk4\Ui\View;
use Atk4\Ui\Wizard;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

$wizard = Wizard::addTo($app);

$wizard->addStep('Define User Action', static function (Wizard $page) {
    Header::addTo($page, ['What are User Actions?']);

    $t = Text::addTo($page);
    $t->addParagraph(<<<'EOF'
        Since the early version ATK UI was about building generic UI capable of automatically read information about
        model Fields and visualising them correctly. Version 2.0 introduces support for "Actions" which can be declared
        in Data layer and can use generic UI for visualising and triggering. Models of Agile Data has always supported 3
        basic actions: "save" (for new and existing records) and "delete". Historically any other interaction required
        tinkering with UI layer. Now ATK implements a generic support for arbitrary actions and then re-implements
        "save", "delete" and "add" on top.
        EOF);

    $t->addParagraph(<<<'EOF'
        This enables developer to easily add more actions in the Data layers and have the rest of ATK recognise
        and respect those actions. Actions can be added into the model just like you are adding fields:
        EOF);

    Demo::addTo($page)->setCodeAndCall(static function (View $owner) {
        $country = new Country($owner->getApp()->db);

        $country->addUserAction('send_message');
    });

    $t = Text::addTo($page);
    $t->addParagraph(<<<'EOF'
        Once defied - actions will be visualised in the Form, Grid, Crud and CardDeck. Additionally add-ons will recognise
        your actions - for example 'Login' add-on introduces ACL system capable of enabling/disabling fields or actions
        on per-user basis.
        EOF);

    $t->addParagraph(<<<'EOF'
        Any actions you define will automatically appear in the UI. This is consistent with your field definitions. You can
        also "disable" or mark actions as "system". When action is executed, the response will appear to the user as a
        toast message, but this can also be customised.
        EOF);

    Demo::addTo($page)->setCodeAndCall(static function (View $owner) {
        $country = new Country($owner->getApp()->db);

        $country->addUserAction('send_message', static function (Country $entity) {
            return 'Sent to ' . $entity->get($entity->fieldName()->name);
        });
        $country = $country->loadAny();

        $card = Card::addTo($owner);
        $card->setModel($country, [$country->fieldName()->iso]);
        $card->addClickAction($country->getModel()->getUserAction('send_message'));
    });
});

$wizard->addStep('UI Integration', static function (Wizard $page) {
    $t = Text::addTo($page);
    $t->addParagraph(<<<'EOF'
        Agile UI introduces a new set of views called "User Action Executors". Their job is to recognise all that meta-information
        that you have specified for the user action and requesting it from the user. "edit" user action is defined for models by default
        and you can trigger it on button-click with a very simple code:
        EOF);

    Demo::addTo($page)->setCodeAndCall(static function (View $owner) {
        $country = new Country($owner->getApp()->db);
        $country = $country->loadAny();

        Button::addTo($owner, ['Edit some country'])
            ->on('click', $country->getUserAction('edit'));
    });

    $t = Text::addTo($page);
    $t->addParagraph(<<<'EOF'
        It is not only the button, but any view can have "User Action" passed as a second step of the on() call. Here the user action
        is executed when you click on "World" menu item:
        EOF);

    Demo::addTo($page)->setCodeAndCall(static function (View $owner) {
        $country = new Country($owner->getApp()->db);
        $country = $country->loadAny();

        $menu = Menu::addTo($owner);
        $menu->addItem('Hello');
        $menu->addItem('World', $country->getUserAction('edit'));
    });
});

$wizard->addStep('Arguments', static function (Wizard $page) {
    $t = Text::addTo($page);
    $t->addParagraph(<<<'EOF'
        Next demo defines an user action that requires arguments. You can specify arguments when the user action is invoked, but if not
        defined - user will be asked to supply an argument. User action will automatically validate argument types and it uses
        same type system as fields.
        EOF);

    Demo::addTo($page)->setCodeAndCall(static function (View $owner) {
        $model = new Model($owner->getApp()->db, ['table' => 'test']);

        $model->addUserAction('greet', [
            'appliesTo' => Model\UserAction::APPLIES_TO_NO_RECORDS,
            'args' => [
                'name' => [
                    'type' => 'string',
                ],
            ],
            'callback' => static function (Model $model, string $name) {
                return 'Hi ' . $name;
            },
        ]);

        $model->addUserAction('ask_age', [
            'appliesTo' => Model\UserAction::APPLIES_TO_NO_RECORDS,
            'args' => [
                'age' => [
                    'type' => 'integer',
                    'required' => true,
                ],
            ],
            'callback' => static function (Model $model, int $age) {
                return 'Age is ' . $age;
            },
        ]);

        Form\Control\Line::addTo($owner, [
            'action' => $model->getUserAction('greet'),
        ]);

        View::addTo($owner, ['ui' => 'divider']);

        Button::addTo($owner, ['Ask Age'])
            ->on('click', $model->getUserAction('ask_age'));
    });
});

$wizard->addStep('Crud integration', static function (Wizard $page) {
    $t = Text::addTo($page);
    $t->addParagraph(<<<'EOF'
        Compared to 1.x versions Crud implementation has became much more lightweight, however you retain all the same
        functionality and more. Next example shows how you can disable user action (add) entirely, or on per-row basis (delete)
        and how you could add your own action with a custom trigger button and even a preview.
        EOF);

    Demo::addTo($page)->setCodeAndCall(static function (View $owner) {
        $country = new Country($owner->getApp()->db);
        $country->getUserAction('add')->enabled = false;
        $country->getUserAction('delete')->enabled = static function (Country $m) {
            return $m->id % 2 === 0;
        };
        $country->addUserAction('mail', [
            'appliesTo' => Model\UserAction::APPLIES_TO_SINGLE_RECORD,
            'preview' => static function (Country $model) {
                return 'Here is email preview for ' . $model->name;
            },
            'callback' => static function (Country $model) {
                return 'Email sent to ' . $model->name;
            },
            'description' => 'Email testing',
        ]);

        // register a trigger for mail action in Crud
        $owner->getExecutorFactory()->registerTrigger(
            ExecutorFactory::TABLE_BUTTON,
            [Button::class, null, 'icon' => 'blue mail'],
            $country->getUserAction('mail')
        );
        Crud::addTo($owner, ['ipp' => 5])
            ->setModel($country, [$country->fieldName()->name, $country->fieldName()->iso]);
    });

    Demo::addTo($page, ['leftWidth' => 6, 'rightWidth' => 10])->setCodeAndCall(static function (View $owner) {
        $model = new Stat($owner->getApp()->db);

        $model->addUserAction('mail', [
            'fields' => [$model->fieldName()->currency],
            'appliesTo' => Model\UserAction::APPLIES_TO_SINGLE_RECORD,
            'callback' => static function (Stat $model) {
                return 'Email sent in ' . $model->currency . ' currency';
            },
            'description' => 'Email testing',
        ]);

        CardDeck::addTo($owner)
            ->setModel(
                $model,
                [$model->fieldName()->description]
            );
    });
});

$wizard->addFinish(static function (Wizard $page) {
    PromotionText::addTo($page);
    Button::addTo($page, ['Exit demo', 'class.primary' => true, 'icon' => 'left arrow'], ['Left'])
        ->link('/demos/index.php');
});
