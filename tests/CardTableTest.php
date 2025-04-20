<?php

declare(strict_types=1);

namespace Atk4\Ui\Tests;

use Atk4\Core\Phpunit\TestCase;
use Atk4\Data\Model;
use Atk4\Ui\CardTable;
use Atk4\Ui\Exception;

class CardTableTest extends TestCase
{
    public function testSetModelException(): void
    {
        $form = new CardTable();
        $entity = (new Model())->createEntity();

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Use CardTable::setEntity() method instead for entity set');
        $form->setModel($entity); // @phpstan-ignore argument.type, method.deprecated
    }
}
