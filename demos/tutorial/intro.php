<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\Header;
use Atk4\Ui\JsToast;
use Atk4\Ui\Message;
use Atk4\Ui\View;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

$wizard = \Atk4\Ui\Wizard::addTo($app);

$wizard->addStep('User Interface', function ($page) {
    $t = \Atk4\Ui\Text::addTo($page);
    $t->addParagraph(
        <<< 'EOF'
            Agile Toolkit is a "Low Code Framework" written in PHP. It is designed to simplify all aspects of web application creation:
            EOF
    );
    $t->addHtml(
        <<< 'HTML'
            <ul>
                <li>No front-end coding necessary (like JavaScript)</li>
                <li>No Database coding required (like SQL)</li>
                <li>No need for routing or worrying about POST/GET</li>
                <li>No need to manually create APIs</li>
                <li>No need to worry about CSS</li>
            </ul>
            HTML
    );

    $t->addParagraph('Your ATK code instead takes a more declarative approach. You work with things like:');

    $t->addHtml(
        <<< 'HTML'
            <ul>
                <li>Models and fields</li>
                <li>Model User actions</li>
                <li>Relationships between models</li>
                <li>Pages and Widgets</li>
                <li>jsActions and Events</li>
            </ul>
            HTML
    );

    $t->addParagraph(
        <<< 'EOF'
            Since 2017 our collection of built-in widgets, add-ons have grown significantly and today Agile Toolkit is a mature
            and production ready framework.
            EOF
    );

    $t->addParagraph('It all has started with a "Button" though:');

    Demo::addTo($page)->setCodeAndCall(function (View $owner) {
        \Atk4\Ui\Button::addTo($owner, ['Hello from the button!']);
    });
});

$wizard->addStep('Interactivity', function ($page) {
    $t = \Atk4\Ui\Text::addTo($page);
    $t->addParagraph(
        <<< 'EOF'
            PHP is a server-side language. That prompted us to implement server-side UI actions. They are very easy to define -
            no need to create any routes or custom routines, simply define a PHP closure like this:
            EOF
    );

    Demo::addTo($page)->setCodeAndCall(function (View $owner) {
        $button = \Atk4\Ui\Button::addTo($owner, ['Click for the greeting!']);
        $button->on('click', function () {
            return 'Hello World!';
        });
    });

    $t = \Atk4\Ui\Text::addTo($page);
    $t->addParagraph(
        <<< 'EOF'
            A component of Agile Toolkit (callback) enables seamless communication between the frontend components (which are often
            written in VueJS) and the backend. We also support seamless reloading of any UI widget:
            EOF
    );

    Demo::addTo($page)->setCodeAndCall(function (View $owner) {
        $seg = \Atk4\Ui\View::addTo($owner, ['ui' => 'segment']);

        \Atk4\Ui\Text::addTo($seg)->set('Number of buttons: ');

        $paginator = \Atk4\Ui\Paginator::addTo($seg, [
            'total' => 5,
            'reload' => $seg,
            'urlTrigger' => 'count',
        ]);

        \Atk4\Ui\View::addTo($seg, ['ui' => 'divider']);

        for ($i = 1; $i <= ($_GET['count'] ?? 1); ++$i) {
            \Atk4\Ui\Button::addTo($seg, [$i]);
        }
    });

    $t = \Atk4\Ui\Text::addTo($page);
    $t->addParagraph(
        <<< 'EOF'
            This demo also shows you how to create composite views. The '$seg' above contains text, paginator, divider and some
            buttons. Interestingly, Paginator view also consists of buttons and Agile Toolkit renders everything reliably.
            EOF
    );
});

$wizard->addStep('Business Model', function ($page) {
    $t = \Atk4\Ui\Text::addTo($page);
    $t->addParagraph(
        <<< 'EOF'
            One major benefit of Server Side Rendered applications is ability to directly interact with data. In other applications
            you may need to manually process data but in Agile Toolkit we use data mapping framework.
            EOF
    );

    Demo::addTo($page)->setCodeAndCall(function (View $owner) {
        /* Showing Class definition.
        class DemoInvoice extends \Atk4\Data\Model
        {
            public $title_field = 'reference';

            protected function init(): void
            {
                parent::init();

                $this->addField('reference');
                $this->addField('date', ['type' => 'date']);
            }
        }
        */
        session_start();

        $model = new \Atk4\Ui\Demos\DemoInvoice(new \Atk4\Data\Persistence\Array_($_SESSION['x'] ?? []), ['dateFormat' => $owner->getApp()->ui_persistence->date_format]);
        $model->onHook(\Atk4\Data\Model::HOOK_AFTER_SAVE, function ($model) {
            $_SESSION['x'][$model->getId()] = $model->get();
        });

        Header::addTo($owner, ['Set invoice data:']);
        $form = \Atk4\Ui\Form::addTo($owner);
        $form->setModel($model)->tryLoad(1);

        if (!$model->loaded()) {
            // set default data
            $model->setMulti([
                'id' => 1,
                'reference' => 'Inv-' . random_int(1000, 9999),
                'date' => date($owner->getApp()->ui_persistence->date_format),
            ]);
            $model->save();
        }

        $form->onSubmit(function ($f) {
            $f->model->save();

            return new JsToast('Saved!');
        });

        \Atk4\Ui\View::addTo($owner, ['ui' => 'divider']);
    });

    $t = \Atk4\Ui\Text::addTo($page);
    $t->addParagraph(
        <<< 'EOF'
            This code shows you a combination of 3 objects:
            EOF
    );
    $t->addHtml(
        <<< 'HTML'
            <ul>
            <li>Form - a generic view that can display and handle any form</li>
            <li>Model - defines fields for a business object</li>
            <li>Persistence - creates a persistent storage location for the data</li>
            </ul>
            HTML
    );
    $t->addParagraph(
        <<< 'EOF'
            All three are combined by "setModel()" function and that is consistent throughout all the views.
            EOF
    );
});

$wizard->addStep('Persistence', function ($page) {
    $t = \Atk4\Ui\Text::addTo($page);
    $t->addParagraph(
        <<< 'EOF'
            Once your model is defined, it can be re-used later with any generic view:
            EOF
    );

    Demo::addTo($page)->setCodeAndCall(function (View $owner) {
        session_start();

        $model = new \Atk4\Ui\Demos\DemoInvoice(new \Atk4\Data\Persistence\Array_($_SESSION['x'] ?? []), ['dateFormat' => $owner->getApp()->ui_persistence->date_format]);
        $model->onHook(\Atk4\Data\Model::HOOK_AFTER_SAVE, function ($model) {
            $_SESSION['x'][$model->getId()] = $model->get();
        });

        Header::addTo($owner, ['Record display in Card View using model data.']);
        $model->tryLoad(1);
        if ($model->loaded()) {
            \Atk4\Ui\Card::addTo($owner, ['useLabel' => true])->setModel($model);
        } else {
            Message::addTo($owner, ['Empty record.']);
        }
    });

    $t = \Atk4\Ui\Text::addTo($page);
    $t->addParagraph(
        <<< 'EOF'
            Re-use of your Business Model code, generic and interactive views and principles of composition and a simple PHP
            code offers a most efficient way of constructing Web Applications.
            EOF
    );
});

$wizard->addFinish(function ($page) use ($wizard) {
    PromotionText::addTo($page);
    \Atk4\Ui\Button::addTo($wizard, ['Exit demo', 'primary', 'icon' => 'left arrow'], ['Left'])
        ->link('/demos/index.php');
});
