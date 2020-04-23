<?php

/*
 * Demonstrate the use of Virtual Page.
 */

chdir('..');
require_once 'init.php';

// define virtual page.
$vp = \atk4\ui\VirtualPage::addTo($layout);

// Add content to virtual page.
\atk4\ui\LoremIpsum::addTo($vp);
$vp_btn = \atk4\ui\Button::addTo($vp, ['Back', 'icon' => 'left arrow']);
$vp_btn->link('virtual.php');
$vp->ui = 'red inverted segment';

$msg = \atk4\ui\Message::addTo($app, ['Virtual Page']);
$msg->text->addParagraph('Virtual page content are not rendered on page load. They will ouptput their content when trigger.');
$msg->text->addParagraph('Click button below to trigger it.');

// button that trigger virtual page.
$btn = \atk4\ui\Button::addTo($app, ['Trigger Virtual Page']);
$btn->link($vp->cb->getUrl());
