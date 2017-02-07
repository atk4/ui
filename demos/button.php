<?php
/**
 * Demonstrates how to use layouts.
 */
require '../vendor/autoload.php';
use \atk4\ui\Button;
use \atk4\ui\Buttons;
use \atk4\ui\H2;
use \atk4\ui\Icon;
use \atk4\ui\Label;
use \atk4\ui\Template;
use \atk4\ui\View;

try {
    $layout = new \atk4\ui\Layout\App(['defaultTemplate'=>'./templates/layout2.html']);

    $layout->add(new H2('Basic Button'));
    $layout->add(new Button())->set('Click me');

    $layout->add(new H2('Properties'));

    $b1 = new Button();
    $b2 = new Button();
    $b3 = new Button();

    $b1->set(['Load', 'primary']);
    $b2->set(['Load', 'labeled', 'icon'=>'pause']);
    $b3->set(['Next', 'right labeled', 'icon'=>'right arrow']);
    $layout->add($b1);
    $layout->add($b2);
    $layout->add($b3);

    $button = new Button();
    $button->set('Click me');
    $button->set(['primary' => true]);
    $button->set(['icon'=>'check']);
    $button->set(['size big'=>true]);

    $layout->add(new H2('Big Button'));

    $layout->add($button);

    $layout->add(new H2('Button Intent'));

    $b_yes = new Button(['Yes', 'positive basic']);
    $b_no = new Button(['No', 'negative basic']);
    $layout->add($b_yes);
    $layout->add($b_no);

    $layout->add(new H2('Combining Buttons'));
    $bar = new Buttons('vertical');  // NOTE: class called Buttons, not Button
    $bar->add(new Button(['Play', 'icon'=>'play']));
    $bar->add(new Button(['Pause', 'icon'=>'pause']));
    $bar->add(new Button(['Shuffle', 'icon'=>'shuffle']));

    $layout->add($bar);

    $layout->add(new H2('Icon Bar'));
    $bar = new Buttons('blue big');
    $bar->add(new Button(['icon'=>'file']));
    $bar->add(new Button(['icon'=>['save', 'yellow']]));
    $bar->add(new Button(['icon'=>'upload', 'disabled'=>true]));
    $layout->add($bar);

    $layout->add(new H2('Forks'));
    $forks = new Button(['labeled'=> true]); // Button, not Buttons!
    $forks->add(new Button(['Forks', 'blue']))->add(new Icon('fork'));
    $forks->add(new Label(['1,048', 'basic blue left pointing']));
    $layout->add($forks);

    $layout->add(new H2('Custom Template'));
    $view = new View(['template'=>new Template('Hello, {$tag1}, my name is {$tag2}')]);

    $view->add(new Button('World'), 'tag1');
    $view->add(new Button(['Agile UI', 'blue']), 'tag2');

    $layout->add($view);

    echo $layout->render();
} catch (\atk4\core\Exception $e) {
    var_dump($e->getMessage());

    var_dump($e->getParams());
    var_dump($e->getTrace());
    throw $e;
}
