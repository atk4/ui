
.. _grid:

====
Grid
====

.. php:namespace:: atk4\ui

Grid is the simplest way to output multiple records of structured data. Grid only works along with the model,
however you can use :php:meth:`Lister::setSource` to inject static data (although it is slower than simply
using a model).


Using Grid
==========

The simplest way to create a grid is to 


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

