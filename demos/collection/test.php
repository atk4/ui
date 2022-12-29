<?php
/* TODO - merge this into /demos/interactive/cardtable.php */
declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\CardTable;
use Atk4\Ui\Table;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

$m = new Country($app->db);
$m->addField('flag', [
    'neverPersist' => true, // no need for actual value in this field
    'ui' => [
        'table' => [
            Table\Column\CountryFlag::class,
            [
                'codeField' => $m->fieldName()->iso,
                'nameField' => $m->fieldName()->name,
            ],
        ],
    ],
]);

$e = $m->loadAny();
$t = CardTable::addTo($app);
$t->setModel($e);
