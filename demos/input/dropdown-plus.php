<?php

require_once __DIR__ . '/../atk-init.php';
require_once __DIR__ . '/../_includes/Demo.php';

$demo = Demo::addTo($app);

\atk4\ui\Header::addTo($demo->left, ['DropDown sample:']);
\atk4\ui\Header::addTo($demo->right, ['Cascading DropDown']);
$txt = \atk4\ui\Text::addTo($demo->right);
$txt->addParagraph('DropDown may also be used in a cascade manner.');
$txt->addParagraph('You may find more information in DropDownCascade class.');
$v = \atk4\ui\View::addTo($demo->right, ['ui' => 'column padded centered grid']);
$btn = \atk4\ui\Button::addTo($v, ['DropDownCascade Class'])
    ->link('https://github.com/atk4/ui/blob/develop/src/FormField/DropDownCascade.php', '_blank')
    ->addClass('centered aligned');

$form = \atk4\ui\Form::addTo($demo->left);

//standard with model: use id_field as Value, title_field as Title for each DropDown option
$form->addField(
    'withModel',
    ['DropDown',
        'caption' => 'DropDown with data from Model',
        'model' => (new Country($db))->setLimit(25),
    ]
);

//custom callback: alter title
$form->addField(
    'withModel2',
    ['DropDown',
        'caption' => 'DropDown with data from Model',
        'model' => (new Country($db))->setLimit(25),
        'renderRowFunction' => function ($row) {
            return [
                'value' => $row->id,
                'title' => $row->getTitle() . ' (' . $row->get('iso3') . ')',
            ];
        },
    ]
);

//custom callback: add icon
$form->addField(
    'withModel3',
    ['DropDown',
        'caption' => 'DropDown with data from Model',
        'model' => (new File($db))->setLimit(25),
        'renderRowFunction' => function ($row) {
            return [
                'value' => $row->id,
                'title' => $row->getTitle(),
                'icon' => $row->get('is_folder') ? 'folder' : 'file',
            ];
        },
    ]
);

$form->addField(
    'enum',
    ['DropDown',
        'caption' => 'Using Single Values',
        'values' => ['default', 'option1', 'option2', 'option3'],
    ]
);

$form->addField(
    'values',
    ['DropDown',
        'caption' => 'Using values with default text',
        'empty' => 'Choose an option',
        'values' => ['default' => 'Default', 'option1' => 'Option 1', 'option2' => 'Option 2', 'option3' => 'Option 3'],
    ]
);

$form->addField(
    'icon',
    ['DropDown',
        'caption' => 'Using icon',
        'empty' => 'Choose an icon',
        'values' => ['tag' => ['Tag', 'icon' => 'tag icon'], 'globe' => ['Globe', 'icon' => 'globe icon'], 'registered' => ['Registered', 'icon' => 'registered icon'], 'file' => ['File', 'icon' => 'file icon']],
    ]
);

$form->addField(
    'multi',
    ['DropDown',
        'caption' => 'Multiple selection',
        'empty' => 'Choose has many options needed',
        'isMultiple' => true,
        'values' => ['default' => 'Default', 'option1' => 'Option 1', 'option2' => 'Option 2'],
    ]
);

$form->onSubmit(function (atk4\ui\Form $form) {
    $echo = print_r($form->model->get('enum'), true) . ' / ';
    $echo .= print_r($form->model->get('values'), true) . ' / ';
    $echo .= print_r($form->model->get('icon'), true) . ' / ';
    $echo .= print_r($form->model->get('multi'), true);

    echo $echo;
});
