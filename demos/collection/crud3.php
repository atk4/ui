<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Data\Model;
use Atk4\Data\Persistence;
use Atk4\Ui\Crud;
use Atk4\Ui\Header;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

$modelClass = AnonymousClassNameCache::get_class(fn () => new class() extends Model {
    public $table = 'test';

    public $caption = 'Country';

    protected function init(): void
    {
        parent::init();

        $this->addField('name');
        $this->addField('code');
        $this->addField('country');
    }
});

// prepare Persistence and data Model
$data = ['test' => [
    1 => ['id' => 1, 'name' => 'ABC9', 'code' => 11, 'country' => 'Ireland'],
    2 => ['id' => 2, 'name' => 'ABC8', 'code' => 12, 'country' => 'Ireland'],
    3 => ['id' => 3, 'name' => 'ABC7', 'code' => 13, 'country' => 'Latvia'],
    4 => ['id' => 4, 'name' => 'ABC6', 'code' => 14, 'country' => 'UK'],
    5 => ['id' => 5, 'name' => 'ABC5', 'code' => 15, 'country' => 'UK'],
    6 => ['id' => 6, 'name' => 'ABC4', 'code' => 16, 'country' => 'Ireland'],
    7 => ['id' => 7, 'name' => 'ABC3', 'code' => 17, 'country' => 'Latvia'],
    8 => ['id' => 8, 'name' => 'ABC2', 'code' => 18, 'country' => 'Russia'],
    9 => ['id' => 9, 'name' => 'ABC1', 'code' => 19, 'country' => 'Latvia'],
]];
$p = new Persistence\Array_($data);
$model = new $modelClass($p);

// add Crud
Header::addTo($app, ['Crud with Array Persistence']);
$c = Crud::addTo($app, ['ipp' => 5]);
$c->setModel($model);
