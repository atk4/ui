<?php

require 'init.php';
$img = 'https://raw.githubusercontent.com/atk4/ui/07208a0af84109f0d6e3553e242720d8aeedb784/public/logo.png';

$app->add(['Header', 'Labels']);
$app->add(['Label', 'Hot!']);
$app->add(['Label', '23', 'icon' => 'mail']);
$app->add(['Label', 'new', 'iconRight' => 'delete']);

$app->add(['Label', 'Coded in PHP', 'image' => $img]);
$app->add(['Label', 'Number of lines', 'detail' => '33']);

$app->add(['Header', 'Combinations and Interraction']);
$del = $app->add(['Label', 'Zoe', 'image' => 'https://semantic-ui.com/images/avatar/small/ade.jpg', 'iconRight' => 'delete']);
$del->on('click', '.delete', $del->js()->fadeOut());

$val = isset($_GET['toggle']) && $_GET['toggle'];
$toggle = $app->add(['Label', 'icon' => 'toggle '.($val ? 'on' : 'off')])->set('Value: '.$val);
$toggle->on('click', new \atk4\ui\jsReload($toggle, ['toggle' => $val ? null : 1]));

$m = $app->add('Menu');
$m->addItem('Inbox')->add(['Label', '20', 'floating red']);
$m->addMenu('Others')->addItem('Draft')->add(['Label', '10', 'floating blue']);

$seg = $app->add(['View', 'ui' => 'segment']);
$seg->add(['Header', 'Label Group']);
$labels = $seg->add(['View', false, 'tag', 'ui' => 'labels']);
$seg->add(['Label', '$9.99']);
$seg->add(['Label', '$19.99']);
$seg->add(['Label', '$24.99']);

$columns = $app->add('Columns');

$c = $columns->addColumn();
$seg = $c->add(['View', 'ui' => 'raised segment']);
$seg->add(['Label', 'Left Column', 'top attached', 'icon' => 'book']);
$seg->add(['Label', 'Lorem', 'red ribbon', 'icon' => 'cut']);
$seg->add(['LoremIpsum', 'size' => 1]);

$c = $columns->addColumn();
$seg = $c->add(['View', 'ui' => 'raised segment']);
$seg->add(['Label', 'Right Column', 'top attached', 'icon' => 'book']);
$seg->add(['LoremIpsum', 'size' => 1]);
$seg->add(['Label', 'Ipsum', 'orange bottom right attached', 'icon' => 'cut']);
