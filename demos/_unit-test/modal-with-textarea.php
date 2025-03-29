<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\Form;
use Atk4\Ui\Modal;
use Atk4\Ui\View;

$_GET['layout'] = \Atk4\Ui\Layout::class;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

$modal = get_class(new class() extends Modal {
    protected function renderView(): void
    {
        parent::renderView();

        $this->js(true, $this->jsShow());
    }
})::addTo($app);
$modal->set(static function (View $p) {
    $textarea = Form\Control\Textarea::addTo($p)
        ->setStyle('width', '100%');
    $textarea->js(true, new \Atk4\Ui\Js\JsExpression('$([]).find(\'> textarea\')[ 0 ].style.height = \'800px\';', [$textarea]));
});
