<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\Button;
use Atk4\Ui\Form;
use Atk4\Ui\Js\JsToast;
use Atk4\Ui\View;
use Atk4\Ui\VirtualPage;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

$vp = VirtualPage::addTo($app);

$vp->set(static function (VirtualPage $firstPage) {
    $secondVp = VirtualPage::addTo($firstPage);
    $secondVp->set(static function (VirtualPage $secondPage) {
        View::addTo($secondPage)->set('Second Level Page')->addClass('__atk-behat-test-second');
        $thirdVp = VirtualPage::addTo($secondPage);
        $thirdVp->set(static function (VirtualPage $thirdPage) {
            View::addTo($thirdPage)->set('Third Level Page')->addClass('__atk-behat-test-third');
            $form = Form::addTo($thirdPage);
            $form->addControl('category', [Form\Control\Lookup::class, 'model' => new Category($thirdPage->getApp()->db)]);
            $form->onSubmit(static function (Form $form) {
                $category = $form->getControl('category')->model->load($form->model->get('category'));

                return new JsToast($category->getTitle());
            });
        });
        Button::addTo($secondPage, ['Open Third'])->link($thirdVp->getUrl());
    });
    View::addTo($firstPage)->set('First Level Page')->addClass('__atk-behat-test-first');
    Button::addTo($firstPage, ['Open Second'])->link($secondVp->getUrl());
});

Button::addTo($app, ['Open First'])->link($vp->getUrl());
