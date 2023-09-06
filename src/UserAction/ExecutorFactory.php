<?php

declare(strict_types=1);

namespace Atk4\Ui\UserAction;

use Atk4\Core\Factory;
use Atk4\Core\WarnDynamicPropertyTrait;
use Atk4\Data\Model\UserAction;
use Atk4\Ui\AbstractView;
use Atk4\Ui\Button;
use Atk4\Ui\Exception;
use Atk4\Ui\MenuItem;
use Atk4\Ui\View;

class ExecutorFactory
{
    use WarnDynamicPropertyTrait;

    public const JS_EXECUTOR = self::class . '@jsExecutorSeed';
    public const STEP_EXECUTOR = self::class . '@stepExecutorSeed';
    public const CONFIRMATION_EXECUTOR = self::class . '@confirmationExecutorClass';

    public const BASIC_BUTTON = self::class . '@basicButton';
    public const MODAL_BUTTON = self::class . '@modalExecutorButton';
    public const TABLE_BUTTON = self::class . '@tableButton';
    public const CARD_BUTTON = self::class . '@cardButton';
    public const MENU_ITEM = self::class . '@menuItem';
    public const TABLE_MENU_ITEM = self::class . '@tableMenuItem';

    /** @var string */
    public $buttonPrimaryColor = 'primary';

    /**
     * Store basic type of executor to use for create method.
     * Basic type can be changed or added globally via the registerTypeExecutor method.
     * A specific model/action executor may be set via the registerExecutor method.
     *
     * @var array<string, array>
     */
    protected $executorSeed = [
        self::JS_EXECUTOR => [JsCallbackExecutor::class],
        self::STEP_EXECUTOR => [ModalExecutor::class],
        self::CONFIRMATION_EXECUTOR => [ConfirmationExecutor::class],
    ];

    /**
     * Store caption to use for action.
     * Can be apply globally per action name
     * or specifically per model/action name.
     *
     * Can be set to a callable method in order
     * to customize the return caption further more.
     *
     * @var array<string, string|array<string, string>>
     */
    protected $triggerCaption = [
        self::MODAL_BUTTON => [
            'add' => 'Save',
            'edit' => 'Save',
        ],
    ];

    /**
     * Store seed|View|callable method
     * to use for creating UI View Object
     * They can be store per either view type or
     * model/action name.
     *
     * @var array<string, array|View>
     */
    protected $triggerSeed = [
        self::TABLE_BUTTON => [
            'edit' => [Button::class, null, 'icon' => 'edit'],
            'delete' => [Button::class, null, 'icon' => 'red trash'],
        ],
        self::MENU_ITEM => [
            'add' => [__CLASS__, 'getAddMenuItem'],
        ],
    ];

    /**
     * Register an executor for a basic type.
     */
    public function registerTypeExecutor(string $type, array $seed): void
    {
        $this->executorSeed[$type] = $seed;
    }

    /**
     * Register an executor instance for a specific model User action.
     */
    public function registerExecutor(UserAction $action, array $seed): void
    {
        $this->executorSeed[$this->getModelKey($action)][$action->shortName] = $seed;
    }

    /**
     * Register a trigger for a specific View type.
     * Trigger can be specify per action or per model/action.
     *
     * @param array|View $seed
     */
    public function registerTrigger(string $type, $seed, UserAction $action, bool $isSpecific = false): void
    {
        if ($isSpecific) {
            $this->triggerSeed[$type][$this->getModelKey($action)][$action->shortName] = $seed;
        } else {
            $this->triggerSeed[$type][$action->shortName] = $seed;
        }
    }

    /**
     * Set an action trigger type to use it's default seed.
     */
    public function useTriggerDefault(string $type): void
    {
        $this->triggerSeed[$type] = [];
    }

    /**
     * Register a trigger caption.
     */
    public function registerCaption(UserAction $action, string $caption, bool $isSpecific = false, string $type = null): void
    {
        if ($isSpecific) {
            $this->triggerCaption[$this->getModelKey($action)][$action->shortName] = $caption;
        } elseif ($type) {
            $this->triggerCaption[$type][$action->shortName] = $caption;
        } else {
            $this->triggerCaption[$action->shortName] = $caption;
        }
    }

