
.. _grid:

====
Grid
====

.. php:namespace:: atk4\ui
.. php:class:: Grid

If you didn't read documentation on :ref:`table` you should start with that. While table implements the actual
data rendering, Grid component supplies various enhancements around it, such as paginator, quick-search, toolbar
and others by relying on other components.

Using Grid
==========

Here is a simple usage::

    $app->add('Grid')->setModel(new Country($db));

To make your grid look nicer, you might want to add some buttons and enable quicksearch::

    $grid = $app->add('Grid');
    $grid->setModel(new Country($db));

    $grid->addQuickSearch();
    $grid->menu->addItem('Reload Grid', new \atk4\ui\jsReload($grid));

Adding Menu Items
=================

.. php:attr: $menu

.. php:method: addButton($label)

Grid top-bar which contains QuickSearch is implemented using Semantic UI "ui menu". With that
you can add additional items and use all features of a regular :php:class:`Menu`::

    $sub = $grid->menu->addMenu('Drop-down');
    $sub->addItem('Test123');

For compatibility grid supports addition of the buttons to the menu, but there are several
Semantic UI limitations that wouldn't allow to format buttons nicely::

    $grid->addButton('Hello');

If you don't need menu, you can disable menu bar entirely::

    $grid = $app->add(['Grid', 'menu' => false]);

Adding Quick Search
===================

.. php:attr: $quickSearch

.. php:method: addQuickSearch($fields = [])

After you have associated grid with a model using :php:class:`View::setModel()` you can
include quick-search component::

    $grid->addQuickSearch(['name', 'surname']);

If you don't specify argument, then search will be done by a models title field.
(http://agile-data.readthedocs.io/en/develop/model.html#title-field)

Paginator
=========

.. php:attr: $paginator

.. php:attr: $ipp

Grid comes with a paginator already. You can disable it by setting $paginator property to false. You can use $ipp
to specify different number of items per page::

    $grid->ipp = 10;

Actions
=======

.. php:attr: $actions

.. php:method: addAction($button, $action, $confirm = false)

:php:class:`Table` supports use of :php:class:`TableColumn\Actions`, which allows to display button for each row.
Calling addAction() provides a useful short-cut for creating column-based actions.

$button can be either a string (for a button label) or something like `['icon'=>'book']`.

If $confirm is set to true, then user will see a confirmation when he clicks on the action (yes/no).

Calling this method multiple times will add button into same action column.

See :php:meth:`TableColumn\Actions::addAction`

.. php:method: addModalAction($button, $title, $callback)

Similar to addAction, but when clicking a button, will open a modal dialog and execute $callback
to populate a content::

    $grid->addModalAction('Details', 'Additional Details', function($p, $id) use ($grid) {

        // $id of the record which was clicked
        // $grid->model->load($id);

        $p->add('LoremIpsum');
    });

Calling this method multiple times will add button into same action column.

See :php:meth:`TableColumn\Actions::addModal`

Selection
=========

Grid can have a checkbox column for you to select elements. It relies on :php:class:`TableColumn\CheckBox`, but will
additionally place this column before any other column inside a grid. You can use :php:meth:`TableColumn\CheckBox::jsChecked()`
method to reference value of selected checkboxes inside any :ref:`js_action`::

    $sel = $grid->addSelection();
    $grid->menu->addItem('show selection')->on('click', new \atk4\ui\jsExpression(
        'alert("Selected: "+[])', [$sel->jsChecked()]
    ));

Sorting
=======

.. php:attr: $sortable

When grid is associated with a model that supports order, it will automatically make itself sortable. You can
override this behaviour by setting $sortable property to `true` or `false`.

Additionally you may set list of sortable fields to a sortable property if you wish that your grid would be
sortable only for those columns.

See also :php:attr:`Table::$sortable`.


Advanced Usage
==============

.. php:attr: $table

You can use a different component instead of default :php:class:`Table` by injecting $table property.
