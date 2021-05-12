<?php

declare(strict_types=1);

namespace Atk4\Ui\Tests;

use Atk4\Core\AtkPhpunit;
use Atk4\Data\Model;

class PostTest extends AtkPhpunit\TestCase
{
    /** @var Model */
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
    public function testPost(): void
    {
        $p = new \Atk4\Ui\Persistence\Post();

        $m = $this->model;
        $m->addField('id');
        $m->persistence = $p;

        $m = $m->load(0);
        $m->set('surname', 'DefSurname');

        $this->assertSame('John', $m->get('name'));
        $this->assertTrue($m->get('is_married'));
        $this->assertSame('DefSurname', $m->get('surname'));
    }
}
