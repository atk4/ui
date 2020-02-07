<?php

require 'init.php';
$img = 'https://github.com/atk4/ui/raw/07208a0af84109f0d6e3553e242720d8aeedb784/public/logo.png';

$app->add(['Header', 'Default view has no styling']);
$app->add('View')->set('just a <div> element');

$app->add(['Header', 'View can specify CSS class']);
$app->add(['View', 'ui' => 'segment', 'raised'])->set('Segment');

$app->add(['Header', 'View can contain stuff']);
$app->add(['View', 'ui' => 'segment'])
    ->addClass('inverted red circular')
    ->add(['Header', 'Buy', 'inverted', 'subHeader' => '$' . (rand(100, 1000) / 100)]);

$app->add(['Header', 'View can use JavaScript']);
$app->add(['View', 'ui' => 'heart rating'])
    ->js(true)->rating(['maxRating' => 5, 'initialRating' => rand(1, 5)]);

$app->add(['Header', 'View can have events']);
$bb = $app->add(['View', 'ui' => 'large blue buttons']);
$bb->on('click', '.button')->transition('fly up');

foreach (str_split('Click me!!') as $letter) {
    $bb->add(['Button', $letter]);
}

$app->add(['Header', 'View load HTML from string or file']);
/** @var \atk4\ui\View $plane */
$plane = $app->add(['View', 'template' => new \atk4\ui\Template('<div id="{$_id}" class="ui statistic">
    <div class="value">
      <i class="plane icon"></i> {$num}
    </div>
    <div class="label">
      Flights
    </div>
  </div>')]);
$plane->template->set('num', rand(5, 20));

$app->add(['Header', 'Can be rendered into HTML']);
$app->add(['View', 'ui' => 'segment', 'raised', 'element' => 'pre'])->set($plane->render());

$app->add(['Header', 'Has a unique global identifier']);
$app->add(['Label', 'Plane ID: ', 'detail' => $plane->name]);

$app->add(['Header', 'Can interract with JavaScript actions']);
$app->add(['Button', 'Hide plane', 'icon' => 'down arrow'])->on('click', $plane->js()->hide());
$app->add(['Button', 'Show plane', 'icon' => 'up arrow'])->on('click', $plane->js()->show());
$app->add(['Button', 'Jiggle plane', 'icon' => 'expand'])->on('click', $plane->js()->transition('jiggle'));
$app->add(['Button', 'Reload plane', 'icon' => 'refresh'])->on('click', new \atk4\ui\jsReload($plane));

$app->add(['Header', 'Can be on a Virtual Page']);
$vp = $app->add('VirtualPage')->set(function ($page) use ($plane) {
    $page->add($plane);
    $page->add(['Label', 'Plane ID: ', 'bottom attached', 'detail' => $plane->name]);
});

$app->add(['Button', 'Show $plane in a dialog', 'icon' => 'clone'])->on('click', new \atk4\ui\jsModal('Plane Box', $vp));

$app->add(['Header', 'All components extend View (even paginator)']);
$columns = $app->add('Columns');

$columns->addColumn()->add(['Button', 'Button'])->addClass('green');
$columns->addColumn()->add(['Header', 'Header'])->addClass('green');
$columns->addColumn()->add(['Label', 'Label'])->addClass('green');
$columns->addColumn()->add(['Message', 'Message'])->addClass('green');
$columns->addColumn()->add(['Paginator', 'total' => 3, 'reload' => $columns])->addClass('green');

$app->add(['Header', 'Can have a custom render logic']);
$app->add('Table')->addclass('green')->setSource(['One', 'Two', 'Three']);
