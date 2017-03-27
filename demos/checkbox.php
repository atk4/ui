<?php
/**
 * Testing fields.
 */
require '../vendor/autoload.php';

try {
    $layout = new \atk4\ui\Layout\App(['defaultTemplate'=>'./templates/layout2.html']);

    $layout->add(new \atk4\ui\Header(['Checkboxes', 'size'=>2]));

    $layout->add(new \atk4\ui\FormField\Checkbox('Make my profile visible'));

    $layout->add(new \atk4\ui\View(['ui'=>'divider']));
    $layout->add(new \atk4\ui\FormField\Checkbox(['Accept terms and conditions', 'slider']));

    $layout->add(new \atk4\ui\View(['ui'=>'divider']));
    $layout->add(new \atk4\ui\FormField\Checkbox(['Subscribe to weekly newsletter', 'toggle']));

    $layout->add(new \atk4\ui\View(['ui'=>'divider']));
    $layout->add(new \atk4\ui\FormField\Checkbox(['Custom setting?']))->js(true)->checkbox('set indeterminate');

    echo $layout->render();
} catch (\atk4\core\Exception $e) {
    var_dump($e->getMessage());

    var_dump($e->getParams());
    var_dump($e->getTrace());
    throw $e;
}
