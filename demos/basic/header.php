<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

$img = 'https://raw.githubusercontent.com/atk4/ui/2.0.4/public/logo.png';

$seg = \Atk4\Ui\View::addTo($app, ['ui' => 'segment']);
\Atk4\Ui\Header::addTo($seg, ['H1 Header', 'size' => 1]);
\Atk4\Ui\Header::addTo($seg, ['H2 Header', 'size' => 2]);
\Atk4\Ui\Header::addTo($seg, ['H3 Header', 'size' => 3]);
\Atk4\Ui\Header::addTo($seg, ['H4 Header', 'size' => 4]);
\Atk4\Ui\Header::addTo($seg, ['H5 Header', 'size' => 5, 'dividing']);
\Atk4\Ui\View::addTo($seg, ['element' => 'P'])->set('This is a following paragraph of text');

\Atk4\Ui\Header::addTo($seg, ['H1', 'size' => 1, 'subHeader' => 'H1 subheader']);
\Atk4\Ui\Header::addTo($seg, ['H5', 'size' => 5, 'subHeader' => 'H5 subheader']);

$seg = \Atk4\Ui\View::addTo($app, ['ui' => 'segment']);
\Atk4\Ui\Header::addTo($seg, ['Huge Header', 'size' => 'huge']);
\Atk4\Ui\Header::addTo($seg, ['Large Header', 'size' => 'large']);
\Atk4\Ui\Header::addTo($seg, ['Medium Header', 'size' => 'medium']);
\Atk4\Ui\Header::addTo($seg, ['Small Header', 'size' => 'small']);
\Atk4\Ui\Header::addTo($seg, ['Tiny Header', 'size' => 'tiny']);

\Atk4\Ui\Header::addTo($seg, ['Sub Header', 'sub']);

$seg = \Atk4\Ui\View::addTo($app, ['ui' => 'segment']);
\Atk4\Ui\Header::addTo($seg, ['Header with icon', 'icon' => 'settings']);
\Atk4\Ui\Header::addTo($seg, ['Header with icon', 'icon' => 'settings', 'subHeader' => 'and with sub-header']);
\Atk4\Ui\Header::addTo($seg, ['Header with image', 'image' => $img, 'subHeader' => 'and with sub-header']);

$seg = \Atk4\Ui\View::addTo($app, ['ui' => 'segment']);
\Atk4\Ui\Header::addTo($seg, ['Center-aligned', 'aligned' => 'center', 'icon' => 'settings', 'subHeader' => 'header with icon']);

$seg = \Atk4\Ui\View::addTo($app, ['ui' => 'segment']);
\Atk4\Ui\Header::addTo($seg, ['Center-aligned', 'aligned' => 'center', 'icon' => 'circular users', 'subHeader' => 'header with icon']);

$seg = \Atk4\Ui\View::addTo($app, ['ui' => 'segment']);
\Atk4\Ui\Header::addTo($seg, ['Center-aligned', 'aligned' => 'center', 'image' => $img, 'subHeader' => 'header with image']);

$seg = \Atk4\Ui\View::addTo($app, ['ui' => 'segment']);
\Atk4\Ui\Header::addTo($seg, ['Center-aligned', 'aligned' => 'center', 'image' => [$img, 'disabled'], 'subHeader' => 'header with image']);
