<?php
/**
 * Testing fields.
 */

chdir('..');

require_once 'atk-init.php';




\atk4\ui\Header::addTo($app, ['Types', 'size' => 2]);

\atk4\ui\FormField\Line::addTo($app)->setDefaults(['placeholder' => 'Search']);
\atk4\ui\FormField\Line::addTo($app, ['placeholder' => 'Search', 'loading' => true]);
\atk4\ui\FormField\Line::addTo($app, ['placeholder' => 'Search', 'loading' => 'left']);
\atk4\ui\FormField\Line::addTo($app, ['placeholder' => 'Search', 'icon' => 'search', 'disabled' => true]);
\atk4\ui\FormField\Line::addTo($app, ['placeholder' => 'Search', 'error' => true]);

\atk4\ui\Header::addTo($app, ['Icon Variations', 'size' => 2]);

\atk4\ui\FormField\Line::addTo($app, ['placeholder' => 'Search users', 'left' => true, 'icon' => 'users']);
\atk4\ui\FormField\Line::addTo($app, ['placeholder' => 'Search users', 'icon' => 'circular search link']);
\atk4\ui\FormField\Line::addTo($app, ['placeholder' => 'Search users', 'icon' => 'inverted circular search link']);

\atk4\ui\Header::addTo($app, ['Labels', 'size' => 2]);

\atk4\ui\FormField\Line::addTo($app, ['placeholder' => 'Search users', 'label' => 'http://']);

// dropdown example
$dd = new \atk4\ui\DropDown('.com');
$dd->setSource(['.com', '.net', '.org']);
\atk4\ui\FormField\Line::addTo($app, [
    'placeholder' => 'Find Domain',
    'labelRight'  => $dd,
]);

\atk4\ui\FormField\Line::addTo($app, ['placeholder' => 'Weight', 'labelRight' => new \atk4\ui\Label(['kg', 'basic'])]);
\atk4\ui\FormField\Line::addTo($app, ['label' => '$', 'labelRight' => new \atk4\ui\Label(['.00', 'basic'])]);

\atk4\ui\FormField\Line::addTo($app, [
    'iconLeft'   => 'tags',
    'labelRight' => new \atk4\ui\Label(['Add Tag', 'tag']),
]);

// left/right corner is not supported, but here is work-around:
$label = new \atk4\ui\Label();
$label->addClass('left corner');
\atk4\ui\Icon::addTo($label, ['asterisk']);

\atk4\ui\FormField\Line::addTo($app, [
    'label' => $label,
])->addClass('left corner');

$label = new \atk4\ui\Label();
$label->addClass('corner');
\atk4\ui\Icon::addTo($label, ['asterisk']);

\atk4\ui\FormField\Line::addTo($app, [
    'label' => $label,
])->addClass('corner');

\atk4\ui\Header::addTo($app, ['Actions', 'size' => 2]);

\atk4\ui\FormField\Line::addTo($app, ['action' => 'Search']);

\atk4\ui\FormField\Line::addTo($app, ['actionLeft' => new \atk4\ui\Button([
    'Checkout', 'icon' => 'cart', 'teal',
])]);

\atk4\ui\FormField\Line::addTo($app, ['iconLeft' => 'search',  'action' => 'Search']);

$dd = new \atk4\ui\DropDownButton(['This Page', 'basic']);
$dd->setSource(['This Organisation', 'Entire Site']);
\atk4\ui\FormField\Line::addTo($app, ['iconLeft' => 'search',  'action' => $dd]);

// double actions are not supported but you can add them yourself
$dd = new \atk4\ui\DropDown(['Articles', 'compact selection']);
$dd->setSource(['All', 'Services', 'Products']);
\atk4\ui\Button::addTo(\atk4\ui\FormField\Line::addTo($app, ['iconLeft' => 'search',  'action' => $dd]), ['Search'], ['AfterAfterInput']);

\atk4\ui\FormField\Line::addTo($app, ['action' => new \atk4\ui\Button([
    'Copy', 'iconRight' => 'copy', 'teal',
])]);

\atk4\ui\FormField\Line::addTo($app, ['action' => new \atk4\ui\Button([
    'icon' => 'search',
])]);

\atk4\ui\Header::addTo($app, ['Modifiers', 'size' => 2]);

\atk4\ui\FormField\Line::addTo($app, ['icon' => 'search', 'transparent' => true, 'placeholder' => 'transparent']);
\atk4\ui\FormField\Line::addTo($app, ['icon' => 'search', 'fluid' => true, 'placeholder' => 'fluid']);

\atk4\ui\FormField\Line::addTo($app, ['icon' => 'search', 'mini' => true, 'placeholder' => 'mini']);

\atk4\ui\Header::addTo($app, ['Custom HTML attributes for <input> tag', 'size' => 2]);
$l = \atk4\ui\FormField\Line::addTo($app, ['placeholder' => 'maxlength attribute set to 10']);
$l->setInputAttr('maxlength', '10');
$l = \atk4\ui\FormField\Line::addTo($app, ['fluid' => true, 'placeholder' => 'overwrite existing attribute (type="number")']);
$l->setInputAttr(['type' => 'number']);
