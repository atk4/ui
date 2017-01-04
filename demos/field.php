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

    $layout->add(new \atk4\UI\H2('Icon Variations'));

    $layout->add(new \atk4\ui\FormField\Line(['placeholder'=>'Search users', 'left'=>true, 'icon'=>'users']));
    $layout->add(new \atk4\ui\FormField\Line(['placeholder'=>'Search users', 'icon'=>'circular search link']));
    $layout->add(new \atk4\ui\FormField\Line(['placeholder'=>'Search users', 'icon'=>'inverted circular search link']));

    $layout->add(new \atk4\UI\H2('Labels'));

    $layout->add(new \atk4\ui\FormField\Line(['placeholder'=>'Search users', 'label'=>'http://']));
    $layout->add(new \atk4\ui\FormField\Line(['placeholder'=>'Weight', 'rightLabel'=>new \atk4\ui\Label(['kg', 'basic'])]));

    $dd = new \atk4\ui\Dropdown('.com');
    $dd->setSource(['.com', '.net', '.org']);

    $layout->add(new \atk4\ui\FormField\Line([
        'placeholder'=> 'Find Domain',
        'rightLabel' => $dd,
    ]));

    echo $layout->render();
} catch (\atk4\core\Exception $e) {
    var_dump($e->getMessage());

    var_dump($e->getParams());
    var_dump($e->getTrace());
    throw $e;
}
