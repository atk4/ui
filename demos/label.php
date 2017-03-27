<?php

require 'init.php';
$img = 'https://github.com/atk4/ui/raw/07208a0af84109f0d6e3553e242720d8aeedb784/public/logo.png';

$layout->add(['Header', 'Labels']);
$layout->add(['Label', 'Hot!']);
$layout->add(['Label', '23', 'icon'=>'mail']);
$layout->add(['Label', 'new', 'iconRight'=>'delete']);

$layout->add(['Label', 'Coded in PHP', 'image'=>$img]);
$layout->add(['Label', 'Number of lines', 'detail'=>'33']);

$layout->add(['Header', 'Combinations and Interraction']);
$del = $layout->add(['Label', 'Zoe', 'image'=>'https://semantic-ui.com/images/avatar/small/ade.jpg', 'iconRight'=>'delete']);
$del->on('click', '.delete', $del->js()->fadeOut());

$val = isset($_GET['toggle']) && $_GET['toggle'];
$toggle = $layout->add(['Label', 'icon'=>'toggle '.($val?'on':'off')])->set('Value: '.$val);
$toggle->on('click', new \atk4\ui\jsReload($toggle, ['toggle'=>$val?null:1]));

$m = $layout->add('Menu');
$m->addItem('Inbox')->add(['Label', '20', 'floating red']);
$m->addMenu('Others')->addItem('Draft')->add(['Label', '10', 'floating blue']);

$seg = $layout->add(['View', 'ui'=>'segment']);
$seg->add(['Header', 'Label Group']);
$labels = $seg->add(['View', false, 'tag', 'ui'=>'labels']);
$seg->add(['Label', '$9.99']);
$seg->add(['Label', '$19.99']);
$seg->add(['Label', '$24.99']);

$columns = $layout->add('Columns');

$c = $columns->addColumn();
$seg = $c->add(['View', 'ui'=>'raised segment']);
$seg->add(['Label', 'Left Column', 'top attached', 'icon'=>'book']);
$seg->add(['Label', 'Lorem', 'red ribbon', 'icon'=>'cut']);
$seg->add(['LoremIpsum', 'size'=>1]);

$c = $columns->addColumn();
$seg = $c->add(['View', 'ui'=>'raised segment']);
$seg->add(['Label', 'Right Column', 'top attached', 'icon'=>'book']);
$seg->add(['LoremIpsum', 'size'=>1]);
$seg->add(['Label', 'Ipsum', 'orange bottom right attached', 'icon'=>'cut']);
