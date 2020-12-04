<?php

declare(strict_types=1);

namespace Atk4\Ui\Tests;

use Atk4\Core\AtkPhpunit;
use Atk4\Data\Model;
use Atk4\Data\Persistence\Array_;
use Atk4\Ui\App;
use Atk4\Ui\Button;
use Atk4\Ui\Item;
use Atk4\Ui\UserAction\BasicExecutor;
use Atk4\Ui\UserAction\ConfirmationExecutor;
use Atk4\Ui\UserAction\JsCallbackExecutor;
use Atk4\Ui\UserAction\ModalExecutor;
use Atk4\Ui\View;

class TestModel extends Model
{
    public $caption = 'Test';

    protected function init(): void
    {
        parent::init();

        $this->addField('name');

        $this->addUserAction('confirm', [
            'confirmation' => function () {
                return 'confirm?';
            },
        ]);

        $this->addUserAction('basic', []);
    }
}

class ExecutorFactoryTest extends AtkPhpunit\TestCase
{
    /** @var Model */
    public $m;
    /** @var App */
    public $app;

    protected function setUp(): void
    {
        $p = new Array_();
        $this->m = new TestModel($p);
        $this->app = $this->getApp();
        $this->app->initLayout([\Atk4\Ui\Layout\Admin::class]);
    }

    protected function getApp()
    {
        return new App([
            'catch_exceptions' => false,
            'always_run' => false,
        ]);
    }

    public function testExecutorFactory()
    {
        $view = View::addTo($this->app);

        $factory = $this->app->getExecutorFactory();
        $modalExecutor = $factory->create($this->m->getUserAction('edit'), $view);
        $jsCallbackExecutor = $factory->create($this->m->getUserAction('delete'), $view);
        $confirmationExecutor = $factory->create($this->m->getUserAction('confirm'), $view);

        // register an executor for a specific type.
        $factory->registerTypeExecutor('MY_TYPE', [BasicExecutor::class]);
        $myRequiredExecutor = $factory->create($this->m->getUserAction('confirm'), $view, 'MY_TYPE');

        // register executor for a specific action
        $factory->registerExecutor($this->m->getUserAction('basic'), [BasicExecutor::class]);
        $myBasicExecutor = $factory->create($this->m->getUserAction('basic'), $view);

        $this->assertInstanceOf(ModalExecutor::class, $modalExecutor, 'Not ModalExecutor Type');
        $this->assertInstanceOf(JsCallbackExecutor::class, $jsCallbackExecutor, 'Not JsCallbackExecutor Type');
        $this->assertInstanceOf(ConfirmationExecutor::class, $confirmationExecutor, 'Not ConfirmationExecutor Type');
        $this->assertInstanceOf(BasicExecutor::class, $myRequiredExecutor, 'Not MyType executor');
        $this->assertInstanceOf(BasicExecutor::class, $myBasicExecutor, 'Not executor set for Basic action');
    }

    public function testExecutorTrigger()
    {
        $factory = $this->app->getExecutorFactory();
        $editAction = $this->m->getUserAction('edit');
        $addAction = $this->m->getUserAction('add');

        $modalButton = $factory->createTrigger($editAction, $factory::MODAL_BUTTON);
        $cardButton = $factory->createTrigger($editAction, $factory::CARD_BUTTON);
        $tableButton = $factory->createTrigger($editAction, $factory::TABLE_BUTTON);
        $addMenuItem = $factory->createTrigger($addAction, $factory::MENU_ITEM);
        $tableMenuItem = $factory->createTrigger($editAction, $factory::TABLE_MENU_ITEM);

        $this->assertInstanceOf(Button::class, $modalButton, 'Not Modal button');
        $this->assertSame($factory->getCaption($editAction, $factory::MODAL_BUTTON), $modalButton->content);

        $this->assertInstanceOf(Button::class, $cardButton, 'Not Card button');
        $this->assertSame($factory->getCaption($editAction, $factory::CARD_BUTTON), $cardButton->content);

        $this->assertInstanceOf(Button::class, $tableButton, 'Not Table button');
        $this->assertNull($tableButton->content);
        $this->assertSame($tableButton->icon, 'edit');

        $this->assertInstanceOf(Item::class, $addMenuItem, 'Not Add menu item');
        $this->assertSame($addMenuItem->content, 'Add Test');
        $this->assertSame($addMenuItem->icon, 'plus');

        $this->assertInstanceOf(Item::class, $tableMenuItem, 'Not Table item');
        $this->assertSame($factory->getCaption($editAction, $factory::TABLE_MENU_ITEM), $tableMenuItem->content);
    }
}
