<?php

chdir('..');
require_once 'init.php';

// Next line produces exception, which Agile UI will catch and display nicely.
\atk4\ui\View::addTo($app, ['foo' => 'bar']);
