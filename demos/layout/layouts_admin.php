<?php

declare(strict_types=1);

namespace atk4\ui\demo;

/** @var \atk4\ui\App $app */
require_once __DIR__ . '/../init-app.php';

$layout = \atk4\ui\Layout\Admin::addTo($app);

$m_comp = $layout->menu->addMenu(['Layouts', 'icon' => 'puzzle']);
$m_comp->addItem(\atk4\ui\Layout\Centered::class);
$m_comp->addItem(\atk4\ui\Layout\Admin::class);

$m_right = $layout->menuRight;
$m_right->addItem(['Warning', 'red', 'icon' => 'red warning']);
$m_user = $m_right->addMenu('John Smith');
$m_user->addItem('Profile');
$m_user->addDivider();
$m_user->addItem('Logout');

$m_comp = $layout->menu->addMenu(['Component Demo', 'icon' => 'puzzle']);
$m_form = $m_comp->addMenu('Forms');
$m_form->addItem('Form Controls');
$m_form->addItem('Form Layouts');
$m_comp->addItem('CRUD');

$layout->menuLeft->addItem(['Home', 'icon' => 'home']);
$layout->menuLeft->addItem(['Topics', 'icon' => 'block layout']);
$layout->menuLeft->addItem(['Friends', 'icon' => 'smile']);
$layout->menuLeft->addItem(['History', 'icon' => 'calendar']);
$layout->menuLeft->addItem(['Settings', 'icon' => 'cogs']);

$layout->template['Footer'] = 'ATK is awesome';

\atk4\ui\Header::addTo($layout, ['Basic Form Example']);

$f = \atk4\ui\Form::addTo($layout, ['segment']);
$f->setModel(new \atk4\data\Model());

$f_group = $f->addGroup('Name');
$f_group->addControl('first_name', ['width' => 'eight']);
$f_group->addControl('middle_name', ['width' => 'three']);
$f_group->addControl('last_name', ['width' => 'five']);

$f_group = $f->addGroup('Address');
$f_group->addControl('address', ['width' => 'twelve']);
$f_group->addControl('zip', ['width' => 'four']);

$f->onSubmit(function (\atk4\ui\Form $form) {
    $errors = [];

    foreach (['first_name', 'last_name', 'address'] as $field) {
        if (!$form->model->get($field)) {
            $errors[] = $form->error($field, 'Field ' . $field . ' is mandatory');
        }
    }

    return $errors ?: $form->success('No more errors', 'so we have saved everything into the database');
});
