<?php

namespace atk4\ui\tests;

use atk4\core\AtkPhpunit;
use atk4\data\Model;

class PostTest extends AtkPhpunit\TestCase
{
    public $model;

    public function setUp(): void
    {
        $_POST = ['name' => 'John', 'is_married' => 'Y'];
        $this->model = new Model();
        $this->model->addField('name');
        $this->model->addField('surname', ['default' => 'Smith']);
        $this->model->addField('is_married', ['type' => 'boolean']);
    }

    /**
     * Test loading from POST persistence, some type mapping applies.
     */
    public function testPost()
    {
        $p = new \atk4\ui\Persistence\POST();

        $this->model['surname'] = 'DefSurname';

        $this->model->load(0, $p);

        $this->assertEquals('John', $this->model['name']);
        $this->assertEquals(true, $this->model['is_married']);
        $this->assertEquals('DefSurname', $this->model['surname']);
    }
}
