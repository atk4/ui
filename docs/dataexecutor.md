:::{php:namespace} Atk4\Ui
:::

(dataexecutor)=

# Data Action Executor

Data action executor in UI is parts of interactive components that can execute a Data model defined user action.
For more details on Data Model User Action please visit: https://atk4-data.readthedocs.io/en/develop/model.html#actions

Atk UI offers many types of action executor.
A model user action may contain many properties. Usually, you would choose the type of executor based on the action
definition. For example, an action that would required arguments prior to be executed can be set using
an ArgumentFormExecutor. Or actions that can run using a single button can use a JsCallbackExecutor.

Demo: https://ui.atk4.org/demos/data-action/actions.php

## Executor Interface

All executors must implement the ExecutorInterface or JsExecutorInterface interface.

:::{php:interface} UserAction\ExecutorInterface
:::

:::{php:interface} UserAction\JsExecutorInterface
:::

## Basic Executor

:::{php:class} UserAction\BasicExecutor
:::

This is the base view for most of the other action executors. This executor generally
required that necessary arguments needed to run the action has been set.
BasicExecutor will display:

- a button for executing the action;
- a header where action name and description are displayed;
- an error message if an action argument is missing;

## Preview Executor

:::{php:class} UserAction\PreviewExecutor
:::

This executor is specifically set in order to display the $preview property of the current model UserAction.
You can select to display the preview using regular console type container, regular text or using HTML content.

## Form Executor

:::{php:class} UserAction\FormExecutor
:::

This executor will display a form where user is required to fill in either all model fields or certain model fields
depending on the model UserAction $field property. Form control will depend on model field type.

## Argument Form Executor

:::{php:class} UserAction\ArgumentFormExecutor
:::

This executor will display a form but instead of filling form control with model field, it will use model UserAction
$args property. This is used when you need to ask user about an argument value prior to execute the action.
The type of form control type to be used in form will depend on how $args is setup within the model UserAction.

## JS Callaback Executor

:::{php:class} UserAction\JsCallbackExecutor
:::

This type of executor will output proper javascript that you can assign to a view event using View::on() method.
It is also possible to pass the UserAction argument via $_POST argument.

## Modal Executor

:::{php:class} UserAction\ModalExecutor
:::

The ModalExecutor is base on Modal view. This is a one size fits all for model UserAction. When setting the UserAction via the
ModelExecutor::setAction($action) method, it will automatically determine what step is require and will display each step
base on the action definition within a modal view:

- Step 1: Argument definition. If the action required arguments, then the modal will display a form and ask user
  to fill argument values required by the model UserAction;

- Step 2: Field definition. If the action required fields, then the modal will display a form and ask user to fill
  field values required by the model UserAction;

- Step 3: Preview. If the action preview is set, then the modal will display it prior to execute the action.

The modal title default is set from the UserAction::getDescription() method but can be override using the
Modal::$title property.

## Confirmation Executor

:::{php:class} UserAction\ConfirmationExecutor
:::

Like ModalExecutor, Confirmation executor is also based on a Modal view. It allow to display UserAction::confirmation property prior to
execute the action. Since UserAction::confirmation property may be set with a Closure function, this give a chance to
return specific record information to be displayed to user prior to execute the action.

Here is an example of an user action returning specific record information in the confirmation message:

```
$country->addUserAction('delete_country', [
    'caption' => 'Delete',
    'description' => 'Delete Country',
    'ui' => ['executor' => [\Atk4\Ui\UserAction\ConfirmationExecutor::class]],
    'confirmation' => function (Model\UserAction $action) {
        return 'Are you sure you want to delete this country: $action->getModel()->getTitle();
    },
    'callback' => 'delete',
]);
```

The modal title default is set from the UserAction::getDescription() method but can be override using the
Modal::$title property.

## Executor HOOK_AFTER_EXECUTE

Executors can use the HOOK_AFTER_EXECUTE hook in order to return javascript action after the model UserAction finish
executing. It is use in Crud for example in order to display users of successful model UserAction execution. Either by displaying
Toast messages or removing a row within a Crud table.

Some Ui View component, like Crud for example, will also set javascript action to return based on the UserAction::modifier property.
For example it the modifier property is set to MODIFIER_DELETE then Crud will know it has to delete a table row on the
other hand, if MODIFIER_UPDATE is set, then Table needs to be reloaded.

## The Executor Factory

:::{php:class} UserAction\ExecutorFactory
:::

:::{php:attr} executorSeed
:::

Executor factory is responsible for creating proper executor type in regards to the model user action being used.

The factory createExecutor method:

```
ExecutorFactory::createExecutor(UserAction $action, View $owner, $requiredType = null)
```

Based on parameter passed to the method, it will return proper executor for the model user action.

If $requiredType is set, then it will look for basic type executor already register in $executorSeed property
for that specific type.

