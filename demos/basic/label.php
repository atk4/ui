<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\Header;
use Atk4\Ui\Label;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

$img = $app->cdn['atk'] . '/logo.png';

Header::addTo($app, ['Labels']);
Label::addTo($app, ['Hot!']);
Label::addTo($app, ['23', 'icon' => 'mail']);
Label::addTo($app, ['new', 'iconRight' => 'delete']);

Label::addTo($app, ['Coded in PHP', 'image' => $img]);
Label::addTo($app, ['Number of lines', 'detail' => '33']);

Header::addTo($app, ['Combinations and Interraction']);
$del = Label::addTo($app, ['Zoe', 'image' => 'https://semantic-ui.com/images/avatar/small/ade.jpg', 'iconRight' => 'delete']);
$del->on('click', '.delete', $del->js()->fadeOut());

$val = isset($_GET['toggle']) && $_GET['toggle'];
$toggle = Label::addTo($app, ['icon' => 'toggle ' . ($val ? 'on' : 'off')])->set('Value: ' . $val);
$toggle->on('click', new \Atk4\Ui\JsReload($toggle, ['toggle' => $val ? null : 1]));

$menu = \Atk4\Ui\Menu::addTo($app);
Label::addTo($menu->addItem('Inbox'), ['20', 'class.floating red' => true]);
Label::addTo($menu->addMenu('Others')->addItem('Draft'), ['10', 'class.floating blue' => true]);

$seg = \Atk4\Ui\View::addTo($app, ['ui' => 'segment']);
Header::addTo($seg, ['Label Group']);
$labels = \Atk4\Ui\View::addTo($seg, [false, 'class.tag' => true, 'ui' => 'labels']);
Label::addTo($seg, ['$9.99']);
Label::addTo($seg, ['$19.99']);
Label::addTo($seg, ['$24.99']);

$columns = \Atk4\Ui\Columns::addTo($app);

$c = $columns->addColumn();
$seg = \Atk4\Ui\View::addTo($c, ['ui' => 'raised segment']);
Label::addTo($seg, ['Left Column', 'class.top attached' => true, 'icon' => 'book']);
Label::addTo($seg, ['Lorem', 'class.red ribbon' => true, 'icon' => 'cut']);
\Atk4\Ui\LoremIpsum::addTo($seg, ['size' => 1]);

$c = $columns->addColumn();
$seg = \Atk4\Ui\View::addTo($c, ['ui' => 'raised segment']);
Label::addTo($seg, ['Right Column', 'class.top attached' => true, 'icon' => 'book']);
\Atk4\Ui\LoremIpsum::addTo($seg, ['size' => 1]);
Label::addTo($seg, ['Ipsum', 'class.orange bottom right attached' => true, 'icon' => 'cut']);
