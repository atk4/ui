<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\Button;
use Nyholm\Psr7\Factory\Psr17Factory;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

$button = Button::addTo($app, ['Download', 'class.atk-test' => true]);
$button->on('click', function() use ($app) {
    // Generate big data and write it to a temporary file
    $pattern = str_repeat('0123456789ABCDEF', 65536); // 1Mb
    $chunks = 128; // 1 chunk = 1Mb, 1024 chunks = 1Gb etc
    $tempFile = tempnam(sys_get_temp_dir(), 'test.txt');

    $fh = fopen($tempFile, 'w');
    for ($i = 0; $i < $chunks; ++$i) {
        fwrite($fh, $pattern);
    }
    fclose($fh);

    // Send the data using a file stream response
    $factory = new Psr17Factory();
    $stream = $factory->createStreamFromFile($tempFile);
    $app->setResponseHeader('Content-Type', 'text/plain');
    $app->setResponseHeader('Content-Disposition', 'attachment; filename="test.txt"');
    $app->setResponseHeader('Content-Length', (string) (strlen($pattern) * $chunks));
    $app->terminate($stream);
});
