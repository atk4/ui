
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

To change the order or explicitly specify which columns must appear, you can pass list of columns as a second
argument to setModel::

    $grid = $layout->add('Grid');
    $grid->setModel(new Order($db), ['name', 'price', 'amount', 'status']);

Grid will make use of "Only Fields" feature in Agile Data to adjust query for fetching only the necessary
columns. See also :ref:`field_visibility`.

Adding Additional Columns
-------------------------

If you feel that you'd like to add several other columns to your grid, you need to understand what type
of columns they would be. 

If your column is designed to carry a value of any type, then it's much better to define it as a Model
Field. A good example of this scenario is adding "total" column to list of your invoice lines that
already contain "price" and "amount" values. Start by adding new Field in the model that is associated
with your grid::

    $grid = $layout->add('Grid');
    $order = new Order($db);

    $order->addExpression('total', '[price]*[amount]')->type = 'money';

    $grid->setModel($order, ['name', 'price', 'amount', 'total', 'status']);

The type of the Model Field determines the way how value is presented in the grid. I've specified
value to be 'money' which makes column align values to the right, format it with 2 decimal signs
and possibly add a currency sign.

To learn about value formatting, read documentation on :ref:`ui_persistence`.

Grid object does not contain any information about your fields (such as captions) but instead it will
consult your Model for the necessary field information. If you are willing to define the type but also
specify the caption, you can use code like this::

    $grid = $layout->add('Grid');
    $order = new Order($db);

    $order->addExpression('total', [
        '[price]*[amount]',
        'type'=>'money',
        'caption'=>'Total Price'
    ]);

    $grid->setModel($order, ['name', 'price', 'amount', 'total', 'status']);

Column Objects
--------------

Grid object relies on a separate class: \atk4\ui\Column\Generic to present most of the values. The goals
of the column object is to format anything around the actual values. The type = 'money' will result in
a custom formatting of the value, but will also require column to be right-aligned. To simplify this,
type = 'money' will use a different column class - :php:class:`Column\Money`. There are several others,
but first we need to look at the generic column and understand it's base capabilities:

.. php:class:: Column\Generic

A class resposnible for cell formatting. This class defines 3 main methods that is used by the Grid
when constructing HTML:

.. php:method:: getHeaderCell(\atk4\data\Field $f)

Must respond with HTML for the header cell (`<th>`) and an appropriate caption. If necessary
will include "sorting" icons or any other controls that go in the header of the table.

The output of this field will automatically encode any values (such as caption), shorten them
if necessary and localize them.

.. php:method:: getTotalsCell(\atk4\data\Field $f, $value)

Provided with the field and the value, format the cell for the footer "totals" column. Grid
can rely on various strategies for calculating totals. See :php:meth:`Grid::addTotals`.

.. php:method:: getCellTemplate(\atk4\data\Field f)

Provided with a field, this method will respond with HTML **template**. In order to keep
performance of Web Application at the maximum, Grid will execute getCellTemplate for all the
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
:php:ref:`grid_html` for further example.

Advanced Column Denifitions
---------------------------

.. php:class:: Grid

Grid defines a method `columnFactory`, which returns Column object which is to be used to
display values of specific model Field. 

.. php:method:: columnFactory(\atk4\data\Field $f)

If the value of the field can be displayed by :php:class:`Column\Generic` then Grid will
respord with object of this class. Since the default column does not contain any customization,
then to save memory Grid will re-use the same objects for all generic fields.

.. php:attr:: default_column

Protected property that will contain "generic" column that will be used to format all
columns, unless a different column type is specified or the Field type will require a use
of a different class (e.g. 'money'). Value will be initialized after first call to
:php:meth:`Grid::addColumn`

.. php:attr:: columns

    Contains array of defined columns.

.. php:method:: addColumn([$name], Column\Generic $column = null, \atk4\ui\Data\Field = null)

Adds a new column to the grid. This method has several few usages, here is the most basic one::

    $grid->setModel(new Order($db), ['name', 'price', 'total']);
    $grid->addColumn(new \atk4\ui\Column\Delete());

