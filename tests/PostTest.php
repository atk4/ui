<?php

namespace atk4\ui\tests;

use atk4\core\AtkPhpunit;
use atk4\data\Model;

class PostTest extends AtkPhpunit\TestCase
{
    public $model;

    protected function setUp(): void
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

        $this->model->set('surname', 'DefSurname');

        $this->model->load(0, $p);

        $this->assertSame('John', $this->model->get('name'));
        $this->assertTrue($this->model->get('is_married'));
        $this->assertSame('DefSurname', $this->model->get('surname'));
    }
}
