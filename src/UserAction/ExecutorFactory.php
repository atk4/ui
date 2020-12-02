<?php
/**
 * Executor utility.
 */

declare(strict_types=1);

namespace atk4\ui\UserAction;

use atk4\core\Factory;
use atk4\data\Model;
use atk4\data\Model\UserAction;
use atk4\ui\Button;
use atk4\ui\Exception;
use atk4\ui\Item;
use atk4\ui\Modal;
use atk4\ui\View;

/**
 * Class ExecutorFactory
 * Contains static method for generating.
 */
class ExecutorFactory
{
    public const JS_EXECUTOR = self::class . '@jsExecutorSeed';
    public const MODAL_EXECUTOR = self::class . '@modalExecutorSeed';
    public const CONFIRMATION_EXECUTOR = self::class . '@confirmationExecutorClass';
    public const MODAL_BUTTON = self::class . '@modalExecutorButton';
    public const TABLE_BUTTON = self::class . '@tableButton';
    public const CARD_BUTTON = self::class . '@cardButton';
    public const MENU_ITEM = self::class . '@menuItem';
    public const TABLE_MENU_ITEM = self::class . '@tableMenuItem';

    public const BUTTON_PRIMARY_COLOR = 'blue';

    /**
     * Contains basic type of executor to use for create method.
     * Basic type can be changed or added globally via the registerTypeExecutor method.
     * A specific model/action executor may be set via the registerActionExecutor method.
     */
    protected static $executorSeed = [
        self::JS_EXECUTOR => [JsCallbackExecutor::class],
        self::MODAL_EXECUTOR => [ModalExecutor::class],
        self::CONFIRMATION_EXECUTOR => [ConfirmationExecutor::class],
    ];

    /**
     * action caption May be set per action generally or specifically per model/action.
     *
     * @var array[callable]|string
     */
    protected static $actionCaption = [
        'add' => [__CLASS__, 'self::getAddActionCaption'],
    ];

    /**
     * Seed can be defined by View type using generic action name
     *  or using specific model/action combination.
     *
     * @var array[]|callable
     */
    protected static $actionTriggerSeed = [
        self::MODAL_BUTTON => [
            'edit' => [Button::class, 'Save', 'blue'],
            'add' => [Button::class, 'Save', 'blue'],
        ],
        self::TABLE_BUTTON => [
            'edit' => [Button::class, null, 'icon' => 'edit'],
            'delete' => [Button::class, null, 'icon' => 'red trash'],
        ],
        self::MENU_ITEM => [
            'add' => [__CLASS__, 'self::getAddMenuItem'],
        ],
    ];

    /**
     * Register an executor for basic type.
     */
    public static function registerTypeExecutor(string $type, $seed)
    {
        static::$executorSeed[$type] = $seed;
    }

    /**
     * Register an executor for a specific model User action.
     */
    public static function registerActionExecutor(UserAction $action, array $seed)
    {
        static::$executorSeed[static::getModelId($action)][$action->short_name] = $seed;
    }

    /**
     * Register an action trigger for a specific type.
     * Trigger can be specify per action or per model/action.
     *
     * @param string|View $seed
     */
    public static function registerActionTrigger(string $type, $seed, UserAction $action, bool $isSpecific = false)
    {
        if ($isSpecific) {
            static::$actionTriggerSeed[$type][static::getModelId($action)][$action->short_name] = $seed;
        } else {
            static::$actionTriggerSeed[$type][$action->short_name] = $seed;
        }
    }

    /**
     * Set an action trigger type to use it's default seed.
     */
    public static function useActionTriggerDefault(string $type)
    {
        static::$actionTriggerSeed[$type] = [];
    }

    /**
     * Register a caption for a model user action.
     * Can be apply globally, i.e. to all action using the same name
     * of specifically, i.e. only for the action name in specific model.
     */
    public static function registerActionCaption(UserAction $action, string $caption, bool $isSpecific = false)
    {
        if ($isSpecific) {
            static::$actionCaption[static::getModelId($action)][$action->short_name] = $caption;
        } else {
            static::$actionCaption[$action->short_name] = $caption;
        }
    }

