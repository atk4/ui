<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\App;
use Atk4\Ui\Form;
use Atk4\Ui\Header;
use Atk4\Ui\Wizard;

/** @var App $app */
require_once __DIR__ . '/../init-app.php';

$wizard = Wizard::addTo($app);

$stepFx = static function (Wizard $wizard) {
    $form = Form::addTo($wizard);
    $form->addControl('city', [], ['required' => true]);
    $form->onSubmit(static function (Form $form) use ($wizard) {
        return $wizard->jsNext();
    });
};
$wizard->addStep(['Step 1'], $stepFx);
$wizard->addStep(['Step 2'], $stepFx);

$wizard->addFinish(static function (Wizard $wizard) {
    Header::addTo($wizard, ['Wizard completed']);
});
