<?php

declare(strict_types=1);

namespace Atk4\Ui\UserAction;

use Atk4\Core\HookTrait;
use Atk4\Data\Model;
use Atk4\Ui\Button;
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

    public Model\UserAction $action;

    /** @var bool display header or not */
    public $hasHeader = true;

    /** @var string|null header description */
    public $description;

    /** @var string display message when action is disabled */
    public $disableMsg = 'Action is disabled and cannot be executed';

    /** @var Button|array<mixed> Button that trigger the action. Either as an array seed or object */
    public $executorButton;

    /** @var array<string, mixed> */
    protected array $arguments = [];

    /** @var string display message when missing arguments */
    public $missingArgsMsg = 'Insufficient arguments';

    /** @var JsExpressionable|\Closure<T of Model>($this, T): ?JsBlock JS expression to return if action was successful, e.g "new JsToast('Thank you')" */
    protected $jsSuccess;

    #[\Override]
    public function getAction(): Model\UserAction
    {
        return $this->action;
    }

    #[\Override]
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
     *
     * @param array<string, mixed> $arguments
     */
    public function setArguments(array $arguments): void
    {
        // TODO: implement mechanism for validating arguments based on definition

        $this->arguments = array_merge($this->arguments, $arguments);
    }

    #[\Override]
    protected function recursiveRender(): void
    {
        $this->action; // assert action is set @phpstan-ignore expr.resultUnused

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
    #[\Override]
    public function executeModelAction(): JsBlock
    {
        $args = [];
        foreach ($this->action->args as $key => $val) {
            $args[] = $this->arguments[$key];
        }

        $return = $this->action->execute(...$args);

        $jsSuccess = $this->jsSuccess instanceof \Closure
            ? ($this->jsSuccess)($this, $this->action->getModel())
            : $this->jsSuccess;

        return JsBlock::fromHookResult($this->hook(self::HOOK_AFTER_EXECUTE, [$return]) // @phpstan-ignore ternary.shortNotAllowed
            ?: ($jsSuccess ?? new JsToast('Success' . (is_string($return) ? (': ' . $return) : ''))));
    }

    public function addHeader(): void
    {
        if ($this->hasHeader) {
            Header::addTo($this, [$this->action->getCaption(), 'subHeader' => $this->description ?? $this->action->getDescription()]);
        }
    }
}
