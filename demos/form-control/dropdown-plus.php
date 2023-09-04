<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\Form;
use Atk4\Ui\Header;
use Atk4\Ui\Message;
use Atk4\Ui\Text;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

$demo = Demo::addTo($app);

Header::addTo($demo->left, ['Dropdown sample:']);
Header::addTo($demo->right, ['Cascading Dropdown']);

$txt = Text::addTo($demo->right);
$txt->addParagraph('Dropdown may also be used in a cascade manner.');
$form = Form::addTo($demo->right);

$form->addControl('category_id', [Form\Control\Dropdown::class, 'model' => new Category($app->db)]);
$form->addControl('sub_category_id', [Form\Control\DropdownCascade::class, 'cascadeFrom' => 'category_id', 'reference' => Category::hinting()->fieldName()->SubCategories]);
$form->addControl('product_id', [Form\Control\DropdownCascade::class, 'cascadeFrom' => 'sub_category_id', 'reference' => SubCategory::hinting()->fieldName()->Products]);

$form->onSubmit(static function (Form $form) use ($app) {
    $message = $app->encodeJson($form->model->get());

    $view = new Message('Values: ');
    $view->setApp($form->getApp());
    $view->invokeInit();
    $view->text->addParagraph($message);

    return $view;
});

$form = Form::addTo($demo->left);

// standard with model: use idField as Value, titleField as Title for each Dropdown option
$form->addControl('withModel', [
    Form\Control\Dropdown::class,
    'caption' => 'Dropdown with data from Model',
    'model' => (new Country($app->db))->setLimit(25),
]);

// custom callback: alter title
$form->addControl('withModel2', [
    Form\Control\Dropdown::class,
    'caption' => 'Dropdown with data from Model and custom render',
    'model' => (new Country($app->db))->setLimit(25),
    'renderRowFunction' => static function (Country $row) {
        return [
            'value' => $row->getId(),
            'title' => $row->getTitle() . ' (' . $row->iso3 . ')',
        ];
    },
]);

// custom callback: add icon
$form->addControl('withModel3', [
    Form\Control\Dropdown::class,
    'caption' => 'Dropdown with data from Model and custom render with icon',
    'model' => (new File($app->db))->setLimit(25),
    'renderRowFunction' => static function (File $row) {
        return [
            'value' => $row->getId(),
            'title' => $row->getTitle(),
            'icon' => $row->is_folder ? 'folder' : 'file',
        ];
    },
]);

$form->addControl('enum', [
    Form\Control\Dropdown::class,
    'caption' => 'Using Single Values',
    'values' => ['default', 'option1', 'option2', 'option3'],
]);

$form->addControl('values', [
    Form\Control\Dropdown::class,
    'caption' => 'Using values with default text',
    'empty' => 'Choose an option',
    'values' => ['default' => 'Default', 'option1' => 'Option 1', 'option2' => 'Option 2', 'option3' => 'Option 3'],
]);

$form->addControl('icon', [
    Form\Control\Dropdown::class,
    'caption' => 'Using icon',
    'empty' => 'Choose an icon',
    'values' => [
        'tag' => ['Tag', 'icon' => 'tag'],
        'globe' => ['Globe', 'icon' => 'globe'],
        'registered' => ['Registered', 'icon' => 'registered'],
        'file' => ['File', 'icon' => 'file'],
    ],
]);

$form->addControl('multi', [
    Form\Control\Dropdown::class,
    'caption' => 'Multiple selection',
    'empty' => 'Choose has many options needed',
    'multiple' => true,
    'values' => ['default' => 'Default', 'option1' => 'Option 1', 'option2' => 'Option 2'],
]);

$form->onSubmit(static function (Form $form) use ($app) {
    $message = $app->encodeJson($form->model->get());

    $view = new Message('Values:');
    $view->setApp($form->getApp());
    $view->invokeInit();
    $view->text->addParagraph($message);

    return $view;
});
