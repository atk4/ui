<?php
include'init.php';

$g = $layout->add('Grid');
$g->setModel(new SomeData());
$g->addColumn('name');
$g->addColumn('surname');
$g->addColumn('date');
