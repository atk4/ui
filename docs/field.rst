
.. _field:

=====================
Form Field Decorators
=====================

.. php:namespace:: atk4\ui\FormField

Agile UI dedicates a separate namespace for the Form Field Decorator. Those are
quite simple components that present themselves as input controls: line, select, checkbox.

Relationship with Form
======================

All Field Decorators can be integrated with :php:class:`\atk4\ui\Form` which will
facilitate collection and processing of data in a form. Field decorators can also
be used as stand-alone controls.

Stand-alone use
---------------

.. php:method:: set()
.. php:method:: jsInput()

Add any field decorator to your application like this::

    $field = $app->add(new \atk4\ui\FormField\Line());

You can set default value and ineract with a field using JavaScript::

    $field->set('hello world');


    $button = $app->add(['Button', 'check value']);
    $button->on('click', new \atk4\ui\jsExpression('alert("field value is: "+[])', [$field->jsInput()->val()]));


When used stand-alone, FormFields will produce a basic HTML (I have omitted id=)::

    <div class="ui  input">
        <input name="line" type="text" placeholder="" value="hello world"/>
    </div>


Using in-form
-------------

Field can also be used inside a form like this::

    $form = $app->add('Form');
    $field = $form->addField('name', new \atk4\ui\FormField\Line());

If you execute this exmple, you'll notice that Feld now has a label, it uses full width of the
page and the following HTML is now produced::

    <div class="field">
      <label for="atk_admin_form_generic_name_input">Name</label>
      <div id="atk_admin_form_generic_name" class="ui input" style="">
        <input name="name" type="text" placeholder="" id="atk_admin_form_generic_name_input" value="">
      </div>
    </div>

The markup that surronds the button which includes Label and formatting is produced by 
:php:class:`\atk4\ui\FormLayout\Generic`, which does draw some of the information from the Field
itself. 

Using in Form Layouts
---------------------

Form may have multiple Form Layouts and that's very useful if you need to split up form
into multiple Tabs or detach field groups or even create nested layouts::

    $form = $app->add('Form');
    $tabs = $form->add('Tabs', 'AboveFields');
    $form->add(['ui'=>'divider'], 'AboveFields');

    $form_page = $tabs->addTab('Basic Info')->add(['FormLayout\Generic', 'form'=>$form]);
    $form_page->addField('name', new \atk4\ui\FormField\Line());

    $form_page = $tabs->addTab('Other Info')->add(['FormLayout\Generic', 'form'=>$form]);
    $form_page->addField('age', new \atk4\ui\FormField\Line());

    $form->onSubmit(function($f) {  return $f->model['name'].' has age '.$f->model['age']; });

This is further explained in documentation for :php:class:`\atk4\ui\FormLayout\Generic` class,
however if you do plan on adding your own field types, it's important that you extend it
properly:

 - Generic (abstract, extends View) - Use this if field is NOT based on `<input>` 
 - Input (abstract, extends Generic) - Easiest since it alrady implements `<input>` and various
   ways to attach button to the input with markup of Semantic UI field.

Relatioship with Model
======================

In the examples above, we looked at the manual use where you create Field Decorator object explicitly.
The most common use-case in large application is use with Models. You would need a model, such as
`Country` model (see demos/database.php) as well as
`Persistence $db <http://agile-data.readthedocs.io/en/develop/persistence.html>`_.

Now, in order to create a form, the following is sufficient::

    $form = $app->add('Form');
    $form->setModel(new Country($db);

The above will populate fields from model into the form automatically. You can use second
argument to :php:meth:`\atk4\ui\Form::setModel()` to indicate which fields to display
or rely on :ref:`field_visibility`.

When Form fields are populated, then :php:meth:`\atk4\ui\Form::_decoratorFactory` is
consulted to make a decision on how to translate
`Model Field <http://agile-data.readthedocs.io/en/develop/fields.html>`_ into
Form Field Decorator.

The rules are rather straightforward but may change in future versions of Agile UI:

 - if `enum <http://agile-data.readthedocs.io/en/develop/fields.html#Field::$enum>`_ is defined, use :php:class:`DropDown`
 - consult :php:attr:`\atk4\ui\Form::$typeToDecorator` property for type-to-seed association
 - type=password will use :php:class:`Password`

You always have an option to explicitly specify which field you would like to use::

    $model->addField('long_text', ['ui'=>['Form'=>'TextArea']]);

It is recommended however, that you use type when possible, because types will be universally supported
by all components::

    $model->addField('long_text', ['type'=>'text']);

.. note:: All forms will be associted with a model. If form is not explicitly linked with a model, it will create
    a ProxyModel and all fields will be created automatically in that model. As a result, all Field Decorators
    will be linked with Model Fields.

Link to Model Field
-------------------

.. php:attr:: $field

Form decorator defines $field property which will be pointing to a field object of a model, so technically
the value of the field would be read from `$decorator->field->get()`.



Line Input Field
================

.. php:class:: Input

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

When you use :php:class:`form::addField()` it will create 'Field Decorator'

JavaScript on Input
-------------------

.. php:method:: jsInput([$event, [$other_action]])

Input class implements method jsInput which is identical to :php:meth:`View::js`, except
that it would target the INPUT element rather then the whole field::

    $field->jsInput(true)->val(123);


DropDown
========

.. php:class:: DropDown

.. php:attr:: $values

.. php:attr:: $empty


AutoComplete
============

.. php:class:: AutoComplete