    /**
     * @return ($type is self::MENU_ITEM ? MenuItem : ($type is self::TABLE_MENU_ITEM ? MenuItem : Button))
     */
    public function createTrigger(UserAction $action, string $type = null): View
    {
        return $this->createActionTrigger($action, $type); // @phpstan-ignore-line
    }

    public function getCaption(UserAction $action, string $type = null): string
    {
        return $this->getActionCaption($action, $type);
    }

    /**
     * @return AbstractView&ExecutorInterface
     */
    public function createExecutor(UserAction $action, View $owner, string $requiredType = null): ExecutorInterface
    {
        if ($requiredType !== null) {
            if (!($this->executorSeed[$requiredType] ?? null)) {
                throw (new Exception('Required executor type is not set'))
                    ->addMoreInfo('type', $requiredType);
            }
            $seed = $this->executorSeed[$requiredType];
        } else {
            $seed = $seed = $this->executorSeed[$this->getModelKey($action)][$action->shortName] ?? null;
            if ($seed === null) {
                // if no type is register, determine executor to use base on action properties
                if ($action->confirmation instanceof \Closure) {
                    $seed = $this->executorSeed[self::CONFIRMATION_EXECUTOR];
                } else {
                    $seed = (!$action->args && !$action->fields && !$action->preview)
                            ? $this->executorSeed[self::JS_EXECUTOR]
                            : $this->executorSeed[self::STEP_EXECUTOR];
                }
            }
        }

        /** @var AbstractView&ExecutorInterface */
        $executor = $owner->add(Factory::factory($seed));
        $executor->setAction($action);

        return $executor;
    }

    /**
     * Create executor View for firing model user action.
     */
    protected function createActionTrigger(UserAction $action, string $type = null): View
    {
        $viewType = array_merge(['default' => [$this, 'getDefaultTrigger']], $this->triggerSeed[$type] ?? []);
        $seed = $viewType[$this->getModelKey($action)][$action->shortName] ?? null;
        if ($seed === null) {
            $seed = $viewType[$action->shortName] ?? null;
            if ($seed === null) {
                $seed = $viewType['default'];
            }
        }

        if (is_array($seed) && is_callable($seed)) {
            $seed = call_user_func($seed, $action, $type);
        }

        return Factory::factory($seed);
    }

    /**
     * Return executor default trigger seed based on type.
     */
    protected function getDefaultTrigger(UserAction $action, string $type = null): array
    {
        switch ($type) {
            case self::CARD_BUTTON:
            case self::TABLE_BUTTON:
            case self::MODAL_BUTTON:
                $seed = [Button::class, $this->getActionCaption($action, $type)];
                if ($type === self::MODAL_BUTTON || $type === self::CARD_BUTTON) {
                    $seed['class.' . $this->buttonPrimaryColor] = true;
                }

                break;
            case self::MENU_ITEM:
                $seed = [MenuItem::class, $this->getActionCaption($action, $type)];

                break;
            case self::TABLE_MENU_ITEM:
                $seed = [MenuItem::class, $this->getActionCaption($action, $type), 'name' => false, 'class.item' => true];

                break;
            default:
                $seed = [Button::class, $this->getActionCaption($action, $type)];
        }

        return $seed;
    }

    /**
     * Return action caption set in actionLabel or default.
     */
    protected function getActionCaption(UserAction $action, string $type = null): string
    {
        $caption = $this->triggerCaption[$type][$action->shortName] ?? null;
        if ($caption === null) {
            $caption = $this->triggerCaption[$this->getModelKey($action)][$action->shortName] ?? null;
            if ($caption === null) {
                $caption = $this->triggerCaption[$action->shortName] ?? null;
                if ($caption === null) {
                    $caption = $action->getCaption();
                }
            }
        }

        if (is_array($caption) && is_callable($caption)) {
            $caption = call_user_func($caption, $action);
        }

        return $caption;
    }

    /**
     * Return Add action seed for menu item.
     */
    protected function getAddMenuItem(UserAction $action): array
    {
        return [MenuItem::class, $this->getAddActionCaption($action), 'icon' => 'plus'];
    }

    /**
     * Return label for add model UserAction.
     */
    protected function getAddActionCaption(UserAction $action): string
    {
        return 'Add' . ($action->getModel()->caption ? ' ' . $action->getModel()->caption : '');
    }

    protected function getModelKey(UserAction $action): string
    {
        return $action->getModel()->getModelCaption();
    }
}
