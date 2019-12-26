<?php

include 'init.php';

/** @var \atk4\ui\Wizard $wizard */
$wizard = $app->add('Wizard');

$wizard->addStep('User Interface', function ($page) {
    /** @var \atk4\ui\Text $t */
    $t = $page->add('Text');
    $t->addParagraph(<<< 'EOF'
Agile Toolkit is a "Low Code Framework" written in PHP. It is designed to simplify all aspects of web application creation:
EOF
);
    $t->addHTML(<<< 'HTML'
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

    $t->addHTML(<<< 'HTML'
<ul>
    <li>Models and fields</li>
    <li>Model User actions</li>
    <li>Relationships between models</li>
    <li>Pages and Widgets</li>
    <li>jsActions and Events</li>
</ul>

HTML
);

    $t->addParagraph(<<< 'EOF'
Since 2017 our collection of built-in widgets, add-ons have grown significantly and today Agile Toolkit is a mature
and production ready framework.
EOF
);

    $t->addParagraph('It all has started with a "Button" though:');

    $page->add(new Demo())->setCode('$app->add(["Button", "Hello from the button!"]);');
});

$wizard->addStep('Interactivity', function ($page) {

    /** @var \atk4\ui\Text $t */
    $t = $page->add('Text');
    $t->addParagraph(<<< 'EOF'
PHP is a server-side language. That prompted us to implement server-side UI actions. They are very easy to define -
no need to create any routes or custom routines, simply define a PHP closure like this:
EOF
);

    $page->add(new Demo())->setCode(<<<'CODE'
$button = $app->add(['Button', "Click for the greeting!"]);
$button->on('click', function() {
    return 'Hello World! rand='.rand(1,100);
});

CODE
);

    $t = $page->add('Text');
    $t->addParagraph(<<< 'EOF'
A component of Agile Toolkit (callback) enables seamless communication between the frontend components (which are often
written in VueJS) and the backend. We also support seamless reloading of any UI widget:
EOF
);

    $page->add(new Demo())->setCode(<<<'CODE'

$seg = $app->add(['ui'=>'segment']);

$seg->add('Text')->set('Number of buttons: ');

$paginator = $seg->add([
    'Paginator',
    'total'=>5,
    'reload'=>$seg,
    'urlTrigger'=>'count'
]);

$seg->add(['ui'=>'divider']);

for($i=1; $i <= ($_GET['count'] ?? 1); $i++) {
    $seg->add(['Button', $i]);
}

CODE
);

    $t = $page->add('Text');
    $t->addParagraph(<<< 'EOF'
This demo also shows you how to create composite views. The '$seg' above contains text, paginator, divider and some
buttons. Interestingly, Paginator view also consists of buttons and Agile Toolkit renders everything reliably.
EOF
);
});

$wizard->addStep('Business Model', function ($page) {

    /** @var \atk4\ui\Text $t */
    $t = $page->add('Text');
    $t->addParagraph(<<< 'EOF'
One major benefit of Server Side Rendered applications is ability to directly interact with data. In other applications
you may need to manually process data but in Agile Toolkit we use our own data mapping framework.
EOF
);

    $page->add(new Demo())->setCode(<<<'CODE'

class Invoice extends \atk4\data\Model {
    public $title_field = 'reference';
        function init() {
        parent::init();

        $this->addField('reference');
        $this->addField('date', ['type'=>'date']);
    }
}

session_start();
$session = new atk4\data\Persistence\Array_($_SESSION['x']);

$form = $app->add('Form');
$form->setModel(new Invoice($session))
    ->tryLoad(1);

$app->add(['ui'=>'divider']);
$app->add(['Button', 'Refresh', 'icon'=>'refresh'])
    ->on('click', $app->jsReload());

CODE
);

    $t = $page->add('Text');
    $t->addParagraph(<<< 'EOF'
This code shows you a combination of 3 objects:
EOF
);
    $t->addHTML(<<< 'HTML'
<ul>
    <li>Form - a generic view that can display and handle any form</li>
    <li>Model - defines fields for a business object</li>
    <li>Persistence - creates a persistent storage location for the data</li>
</ul>

HTML
);
    $t->addParagraph(<<< 'EOF'
All three are combined by "setModel()" function and that is consistent throughout all the views.
EOF
);
});

$wizard->addStep('Persistence', function ($page) {

    /** @var \atk4\ui\Text $t */
    $t = $page->add('Text');
    $t->addParagraph(<<< 'EOF'
Once your model is defined, it can be re-used later with any generic view:
EOF
    );

    class Invoice extends \atk4\data\Model
    {
        public $title_field = 'reference';

        public function init()
        {
            parent::init();

            $this->addField('reference');
            $this->addField('date', ['type'=>'date']);
        }
    }
    session_start();

    $page->add(new Demo())->setCode(<<<'CODE'
$session = new atk4\data\Persistence\Array_($_SESSION['x']);

$model = new Invoice($session);
$app->add('Table')->setModel($model, ['reference', 'date']);

CODE
    );

    $t = $page->add('Text');
    $t->addParagraph(<<< 'EOF'
Re-use of your Business Model code, generic and interactive views and principles of composition and a simple PHP
code offers a most efficient way of constructing Web Applications.
EOF
    );
});

$wizard->addFinish(function ($page) use ($wizard) {
    $t = $page->add('Text');
    $t->addParagraph(<<< 'EOF'
Agile Toolkit base package includes:
EOF
    );

    $t->addHTML(<<< 'HTML'
<ul>
    <li>Over 40 ready-to-use and nicely styled UI components</li>
    <li>Over 10 ways to build interraction</li>
    <li>Over 10 configurable field types, relations, aggregation and much more</li>
    <li>Over 5 SQL and some NoSQL vendors fully supported</li>
</ul>

HTML
    );

    $gl = $page->add(new \atk4\ui\GridLayout([null, 'stackable divided', 'columns'=>4]));
    $gl->add(['Button', 'Explore UI components', 'primary basic fluid', 'iconRight'=>'right arrow'], 'r1c1')
        ->link('https://github.com/atk4/ui/#bundled-and-planned-components');
    $gl->add(['Button', 'Try out interactive features', 'primary basic fluid', 'iconRight'=>'right arrow'], 'r1c2')
        ->link(['loader', 'begin'=>false, 'layout'=>false]);
    $gl->add(['Button', 'Dive into Agile Data', 'primary basic fluid', 'iconRight'=>'right arrow'], 'r1c3')
        ->link('https://git.io/ad');
    $gl->add(['Button', 'More ATK Add-ons', 'primary basic fluid', 'iconRight'=>'right arrow'], 'r1c4')
        ->link('https://github.com/atk4/ui/#add-ons-and-integrations');

    $wizard->add(['Button', 'Exit demo', 'primary', 'icon'=>'left arrow'], 'Left')
        ->link(['index', 'layout'=>false]);

    $page->add(['ui'=>'divider']);

    $page->add(['Message', 'Cool fact!', 'info', 'icon'=>'book'])->text
        ->addParagraph('This entire demo is coded in Agile Toolkit and takes up less than 300 lines of very simple code code!');
});
