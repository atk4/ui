<?php

declare(strict_types=1);

namespace Atk4\Ui\Tests;

use Atk4\Core\Phpunit\TestCase;
use Atk4\Data\Model;
use Atk4\Data\Persistence;
use Atk4\Ui\App;
use Atk4\Ui\Button;
use Atk4\Ui\Layout;
use Atk4\Ui\MenuItem;
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

class ExecutorFactoryTest extends TestCase
{
    /** @var Model */
    public $model;
    /** @var App */
    public $app;

    protected function setUp(): void
    {
        $p = new Persistence\Array_();
        $this->model = new TestModel($p);
        $this->app = $this->createApp();
        $this->app->initLayout([Layout\Admin::class]);
    }

    protected function createApp(): App
    {
        return new App([
            'catchExceptions' => false,
            'alwaysRun' => false,
        ]);
    }

    public function testExecutorFactory(): void
    {
        $view = View::addTo($this->app);

        $factory = $this->app->getExecutorFactory();
        $modalExecutor = $factory->create($this->model->getUserAction('edit'), $view);
        $jsCallbackExecutor = $factory->create($this->model->getUserAction('delete'), $view);
        $confirmationExecutor = $factory->create($this->model->getUserAction('confirm'), $view);

        $factory->registerTypeExecutor('MY_TYPE', [BasicExecutor::class]);
        $myRequiredExecutor = $factory->create($this->model->getUserAction('confirm'), $view, 'MY_TYPE');

        $factory->registerExecutor($this->model->getUserAction('basic'), [BasicExecutor::class]);
        $myBasicExecutor = $factory->create($this->model->getUserAction('basic'), $view);

        $this->assertInstanceOf(ModalExecutor::class, $modalExecutor);
        $this->assertInstanceOf(JsCallbackExecutor::class, $jsCallbackExecutor);
        $this->assertInstanceOf(ConfirmationExecutor::class, $confirmationExecutor);
        $this->assertInstanceOf(BasicExecutor::class, $myRequiredExecutor);
        $this->assertInstanceOf(BasicExecutor::class, $myBasicExecutor);
    }

    public function testExecutorTrigger(): void
    {
        $factory = $this->app->getExecutorFactory();
        $editAction = $this->model->getUserAction('edit');
        $addAction = $this->model->getUserAction('add');

        $modalButton = Button::assertInstanceOf($factory->createTrigger($editAction, $factory::MODAL_BUTTON));
        $this->assertSame($factory->getCaption($editAction, $factory::MODAL_BUTTON), $modalButton->content);

        $cardButton = Button::assertInstanceOf($factory->createTrigger($editAction, $factory::CARD_BUTTON));
        $this->assertSame($factory->getCaption($editAction, $factory::CARD_BUTTON), $cardButton->content);

        $tableButton = Button::assertInstanceOf($factory->createTrigger($editAction, $factory::TABLE_BUTTON));
        $this->assertNull($tableButton->content);
        $this->assertSame($tableButton->icon, 'edit');

        $addMenuItem = MenuItem::assertInstanceOf($factory->createTrigger($addAction, $factory::MENU_ITEM));
        $this->assertSame($addMenuItem->content, 'Add Test');
        $this->assertSame($addMenuItem->icon, 'plus');

        $tableMenuItem = MenuItem::assertInstanceOf($factory->createTrigger($editAction, $factory::TABLE_MENU_ITEM));
        $this->assertSame($factory->getCaption($editAction, $factory::TABLE_MENU_ITEM), $tableMenuItem->content);
    }

    public function testRegisterTrigger(): void
    {
        $factory = $this->app->getExecutorFactory();
        $factory->useTriggerDefault($factory::TABLE_BUTTON);
        $factory->useTriggerDefault($factory::MENU_ITEM);

        $editAction = $this->model->getUserAction('edit');

        $p = new Persistence\Array_();
        $otherModelClass = get_class(new class() extends Model {
        });
        $secondEditAction = (new $otherModelClass($p))->getUserAction('edit');

        $specialClass = get_class(new class() extends Model {
            public $caption = 'Special Test';
        });
        $specialEditAction = (new $specialClass($p))->getUserAction('edit');

        $factory->registerTrigger($factory::MENU_ITEM, [MenuItem::class, 'edit_item', 'icon' => 'pencil'], $editAction);
        $editItem = MenuItem::assertInstanceOf($factory->createTrigger($editAction, $factory::MENU_ITEM));

        $this->assertSame('edit_item', $editItem->content);
        $this->assertSame('pencil', $editItem->icon);

        $factory->registerTrigger($factory::TABLE_BUTTON, [Button::class, 'edit_button'], $editAction);
        $factory->registerTrigger($factory::TABLE_BUTTON, [Button::class, 'specific_edit_button'], $specialEditAction, true);

        $editButton = Button::assertInstanceOf($factory->createTrigger($editAction, $factory::TABLE_BUTTON));
        $secondEditButon = Button::assertInstanceOf($factory->createTrigger($secondEditAction, $factory::TABLE_BUTTON));
        $specialEditButton = Button::assertInstanceOf($factory->createTrigger($specialEditAction, $factory::TABLE_BUTTON));

        $this->assertSame('specific_edit_button', $specialEditButton->content);
        $this->assertSame($editButton->content, $secondEditButon->content);
        $this->assertNotSame($editButton->content, $specialEditButton->content);
    }
}
