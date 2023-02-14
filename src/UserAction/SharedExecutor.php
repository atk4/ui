<?php

declare(strict_types=1);

namespace Atk4\Ui\UserAction;

use Atk4\Ui\AbstractView;
use Atk4\Ui\Js\JsExpressionable;

class SharedExecutor
{
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
     * @return array<int, JsExpressionable>
     */
    public function jsExecute(array $urlArgs): array
    {
        return $this->getExecutor()->jsExecute($urlArgs); // @phpstan-ignore-line TODO dedup JS
    }
}
