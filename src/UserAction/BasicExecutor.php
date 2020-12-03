<?php

declare(strict_types=1);

namespace Atk4\Ui\UserAction;

use Atk4\Core\HookTrait;
use Atk4\Data\Model;
use Atk4\Ui\Button;
use Atk4\Ui\Exception;
use Atk4\Ui\JsExpressionable;
use Atk4\Ui\JsToast;
use Atk4\Ui\Message;

class BasicExecutor extends \Atk4\Ui\View implements ExecutorInterface
{
    use HookTrait;

    /** @const string */
    public const HOOK_AFTER_EXECUTE = self::class . '@afterExecute';

    /**
     * @var Model\UserAction
     */
    public $action;

    /**
     * @var bool display header or not
     */
    public $hasHeader = true;

    /**
     * @var string header description
     */
    public $description;

    /**
     * @var string display message when action is disabled
     */
    public $disableMsg = 'Action is disabled and cannot be executed';

    /**
     * @var Button | array  Button that trigger the action. Either as an array seed or object
     */
    public $executorButton = [Button::class, 'Confirm', 'primary'];

    /**
     * @var array
     */
    protected $arguments = [];

    /**
     * @var string display message when missing arguments
     */
    public $missingArgsMsg = 'Insufficient arguments';

    /**
     * @var array list of validated arguments
     */
    protected $validArguments = [];

    /**
     * @var JsExpressionable array|\Closure JsExpression to return if action was successful, e.g "new JsToast('Thank you')"
     */
    protected $jsSuccess;

    /**
     * Associate executor with action.
     */
    public function setAction(Model\UserAction $action): void
    {
        $this->action = $action;
    }

    /**
     * Provide values for named arguments.
     */
    public function setArguments(array $arguments)
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
    public function hasAllArguments()
    {
        foreach ($this->action->args as $key => $val) {
            if (!isset($this->arguments[$key])) {
                return false;
            }
        }

        return true;
    }

    protected function initPreview()
    {
        // lets make sure that all arguments are supplied
        if (!$this->hasAllArguments()) {
            Message::addTo($this, ['type' => 'error', $this->missingArgsMsg]);

            return;
        }

        $this->addHeader();

        \Atk4\Ui\Button::addToWithCl($this, $this->executorButton)->on('click', function () {
            return $this->jsExecute();
        });
    }

    /**
     * Will call $action->execute() with the correct arguments.
     *
     * @return mixed
     */
    public function jsExecute()
    {
        $args = [];

        foreach ($this->action->args as $key => $val) {
            $args[] = $this->arguments[$key];
        }

        $return = $this->action->execute(...$args);

        $success = $this->jsSuccess instanceof \Closure
            ? ($this->jsSuccess)($this, $this->action->getOwner())
            : $this->jsSuccess;

        return ($this->hook(self::HOOK_AFTER_EXECUTE, [$return]) ?: $success) ?: new JsToast('Success' . (is_string($return) ? (': ' . $return) : ''));
    }

    /**
     * Will add header if set.
     */
    public function addHeader()
    {
        if ($this->hasHeader) {
            \Atk4\Ui\Header::addTo($this, [$this->action->getCaption(), 'subHeader' => $this->description ?: $this->action->getDescription()]);
        }
    }
}
