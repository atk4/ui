<?php

declare(strict_types=1);

namespace Atk4\Ui\UserAction;

use Atk4\Data\Model;
use Atk4\Ui\View;

/**
 * Multi entity views like CardDeck can render many items with the same UA, to improve
 * the performance and reduce the total page size, render the UA executors only once.
 */
class SharedExecutorsContainer extends View
{
    /** @var array<string, SharedExecutor> */
    public array $sharedExecutors = [];

    public function getExecutor(Model\UserAction $action): SharedExecutor
    {
        $action->getOwner()->assertIsModel(); // @phpstan-ignore-line
        $this->getOwner()->model->assertIsModel($action->getModel());

        if (!isset($this->sharedExecutors[$action->shortName])) {
            $ex = $this->getExecutorFactory()->createExecutor($action, $this);
            $ex->executeModelAction();
            $this->sharedExecutors[$action->shortName] = new SharedExecutor($ex);
        }

        return $this->sharedExecutors[$action->shortName];
    }
}
