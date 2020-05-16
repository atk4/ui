
.. _table:

=====
Table
=====

.. php:namespace:: atk4\ui

.. php:class:: Table

.. important:: For columns, see :php:class:`TableColumn\\Generic`. For DIV-based lists, see :php:class:`Lister`. For an
    interractive features see :php:class:`Grid` and :php:class:`CRUD`.

Table is the simplest way to output multiple records of structured, static data. For Un-structure output
please see :php:class:`Lister`

    .. image:: images/table.png

Various composite components use Table as a building block, see :php:class:`Grid` and :php:class:`CRUD`.
Main features of Table class are:

 - Tabular rendering using column headers on top of markup of https://fomantic-ui.com/collections/table.html.
 - Support for data decorating. (money, dates, etc)
 - Column decorators, icons, buttons, links and color.
 - Support for "Totals" row.
 - Can use Agile Data source or Static data.
 - Custom HTML, Format hooks

Basic Usage
===========

The simplest way to create a table is when you use it with Agile Data model::

    $table = Table::addTo($app);
    $table->setModel(new Order($db));

The table will be able to automatically determine all the fields defined in your "Order" model, map them to
appropriate column types, implement type-casting and also connect your model with the appropriate data source
(database) $db.

Using with Array Data
---------------------

You can also use Table with Array data source like this::

    $my_array = [
        ['name'=>'Vinny', 'surname'=>'Sihra', 'birthdate'=>new \DateTime('1973-02-03')],
        ['name'=>'Zoe', 'surname'=>'Shatwell', 'birthdate'=>new \DateTime('1958-08-21')],
        ['name'=>'Darcy', 'surname'=>'Wild', 'birthdate'=>new \DateTime('1968-11-01')],
        ['name'=>'Brett', 'surname'=>'Bird', 'birthdate'=>new \DateTime('1988-12-20')],
    ];

    $table = Table::addTo($app);
    $table->setSource($my_array);

    $table->addColumn('name');
    $table->addColumn('surname', ['Link', 'url'=>'details.php?surname={$surname}']);
    $table->addColumn('birthdate', null, ['type'=>'date']);

.. warning:: I encourage you to seek appropriate Agile Data persistence instead of
    handling data like this. The implementation of :php:meth:`View::setSource` will
    create a model for you with Array persistence for you anyways.

Adding Columns
--------------

.. php:method:: setModel(\atk4\data\Model $m, $fields = null)

.. php:method:: addColumn($name, $columnDecorator = null, $field = null)

To change the order or explicitly specify which field columns must appear, if you pass list of those
fields as second argument to setModel::

    $table = Table::addTo($app);
    $table->setModel(new Order($db), ['name', 'price', 'amount', 'status']);

Table will make use of "Only Fields" feature in Agile Data to adjust query for fetching only the necessary
columns. See also :ref:`field_visibility`.


You can also add individual column to your table::

    $table->setModel(new Order($db), false); // false here means - don't add any fields by default
    $table->addColumn('name');
    $table->addColumn('price');

When invoking addColumn, you have a great control over the field properties and decoration. The format
of addColumn() is very similar to :php:meth:`Form::addField`.

Calculations
============

