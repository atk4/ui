<?php

include 'init.php';

// Next line produces exception, which Agile UI will catch and display nicely.
$layout->add(new \atk4\ui\View(['foo'=>'bar']));
