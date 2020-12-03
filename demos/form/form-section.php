<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\Form;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

\Atk4\Ui\Button::addTo($app, ['Accordion in Form', 'small right floated basic blue', 'iconRight' => 'right arrow'])
    ->link(['form-section-accordion']);
\Atk4\Ui\View::addTo($app, ['ui' => 'ui clearing divider']);

$model = new CountryLock($app->db);
$model->loadAny();

// Prevent form from saving
$noSave = function (Form $form) {
    return new \Atk4\Ui\JsToast([
        'title' => 'POSTed field values',
        'message' => '<pre>' . $form->getApp()->encodeJson($form->model->get()) . '</pre>',
        'class' => 'success',
        'displayTime' => 5000,
    ]);
};

////////////////////////////////

$form = Form::addTo($app);
$form->setModel($model, false);

$sublayout = $form->layout->addSubLayout([Form\Layout\Section::class]);

\Atk4\Ui\Header::addTo($sublayout, ['Column Section in Form']);
$sublayout->setModel($model, ['name']);

$colsLayout = $form->layout->addSubLayout([Form\Layout\Section\Columns::class]);

$c1 = $colsLayout->addColumn();
$c1->setModel($model, ['iso', 'iso3']);

$c2 = $colsLayout->addColumn();
$c2->setModel($model, ['numcode'/*, 'phonecode'*/]);

$form->addControl('phonecode');

$form->onSubmit($noSave);

\Atk4\Ui\View::addTo($app, ['ui' => 'divider']);

////////////////////////////////

$form = Form::addTo($app);
$form->setModel($model, false);

$sublayout = $form->layout->addSubLayout([Form\Layout\Section::class]);

\Atk4\Ui\Header::addTo($sublayout, ['Accordion Section in Form']);
$sublayout->setModel($model, ['name']);

$accordionLayout = $form->layout->addSubLayout([Form\Layout\Section\Accordion::class]);

$a1 = $accordionLayout->addSection('Section 1');
$a1->setModel($model, ['iso', 'iso3']);

$a2 = $accordionLayout->addSection('Section 2');
$a2->setModel($model, ['numcode', 'phonecode']);

$form->onSubmit($noSave);

\Atk4\Ui\View::addTo($app, ['ui' => 'divider']);

////////////////////////////////

$form = Form::addTo($app);
$form->setModel($model, false);

$sublayout = $form->layout->addSubLayout([Form\Layout\Section::class]);

\Atk4\Ui\Header::addTo($sublayout, ['Tabs in Form']);
$sublayout->setModel($model, ['name']);

$tabsLayout = $form->layout->addSubLayout([Form\Layout\Section\Tabs::class]);

$tab1 = $tabsLayout->addTab('Tab 1');
$tab1->addGroup('In Group')->setModel($model, ['iso', 'iso3']);

$tab2 = $tabsLayout->addTab('Tab 2');
$tab2->setModel($model, ['numcode', 'phonecode']);

$form->onSubmit($noSave);

\Atk4\Ui\View::addTo($app, ['ui' => 'divider']);

/////////////////////////////////////////

\Atk4\Ui\Header::addTo($app, ['Color in form']);

$form = Form::addTo($app);
$form->setModel($model, false);

$sublayout = $form->layout->addSubLayout([Form\Layout\Section::class, 'ui' => 'segment red inverted'], false);

\Atk4\Ui\Header::addTo($sublayout, ['This section in Red', 'ui' => 'dividing header', 'element' => 'h2']);
$sublayout->setModel($model, ['name']);

$sublayout = $form->layout->addSubLayout([Form\Layout\Section::class, 'ui' => 'segment teal inverted']);
$colsLayout = $sublayout->addSubLayout([Form\Layout\Section\Columns::class]);

$c1 = $colsLayout->addColumn();
$c1->setModel($model, ['iso', 'iso3']);

$c2 = $colsLayout->addColumn();
$c2->setModel($model, ['numcode', 'phonecode']);

$form->onSubmit($noSave);

\Atk4\Ui\View::addTo($app, ['ui' => 'divider']);
