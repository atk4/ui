<?php
/**
 * Demonstrates how to use menu.
 */
require 'init.php';

$m = $app->add('Menu');
$m->addItem('foo', 'foo.php');
$m->addItem('bar');
$m->addItem('baz');
$m->add(['Dropdown', 'huhhuh', 'js'=>['on'=>'hover']])->setSource(['a', 'b', 'c']);

$sm = $m->addMenu('Sub-menu');
$sm->addItem('one', 'one.php');
$sm->addItem(['two', 'label'=>'VIP', 'disabled']);

$sm = $sm->addMenu('Sub-menu');
$sm->addItem('one');
$sm->addItem('two');

$m = $app->add(['Menu', 'vertical pointing']);
$m->addItem(['Inbox', 'label'=>['123', 'teal left pointing']]);
$m->addItem('Spam');
$m->addItem()->add(new \atk4\ui\FormField\Input(['placeholder'=>'Search', 'icon'=>'search']))->addClass('transparent');

$m = $app->add(['Menu', 'secondary vertical pointing']);
$m->addItem(['Inbox', 'label'=>['123', 'teal left pointing']]);
$m->addItem('Spam');
$m->addItem()->add(new \atk4\ui\FormField\Input(['placeholder'=>'Search', 'icon'=>'search']))->addClass('transparent');
$m = $app->add(['Menu', 'vertical']);
$gr = $m->addGroup('Products');
$gr->addItem('Enterprise');
$gr->addItem('Consumer');

$gr = $m->addGroup('Hosting');
$gr->addItem('Shared');
$gr->addItem('Dedicated');

$m = $app->add(['Menu', 'vertical']);
$i = $m->addItem();
$i->add(['Header', 'size'=>4])->set('Promotions');
$i->add(['View', 'element'=>'P'])->set('Check out our promotions');

//$m = $app->add('Menu');
//$i->addItem()->add('FormField/Input');
//$i->add(['View', 'element'=>'P'])->set('Check out our promotions');
