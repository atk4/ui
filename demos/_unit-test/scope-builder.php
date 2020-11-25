<?php

declare(strict_types=1);

namespace atk4\ui\demo;

use atk4\data\Model\Scope;
use atk4\data\Model\Scope\Condition;
use atk4\ui\Header;
use atk4\ui\View;

/** @var \atk4\ui\App $app */
require_once __DIR__ . '/../init-app.php';

$model = new Stat($app->db, ['caption' => 'Demo Stat']);

$project = new Condition('project_name', Condition::OPERATOR_REGEXP, '[a-zA-Z]');
$brazil = new Condition('client_country_iso', '=', '30');
$start = new Condition('start_date', '=', '2020-10-22');
$finish = new Condition('finish_time', '!=', '22:22');
$isCommercial = new Condition('is_commercial', '0');
$budget = new Condition('project_budget', '>=', '1000');

$scope = Scope::createAnd($project, $brazil, $start);
$orScope = Scope::createOr($finish, $isCommercial);

$model->addCondition($budget);
$model->scope()->add($scope);
$model->scope()->add($orScope);

$form = \atk4\ui\Form::addTo($app);

$form->addControl('qb', [\atk4\ui\Form\Control\ScopeBuilder::class, 'model' => $model]);

$form->onSubmit(function ($form) use ($model) {
    $message = $form->model->get('qb')->toWords($model);
    $view = new \atk4\ui\Message('');
    $view->invokeInit();

    $view->text->addHTML($message);

    return $view;
});

$expectedWord = <<<'EOF'
     Project Budget is greater or equal to '1000' 
     and (Project Name is regular expression '[a-zA-Z]' 
            and Client Country Iso is equal to '30' 
            and Start Date is equal to '2020-10-22') 
    and (Finish Time is not equal to '22:22' or Is Commercial is equal to '0')
    EOF;

$expectedInput = <<< 'EOF'
    {
      "logicalOperator": "AND",
      "children": [
        {
          "type": "query-builder-rule",
          "query": {
            "rule": "project_budget",
            "operator": ">=",
            "value": "1000",
            "option": null
          }
        },
        {
          "type": "query-builder-group",
          "query": {
            "logicalOperator": "AND",
            "children": [
              {
                "type": "query-builder-rule",
                "query": {
                  "rule": "project_name",
                  "operator": "matches regular expression",
                  "value": "[a-zA-Z]",
                  "option": null
                }
              },
              {
                "type": "query-builder-rule",
                "query": {
                  "rule": "client_country_iso",
                  "operator": "equals",
                  "value": "30",
                  "option": {
                    "key": "30",
                    "text": "Brazil",
                    "value": "30"
                  }
                }
              },
              {
                "type": "query-builder-rule",
                "query": {
                  "rule": "start_date",
                  "operator": "is on",
                  "value": "2020-10-22",
                  "option": null
                }
              }
            ]
          }
        },
        {
          "type": "query-builder-group",
          "query": {
            "logicalOperator": "OR",
            "children": [
              {
                "type": "query-builder-rule",
                "query": {
                  "rule": "finish_time",
                  "operator": "is not on",
                  "value": "22:22",
                  "option": null
                }
              },
              {
                "type": "query-builder-rule",
                "query": {
                  "rule": "is_commercial",
                  "operator": "is exactly",
                  "value": "0",
                  "option": null
                }
              }
            ]
          }
        }
      ]
    }
    EOF;

Header::addTo($app, ['Word:']);
View::addTo($app, ['element' => 'p', 'content' => $expectedWord])->addClass('atk-expected-word-result');

Header::addTo($app, ['Input:']);
View::addTo($app, ['element' => 'p', 'content' => $expectedInput])->addClass('atk-expected-input-result');
