<?php

declare(strict_types=1);

namespace atk4\ui\demo;

use atk4\data\Model\Scope;
use atk4\data\Model\Scope\Condition;
use atk4\ui\Header;
use atk4\ui\Message;

/** @var \atk4\ui\App $app */
require_once __DIR__ . '/../init-app.php';

$model = new Stat($app->db, ['caption' => 'Demo Stat']);

// Display: Project Name matches regular expression [a-zA-Z]
$project = new Condition('project_name', Condition::OPERATOR_REGEXP, '[a-zA-Z]');
// Display: Client Country Iso equals Brazil => ToWord: Client Country Iso is equal to 'Brazil'
$brazil = new Condition('client_country_iso', Condition::OPERATOR_EQUALS, 'Brazil');
// Display: Start Date is on Oct 22, 2020 => Toword: Start Date is equal to '2020-10-22'
$start = new Condition('start_date', Condition::OPERATOR_EQUALS, '2020-10-22');
// Display: Finish Time is not on 22:22 => ToWord: Finish Time is not equal to '22:22'
$finish = new Condition('finish_time', Condition::OPERATOR_DOESNOT_EQUAL, '22:22');
// Display: Is Commercial (No) => ToWord: Is Commercial is equal to '0'
$isCommercial = new Condition('is_commercial', '0');
$budget = new Condition('project_budget', Condition::OPERATOR_GREATER_EQUAL, '1000');

$scope = Scope::createAnd($project, $brazil, $start);
$orScope = Scope::createOr($finish, $isCommercial);

$model->addCondition($budget);
$model->scope()->add($scope);
$model->scope()->add($orScope);

$expected = 'Project Budget is greater or equal to \'1000\' and '
        . '(Project Name is regular expression \'[a-zA-Z]\' '
        . 'and Client Country Iso is equal to \'Brazil\' '
        . 'and Start Date is equal to \'2020-10-22\') '
        . 'and (Finish Time is not equal to \'22:22\' '
        . 'or Is Commercial is equal to \'0\')';

Header::addTo($app, ['Expected result:']);
$result = Message::addTo($app)->addClass('atk-expected-result');
$result->text->addHTML($expected);

$form = \atk4\ui\Form::addTo($app);

$form->addControl('qb', [\atk4\ui\Form\Control\ScopeBuilder::class, 'model' => $model]);

$form->onSubmit(function ($form) use ($model) {
    $message = $form->model->get('qb')->toWords($model);
    $view = new \atk4\ui\Message('');
    $view->invokeInit();

    $view->text->addHTML($message);

    return $view;
});
