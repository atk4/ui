<?php

declare(strict_types=1);

namespace atk4\ui\demo;

/** @var \atk4\ui\App $app */
require_once __DIR__ . '/../atk-init.php';

$wizard = \atk4\ui\Wizard::addTo($app);

$wizard->addStep('User Interface', function ($page) {
    $t = \atk4\ui\Text::addTo($page);
    $t->addParagraph(
        <<< 'EOF'
Agile Toolkit is a "Low Code Framework" written in PHP. It is designed to simplify all aspects of web application creation:
EOF
    );
    $t->addHTML(
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

    $t->addHTML(
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

    Demo::addTo($page)->setCode('\atk4\ui\Button::addTo($app, [\'Hello from the button!\']);');
});

$wizard->addStep('Interactivity', function ($page) {
    $t = \atk4\ui\Text::addTo($page);
    $t->addParagraph(
        <<< 'EOF'
PHP is a server-side language. That prompted us to implement server-side UI actions. They are very easy to define -
no need to create any routes or custom routines, simply define a PHP closure like this:
EOF
    );

    Demo::addTo($page)->setCode(
        <<<'CODE'
$button = \atk4\ui\Button::addTo($app, ["Click for the greeting!"]);
$button->on('click', function() {
    return 'Hello World!';
});

CODE
    );

    $t = \atk4\ui\Text::addTo($page);
    $t->addParagraph(
        <<< 'EOF'
A component of Agile Toolkit (callback) enables seamless communication between the frontend components (which are often
written in VueJS) and the backend. We also support seamless reloading of any UI widget:
EOF
    );

    Demo::addTo($page)->setCode(
        <<<'CODE'

$seg = \atk4\ui\View::addTo($app, ['ui'=>'segment']);

\atk4\ui\Text::addTo($seg)->set('Number of buttons: ');

$paginator = \atk4\ui\Paginator::addTo($seg, [
    'total'=>5,
    'reload'=>$seg,
    'urlTrigger'=>'count'
]);

\atk4\ui\View::addTo($seg, ['ui'=>'divider']);

for($i=1; $i <= ($_GET['count'] ?? 1); $i++) {
    \atk4\ui\Button::addTo($seg, [$i]);
}

CODE
    );

    $t = \atk4\ui\Text::addTo($page);
    $t->addParagraph(
        <<< 'EOF'
This demo also shows you how to create composite views. The '$seg' above contains text, paginator, divider and some
buttons. Interestingly, Paginator view also consists of buttons and Agile Toolkit renders everything reliably.
EOF
    );
});

$wizard->addStep('Business Model', function ($page) {
    $t = \atk4\ui\Text::addTo($page);
    $t->addParagraph(
        <<< 'EOF'
One major benefit of Server Side Rendered applications is ability to directly interact with data. In other applications
you may need to manually process data but in Agile Toolkit we use data mapping framework.
EOF
    );

    Demo::addTo($page)->setCode(
        <<<'CODE'
/* Showing Class definition.
class DemoInvoice extends \atk4\data\Model {
    public $title_field = 'reference';
    function init(): void {
        parent::init();

        $this->addField('reference');
        $this->addField('date', ['type'=>'date']);
    }
}
*/

session_start();
$session = new \atk4\data\Persistence\Array_($_SESSION['x']);

$form = \atk4\ui\Form::addTo($app);
$form->setModel(new DemoInvoice($session))
    ->tryLoad(1);

\atk4\ui\View::addTo($app, ['ui'=>'divider']);
\atk4\ui\Button::addTo($app, ['Refresh', 'icon'=>'refresh'])
    ->on('click', $app->jsReload());

CODE
    );

    $t = \atk4\ui\Text::addTo($page);
    $t->addParagraph(
        <<< 'EOF'
This code shows you a combination of 3 objects:
EOF
    );
    $t->addHTML(
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
    $t = \atk4\ui\Text::addTo($page);
    $t->addParagraph(
        <<< 'EOF'
Once your model is defined, it can be re-used later with any generic view:
EOF
    );

    Demo::addTo($page)->setCode(
        <<<'CODE'
session_start();
$session = new \atk4\data\Persistence\Array_($_SESSION['x']);

$model = new DemoInvoice($session);
$model->tryLoad(1);
\atk4\ui\Card::addTo($app)->setModel($model, ['date']);

CODE
    );

    $t = \atk4\ui\Text::addTo($page);
    $t->addParagraph(
        <<< 'EOF'
Re-use of your Business Model code, generic and interactive views and principles of composition and a simple PHP
code offers a most efficient way of constructing Web Applications.
EOF
    );
});

$wizard->addFinish(function ($page) use ($wizard) {
    PromotionText::addTo($page);
    \atk4\ui\Button::addTo($wizard, ['Exit demo', 'primary', 'icon' => 'left arrow'], ['Left'])
        ->link('/demos/index.php');
});
