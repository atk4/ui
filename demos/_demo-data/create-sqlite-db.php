<?php

declare(strict_types=1);

namespace atk4\ui\demo;

$srcFile = __DIR__ . '/dump.sql';
$destFile = __DIR__ . '/db.sqlite';

if (file_exists($destFile)) {
    unlink($destFile);
}
$db = new \SQLite3($destFile);

foreach (preg_split('~;\s*(\n\s*|$)~', file_get_contents($srcFile)) as $query) {
    if (preg_match('~^(CREATE TABLE[^()]+\()(.+)(\).*?)$~is', $query, $createMatches)) {
        $createRows = [];
        foreach (preg_split('~\s*,\s*\n\s*~', trim($createMatches[2])) as $row) {
            $row = preg_replace('~ \w*int(?:\(\d+\))~is', ' INTEGER', $row);
            $row = preg_replace('~ UNSIGNED~is', '', $row);
            $row = preg_replace('~ AUTO_INCREMENT~is', ' PRIMARY KEY AUTOINCREMENT', $row);
            $row = preg_replace('~ ENUM\((.+?)\)~is', ' TEXT', $row);

            if (!preg_match('~ INTEGER ~is', $row)) {
                $row = $row . ' COLLATE NOCASE';
            }

            if (!preg_match('~^(PRIMARY )?KEY ~is', $row)) {
                $createRows[] = $row;
            }
        }

        $createMatches[3] = preg_replace('~(?<!\w)ENGINE=InnoDB(?!\w)~is', '', $createMatches[3]);
        $query = $createMatches[1] . "\n    " . implode(",\n    ", $createRows) . "\n" . $createMatches[3];

        // echo $query . "\n\n";
    }

    $query = preg_replace('~\\\\\'~is', '\'\'', $query);
    $db->exec($query);
}
