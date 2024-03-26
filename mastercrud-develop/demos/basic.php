<?php

declare(strict_types=1);

namespace Atk4\MasterCrud\Demo;

include 'init.php';

use Atk4\MasterCrud\MasterCRUD;
use Atk4\Ui\Crud;

$app->cdn['atk'] = '../public';
$mc = $app->add([
    MasterCRUD::class,
    'ipp' => 5,
    'quickSearch' => ['name'],
]);
$mc->setModel(
    new Client($app->db),
    [
        'Invoices' => [
            'Lines' => [
                ['_crud' => [Crud::class, 'displayFields' => ['item', 'total']]],
            ],
            'Allocations' => [],
        ],
        'Payments' => [
            'Allocations' => [],
        ],
    ]
);
