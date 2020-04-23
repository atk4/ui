<?php

/*
 * Demonstrate the use of Virtual Page.
 */

chdir('..');
require_once 'init.php';

// define virtual page.
$vp = \atk4\ui\VirtualPage::addTo($layout);
$vp->cb->urlTrigger = 'in';

// Add content to virtual page.
\atk4\ui\Header::addTo($vp, [$_GET['p_id']]);
\atk4\ui\LoremIpsum::addTo($vp);
$vp_btn = \atk4\ui\Button::addTo($vp, ['Back', 'icon' => 'left arrow']);
$vp_btn->link('virtual.php');
$vp->ui = 'grey inverted segment';

$msg = \atk4\ui\Message::addTo($app, ['Virtual Page']);
$msg->text->addParagraph('Virtual page content are not rendered on page load. They will ouptput their content when trigger.');
$msg->text->addParagraph('Click button below to trigger it.');

// button that trigger virtual page.
$btn = \atk4\ui\Button::addTo($app, ['More info on Car']);
$btn->link($vp->cb->getUrl() . '&p_id=Car');

$btn = \atk4\ui\Button::addTo($app, ['More info on Bike']);
$btn->link($vp->cb->getUrl() . '&p_id=Bike');
