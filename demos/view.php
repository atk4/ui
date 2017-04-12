<?php

require 'init.php';
$img = 'https://github.com/atk4/ui/raw/07208a0af84109f0d6e3553e242720d8aeedb784/public/logo.png';

$layout->add(['Header', 'Default view has no styling']);
$layout->add('View')->set('just a <div> element');

$layout->add(['Header', 'View can specify CSS class']);
$layout->add(['View', 'ui'=>'segment', 'raised'])->set('Segment');

$layout->add(['Header', 'View can contain stuff']);
$layout->add(['View', 'ui'=>'segment'])
    ->addClass('inverted red circular')
    ->add(['Header', 'Buy', 'inverted', 'subHeader'=>'$'.(rand(100, 1000) / 100)]);

$layout->add(['Header', 'View can use JavaScript']);
$layout->add(['View', 'ui'=>'heart rating'])
    ->js(true)->rating(['maxRating'=>5, 'initialRating'=>rand(1, 5)]);

$layout->add(['Header', 'View can have events']);
$bb = $layout->add(['View', 'ui'=>'large blue buttons']);
$bb->on('click', '.button')->transition('fly up');

foreach (str_split('Click me!!') as $letter) {
    $bb->add(['Button', $letter]);
}

$layout->add(['Header', 'View load HTML from string or file']);
$plane = $layout->add(['View', 'template'=>new \atk4\ui\Template('<div id="{$_id}" class="ui statistic">
    <div class="value">
      <i class="plane icon"></i> {$num}
    </div>
    <div class="label">
      Flights
    </div>
  </div>')]);
$plane->template->set('num', rand(5, 20));

$layout->add(['Header', 'Can be rendered into HTML']);
$layout->add(['View', 'ui'=>'segment', 'raised', 'element'=>'pre'])->set($plane->render());

$layout->add(['Header', 'Has a unique global identifier']);
$layout->add(['Label', 'Plane ID: ', 'detail'=>$plane->name]);

$layout->add(['Header', 'Can interract with JavaScript actions']);
$layout->add(['Button', 'Hide plane', 'icon'=>'down arrow'])->on('click', $plane->js()->hide());
$layout->add(['Button', 'Show plane', 'icon'=>'up arrow'])->on('click', $plane->js()->show());
$layout->add(['Button', 'Jiggle plane', 'icon'=>'expand'])->on('click', $plane->js()->transition('jiggle'));
$layout->add(['Button', 'Reload plane', 'icon'=>'refresh'])->on('click', new \atk4\ui\jsReload($plane));

$layout->add(['Header', 'Can be on a Virtual Page']);
$vp = $layout->add('VirtualPage')->set(function ($page) use ($plane) {
    $page->add($plane);
    $page->add(['Label', 'Plane ID: ', 'bottom attached', 'detail'=>$plane->name]);
});

$layout->add(['Button', 'Show $plane in a dialog', 'icon'=>'clone'])->on('click', new \atk4\ui\jsModal('Plane Box', $vp));

$layout->add(['Header', 'All components extend View (even paginator)']);
$columns = $layout->add('Columns');

$columns->addColumn()->add(['Button', 'Button'])->addClass('green');
$columns->addColumn()->add(['Header', 'Header'])->addClass('green');
$columns->addColumn()->add(['Label', 'Label'])->addClass('green');
$columns->addColumn()->add(['Message', 'Message'])->addClass('green');
$columns->addColumn()->add(['Paginator', 'total'=>3, 'reload'=>$columns])->addClass('green');

$layout->add(['Header', 'Can have a custom render logic']);
$layout->add('Table')->addclass('green')->setSource(['One', 'Two', 'Three']);
