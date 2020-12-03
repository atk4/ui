<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

/**
 * Demonstrates how to use tabs.
 */
/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

$p = \Atk4\Ui\ProgressBar::addTo($app, [20]);

$p = \Atk4\Ui\ProgressBar::addTo($app, [60, 'indicating progress', 'indicating']);
\Atk4\Ui\Button::addTo($app, ['increment'])->on('click', $p->jsIncrement());
\Atk4\Ui\Button::addTo($app, ['set'])->on('click', $p->jsValue(20));
