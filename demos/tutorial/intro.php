<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Data\Model;
use Atk4\Data\Persistence;
use Atk4\Ui\Button;
use Atk4\Ui\Card;
use Atk4\Ui\Form;
use Atk4\Ui\Header;
use Atk4\Ui\Js\JsToast;
use Atk4\Ui\Message;
use Atk4\Ui\Paginator;
use Atk4\Ui\Text;
use Atk4\Ui\View;
use Atk4\Ui\Wizard;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

$wizard = Wizard::addTo($app);

$wizard->addStep('User Interface', static function (Wizard $page) {
    $t = Text::addTo($page);
    $t->addParagraph(<<<'EOF'
        Agile Toolkit is a "Low Code Framework" written in PHP. It is designed to simplify all aspects of web application creation:
        EOF);
    $t->addHtml(<<<'EOF'
        <ul>
            <li>No front-end coding necessary (like JavaScript)</li>
            <li>No Database coding required (like SQL)</li>
            <li>No need for routing or worrying about POST/GET</li>
            <li>No need to manually create APIs</li>
            <li>No need to worry about CSS</li>
        </ul>
        EOF);

    $t->addParagraph('Your ATK code instead takes a more declarative approach. You work with things like:');

    $t->addHtml(<<<'EOF'
        <ul>
            <li>Models and fields</li>
            <li>Model User actions</li>
            <li>Relationships between models</li>
            <li>Pages and Widgets</li>
            <li>jsActions and Events</li>
        </ul>
        EOF);

    $t->addParagraph(<<<'EOF'
        Since 2017 our collection of built-in widgets, add-ons have grown significantly and today Agile Toolkit is a mature
        and production ready framework.
        EOF);

    $t->addParagraph('It all has started with a "Button" though:');

    Demo::addTo($page)->setCodeAndCall(static function (View $owner) {
        Button::addTo($owner, ['Hello from the button!']);
    });
});

$wizard->addStep('Interactivity', static function (Wizard $page) {
    $t = Text::addTo($page);
    $t->addParagraph(<<<'EOF'
        PHP is a server-side language. That prompted us to implement server-side UI actions. They are very easy to define -
        no need to create any routes or custom routines, simply define a PHP closure like this:
        EOF);

    Demo::addTo($page)->setCodeAndCall(static function (View $owner) {
        $button = Button::addTo($owner, ['Click for the greeting!']);
        $button->on('click', static function () {
            return 'Hello World!';
        });
    });

    $t = Text::addTo($page);
    $t->addParagraph(<<<'EOF'
        A component of Agile Toolkit (callback) enables seamless communication between the frontend components (which are often
        written in VueJS) and the backend. We also support seamless reloading of any UI widget:
        EOF);

    Demo::addTo($page)->setCodeAndCall(static function (View $owner) {
        $seg = View::addTo($owner, ['ui' => 'segment']);

        Text::addTo($seg)->set('Number of buttons: ');

        $paginator = Paginator::addTo($seg, [
            'total' => 5,
            'reload' => $seg,
            'urlTrigger' => 'count',
        ]);

        View::addTo($seg, ['ui' => 'divider']);

        $count = $seg->getApp()->tryGetRequestQueryParam('count') ?? 1;
        for ($i = 1; $i <= $count; ++$i) {
            Button::addTo($seg, [(string) $i]);
        }
    });

    $t = Text::addTo($page);
    $t->addParagraph(<<<'EOF'
        This demo also shows you how to create composite views. The '$seg' above contains text, paginator, divider and some
        buttons. Interestingly, Paginator view also consists of buttons and Agile Toolkit renders everything reliably.
        EOF);
});

$wizard->addStep('Business Model', static function (Wizard $page) {
    $t = Text::addTo($page);
    $t->addParagraph(<<<'EOF'
        One major benefit of Server Side Rendered applications is ability to directly interact with data. In other applications
        you may need to manually process data but in Agile Toolkit we use data mapping framework.
        EOF);

    Demo::addTo($page)->setCodeAndCall(static function (View $owner) {
        /* Showing Class definition.
        class DemoInvoice extends Model
        {
            public ?string $titleField = 'reference';

            protected function init(): void
            {
                parent::init();

                $this->addField('reference');
                $this->addField('date', ['type' => 'date']);
            }
        }
        */
        session_start();

        $model = new DemoInvoice(new Persistence\Array_($_SESSION['atk4_ui_intro_demo'] ?? []));
        $model->onHook(Model::HOOK_AFTER_SAVE, static function (Model $model) {
            $_SESSION['atk4_ui_intro_demo'][$model->getId()] = (clone $model->getModel())->addCondition($model->idField, $model->getId())->export(null, null, false)[$model->getId()];
        });

        Header::addTo($owner, ['Set invoice data:']);
        $form = Form::addTo($owner);

        $entity = $model->tryLoad(1);
        if ($entity === null) {
            // set default data
            $entity = $model->createEntity();
            $entity->setMulti([
                'id' => 1,
                'reference' => 'Inv-' . random_int(1000, 9999),
                'date' => new \DateTime(),
            ]);
            $entity->save();
        }
        $form->setModel($entity);

        $form->onSubmit(static function (Form $form) {
            $form->model->save();

            return new JsToast('Saved!');
        });

        View::addTo($owner, ['ui' => 'divider']);
    });

    $t = Text::addTo($page);
    $t->addParagraph(<<<'EOF'
        This code shows you a combination of 3 objects:
        EOF);
    $t->addHtml(<<<'EOF'
        <ul>
        <li>Form - a generic view that can display and handle any form</li>
        <li>Model - defines fields for a business object</li>
        <li>Persistence - creates a persistent storage location for the data</li>
        </ul>
        EOF);
    $t->addParagraph(<<<'EOF'
        All three are combined by "setModel()" function and that is consistent throughout all the views.
        EOF);
});

$wizard->addStep('Persistence', static function (Wizard $page) {
    $t = Text::addTo($page);
    $t->addParagraph(<<<'EOF'
        Once your model is defined, it can be re-used later with any generic view:
        EOF);

    Demo::addTo($page)->setCodeAndCall(static function (View $owner) {
        session_start();

        $model = new DemoInvoice(new Persistence\Array_($_SESSION['atk4_ui_intro_demo'] ?? []));
        $model->onHook(Model::HOOK_AFTER_SAVE, static function (Model $model) {
            $_SESSION['atk4_ui_intro_demo'][$model->getId()] = (clone $model->getModel())->addCondition($model->idField, $model->getId())->export(null, null, false)[$model->getId()];
        });

        Header::addTo($owner, ['Record display in Card View using model data.']);
        $model = $model->tryLoad(1);
        if ($model !== null) {
            Card::addTo($owner, ['useLabel' => true])
                ->setModel($model);
        } else {
            Message::addTo($owner, ['Empty record.']);
        }
    });

    $t = Text::addTo($page);
    $t->addParagraph(<<<'EOF'
        Re-use of your Business Model code, generic and interactive views and principles of composition and a simple PHP
        code offers a most efficient way of constructing Web Applications.
        EOF);
});

$wizard->addFinish(static function (Wizard $page) {
    PromotionText::addTo($page);
    Button::addTo($page, ['Exit demo', 'class.primary' => true, 'icon' => 'left arrow'], ['Left'])
        ->link('../');
});
