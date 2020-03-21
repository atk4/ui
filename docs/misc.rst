

.. php:namespace:: atk4\ui


Columns
=======

This class implements CSS Grid or ability to divide your elements into columns. If you are an expert
designer with knowledge of HTML/CSS we recommend you to create your own layouts and templates, but
if you are not sure how to do that, then using "Columns" class might be a good alternative for some
basic content arrangements.

.. php:method:: addColumn()

When you add new component to the page it will typically consume 100% width of its container. Columns
will break down width into chunks that can be used by other elements::

    $c = Columns::addTo($page);
    LoremIpsum::addTo($c->addColumn(), [1]);
    LoremIpsum::addTo($c->addColumn(), [1]);

By default width is equally divided by columns. You may specify a custom width expressed as fraction of 16::

    $c = Columns::addTo($page);
    LoremIpsum::addTo($c->addColumn(6), [1]);
    LoremIpsum::addTo($c->addColumn(10), [2]);  // wider column, more filler

You can specify how many columns are expected in a grid, but if you do you can't specify widths of individual
columns. This seem like a limitation of Fomantic UI::

    $c = Columns::addTo($page, ['width'=>4]);
    Box::addTo($c->addColumn(), ['red']);
    Box::addTo($c->addColumn([null, 'right floated']), ['blue']);

Rows
----

When you add columns for a total width which is more than permitted, columns will stack below and form a second
row. To improve and controll the flow of rows better, you can specify addRow()::

    $c = Columns::addTo($page, ['internally celled']);

    $r = $c->addRow();
    Icon::addTo($r->addColumn([2, 'right aligned']), ['huge home']);
    LoremIpsum::addTo($r->addColumn(12), [1]);
    Icon::addTo($r->addColumn(2), ['huge trash']);

    $r = $c->addRow();
    Icon::addTo($r->addColumn([2, 'right aligned']), ['huge home']);
    LoremIpsum::addTo($r->addColumn(12), [1]);
    Icon::addTo($r->addColumn(2), ['huge trash']);

This example also uses custom class for Columns ('internally celled') that adds dividers between columns and rows.
For more information on available classes, see https://fomantic-ui.com/collections/grid.html.

Responsiveness and Performance
------------------------------

Although you can use responsiveness with the Column class to some degree, we recommend that you create your own
component template where you can have greater control over all classes.

Similarly if you intend to output a lot of data, we recommend you to use :php:class:`Lister` instead with a custom
template.
