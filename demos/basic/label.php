<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

$img = 'https://raw.githubusercontent.com/atk4/ui/2.0.4/public/logo.png';

\Atk4\Ui\Header::addTo($app, ['Labels']);
\Atk4\Ui\Label::addTo($app, ['Hot!']);
\Atk4\Ui\Label::addTo($app, ['23', 'icon' => 'mail']);
\Atk4\Ui\Label::addTo($app, ['new', 'iconRight' => 'delete']);

\Atk4\Ui\Label::addTo($app, ['Coded in PHP', 'image' => $img]);
\Atk4\Ui\Label::addTo($app, ['Number of lines', 'detail' => '33']);

\Atk4\Ui\Header::addTo($app, ['Combinations and Interraction']);
$del = \Atk4\Ui\Label::addTo($app, ['Zoe', 'image' => 'https://semantic-ui.com/images/avatar/small/ade.jpg', 'iconRight' => 'delete']);
$del->on('click', '.delete', $del->js()->fadeOut());

$val = isset($_GET['toggle']) && $_GET['toggle'];
$toggle = \Atk4\Ui\Label::addTo($app, ['icon' => 'toggle ' . ($val ? 'on' : 'off')])->set('Value: ' . $val);
$toggle->on('click', new \Atk4\Ui\JsReload($toggle, ['toggle' => $val ? null : 1]));

$menu = \Atk4\Ui\Menu::addTo($app);
\Atk4\Ui\Label::addTo($menu->addItem('Inbox'), ['20', 'floating red']);
\Atk4\Ui\Label::addTo($menu->addMenu('Others')->addItem('Draft'), ['10', 'floating blue']);

$seg = \Atk4\Ui\View::addTo($app, ['ui' => 'segment']);
\Atk4\Ui\Header::addTo($seg, ['Label Group']);
$labels = \Atk4\Ui\View::addTo($seg, [false, 'tag', 'ui' => 'labels']);
\Atk4\Ui\Label::addTo($seg, ['$9.99']);
\Atk4\Ui\Label::addTo($seg, ['$19.99']);
\Atk4\Ui\Label::addTo($seg, ['$24.99']);

$columns = \Atk4\Ui\Columns::addTo($app);

$c = $columns->addColumn();
$seg = \Atk4\Ui\View::addTo($c, ['ui' => 'raised segment']);
\Atk4\Ui\Label::addTo($seg, ['Left Column', 'top attached', 'icon' => 'book']);
\Atk4\Ui\Label::addTo($seg, ['Lorem', 'red ribbon', 'icon' => 'cut']);
\Atk4\Ui\LoremIpsum::addTo($seg, ['size' => 1]);

$c = $columns->addColumn();
$seg = \Atk4\Ui\View::addTo($c, ['ui' => 'raised segment']);
\Atk4\Ui\Label::addTo($seg, ['Right Column', 'top attached', 'icon' => 'book']);
\Atk4\Ui\LoremIpsum::addTo($seg, ['size' => 1]);
\Atk4\Ui\Label::addTo($seg, ['Ipsum', 'orange bottom right attached', 'icon' => 'cut']);
