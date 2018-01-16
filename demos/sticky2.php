<?php

require 'init.php';

$b = $app->add(['Button', 'b1='.@$_GET['b1']]);
$b->link(['b1'=>$b->stickyGet('b1') + 1]);

$b = $app->add(['Button', 'b2='.@$_GET['b2']]);
$b->link(['b2'=>$b->stickyGet('b2') + 1]);
