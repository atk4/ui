<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\Button;
use Atk4\Ui\Header;
use Atk4\Ui\View;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

View::addTo($app, [
    'Sticky GET allows us to preserve some GET arguments',
    'ui' => 'ignored info message',
]);

$myButtonClass = AnonymousClassNameCache::get_class(fn () => new class() extends Button {
    protected function renderView(): void
    {
        $this->link($this->content);
        $this->addClass('green');

        parent::renderView();
    }
});

// buttons
$myButtonClass::addTo($app, [$app->url()]);
$myButtonClass::addTo($app, [$app->url(['xx' => 'YEY'])]);
$myButtonClass::addTo($app, [$app->url(['c' => 'OHO'])]);
$myButtonClass::addTo($app, [$app->url(['xx' => 'YEY', 'c' => 'OHO'])]);

// URLs presented by a blank app
Header::addTo($app, ['URLs presented by a blank app']);
Button::addTo($app, [$app->url()]);
Button::addTo($app, [$app->url(['b' => 2])]);
Button::addTo($app, [$app->url(['b' => 2, 'c' => false])]);
Button::addTo($app, [$app->url(['b' => 2, 'c' => 'abc'])]);

// sticky for xx=
Header::addTo($app, ['Now add sticky for xx=' . $app->stickyGet('xx')]);
Button::addTo($app, [$app->url()]);
Button::addTo($app, [$app->url(['b' => 2])]);
Button::addTo($app, [$app->url(['b' => 2, 'c' => false])]);
Button::addTo($app, [$app->url(['b' => 2, 'c' => 'abc'])]);

// sticky for c=
Header::addTo($app, ['Now also add sticky for c=' . $app->stickyGet('c')]);
Button::addTo($app, [$app->url()]);
Button::addTo($app, [$app->url(['b' => 2])]);
Button::addTo($app, [$app->url(['b' => 2, 'c' => false])]);
Button::addTo($app, [$app->url(['b' => 2, 'c' => 'abc'])]);

// various ways to build links
Header::addTo($app, ['Various ways to build links']);
Button::addTo($app, [$app->url()]);
Button::addTo($app, [$app->url('other.php')]);
Button::addTo($app, [$app->url('other')]);
Button::addTo($app, [$app->url(['other', 'b' => 2])]);
Button::addTo($app, [$app->url('http://google.com/')]);
Button::addTo($app, [$app->url('http://google.com/?q=abc')]);

// unset app/global sticky
$app->stickyForget('xx');
$app->stickyForget('c');
