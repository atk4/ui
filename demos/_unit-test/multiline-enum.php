<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Data\Model;
use Atk4\Data\Persistence;
use Atk4\Ui\App;
use Atk4\Ui\Form;
use Atk4\Ui\Js\JsToast;

/** @var App $app */
require_once __DIR__ . '/../init-app.php';

$form = Form::addTo($app);

/** @var Form\Control\Multiline */
$multiline = $form->addControl('items', [Form\Control\Multiline::class, 'itemLimit' => 2]);
$multiline->setModel(new class(new Persistence\Array_()) extends Model {
    #[\Override]
    protected function init(): void
    {
        parent::init();

        $ageValues = [1 => 'Child', 2_000 => 'Adult', 3_000 => 'Senior'];
        $this->addField('age0', ['type' => 'integer', 'values' => $ageValues]);
        $this->addField('age1', ['type' => 'integer', 'values' => $ageValues, 'default' => 1]);
        $this->addField('age2', ['type' => 'integer', 'values' => $ageValues, 'default' => 2_000]);
        $this->addField('age3', ['type' => 'integer', 'values' => $ageValues, 'default' => 3_000]);
        $this->addField('text', ['type' => 'text', 'default' => "a\nb"]);
    }
});

$form->onSubmit(static function (Form $form) use ($multiline) {
    return new JsToast($form->getApp()->encodeJson($multiline->changes->inserts));
});
