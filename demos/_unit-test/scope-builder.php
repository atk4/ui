<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Data\Model\Scope;
use Atk4\Data\Model\Scope\Condition;
use Atk4\Ui\Form;
use Atk4\Ui\Header;
use Atk4\Ui\View;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

$model = new Stat($app->db, ['caption' => 'Demo Stat']);

$project = new Condition($model->fieldName()->project_name, Condition::OPERATOR_REGEXP, '[a-zA-Z]');
$brazil = new Condition($model->fieldName()->client_country_iso, '=', 'BR');
$start = new Condition($model->fieldName()->start_date, '=', new \DateTime('2020-10-22'));
$finish = new Condition($model->fieldName()->finish_time, '!=', new \DateTime('22:22'));
$isCommercial = new Condition($model->fieldName()->is_commercial, true);
$budget = new Condition($model->fieldName()->project_budget, '>=', 1000);
$currency = new Condition($model->fieldName()->currency, 'USD');

$scope = Scope::createAnd($project, $brazil, $start);
$orScope = Scope::createOr($finish, $isCommercial, $currency);

$model->addCondition($budget);
$model->scope()->add($scope);
$model->scope()->add($orScope);

$form = Form::addTo($app);

$form->addControl('qb', [Form\Control\ScopeBuilder::class, 'model' => $model], ['type' => 'object']);

$form->onSubmit(static function (Form $form) use ($model) {
    $message = $form->model->get('qb')->toWords($model);
    $view = (new View(['name' => false]))->addClass('atk-scope-builder-response');
    $view->setApp($form->getApp());
    $view->invokeInit();

    $view->set($message);

    return $view;
});

$statModelForHinting = new Stat($app->db);
$budget1000Eur = "€\u{00a0}1\u{00a0}000.00";
$expectedInput = json_encode(json_decode(<<<"EOF"
    {
      "logicalOperator": "AND",
      "children": [
        {
          "type": "query-builder-rule",
          "query": {
            "rule": "{$statModelForHinting->fieldName()->project_budget}",
            "operator": ">=",
            "value": "{$budget1000Eur}",
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
                  "rule": "{$statModelForHinting->fieldName()->project_name}",
                  "operator": "matches regular expression",
                  "value": "[a-zA-Z]",
                  "option": null
                }
              },
              {
                "type": "query-builder-rule",
                "query": {
                  "rule": "{$statModelForHinting->fieldName()->client_country_iso}",
                  "operator": "equals",
                  "value": "BR",
                  "option": {
                    "key": "BR",
                    "text": "Brazil",
                    "value": "BR"
                  }
                }
              },
              {
                "type": "query-builder-rule",
                "query": {
                  "rule": "{$statModelForHinting->fieldName()->start_date}",
                  "operator": "is on",
                  "value": "Oct 22, 2020",
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
                  "rule": "{$statModelForHinting->fieldName()->finish_time}",
                  "operator": "is not on",
                  "value": "22:22",
                  "option": null
                }
              },
              {
                "type": "query-builder-rule",
                "query": {
                  "rule": "{$statModelForHinting->fieldName()->is_commercial}",
                  "operator": "equals",
                  "value": "Yes",
                  "option": null
                }
              },
              {
                "type": "query-builder-rule",
                "query": {
                  "rule": "{$statModelForHinting->fieldName()->currency}",
                  "operator": "equals",
                  "value": "USD",
                  "option": null
                }
              }
            ]
          }
        }
      ]
    }
    EOF, true), \JSON_UNESCAPED_UNICODE);

Header::addTo($app, ['Input:']);
View::addTo($app, ['element' => 'p', 'content' => $expectedInput])->addClass('atk-expected-input-result');

$expectedWord = <<<'EOF'
    Project Budget is greater or equal to '{$budget1000Eur}'
    and (Project Name is regular expression '[a-zA-Z]'
    and Client Country Iso is equal to 'BR' ('Brazil') and Start Date is equal to 'Oct 22, 2020')
    and (Finish Time is not equal to '22:22' or Is Commercial is equal to 'Yes' or Currency is equal to 'USD')
    EOF;

Header::addTo($app, ['Word:']);
View::addTo($app, ['element' => 'p', 'content' => $expectedWord])->addClass('atk-expected-word-result');
