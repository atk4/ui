<?php

include 'init.php';

$app->add('Header')->set('Getting Started with Agile UI Demo Suite');

$t = $app->add(['View', false, 'red',  'ui'=>'segment'])->add('Text');

$t->addParagraph('This is just collection of tests. You are welcome to explore the sources, but you will not find many comments. Use our main documentation if you are new to Agile UI.');

$app->add(['Button', 'See the sources', 'right labeled', 'icon'=>'right arrow'])->link('https://github.com/atk4/ui/tree/develop/demos');
$app->add(['Button', 'Visit AgileToolkit.org Website', 'right labeled', 'icon'=>'right arrow'])->link('http://agiletoolkit.org/');
$app->add(['Button', 'Open Documentation', 'right labeled', 'icon'=>'right arrow'])->link('http://agile-ui.readthedocs.io/');
