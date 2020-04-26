<?php

require_once __DIR__ . '/../atk-init.php';
require_once __DIR__ . '/../_includes/country_actions.php';

/**
 * Demo for Model action in Grid
 * Action definition for Country model is located in country_actions.php
 */

\atk4\ui\Button::addTo($app, ['Actions from jsEvent', 'small left floated basic blue', 'icon' => 'left arrow'])
    ->link(['jsactions2']);
\atk4\ui\View::addTo($app, ['ui' => 'ui clearing divider']);

\atk4\ui\Header::addTo($app, ['Model Custom Actions', 'subHeader' => 'Model custom action can be execute from Grid.']);


$g = \atk4\ui\Grid::addTo($app, ['menu' => false]);
$g->setModel($country);

$divider = $app->factory('View', ['id' => false, 'class' => ['divider'], 'content' => ''], 'atk4\ui');

$model_header = $app->factory('View', ['id' => false, 'class' => ['header'], 'content' => 'Model Actions'], 'atk4\ui');
\atk4\ui\Icon::addTo($model_header, ['content' => 'database']);

$js_header = $app->factory('View', ['id' => false, 'class' => ['header'], 'content' => 'Js Actions'], 'atk4\ui');
\atk4\ui\Icon::addTo($js_header, ['content' => 'file code']);

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
\atk4\ui\Icon::addTo($special_item, ['content' => 'window maximize outline']);

$g->addActionMenuItem($special_item, $country->getAction('multi_step'));

$g->ipp = 10;
