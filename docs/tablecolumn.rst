
.. _tablecolumn:

Table Columns and Formatters
============================

.. php:namespace:: atk4\ui\TableColumn

.. php:class:: Generic

    Generic description of a column for :php:class:`atk4\\ui\\Table`

Table object relies on a separate class: \atk4\ui\TableColumn\Generic to present most of the values. The goals
of the column object is to format anything around the actual values. The type = 'money' will result in
a custom formatting of the value, but will also require column to be right-aligned. To simplify this,
type = 'money' will use a different column class - :php:class:`TableColumn\Money`. There are several others,
but first we need to look at the generic column and understand it's base capabilities:

A class resposnible for cell formatting. This class defines 3 main methods that is used by the Table
when constructing HTML:

.. php:method:: getHeaderCellHTML(\atk4\data\Field $f)

Must respond with HTML for the header cell (`<th>`) and an appropriate caption. If necessary
will include "sorting" icons or any other controls that go in the header of the table.

.. php:method:: getTotalsCellHTML(\atk4\data\Field $f, $value)

Provided with the field and the value, format the cell for the footer "totals" row. Table
can rely on various strategies for calculating totals. See :php:meth:`Table::addTotals`.

.. php:method:: getDataCellHTML(\atk4\data\Field f)

Provided with a field, this method will respond with HTML **template**. In order to keep
performance of Web Application at the maximum, Table will execute getDataCellHTML for all the
fields once. When iterating, a combined template will be used to display the values.

The template must not incorporate field values (simply because related model will not be
loaded just yet), but instead should resort to tags and syntax compatible with :php:class:`Template`.

A sample template could be::

    <td><b>{$name}</b></td>

Note that the "name" here must correspond with the field name inside the Model. You may use
multiple field names to format the column::

    <td><b>{$year}-{$month}-{$day}</b></td>

The above 3 methods define first argument as a field, however it's possible to define column
without a physical field. This makes sense for situations when column contains multiple field
values or if it doesn't contain any values at all.

Sometimes you do want to inject HTML instead of using row values:

.. php:method:: getHTMLTags($model, $field = null)

Return array of HTML tags that will be injected into the row template. See
:php:ref:`table_html` for further example.
