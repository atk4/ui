<?php

include 'init.php';

// Next line produces exception, which Agile UI will catch and display nicely.
$app->add(new \atk4\ui\View(['foo'=>'bar']));
