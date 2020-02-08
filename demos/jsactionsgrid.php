<?php

require_once __DIR__ . '/init.php';
require_once __DIR__ . '/database.php';

$app->add(['Button', 'Actions from jsEvent', 'small left floated basic blue', 'icon' => 'left arrow'])
    ->link(['jsactions2']);
$app->add(['View', 'ui' => 'ui clearing divider']);

$country = new Country($db);

$country->addAction('callback', ['callback'=> function ($m) {
    return 'ok '.$m->getTitle();
}]);

$country->addAction('preview', ['preview'=> function ($m) {
    return 'Previewing country '.$m->getTitle();
}, 'callback'=>function ($m) {
    return 'Done previewing '.$m->getTitle();
}]);

$country->addAction('disabled_action', ['enabled'=> false, 'callback'=>function () {
    return 'ok';
}]);

$country->addAction('edit_argument', ['args'=> ['age'=>['type'=>'integer', 'required' => true]], 'callback'=>function ($m, $age) {
    return 'Proper age to visit '.$m->getTitle().' is '.$age;
}]);

$country->addAction('edit_argument_prev', ['args'=> ['age'=>['type'=>'integer', 'required' => true]], 'preview'=> function ($m, $age) {
    return 'You age is: '.$age;
}, 'callback'=>function ($m, $age) {
    return 'age = '.$age;
}]);

$country->addAction('edit_iso', ['fields'=> ['iso3'], 'callback'=>function () {
    return 'ok';
}]);

$country->addAction('Ouch', ['args'=> ['age'=>['type'=>'integer']], 'preview'=> function () {
    return 'Be careful with this action.';
}, 'callback'=> function () {
    throw new \atk4\ui\Exception('Told you, didn\'t I?');
}]);
$country->addAction('confirm', ['ui' => ['confirm'=>'Call action?'], 'callback'=>function ($m) {
    return 'Confirm ok '.$m->getTitle();
}]);

$country->addAction('multi_step',
    [
        'args'  => [
            'age'    => ['type'=>'integer', 'required'=> true],
            'gender' => ['type'=> 'enum', 'values' => ['Male' => 'Male', 'Female' => 'Female'], 'required'=>true],
        ],
        'fields' => ['iso3'],
        'preview'=> function ($m, $age, $gender) {
            return 'Gender = '.$gender.' / Age = '.$age;
        },
        'callback'=> function ($m, $age, $gender) {
            return 'You are a '.$gender.' of age '.$age.' who want to visit '.$m->getTitle();
        },
    ]
);

$g = $app->add(['Grid', 'menu' => false]);
$g->setModel($country);

$divider = $app->factory('View', ['id' => false, 'class' => ['divider'], 'content' => ''], 'atk4\ui');

$model_header = $app->factory('View', ['id' => false, 'class' => ['header'], 'content' => 'Model Actions'], 'atk4\ui');
$model_header->add(['Icon', 'content' => 'database']);

$js_header = $app->factory('View', ['id' => false, 'class' => ['header'], 'content' => 'Js Actions'], 'atk4\ui');
$js_header->add(['Icon', 'content' => 'file code']);

$g->addActionMenuItem($js_header);
$g->addActionMenuItem('Js Callback', function () {
    return (new \atk4\ui\View())->set('Js Callback done!');
});

$g->addActionMenuItem($divider);

$g->addActionMenuItem($model_header);
$g->addActionMenuItems(
    [
        'callback',
        'preview',
        'disabled_action',
        'edit_argument',
        'edit_argument_prev',
        'edit_iso',
        'Ouch',
        'confirm',
    ]
);

$special_item = $app->factory('View', ['id' => false, 'class' => ['item'], 'content' => 'Multi Step'], 'atk4\ui');
$special_item->add(['Icon', 'content' => 'window maximize outline']);

$g->addActionMenuItem($special_item, $country->getAction('multi_step'));

$g->ipp = 10;
