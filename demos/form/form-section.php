<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\Button;
use Atk4\Ui\Form;
use Atk4\Ui\Header;
use Atk4\Ui\Js\JsToast;
use Atk4\Ui\View;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

Button::addTo($app, ['Accordion in Form', 'class.small right floated basic blue' => true, 'iconRight' => 'right arrow'])
    ->link(['form-section-accordion']);
View::addTo($app, ['ui' => 'clearing divider']);

$model = new Country($app->db);
$model = $model->loadAny();

$saveAndDumpValues = static function (Form $form) {
    $form->model->save();

    return new JsToast([
        'title' => 'POSTed field values',
        'message' => '<pre>' . $form->getApp()->encodeJson($form->model->get()) . '</pre>',
        'class' => 'success',
        'displayTime' => 5000,
    ]);
};

// -----------------------------------------------------------------------------

$form = Form::addTo($app);
$form->setModel($model, []);

$sublayout = $form->layout->addSubLayout([Form\Layout\Section::class]);

Header::addTo($sublayout, ['Column Section in Form']);
$sublayout->setModel($model, [$model->fieldName()->name]);

$colsLayout = $form->layout->addSubLayout([Form\Layout\Section\Columns::class]);

$c1 = $colsLayout->addColumn();
$c1->setModel($model, [$model->fieldName()->iso, $model->fieldName()->iso3]);

$c2 = $colsLayout->addColumn();
$c2->setModel($model, [$model->fieldName()->numcode/* , $model->fieldName()->phonecode */]);

$form->addControl($model->fieldName()->phonecode);

$form->onSubmit($saveAndDumpValues);

View::addTo($app, ['ui' => 'divider']);

// -----------------------------------------------------------------------------

$form = Form::addTo($app);
$form->setModel($model, []);

$sublayout = $form->layout->addSubLayout([Form\Layout\Section::class]);

Header::addTo($sublayout, ['Accordion Section in Form']);
$sublayout->setModel($model, [$model->fieldName()->name]);

$accordionLayout = $form->layout->addSubLayout([Form\Layout\Section\Accordion::class]);

$a1 = $accordionLayout->addSection('Section 1');
$a1->setModel($model, [$model->fieldName()->iso, $model->fieldName()->iso3]);

$a2 = $accordionLayout->addSection('Section 2');
$a2->setModel($model, [$model->fieldName()->numcode, $model->fieldName()->phonecode]);

$form->onSubmit($saveAndDumpValues);

View::addTo($app, ['ui' => 'divider']);

// -----------------------------------------------------------------------------

$form = Form::addTo($app);
$form->setModel($model, []);

$sublayout = $form->layout->addSubLayout([Form\Layout\Section::class]);

Header::addTo($sublayout, ['Tabs in Form']);
$sublayout->setModel($model, [$model->fieldName()->name]);

$tabsLayout = $form->layout->addSubLayout([Form\Layout\Section\Tabs::class]);

$tab1 = $tabsLayout->addTab('Tab 1');
$tab1->addGroup('In Group')->setModel($model, [$model->fieldName()->iso, $model->fieldName()->iso3]);

$tab2 = $tabsLayout->addTab('Tab 2');
$tab2->setModel($model, [$model->fieldName()->numcode, $model->fieldName()->phonecode]);

$form->onSubmit($saveAndDumpValues);

View::addTo($app, ['ui' => 'divider']);

// -----------------------------------------------------------------------------

Header::addTo($app, ['Color in form']);

$form = Form::addTo($app);
$form->setModel($model, []);

$sublayout = $form->layout->addSubLayout([Form\Layout\Section::class, 'ui' => 'segment red inverted'], false);

Header::addTo($sublayout, ['This section in Red', 'ui' => 'dividing header', 'element' => 'h2']);
$sublayout->setModel($model, [$model->fieldName()->name]);

$sublayout = $form->layout->addSubLayout([Form\Layout\Section::class, 'ui' => 'segment teal inverted']);
$colsLayout = $sublayout->addSubLayout([Form\Layout\Section\Columns::class]);

$c1 = $colsLayout->addColumn();
$c1->setModel($model, [$model->fieldName()->iso, $model->fieldName()->iso3]);

$c2 = $colsLayout->addColumn();
$c2->setModel($model, [$model->fieldName()->numcode, $model->fieldName()->phonecode]);

$form->onSubmit($saveAndDumpValues);

View::addTo($app, ['ui' => 'divider']);
