

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

    $c = $page->add(new \atk4\ui\Columns());
    $c->addColumn()->add(['LoremIpsum', 1]);
    $c->addColumn()->add(['LoremIpsum', 1]);

By default width is equally divided by columns. You may specify a custom width expressed as fraction of 16::

    $c = $page->add(new \atk4\ui\Columns());
    $c->addColumn(6)->add(['LoremIpsum', 1]);
    $c->addColumn(10)->add(['LoremIpsum', 2]);  // wider column, more filler

You can specify how many columns are expected in a grid, but if you do you can't specify widths of individual
columns. This seem like a limitation of Semantic UI::

    $c = $page->add(new \atk4\ui\Columns(['width'=>4]));
    $c->addColumn()->add(new Box(['red']));
    $c->addColumn([null, 'right floated'])->add(new Box(['blue']));

Rows
----

When you add columns for a total width which is more than permitted, columns will stack below and form a second
row. To improve and controll the flow of rows better, you can specify addRow()::

    $c = $page->add(new \atk4\ui\Columns(['internally celled']));

    $r = $c->addRow();
    $r->addColumn([2, 'right aligned'])->add(['Icon', 'huge home']);
    $r->addColumn(12)->add(['LoremIpsum', 1]);
    $r->addColumn(2)->add(['Icon', 'huge trash']);

    $r = $c->addRow();
    $r->addColumn([2, 'right aligned'])->add(['Icon', 'huge home']);
    $r->addColumn(12)->add(['LoremIpsum', 1]);
    $r->addColumn(2)->add(['Icon', 'huge trash']);

This example also uses custom class for Columns ('internally celled') that adds dividers between columns and rows.
For more information on available classes, see https://semantic-ui.com/collections/grid.html.

Responsiveness and Performance
------------------------------

Although you can use responsiveness with the Column class to some degree, we recommend that you create your own
component template where you can have greater control over all classes.

Similarly if you intend to output a lot of data, we recommend you to use :php:class:`Lister` instead with a custom
template.
