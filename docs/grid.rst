
.. _grid:

====
Grid
====

.. php:namespace:: atk4\ui

Grid is the simplest way to output multiple records of structured data. Grid only works along with the model,
however you can use :php:meth:`Lister::setSource` to inject static data (although it is slower than simply
using a model). :ref:`no_data`


Using Grid
==========

The simplest way to create a grid::

    $grid = $layout->add('Grid');
    $grid->setModel(new Order($db));

The grid will be able to automatcally determine all the fields defined in your "Order" model, map them to
appropriate column types, implement type-casting and also connect your model with the appropriate data source
(database) $db.

Adding Additional Columns
-------------------------

There are various ways to define a new column. The most common approach is to add a new Model Field that 
would map into the new column. For instance if you are showing orders with quantity and price and are willing
to add a "Total" column::

    $grid = $layout->add('Grid');
    $order = new Order($db);

    $order->addExpression('total', '[price]*[amount]')->type = 'money';

    $grid->setModel($order);

Grid Data Handling
==================

There are three areas to look into if you are willing to learn about Grid:

1. Look at Lister and learn how it works, because Grid extends Lister and is similar to it in many ways.
2. Look into :php:class:`Column\Generic` that takes care of column formatting.
3. See how 

:php:class:`Lister` is explained in it's own chapter.

Column Formatting
-----------------

.. php:class:: Column\Generic

Column\Generic class is designed to put together a "row template" for the Grid. The column knows how
to format a single cell and may inject any HTML inbetween ``<td>..</td>``. The most basic column will
simply put a tag inside: ``<td>{$name}</td>`` but some columns may do different things to it.

Columns do not handle the formatting, because it's the job of UI Persistence, they just need to arrange
the template chunks. Column should also arrange the header cell.

If column wishes to participate in row formatting, then it can register ``Grid.formatRow`` hook callback
and use it to populate specialized tags.

Formatting of Model Data
------------------------

Application holds an instance of the UI persistence, which is a class responsible for converting
domain model data for presentation and back. When this persistence is populated, it works in
conjunction with a template, so simply popping any model values into UI persistence will actually
populate template tags.

To avoid one column poisoning another, all the tags of the row will be deleted. This functionality
is same as the Lister's. 