The above code will add a new extra column that will only contain 'delete' icon. When clicked
it will automatically delete the record.

You have probably noticed, that I have omitted the name for this column. If name is not specified
(null) then the Column object will receive "null" when the call to
:php:meth:`Column\Generic::getHeaderCell`, :php:meth:`Column\Generic::getTotalsCell` and 
:php:meth:`Column\Generic::getCellTemplate` will be made. The :php:class:`Column\Generic` will
not be able to cope with this situations, but many other column types are perfectly fine with this.

Some column classes will be able to take some information from a specified column, but will work
just fine if column is not passed.

If you do specify a string as a $name for addColumn, but no such field exist in the model, the
method will rely on 3rd argument to create a new field for you. Here is example that calculates
the "total" column value (as above) but using PHP math instead of doing it inside database::


    $grid = $layout->add('Grid');
    $order = new Order($db);

    $grid->setModel($order, ['name', 'price', 'amount', 'status']);
    $grid->addColumn('total', new \atk4\data\Field\Calculated(
        function($row) {
            return $row['price'] * $row['amount'];
        }));

If you execute this code, you'll notice that the "total" column is now displayed last. If you
wish to position it before status, you can use the final format of addColumn()::

    $grid = $layout->add('Grid');
    $order = new Order($db);

    $grid->setModel($order, ['name', 'price', 'amount']);
    $grid->addColumn('total', new \atk4\data\Field\Calculated(
        function($row) {
            return $row['price'] * $row['amount'];
        }));
    $grid->addColumn('status');

This way we don't populate the column through setModel() and instead populate it manually later
through addColumn(). This will use an identical logic (see :php:meth:`Grid::columnFactory`). For
your convenience there is a way to add multiple columns efficiently.

.. php:method:: addColumns($names);

    Here, names can be an array of strings (['status', 'price']) or contain array that will be passed
    as argument sto the addColumn method ([['total', $field_def], ['delete', $delete_column]);

As a final note in this section - you can re-use column objects multiple times::

    $c_gap = new \atk4\ui\Column\Template('<td> ... <td>');
    
    $grid->addColumn($c_gap);
    $grid->setModel(new Order($db), ['name', 'price', 'amount']);
    $grid->addColumn($c_gap);
    $grid->addColumns(['total','status'])
    $grid->addColumn($c_gap);

This will result in 3 gap columns rendered to the left, middle and right of your Grid.

.. _grid_html:

Injecting HTML
--------------

The tag will override model value. Here is example usage of :php:meth:`Column\Generic::getHTMLTags`::


    class ExpiredColumn extends \atk4\ui\Column\Generic
        public function getCellTemplate()
        {
            return '{$_expired}';
        }

        function getHTMLTags($model)
        {
            return ['_expired'=>
                $model['date'] < new \DateTime() ?
                '<td class="danger">EXPIRED</td>' :
                '<td></td>'
            ];
        }
    }

Your column now can be added to any grid::

    $grid->addColumn(new ExpiredColumn());

IMPORTANT: HTML injection will work unless :php:attr:`Grid::use_html_tags` property is disabled (for performance).

Grid Data Handling
==================

Grid is very similar to :php:class:`Lister` in the way how it loads and displays data. To control which
data Grid will be displaying you need to properly specify the model and persistence. The following two
examples will show you how to display list of "files" inside your Dropbox folder and how to display list
of issues from your Github repository::

    // Show contents of dropbox
    $dropbox = \atk4\dropbox\Persistence($db_config);
    $files = new \atk4\dropbox\Model\File($dropbox);

    $layout->add('Grid')->setModel($files);


    // Show contents of dropbox
    $github = \atk4\github\Persistence_Issues($github_api_config);
    $issues = new \atk4\github\Model\Issue($github);

    $layout->add('Grid')->setModel($issues);

This example demonstrates that by selecting a 3rd party persistence implementation, you can access
virtually any API, Database or SQL resource and it will always take care of formatting for you as well
as handle field types.

I must also note that by simply adding 'Delete' column (as in example above) will allow your app users
to delete files from dropbox or issues from GitHub. 

