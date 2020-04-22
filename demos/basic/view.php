<?php

chdir('..');
require_once 'init.php';

$img = 'https://github.com/atk4/ui/raw/07208a0af84109f0d6e3553e242720d8aeedb784/public/logo.png';

\atk4\ui\Header::addTo($app, ['Default view has no styling']);
\atk4\ui\View::addTo($app)->set('just a <div> element');

\atk4\ui\Header::addTo($app, ['View can specify CSS class']);
\atk4\ui\View::addTo($app, ['ui' => 'segment', 'raised'])->set('Segment');

\atk4\ui\Header::addTo($app, ['View can contain stuff']);
\atk4\ui\Header::addTo(\atk4\ui\View::addTo($app, ['ui' => 'segment'])
    ->addClass('inverted red circular'), ['Buy', 'inverted', 'subHeader' => '$' . (rand(100, 1000) / 100)]);

\atk4\ui\Header::addTo($app, ['View can use JavaScript']);
\atk4\ui\View::addTo($app, ['ui' => 'heart rating'])
    ->js(true)->rating(['maxRating' => 5, 'initialRating' => rand(1, 5)]);

\atk4\ui\Header::addTo($app, ['View can have events']);
$bb = \atk4\ui\View::addTo($app, ['ui' => 'large blue buttons']);
$bb->on('click', '.button')->transition('fly up');

foreach (str_split('Click me!!') as $letter) {
    \atk4\ui\Button::addTo($bb, [$letter]);
}

\atk4\ui\Header::addTo($app, ['View load HTML from string or file']);
$plane = \atk4\ui\View::addTo($app, ['template' => new \atk4\ui\Template('<div id="{$_id}" class="ui statistic">
    <div class="value">
      <i class="plane icon"></i> {$num}
    </div>
    <div class="label">
      Flights
    </div>
  </div>')]);
$plane->template->set('num', rand(5, 20));

\atk4\ui\Header::addTo($app, ['Can be rendered into HTML']);
\atk4\ui\View::addTo($app, ['ui' => 'segment', 'raised', 'element' => 'pre'])->set($plane->render());

\atk4\ui\Header::addTo($app, ['Has a unique global identifier']);
\atk4\ui\Label::addTo($app, ['Plane ID: ', 'detail' => $plane->name]);

\atk4\ui\Header::addTo($app, ['Can interract with JavaScript actions']);
\atk4\ui\Button::addTo($app, ['Hide plane', 'icon' => 'down arrow'])->on('click', $plane->js()->hide());
\atk4\ui\Button::addTo($app, ['Show plane', 'icon' => 'up arrow'])->on('click', $plane->js()->show());
\atk4\ui\Button::addTo($app, ['Jiggle plane', 'icon' => 'expand'])->on('click', $plane->js()->transition('jiggle'));
\atk4\ui\Button::addTo($app, ['Reload plane', 'icon' => 'refresh'])->on('click', new \atk4\ui\jsReload($plane));

\atk4\ui\Header::addTo($app, ['Can be on a Virtual Page']);
$vp = \atk4\ui\VirtualPage::addTo($app)->set(function ($page) use ($plane) {
    $page->add($plane);
    \atk4\ui\Label::addTo($page, ['Plane ID: ', 'bottom attached', 'detail' => $plane->name]);
});

\atk4\ui\Button::addTo($app, ['Show $plane in a dialog', 'icon' => 'clone'])->on('click', new \atk4\ui\jsModal('Plane Box', $vp));

\atk4\ui\Header::addTo($app, ['All components extend View (even paginator)']);
$columns = \atk4\ui\Columns::addTo($app);

\atk4\ui\Button::addTo($columns->addColumn(), ['Button'])->addClass('green');
\atk4\ui\Header::addTo($columns->addColumn(), ['Header'])->addClass('green');
\atk4\ui\Label::addTo($columns->addColumn(), ['Label'])->addClass('green');
\atk4\ui\Message::addTo($columns->addColumn(), ['Message'])->addClass('green');
\atk4\ui\Paginator::addTo($columns->addColumn(), ['total' => 3, 'reload' => $columns])->addClass('green');

\atk4\ui\Header::addTo($app, ['Can have a custom render logic']);
\atk4\ui\Table::addTo($app)->addclass('green')->setSource(['One', 'Two', 'Three']);
