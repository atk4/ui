<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\Button;
use Nyholm\Psr7\Factory\Psr17Factory;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

/**
 * Size in Mb of generated download file.
 * This can also be passed as GET['size_mb'] parameter.
 *
 * @var int
 */
$size_mb = (int) ($_GET['size_mb'] ?? 64);

$button = Button::addTo($app, ['Download', 'class.atk-test' => true]);
$button->on('click', function () use ($app, $size_mb) {
    // Generate big data and write it to a temporary file
    $pattern = str_repeat('0123456789ABCDEF', 65536); // 1Mb
    $total_size = strlen($pattern) * $size_mb;
    $tempFile = tempnam(sys_get_temp_dir(), 'test.txt');

    $fh = fopen($tempFile, 'w');
    for ($i = 0; $i < $size_mb; ++$i) {
        fwrite($fh, $pattern);
    }
    fclose($fh);

    // Send the data using a file stream response
    $factory = new Psr17Factory();
    $stream = $factory->createStreamFromFile($tempFile);
    $app->setResponseHeader('Content-Type', 'text/plain');
    $app->setResponseHeader('Content-Disposition', 'attachment; filename="test.txt"');
    $app->setResponseHeader('Content-Length', (string) $total_size);
    $app->terminate($stream);
});
