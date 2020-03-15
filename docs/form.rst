

.. _form:

=====
Forms
=====

.. php:namespace:: atk4\ui

.. php:class:: Form

One of the most important components of ATK UI is the "Form". Class :php:class:`Form`
implements the following 4 major features:

- Form Rendering using Fomantic UI HTML/CSS (https://fomantic-ui.com/collections/form.html):

    .. image:: images/form.png

- Fields are automatically populated based on your existing data model with special treatment
  for date/time, auto-complete and even file upload.

- Loading data from database and storing it back. Any persistence (SQL, NoSQL) supported by
  ATK Data (https://agile-data.readthedocs.io/en/develop/persistence.html) can be used.

- Support for Events and Actions on fields, buttons and form callback. (:ref:`js`) Automatic
  execution of PHP-based Submit Handler passing all the collected data (:ref:`callback`)

So if looking for a PHP Form class, ATK Form has the most complete implementation which does
not require to fall-back into HTML / JS, perform any data conversion, load / store data and
implement any advanced interactions such as file uploads.

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

or you can tweak it when you create form like this::

    $form = $app->add(['Form', 'buttonSave'=>[null, 'Subscribe', 'icon'=>'mail']]);

To set the default values in the fields of the form you can use the model property of the form.
Even if model not explicitly set (see section below) each form has an underlying model which is automatically generated::

	// single field
	$form->model->set('email', 'some@email.com');

	// or multiple fields
	$form->model->set([
		'name'	=> 'John',
		'email' => 'some@email.com'
	]);

Form also relies on a ``atk4\ui\FormLayout`` class and displays fields through
decorators defined at ``atk4\ui\FormField``. See dedicated documentation for:

 - :php:class:`FormLayout::Generic`
 - :php:class:`FormField::Generic`

The rest of this chapter will focus on Form mechanics, such as submission,
integration with front-end, integration with Model, error handling etc.

Usage with Model
----------------

A most common use of form is if you have a working Model (https://agile-data.readthedocs.io/en/develop/model.html)::

    // Form will automatically add a new user and save into the database
    $form = $app->add('Form');
    $form->setModel(new User($db));

The basic 2-line syntax will extract all the required logic from the Model including:

 - Fields defined for this Model will be displayed
 - Display of default values in the form
 - Depending on field type, a decorator will be selected from FormField/Generic
 - Using :php:class:`FormLayout::Columns` can make form more compact by splitting it into columns
 - Field captions, placeholders, hints and other elements defined in Field::ui are respected (https://agile-data.readthedocs.io/en/develop/fields.html#Field::$ui)
 - Fields that are not editable by default will not appear on the form (https://agile-data.readthedocs.io/en/develop/fields.html#Field::isEditable)
 - Field typecasting will be invoked such as for converting dates
 - Reference fields (https://agile-data.readthedocs.io/en/develop/references.html?highlight=hasOne#hasone-reference) displayed as DropDown
 - Booleans are displayed as checkboxes but stored as defined by the model field
 - Mandatory and Required fields will be visually highlighted (https://agile-data.readthedocs.io/en/develop/fields.html?highlight=required#Field::$mandatory)
 - Validation will be performed and errors will appear on the form (NEED LINK)
 - Unless you specify a submission handler, form will save the model ``User`` into ``$db`` on successful submission.

All of the above works auto-magically, but you can tweak it even more:

 - Provide custom submission handler
 - Specify which fields and in which order to display on the form
 - Override labels, decorator classes
 - Group fields or use custom layout template
 - Mix standard model fields with your own
 - Add JS Actions around fields
 - Split up form into multiple tabs

If your form is NOT associated with a model, then Form will automatically create a :php:class:`ProxyModel`
and associate it with your Form. As you add fields, they will also be added into ProxyModel.

Extensions
----------

Starting with Agile UI 1.3 Form has a stable API and we expect to introduce some extensions like:

 - Captcha decorator
 - File Upload field (see https://github.com/atk4/filestore)
 - Multi-record form
 - Multi-tab form

If you develop such a feature please let me know so that I can include it in the documentation
and give you credit.

Layout and Fields
=================

Although Form extends the View class, fields are not added into Form directly but rather use
a View layout for it in order to create their html element. In other words, layout attached to the form
is responsible of rendering html for fields.

When Form is first initialized, it will provide and set a default Generic layout within the form.
Then using :php:meth:`Form::addField()` will rely on that layout to add field View to it and render it properly.
You may also supply your own layout when creating your form.

Form layout may contain sub layouts. Each sub layout being just another layout view, it is possible
to nest them, by adding other sub layout to them. This allows for great flexibility on how to place
your fields within Form.

Each sub layout may also contain specific section layout like Accordion, Columns or Tabs.

More on Form layout and sub layout below.

Adding Fields
=============

.. php:method:: addField($name, $decorator = null, $field = null)

Create a new field on a form::

    $form = $app->add('Form');
    $form->addField('email');
    $form->addField('gender', ['DropDown', 'values'=>['Female', 'Male']]);
    $form->addField('terms', null, ['type'=>'boolean', 'caption'=>'Agree to Terms & Conditions']);

Create a new field on a form using Model does not require you to describe each field.
Form will rely on Model Field Definition and UI meta-values to decide on the best way to handle
specific field type::

    $form = $app->add('Form');
    $form->setModel(new User($db), ['email', 'gender', 'terms']);

Field Decorator does not have to be added directly into the form. You can use a separate
:php:class:`FormLayout` or even a regular view. Simply specify property :php:meth:`FormField\Generic::$form`::

    $myview = $form->add(['View', 'defaultTemplate'=>'./mytemplate.html']);
    $myview->add(['FormField\Dropdown', 'form'=>$form]);

.. php:method:: addFields($fields)

Similar to :php:meth:`Form::addField()`, but allows to add multiple fields in one method call.

    $form = $app->add('Form');
    $form->addFields([
        'email',
        ['gender', ['DropDown', 'values'=>['Female', 'Male']]],
        ['terms', null, ['type'=>'boolean', 'caption'=>'Agree to Terms & Conditions']],
    ]);

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
is documented here: https://agile-data.readthedocs.io/en/develop/fields.html

Form uses a small UI component to visualize HTML input fields associated with
the respective Model Field. We call this object "Field Decorator". All field
decorators extend from class :php:class:`FormField::Generic`.

Agile UI comes with at least the following decorators:

- Input (also extends into Line, Password, Hidden)
- DropDown
- CheckBox
- Radio
- Calendar
- Radio
- Money

For some examples see: https://ui.agiletoolkit.org/demos/form3.php

Field Decorator can be passed to ``addField`` using 'string', :php:ref:`seed` or 'object'::

    $form->addField('accept_terms', 'CheckBox');
    $form->addField('gender', ['DropDown', 'values'=>['Female', 'Male']]);

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

    $form->addField('accept_terms', 'CheckBox', 'Accept Terms & Conditions');
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
(https://agile-data.readthedocs.io/en/develop/model.html#Model::addField)

If field already exist inside model, then values of $field will be merged into
existing field properties. This example make email field mandatory for the form::

    $form = $app->add('Form');
    $form->setModel(new User($db), false);

    $form->addField('email', null, ['required'=>true]);

addField into Existing Model
----------------------------

If your form is using a model and you add additional field, then it will automatically
be marked as "never_persist" (https://agile-data.readthedocs.io/en/develop/fields.html#Field::$never_persist).

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

Sometimes you may wonder - should you pass decorator class ('CheckBox') or
a data field type (['type' => 'boolean']);

It is always recommended to use data field type, because it will take care of type-casting
for you. Here is an example with date::

    $form = $app->add('Form');
    $form->addField('date1', null, ['type'=>'date']);
    $form->addField('date2', ['Calendar', 'type'=>'date']);

    $form->onSubmit(function($form) {
        echo 'date1 = '.print_r($form->model['date1'], true).' and date2 = '.print_r($form->model['date2'], true);
    });

Field ``date1`` is defined inside a :php:class:`ProxyModel` as a date field and will
be automatically converted into DateTime object by Persistence typecasting.

Field ``date2`` has no data type, do not confuse with ui type=>date pass as second argument for Calendar field,
and therefore Persistence typecasting will not modify it's value and it's stored inside model as a string.

The above code result in the following output::

    date1 = DateTime Object ( [date] => 2017-09-03 00:00:00 .. ) and date2 = September 3, 2017

Seeding Decorator from Model
----------------------------

In large projects you most likely won't be setting individual fields for each Form. Instead
you can simply use ``setModel()`` to populate all defined fields inside a model. Form does
have a pretty good guess about Decorator based on their data field type, but what if you want to
use a custom decorator?

This is where ``$field->ui`` comes in (https://agile-data.readthedocs.io/en/develop/fields.html#Field::$ui).

You can specify ``'ui'=>['form' => $decorator_seed]`` when defining your model field inside your Model::

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

Without the data 'type' property, now the calendar selection will be stored as text.

Using setModel()
----------------

Although there were many examples above for the use of setModel() this method
needs a bit more info:

.. php:attr:: model

.. php:method:: setModel($model, [$fields])

Associate fields with existing model object and import all editable fields
in the order in which they were defined inside model's init() method.

You can specify which fields to import and their order by simply listing
field names through second argument.

Specifying "false" or empty array as a second argument will import no fields,
so you can then use :php:meth:`Form::addField` to import fields individually.

Note that :php:meth:`Form::setModel` also delegate adding field to the form layout
by using `Form->layout->setModel()` internally.

See also: https://agile-data.readthedocs.io/en/develop/fields.html#Field::isEditable

Using setModel() on a sub layout
--------------------------------

You may add field to sub layout directly using setModel method on the sub layout itself.::

    $f = $app->add('Form');
    $f->setModel($m, false);

    $sub_layout = $f->layout->addSubLayout();
    $sub_layout->setModel($m, ['first_name', 'last_name']);


When using setModel() on a sub layout to add fields per sub layout instead of entire layout,
make sure you pass false as second argument when setting the model on the Form itself, like above.
Otherwise all model fields will be automatically added in Forms main layout and you will not be
able to add them again in sub-layouts.

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
POST data to submit itself and will re-use the querystring, so you can also safely
use any GET arguments for passing record $id. You may also perform model load after
record association. This gives the benefit of not loading any other fields, unless
they're marked as System (https://agile-data.readthedocs.io/en/develop/fields.html#Field::$system),
see https://agile-data.readthedocs.io/en/develop/model.html?highlight=onlyfields#Model::onlyFields::

    $form = $app->add('Form');
    $form->setModel(new User($db), ['email', 'name']);
    $form->model->load($current_user);

As before, field ``password`` will not be loaded from the database, but this time
using onlyFields restriction rather then `never_persist`.

Validating
----------

The topic of validation in web apps is quite extensive. You should start by reading what Agile Data
has to say about validation:
https://agile-data.readthedocs.io/en/develop/persistence.html#validation

Sometimes validation is needed when storing field value inside a model (e.g. setting boolean
to "blah") and sometimes validation should be performed only when storing model data into
the database.

Here are a few questions:

- If user specified incorrect value into field, can it be stored inside model and then
  re-displayed in the field again? If user must enter "date of birth" and he picks date
  in the future, should we reset field value or simply indicate error?

- If you have a multi-step form with complex logic, it may need to run validation before
  record status changes from "draft" to "submitted".

As far as form is concerned:

- Decorators must be able to parse entered values. For instance DropDown will make sure that
  value entered is one of the available values (by key)

- Form will rely on Agile Data Typecasting (https://agile-data.readthedocs.io/en/develop/typecasting.html)
  to load values from POST data and store them in model.

- Form submit handler will rely on ``Model::save()`` (https://agile-data.readthedocs.io/en/develop/persistence.html#Model::save)
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

.. php:method:: setApiConfing($config)

    Add additional parameters to Fomantic UI .api function which does the AJAX submission of the form.
For example, if you want the loading overlay at a different HTML element, you can define it with
$form->setApiConfig(['stateContext' => 'my-JQuery-selector']);
All available parameters can be found here: https://fomantic-ui.com/behaviors/api.html#/settings

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
:php:meth:`error()` or :php:meth:`success()` will help initialize those actions for your form.
Here is a code that can be used to output multiple errors at once. I intentionally didn't want
to group errors with a message about terms and conditions::

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


Form Layout and sub layout
--------------------------

As stated above, when you create a Form object and start adding fields through either :php:meth:`addField()`
or :php:meth:`setModel()`, they will appear one under each-other. This arrangement of fields as
well as display of labels and structure around the fields themselves is not done by a form,
but another object - "Form Layout". This object is responsible for the field flow, presence
of labels etc.

.. php:method:: initLayout(FormLayout\Generic $layout)

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

Form group layout and sub layout
--------------------------------

Fields can be organized in groups, using method `addGroup()` or as sub section using `addSubLayout()` method.

Using group
-----------

Group will create a sub layout for you where fields added to the group will be placed side by side in one line
and where you can setup specific width for each field.

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

    $gr = $f->addGroup(['width'=>'two']);
    $gr->addFields(['city', 'country']);

You can also use in-line form groups. Fields in such a group will display header on the left and
the error messages appearing on the right from the field::

    $gr = $f->addGroup(['Name', 'inline'=>true]);
    $gr->addField('first_name', ['width'=>'eight']);
    $gr->addField('middle_name', ['width'=>'three', 'disabled'=>true]);
    $gr->addField('last_name', ['width'=>'five']);

Using Sub layout
----------------

There are four specific sub layout views that you can add to your existing form layout: Generic, Accordion, Tabs and Columns.

Generic sub layout is simply another layout view added to your existing form layout view. You add fields
the same way as you would do for :php:class:`FormLayout\Generic`.

Sub layout section like Accordion, Tabs or Columns will create layout specific section where you can
organize fields in either accordion, tabs or columns.

The following example will show how to organize fields using regular sub layout and accordion sections::

    $f = $app->add('Form');
    $f->setModel($m, false);

    $sub_layout = $f->layout->addSubLayout('Generic');

    $sub_layout->add(['Header', 'Accordion Section in Form']);
    $sub_layout->setModel($m, ['name']);

    $accordion_layout = $f->layout->addSubLayout('Accordion');

    $a1 = $accordion_layout->addSection('Section 1');
    $a1->setModel($m, ['iso', 'iso3']);

    $a2 = $accordion_layout->addSection('Section 2');
    $a2->setModel($m, ['numcode', 'phonecode']);

In the example above, we first add a Generic sub layout to the existing layout of the form where one
field, name, is added to this sub layout.

Then we add another layout to the form layout. In this case it's specific Accordion layout. This sub layout
is further separated in two accordion sections and fields are added to each section:
`$a1->setModel($m, ['iso', 'iso3']);` and `$a2->setModel($m, ['numcode', 'phonecode']);`

Sub layout gives you greater control on how to display fields within your form. For more examples on
sub layouts please visit demo page: https://github.com/atk4/ui/blob/develop/demos/form-section.php

Fomantic UI modifiers
---------------------

There are many other classes Fomantic UI allow you to use on a form. The next code will produce
form inside a segment (outline) and will make fields appear smaller::

    $f = new \atk4\ui\Form(['small segment']));

For further styling see documentation on :php:class:`View`.

Mandatory and Required Fields
=============================

ATK Data has two field flags - "mandatory" and "required". Because ATK Data works with PHP
values, the values are defined like this:

 - mandatory = value of the field must not be null.
 - required = value of the field must not be empty. (see is_empty())

Form changes things slightly, because it does not allow user to enter NULL values. For
example - string (or unspecified type) fields will contain empty string if are not
entered (""). Form will never set NULL value for them.

When working with other types such as numeric values and dates - empty string is not
a valid number (or date) and therefore will be converted to NULL.

So in most cases you'd want "required=true" flag set on your ATK Data fields. For
numeric field, if zero must be a permitted entry, use "mandatory=true" instead.


Conditional Form
================

.. php:method:: setFieldsDisplayRules()

So far we had to present form with a set of fields while initializing. Sometimes
you would want to hide/display fields while user enters the data.

The logic is based around passing a declarative array::

    $form = $app->add('Form');
    $form->addField('phone1');
    $form->addField('phone2');
    $form->addField('phone3');
    $form->addField('phone4');

    $form->setFieldsDisplayRules([
        'phone2'=>['phone1'=>'empty'],
        'phone3'=>['phone1'=>'empty', 'phone2'=>'empty'],
        'phone4'=>['phone1'=>'empty', 'phone2'=>'empty', 'phone3'=>'empty'],
    ]);

The only catch here is that "empty" means "not empty". ATK UI relies on rules defined by FomanticUI
https://fomantic-ui.com/behaviors/form.html, so you can use any of the conditions there.

Here is a more advanced example::

    $f_sub = $app->add('Form');
    $f_sub->addField('name');
    $f_sub->addField('subscribe', ['CheckBox', 'Subscribe to weekly newsletter', 'toggle']);
    $f_sub->addField('email');
    $f_sub->addField('gender', ['Radio'], ['enum'=>['Female', 'Male']])->set('Female');
    $f_sub->addField('m_gift', ['DropDown', 'caption'=>'Gift for Men', 'values' => ['Beer Glass', 'Swiss Knife']]);
    $f_sub->addField('f_gift', ['DropDown', 'caption'=>'Gift for Women', 'values' => ['Wine Glass', 'Lipstick']]);

    // Show email and gender when subscribe is checked.

    // Show m_gift when gender is exactly equal to 'male' and subscribe is checked.
    // Show f_gift when gender is exactly equal to 'female' and subscribe is checked.

    $f_sub->setFieldsDisplayRules([
       'email' => ['subscribe' => 'checked'],
       'gender'=> ['subscribe' => 'checked'],
       'm_gift'=> ['gender' => 'isExactly[Male]', 'subscribe' => 'checked'],
       'f_gift'=> ['gender' => 'isExactly[Female]', 'subscribe' => 'checked'],
    ]);

You may also define multiple conditions for the field to be visible if you wrap them inside and array::


    $f_sub = $app->add('Form');
    $f_dog->addField('race', ['Line']);
    $f_dog->addField('age');
    $f_dog->addField('hair_cut', ['DropDown', 'values' => ['Short', 'Long']]);

    // Show 'hair_cut' when race contains the word 'poodle' AND age is between 1 and 5
    // OR
    // Show 'hair_cut' when race contains exactly the word 'bichon'
    $f_dog->setFieldsDisplayRules([
        'hair_cut' => [['race' => 'contains[poodle]', 'age'=>'integer[1..5]'], ['race' => 'isExactly[bichon]']],
    ]);

Hiding / Showing group of field
-------------------------------

Instead of defining rules for fields individually you can hide/show entire group::

    $f_group = $app->add(['Form', 'segment']);
    $f_group->add(['Label', 'Work on form group too.', 'top attached'], 'AboveFields');

    $g_basic = $f_group->addGroup(['Basic Information']);
    $g_basic->addField('first_name', ['width' => 'eight']);
    $g_basic->addField('middle_name', ['width' => 'three']);
    $g_basic->addField('last_name', ['width' => 'five']);

    $f_group->addField('dev', ['CheckBox', 'caption' => 'I am a developper']);

    $g_code = $f_group->addGroup(['Check all language that apply']);
    $g_code->addField('php', ['CheckBox']);
    $g_code->addField('js', ['CheckBox']);
    $g_code->addField('html', ['CheckBox']);
    $g_code->addField('css', ['CheckBox']);

    $g_other = $f_group->addGroup(['Others']);
    $g_other->addField('language', ['width' => 'eight']);
    $g_other->addField('favorite_pet', ['width' => 'four']);

    //To hide-show group simply select a field in that group.
    // Show group where 'php' belong when dev is checked.
    // Show group where 'language' belong when dev is checked.

    $f_group->setGroupDisplayRules([
        'php' => ['dev' => 'checked'],
        'language'=>['dev'=>'checked']
    ]);
