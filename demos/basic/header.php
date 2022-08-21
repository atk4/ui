<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\Header;
use Atk4\Ui\View;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

$img = $app->cdn['atk'] . '/logo.png';

$seg = View::addTo($app, ['ui' => 'segment']);
Header::addTo($seg, ['H1 Header', 'size' => 1]);
Header::addTo($seg, ['H2 Header', 'size' => 2]);
Header::addTo($seg, ['H3 Header', 'size' => 3]);
Header::addTo($seg, ['H4 Header', 'size' => 4]);
Header::addTo($seg, ['H5 Header', 'size' => 5, 'class.dividing' => true]);
View::addTo($seg, ['element' => 'P'])->set('This is a following paragraph of text');

Header::addTo($seg, ['H1', 'size' => 1, 'subHeader' => 'H1 subheader']);
Header::addTo($seg, ['H5', 'size' => 5, 'subHeader' => 'H5 subheader']);

$seg = View::addTo($app, ['ui' => 'segment']);
Header::addTo($seg, ['Huge Header', 'size' => 'huge']);
Header::addTo($seg, ['Large Header', 'size' => 'large']);
Header::addTo($seg, ['Medium Header', 'size' => 'medium']);
Header::addTo($seg, ['Small Header', 'size' => 'small']);
Header::addTo($seg, ['Tiny Header', 'size' => 'tiny']);

Header::addTo($seg, ['Sub Header', 'class.sub' => true]);

$seg = View::addTo($app, ['ui' => 'segment']);
Header::addTo($seg, ['Header with icon', 'icon' => 'settings']);
Header::addTo($seg, ['Header with icon', 'icon' => 'settings', 'subHeader' => 'and with sub-header']);
Header::addTo($seg, ['Header with image', 'image' => $img, 'subHeader' => 'and with sub-header']);

$seg = View::addTo($app, ['ui' => 'segment']);
Header::addTo($seg, ['Center-aligned', 'aligned' => 'center', 'icon' => 'settings', 'subHeader' => 'header with icon']);

$seg = View::addTo($app, ['ui' => 'segment']);
Header::addTo($seg, ['Center-aligned', 'aligned' => 'center', 'icon' => 'circular users', 'subHeader' => 'header with icon']);

$seg = View::addTo($app, ['ui' => 'segment']);
Header::addTo($seg, ['Center-aligned', 'aligned' => 'center', 'image' => $img, 'subHeader' => 'header with image']);

$seg = View::addTo($app, ['ui' => 'segment']);
Header::addTo($seg, ['Center-aligned', 'aligned' => 'center', 'image' => [$img, 'class.disabled' => true], 'subHeader' => 'header with image']);
