

.. _form:

=====
Forms
=====

.. php:namespace:: atk4\ui

One of the most important views of Agile UI is "Form". Features of a Form include:

 - Rendering a valid HTML form
    - including nested fields
    - supports field grouping
    - adds labels, placeholders and hints
    - allows any "Field" valiations (see :ref:`field`)
    - automatically sets ID for form/fields/labels
    - labels are linked with input elements
    - supports automated or custom field layouts

 - Augment Form with JS integration (based around Semantic UI API)
    - form is submitted using JavaScript
    - during submit, loading indicator is shown
    - javascript sends data through POST

 - You may define onSubmit PHP routine that will decide what to do
    - display error for any field
    - display errors for multiple fields
    - perform custom actions on "input" element such as set value
    - perform custom action on "field" div, such as use checkbox APIs.
    - indicate successful completion of a form (will disable furter use of form)


Agile UI dedicates a separate namespace for the Form Fields. What's common about
the Form Fields is that they have either 'input' or 'select' element inside them
making them perfect for using inside a :php:class:`Form`.

Field can also be used on it's own like this::

    $page->add(new \atk4\ui\FormField\Line());

There are 3 important classes in FormField namespace that you should be aware of:

 - Generic (abstract, extends View) - Bindings with Form and Models.
 - Input (abstract, extends Generic) - HTML-generation capabilities.
 - Line, Money, etc (extends Input) - Deal with specific Field Type.

When you define your Model Fields, the 'type' will be mapped to appropriate FormField
object. Most of those will extend Input, so it makes sense to start by looking
at this class.

Look and Feel
-------------

.. php:class: Input

    Implements View for presenting Input fields. Based around http://semantic-ui.com/elements/input.html.

Similar to other views, Input has various properties that you can specify directly
or inject through constructor. Those properties will affect the look of the input
element. For example, `icon` property:

.. php:attr: icon
.. php:attr: iconLeft

    Adds icon into the input field. Default - `icon` will appear on the right, while `leftIcon`
    will display icon on the left.

Here are few ways to specify `icon` to an Input::

    // compact
    $page->add(new \atk4\ui\FormField\Line('icon'=>'search'));

    // Type-hinting friendly
    $line = new \atk4\ui\FormField\Line();
    $line->icon='search';
    $page->add($line);

    // using class factory
    $page->add('FormField/Line', ['icon'=>'search']);

The 'icon' property can be either string or a View. The string is for convenience and will
be automatically substituted with `new Icon($icon)`. If you wish to be more specifc
and pass some arguments to the icon, there are two options::

    // compact
    $line->icon=['search', 'big'];

    // Type-hinting friendly
    $line->icon = new Icon('search');
    $line->icon->addClass('big');

To see how Icon interprets `new Icon(['search', 'big'])`, refer to :php:class:`Icon`.

.. note::

    View's constructor will map received arguments into object properties, if they are defined
    or addClass() if not. See :php:meth:`View::setProperties`.

.. php:attr:: placeholder

    Will set placeholder property.

.. php:attr:: loading

    Set to "left" or "right" to display spinning loading indicator.

.. php:attr:: label
.. php:attr:: labelRight

    Convert text into :php:class:`Label` and insert it into the field.

.. php:attr:: action
.. php:attr:: actionLeft

    Convert text into :php:class:`Button` and insert it into the field.

To see various examples of fields and their attributes see `demos/field.php`.

Integration with Form
---------------------

This section explains how Field interracts with the form.
