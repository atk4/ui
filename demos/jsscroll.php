<?php

require 'init.php';
require 'database.php';

//$container = $app->add('View')->addClass('');//->setStyle('height', '300px')->addStyle('overflow-y', 'scroll');
$container = $app->add('View');

$v = $container->add(['View', 'template' => new \atk4\ui\Template('
<div class="ui header">Top countries (alphabetically)</div>
{List}<div class="ui segment" style="height: 60px"><i class="{iso}ae{/} flag"></i> {name}andorra{/}</div>{/}
</div>')]);

$l = $v->add('Lister', 'List')->addHook('beforeRow', function ($l) {
    $l->current_row['iso'] = strtolower($l->current_row['iso']);
});
//
$m = $l->setModel(new Country($db))->setLimit(4);

$l->addJsScroll(20, $container);

//$t = $l->renderJSON();

//$s = 's';

//$ipp = $v->add(new atk4\ui\ItemsPerPageSelector(['label' => 'Select how many countries:', 'pageLengthItems' => [12, 24, 36]]), 'Content');
//
//$ipp->onPageLengthSelect(function ($ipp) use ($m, $container) {
//    $m->setLimit($ipp);
//
//    return $container;
//});

/*
//$app->add(['Header', 'Button reloading segment']);
//$v = $app->add(['View', 'ui' => 'segment'])->set((string) rand(1, 100));
//$app->add(['Button', 'Reload random number'])->js('click', new \atk4\ui\jsReload($v, [], new \atk4\ui\jsExpression('console.log("Output with afterSuccess");')));
//
//
//$app->add(['Header', 'Infinite scrolling']);
//
//
////$v = $app->add('View')->addClass('ui segment')->setStyle('height', '300px')->addStyle('overflow-y', 'scroll');
////$vin = $v->add(['View', 'allo'])->setStyle('height', '600px');
//
//$v = $app->add('View')->addClass('ui segment')->setStyle('height', '1200px');
//
//
//if (@$_GET['page'] === '1') {
//    $app->terminate((new \atk4\ui\View())->renderJSON());
//}
//
//$v->js(true)->atkScroll(['uri'=>$v->url(['page'=>1]), 'padding' => 10]);

*/
