

.. _form:

=====
Forms
=====

.. php:namespace:: atk4\ui

.. php:class:: Form

One of the most important components of Agile UI is the "Form". Class ``Form``
implements the following 4 major features:

- Form Rendering using Semantic UI Form (https://semantic-ui.com/collections/form.html):

    .. image:: images/form.png

- Loading and storing data in any database (SQL, NoSQL) supported by Agile Data (http://agile-data.readthedocs.io/en/develop/persistence.html).
- Full Integration with Events and Actions (:ref:`js`)
- PHP-based Submit Handler using callbacks (:ref:`callback`)

Form can be used a web application built entirely in Agile UI or you can extract
the component by integrating it into your existing application or framework.

Basic Usage
===========

It only takes 2 PHP lines to create a fully working form::

    $form = $app->add('Form');
    $form->addField('email');

The form component can be further tweaked by setting a custom call-back handler
directly in PHP::

    $form->onSubmit(function($form) {
        // implement subscribe here

        return "Subscribed ".$form->model['email']." to newsletter.";
    });

Form is a composite component and it relies on other components to render parts
of it. Form uses :php:class:`Button` that you can tweak to your liking::

    $form->buttonSave->set('Subscribe');
    $form->buttonSave->icon = 'mail';

Form also relies on a ``atk4\ui\FormLayout`` class and displays fields through
decorators defined at ``atk4\ui\FormField``. See dedicated documentation for:

 - :php:class:`FormLayout::Generic`
 - :php:class:`FormField::Generic`

The rest of this chapter will focus on Form mechanics, such as submission,
integration with front-end, integration with Model, error handling etc.

Usage with Model
----------------

A most common use of form is if you have a working Model (http://agile-data.readthedocs.io/en/develop/model.html)::

    // Form will automatically add a new user and save into the database
    $form = $app->add('Form');
    $form->setModel(new User($db));

The basic 2-line syntax will extract all the required logic from the Model including:

 - Fields defined for this Model will be displayed
 - Display of default values in the form
 - Depending on field type, a decorator will be selected from FormField/Generic
 - Using :php:class:`FormLayout::Columns` can make form more compact by splitting it into columns
 - Field captions, placeholders, hints and other elements defined in Field::ui are respected (http://agile-data.readthedocs.io/en/develop/fields.html#Field::$ui)
 - Fields that are not editable by default will not appear on the form (http://agile-data.readthedocs.io/en/develop/fields.html#Field::isEditable)
 - Field typecasting will be invoked such as for converting dates
 - Reference fields (http://agile-data.readthedocs.io/en/develop/references.html?highlight=hasOne#hasone-reference) displayed as Dropdowns
 - Booleans are displayed as checkboxes but stored as defined by the model field
 - Mandatory and Required fields will be visually highlighted (http://agile-data.readthedocs.io/en/develop/fields.html?highlight=required#Field::$mandatory)
 - Validation will be performed and errors will appear on the form (NEED LINK)
 - Unless you specify a submission handler, form will save the model ``User`` into ``$db`` on successful submission.

All of the above works auto-magically, but you can tweak it even more:

 - Provide custom submission handler
 - Specify which fields and in which order to display on the form
 - Override labels, decorator classes
 - Froup fields or use custom layout template
 - Mix standard model fields with your own
 - Add JS Actions around fields
 - Split up form into multiple tabs

If your form is NOT associated with a model, then Form will automatically create a :php:class:`ProxyModel`
and associate it with your Form. As you add fields, they will also be added into ProxyModel.

Extensions
----------

Starting with Agile UI 1.3 Form has a stable API and we expect to introduce some extensions like:

 - Capcha decorator
 - File Upload field
 - Multi-record form
 - Multi-tab form

If you develop feature like that, please let me know so that I can include it in the documentation
and give you credit.


Adding Fields
=============

.. php:method:: addField($name, $decorator = null, $field = null)

Create a new field on a form::

    $form = $app->add('Form');
    $form->addField('email');
    $form->addField('gender', ['Dropdown', 'values'=>['Female', 'Male']);
    $form->addField('terms', null, ['type'=>'boolean', 'caption'=>'Agree to Terms & Conditions']);

Create a new field on a form using Model does not require you to describe each field.
Form will rely on Model Field Definition and UI meta-values to decide on the best way to handle
specific field type::

    $form = $app->add('Form');
    $form->setModel(new User($db), ['email', 'gender', 'terms']);

Adding new fields
-----------------

First argument to addField is the name of the field. You cannot have multiple fields
with the same name.

If field exist inside associated model, then model field definition will be used as
a base, otherwise you can specify field definition through 3rd argument. I explain
that below in more detail.

You can specify first argument ``null`` in which case decorator will be added without
association with field. This will not work with regular fields, but you can add
custom decorators such as CAPCHA, which does not really need association with a
field.

Field Decorator
---------------

To avoid term miss-use, we use "Field" to refer to ``\atk4\data\Field``. This class
is documented here: http://agile-data.readthedocs.io/en/develop/fields.html

Form uses a small UI components to vizualize HTML input fields associated with
the respective Model Field. We call this object "Field Decorator". All field
decorators extend from class :php:class:`FormField::Generic`.

Agile UI comes with at least the following decorators:

- Input (also extends into Line, Password, Hidden)
- Dropdown
- Checkbox
- Radio
- Calendar
- Radio
- Money

For some examples see: http://ui.agiletoolkit.org/demos/form3.php

Field Decorator can be passed to ``addField`` using 'string', :php:ref:`seed` or 'object'::

    $form->addField('accept_terms', 'Checkbox');
    $form->addField('gender', ['Dropdown', 'values'=>['Female', 'Male']]);

    $calendar = new \atk4\ui\FormField\Calendar();
    $calendar->type = 'tyme';
    $calendar->options['ampm'] = true;
    $form->addField('time', $calendar);

For more information on default decorators as well as examples on how to create
your own see documentation on :php:class:`FormField::Generic`.

.. php:method:: decoratorFactory(\atk4\data\Field $f, $defaults = [])

If Decorator is not specified (``null``) then it's class will be determined from
the type of the Data Field with ``decoratorFactory`` method.

Data Field
----------

Data field is the 3rd argument to ``Form::addField()``.

There are 3 ways to define Data Field using 'string', 'array' or 'object'::

    $form->addField('accept_terms', 'Checkbox', 'Accept Terms & Conditions');
    $form->addField('gender', null, ['enum'=>['Female', 'Male']]);

    class MyBoolean extends \atk4\data\Field {
        public $type = 'boolean';
        public $enum = ['N', 'Y'];
    }
    $form->addField('test2', null, new MyBoolean());

String will be converted into ``['caption' => $string]`` a short way to give
field a custom label. Without a custom label, Form will clean up the name (1st
argument) by replacing '_' with spaces and uppercasing words (accept_terms
becomes "Accept Terms")

Specifying array will use the same syntax as the 2nd argument for ``\atk4\data\Model::addField()``.
(http://agile-data.readthedocs.io/en/develop/model.html#Model::addField)

If field already exist inside model, then values of $field will be merged into
existing field properties. This example make email field mandatory for the form::

    $form = $app->add('Form');
    $form->setModel(new User($db), false);

    $form->addField('email', null, ['required'=>true]);

addField into Existing Model
----------------------------

If your form is using a model and you add additional field, then it will automatically
be marked as "never_persist" (http://agile-data.readthedocs.io/en/develop/fields.html#Field::$never_persist).

This is to make sure that custom fields wouldn't go directly into database. Next
example displays a registration form for a User::

    class User extends \atk4\data\Model {
        public $table = 'user';
        function init() {
            parent::init();

            $this->addField('email');
            $this->addFiled('password');
        }
    }

    $form = $app->add('Form');
    $form->setModel(new User($db));

    // add password verification field
    $form->addField('password_verify', 'Password', 'Type password again');
    $form->addField('accept_terms', null, ['type'=>'boolean']);

    // submit event
    $form->onSubmit(function($form){
        if ($form->model['password'] != $form->model['password_verify']) {
            return $form->error('password_verify', 'Passwords do not match');
        }

        if (!$form->model['accept_terms']) {
            return $form->error('accept_terms', 'Read and accept terms');
        }

        $form->model->save(); // will only store email / password
        return $form->success('Thank you. Check your email now');
    });

Type vs Decorator Class
-----------------------

Sometimes you may wonder - should you pass decorator class ('Checkbox') or
a data field type (['type' => 'boolean']);

I always to recommend use of field type, because it will take care of type-casting
for you. Here is an example with date::

    $form = $app->add('Form');
    $form->addField('date1', null, ['type'=>'date']);
    $form->addField('date2', ['Calendar', 'type'=>'date']);

    $form->onSubmit(function($form) {
        echo 'date1 = '.print_r($form->model['date1'], true).' and date2 = '.print_r($form->model['date2'], true);
    });

Field ``date1`` is defined inside a :php:class:`ProxyModel` as a date field and will
be automatically converted into DateTime object by Persistence typecasting.

Field ``date2`` has no type and therefore Persistence typecasting will not modify it's
value and it's stored inside model as a string.

The above code result in the following output::

    date1 = DateTime Object ( [date] => 2017-09-03 00:00:00 .. ) and date2 = September 3, 2017

Seeding Decorator from Model
----------------------------

In a large projects, you most likely will not be setting individual fields for each Form, instead
you would simply use ``addModel()`` to populate all defined fields inside a model. Form does
have a pretty good guess about Decorator based on field type, but what if you want to
use a custom decorator?

This is where ``$field->ui`` comes in (http://agile-data.readthedocs.io/en/develop/fields.html#Field::$ui).

You can specify ``'ui'=>['form' => $decorator_seed]`` for your model field::

    class User extends \atk4\data\Model {
        public $table = 'user';

        function init() {
            parent::init();

            $this->add('email');
            $this->add('password', ['type'=>'password']);

            $this->add('birth_year', ['type'=>'date', 'ui'=>['type'=>'month']);
        }
    }

The seed for the UI will be combined with the default overriding :php:attr:`FormField\Calendar::type`
to allow month/year entry by the Calendar extension, which will then be saved and
stored as a regular date. Obviously you can also specify decorator class::

    $this->add('birth_year', ['ui'=>['Calendar', 'type'=>'month']);

Without the 'type' propoerty, now the calendar selection will be stored as text.

using setModel()
----------------

Although there were many examples above for the use of setModel() this method
needs a bit more info::

.. php:attr:: model

.. php:method:: setModel($model, [$fields])

Associate field with existing model object and import all editable fields
in the order in which they were defined inside model's init() method.

You can specify which fields to import and their order by simply listing
field names through second argument.

Specifying "false" or empty array as a second argument will import no fields,
so you can then use `addField` to import fields individually.

See also: http://agile-data.readthedocs.io/en/develop/fields.html#Field::isEditable

Loading Values
--------------

Although you can set form fields individually using ``$form->model['field'] = $value``
it's always nicer to load values for the database. Given a ``User`` model this is how
you can create a form to change profile of a currently logged user::

    $user = new User($db);
    $user->getElement('password')->never_persist = true; // ignore password field
    $user->load($current_user);

    // Display all fields (except password) and values
    $form = $app->add('Form');
    $form->setModel($user);

Submitting this form will automatically store values back to the database. Form uses
POST data to submit itself and will re-use the query-string, so you can also safely
use any GET arguments for passing record $id. You may also perform model load after
record association. This gives the benefit of not loading any other fileds, unless
it's marked as System (http://agile-data.readthedocs.io/en/develop/fields.html#Field::$system),
see http://agile-data.readthedocs.io/en/develop/model.html?highlight=onlyfields#Model::onlyFields::

    $form = $app->add('Form');
    $form->setModel(new User($db), ['email', 'name']);
    $form->model->load($current_user);

As before, field ``password`` will not be loaded from the database, but this time
using onlyFields restriction rather then `never_persist`.

Validating
----------

Topic of Validation in web apps is quite extensive. You sould start by reading what Agile Data
has to say about validation:
http://agile-data.readthedocs.io/en/develop/persistence.html#validation

TL;DR - sometimes validation needed when storing field value inside model (e.g. setting boolean
to "blah") and sometimes validation should be performed only when storing model data into
database.

Here are few questions:

- If user specified incorrect value into field, can it be stored inside model and then
  re-displayed in the field again? If user must enter "date of birth" and he picks date
  in the future, should we reset field value or simply indicate error?

- If you have a multi-step form with a complex logic, it may need to run validation before
  record status changes from "draft" to "submitted".

As far as form is concerned:

- Decorators must be able to parse entered values. For instance Dropdown will make sure that
  value entered is one of the available values (by key)

- Form will rely on Agile Data Typecasting (http://agile-data.readthedocs.io/en/develop/typecasting.html)
  to load values from POST data and store them in model.

- Form submit handler will rely on ``Model::save()`` (http://agile-data.readthedocs.io/en/develop/persistence.html#Model::save)
  not to throw validation exception.

- Form submit handler will also interpret use of :php:meth:`Form::error` by displaying errors that
  do not originate inside Model save logic.

Example use of Model's validate() method::

    class Person extends \atk4\data\Model
    {
        public $table = 'person';

        public function init()
        {
            parent::init();
            $this->addField('name', ['required'=>true]);
            $this->addField('surname');
            $this->addField('gender', ['enum' => ['M', 'F']]);
        }

        public function validate()
        {
            $errors = parent::validate();

            if ($this['name'] == $this['surname']) {
                $errors['surname'] = 'Your surname cannot be same as the name';
            }

            return $errors;
        }
    }



We can now populate form fields based around the data fields defined in the model::

    $app->add('Form')
        ->setModel(new Person($db));

This should display a following form::

    $form->addField(
        'terms',
        ['type'=>'boolean', 'ui'=>['caption'=>'Accept Terms and Conditions']]
    );

Form Submit Handling
--------------------

.. php:method:: onSubmit($callback)

    Specify a PHP call-back that will be executed on successful form submission.

.. php:method:: error($field, $message)

    Create and return :php:class:`jsChain` action that will indicate error on a field.

.. php:method:: success($title, [$sub_title])

    Create and return :php:class:`jsChain` action, that will replace form with a success message.

.. php:attr:: successTemplate

    Name of the template which will be used to render success message.

To continue with my example, I'd like to add new Person record into the database
but only if they have also accepted terms and conditions. I can define onSubmit handler
that would perform the check, display error or success message::

    $form->onSubmit(function($form) {
        if (!$form->model['terms']) {
            return $form->error('terms', 'You must accept terms and conditions');
        }

        $form->model->save();

        return $form->success('Registration Successful', 'We will call you soon.');
    });

Callback function can return one or multiple JavaScript actions. Methods such as
:php:meth:`error()` or :php:meth:`success()` will help initialize those actions for your form. Here is a code
that can be used to output multiple errors at once. I intentionally didn't want to group
errors with a message about terms and conditions::

    $form->onSubmit(function($form) {
        $errors = [];

        if (!$form->model['name']) {
            $errors[] = $form->error('name', 'Name must be specified');
        }

        if (!$form->model['surname']) {
            $errors[] = $form->error('surname', 'Surname must be specified');
        }

        if ($errors) {
            return $errors;
        }

        if (!$form->model['terms']) {
            return $form->error('terms', 'You must accept terms and conditions');
        }

        $form->model->save();

        return $form->success('Registration Successful', 'We will call you soon.');
    });

At the time of writing, Agile UI / Agile Data does not come with a validation library, but
you can use any 3rd party validation code.

Callback function may raise exception. If Exception is based on ``\atk4\core\Exception``,
then the parameter "field" can be used to associate error with specific field::

    throw new \atk4\core\Exception(['Sample Exception', 'field'=>'surname']);

If 'field' parameter is not set or any other exception is generated, then error will not be
associated with a field. Only the main Exception message will be delivered to the user.
Core Exceptions may contain some sensitive information in parameters or back-trace, but those
will not be included in response for security reasons.


Form Layout
-----------

When you create a Form object and start adding fields through either :php:meth:`addField()` or
:php:meth:`setModel()`, they will appear one under each-other. This arrangement of fields as
well as display of labels and structure around the fields themselves is not done by a form,
but another object - "Form Layout". This object is responsible for the field flow, presence
of labels etc.

.. php:method:: setLayout(FormLayout\Generic $layout)

    Sets a custom FormLayout object for a form. If not specified then form will automatically
    use FormLayout\Generic.

.. php:attr:: layout

    Current form layout object.

.. php:method:: addHeader($header)

    Adds a form header with a text label. Returns View.

.. php:method:: addGroup($header)

    Creates a sub-layout, returning new instance of a :php:class:`FormLayout\Generic` object. You
    can also specify a header.

.. todo:: MOVE THIS TO SEPARATE FILE

.. php:class:: FormLayout\Generic

    Renders HTML outline encasing form fields.

.. php:attr:: form

    Form layout objects are always associated with a Form object.

.. php:method:: addField()

    Same as :php:class:`Form::addField()` but will place a field inside this specific layout
    or sub-layout.

My next example will add multiple fields on the same line::

    $form->setModel(new User($db), false);  // will not populate any fields automatically

    $form->addFields(['name', 'surname']);

    $gr = $form->addGroup('Address');
    $gr->addFields(['address', 'city', 'country']); // grouped fields, will appear on the same line

By default grouped fields will appear with fixed width. To distribute space you can either specify
proportions manually::

    $gr = $f->addGroup('Address');
    $gr->addField('address', ['width'=>'twelve']);
    $gr->addField('code', ['Post Code', 'width'=>'four']);

or you can divide space equally between fields. I am also omitting header for this group::

    $gr = $f->addGroup(['n'=>'two']);
    $gr->addFields(['city', 'country']);

You can also use in-line form groups. Fields in such a group will display header on the left and
the error messages appearing on the right from the field::

    $gr = $f->addGroup(['Name', 'inline'=>true]);
    $gr->addField('first_name', ['width'=>'eight']);
    $gr->addField('middle_name', ['width'=>'three', 'disabled'=>true]);
    $gr->addField('last_name', ['width'=>'five']);

Semantic UI modifiers
---------------------

There are many other classes Semantic UI allow you to use on a form. The next code will produce
form inside a segment (outline) and will make fields appear smaller::

    $f = new \atk4\ui\Form(['small segment']));

For further styling see documentation on :php:class:`View`.
