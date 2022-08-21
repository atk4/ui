<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\Header;
use Atk4\Ui\HtmlTemplate;
use Atk4\Ui\Label;
use Atk4\Ui\View;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

$img = $app->cdn['atk'] . '/logo.png';

Header::addTo($app, ['Default view has no styling']);
\Atk4\Ui\View::addTo($app)->set('just a <div> element');

Header::addTo($app, ['View can specify CSS class']);
\Atk4\Ui\View::addTo($app, ['ui' => 'segment', 'class.raised' => true])->set('Segment');

Header::addTo($app, ['View can contain stuff']);
Header::addTo(\Atk4\Ui\View::addTo($app, ['ui' => 'segment'])
    ->addClass('inverted red circular'), ['Buy', 'class.inverted' => true, 'subHeader' => '$' . (random_int(100, 1000) / 100)]);

Header::addTo($app, ['View can use JavaScript']);
\Atk4\Ui\View::addTo($app, ['ui' => 'heart rating'])
    ->js(true)->rating(['maxRating' => 5, 'initialRating' => random_int(1, 5)]);

Header::addTo($app, ['View can have events']);
$bb = \Atk4\Ui\View::addTo($app, ['ui' => 'large blue buttons']);
$bb->on('click', '.button')->transition('fly up');

foreach (str_split('Click me!!') as $letter) {
    \Atk4\Ui\Button::addTo($bb, [$letter]);
}

Header::addTo($app, ['View load HTML from string or file']);
$planeTemplate = new HtmlTemplate('<div id="{$_id}" class="ui statistic">
    <div class="value">
      <i class="plane icon"></i> {$num}
    </div>
    <div class="label">
      Flights
    </div>
  </div>');
$planeTemplate->set('num', random_int(100, 999));

$plane = \Atk4\Ui\View::addTo($app, ['template' => $planeTemplate]);

Header::addTo($app, ['Can be rendered into HTML']);
\Atk4\Ui\View::addTo($app, ['ui' => 'segment', 'class.raised' => true, 'element' => 'pre'])->set($plane->render());

Header::addTo($app, ['Has a unique global identifier']);
Label::addTo($app, ['Plane ID: ', 'detail' => $plane->name]);

Header::addTo($app, ['Can interract with JavaScript actions']);
\Atk4\Ui\Button::addTo($app, ['Hide plane', 'icon' => 'down arrow'])->on('click', $plane->js()->hide());
\Atk4\Ui\Button::addTo($app, ['Show plane', 'icon' => 'up arrow'])->on('click', $plane->js()->show());
\Atk4\Ui\Button::addTo($app, ['Jiggle plane', 'icon' => 'expand'])->on('click', $plane->js()->transition('jiggle'));
\Atk4\Ui\Button::addTo($app, ['Reload plane', 'icon' => 'refresh'])->on('click', new \Atk4\Ui\JsReload($plane));

Header::addTo($app, ['Can be on a Virtual Page']);
$vp = \Atk4\Ui\VirtualPage::addTo($app)->set(function ($page) use ($planeTemplate) {
    $plane = View::addTo($page, ['template' => $planeTemplate]);
    Label::addTo($page, ['Plane ID: ', 'class.bottom attached' => true, 'detail' => $plane->name]);
});

\Atk4\Ui\Button::addTo($app, ['Show $plane in a dialog', 'icon' => 'clone'])->on('click', new \Atk4\Ui\JsModal('Plane Box', $vp));

Header::addTo($app, ['All components extend View (even paginator)']);
$columns = \Atk4\Ui\Columns::addTo($app);

\Atk4\Ui\Button::addTo($columns->addColumn(), ['Button'])->addClass('green');
Header::addTo($columns->addColumn(), ['Header'])->addClass('green');
Label::addTo($columns->addColumn(), ['Label'])->addClass('green');
\Atk4\Ui\Message::addTo($columns->addColumn(), ['Message'])->addClass('green');
\Atk4\Ui\Paginator::addTo($columns->addColumn(), ['total' => 3, 'reload' => $columns])->addClass('green');

Header::addTo($app, ['Can have a custom render logic']);
\Atk4\Ui\Table::addTo($app)->addClass('green')->setSource(['One', 'Two', 'Three']);