Grid follows a "universal data design" principles established by Agile UI to make it compatible with
all the different data persitences. (see :php:ref:`universal_data_access`)

For most applications, however, you would be probably using internally defined models that rely on
data stored inside your own database. Either way, several principles apply to the way how Grid works.

Grid Rendering Steps
--------------------

Once model is specified to the Grid it will keep the object until render process will begin. Grid
columns can be defined anytime and will be stored in the :php:attr:`Grid::columns` property. Columns
without defined name will have a numeric index.

During the render process (see :php:meth:`View::renderView`) Grid will perform the following actions:

1. Generate header row.
2. Generate template for data rows.
3. Iterate through rows
    3.1 Current row data is accessible through $grid->model property.
    3.2 Update Totals if :php:meth:`Grid::addTotals` was used.
    3.3 Insert row values into :php:attr:`Grid::t_row`
        3.3.1 Template relies on :ref:`ui_persistence` for formatting values
    3.4 Collect HTML tags from 'getHTMLTags' hook.
    3.5 Collect getHTMLTags() from columns objects
    3.6 Inject HTML into :php:attr:`Grid::t_row` template
    3.7 Render and append row template to Table Body ({$Body})
    3.8 Clear HTML tag values from template.
4. If no rows were displayed, then "empty message" will be shown (see :php:attr:`Grid::t_empty`).
5. If :php:meth:`addTotals` was used, append totals row to table footer.


Advanced Usage
==============

Grid is a very flexible object and can be extended through various means. This chapter will focus
on various requirements and will provide a way how to achieve that.

Toolbar, Quick-search and Paginator
-----------------------------------

It's quite common to have "Toolbar" above the grid and pagination below. The toolbar often hosts
a Quicksearch form too. Default Grid implementation does not have any of these features, however
you can use a separate 'Advanced Grid' add-on, which extends standard Grid functionality::


    $grid = $layout->add(new \atk4\ui\AdvancedGrid());

    // Buttons appear above the grid. Clicking them will dynamically load more views inside dialog.
    $grid->addButton('Download', function($page){ 
        $page->add('Info')->set('This UI will appear in a dialog');
    });

    // Paginator allow to go back and fourth inside Grid, if you have a lot of data.
    $grid->addPaginator(20);

    // Quick-search allow your user to quickly search for results.
    $grid->addQuickSearch(['name', 'surname']);

    // Expander allow user to "open up" individual records and reveal additional UI elements
    $grid->addExpander(function($page, $id){ 
        $page->add('Info')->set('This UI will appear in-line in your grid');
    });

    // Standartise use of 'Actions' through Column\Action
    $grid->addAction('Delete');

The implementation for Advanced Grid is scheduled to be added in Agile UI 1.1, check with
http://github.com/atk4/ui on the progress.

Column attributes and classes
=============================
By default Grid will include ID for each row: `<tr data-id="123">`. The following code example
demonstrates how various standard column types are relying on this property::

    $grid->on('click', 'td', new jsExpression(
        'document.location=page.php?id=[]', 
        [(new jQuery())->closest('tr')->data('id')]
    ));

See also :ref:`js`.

Static Attributes and classes
-----------------------------

.. php:class:: Column\Generic

.. php:method:: addClass($class, $scope = 'body');

.. php:method:: setAttr($attribute, $value, $scope = 'body');


The following code will make sure that contens of the column appear on a single line by
adding class "single line" to all body cells::

    $grid->addColumn('name', (new \atk4\ui\Column()->addClass('single line')));

If you wish to add a class to 'head' or 'foot' or 'all' cells, you can pass 2nd argument to addClass::

    $grid->addColumn('name', (new \atk4\ui\Column()->addClass('right aligned', 'all')));

There are several ways to make your code more readable::

    $grid->addColumn('name', new \atk4\ui\Column\Generic())
        ->addClass('right aligned', 'all');

Or if you wish to use factory, the syntax is::

    $grid->addColumn('name', 'Generic')
        ->addClass('right aligned', 'all');

For setting an attribute you can use setAttr() method::

    $grid->addColumn('name', 'Generic')
        ->setAttr('colspan', 2, 'all');

