<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Data\Model;
use Atk4\Data\Persistence;
use Atk4\Ui\Form;
use Atk4\Ui\Js\JsToast;

class FlyersForm extends Form
{
    public array $flyers = [];

    public array $cards = [
        ['name' => 'Frequent Flyer Program', 'id' => 1, 'nodes' => []],
        ['name' => 'World Class', 'id' => 2, 'nodes' => []],
        ['name' => 'Around the world', 'id' => 3, 'nodes' => []],
    ];

    protected function init(): void
    {
        parent::init();

        $this->addControl('first_name', [Form\Control\Line::class, 'caption' => 'Main passenger', 'placeholder' => 'First name'], ['required' => true]);
        $this->addControl('last_name', [Form\Control\Line::class, 'renderLabel' => false, 'placeholder' => 'Last name'], ['required' => true]);
        $this->addControl('email', [Form\Control\Line::class], ['required' => true]);

        $this->addControl('from', [Form\Control\Calendar::class, 'caption' => 'Date:', 'placeholder' => 'From'], ['type' => 'date', 'required' => true]);
        $this->addControl('to', [Form\Control\Calendar::class, 'renderLabel' => false, 'placeholder' => 'To'], ['type' => 'date', 'required' => true]);

        $this->addControl('contains', [
            Form\Control\Line::class,
            'placeholder' => 'Search for country containing ...',
            'renderLabel' => false,
        ]);

        $this->addControl('country', [
            Form\Control\Lookup::class,
            'model' => new Country($this->getApp()->db),
            'dependency' => static function (Model $model, $data) {
                if (isset($data['contains'])) {
                    $model->addCondition(Country::hinting()->fieldName()->name, 'like', '%' . $data['contains'] . '%');
                }
            },
            'search' => [
                Country::hinting()->fieldName()->name,
                Country::hinting()->fieldName()->iso,
                Country::hinting()->fieldName()->iso3,
            ],
            'caption' => 'Destination',
            'placeholder' => 'Select your destination',
        ], ['required' => true]);

        $ml = $this->addControl('multi', [Form\Control\Multiline::class, 'rowLimit' => 4, 'addOnTab' => true, 'caption' => 'Additional passengers:', 'renderLabel' => false]);
        $ml->setModel(new Flyers(new Persistence\Array_($this->flyers)));

        $cards = $this->addControl('cards', [Form\Control\TreeItemSelector::class, 'treeItems' => $this->cards, 'caption' => 'Flyers program:'], ['type' => 'json']);
        $cards->set([]);

        $this->onSubmit(static function (Form $form) {
            return new JsToast('Thank you!');
        });
    }
}
