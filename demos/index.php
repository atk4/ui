<?php

include 'init.php';

$layout->add('Header')->set('Getting Started with Agile UI Demo Suite');

$t = $layout->add(['View', 'red'=>true,  'ui'=>'segment'])->add('Text');

$t->addParagraph('This is just collection of tests. You are welcome to explore the sources, but you will not find many comments. Use our main documentation if you are new to Agile UI.');

$layout->add('Button')->set(['See the sources', 'right labeled', 'icon'=>'right arrow'])->link('https://github.com/atk4/ui/tree/develop/demos');
$layout->add('Button')->set(['Visit AgileToolkit.org Website', 'right labeled', 'icon'=>'right arrow'])->link('http://agiletoolkit.org/');
$layout->add('Button')->set(['Open Documentation', 'right labeled', 'icon'=>'right arrow'])->link('http://agile-ui.readthedocs.io/');