Setting a new value to the attribute will override previous value.

Please note that if you are redefining :php:meth:`Column\Generic::getHeaderCell`, 
:php:meth:`Column\Generic::getTotalsCell` or :php:meth:`Column\Generic::getCellTemplate`
and you wish to preserve functionality of setting custom attributes and
classes, you should generate your TD/TH tag through getTag method.

.. php:method:: getTag($tag, $position, $value);

    Will apply cell-based attributes or classes then use :php:meth:`App::getTag` to
    generate HTML tag and encode it's content.


Using dynamic values
--------------------

Body attributes will be embedded into the template by the default :php:meth:`Column\Generic::getCellTemplate`,
but if you specify attribute (or class) value as a tag, then it will be auto-filled
with row value or injected HTML.

For further examples of and advanced usage, see implementation of :php:class:`Column\Status`.



Standard Column Types
=====================

In addition to :php:class:`Column\Generic`, Agile UI includes several column implementations.

Link
----

.. php:class:: Column\Link

Put `<a href..` link over the value of the cell. The page property can be specified to constructor. There
are two usage patterns. With the first you can specify full URL as a string::

    $grid->addColumn('name', new \atk4\ui\Column\Link('http://google.com/?q={$name}'));

The name value will be automatically inserted. The other option is to use page array::

    $grid->addColumn('name', new \atk4\ui\Column\Link(['details', 'id'=>'{$id}', 'status'=>'{$status}']));

Money
-----

.. php:class:: Column\Money

Helps formatting monetary values. Will align value to the right and if value is less than zero will also
use red text. The money cells are not wrapped.

For the actual number formatting, see :ref:`ui_persistence`

Status
------

.. php:class:: Column\Status

Allow you to set highlight class and icon based on column value. This is most suitable for columns that
contain pre-defined values. 

If your column "status" can be one of the following "pending", "declined", "archived" and "paid" and you would like
to use different icons and colors to emphasise status::


    $states = [ 'positive'=>['paid', 'archived'], 'negative'=>['declined'] ];

    $grid->addColumn('status', new \atk4\ui\Column\Status($states));

Current list of states supported:

 - positive (icon checkmark)
 - negative (icon close)
 - and the default/unspecified state (icon question)

(list of states may be expanded furteher)

Template
--------

.. php:class:: Column\Template

This column is suitable if you wish to have custom cell formatting but do not wish to go through
the trouble of setting up your own class.

If you wish to display movie rating "4 out of 10" based around the column "rating", you can use::

    $grid->addColumn('rating', new \atk4\ui\Column\Template('{$rating} out of 10'));

Template may incorporate values from multiple fields in a data row, but current implementation
will only work if you asign it to a primary column (by passing 1st argument to addColumn).

(In the future it may be optional with the ability to specify caption).


Action Column
=============

.. note:: Action column is planned for Agile UI 1.1.


.. php:class:: Column\Action

This column allows you to incorporate any of the standard :ref:`actions` into your column.
The functionality and diveristy of actions is seamlessly integrated into the column and
the actions are performed on the row level.

The basic usage format is::

    $act = $grid->addColumn(new \atk4\ui\Column\Action())

    // Pencil icon linking to a URL
    $act->addAction(new \atk4\ui\Action\Link(
        'http://google.com/?q={$text}'
    ));

    // Delete, that will delete current row
    $act->addAction(new \atk4\ui\Action\Delete('trash'));

    // Method, executes user-defined method for the model
    $act->addAction(new \atk4\ui\Action\Method('archive'));

    // Callback executes JavaScript
    $act->addAction(new \atk4\ui\Action\Callback(
        function($model) {
            return new jsExpression('alert([])', 'Clicked on id='.$model->id);
        }
    ));

    // Dialog opens a modal dialog with content
    $act->addAction(new \atk4\ui\Action\Dialog(
        function($page, $model) {
            $page->add('Info')->set('Dialog for record with id='.$model->id);
        }
    ));

For more information about Actions, see :ref:`actions`. (Scheduled to be implemented in Agile UI 1.1)

