<?php

require 'init.php';
require 'database.php';

$user = new LUser($db);

$f = $app->add('Form');

$ml = $f->addField('ml', [new \atk4\invoice\FormField\MultiLine()]);
$ml->setModel($user, ['name', 'is_vip']);

$f->onSubmit(function ($f) use ($ml) {
    $ml->saveRows();

    return new \atk4\ui\jsToast('Saved!');
});
