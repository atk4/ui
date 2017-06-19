.. _seed:

Purpose of the Seed
-------------------


When creating a view, you have a chance to pass an argument to it. We have decided to
refer to this special argument as "seed" because it has multiple purposes and the structure
may differ depending on the element.

The most trivial case of seeding is::

    $button = new Button('Hello');

Here button is seeded with a string and the button interprets it by setting a label. Other
Views may interpret seed differenttly, Icon will convert seed into a class::

    $icon = new Icon('book');

The seed designed to be intuitive for reading and remembering rather than attaching it
to a specific technical property. Here are some more examples::

    $app = new App('Hello World'); // name of the app

Empty Seed
----------

Some views may not use a seed (yet), they will still accept an empty seed::

    $app = new App();       // will use name = 'Untitled'
    $form = new Form();     // no name yet


Dependency Injection
--------------------

Seed is a great way for you to perform dependency injection because seed argument may
be an array. If seed is specified as "array", then the value with index "0" will have
identical effect as not using array::

    $button = new Button('Hello');

    // same as

    $button = new Button(['Hello']);

Once the zero-indexed value is located and extracted from the seed, the rest of the array
will be used as a dependency-injection or "defaults"::

    $button = new Button(['Learn', 'icon'=>new Icon('book')]);

This will set the "icon" property of a Button class to the specified value (object). Setting
of an object properties in only possible, if the property is declared. Attempt to set
non-existant property will result in exception::

    $button = new Button(['Learn', 'my_property'=>123]);

Additional cases
----------------

An individual object may add more ways to deal with seed. For example, when dealing with button
you can specify both the label and the class through the seed::

    $button = new Button(['Learn', 'big teal', 'icon'=>new Icon('book')]);

The view will generally map non-existing property seeds into HTML class, although it is recommended
to use :php:meth:`View::addClass` method::

    $button = new Icon(['book', 'red'=>true]);

    // same as

    $button = new Icon('book');
    $button->addClass('red');

    // or because it's a button
    $button = new Icon('red book');

