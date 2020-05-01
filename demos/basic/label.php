<?php

require_once __DIR__ . '/../atk-init.php';

$img = 'https://raw.githubusercontent.com/atk4/ui/2.0.4/public/logo.png';

\atk4\ui\Header::addTo($app, ['Labels']);
\atk4\ui\Label::addTo($app, ['Hot!']);
\atk4\ui\Label::addTo($app, ['23', 'icon' => 'mail']);
\atk4\ui\Label::addTo($app, ['new', 'iconRight' => 'delete']);

\atk4\ui\Label::addTo($app, ['Coded in PHP', 'image' => $img]);
\atk4\ui\Label::addTo($app, ['Number of lines', 'detail' => '33']);

\atk4\ui\Header::addTo($app, ['Combinations and Interraction']);
$del = \atk4\ui\Label::addTo($app, ['Zoe', 'image' => 'https://semantic-ui.com/images/avatar/small/ade.jpg', 'iconRight' => 'delete']);
$del->on('click', '.delete', $del->js()->fadeOut());

$val = isset($_GET['toggle']) && $_GET['toggle'];
$toggle = \atk4\ui\Label::addTo($app, ['icon' => 'toggle ' . ($val ? 'on' : 'off')])->set('Value: ' . $val);
$toggle->on('click', new \atk4\ui\jsReload($toggle, ['toggle' => $val ? null : 1]));

$m = \atk4\ui\Menu::addTo($app);
\atk4\ui\Label::addTo($m->addItem('Inbox'), ['20', 'floating red']);
\atk4\ui\Label::addTo($m->addMenu('Others')->addItem('Draft'), ['10', 'floating blue']);

$seg = \atk4\ui\View::addTo($app, ['ui' => 'segment']);
\atk4\ui\Header::addTo($seg, ['Label Group']);
$labels = \atk4\ui\View::addTo($seg, [false, 'tag', 'ui' => 'labels']);
\atk4\ui\Label::addTo($seg, ['$9.99']);
\atk4\ui\Label::addTo($seg, ['$19.99']);
\atk4\ui\Label::addTo($seg, ['$24.99']);

$columns = \atk4\ui\Columns::addTo($app);

$c = $columns->addColumn();
$seg = \atk4\ui\View::addTo($c, ['ui' => 'raised segment']);
\atk4\ui\Label::addTo($seg, ['Left Column', 'top attached', 'icon' => 'book']);
\atk4\ui\Label::addTo($seg, ['Lorem', 'red ribbon', 'icon' => 'cut']);
\atk4\ui\LoremIpsum::addTo($seg, ['size' => 1]);

$c = $columns->addColumn();
$seg = \atk4\ui\View::addTo($c, ['ui' => 'raised segment']);
\atk4\ui\Label::addTo($seg, ['Right Column', 'top attached', 'icon' => 'book']);
\atk4\ui\LoremIpsum::addTo($seg, ['size' => 1]);
\atk4\ui\Label::addTo($seg, ['Ipsum', 'orange bottom right attached', 'icon' => 'cut']);
