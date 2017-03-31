
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

Addin Menu Items
================

Grid top-bar which contains QuickSearch is implemented using Semantic UI "ui menu". With that
you can add additional items and use all feautres of a regular :php:class:`Menu`::

    $m = $grid->menu->addMenu('Drop-down');
    $m->addItem('Test123');
