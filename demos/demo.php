<html>
  <head>
    <!-- Standard Meta-->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <!-- Site Properties-->
    <title>Agile UI - Button Test</title>
    <link rel="stylesheet" type="text/css" href="http://semantic-ui.com/dist/semantic.css">
    <script src="https://code.jquery.com/jquery-3.1.1.js"></script>
    <script src="http://semantic-ui.com/dist/semantic.js"></script>
  </head>
  <body>

<?php

require '../vendor/autoload.php';

$button = new \atk4\ui\Button('hello');
$button->init();
echo $button->render();

exit;

$app = new \atk4\ui\App('Hello');

$layout = new \atk4\ui\Layout\Centered();

$layout->add(new \atk4\ui\Button(['PHP7 Rocks', 'icon'=>'book', 'blue']));

$app->setLayout($layout);
$app->run();

    //\atk4\ui\Layout\Centered();
