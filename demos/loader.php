<?php
require 'init.php';

$s = $app->add(['ui'=>'green segment'])->setStyle('min-height', '20em');
$b = $app->add(['Button','Click me']);
$b->on('click', new \atk4\ui\jsReload($s, ['a'=>'yes']));

if (isset($_GET['a'])) {
    $s->add('LoremIpsum');

    // this triggers issue #234
    new blabalba();
} else {
    $s->add(['ui'=>'active dimmer'])->add(['ui'=>'active text loader'])->set('Loading.. Click button to load');
}
