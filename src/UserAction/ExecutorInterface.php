<?php

declare(strict_types=1);

namespace Atk4\Ui\UserAction;

use Atk4\Data\Model\UserAction;

/**
 * ExecutorInterface can be implemented by a View that can be displayed on a page or in a modal window
 * and it would have an interaction with the user before invoking Action's callback.
 *
 * SomeExecutor::addTo($app)->setAction($model, 'action_name');
 *
 * Here are some suggested implementation for ExecutorInterface:
 *
 *  - MarkdownPreview. Requires $preview callback to be defined by the action. Will treat output as Markdown. Confirm button will
 *      execute action normally.
 *
 *
 *  - ArgumentForm. Displays a form which is populated with arguments. When submitting the form, action will be executed.
 *
 *  - ArgumentForm\Preview. extends Argument form by adding a "Preview" area to the right of the form. By default will
 *      treat $preview as text, but can also use a more specific view, such as a Pie Chart
 */
interface ExecutorInterface
{
    // Generate UI which is presented to the user before action is executed
    // https://github.com/php/php-src/pull/5708 protected methods cannot be defined in interface
    // protected function init(): void;

    /**
     * Will associate executor with the action.
     *
     * @return $this
     */
    public function setAction(UserAction $action);

    public function getAction(): UserAction;

    /**
     * @return mixed
     */
    public function executeModelAction();
}
