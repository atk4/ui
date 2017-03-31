
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

    $layout->add('Grid')->setModel(new Country($db));

To make your grid look nicer, you might want to add some buttons end enable quicksearch::

    $grid = $layout->add('Grid');
    $grid->setModel(new Country($db));

    $grid->addQuickSearch();
    $grid->menu->addItem('Reload Grid', new \atk4\ui\jsReload($grid));

Adding Menu Items
=================

.. php:attr: $menu

.. php:method: addButton($label)

Grid top-bar which contains QuickSearch is implemented using Semantic UI "ui menu". With that
you can add additional items and use all feautres of a regular :php:class:`Menu`::

    $sub = $grid->menu->addMenu('Drop-down');
    $sub->addItem('Test123');

For compatibility grid supports addition of the buttons to the menu, but there are several
Semantic UI limitations that wouldn't allow to format buttons nicely::

    $grid->addButton('Hello');

If you don't need menu, you can disable menu bar entirely::

    $grid = $layout->add(['Grid', 'menu' => false]);

Adding Quick Search
===================

.. php:method: addQuickSearch($fields = [])

.. php:attr: $quickSearch

After you have associated grid with a model using :php:class:`View::setModel()` you can
include quick-search component::

    $grid->addQuickSearch(['name', 'surname']);

If you don't specify argument, then search will be done by a title field.
(http://agile-data.readthedocs.io/en/develop/model.html#title-field)

Advanced Usage
==============

.. php:attr: $table

You can use a different component instead of default 'Table' 
