

.. php:namespace: atk4\\ui

==================
Miscelaneous Views
==================

There are a lot of Views in Agile Toolkit that all behave similar and can be
summarized together. Those views share the following properties:

 - Extend directly from View
 - Use single HTML element or a very simple element structure
 - Do not benefit from setModel other than setting the title
 - May contain other misc elements


.. php:class:: Icon

Icon
====

Probably the simplest element, Icon is often a single element as depicted on http://semantic-ui.com/elements/icon.html. 
To add icon inside your application::

    $this->add('Icon', 'bomb');

The :ref:`seed` is used as an icon class. It may contain spaces to separate multiple classes. You can add additional classes or remove them
through :php:meth:`View::addClass` :php:meth:`View::removeClass`::

    $this->add('Icon', 'flag')->addClass('outline');

If you are not ready to add 'Icon' into a render-tree, you can use `new`::

    $icon = new Icon('battery low');

Special Icons
-------------

You may add additional classes to your icon for size, positioning, disabling, rotation, decorating. Consult documentation
on Semantic UI, since Agile UI will not perform any special treatment on those classes::

    $icon = new Icon('circular inverted teal users');

    $no_users = new View([null, 'huge icons']);
    $no_users->add(new Icon('big red dont'));
    $no_users->add(new Icon('black user icon'));

    $chainsaw = new View([null, 'huge icons']);
    $chainsaw->add(new Icon('big loading sun'));
    $chainsaw->add(new Icon('user'));

Agile UI does not implement a special class for 'Icons' because it has not much custom properties and can easily used
through a generic View class. The next example demonstrates how to integrate 'corner icon' into a header::

    $tw = new View([null, 'large icons']);
    $tw->add(new Icon('twitter'));
    $tw->add(new Icon('inverted corner add'));

    $h = new Header(['Add on Twitter', ['icon'=>$tw]);

As a part of
------------

Icon class is often used as a part of another view. For instance a Button may have an icon defined::

    $b = new Button(['Learn', 'icon'=>'book');

This format is identical but cleaner than passing the object (also saves the work until rendering phase)::

    $b = new Button(['Learn', 'icon'=>new Icon('book'));

    // or

    $button = new Button('Learn');
    $button -> icon = 'book';

Most other use the following pattern to decode 'icon' property. If you are designing a component
that may contain one or several icons, use the following rules:

 - define each icon positioning separatly. (icon, leftIcon, etc)
 - the main icon properly should be caled 'icon'
 - icon may be string, array or an object

The code placed in renderView will look like this::

    if ($this->icon) {
        if (!is_object($this->icon)) {
            $this->icon = new Icon($this->icon);
        }
        $this->add($this->icon, 'Icon');
    }

Following this pattern will make sure that developer who uses your component is able to inject an
alternative object for an icon yet has the ability to use short format. Additionally between the
init and :php:meth:`View::renderView` other logic may intervene and perform actions with the icon.

Some elements will also add an extra class when icon is used, for example when adding 'icon' property
with the button, it will add 'labeled' class.

.. php:class:: Label

Label
=====

Implementing http://semantic-ui.com/elements/label.html for Agile UI, Label is a very basic view,
that can be used on it's own or as part of another UI view (such as menu item).

Basic Usage
-----------

First argument of constructor or first element in array passed to constructor is considered::

    $layout->add(['Label', 'hello world']);

Label has the following propetries:

.. php:attr:: icon

.. php:attr:: iconRight

.. php:attr:: image

.. php:attr:: imageRight

.. php:attr:: detail

All the above can be string, array (passed to Icon, Image or View class) or an object.

.. php:class:: HelloWorld

HelloWorld
==========

A very basic class that says hello world. This is a manefistation of our component concept. If using
other PHP frameworks may require you to create multiple files and spend considerable time creating even
a "Hello, World!" app, then we do it in a single line::

    $app->layout->add('HelloWorld');

The component will output "Hello, World!".

.. php:class:: LoremIpsum

LoremIpsum
==========

This is another component that is included for learning purposes, but is also quite useful in actual
development. It saves you a trip to google and some copy-pasting action for a filler text. Simply
add 'LoremIpsum' component::

    $app->layout->add('LoremIpsum');

You may specify amont of text to be generated with lorem::

    $app->layout->add(['LoremIpsum', 1]); // just add a little one

    // or

    $app->layout->add(new LoremIpsum(5)); // adds a lot of text

.. php:class:: Columns

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
For more information on available classes, see http://semantic-ui.com/collections/grid.html.

Responsiveness and Performance
------------------------------

Although you can use responsiveness with the Column class to some degree, we recommend that you create your own
component template where you can have greater control over all classes.

Similarly if you intend to output a lot of data, we recommend you to use :php:class:`Lister` instead with a custom
template.
