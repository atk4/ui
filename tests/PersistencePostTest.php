<?php

declare(strict_types=1);

namespace Atk4\Ui\Tests;

use Atk4\Core\Phpunit\TestCase;
use Atk4\Data\Model;
use Atk4\Ui\Persistence\Post as PostPersistence;

class PersistencePostTest extends TestCase
{
    /** @var Model */
    public $model;

    protected function setUp(): void
    {
        parent::setUp();

        $_POST = ['name' => 'John', 'is_married' => 'Y'];
        $this->model = new Model();
        $this->model->addField('name');
        $this->model->addField('surname', ['default' => 'Smith']);
        $this->model->addField('is_married', ['type' => 'boolean']);
    }

    protected function tearDown(): void
    {
        unset($_POST);

        parent::tearDown();
    }

    /**
     * Test loading from POST persistence, some type mapping applies.
     */
    public function testPost(): void
    {
        $p = new PostPersistence();

        $m = $this->model;
        $m->setPersistence($p);

        $m = $m->load(0);
        $m->set('surname', 'DefSurname');

        static::assertSame('John', $m->get('name'));
        static::assertTrue($m->get('is_married'));
        static::assertSame('DefSurname', $m->get('surname'));
    }
}
