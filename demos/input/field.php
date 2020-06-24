<?php

declare(strict_types=1);

namespace atk4\ui\demo;

use atk4\ui\Form;

/**
 * Testing fields.
 */
/** @var \atk4\ui\App $app */
require_once __DIR__ . '/../init-app.php';

\atk4\ui\Header::addTo($app, ['Types', 'size' => 2]);

Form\Field\Line::addTo($app)->setDefaults(['placeholder' => 'Search']);
Form\Field\Line::addTo($app, ['placeholder' => 'Search', 'loading' => true]);
Form\Field\Line::addTo($app, ['placeholder' => 'Search', 'loading' => 'left']);
Form\Field\Line::addTo($app, ['placeholder' => 'Search', 'icon' => 'search', 'disabled' => true]);
Form\Field\Line::addTo($app, ['placeholder' => 'Search', 'error' => true]);

\atk4\ui\Header::addTo($app, ['Icon Variations', 'size' => 2]);

Form\Field\Line::addTo($app, ['placeholder' => 'Search users', 'left' => true, 'icon' => 'users']);
Form\Field\Line::addTo($app, ['placeholder' => 'Search users', 'icon' => 'circular search link']);
Form\Field\Line::addTo($app, ['placeholder' => 'Search users', 'icon' => 'inverted circular search link']);

\atk4\ui\Header::addTo($app, ['Labels', 'size' => 2]);

Form\Field\Line::addTo($app, ['placeholder' => 'Search users', 'label' => 'http://']);

// dropdown example
$dd = new \atk4\ui\DropDown('.com');
$dd->setSource(['.com', '.net', '.org']);
Form\Field\Line::addTo($app, [
    'placeholder' => 'Find Domain',
    'labelRight' => $dd,
]);

Form\Field\Line::addTo($app, ['placeholder' => 'Weight', 'labelRight' => new \atk4\ui\Label(['kg', 'basic'])]);
Form\Field\Line::addTo($app, ['label' => '$', 'labelRight' => new \atk4\ui\Label(['.00', 'basic'])]);

Form\Field\Line::addTo($app, [
    'iconLeft' => 'tags',
    'labelRight' => new \atk4\ui\Label(['Add Tag', 'tag']),
]);

// left/right corner is not supported, but here is work-around:
$label = new \atk4\ui\Label();
$label->addClass('left corner');
\atk4\ui\Icon::addTo($label, ['asterisk']);

Form\Field\Line::addTo($app, [
    'label' => $label,
])->addClass('left corner');

$label = new \atk4\ui\Label();
$label->addClass('corner');
\atk4\ui\Icon::addTo($label, ['asterisk']);

Form\Field\Line::addTo($app, [
    'label' => $label,
])->addClass('corner');

\atk4\ui\Header::addTo($app, ['Actions', 'size' => 2]);

Form\Field\Line::addTo($app, ['action' => 'Search']);

Form\Field\Line::addTo($app, ['actionLeft' => new \atk4\ui\Button([
    'Checkout', 'icon' => 'cart', 'teal',
])]);

Form\Field\Line::addTo($app, ['iconLeft' => 'search',  'action' => 'Search']);

$dd = new \atk4\ui\DropDownButton(['This Page', 'basic']);
$dd->setSource(['This Organisation', 'Entire Site']);
Form\Field\Line::addTo($app, ['iconLeft' => 'search',  'action' => $dd]);

// double actions are not supported but you can add them yourself
$dd = new \atk4\ui\DropDown(['Articles', 'compact selection']);
$dd->setSource(['All', 'Services', 'Products']);
\atk4\ui\Button::addTo(Form\Field\Line::addTo($app, ['iconLeft' => 'search',  'action' => $dd]), ['Search'], ['AfterAfterInput']);

Form\Field\Line::addTo($app, ['action' => new \atk4\ui\Button([
    'Copy', 'iconRight' => 'copy', 'teal',
])]);

Form\Field\Line::addTo($app, ['action' => new \atk4\ui\Button([
    'icon' => 'search',
])]);

\atk4\ui\Header::addTo($app, ['Modifiers', 'size' => 2]);

Form\Field\Line::addTo($app, ['icon' => 'search', 'transparent' => true, 'placeholder' => 'transparent']);
Form\Field\Line::addTo($app, ['icon' => 'search', 'fluid' => true, 'placeholder' => 'fluid']);

Form\Field\Line::addTo($app, ['icon' => 'search', 'mini' => true, 'placeholder' => 'mini']);

\atk4\ui\Header::addTo($app, ['Custom HTML attributes for <input> tag', 'size' => 2]);
$l = Form\Field\Line::addTo($app, ['placeholder' => 'maxlength attribute set to 10']);
$l->setInputAttr('maxlength', '10');
$l = Form\Field\Line::addTo($app, ['fluid' => true, 'placeholder' => 'overwrite existing attribute (type="number")']);
$l->setInputAttr(['type' => 'number']);
