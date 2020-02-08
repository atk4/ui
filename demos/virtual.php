<?php

include __DIR__ . '/init.php';

$vp = $layout->add('VirtualPage');
$vp->add('LoremIpsum');
$vp->ui = 'red inverted segment';

$label = $layout->add('Label');

$label->detail = $vp->cb->getURL();
$label->link($vp->cb->getURL());

$label = $layout->add(['Label', 'Callback test']);

$label->on('click', function ($j, $arg1) {
    return 'width is '.$arg1;
}, [new \atk4\ui\jsExpression('$(window).width()')]);
