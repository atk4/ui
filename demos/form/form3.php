<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Data\Model;
use Atk4\Ui\Button;
use Atk4\Ui\Form;
use Atk4\Ui\Header;
use Atk4\Ui\Js\JsBlock;
use Atk4\Ui\Js\JsReload;
use Atk4\Ui\View;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

Header::addTo($app, ['Form automatically decided how many columns to use']);

$buttons = View::addTo($app, ['ui' => 'green basic buttons']);

$seg = View::addTo($app, ['ui' => 'raised segment']);

Button::addTo($buttons, ['Use Country Model', 'icon' => 'arrow down'])
    ->on('click', new JsReload($seg, ['m' => 'country']));
Button::addTo($buttons, ['Use File Model', 'icon' => 'arrow down'])
    ->on('click', new JsReload($seg, ['m' => 'file']));
Button::addTo($buttons, ['Use Stat Model', 'icon' => 'arrow down'])
    ->on('click', new JsReload($seg, ['m' => 'stat']));

$form = Form::addTo($seg, ['layout' => [Form\Layout\Columns::class]]);
$modelClass = ['country' => Country::class, 'file' => File::class][$app->tryGetRequestQueryParam('m')] ?? Stat::class;
$form->setModel((new $modelClass($app->db))->loadAny());

$form->onSubmit(static function (Form $form) {
    $errors = [];
    $modelDirty = \Closure::bind(static function () use ($form): array { // TODO Model::dirty property is private
        return $form->model->dirty;
    }, null, Model::class)();
    foreach ($modelDirty as $field => $value) {
        // we should care only about editable fields
        if ($form->model->getField($field)->isEditable()) {
            $errors[] = $form->jsError($field, 'Value was changed, ' . $form->getApp()->encodeJson($value) . ' to ' . $form->getApp()->encodeJson($form->model->get($field)));
        }
    }

    return $errors !== [] ? new JsBlock($errors) : 'No fields were changed';
});
