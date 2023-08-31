<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\Button;
use Atk4\Ui\Columns;
use Atk4\Ui\Header;
use Atk4\Ui\HtmlTemplate;
use Atk4\Ui\Js\JsModal;
use Atk4\Ui\Js\JsReload;
use Atk4\Ui\Label;
use Atk4\Ui\Message;
use Atk4\Ui\Paginator;
use Atk4\Ui\Table;
use Atk4\Ui\View;
use Atk4\Ui\VirtualPage;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

$img = $app->cdn['atk'] . '/logo.png';

Header::addTo($app, ['Default view has no styling']);
View::addTo($app)->set('just a <div> element');

Header::addTo($app, ['View can specify CSS class']);
View::addTo($app, ['ui' => 'segment', 'class.raised' => true])->set('Segment');

Header::addTo($app, ['View can contain stuff']);
Header::addTo(View::addTo($app, ['ui' => 'segment'])
    ->addClass('inverted red circular'), ['Buy', 'class.inverted' => true, 'subHeader' => '$' . (random_int(100, 1000) / 100)]);

Header::addTo($app, ['View can use JavaScript']);
View::addTo($app, ['ui' => 'heart rating'])
    ->js(true)->rating(['maxRating' => 5, 'initialRating' => random_int(1, 5)]);

Header::addTo($app, ['View can have events']);
$bb = View::addTo($app, ['ui' => 'large blue buttons']);
$bb->on('click', '.button')->transition('fly up');

foreach (str_split('Click me!!') as $letter) {
    Button::addTo($bb, [$letter]);
}

Header::addTo($app, ['View load HTML from string or file']);
$planeTemplate = new HtmlTemplate('<div class="ui statistic" {$attributes}>
    <div class="value">
      <i class="plane icon"></i> {$num}
    </div>
    <div class="label">
      Flights
    </div>
  </div>');
$planeTemplate->set('num', (string) random_int(100, 999));

$plane = View::addTo($app, ['template' => $planeTemplate]);

Header::addTo($app, ['Can be rendered into HTML']);
View::addTo($app, ['ui' => 'segment', 'class.raised' => true, 'element' => 'pre'])->set($plane->render());

Header::addTo($app, ['Has a unique global identifier']);
Label::addTo($app, ['Plane ID:', 'detail' => $plane->name]);

Header::addTo($app, ['Can interact with JavaScript actions']);
Button::addTo($app, ['Hide plane', 'icon' => 'down arrow'])
    ->on('click', $plane->js()->hide());
Button::addTo($app, ['Show plane', 'icon' => 'up arrow'])
    ->on('click', $plane->js()->show());
Button::addTo($app, ['Jiggle plane', 'icon' => 'expand'])
    ->on('click', $plane->js()->transition('jiggle'));
Button::addTo($app, ['Reload plane', 'icon' => 'refresh'])
    ->on('click', new JsReload($plane));

Header::addTo($app, ['Can be on a Virtual Page']);
$vp = VirtualPage::addTo($app)->set(static function (VirtualPage $vp) use ($planeTemplate) {
    $plane = View::addTo($vp, ['template' => $planeTemplate]);
    Label::addTo($vp, ['Plane ID:', 'class.bottom attached' => true, 'detail' => $plane->name]);
});

Button::addTo($app, ['Show $plane in a dialog', 'icon' => 'clone'])
    ->on('click', new JsModal('Plane Box', $vp));

Header::addTo($app, ['All components extend View (even paginator)']);
$columns = Columns::addTo($app);

Button::addTo($columns->addColumn(), ['Button'])->addClass('green');
Header::addTo($columns->addColumn(), ['Header'])->addClass('green');
Label::addTo($columns->addColumn(), ['Label'])->addClass('green');
Message::addTo($columns->addColumn(), ['Message'])->addClass('green');
Paginator::addTo($columns->addColumn(), ['total' => 3, 'reload' => $columns])->addClass('green');

Header::addTo($app, ['Can have a custom render logic']);
Table::addTo($app)->addClass('green')->setSource(['One', 'Two', 'Three']);
