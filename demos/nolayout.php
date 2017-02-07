<?php
include '../vendor/autoload.php';

$b = new \atk4\ui\Button(['Hello', 'icon'=>'cubes']);

$b->js('click')->hide();

echo (htmlspecialchars($b->render()));
