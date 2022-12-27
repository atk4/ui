<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Data\Persistence;

// to use MySQL database:
//   1. copy this file to "db.php"
//   2. uncomment the line below (and update the configuration if needed)
//   3. remove the Sqlite code from the new file
// $db = new Persistence\Sql('mysql:dbname=atk4_test__ui;host=mysql', 'atk4_test', 'atk4_pass');

$sqliteFile = __DIR__ . '/_demo-data/db.sqlite';
if (!file_exists($sqliteFile)) {
    throw new \Exception('Sqlite database does not exist, create it first');
}
$db = new Persistence\Sql('sqlite:' . $sqliteFile);
unset($sqliteFile);
