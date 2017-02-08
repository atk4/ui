

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

First argument of constructor or first element in array passed to constructor is considered 
