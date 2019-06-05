<?php

namespace atk4\ui\ActionExecutor;

/**
 * Interface ExecutorInterface can be implemented by a View that can be displayed on a page or in a modal window
 * and it would have an interaction with the user before invoking Action's callback.
 *
 * $app->add('SomeExecutor')->setAction($model, 'action_name');
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
interface Interface_
{
    /** Generate UI which is presented to the user before action is executed */
    public function init();

    /**
     * Will associate executor with the action.
     *
     * @param \atk4\data\UserAction\Action $action
     */
    public function setAction(\atk4\data\UserAction\Generic $action);
}