Apart from adding columns that reflect currrent values of your database, there are several ways
how you can calculate additional values. You must know the capabilities of your database server
if you want to execute some calculation there. (See https://agile-data.readthedocs.io/en/develop/expressions.html)

It's always a good idea to calculate column inside datababase. Lets create "total" column  which will
multiply "price" and "amount" values. Use ``addExpression`` to provide in-line definition for this
field if it's not alrady defined in ``Order::init()``::

    $table = Table::addTo($app);
    $order = new Order($db);

    $order->addExpression('total', '[price]*[amount]')->type = 'money';

    $table->setModel($order, ['name', 'price', 'amount', 'total', 'status']);

The type of the Model Field determines the way how value is presented in the table. I've specified
value to be 'money' which makes column align values to the right, format it with 2 decimal signs
and possibly add a currency sign.

To learn about value formatting, read documentation on :ref:`ui_persistence`.

Table object does not contain any information about your fields (such as captions) but instead it will
consult your Model for the necessary field information. If you are willing to define the type but also
specify the caption, you can use code like this::

    $table = Table::addTo($app);
    $order = new Order($db);

    $order->addExpression('total', [
        '[price]*[amount]',
        'type'=>'money',
        'caption'=>'Total Price'
    ]);

    $table->setModel($order, ['name', 'price', 'amount', 'total', 'status']);

Column Objects
--------------

To read more about column objects, see :ref:`tablecolumn`

Advanced Column Denifitions
---------------------------

Table defines a method `columnFactory`, which returns Column object which is to be used to
display values of specific model Field.

.. php:method:: columnFactory(\atk4\data\Field $f)

If the value of the field can be displayed by :php:class:`TableColumn\\Generic` then :php:class:`Table` will
respord with object of this class. Since the default column does not contain any customization,
then to save memory Table will re-use the same objects for all generic fields.

.. php:attr:: default_column

Protected property that will contain "generic" column that will be used to format all
columns, unless a different column type is specified or the Field type will require a use
of a different class (e.g. 'money'). Value will be initialized after first call to
:php:meth:`Table::addColumn`

.. php:attr:: columns

    Contains array of defined columns.

`addColumn` adds a new column to the table. This method was explained above but can also be
used to add columns without field::

    $action = $this->addColumn(null, ['Actions']);
    $actions->addAction('Delete', function() { return 'ok'; });

The above code will add a new extra column that will only contain 'delete' icon. When clicked
it will automatically delete the corresponding record.

You have probably noticed, that I have omitted the name for this column. If name is not specified
(null) then the Column object will not be associated with any model field in
:php:meth:`TableColumn\\Generic::getHeaderCellHTML`, :php:meth:`TableColumn\\Generic::getTotalsCellHTML` and
:php:meth:`TableColumn\\Generic::getDataCellHTML`.

Some columns require name, such as :php:class:`TableColumn\\Generic` will
not be able to cope with this situations, but many other column types are perfectly fine with this.

Some column classes will be able to take some information from a specified column, but will work
just fine if column is not passed.

If you do specify a string as a $name for addColumn, but no such field exist in the model, the
method will rely on 3rd argument to create a new field for you. Here is example that calculates
the "total" column value (as above) but using PHP math instead of doing it inside database::

    $table = Table::addTo($app);
    $order = new Order($db);

    $table->setModel($order, ['name', 'price', 'amount', 'status']);
    $table->addColumn('total', new \atk4\data\Field\Calculated(
        function($row) {
            return $row['price'] * $row['amount'];
        }));

If you execute this code, you'll notice that the "total" column is now displayed last. If you
wish to position it before status, you can use the final format of addColumn()::

    $table = Table::addTo($app);
    $order = new Order($db);

    $table->setModel($order, ['name', 'price', 'amount']);
    $table->addColumn('total', new \atk4\data\Field\Calculated(
        function($row) {
            return $row['price'] * $row['amount'];
        }));
    $table->addColumn('status');

This way we don't populate the column through setModel() and instead populate it manually later
through addColumn(). This will use an identical logic (see :php:meth:`Table::columnFactory`). For
your convenience there is a way to add multiple columns efficiently.

.. php:method:: addColumns($names);

    Here, names can be an array of strings (['status', 'price']) or contain array that will be passed
    as argument sto the addColumn method ([['total', $field_def], ['delete', $delete_column]);

As a final note in this section - you can re-use column objects multiple times::

    $c_gap = new \atk4\ui\TableColumn\Template('<td> ... <td>');

    $table->addColumn($c_gap);
    $table->setModel(new Order($db), ['name', 'price', 'amount']);
    $table->addColumn($c_gap);
    $table->addColumns(['total','status'])
    $table->addColumn($c_gap);

This will result in 3 gap columns rendered to the left, middle and right of your Table.

Table sorting
=============

.. php:attr:: sortable
.. php:attr:: sort_by
.. php:attr:: sort_order

Table does not support an interractive sorting on it's own, (but :php:class:`Grid` does), however
you can designade columns to display headers as if table were sorted::

    $table->sortable = true;
    $table->sort_by = 'name';
    $table->sort_order = 'ascending';

This will highlight the column "name" header and will also display a sorting indicator as per sort
order.

JavaScript Sorting
------------------

You can make your table sortable through JavaScript inside your browser. This won't work well if
your data is paginated, because only the current page will be sorted::

    $table->app->includeJS('https://fomantic-ui.com/javascript/library/tablesort.js');
    $table->js(true)->tablesort();

For more information see https://github.com/kylefox/jquery-tablesort



.. _table_html:

Injecting HTML
--------------

The tag will override model value. Here is example usage of :php:meth:`TableColumn\\Generic::getHTMLTags`::


    class ExpiredColumn extends \atk4\ui\TableColumn\Generic
        public function getDataCellHTML()
        {
            return '{$_expired}';
        }

        function getHTMLTags(\atk4\data\Model $row)
        {
            return ['_expired'=>
                $row->get('date') < new \DateTime() ?
                '<td class="danger">EXPIRED</td>' :
                '<td></td>'
            ];
        }
    }

Your column now can be added to any table::

    $table->addColumn(new ExpiredColumn());

IMPORTANT: HTML injection will work unless :php:attr:`Table::use_html_tags` property is disabled (for performance).

Table Data Handling
===================

Table is very similar to :php:class:`Lister` in the way how it loads and displays data. To control which
data Table will be displaying you need to properly specify the model and persistence. The following two
examples will show you how to display list of "files" inside your Dropbox folder and how to display list
of issues from your Github repository::

    // Show contents of dropbox
    $dropbox = \atk4\dropbox\Persistence($db_config);
    $files = new \atk4\dropbox\Model\File($dropbox);

    Table::addTo($app)->setModel($files);


    // Show contents of dropbox
    $github = \atk4\github\Persistence_Issues($github_api_config);
    $issues = new \atk4\github\Model\Issue($github);

    Table::addTo($app)->setModel($issues);

This example demonstrates that by selecting a 3rd party persistence implementation, you can access
virtually any API, Database or SQL resource and it will always take care of formatting for you as well
as handle field types.

I must also note that by simply adding 'Delete' column (as in example above) will allow your app users
to delete files from dropbox or issues from GitHub.

Table follows a "universal data design" principles established by Agile UI to make it compatible with
all the different data persitences. (see :php:ref:`universal_data_access`)

For most applications, however, you would be probably using internally defined models that rely on
data stored inside your own database. Either way, several principles apply to the way how Table works.

Table Rendering Steps
--------------------

Once model is specified to the Table it will keep the object until render process will begin. Table
columns can be defined anytime and will be stored in the :php:attr:`Table::columns` property. Columns
without defined name will have a numeric index. It's also possible to define multiple columns per key
in which case we call them "decorators".

During the render process (see :php:meth:`View::renderView`) Table will perform the following actions:

1. Generate header row.
2. Generate template for data rows.
3. Iterate through rows
    3.1 Current row data is accessible through $table->model property.
    3.2 Update Totals if :php:meth:`Table::addTotals` was used.
    3.3 Insert row values into :php:attr:`Table::t_row`
        3.3.1 Template relies on :ref:`ui_persistence` for formatting values
    3.4 Collect HTML tags from 'getHTMLTags' hook.
    3.5 Collect getHTMLTags() from columns objects
    3.6 Inject HTML into :php:attr:`Table::t_row` template
    3.7 Render and append row template to Table Body ({$Body})
    3.8 Clear HTML tag values from template.
4. If no rows were displayed, then "empty message" will be shown (see :php:attr:`Table::t_empty`).
5. If :php:meth:`addTotals` was used, append totals row to table footer.

Dealing with Multiple decorators
================================

.. php:method:: addDecorator($name, $columnDecorator)

.. php:method:: getColumnDecorators($name)

Decorator is an object, responsible for wrapping column data into a table cell (td/tr). This object
is also responsible for setting class of the column, labeling the column and somehow making it look
nicer especially inside a table.

.. important:: Decorating is not formatting. If we talk "date", then in order to display it to
    the user, date must be in a proper format. Formatting of data is done by Persistence_UI and
    is not limited to the table columns. Decorators may add an icon, change cell style, align cell
    or hide overflowing text to make table output look better.

One column may have several decorators::

    $table->addColumn('salary', new \atk4\ui\TableColumn\Money());
    $table->addDecorator('salary', new \atk4\ui\TableColumn\Link(['page2']));

In this case the first decorator will take care of tr/td tags but second decorator will compliment
it. Result is that table will output 'salary' as a currency (align and red ink) and also decorate
it with a link. The first decorator will be responsible for the table column header. If field type
is not set or type is like "integer", then a generic formatter is used.

There are a few things to note:

1. Property :php:attr:`Table::columns` contains either a single or multiple decorators for each
   column. Some tasks will be done by first decorator only, such as getting TH/header cell. Others will
   be done by all decorators, such as collecting classes / styles for the cell or wrapping formatted
   content (link, icon, template).

2. formatting is always applied in same order as defined - in example above Money first, Link after.

3. output of the 'Money' decorator is used into Link decorator as if it would be value of cell, however
   decorators have access to original value also. Decorator implementation is usually aware of combinations.

:php:meth:`TableColumn\\Money::getDataCellTemplate` is called, which returns ONLY the HTML value,
without the <td> cell itself. Subsequently :php:meth:`TableColumn\\Link::getDataCellTemplate` is called
and the '{$salary}' tag from this link is replaced by output from Money column resulting in this
template::

    <a href="{$c_name_link}">£ {$salary}</a>

To calculate which tag should be used, a different approach is done. Attributes for <td> tag
from Money are collected then merged with attributes of a Link class. The money column wishes
to add class "right aligned single line" to the <td> tag but sometimes it may also use
class "negative". The way how it's done is by defining `class="{$f_name_money}"` as one
of the TD properties.

The link does add any TD properties so the resulting "td" tag would be::

    ['class' => ['{$f_name_money}'] ]

    // would produce <td class="{$f_name_money}"> .. </td>

Combined with the field template generated above it provides us with a full cell
template::

    <td class="{$f_name_money}"><a href="{$c_name_link}">£ {$salary}</a></td>

Which is concatinated with other table columns just before rendering starts. The
actual template is formed by calling. This may be too much detail, so if you need
to make a note on how template caching works then,

 - values are encapsulated for named fields.
 - values are concatinated by anonymous fields.
 - <td> properties are stacked
 - last decorator will convert array with td properties into an actual tag.

Header and Footer
-----------------
When using with multiple decorators, the last decorator gets to render Header cell.
The footer (totals) uses the same approach for generating template, however a
different methods are called from the columns: getTotalsCellTemplate

Redefining
----------

If you are defining your own column, you may want to re-define getDataCellTemplate. The
getDataCellHTML can be left as-is and will be handled correctly. If you have overriden
getDataCellHTML only, then your column will still work OK provided that it's used as a
last decorator.

Advanced Usage
==============

Table is a very flexible object and can be extended through various means. This chapter will focus
on various requirements and will provide a way how to achieve that.

Toolbar, Quick-search and Paginator
-----------------------------------

See :php:class:`Grid`

jsPaginator
-----------

.. php:method:: addJsPaginator($ipp, $options = [], $container = null, $scrollRegion = 'Body')

jsPaginator will load table content dynamically when user scroll down the table window on screen.

    $table->addJsPaginator(30);

See also :php:meth:`Lister::addJsPaginator`

Resizable Columns
-----------------

.. php:method:: resizableColumn($fx = null, $widths = null, $resizerOptions = null)

Each table's column width can be resize by dragging the column right border::

    $table->resizableColumn();

You may specify a callback function to the method. The callback will return a json string containing each
column name in table with their new width in pixel.::

    $table->resizableColumn(function($j, $w){
        // do something with new column width
        $columnWidths = json_decode($w);
        return;
    }, [200,300,100,100,100]);

Note that you may specify an array of integer representing the initial width value in pixel for each column in your table.

Finally you may also specify some of the resizer options use by column-resizer. Column-resizer is the npm package js library use for implementing
the atkColumnResizer jQuery plugin.

Column attributes and classes
=============================
By default Table will include ID for each row: `<tr data-id="123">`. The following code example
demonstrates how various standard column types are relying on this property::

    $table->on('click', 'td', new jsExpression(
        'document.location=page.php?id=[]',
        [(new jQuery())->closest('tr')->data('id')]
    ));

See also :ref:`js`.

Static Attributes and classes
-----------------------------

.. php:class:: TableColumn\\Generic

.. php:method:: addClass($class, $scope = 'body');

.. php:method:: setAttr($attribute, $value, $scope = 'body');


The following code will make sure that contens of the column appear on a single line by
adding class "single line" to all body cells::

    $table->addColumn('name', (new \atk4\ui\TableColumn\Generic()->addClass('single line')));

If you wish to add a class to 'head' or 'foot' or 'all' cells, you can pass 2nd argument to addClass::

    $table->addColumn('name', (new \atk4\ui\TableColumn\Generic()->addClass('right aligned', 'all')));

There are several ways to make your code more readable::

    $table->addColumn('name', new \atk4\ui\TableColumn\Generic())
        ->addClass('right aligned', 'all');

Or if you wish to use factory, the syntax is::

    $table->addColumn('name', 'Generic')
        ->addClass('right aligned', 'all');

For setting an attribute you can use setAttr() method::

    $table->addColumn('name', 'Generic')
        ->setAttr('colspan', 2, 'all');

Setting a new value to the attribute will override previous value.

Please note that if you are redefining :php:meth:`TableColumn\\Generic::getHeaderCellHTML`,
:php:meth:`TableColumn\\Generic::getTotalsCellHTML` or :php:meth:`TableColumn\\Generic::getDataCellHTML`
and you wish to preserve functionality of setting custom attributes and
classes, you should generate your TD/TH tag through getTag method.

.. php:method:: getTag($tag, $position, $value);

    Will apply cell-based attributes or classes then use :php:meth:`App::getTag` to
    generate HTML tag and encode it's content.

Columns without fields
----------------------

You can add column to a table that does not link with field::

    $cb = $table->addColumn('CheckBox');


Using dynamic values
--------------------

Body attributes will be embedded into the template by the default :php:meth:`TableColumn\\Generic::getDataCellHTML`,
but if you specify attribute (or class) value as a tag, then it will be auto-filled
with row value or injected HTML.

For further examples of and advanced usage, see implementation of :php:class:`TableColumn\\Status`.


