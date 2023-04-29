<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\Form;
use Atk4\Ui\Grid;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

$q = [
    'logicalOperator' => 'AND',
    'children' => [
        [
            'type' => 'query-builder-rule',
            'query' => [
                'rule' => Product::hinting()->fieldName()->product_category_id,
                'operator' => 'equals',
                'operand' => 'Product Category Id',
                'value' => '3',
            ],
        ],
        [
            'type' => 'query-builder-rule',
            'query' => [
                'rule' => Product::hinting()->fieldName()->product_sub_category_id,
                'operator' => 'equals',
                'operand' => 'Product Sub Category Id',
                'value' => '6',
            ],
        ],
    ],
];
$scope = (new Form\Control\ScopeBuilder())->queryToScope($q);

$product = new Product($app->db);

$g = Grid::addTo($app);
$g->setModel($product->addCondition($scope));
