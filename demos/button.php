<?php
/**
 * Demonstrates how to use layouts.
 */
require 'init.php';
use \atk4\ui\Button;
use \atk4\ui\Buttons;
use \atk4\ui\Header;
use \atk4\ui\Icon;
use \atk4\ui\Label;
use \atk4\ui\Template;
use \atk4\ui\View;

$layout->add(new Header('Basic Button', 'size'=>2));
$layout->add(new Button())->set('Click me');

$layout->add(new Header('Properties', 'size'=>2));

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

$layout->add(new Header('Big Button', 'size'=>2));

$layout->add($button);

$layout->add(new Header('Button Intent', 'size'=>2));

$b_yes = new Button(['Yes', 'positive basic']);
$b_no = new Button(['No', 'negative basic']);
$layout->add($b_yes);
$layout->add($b_no);

$layout->add(new Header('Combining Buttons', 'size'=>2));
$bar = new Buttons('vertical');  // NOTE: class called Buttons, not Button
$bar->add(new Button(['Play', 'icon'=>'play']));
$bar->add(new Button(['Pause', 'icon'=>'pause']));
$bar->add(new Button(['Shuffle', 'icon'=>'shuffle']));

$layout->add($bar);

$layout->add(new Header('Icon Bar', 'size'=>2));
$bar = new Buttons('blue big');
$bar->add(new Button(['icon'=>'file']));
$bar->add(new Button(['icon'=>['save', 'yellow']]));
$bar->add(new Button(['icon'=>'upload', 'disabled'=>true]));
$layout->add($bar);

$layout->add(new Header('Forks', 'size'=>2));
$forks = new Button(['labeled'=> true]); // Button, not Buttons!
$forks->add(new Button(['Forks', 'blue']))->add(new Icon('fork'));
$forks->add(new Label(['1,048', 'basic blue left pointing']));
$layout->add($forks);

$layout->add(new Header('Custom Template', 'size'=>2));
$view = new View(['template'=>new Template('Hello, {$tag1}, my name is {$tag2}')]);

$view->add(new Button('World'), 'tag1');
$view->add(new Button(['Agile UI', 'blue']), 'tag2');

$layout->add($view);
