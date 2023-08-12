<?php

declare(strict_types=1);

namespace Atk4\Ui\UserAction;

use Atk4\Core\HookTrait;
use Atk4\Data\Model;
use Atk4\Ui\Button;
use Atk4\Ui\Exception;
use Atk4\Ui\Header;
use Atk4\Ui\Js\JsBlock;
use Atk4\Ui\Js\JsExpressionable;
use Atk4\Ui\Js\JsToast;
use Atk4\Ui\Message;
use Atk4\Ui\View;

class BasicExecutor extends View implements ExecutorInterface
{
    use HookTrait;

    public const HOOK_AFTER_EXECUTE = self::class . '@afterExecute';

    /** @var Model\UserAction|null */
    public $action;

    /** @var bool display header or not */
    public $hasHeader = true;

    /** @var string|null header description */
    public $description;

    /** @var string display message when action is disabled */
    public $disableMsg = 'Action is disabled and cannot be executed';

    /** @var Button|array Button that trigger the action. Either as an array seed or object */
    public $executorButton;

    /** @var array */
    protected $arguments = [];

    /** @var string display message when missing arguments */
    public $missingArgsMsg = 'Insufficient arguments';

    /** @var array list of validated arguments */
    protected $validArguments = [];

    /** @var JsExpressionable|\Closure JS expression to return if action was successful, e.g "new JsToast('Thank you')" */
    protected $jsSuccess;

    public function getAction(): Model\UserAction
    {
        return $this->action;
    }

    public function setAction(Model\UserAction $action)
    {
        $this->action = $action;
        if (!$this->executorButton) {
            $this->executorButton = $this->getExecutorFactory()->createTrigger($action, ExecutorFactory::BASIC_BUTTON);
        }

        return $this;
    }

    /**
     * Provide values for named arguments.
     */
    public function setArguments(array $arguments): void
    {
        // TODO: implement mechanism for validating arguments based on definition

        $this->arguments = array_merge($this->arguments, $arguments);
    }

    protected function recursiveRender(): void
    {
        if (!$this->action) {
            throw new Exception('Action is not set. Use setAction()');
        }

        // check action can be called
        if ($this->action->enabled) {
            $this->initPreview();
        } else {
            Message::addTo($this, ['type' => 'error', $this->disableMsg]);

            return;
        }

        parent::recursiveRender();
    }

    /**
     * Check if all argument values have been provided.
     */
    public function hasAllArguments(): bool
    {
        foreach ($this->action->args as $key => $val) {
            if (!isset($this->arguments[$key])) {
                return false;
            }
        }

        return true;
    }

    protected function initPreview(): void
    {
        // lets make sure that all arguments are supplied
        if (!$this->hasAllArguments()) {
            Message::addTo($this, ['type' => 'error', $this->missingArgsMsg]);

            return;
        }

        $this->addHeader();

        Button::addToWithCl($this, $this->executorButton)->on('click', function () {
            return $this->executeModelAction();
        });
    }

    /**
     * Will call $action->execute() with the correct arguments.
     */
    public function executeModelAction(): JsBlock
    {
        $args = [];
        foreach ($this->action->args as $key => $val) {
            $args[] = $this->arguments[$key];
        }

        $return = $this->action->execute(...$args);

        $success = $this->jsSuccess instanceof \Closure
            ? ($this->jsSuccess)($this, $this->action->getModel())
            : $this->jsSuccess;

        return JsBlock::fromHookResult($this->hook(self::HOOK_AFTER_EXECUTE, [$return]) // @phpstan-ignore-line
            ?: ($success ?? new JsToast('Success' . (is_string($return) ? (': ' . $return) : ''))));
    }

    public function addHeader(): void
    {
        if ($this->hasHeader) {
            Header::addTo($this, [$this->action->getCaption(), 'subHeader' => $this->description ?? $this->action->getDescription()]);
        }
    }
}
