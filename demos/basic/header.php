<?php

chdir('..');

require_once 'atk-init.php';$img = 'https://raw.githubusercontent.com/atk4/ui/07208a0af84109f0d6e3553e242720d8aeedb784/public/logo.png';

$seg = \atk4\ui\View::addTo($app, ['ui' => 'segment']);
\atk4\ui\Header::addTo($seg, ['H1 Header', 'size' => 1]);
\atk4\ui\Header::addTo($seg, ['H2 Header', 'size' => 2]);
\atk4\ui\Header::addTo($seg, ['H3 Header', 'size' => 3]);
\atk4\ui\Header::addTo($seg, ['H4 Header', 'size' => 4]);
\atk4\ui\Header::addTo($seg, ['H5 Header', 'size' => 5, 'dividing']);
\atk4\ui\View::addTo($seg, ['element' => 'P'])->set('This is a following paragraph of text');

\atk4\ui\Header::addTo($seg, ['H1', 'size' => 1, 'subHeader' => 'H1 subheader']);
\atk4\ui\Header::addTo($seg, ['H5', 'size' => 5, 'subHeader' => 'H5 subheader']);

$seg = \atk4\ui\View::addTo($app, ['ui' => 'segment']);
\atk4\ui\Header::addTo($seg, ['Huge Header', 'size' => 'huge']);
\atk4\ui\Header::addTo($seg, ['Large Header', 'size' => 'large']);
\atk4\ui\Header::addTo($seg, ['Medium Header', 'size' => 'medium']);
\atk4\ui\Header::addTo($seg, ['Small Header', 'size' => 'small']);
\atk4\ui\Header::addTo($seg, ['Tiny Header', 'size' => 'tiny']);

\atk4\ui\Header::addTo($seg, ['Sub Header', 'sub']);

$seg = \atk4\ui\View::addTo($app, ['ui' => 'segment']);
\atk4\ui\Header::addTo($seg, ['Header with icon', 'icon' => 'settings']);
\atk4\ui\Header::addTo($seg, ['Header with icon', 'icon' => 'settings', 'subHeader' => 'and with sub-header']);
\atk4\ui\Header::addTo($seg, ['Header with image', 'image' => $img, 'subHeader' => 'and with sub-header']);

$seg = \atk4\ui\View::addTo($app, ['ui' => 'segment']);
\atk4\ui\Header::addTo($seg, ['Center-aligned', 'aligned' => 'center', 'icon' => 'settings', 'subHeader' => 'header with icon']);

$seg = \atk4\ui\View::addTo($app, ['ui' => 'segment']);
\atk4\ui\Header::addTo($seg, ['Center-aligned', 'aligned' => 'center', 'icon' => 'circular users', 'subHeader' => 'header with icon']);

$seg = \atk4\ui\View::addTo($app, ['ui' => 'segment']);
\atk4\ui\Header::addTo($seg, ['Center-aligned', 'aligned' => 'center', 'image' => $img, 'subHeader' => 'header with image']);

$seg = \atk4\ui\View::addTo($app, ['ui' => 'segment']);
\atk4\ui\Header::addTo($seg, ['Center-aligned', 'aligned' => 'center', 'image' => [$img, 'disabled'], 'subHeader' => 'header with image']);