    /**
     * Create proper executor based on action properties.
     */
    public static function create(UserAction $action, View $owner, string $requiredType = null)
    {
        // required a specific executor type.
        if ($requiredType) {
            if (!(static::$executorSeed[$requiredType] ?? null)) {
                throw (new Exception('Required executor type is not set. Register it via the registerTypeExecutor method.'))
                    ->addMoreInfo('type', $requiredType);
            }
            $seed = static::$executorSeed[$requiredType];
        // check if executor is register for this model/action.
        } elseif ($seed = static::$executorSeed[static::getModelId($action)][$action->short_name] ?? null) {
        } else {
            // if no type is register, determine executor to use base on action properties.
            if (is_callable($action->confirmation)) {
                $seed = static::$executorSeed[static::CONFIRMATION_EXECUTOR];
            } else {
                $seed = (!$action->args && !$action->fields && !$action->preview)
                        ? static::$executorSeed[static::JS_EXECUTOR]
                        : static::$executorSeed[static::MODAL_EXECUTOR];
            }
        }

        $executor = Factory::factory($seed);
        if ($executor instanceof Modal) {
            // add modal to app->html for proper rendering on callback.
            if (!isset($owner->getApp()->html->elements[$executor->short_name])) {
                // very dirty hack, @TODO, attach modals in the standard render tree
                // but only render the result to a different place/html DOM
                $executor->viewForUrl = $owner;
                $executor = $owner->getApp()->html->add($executor, 'Modals'); //->setAction($action);
            }
        } else {
            $executor = $owner->add($executor);
        }

        $executor->setAction($action);

        return $executor;
    }

    /**
     * Create executor View for firing model user action.
     */
    public static function createActionTrigger(UserAction $action, string $type = null): View
    {
        $viewType = array_merge(['default' => [__CLASS__, 'self::getDefaultTrigger']], static::$actionTriggerSeed[$type] ?? []);
        if ($seed = $viewType[static::getModelId($action)][$action->short_name] ?? null) {
        } elseif ($seed = $viewType[$action->short_name] ?? null) {
        } else {
            $seed = $viewType['default'];
        }

        $seed = is_array($seed) && is_callable($seed) ? call_user_func($seed, $action, $type) : $seed;

        return Factory::factory($seed);
    }

    /**
     * Return executor default trigger seed based on type.
     */
    protected static function getDefaultTrigger(UserAction $action, string $type = null): array
    {
        switch ($type) {
            case self::CARD_BUTTON:
            case self::TABLE_BUTTON:
            case self::MODAL_BUTTON:
                $seed = [Button::class, static::getActionCaption($action)];
                if ($type === static::MODAL_BUTTON || $type === static::CARD_BUTTON) {
                    $seed[] = static::BUTTON_PRIMARY_COLOR;
                }

                break;
            case self::MENU_ITEM:
                $seed = [Item::class, static::getActionCaption($action), ['class' => 'item']];

                break;
            case self::TABLE_MENU_ITEM:
                $seed = [Item::class, static::getActionCaption($action), 'id' => false, ['class' => 'item']];

                break;
            default:
                $seed = [Button::class, static::getActionCaption($action)];
        }

        return $seed;
    }

    /**
     * Return action caption set in actionLabel or default.
     */
    public static function getActionCaption(UserAction $action): string
    {
        if ($caption = static::$actionCaption[static::getModelId($action)][$action->short_name] ?? null) {
        } elseif ($caption = static::$actionCaption[$action->short_name] ?? null) {
        } else {
            $caption = $action->getCaption();
        }

        return is_array($caption) && is_callable($caption) ? call_user_func($caption, $action) : $caption;
    }

    protected static function getAddMenuItem($action, $type)
    {
        return [Item::class, static::getAddActionCaption($action), 'icon' => 'plus'];
    }

    /**
     * Return label for add model UserAction.
     */
    protected static function getAddActionCaption(UserAction $action): string
    {
        return 'Add ' . $action->getModel()->caption ?? '';
    }

    protected static function getModelId(UserAction $action)
    {
        return strtolower(str_replace(' ', '_', $action->getModel()->getModelCaption()));
    }
}
