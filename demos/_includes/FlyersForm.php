<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Data\Model;
use Atk4\Ui\Form;

class FlyersForm extends Form
{
    public $flyers = [];

    public $cards = [
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
            'model' => new \Atk4\Ui\Demos\Country($this->getApp()->db),
            'dependency' => function (Model $model, $data) {
                if (isset($data['contains'])) {
                    $model->addCondition(\Atk4\Ui\Demos\Country::hinting()->fieldName()->name, 'like', '%' . $data['contains'] . '%');
                }
            },
            'search' => [
                \Atk4\Ui\Demos\Country::hinting()->fieldName()->name,
                \Atk4\Ui\Demos\Country::hinting()->fieldName()->iso,
                \Atk4\Ui\Demos\Country::hinting()->fieldName()->iso3,
            ],
            'caption' => 'Destination',
            'placeholder' => 'Select your destination',
        ], ['required' => true]);

        $ml = $this->addControl('multi', [Form\Control\Multiline::class, 'rowLimit' => 4, 'addOnTab' => true, 'caption' => 'Additional passengers:', 'renderLabel' => false]);
        $ml->setModel(new Flyers(new \Atk4\Data\Persistence\Array_($this->flyers)));

        $cards = $this->addControl('cards', [Form\Control\TreeItemSelector::class, 'treeItems' => $this->cards, 'caption' => 'Flyers program:'], ['type' => 'json']);
        $cards->set([]);

        $this->onSubmit(function (Form $form) {
            return new \Atk4\Ui\JsToast('Thank you!');
        });
    }
}
