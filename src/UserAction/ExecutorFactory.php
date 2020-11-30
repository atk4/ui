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
use atk4\ui\Modal;
use atk4\ui\View;

/**
 * Class ExecutorFactory
 * Contains static method for generating.
 */
class ExecutorFactory
{
    public const JS_EXECUTOR = 'jsExecutor';
    public const MODAL_EXECUTOR = 'modalExecutor';
    public const MODAL_BUTTON = 'modalExecutor';
    public const TABLE_BUTTON = 'table';
    public const CARD_BUTTON = 'card';

    /** @var array default executor seed. */
    protected static $executorSeed = [
        self::JS_EXECUTOR => [JsCallbackExecutor::class],
        self::MODAL_EXECUTOR => [ModalExecutor::class],
    ];

    /** @var array Executor seed for specific Model user action. */
    protected static $actionExecutorSeed = [];

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
    ];

    /**
     * Register an executor for a specific model User action.
     */
    public static function registerActionExecutor(UserAction $action, array $seed)
    {
        self::$actionExecutorSeed[self::getModelId($action)][$action->short_name] = $seed;
    }

    public static function registerActionTrigger(string $type, array $seed, UserAction $action = null)
    {
        if ($action) {
            self::$actionTriggerSeed[$type][self::getModelId($action)][$action->short_name] = $seed;
        } else {
            self::$actionTriggerSeed[$type] = $seed;
        }
    }

    /**
     * Register a caption for a model user action.
     * Can be apply globally, i.e. to all action using the same name
     * of specifically, i.e. only for the action name in specific model.
     */
    public static function registerActionCaption(UserAction $action, $caption, $isSpecific = false)
    {
        if ($isSpecific) {
            self::$actionCaption[self::getModelId($action)][$action->short_name] = $caption;
        } else {
            self::$actionCaption[$action->short_name] = $caption;
        }
    }

    /**
     * Create proper executor  based on action properties.
     */
    public static function create(UserAction $action, View $owner, string $required = null)
    {
        if ($required) {
            if (!(self::$executorSeed[$required] ?? null)) {
                throw (new Exception('Required executor type is not set.'))
                    ->addMoreInfo('type', $required);
            }
            $seed = self::$executorSeed[$required];
        } elseif ($seed = self::$actionExecutorSeed[self::getModelId($action)][$action->short_name] ?? null) {
        } else {
            $seed = (!$action->args && !$action->fields && !$action->preview)
                ? self::$executorSeed[self::JS_EXECUTOR]
                : self::$executorSeed[self::MODAL_EXECUTOR];
        }

        $executor = Factory::factory($seed);
        if ($executor instanceof Modal) {
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
        $viewType = array_merge(['default' => [__CLASS__, 'self::getDefaultTrigger']], self::$actionTriggerSeed[$type] ?? []);
        if ($seed = $viewType[self::getModelId($action)][$action->short_name] ?? null) {
        } elseif ($seed = $viewType[$action->short_name] ?? null) {
        } else {
            $seed = $viewType['default'];
        }

        $seed = is_array($seed) && is_callable($seed) ? call_user_func($seed, $action, $type) : $seed;

        return Factory::factory($seed);
    }

    /**
     * Return executor default button seed based on action.
     */
    protected static function getDefaultTrigger(UserAction $action, string $type = null): array
    {
        $seed = [Button::class, self::getActionCaption($action)];
        if ($type === self::MODAL_BUTTON || $type === self::CARD_BUTTON) {
            $seed[] = 'blue';
        }

        return $seed;
    }

    /**
     * Return action caption set in actionLabel or default.
     */
    public static function getActionCaption(UserAction $action): string
    {
        if ($caption = self::$actionCaption[self::getModelId($action)][$action->short_name] ?? null) {
        } elseif ($caption = self::$actionCaption[$action->short_name] ?? null) {
        } else {
            $caption = $action->getCaption();
        }

        return is_array($caption) && is_callable($caption) ? call_user_func($caption, $action) : $caption;
    }

    /**
     * Return label for add model UserAction.
     */
    protected static function getAddActionCaption(UserAction $action): string
    {
        return 'Add ' . $action->getModel()->caption ?? '';
    }

    private static function getModelId(UserAction $action)
    {
        return strtolower(str_replace(' ', '_', $action->getModel()->getModelCaption()));
    }
}
