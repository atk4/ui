<?php
/**
 * Demonstrates how to use buttons.
 */
require 'init.php';
use atk4\ui\Button;
use atk4\ui\Header;
use atk4\ui\Icon;
use atk4\ui\Label;
use atk4\ui\Template;
use atk4\ui\View;

$app->add(new Header(['Basic Button', 'size' => 2]));
$app->add(new Button())->set('Click me');

$app->add(new Header(['Properties', 'size' => 2]));

$b1 = new Button();
$b2 = new Button();
$b3 = new Button();
$b4 = new Button();

$b1->set(['Load', 'primary']);
$b2->set(['Load', 'labeled', 'icon' => 'pause']);
$b3->set(['Next', 'iconRight' => 'right arrow']);
$b4->set([false, 'circular', 'icon' => 'settings']);
$app->add($b1);
$app->add($b2);
$app->add($b3);
$app->add($b4);

$button = new Button();
$button->set('Click me');
$button->set(['primary' => true]);
$button->set(['icon' => 'check']);
$button->set(['size big' => true]);

$app->add(new Header(['Big Button', 'size' => 2]));

$app->add($button);

$app->add(new Header(['Button Intent', 'size' => 2]));

$b_yes = new Button(['Yes', 'positive basic']);
$b_no = new Button(['No', 'negative basic']);
$app->add($b_yes);
$app->add($b_no);

$app->add(new Header(['Combining Buttons', 'size' => 2]));
$bar = new View(['ui' => 'buttons', null, 'vertical']);  // NOTE: class called Buttons, not Button
$bar->add(new Button(['Play', 'icon' => 'play']));
$bar->add(new Button(['Pause', 'icon' => 'pause']));
$bar->add(new Button(['Shuffle', 'icon' => 'shuffle']));

$app->add($bar);

$app->add(new Header(['Icon Bar', 'size' => 2]));
$bar = new View(['ui' => 'buttons', null, 'blue big']);  // NOTE: class called Buttons, not Button
$bar->add(new Button(['icon' => 'file']));
$bar->add(new Button(['icon' => ['save', 'yellow']]));
$bar->add(new Button(['icon' => 'upload', 'disabled' => true]));
$app->add($bar);

$app->add(new Header(['Forks', 'size' => 2]));
$forks = new Button(['labeled' => true]); // Button, not Buttons!
$forks->add(new Button(['Forks', 'blue']))->add(new Icon('fork'));
$forks->add(new Label(['1,048', 'basic blue left pointing']));
$app->add($forks);

$app->add(new Header(['Custom Template', 'size' => 2]));
$view = new View(['template' => new Template('Hello, {$tag1}, my name is {$tag2}')]);

$view->add(new Button('World'), 'tag1');
$view->add(new Button(['Agile UI', 'blue']), 'tag2');

$app->add($view);

$app->add(new Header(['Attaching', 'size' => 2]));

$app->add(['Button', 'Scroll Up', 'top attached']);
$app->add(['Table', 'attached', 'header' => false])->setSource(['One', 'Two', 'Three', 'Four']);
$app->add(['Button', 'Scroll Up', 'bottom attached']);

