<?php
/**
 * Testing fields.
 */
require '../vendor/autoload.php';

try {
    $layout = new \atk4\ui\Layout\App(['defaultTemplate'=>'./templates/layout2.html']);

    $layout->add(new \atk4\ui\H2('Types'));

    $layout->add(new \atk4\ui\FormField\Line(['placeholder'=>'Search']));
    $layout->add(new \atk4\ui\FormField\Line(['placeholder'=>'Search', 'loading'=>true]));
    $layout->add(new \atk4\ui\FormField\Line(['placeholder'=>'Search', 'loading'=>'left']));
    $layout->add(new \atk4\ui\FormField\Line(['placeholder'=>'Search', 'icon'=>'search', 'disabled'=>true]));
    $layout->add(new \atk4\ui\FormField\Line(['placeholder'=>'Search', 'error'=>true]));

    $layout->add(new \atk4\ui\H2('Icon Variations'));

    $layout->add(new \atk4\ui\FormField\Line(['placeholder'=>'Search users', 'left'=>true, 'icon'=>'users']));
    $layout->add(new \atk4\ui\FormField\Line(['placeholder'=>'Search users', 'icon'=>'circular search link']));
    $layout->add(new \atk4\ui\FormField\Line(['placeholder'=>'Search users', 'icon'=>'inverted circular search link']));

    $layout->add(new \atk4\ui\H2('Labels'));

    $layout->add(new \atk4\ui\FormField\Line(['placeholder'=>'Search users', 'label'=>'http://']));

    // dropdown example
    $dd = new \atk4\ui\Dropdown('.com');
    $dd->setSource(['.com', '.net', '.org']);
    $layout->add(new \atk4\ui\FormField\Line([
        'placeholder'=> 'Find Domain',
        'labelRight' => $dd,
    ]));

    $layout->add(new \atk4\ui\FormField\Line(['placeholder'=>'Weight', 'labelRight'=>new \atk4\ui\Label(['kg', 'basic'])]));
    $layout->add(new \atk4\ui\FormField\Line(['label'=>'$', 'labelRight'=>new \atk4\ui\Label(['.00', 'basic'])]));

    $layout->add(new \atk4\ui\FormField\Line([
        'iconLeft'  => 'tags',
        'labelRight'=> new \atk4\ui\Label(['Add Tag', 'tag']),
    ]));

    // left/right corner is not supported, but here is work-around:
    $label = new \atk4\ui\Label();
    $label->addClass('left corner');
    $label->add(new \atk4\ui\Icon('asterisk'));

    $layout->add(new \atk4\ui\FormField\Line([
        'label'=> $label,
    ]))->addClass('left corner');

    $label = new \atk4\ui\Label();
    $label->addClass('corner');
    $label->add(new \atk4\ui\Icon('asterisk'));

    $layout->add(new \atk4\ui\FormField\Line([
        'label'=> $label,
    ]))->addClass('corner');

    $layout->add(new \atk4\ui\H2('Actions'));

    $layout->add(new \atk4\ui\FormField\Line(['action'=>'Search']));

    $layout->add(new \atk4\ui\FormField\Line(['actionLeft'=> new \atk4\ui\Button([
        'Checkout', 'icon'=>'cart', 'teal',
    ])]));

    $layout->add(new \atk4\ui\FormField\Line(['iconLeft'=>'search',  'action'=>'Search']));

    $dd = new \atk4\ui\DropdownButton(['This Page', 'basic']);
    $dd->setSource(['This Organisation', 'Entire Site']);
    $layout->add(new \atk4\ui\FormField\Line(['iconLeft'=>'search',  'action'=>$dd]));

    // double actions are not supported but you can add them yourself
    $dd = new \atk4\ui\Dropdown(['Articles', 'compact selection']);
    $dd->setSource(['All', ['name'=>'Articles', 'active'=>true], 'Products']);
    $layout->add(new \atk4\ui\FormField\Line(['iconLeft'=>'search',  'action'=>$dd]))
        ->add(new \atk4\ui\Button('Search'), 'AfterAfterInput');

    $layout->add(new \atk4\ui\FormField\Line(['action'=> new \atk4\ui\Button([
        'Copy', 'rightIcon'=>'copy', 'teal',
    ])]));

    $layout->add(new \atk4\ui\FormField\Line(['action'=> new \atk4\ui\Button([
       'icon'=> 'search',
    ])]));

    $layout->add(new \atk4\ui\H2('Modifiers'));

    $layout->add(new \atk4\ui\FormField\Line(['icon'=>'search', 'transparent'=>true, 'placeholder'=>'transparent']));
    $layout->add(new \atk4\ui\FormField\Line(['icon'=>'search', 'fluid'=>true, 'placeholder'=>'fluid']));

    $layout->add(new \atk4\ui\FormField\Line(['icon'=>'search', 'mini'=>true, 'placeholder'=>'mini']));

    echo $layout->render();
} catch (\atk4\core\Exception $e) {
    var_dump($e->getMessage());
    var_dump($e->getParams());
    var_dump($e->getTrace());
    throw $e;
}
