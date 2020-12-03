<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Data\Model;
use Atk4\Ui\Form;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

$demo = Demo::addTo($app);

\Atk4\Ui\Header::addTo($demo->left, ['Dropdown sample:']);
\Atk4\Ui\Header::addTo($demo->right, ['Cascading Dropdown']);

$txt = \Atk4\Ui\Text::addTo($demo->right);
$txt->addParagraph('Dropdown may also be used in a cascade manner.');
$form = Form::addTo($demo->right);

$form->addControl('category_id', [Form\Control\Dropdown::class, 'model' => new Category($app->db)]);
$form->addControl('sub_category_id', [Form\Control\DropdownCascade::class, 'cascadeFrom' => 'category_id', 'reference' => 'SubCategories']);
$form->addControl('product_id', [Form\Control\DropdownCascade::class, 'cascadeFrom' => 'sub_category_id', 'reference' => 'Products']);

$form->onSubmit(function (Form $form) use ($app) {
    $message = $app->encodeJson($form->model->get());

    $view = new \Atk4\Ui\Message('Values: ');
    $view->invokeInit();
    $view->text->addParagraph($message);

    return $view;
});

$form = Form::addTo($demo->left);

// standard with model: use id_field as Value, title_field as Title for each Dropdown option
$form->addControl(
    'withModel',
    [Form\Control\Dropdown::class,
        'caption' => 'Dropdown with data from Model',
        'model' => (new Country($app->db))->setLimit(25),
    ]
);

// custom callback: alter title
$form->addControl(
    'withModel2',
    [Form\Control\Dropdown::class,
        'caption' => 'Dropdown with data from Model',
        'model' => (new Country($app->db))->setLimit(25),
        'renderRowFunction' => function (Model $row) {
            return [
                'value' => $row->getId(),
                'title' => $row->getTitle() . ' (' . $row->get('iso3') . ')',
            ];
        },
    ]
);

// custom callback: add icon
$form->addControl(
    'withModel3',
    [Form\Control\Dropdown::class,
        'caption' => 'Dropdown with data from Model',
        'model' => (new File($app->db))->setLimit(25),
        'renderRowFunction' => function (Model $row) {
            return [
                'value' => $row->getId(),
                'title' => $row->getTitle(),
                'icon' => $row->get('is_folder') ? 'folder' : 'file',
            ];
        },
    ]
);

$form->addControl(
    'enum',
    [Form\Control\Dropdown::class,
        'caption' => 'Using Single Values',
        'values' => ['default', 'option1', 'option2', 'option3'],
    ]
);

$form->addControl(
    'values',
    [Form\Control\Dropdown::class,
        'caption' => 'Using values with default text',
        'empty' => 'Choose an option',
        'values' => ['default' => 'Default', 'option1' => 'Option 1', 'option2' => 'Option 2', 'option3' => 'Option 3'],
    ]
);

$form->addControl(
    'icon',
    [Form\Control\Dropdown::class,
        'caption' => 'Using icon',
        'empty' => 'Choose an icon',
        'values' => ['tag' => ['Tag', 'icon' => 'tag icon'], 'globe' => ['Globe', 'icon' => 'globe icon'], 'registered' => ['Registered', 'icon' => 'registered icon'], 'file' => ['File', 'icon' => 'file icon']],
    ]
);

$form->addControl(
    'multi',
    [Form\Control\Dropdown::class,
        'caption' => 'Multiple selection',
        'empty' => 'Choose has many options needed',
        'isMultiple' => true,
        'values' => ['default' => 'Default', 'option1' => 'Option 1', 'option2' => 'Option 2'],
    ]
);

$form->onSubmit(function (Form $form) use ($app) {
    $message = $app->encodeJson($form->model->get());

    $view = new \Atk4\Ui\Message('Values: ');
    $view->invokeInit();
    $view->text->addParagraph($message);

    return $view;
});
