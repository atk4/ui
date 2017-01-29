<?php

require '../vendor/autoload.php';

$app = new \atk4\ui\App('Hello World', ['icon'=>'user']);
$app->initLayout('Centered');
$layout = $app->layout;

$img = 'https://github.com/atk4/ui/raw/07208a0af84109f0d6e3553e242720d8aeedb784/public/logo.png';

