:::{php:namespace} Atk4\Ui
:::

(crud)=

# Crud

:::{php:class} Crud
:::

Crud class offers a very usable extension to {php:class}`Grid` class, which automatically adds actions for deleting,
updating and adding records as well as linking them with corresponding Model actions.

:::{important}
If you only wish to display a non-interactive table use {php:class}`Table` class. If you need to
display Data Grid with some custom actions (not update/delete/add) or if you want to use your own editing
mechanism (such as edit data on separate page, not inside a modal), use {php:class}`Grid`
:::

:::{important}
ATK Addon - MasterCrud implements a higher-level multi-model management solution, that takes
advantage of model relations and traversal to create multiple levels of Cruds: https://github.com/atk4/mastercrud
:::

## Using Crud

The basic usage of Crud is:

```
Crud::addTo($app)->setModel(new Country($app->db));
```

Users are now able to fully interact with the table. There are ways to restrict which "rows" and which "columns" user
can access. First we can only allow user to read, manage and delete only countries that are part of European Union:

```
$euCountries = new Country($app->db);
$euCountries->addCondition('is_eu', true);

Crud::addTo($app)->setModel($euCountries);
```

After that column `is_eu` will not be editable to the user anymore as it will be marked `system` by `addCondition`.

You can also specify which columns you would like to see on the grid:

```
$crud->setModel($euCountries, ['name']);
```

This restriction will apply to both viewing and editing, but you can fine-tune that by specifying one of many
parameters to Crud.

## Disabling Actions

By default Crud allows all four operations - creating, reading, updating and deleting. These action is set by default in model
action. It is possible to disable these default actions by setting their system property to true in your model:

```
$euCountries->getUserAction('edit')->system = true;
```

Model action using system property set to true, will not be display in Crud. Note that action must be setup prior to use
`$crud->setModel($euCountries)`

## Specifying Fields (for different views)

:::{php:attr} displayFields
:::

Only fields name set in this property will be display in Grid. Leave empty for all fields.

:::{php:attr} editFields
:::

If you'd like to have different fields in the grid of the CRUD, but you need more/different fields in the editing modal (which opens when clicking on an entry),
you can choose here the fields that are available in the editing modal window.

:::{important}
Both views (overview and editing view) refer to the same model, just the fields shown in either of them differ
:::

Example:

```
$crud = \Atk4\Ui\Crud::addTo($app);
$model = new \Atk4\Data\Model($app->db);
$crud->displayFields(['field1, field2']);
$crud->editFields(['field1, field2, field3, field4']);
```

:::{php:attr} addFields
:::

Through those properties you can specify which fields to use when form is display for add and edit action.
Field name add here will have priorities over the action fields properties. When set to null, the action fields property
will be used.

## Custom Form Behavior

{php:class}`Form` in Agile UI allows you to use many different things, such as custom layouts. With Crud you can
specify your own form behavior using a callback for action:

```
// callback for model action add form
$g->onFormAdd(function (Form $form, ModalExecutor $ex) {
    $form->js(true, $form->getControl('name')->jsInput()->val('Entering value via javascript'));
});

// callback for model action edit form
$g->onFormEdit(function (Form $form, ModalExecutor $ex) {
    $form->js(true, $form->getControl('name')->jsInput()->attr('readonly', true));
});

// callback for both model action edit and add
$g->onFormAddEdit(function (Form $form, ModalExecutor $ex) {
    $form->onSubmit(function (Form $form) use ($ex) {
        return new \Atk4\Ui\Js\JsBlock([
            $ex->hide(),
            new \Atk4\Ui\Js\JsToast('Submit all right! This demo does not saved data.'),
        ]);
    });
});
```

Callback function will receive the Form and ActionExecutor as arguments.

## Changing titles

:::{important}
Changing the title of the CRUD's grid view must be done before setting the model.
Changing the title of the modal of a CRUD's modal window must be done after loading the model.
Otherwise the changes will have no effect.
:::

Here's an example:

```
$crud = \Atk4\Ui\Crud::addTo($app);
$model = new \Atk4\Data\Model($app->db);
$model->getUserAction('add')->description = 'New title for adding a record'; // the button of the overview - must be loaded before setting model
$crud->setModel($model);
$model->getUserAction('add')->ui['executor']->title = 'New title for modal'; // the button of the modal - must be rendered after setting model
```

## Notification

:::{php:attr} notifyDefault
:::

:::{php:attr} saveMsg
:::

:::{php:attr} deleteMsg
:::

:::{php:attr} defaultMsg
:::

When a model action execute in Crud, a notification to user is display. You can specify your notifier default seed using
`$notifyDefault`. The notifier message may be set via `$saveMsg`, `$deleteMsg` or `$defaultMsg` property.
