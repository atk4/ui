
.. _grid:

====
Grid
====

.. php:namespace:: Atk4\Ui
.. php:class:: Grid

If you didn't read documentation on :ref:`table` you should start with that. While table implements the actual
data rendering, Grid component supplies various enhancements around it, such as paginator, quick-search, toolbar
and others by relying on other components.

Using Grid
==========

Here is a simple usage::

    Grid::addTo($app)->setModel(new Country($db));

To make your grid look nicer, you might want to add some buttons and enable quicksearch::

    $grid = Grid::addTo($app);
    $grid->setModel(new Country($db));

    $grid->addQuickSearch();
    $grid->menu->addItem('Reload Grid', new \Atk4\Ui\JsReload($grid));

Adding Menu Items
=================

.. php:attr:: menu

.. php:method: addButton($label)

Grid top-bar which contains QuickSearch is implemented using Fomantic UI "ui menu". With that
you can add additional items and use all features of a regular :php:class:`Menu`::

    $sub = $grid->menu->addMenu('Drop-down');
    $sub->addItem('Test123');

For compatibility grid supports addition of the buttons to the menu, but there are several
Fomantic UI limitations that wouldn't allow to format buttons nicely::

    $grid->addButton('Hello');

If you don't need menu, you can disable menu bar entirely::

    $grid = Grid::addTo($app, ['menu' => false]);

Adding Quick Search
===================

.. php:attr:: quickSearch

.. php:method: addQuickSearch($fields = [], $hasAutoQuery = false)

After you have associated grid with a model using :php:class:`View::setModel()` you can
include quick-search component::

    $grid->addQuickSearch(['name', 'surname']);

If you don't specify argument, then search will be done by a models title field.
(https://agile-data.readthedocs.io/en/develop/model.html#title-field)

By default, quick search input field will query server when user press the Enter key. However, it is possible to make it
querying the server automatically, i.e. after the user has finished typing, by setting the auto query parameter::

    $grid->addQuickSearch(['name', 'surname'], true);

Paginator
=========

.. php:attr:: paginator

.. php:attr:: ipp

Grid comes with a paginator already. You can disable it by setting $paginator property to false. Alternatively you
can provide seed for the paginator or even entire object::

    $grid = Grid::addTo($app, ['paginator'=>['range'=>2]]);

You can use $ipp property to specify different number of items per page::

    $grid->ipp = 10;

JsPaginator
-----------

.. php:method:: addJsPaginator($ipp, $options = [], $container = null, $scrollRegion = 'Body')

JsPaginator will load table content dynamically when user scroll down the table window on screen.

    $table->addJsPaginator(30);

See :php:meth:`Table::addJsPaginator`

.. php:method:: addJsPaginatorInContainer($ipp, $containerHeight, $options = [], $container = null, $scrollRegion = 'Body')

Use this method if you want fixed table header when scrolling down table. In this case you have to set
fixed height of your table container.

Actions
=======

.. php:attr:: actions

.. php:method:: addAction($button, $action, $confirm = false)

:php:class:`Table` supports use of :php:class:`Table\\Column\\\Actions`, which allows to display button for each row.
Calling addAction() provides a useful short-cut for creating column-based actions.

$button can be either a string (for a button label) or something like `['icon'=>'book']`.

If $confirm is set to true, then user will see a confirmation when he clicks on the action (yes/no).

Calling this method multiple times will add button into same action column.

See :php:meth:`Table\\Column\\\Actions::addAction`

.. php:method:: addModalAction($button, $title, $callback)

Similar to addAction, but when clicking a button, will open a modal dialog and execute $callback
to populate a content::

    $grid->addModalAction('Details', 'Additional Details', function($p, $id) use ($grid) {

        // $id of the record which was clicked
        // $grid->model = $grid->model->load($id);

        LoremIpsum::addTo($p);
    });

Calling this method multiple times will add button into same action column.

See :php:meth:`Atk4\\Ui\\Table\\Column\\Actions::addModal`


Column Menus
============

.. php:method:: addDropdown($columnName, $items, $fx, $icon = 'caret square down', $menuId = null)

.. php:method:: addPopup($columnName, $popup = null, $icon = 'caret square down')

Methods addDropdown and addPopup provide a wrapper for :php:meth:`Atk4\\Ui\\Table\\Column::addDropdown` and
:php:meth:`Atk4\\Ui\\\Table\\Column::addPopup` methods.

Selection
=========

Grid can have a checkbox column for you to select elements. It relies on :php:class:`Table\\Column\\Checkbox`, but will
additionally place this column before any other column inside a grid. You can use :php:meth:`Table\\Column\\Checkbox::jsChecked()`
method to reference value of selected checkboxes inside any :ref:`js_action`::

    $sel = $grid->addSelection();
    $grid->menu->addItem('show selection')->on('click', new \Atk4\Ui\JsExpression(
        'alert("Selected: "+[])', [$sel->jsChecked()]
    ));

Sorting
=======

.. php:attr:: sortable

When grid is associated with a model that supports order, it will automatically make itself sortable. You can
override this behaviour by setting $sortable property to `true` or `false`.

You can also set $sortable property for each table column decorator. That way you can enable/disable sorting
of particular columns.

See also :php:attr:`Table::$sortable`.


Advanced Usage
==============

.. php:attr:: table

You can use a different component instead of default :php:class:`Table` by injecting $table property.
