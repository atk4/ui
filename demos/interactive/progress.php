<?php
/**
 * Demonstrates how to use tabs.
 */
chdir('..');
require_once dirname(__DIR__) . '/atk-init.php';

$p = \atk4\ui\ProgressBar::addTo($app, [20]);

$p = \atk4\ui\ProgressBar::addTo($app, [60, 'indicating progress', 'indicating']);
\atk4\ui\Button::addTo($app, ['increment'])->on('click', $p->jsIncrement());
\atk4\ui\Button::addTo($app, ['set'])->on('click', $p->jsValue(20));
