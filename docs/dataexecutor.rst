
.. _dataexecutor:

====================
Data Action Executor
====================

Data action executor in UI is parts of interactive components that can execute a Data model defined user action.
For more details on Data Model User Action please visit: https://agile-data.readthedocs.io/en/develop/model.html#actions


Atk UI offers many types of action executor.
A model user action may contain many properties. Usually, you would choose the type of executor based on the action
definition. For example, an action that would required arguments prior to be executed can be set using
an ArgumentFormExecutor. Or actions that can run using a single button can use a JsCallbackExecutor.

Demo: https://ui.agiletoolkit.org/demos/data-action/actions.php

Executor Interface
==================

.. php:namespace:: Atk4\Ui\UserAction

All executors must implement the Executor or JsExecutor interface.

.. php:interface:: ExecutorInterface
.. php:interface:: JsExecutorInterface

Basic Executor
==============

.. php:class:: BasicExecutor

This is the base view for most of the other action executors. This executor generally
required that necessary arguments needed to run the action has been set.
BasicExecutor will display:

    - a button for executing the action;
    - a header where action name and description are displayed;
    - an error message if an action argument is missing;

Preview Executor
================

.. php:class:: PreviewExecutor

This executor is specifically set in order to display the $preview property of the current model UserAction.
You can select to display the preview using regular console type container, regular text or using html content.

Form Executor
=============

.. php:class:: FormExecutor

This executor will display a form where user is required to fill in either all model fields or certain model fields
depending on the model UserAction $field property. Form control will depend on model field type.

Argument Form Executor
======================

.. php:class:: ArgumentFormExecutor

This executor will display a form but instead of filling form control with model field, it will use model UserAction
$args property. This is used when you need to ask user about an argument value prior to execute the action.
The type of form control type to be used in form will depend on how $args is setup within the model UserAction.

Js Callaback Executor
=====================

.. php:class:: JsCallbackExecutor

This type of executor will output proper javascript that you can assign to a view event using View::on() method.
It is also possible to pass the UserAction argument via $_POST argument.

Modal Executor
==============

.. php:class:: ModalExecutor

The ModalExecutor is base on Modal view. This is a one size fits all for model UserAction. When setting the UserAction via the
ModelExecutor::setAction($action) method, it will automatically determine what step is require and will display each step
base on the action definition within a modal view:

    Step 1: Argument definition. If the action required arguments, then the modal will display a form and ask user
    to fill argument values required by the model UserAction;

    Step 2: Field definition. If the action required fields, then the modal will display a form and ask user to fill
    field values required by the model UserAction;

    Step 3: Preview. If the action preview is set, then the modal will display it prior to execute the action.

The modal title default is set from the UserAction::getDescription() method but can be override using the
Modal::$title property.

Confirmation Executor
=====================

.. php:class:: ConfirmationExecutor

Like ModalExecutor, Confirmation executor is also based on a Modal view. It allow to display UserAction::confirmation property prior to
execute the action. Since UserAction::confirmation property may be set with a Closure function, this give a chance to
return specific record information to be display to user prior to execute the action.

Here is an example of an user action returning specific record information in the confirmation message::

        $country->addUserAction(
            'delete_country',
            [
                'caption' => 'Delete',
                'description' => 'Delete Country',
                'ui' => ['executor' => [\Atk4\Ui\UserAction\ConfirmationExecutor::class]],
                'confirmation' => function ($action) {
                    return 'Are you sure you want to delete this country: $action->getModel()->getTitle();
                },
                'callback' => 'delete',
            ]
        );

The modal title default is set from the UserAction::getDescription() method but can be override using the
Modal::$title property.

Executor HOOK_AFTER_EXECUTE
============================

Executors can use the HOOK_AFTER_EXECUTE hook in order to return javascript action after the model UserAction finish
executing. It is use in Crud for example in order to display users of successful model UserAction execution. Either by displaying
Toast messages or removing a row within a Crud table.

Some Ui View component, like Crud for example, will also set javascript action to return based on the UserAction::modifier property.
For example it the modifier property is set to MODIFIER_DELETE then Crud will know it has to delete a table row on the
other hand, if MODIFIER_READ is set, then Table need to be reload.

Model UserAction assignment to View
===================================

It is possible to assign a model UserAction to the View::on() method directly::

    $button->on('click', $model->getUserAction('my_action'));

By doing so, the View::on() method will automatically determine which executor is required to properly run the action.
If the model UserAction contains has either $fields, $args or $preview property set, then the ModalExecutor will be
used, JsCallback will be used otherwise.

It is possible to override this behavior by setting the $ui['executor'] property of the model UserAction, since View::on() method
will first look for that property prior to determine which executor to use.

Example of overriding executor assign to a button.::

    $myAction = $model->getUserAction('my_action');
    $myAction->ui['executor'] = $myExecutor;

    $btn->on('click', $myAction);

Demo
----

For more information on how Model UserAction are assign to button and interact with user according to their definition,
please visit: `Assign action to button event <https://ui.agiletoolkit.org/demos/data-action/jsactions2.php>`_

You will find the UserAction definition for the demo `here <https://github.com/atk4/ui/blob/develop/demos/_includes/DemoActionsUtil.php>`_