When required is not set, it will first look for a specific executor that has been already register for the model/action.

If no executor type is found, then the createExecutor method will determine one, based on the model user action properties:

- if action contains a callable confirmation property, then, the executor create is based on CONFIRMATION_EXECUTOR type;
- if action contains use either, fields, argument or preview properties, then, the executor create is based on MODAL_EXECUTOR type;
- if action does not use any of the above properties, then, the executor create is based on JS_EXECUTOR type.

The createExecutor method also add the executor to the View passed as argument. However, note that when an executor View parent
class is of type Modal, then it will be attached to the $app->html view instead. This is because Modal view in ui needs
to be added to $app->html view in order to work correctly on reload.

### Changing or adding Executor type

Existing executor type can be change or added globally for all your user model actions via this method:

```
ExecutorFactory::registerTypeExecutor(string $type, array $seed): void
```

This will set a type to your own executor class. For example, a custom executor class can be set as a MODAL_EXECUTOR type
and all model user action that use this type will be executed using this custom executor instance.

Type may also be registered per specific model user action via this method:

```
ExecutorFactory::registerExecutor(UserAction $action, array $seed): void
```

For example, you need a custom executor to be created when using a specific model user action:

```
class MySpecialFormExecutor extends \Atk4\Ui\UserAction\ModalExecutor
{
    public function addFormTo(\Atk4\Ui\View $view): \Atk4\Ui\Form
    {
        $myView = MySpecialView::addTo($view);

        return parent::addFormTo($myView);
    }
}

// ...
ExecutorFactory::registerExecutor($action, [MySpecialFormExecutor::class]);
```

Then, when ExecutorFactory::createExecutor method is called for this $action, MySpecialExecutor instance will be create in order
to run this user model action.

### Triggering model user action

The Executor factory is also responsible for creating the UI view element, like regular, table or card button or menu
item that will fire the model user action execution.

The method is:

```
ExecutorFactory::createTrigger(UserAction $action, string $type = null): View
```

This method return an instance object for the proper type. When no type is supply, a default View Button object
is returned.

As per executor type, it is also possible to add or change already register type via the registerTrigger method:

```
ExecutorFactory::registerTrigger(string $type, $seed, UserAction $action, bool $isSpecific = false): void
```

Again, the type can be apply globally to all action using the same name or specifically for a certain model/action.

For example, changing default Table button for a specific model user action when this action is used inside a crud table:

```
ExecutorFactory::registerTrigger(
    ExecutorFactory::TABLE_BUTTON,
    [Button::class, null, 'icon' => 'mail'],
    $m->getUserAction('mail')
);
```

This button view will then be display in Crud when it use a model containing 'mail' user action.

### Overriding ExecutorFactory

Overriding the ExecutorFactory class is a good way of changing the look of all trigger element within your app or
within a specific view instance.

Example of changing button for Card, Crud and Modal executor globally within your app:

```
class MyFactory extends \Atk4\Ui\UserAction\ExecutorFactory
{
    protected static $actionTriggerSeed = [
        self::MODAL_BUTTON => [
            'edit' => [Button::class, 'Save', 'class.green' => true],
            'add' => [Button::class, 'Save', 'class.green' => true],
        ],
        self::TABLE_BUTTON => [
            'edit' => [Button::class, null, 'icon' => 'pencil'],
            'delete' => [Button::class, null, 'icon' => 'times red'],
        ],
        self::CARD_BUTTON => [
            'edit' => [Button::class, 'Edit', 'icon' => 'pencil', 'ui' => 'tiny button'],
            'delete' => [Button::class, 'Remove', 'icon' => 'times', 'ui' => 'tiny button'],
        ],
    ];

    protected static $actionCaption = [
        'add' => 'Add New Record',
    ];
}

// ...
$app->defaultExecutorFactory = $myFactory;
```

## Model UserAction assignment to View

It is possible to assign a model UserAction to the View::on() method directly:

```
$button->on('click', $model->getUserAction('my_action'));
```

By doing so, the View::on() method will automatically determine which executor is required to properly run the action.
If the model UserAction contains has either $fields, $args or $preview property set, then the ModalExecutor will be
used, JsCallback will be used otherwise.

It is possible to override this behavior by setting the $ui['executor'] property of the model UserAction, since View::on() method
will first look for that property prior to determine which executor to use.

Example of overriding executor assign to a button.:

```
$myAction = $model->getUserAction('my_action');
$myAction->ui['executor'] = $myExecutor;

$button->on('click', $myAction);
```

### Demo

For more information on how Model UserAction are assign to button and interact with user according to their definition,
please visit: [Assign action to button event](https://ui.atk4.org/demos/data-action/jsactions2.php)

You will find the UserAction definition for the demo [here](https://github.com/atk4/ui/blob/develop/demos/_includes/DemoActionsUtil.php)
