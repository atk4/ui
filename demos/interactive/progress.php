<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\Button;
use Atk4\Ui\ProgressBar;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

$p = ProgressBar::addTo($app, [20]);

$p = ProgressBar::addTo($app, [60, 'indicating progress', 'class.indicating' => true]);
Button::addTo($app, ['increment'])
    ->on('click', $p->jsIncrement());
Button::addTo($app, ['set'])
    ->on('click', $p->jsValue(20));
