<?php

declare(strict_types=1);

namespace Atk4\Ui\Example;

use Atk4\Core\Factory;
use Atk4\Data\Model;
use Atk4\Ui\AbstractView;
use Atk4\Ui\Exception;
use Atk4\Ui\Form;
use Atk4\Ui\UserAction\ExecutorFactory;
use Atk4\Ui\UserAction\ExecutorInterface;
use Atk4\Ui\UserAction\ModalExecutor;
use Atk4\Ui\View;

class Inc
{
}

class CustomUserAction extends Model\UserAction
{
    /** @var array<string, mixed> */
    public $ui;
}

class CustomExecutorFactory extends ExecutorFactory
{
    protected function createExecutor(Model\UserAction $action, View $owner, string $requiredType = null): ExecutorInterface
    {
        // required a specific executor type.
        if ($requiredType) {
            if (!($this->executorSeed[$requiredType] ?? null)) {
                throw (new Exception('Required executor type is not set'))
                    ->addMoreInfo('type', $requiredType);
            }
            $seed = $this->executorSeed[$requiredType];
        // check if executor is register for this model/action.
        } elseif ($seed = $this->executorSeed[$this->getModelId($action)][$action->shortName] ?? null) {
        } else {
            // if no type is register, determine executor to use base on action properties.
            if (is_callable($action->confirmation)) {
                $seed = $this->executorSeed[self::CONFIRMATION_EXECUTOR];
            } else {
                $seed = (!$action->args && !$action->fields && !$action->preview)
                        ? $this->executorSeed[self::JS_EXECUTOR]
                        : $this->executorSeed[self::STEP_EXECUTOR];
            }
        }

        if ($action instanceof CustomUserAction) {
            $seed = Factory::mergeSeeds($action->ui['executor'] ?? [], $seed);
        }

        /** @var AbstractView&ExecutorInterface */
        $executor = $owner->add(Factory::factory($seed));
        $executor->setAction($action);

        return $executor;
    }

    protected function createActionTrigger(Model\UserAction $action, string $type = null): View
    {
        $viewType = array_merge(['default' => [$this, 'getDefaultTrigger']], $this->triggerSeed[$type] ?? []);
        if ($seed = $viewType[$this->getModelId($action)][$action->shortName] ?? null) {
        } elseif ($seed = $viewType[$action->shortName] ?? null) {
        } else {
            $seed = $viewType['default'];
        }

        $seed = is_array($seed) && is_callable($seed) ? call_user_func($seed, $action, $type) : $seed;

        if ($action instanceof CustomUserAction) {
            $seed = Factory::mergeSeeds($action->ui['executorTrigger'] ?? [], $seed);
        }

        return Factory::factory($seed);
    }
}

class CustomForm extends Form
{
    protected function init(): void
    {
        parent::init();

        // demo - allow custom modal form executor to be recognized easily
        $this->style['padding'] = '10px';
        $this->style['background-color'] = '#ccf';
    }

    public function addControl(string $name, $control = [], $field = []): Form\Control
    {
        // demo - handle self::addControl() calls
        // the calls are made by StepExecutorTrait::doArgs(), the result is is unused there,
        // but you should create the form controls and place/add them via custom logic to desired places/layouts
        return $this->layout->addControl($name, $control, $field);
    }
}

class CustomModalExecutor extends ModalExecutor
{
}
