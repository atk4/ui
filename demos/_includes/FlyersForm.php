<?php

declare(strict_types=1);

namespace atk4\ui\demo;

use atk4\ui\Form;

class FlyersForm extends Form
{
    public $flyers = [];

    public $cards = [
        ['name' => 'Frequent Flyer Program', 'id' => 1, 'nodes' => []],
        ['name' => 'World Class', 'id' => 2, 'nodes' => []],
        ['name' => 'Around the world', 'id' => 3, 'nodes' => []],
    ];

    public function init(): void
    {
        parent::init();

        $this->addField('first_name', [Form\Field\Line::class, 'caption' => 'Main passenger', 'placeholder' => 'First name'], ['required' => true]);
        $this->addField('last_name', [Form\Field\Line::class, 'renderLabel' => false, 'placeholder' => 'Last name'], ['required' => true]);
        $this->addField('email', [Form\Field\Line::class], ['required' => true]);

        $this->addField('from', [Form\Field\Calendar::class, 'caption' => 'Date:', 'placeholder' => 'From'], ['type' => 'date', 'required' => true]);
        $this->addField('to', [Form\Field\Calendar::class, 'renderLabel' => false, 'placeholder' => 'To'], ['type' => 'date', 'required' => true]);

        $this->addField('contains', [
            Form\Field\Line::class,
            'placeholder' => 'Search for country containing ...',
            'renderLabel' => false,
        ]);

        $this->addField('country', [
            Form\Field\Lookup::class,
            'model' => new \atk4\ui\demo\Country($this->app->db),
            'dependency' => function ($model, $data) {
                isset($data['contains']) ? $model->addCondition('name', 'like', '%' . $data['contains'] . '%') : null;
            },
            'search' => ['name', 'iso', 'iso3'],
            'caption' => 'Destination',
            'placeholder' => 'Select your destination',
        ], ['required' => true]);

        $ml = $this->addField('multi', [Form\Field\MultiLine::class, 'rowLimit' => 4, 'addOnTab' => true, 'caption' => 'Additional passengers:', 'renderLabel' => false]);
        $ml->setModel(new Flyers(new \atk4\data\Persistence\Array_($this->flyers)));

        $cards = $this->addField('cards', [new Form\Field\TreeItemSelector(['treeItems' => $this->cards]), 'caption' => 'Flyers program:'], ['type' => 'array', 'serialize' => 'json']);
        $cards->set(json_encode([]));

        $this->onSubmit(function ($f) {
            return new \atk4\ui\jsToast('Thank you!');
        });
    }
}
