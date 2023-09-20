<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\Button;
use Atk4\Ui\Header;
use Atk4\Ui\HtmlTemplate;
use Atk4\Ui\Icon;
use Atk4\Ui\Label;
use Atk4\Ui\Table;
use Atk4\Ui\View;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

Header::addTo($app, ['Basic Button', 'size' => 2]);

// with seed
Button::addTo($app, ['Click me'])->link(['index']);

// without seeding
$b1 = new Button('Click me (no seed)');
$app->add($b1);
// must be added first
$b1->link(['index']);

Header::addTo($app, ['Properties', 'size' => 2]);
Button::addTo($app, ['Primary button', 'class.primary' => true]);
Button::addTo($app, ['Load', 'icon' => 'pause']);
Button::addTo($app, ['Next', 'iconRight' => 'right arrow']);
Button::addTo($app, ['class.circular' => true, 'icon' => 'settings']);

Header::addTo($app, ['Big Button', 'size' => 2]);
Button::addTo($app, ['Click me', 'class.big primary' => true, 'icon' => 'check']);

Header::addTo($app, ['Button Intent', 'size' => 2]);
Button::addTo($app, ['Yes', 'class.positive basic' => true]);
Button::addTo($app, ['No', 'class.negative basic' => true]);

Header::addTo($app, ['Combining Buttons', 'size' => 2]);

$bar = View::addTo($app, ['ui' => 'vertical buttons']);
Button::addTo($bar, ['Play', 'icon' => 'play']);
Button::addTo($bar, ['Pause', 'icon' => 'pause']);
Button::addTo($bar, ['Shuffle', 'icon' => 'shuffle']);

Header::addTo($app, ['Icon Bar', 'size' => 2]);
$bar = View::addTo($app, ['ui' => 'big blue buttons']);
Button::addTo($bar, ['icon' => 'file']);
Button::addTo($bar, ['icon' => 'yellow save']);
Button::addTo($bar, ['icon' => 'upload', 'class.disabled' => true]);

Header::addTo($app, ['Forks Button Component', 'size' => 2]);

// Creating your own button component example

$forkButtonClass = AnonymousClassNameCache::get_class(fn () => new class(0) /* need 0 argument here for constructor */ extends Button {
    public function __construct(int $n)
    {
        Icon::addTo(Button::addTo($this, ['Forks', 'class.blue' => true]), ['fork']);
        Label::addTo($this, [number_format($n), 'class.basic blue left pointing' => true]);

        parent::__construct(['class.labeled' => true]);
    }
});

$forkButton = new $forkButtonClass(1234 + random_int(1, 100));
$app->add($forkButton);

Header::addTo($app, ['Custom Template', 'size' => 2]);

$view = View::addTo($app, ['template' => new HtmlTemplate('Hello, {$tag1}, my name is {$tag2}')]);

Button::addTo($view, ['World'], ['tag1']);
Button::addTo($view, ['Agile UI', 'class.blue' => true], ['tag2']);

Header::addTo($app, ['Attaching', 'size' => 2]);

Button::addTo($app, ['Previous', 'class.top attached' => true]);
Table::addTo($app, ['class.attached' => true, 'header' => false])
    ->setSource(['One', 'Two', 'Three', 'Four']);
Button::addTo($app, ['Next', 'class.bottom attached' => true]);
