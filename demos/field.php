<?php
/**
 * Testing fields.
 */
require 'init.php';

$app->add(['Header', 'Types', 'size' => 2]);

$app->add(new \atk4\ui\FormField\Line())->setDefaults(['placeholder' => 'Search']);
$app->add(new \atk4\ui\FormField\Line(['placeholder' => 'Search', 'loading' => true]));
$app->add(new \atk4\ui\FormField\Line(['placeholder' => 'Search', 'loading' => 'left']));
$app->add(new \atk4\ui\FormField\Line(['placeholder' => 'Search', 'icon' => 'search', 'disabled' => true]));
$app->add(new \atk4\ui\FormField\Line(['placeholder' => 'Search', 'error' => true]));

$app->add(new \atk4\ui\Header(['Icon Variations', 'size' => 2]));

$app->add(new \atk4\ui\FormField\Line(['placeholder' => 'Search users', 'left' => true, 'icon' => 'users']));
$app->add(new \atk4\ui\FormField\Line(['placeholder' => 'Search users', 'icon' => 'circular search link']));
$app->add(new \atk4\ui\FormField\Line(['placeholder' => 'Search users', 'icon' => 'inverted circular search link']));

$app->add(new \atk4\ui\Header(['Labels', 'size' => 2]));

$app->add(new \atk4\ui\FormField\Line(['placeholder' => 'Search users', 'label' => 'http://']));

// dropdown example
$dd = new \atk4\ui\DropDown('.com');
$dd->setSource(['.com', '.net', '.org']);
$app->add(new \atk4\ui\FormField\Line([
    'placeholder' => 'Find Domain',
    'labelRight'  => $dd,
]));

$app->add(new \atk4\ui\FormField\Line(['placeholder' => 'Weight', 'labelRight' => new \atk4\ui\Label(['kg', 'basic'])]));
$app->add(new \atk4\ui\FormField\Line(['label' => '$', 'labelRight' => new \atk4\ui\Label(['.00', 'basic'])]));

$app->add(new \atk4\ui\FormField\Line([
    'iconLeft'   => 'tags',
    'labelRight' => new \atk4\ui\Label(['Add Tag', 'tag']),
]));

// left/right corner is not supported, but here is work-around:
$label = new \atk4\ui\Label();
$label->addClass('left corner');
$label->add(new \atk4\ui\Icon('asterisk'));

$app->add(new \atk4\ui\FormField\Line([
    'label' => $label,
]))->addClass('left corner');

$label = new \atk4\ui\Label();
$label->addClass('corner');
$label->add(new \atk4\ui\Icon('asterisk'));

$app->add(new \atk4\ui\FormField\Line([
    'label' => $label,
]))->addClass('corner');

$app->add(new \atk4\ui\Header(['Actions', 'size' => 2]));

$app->add(new \atk4\ui\FormField\Line(['action' => 'Search']));

$app->add(new \atk4\ui\FormField\Line(['actionLeft' => new \atk4\ui\Button([
    'Checkout', 'icon' => 'cart', 'teal',
])]));

$app->add(new \atk4\ui\FormField\Line(['iconLeft' => 'search',  'action' => 'Search']));

$dd = new \atk4\ui\DropDownButton(['This Page', 'basic']);
$dd->setSource(['This Organisation', 'Entire Site']);
$app->add(new \atk4\ui\FormField\Line(['iconLeft' => 'search',  'action' => $dd]));

// double actions are not supported but you can add them yourself
$dd = new \atk4\ui\DropDown(['Articles', 'compact selection']);
$dd->setSource(['All', 'Services', 'Products']);
$app->add(new \atk4\ui\FormField\Line(['iconLeft' => 'search',  'action' => $dd]))
    ->add(new \atk4\ui\Button('Search'), 'AfterAfterInput');

$app->add(new \atk4\ui\FormField\Line(['action' => new \atk4\ui\Button([
    'Copy', 'iconRight' => 'copy', 'teal',
])]));

$app->add(new \atk4\ui\FormField\Line(['action' => new \atk4\ui\Button([
    'icon' => 'search',
])]));

$app->add(new \atk4\ui\Header(['Modifiers', 'size' => 2]));

$app->add(new \atk4\ui\FormField\Line(['icon' => 'search', 'transparent' => true, 'placeholder' => 'transparent']));
$app->add(new \atk4\ui\FormField\Line(['icon' => 'search', 'fluid' => true, 'placeholder' => 'fluid']));

$app->add(new \atk4\ui\FormField\Line(['icon' => 'search', 'mini' => true, 'placeholder' => 'mini']));

$app->add(new \atk4\ui\Header(['Custom HTML attributes for <input> tag', 'size' => 2]));
$l = $app->add(new \atk4\ui\FormField\Line(['placeholder' => 'maxlength attribute set to 10']));
$l->setInputAttr('maxlength', '10');
$l = $app->add(new \atk4\ui\FormField\Line(['fluid' => true, 'placeholder' => 'overwrite existing attribute (type="number")']));
$l->setInputAttr(['type' => 'number']);
