<?php

/**
 * Demonstrates how to use buttons.
 */

require 'init.php';
use atk4\ui\Button;
use atk4\ui\Icon;
use atk4\ui\Label;
use atk4\ui\Template;

$app->add(['Header', 'Basic Button', 'size' => 2]);

// With Seed
$app->add(['Button', 'Click me'])->link(['index']);

// Without Seeding
$b1 = new Button('Click me (no seed)');
$app->add($b1);
// must be added first
$b1->link(['index']);

$app->add(['Header', 'Properties', 'size' => 2]);
$app->add(['Button', 'Primary button', 'primary']);
$app->add(['Button', 'Load', 'labeled', 'icon' => 'pause']);
$app->add(['Button', 'Next', 'iconRight' => 'right arrow']);
$app->add(['Button', null, 'circular', 'icon' => 'settings']);

$app->add(['Header', 'Big Button', 'size' => 2]);
$app->add(['Button', 'Click me', 'big primary', 'icon' => 'check']);

$app->add(['Header', 'Button Intent', 'size' => 2]);
$app->add(['Button', 'Yes', 'positive basic']);
$app->add(['Button', 'No', 'negative basic']);

$app->add(['Header', 'Combining Buttons', 'size' => 2]);

$bar = $app->add(['ui' => 'vertical buttons']);
$bar->add(['Button', 'Play', 'icon' => 'play']);
$bar->add(['Button', 'Pause', 'icon' => 'pause']);
$bar->add(['Button', 'Shuffle', 'icon' => 'shuffle']);

$app->add(['Header', 'Icon Bar', 'size' => 2]);
$bar = $app->add(['ui' => 'big blue buttons']);
$bar->add(['Button', 'icon' => 'file']);
$bar->add(['Button', 'icon' => 'yellow save']);
$bar->add(['Button', 'icon' => 'upload', 'disabled' => true]);

$app->add(['Header', 'Forks Button Component', 'size' => 2]);

// Creating your own button component example
class ForkButton extends Button
{
    public function __construct($n)
    {
        $this->add(new Button(['Forks', 'blue']))->add(new Icon('fork'));
        $this->add(new Label([number_format($n), 'basic blue left pointing']));
        parent::__construct(null, 'labeled');
    }
}

$app->add(new ForkButton(1234 + rand(1, 100)));

$app->add(['Header', 'Custom Template', 'size' => 2]);

$view = $app->add(['template' => new Template('Hello, {$tag1}, my name is {$tag2}')]);

$view->add(new Button('World'), 'tag1');
$view->add(new Button(['Agile UI', 'blue']), 'tag2');

$app->add(['Header', 'Attaching', 'size' => 2]);

$app->add(['Button', 'Previous', 'top attached']);
$app->add(['Table', 'attached', 'header' => false])
    ->setSource(['One', 'Two', 'Three', 'Four']);
$app->add(['Button', 'Next', 'bottom attached']);
