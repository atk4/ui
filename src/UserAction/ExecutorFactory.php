<?php
/**
 * Executor utility.
 */

declare(strict_types=1);

namespace atk4\ui\UserAction;

use atk4\core\Factory;
use atk4\data\Model\UserAction;
use atk4\ui\Button;
use atk4\ui\Modal;
use atk4\ui\View;

class ExecutorFactory
{
    public const MODAL_BUTTON = 'modalExecutor';
    public const TABLE_BUTTON = 'table';
    public const CARD_BUTTON = 'card';

    protected static $customExecutorClass = [];

    protected static $executorClass = [
        'jsExecutor' => JsCallbackExecutor::class,
        'modalExecutor' => ModalExecutor::class,
    ];

    protected static $actionLabel = [
        'add' => [__CLASS__, 'self::getAddActionCaption'],
    ];

    protected static $actionButton = [
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
    public static function registerExecutor(UserAction $action, ExecutorInterface $executor)
    {
        self::$customExecutorClass[$action->getModel()->caption][$action->short_name] = $executor;
    }

    /**
     * Create proper executor  based on action properties.
     */
    public static function create(UserAction $action, View $owner)
    {
        if (isset(self::$customExecutorClass[$action->getModel()->caption][$action->short_name])) {
            $executor = self::$customExecutorClass[$action->getModel()->caption][$action->short_name];
        } else {
            $executor = (!$action->args && !$action->fields && !$action->preview) ? [self::$executorClass['jsExecutor']] : [self::$executorClass['modalExecutor']];
        }

        $executor = Factory::factory($executor);
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
    public static function createActionButton(UserAction $action, string $type = null): View
    {
        $viewType = array_merge(['default' => [__CLASS__, 'self::getDefaultButtonSeed']], self::$actionButton[$type] ?? []);
        if (isset($viewType[$action->short_name]) && $seed = $viewType[$action->short_name]) {
            $seed = is_array($seed) && is_callable($seed) ? call_user_func($seed, $action) : $seed;
        } else {
            $seed = is_array($viewType['default']) && is_callable($viewType['default']) ? call_user_func($viewType['default'], $action, $type) : $viewType['default'];
        }

        return Factory::factory($seed);
    }

    /**
     * Return executor default button seed based on action.
     */
    protected static function getDefaultButtonSeed(UserAction $action, string $type = null): array
    {
        $seed = [Button::class, self::getActionCaption($action)];
        if ($type === self::MODAL_BUTTON) {
            $seed[] = 'blue';
        }

        return $seed;
    }

    /**
     * Return action caption set in actionLabel or default.
     */
    public static function getActionCaption(UserAction $action): string
    {
        if (isset(self::$actionLabel[$action->short_name]) && $label = self::$actionLabel[$action->short_name]) {
            return is_array($label) && is_callable($label) ? call_user_func($label, $action) : (string) $label;
        }

        return $action->getCaption();
    }

    /**
     * Return label for add model UserAction.
     */
    protected static function getAddActionCaption(UserAction $action): string
    {
        return 'Add ' . $action->getModel()->caption ?? '';
    }
}
