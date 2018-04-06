<?php

require 'init.php';

$app->add('Header')->set('Menu popup');

$m = $app->add('Menu');
$browse = $m->add(['DropDown', 'Browse']);
$browse->setSource([]);
$m->addItem('Cart')->add('Icon')->set('cart');


$pop = $app->add('Popup', ['triggerBy' => $browse, 'position' => 'bottom left'])
           ->setHoverable()
           ->setOption('delay', ['show' => 100, 'hide' => 400]);

$v= $pop->add('View', ['ui'=>'fluid']);
$cols = $v->add('Columns', ['ui' => 'relaxed divided grid']);

$c1 = $cols->addColumn();
$c1->add('Header', ['size' => 5])->set('Fabrics');
$l1 = $c1->add('View', ['ui' => 'list']);
$l1->add('Item', ['content'=>'Cahsmere', 'ui' => 'item'])->setElement('a');
$l1->add('Item', ['content'=>'Linen', 'ui' => 'item'])->setElement('a');
$l1->add('Item', ['content'=>'Cotton', 'ui' => 'item'])->setElement('a');
$l1->add('Item', ['content'=>'Viscose', 'ui' => 'item'])->setElement('a');

$c2 = $cols->addColumn();
$c2->add('Header', ['size' => 5])->set('Size');
$l2 = $c2->add('View', ['ui' => 'list']);
$l2->add('Item', ['content'=>'Small', 'ui' => 'item'])->setElement('a');
$l2->add('Item', ['content'=>'Medium', 'ui' => 'item'])->setElement('a');
$l2->add('Item', ['content'=>'Large', 'ui' => 'item'])->setElement('a');
$l2->add('Item', ['content'=>'Plus', 'ui' => 'item'])->setElement('a');

$c3 = $cols->addColumn();
$c3->add('Header', ['size' => 5])->set('Colors');
$l3 = $c3->add('View', ['ui' => 'list']);
$l3->add('Item', ['content'=>'Neutrals', 'ui' => 'item'])->setElement('a');
$l3->add('Item', ['content'=>'Brights', 'ui' => 'item'])->setElement('a');
$l3->add('Item', ['content'=>'Pastels', 'ui' => 'item'])->setElement('a');

//////////////////////////////////////////////////////////////////////////////

$app->add('Header')->set('Specifying trigger');

$button = $app->add(['Button', 'Click Me', 'primary']);

$b_pop = $app->add('Popup',  ['triggerBy' => $button, 'triggerOn' => 'click']);
$b_pop->add('Header', ['size'=> 5])->set('Using click events');
$b_pop->add('View')->set('Clicked popups will close if you click away, but not if you click inside it.');

$input = $app->add(new \atk4\ui\FormField\Line(['placeholder' => 'Search users', 'icon' => 'circular search link']));

$i_pop = $app->add('Popup', ['triggerBy' => $input, 'triggerOn' => 'focus']);
$i_pop->add('View')->set('You can use this field to search data.');

//$button = $app->add('Button', ['attr' => ['data-content'=>'Click here an nothing happen']])
//    ->set('Click')
//    ->js(true)
//    ->popup();


///



//$btn = $app->add('Button', ['content'=> 'allo']);




