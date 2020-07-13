<?php

declare(strict_types=1);

namespace atk4\ui\demo;

// to use MySQL database:
//   1. copy this file to "db.php"
//   2. uncomment the line below (and update the configuration if needed)
//   3. remove the Sqlite code from the new file
// $db = new \atk4\data\Persistence\Sql('mysql:dbname=atk4_test__ui;host=mysql', 'atk4_test', 'atk4_pass');

$sqliteFile = __DIR__ . '/_demo-data/db.sqlite';
if (!file_exists($sqliteFile)) {
    throw new \Exception('Sqlite database does not exist, create it first.');
}
$db = new \atk4\data\Persistence\Sql('sqlite:' . $sqliteFile);
unset($sqliteFile);
