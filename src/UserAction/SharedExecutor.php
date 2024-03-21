<?php

declare(strict_types=1);

namespace Atk4\Ui\UserAction;

use Atk4\Core\WarnDynamicPropertyTrait;
use Atk4\Ui\AbstractView;
use Atk4\Ui\Js\JsBlock;

class SharedExecutor
{
    use WarnDynamicPropertyTrait;

    /** @var AbstractView&ExecutorInterface */
    private ExecutorInterface $executor;

    /**
     * @param AbstractView&ExecutorInterface $executor
     */
    public function __construct(ExecutorInterface $executor)
    {
        $this->executor = $executor;
    }

    /**
     * @return AbstractView&ExecutorInterface
     */
    public function getExecutor(): ExecutorInterface
    {
        return $this->executor;
    }

    /**
     * @param array<string, string> $urlArgs
     */
    public function jsExecute(array $urlArgs): JsBlock
    {
        // TODO executor::jsExecute() should be called only once, registered as a custom jQuery event and then
        // call the event from JS with arguments to improve performance, ie. render (possibly large) JS only once
        return $this->getExecutor()->jsExecute($urlArgs); // @phpstan-ignore-line
    }
}
