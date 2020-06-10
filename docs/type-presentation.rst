
.. _type-presentation:

Formatters vs Decorators
========================

This chapter describes a common technique used by various components that wish to preserve
extensible nature when dealing with used-defined types. Reading this chapter will also help
you understand some of the thinking behind major decisions when designing the type system.

When looking into the default money field in Agile UI, which does carry amount, but not
the currency, there are a number of considerations when dealing with the field. The first
important concept to understand is the distinction between data Presentation and Decoration.

 - Data Presentation: displaying value of the data in a different format, e.g. 123,123.00 vs 123.123,00
 - Data Decoration: adding currency symbol or calendar icon.

Agile UI believes that presentation must be consistent throughout the system. A monetary
field will use same format on the :php:class:`Form`, :php:class:`Table` and even inside a
custom HTML template specified into generic :php:class:`View`. 

When it comes to decoration, the method is very dependent on the context. A form may present
Calendar (DatePicker) or enable field icon to indicate currency.

Presentation in Agile Toolkit is handled by :php:class:`Persistence\\UI`.

Decoration is performed by helper classes, such as :php:class:`FormField\\Calendar` or
:php:class:`TableColumn\\Money`. The decorator is in control of the final output, so it can decide if
it uses the value from presentation or do some decoration on its own.

Extending Data Types
====================

If you are looking to add a new data type, such as "money+currency" combination, which would
allow user to specify both the currency and the monetary value, you should start by adding
support for a new type.

In the below steps, the #1 and #2 are a minimum to achieve. #3 and #4 will improve experience
of your integration.

 1. Extend UI persistence and use your class prototype in `$app->persistence`.

  You need to define how to output your data as well as read it.

 2. Try your new type with a standard Form field.

  The value you output should read and stored back correctly.
  This ensures that standard UI will work with your new data type.

 3. Create your new decorator.

  Such as use drop-down to select currency from a pre-defined list inside your specific class
  while extending :php:class:`FormField\\Input` class. Make sure it can interpret input correctly.
  The process is explained further down in this chapter.

 4. Associate the types with your decorator.

  This happens in :php:meth:`Form::_fieldFactory` and :php:meth:`Table::_columnFactory`.

For the third party add-ons it is only possible to provide decorators. They must rely on one of
the standard types, unless they also offer a dedicated model.

Manually Specifying Decorators
==============================

When working with components, they allow to specify decorators manually, even if the type
of the field does not seem compatible::

    $table->addColumn('field_name', new \atk4\ui\TableColumn\Password());

    // or

    $form->addField('field_name', new \atk4\ui\FormField\Password());

Selecting the decorator is done in the following order:

 - specified in second argument to UI `addColumn()` or `addField()` (as shown above)
 - specified using `ui` property of :php:class:`\atk4\data\Field`::

    $field->ui['form'] = new \atk4\ui\FormField\Password();

 - fallback to :php:meth:`Form::_fieldFactory`

.. note:: When talking about "fields": you need to know what kind of field you are talking about (Data or UI).
    Both **models** (Data) as well as some **views** (UI: form) use fields. They are not the same.
    Notably, Model field `ui` property contains flags like editable, visible and hidden,
    which do have some impact on rendering, whereas UI field `ui` property (not used here)
    designates the Fomantic UI element to use.

Examples
========

Let's explore various use cases and how to properly deal with scenarios

Display password in plain-text for Admin
----------------------------------------

Normally password is presented as asterisks on the Grid and Form. But what if you want to
show it without masking just for the admin? Change type in-line for the model field::

    $m = new User($app->db);
    $m->getElement('password')->type = 'string';

    $crud->setModel($m);

.. note:: Changing element's type to string will certainly not perform any password encryption.

Hide account_number in specific Table
-------------------------------------

This is reverse scenario. Field `account_number` needs to be stored as-is but should be
hidden when presented. To hide it from Table::

    $m = new User($app->db);
    
    $table->setModel($m);
    $m->addDecorator('account_number', new \atk4\ui\TableColumn\Password());

Create a decorator for hiding credit card number
------------------------------------------------

If you happen to store card numbers and you only want to display the last digits in tables,
yet make it available when editing, you could create your own :php:class:`TableColumn` decorator::

    class Masker extends \atk4\ui\TableColumn\Generic
    {
        public function getDataCellTemplate(\atk4\data\Field $f = null)
        {
            return '**** **** **** {$mask}';
        }

        public function getHTMLTags(\atk4\data\Model $row, $field)
        {
            return [
                'mask' => substr($field->get(), -4) 
            ];
        }
    }

If you are wondering, why I'm not overriding by providing HTML tag equal to the field name,
it's because this technique is unreliable due to ability to exclude HTML tags with
:php:attr:`Table::$use_html_tags`.

Display credit card number with spaces
--------------------------------------
If we always have to display card numbers with spaces, e.g. "1234 1234 1234 1234" but have
the database store them without spaces, then this is a data formatting task best done by
extending :php:class:`Persistence\\UI`::

    class MyPersistence extends \atk4\ui\Persistence\UI
    {

        public function _typecastSaveField(\atk4\data\Field $f, $value)
        {
            switch ($f->type) {
            case 'card':
                $parts = str_split($value, 4);
                return join(' ', $parts);
            }
            return parent::_typecastSaveField($f, $value);
        }

        public function _typecastLoadField(\atk4\data\Field $f, $value)
        {
            switch ($f->type) {
            case 'card':
                return str_replace(' ', '', $value);
            }
            return parent::_typecastLoadField($f, $value);
        }
    }

    class MyApp extends App
    {
        public function __construct($defaults = [])
        {
            $this->ui_persistence = new MyPersistence()

            parent::__construct($defaults);
        }

    }

Now your 'card' type will work system-wide.
