<?php

include_once __DIR__ . '/init.php';

$vp = \atk4\ui\VirtualPage::addTo($layout);
\atk4\ui\LoremIpsum::addTo($vp);
$vp->ui = 'red inverted segment';

$label = \atk4\ui\Label::addTo($layout);

$label->detail = $vp->cb->getURL();
$label->link($vp->cb->getURL());

$label = \atk4\ui\Label::addTo($layout, ['Callback test']);

$label->on('click', function ($j, $arg1) {
    return 'width is ' . $arg1;
}, [new \atk4\ui\jsExpression('$(window).width()')]);
