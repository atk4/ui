:::{php:namespace} Atk4\Ui
:::

(form)=

# Forms

:::{php:class} Form
:::

One of the most important components of ATK UI is the "Form". Class {php:class}`Form`
implements the following 4 major features:

- Form Rendering using Fomantic-UI HTML/CSS (https://fomantic-ui.com/collections/form.html):

  :::{image} images/form.png
  :::

- Form controls are automatically populated based on your existing data model with special treatment
  for date/time, auto-complete and even file upload.

- Loading data from database and storing it back. Any persistence (SQL, NoSQL) supported by
  ATK Data (https://atk4-data.readthedocs.io/en/develop/persistence.html) can be used.

- Support for Events and Actions on form controls, buttons and form callback. ({ref}`js`) Automatic
  execution of PHP-based Submit Handler passing all the collected data ({ref}`callback`)

So if looking for a PHP Form class, ATK Form has the most complete implementation which does
not require to fall-back into HTML / JS, perform any data conversion, load / store data and
implement any advanced interactions such as file uploads.

## Basic Usage

It only takes 2 PHP lines to create a fully working form:

```
$form = Form::addTo($app);
$form->addControl('email');
```

The form component can be further tweaked by setting a custom callback handler
directly in PHP:

```
$form->onSubmit(function (Form $form) {
    // implement subscribe here

    return "Subscribed " . $form->model->get('email') . " to newsletter.";
});
```

Form is a composite component and it relies on other components to render parts
of it. Form uses {php:class}`Button` that you can tweak to your liking:

```
$form->buttonSave->set('Subscribe');
$form->buttonSave->icon = 'mail';
```

or you can tweak it when you create form like this:

```
$form = Form::addTo($app, ['buttonSave' => [null, 'Subscribe', 'icon' => 'mail']]);
```

To set the default values in the form controls you can use the model property of the form.
Even if model not explicitly set (see section below) each form has an underlying model which is automatically generated:

```
// single field
$form->model->set('email', 'some@email.com');

// or multiple fields
$form->model->set([
    'name' => 'John',
    'email' => 'some@email.com',
]);
```

Form also relies on a `\Atk4\Ui\Form::Layout` class and displays form controls through
decorators defined at `\Atk4\Ui\Form::Control`. See dedicated documentation for:

- {php:class}`Form\Layout`
- {php:class}`Form\Control`

To tweak the UI properties of an form control input use `setInputAttr()` (and not the surrounding `<div>` as `setAttr()`
would do). Here is how to set the HTML "maxlength" attribute on the generated input field:

```
$form = \Atk4\Ui\Form::addTo($this);
$form->setModel($model);
$form->getControl('name')->setInputAttr('maxlength', 20);
```

The rest of this chapter will focus on Form mechanics, such as submission,
integration with front-end, integration with Model, error handling etc.

### Usage with Model

A most common use of form is if you have a working Model (https://atk4-data.readthedocs.io/en/develop/model.html):

```
// Form will automatically add a new user and save into the database
$form = Form::addTo($app);
$form->setModel(new User($db));
```

The basic 2-line syntax will extract all the required logic from the Model including:

- Fields defined for this Model will be displayed
- Display of default values in the form
- Depending on the field type, a form control will be selected from Form\Control namespace
- Using {php:class}`Form\Layout\Columns` can make form more compact by splitting it into columns
- Form control captions, placeholders, hints and other elements defined in Field::ui are respected (https://atk4-data.readthedocs.io/en/develop/fields.html#Field::$ui)
- Fields that are not editable by default will not appear on the form (https://atk4-data.readthedocs.io/en/develop/fields.html#Field::isEditable)
- Field typecasting will be invoked such as for converting dates
- Reference fields (https://atk4-data.readthedocs.io/en/develop/references.html?highlight=hasOne#hasone-reference) displayed as Dropdown
- Booleans are displayed as checkboxes but stored as defined by the model field
- Not-nullable and Required fields will have form controls visually highlighted (https://atk4-data.readthedocs.io/en/develop/fields.html?highlight=required#Field::$nullable)
- Validation will be performed and errors will appear on the form (NEED LINK)
- Unless you specify a submission handler, form will save the model `User` into `$db` on successful submission.

All of the above works auto-magically, but you can tweak it even more:

- Provide custom submission handler
- Specify which form controls and in which order to display on the form
- Override labels, form control classes
- Group form controls or use custom layout template
- Mix standard model fields with your own
- Add JS Actions around fields
- Split up form into multiple tabs

If your form is NOT associated with a model, then Form will automatically create a {php:class}`ProxyModel`
and associate it with your Form. As you add form controls respective fields will also be added into ProxyModel.

### Extensions

Starting with Agile UI 1.3 Form has a stable API and we expect to introduce some extensions like:

- Captcha form control
- File Upload form control (see https://github.com/atk4/filestore)
- Multi-record form

If you develop such a feature please let me know so that I can include it in the documentation
and give you credit.

## Layout and Form Controls

Although Form extends the View class, controls are not added into Form directly but rather use
a View layout for it in order to create their HTML element. In other words, layout attached to the form
is responsible of rendering HTML for fields.

When Form is first initialized, it will provide and set a default Generic layout within the form.
Then using {php:meth}`Form::addControl()` will rely on that layout to add form control to it and render it properly.
You may also supply your own layout when creating your form.

Form layout may contain sub layouts. Each sub layout being just another layout view, it is possible
to nest them, by adding other sub layout to them. This allows for great flexibility on how to place
your form controls within Form.

Each sub layout may also contain specific section layout like Accordion, Columns or Tabs.

More on Form layout and sub layout below.

## Adding Controls

:::{php:method} addControl($name, $decorator = [], $field = [])
:::

Create a new control on a form:

```
$form = Form::addTo($app);
$form->addControl('email');
$form->addControl('gender', [\Atk4\Ui\Form\Control\Dropdown::class, 'values' => ['Female', 'Male']]);
$form->addControl('terms', [], ['type' => 'boolean', 'caption' => 'Agree to Terms & Conditions']);
```

Create a new control on a form using Model does not require you to describe each control.
Form will rely on Model Field Definition and UI meta-values to decide on the best way to handle
specific field type:

```
$form = Form::addTo($app);
$form->setModel(new User($db), ['email', 'gender', 'terms']);
```

Form control does not have to be added directly into the form. You can use a separate
{php:class}`Form\Layout` or even a regular view. Simply specify property {php:meth}`Form\Control::$form`:

```
$myview = View::addTo($form, ['defaultTemplate' => './mytemplate.html']);
Form\Control\Dropdown::addTo($myview, ['form' => $form]);
```

### Adding new controls

First argument to addControl is the name of the form control. You cannot have multiple controls
with the same name.

If a field exists inside associated model, then model field definition will be used as
a base, otherwise you can specify field definition through 3rd argument. I explain
that below in more detail.

You can specify first argument `null` in which case control will be added without
association with field. This will not work with regular fields, but you can add
custom control such as CAPTCHA, which does not really need association with a
field.

### Form Control

To avoid term miss-use, we use "Field" to refer to `\Atk4\Data\Field`. This class
is documented here: https://atk4-data.readthedocs.io/en/develop/fields.html

Form uses a small UI component to visualize HTML input fields associated with
the respective Model Field. We call this object "Form Control". All form
controls extend from class {php:class}`Form\Control`.

Agile UI comes with at least the following form controls:

- Input (also extends into Line, Password, Hidden)
- Dropdown
- Checkbox
- Radio
- Calendar
- Radio
- Money

For some examples see: https://ui.atk4.org/demos/form3.php

Field Decorator can be passed to `addControl` using 'string', {php:ref}`seed` or 'object':

```
$form->addControl('accept_terms', [\Atk4\Ui\Form\Control\Checkbox::class]);
$form->addControl('gender', [\Atk4\Ui\Form\Control\Dropdown::class, 'values' => ['Female', 'Male']]);

$calendar = new \Atk4\Ui\Form\Control\Calendar();
$calendar->type = 'tyme';
$calendar->options['ampm'] = true;
$form->addControl('time', $calendar);
```

For more information on default form controls as well as examples on how to create
your own see documentation on {php:class}`Form\Control`.

:::{php:method} controlFactory(\Atk4\Data\Field $field, $defaults = [])
:::

If form control class is not specified (`null`) then it will be determined from
the type of the Data control with `controlFactory` method.

### Data Field

Data field is the 3rd argument to `Form::addControl()`.

There are 3 ways to define Data form control using 'string', 'json' or 'object':

```
$form->addControl('accept_terms', [\Atk4\Ui\Form\Control\Checkbox::class], 'Accept Terms & Conditions');
$form->addControl('gender', [], ['enum' => ['Female', 'Male']]);

class MyBoolean extends \Atk4\Data\Field
{
    public string $type = 'boolean';
    public ?array $enum = ['N', 'Y'];
}
$form->addControl('test2', [], new MyBoolean());
```

String will be converted into `['caption' => $string]` a short way to give
field a custom label. Without a custom label, Form will clean up the name (1st
argument) by replacing '_' with spaces and uppercasing words (accept_terms
becomes "Accept Terms")

Specifying array will use the same syntax as the 2nd argument for `\Atk4\Data\Model::addField()`.
(https://atk4-data.readthedocs.io/en/develop/model.html#Model::addField)

If field already exist inside model, then values of $field will be merged into
existing field properties. This example make email field mandatory for the form:

```
$form = Form::addTo($app);
$form->setModel(new User($db), []);

$form->addControl('email', [], ['required' => true]);
```

### addControl into Form with Existing Model

If your form is using a model and you add an additional control, then the underlying model field will be created but it will
be set as "neverPersist" (https://atk4-data.readthedocs.io/en/develop/fields.html#Field::$neverPersist).

This is to make sure that data from custom form controls wouldn't go directly into the database. Next
example displays a registration form for a User:

```
class User extends \Atk4\Data\Model
{
    public $table = 'user';

    protected function init(): void
    {
        parent::init();

        $this->addField('email');
        $this->addFiled('password');
    }
}

$form = Form::addTo($app);
$form->setModel(new User($db));

// add password verification field
$form->addControl('password_verify', [\Atk4\Ui\Form\Control\Password::class], 'Type password again');
$form->addControl('accept_terms', [], ['type' => 'boolean']);

// submit event
$form->onSubmit(function (Form $form) {
    if ($form->model->get('password') != $form->model->get('password_verify')) {
        return $form->jsError('password_verify', 'Passwords do not match');
    }

    if (!$form->model->get('accept_terms')) {
        return $form->jsError('accept_terms', 'Read and accept terms');
    }

    $form->model->save(); // will only store email / password

    return $form->jsSuccess('Thank you. Check your email now');
});
```

### Field Type vs Form Control

Sometimes you may wonder - should you pass form control class (Form\Control\Checkbox) or
a data field type (['type' => 'boolean']);

It is always recommended to use data field type, because it will take care of type-casting
for you. Here is an example with date:

```
$form = Form::addTo($app);
$form->addControl('date1', [], ['type' => 'date']);
$form->addControl('date2', [\Atk4\Ui\Form\Control\Calendar::class, 'type' => 'date']);

$form->onSubmit(function (Form $form) {
    echo 'date1 = ' . print_r($form->model->get('date1'), true) . ' and date2 = ' . print_r($form->model->get('date2'), true);
});
```

Field `date1` is defined inside a {php:class}`ProxyModel` as a date field and will
be automatically converted into DateTime object by Persistence typecasting.

Field `date2` has no data type, do not confuse with ui type => date pass as second argument for Calendar field,
and therefore Persistence typecasting will not modify it's value and it's stored inside model as a string.

The above code result in the following output:

```
date1 = DateTime Object ( [date] => 2017-09-03 00:00:00 .. ) and date2 = September 3, 2017
```

### Seeding Form Control from Model

In large projects you most likely won't be setting individual form controls for each Form. Instead
you can simply use `setModel()` to populate all form controls from fields defined inside a model. Form does
have a pretty good guess about form control decorator based on the data field type, but what if you want to
use a custom decorator?

This is where `$field->ui` comes in (https://atk4-data.readthedocs.io/en/develop/fields.html#Field::$ui).

You can specify `'ui' => ['form' => $decoratorSeed]` when defining your model field inside your Model:

```
class User extends \Atk4\Data\Model
{
    public $table = 'user';

    protected function init(): void
    {
        parent::init();

        $this->addField('email');
        $this->addField('password');

        $this->addField('birth_year', ['type' => 'date', 'ui' => ['type' => 'month']);
    }
}
```

The seed for the UI will be combined with the default overriding {php:attr}`Form\Control\Calendar::$type`
to allow month/year entry by the Calendar extension, which will then be saved and
stored as a regular date. Obviously you can also specify decorator class:

```
$this->addField('birth_year', ['ui' => [\Atk4\Ui\Form\Control\Calendar::class, 'type' => 'month']);
```

Without the data 'type' property, now the calendar selection will be stored as text.

### Using setModel()

Although there were many examples above for the use of setModel() this method
needs a bit more info:

:::{php:attr} model
:::

:::{php:method} setModel($model, [$fields])
:::

Associate form controls with existing model object and import all editable fields
in the order in which they were defined inside model's init() method.

You can specify which form controls to import from model fields and their order by simply listing model
field names in an array as a second argument.

Specifying "false" or empty array as a second argument will import no model fields as form controls,
so you can then use {php:meth}`Form::addControl` to import form controls from model fields individually.

Note that {php:meth}`Form::setModel` also delegates adding form control to the form layout
by using `Form->layout->setModel()` internally.

See also: https://atk4-data.readthedocs.io/en/develop/fields.html#Field::isEditable

### Using setModel() on a sub layout

You may add form controls to sub layout directly using setModel method on the sub layout itself.:

```
$form = Form::addTo($app);
$form->setModel($model, []);

$subLayout = $form->layout->addSubLayout();
$subLayout->setModel($model, ['first_name', 'last_name']);
```

When using setModel() on a sub layout to add controls per sub layout instead of entire layout,
make sure you pass false as second argument when setting the model on the Form itself, like above.
Otherwise all model fields will be automatically added in Forms main layout and you will not be
able to add them again in sub-layouts.

### Loading Values

Although you can set form control values individually using `$form->model->set('field', $value)`
it's always nicer to load values for the database. Given a `User` model this is how
you can create a form to change profile of a currently logged user:

```
$user = new User($db);
$user->getField('password')->neverPersist = true; // ignore password field
$user = $user->load($currentUserId);

// display all fields (except password) and values
$form = Form::addTo($app);
$form->setModel($user);
```

Submitting this form will automatically store values back to the database. Form uses
POST data to submit itself and will re-use the query string, so you can also safely
use any GET arguments for passing record $id. You may also perform model load after
record association. This gives the benefit of not loading any other fields, unless
they're marked as System (https://atk4-data.readthedocs.io/en/develop/fields.html#Field::$system),
see https://atk4-data.readthedocs.io/en/develop/model.html?highlight=onlyfields#Model::setOnlyFields:

```
$form = Form::addTo($app);
$form->setModel((new User($db))->load($currentUserId), ['email', 'name']);
```

As before, field `password` will not be loaded from the database, but this time
using onlyFields restriction rather then `neverPersist`.

### Validating

The topic of validation in web apps is quite extensive. You should start by reading what Agile Data
has to say about validation:
https://atk4-data.readthedocs.io/en/develop/persistence.html#validation

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

- Decorators must be able to parse entered values. For instance Dropdown will make sure that
  value entered is one of the available values (by key)

- Form will rely on Agile Data Typecasting (https://atk4-data.readthedocs.io/en/develop/typecasting.html)
  to load values from POST data and store them in model.

- Form submit handler will rely on `Model::save()` (https://atk4-data.readthedocs.io/en/develop/persistence.html#Model::save)
  not to throw validation exception.

- Form submit handler will also interpret use of {php:meth}`Form::jsError` by displaying errors that
  do not originate inside Model save logic.

Example use of Model's validate() method:

```
class Person extends \Atk4\Data\Model
{
    public $table = 'person';

    protected function init(): void
    {
        parent::init();

        $this->addField('name', ['required' => true]);
        $this->addField('surname');
        $this->addField('gender', ['enum' => ['M', 'F']]);
    }

    public function validate(): array
    {
        $errors = parent::validate();

        if ($this->get('name') === $this->get('surname')) {
            $errors['surname'] = 'Your surname cannot be same as the name';
        }

        return $errors;
    }
}
```

We can now populate form controls based around the data fields defined in the model:

```
Form::addTo($app)
    ->setModel(new Person($db));
```

This should display a following form:

```
$form->addControl('terms', ['type' => 'boolean', 'ui' => ['caption' => 'Accept Terms and Conditions']]);
```

### Form Submit Handling

:::{php:method} onSubmit($callback)
Specify a PHP callback that will be executed on successful form submission.
:::

:::{php:method} jsError($field, $message)
Create and return {php:class}`Js\JsChain` action that will indicate error on a form control.
:::

:::{php:method} jsSuccess($title, [$subTitle])
Create and return {php:class}`Js\JsChain` action, that will replace form with a success message.
:::

:::{php:method} setApiConfig($config)
Add additional parameters to Fomantic-UI .api function which does the AJAX submission of the form.
:::

For example, if you want the loading overlay at a different HTML element, you can define it with:

```
$form->setApiConfig(['stateContext' => 'my-JQuery-selector']);
```

All available parameters can be found here: https://fomantic-ui.com/behaviors/api.html#/settings

:::{php:attr} successTemplate
Name of the template which will be used to render success message.
:::

To continue with the example, a new Person record can be added into the database
but only if they have also accepted terms and conditions. An onSubmit handler
that would perform the check can be defined displaying error or success messages:

```
$form->onSubmit(function (Form $form) {
    if (!$form->model->get('terms')) {
        return $form->jsError('terms', 'You must accept terms and conditions');
    }

    $form->model->save();

    return $form->jsSuccess('Registration Successful', 'We will call you soon.');
});
```

Callback function can return one or multiple JavaScript actions. Methods such as
{php:meth}`Form::jsError()` or {php:meth}`Form::jsSuccess()` will help initialize those actions for your form.
Here is a code that can be used to output multiple errors at once. Errors were intentionally not grouped
with a message about failure to accept of terms and conditions:

```
$form->onSubmit(function (Form $form) {
    $errors = [];

    if (!$form->model->get('name')) {
        $errors[] = $form->jsError('name', 'Name must be specified');
    }

    if (!$form->model->get('surname')) {
        $errors[] = $form->jsError('surname', 'Surname must be specified');
    }

    if ($errors) {
        return new \Atk4\Ui\Js\JsBlock($errors);
    }

    if (!$form->model->get('terms')) {
        return $form->jsError('terms', 'You must accept terms and conditions');
    }

    $form->model->save();

    return $form->jsSuccess('Registration Successful', 'We will call you soon.');
});
```

So far Agile UI / Agile Data does not come with a validation library but
it supports usage of 3rd party validation libraries.

Callback function may raise exception. If Exception is based on `\Atk4\Core\Exception`,
then the parameter "field" can be used to associate error with specific field:

```
throw (new \Atk4\Core\Exception('Sample Exception'))
    ->addMoreInfo('field', 'surname');
```

If 'field' parameter is not set or any other exception is generated, then error will not be
associated with a field. Only the main Exception message will be delivered to the user.
Core Exceptions may contain some sensitive information in parameters or back-trace, but those
will not be included in response for security reasons.

### Form Layout and Sub-layout

As stated above, when a Form object is created and form controls are added through either {php:meth}`Form::addControl()`
or {php:meth}`Form::setModel()`, the form controls will appear one under each-other. This arrangement of form controls as
well as display of labels and structure around the form controls themselves is not done by a form,
but another object - "Form Layout". This object is responsible for the form control flow, presence
of labels etc.

:::{php:method} initLayout(Form\Layout $layout)
Sets a custom Form\Layout object for a form. If not specified then form will automatically
use Form\Layout class.
:::

:::{php:attr} layout
Current form layout object.
:::

:::{php:method} addHeader($header)
Adds a form header with a text label. Returns View.
:::

:::{php:method} addGroup($header)
Creates a sub-layout, returning new instance of a {php:class}`Form\Layout` object. You
can also specify a header.
:::

### Form Control Group Layout and Sub-layout

Controls can be organized in groups, using method `Form::addGroup()` or as sub section using `Form\Layout::addSubLayout()` method.

### Using Group

Group will create a sub layout for you where form controls added to the group will be placed side by side in one line
and where you can setup specific width for each field.

My next example will add multiple controls on the same line:

```
$form->setModel(new User($db), []); // will not populate any form controls automatically

$group = $form->addGroup('Customer');
$group->addControl('name');
$group->addControl('surname');

$group = $form->addGroup('Address');
$group->addControl('street');
$group->addControl('city');
$group->addControl('country');
```

By default grouped form controls will appear with fixed width. To distribute space you can either specify
proportions manually:

```
$group = $form->addGroup('Address');
$group->addControl('address', ['width' => 'twelve']);
$group->addControl('code', ['Post Code', 'width' => 'four']);
```

or you can divide space equally between form controls. Header is also omitted for this group:

```
$group = $form->addGroup(['width' => 'two']);
$group->addControl('city');
$group->addControl('country');
```

You can also use in-line form groups. Controls in such a group will display header on the left and
the error messages appearing on the right from the control:

```
$group = $form->addGroup(['Name', 'inline' => true]);
$group->addControl('first_name', ['width' => 'eight']);
$group->addControl('middle_name', ['width' => 'three', 'disabled' => true]);
$group->addControl('last_name', ['width' => 'five']);
```

### Using Sub-layout

There are four specific sub layout views that you can add to your existing form layout: Generic, Accordion, Tabs and Columns.

Generic sub layout is simply another layout view added to your existing form layout view. You add fields
the same way as you would do for {php:class}`Form\Layout`.

Sub layout section like Accordion, Tabs or Columns will create layout specific section where you can
organize fields in either accordion, tabs or columns.

The following example will show how to organize fields using regular sub layout and accordion sections:

```
$form = Form::addTo($app);
$form->setModel($model, []);

$subLayout = $form->layout->addSubLayout([\Atk4\Ui\Form\Layout\Section::class]);

Header::addTo($subLayout, ['Accordion Section in Form']);
$subLayout->setModel($model, ['name']);

$accordionLayout = $form->layout->addSubLayout([\Atk4\Ui\Form\Layout\Section\Accordion::class]);

$a1 = $accordionLayout->addSection('Section 1');
$a1->setModel($model, ['iso', 'iso3']);

$a2 = $accordionLayout->addSection('Section 2');
$a2->setModel($model, ['numcode', 'phonecode']);
```

In the example above, we first add a Generic sub layout to the existing layout of the form where one form
control ('name') is added to this sub layout.

Then we add another layout to the form layout. In this case it's specific Accordion layout. This sub layout
is further separated in two accordion sections and form controls are added to each section:

```
$a1->setModel($model, ['iso', 'iso3']);
$a2->setModel($model, ['numcode', 'phonecode']);
```

Sub layout gives you greater control on how to display form controls within your form. For more examples on
sub layouts please visit demo page: https://github.com/atk4/ui/blob/develop/demos/form-section.php

### Fomantic-UI Modifiers

There are many other classes Fomantic-UI allow you to use on a form. The next code will produce
form inside a segment (outline) and will make form controls appear smaller:

```
$form = new \Atk4\Ui\Form(['class.small segment' => true]));
```

For further styling see documentation on {php:class}`View`.

## Not-Nullable and Required Fields

ATK Data has two field flags - "nullable" and "required". Because ATK Data works with PHP
values, the values are defined like this:

- nullable = value of the field can be null.
- required = value of the field must not be empty/false/zero, null is empty too.

Form changes things slightly, because it does not allow user to enter NULL values. For
example - string (or unspecified type) fields will contain empty string if are not
entered (""). Form will never set NULL value for them.

When working with other types such as numeric values and dates - empty string is not
a valid number (or date) and therefore will be converted to NULL.

So in most cases you'd want "required=true" flag set on your ATK Data fields. For
numeric field, if zero must be a permitted entry, use "nullable=false" instead.

## Conditional Form

:::{php:method} setControlsDisplayRules()
:::

So far we had to present form with a set of form controls while initializing. Sometimes
you would want to hide/display controls while user enters the data.

The logic is based around passing a declarative array:

```
$form = Form::addTo($app);
$form->addControl('phone1');
$form->addControl('phone2');
$form->addControl('phone3');
$form->addControl('phone4');

$form->setControlsDisplayRules([
    'phone2' => ['phone1' => 'empty'],
    'phone3' => ['phone1' => 'empty', 'phone2' => 'empty'],
    'phone4' => ['phone1' => 'empty', 'phone2' => 'empty', 'phone3' => 'empty'],
]);
```

The only catch here is that "empty" means "not empty". ATK UI relies on rules defined by Fomantic-UI
https://fomantic-ui.com/behaviors/form.html, so you can use any of the conditions there.

Here is a more advanced example:

```
$form = Form::addTo($app);
$form->addControl('name');
$form->addControl('subscribe', [\Atk4\Ui\Form\Control\Checkbox::class, 'Subscribe to weekly newsletter', 'class.toggle' => true]);
$form->addControl('email');
$form->addControl('gender', [\Atk4\Ui\Form\Control\Radio::class], ['enum' => ['Female', 'Male']])->set('Female');
$form->addControl('m_gift', [\Atk4\Ui\Form\Control\Dropdown::class, 'caption' => 'Gift for Men', 'values' => ['Beer Glass', 'Swiss Knife']]);
$form->addControl('f_gift', [\Atk4\Ui\Form\Control\Dropdown::class, 'caption' => 'Gift for Women', 'values' => ['Wine Glass', 'Lipstick']]);

// show email and gender when subscribe is checked
// show m_gift when gender = 'male' and subscribe is checked
// show f_gift when gender = 'female' and subscribe is checked

$form->setControlsDisplayRules([
    'email' => ['subscribe' => 'checked'],
    'gender' => ['subscribe' => 'checked'],
    'm_gift' => ['gender' => 'isExactly[Male]', 'subscribe' => 'checked'],
    'f_gift' => ['gender' => 'isExactly[Female]', 'subscribe' => 'checked'],
]);
```

You may also define multiple conditions for the form control to be visible if you wrap them inside and array:

```
$form = Form::addTo($app);
$form->addControl('race', [\Atk4\Ui\Form\Control\Line::class]);
$form->addControl('age');
$form->addControl('hair_cut', [\Atk4\Ui\Form\Control\Dropdown::class, 'values' => ['Short', 'Long']]);

// show 'hair_cut' when race contains the word 'poodle' AND age is between 1 and 5
// OR
// show 'hair_cut' when race contains exactly the word 'bichon'
$form->setControlsDisplayRules([
    'hair_cut' => [['race' => 'contains[poodle]', 'age' => 'integer[1..5]'], ['race' => 'isExactly[bichon]']],
]);
```

### Hiding / Showing group of field

Instead of defining rules for form controls individually you can hide/show entire group:

```
$form = Form::addTo($app, ['class.segment' => true]);
Label::addTo($form, ['Work on form group too.', 'class.top attached' => true], ['AboveControls']);

$groupBasic = $form->addGroup(['Basic Information']);
$groupBasic->addControl('first_name', ['width' => 'eight']);
$groupBasic->addControl('middle_name', ['width' => 'three']);
$groupBasic->addControl('last_name', ['width' => 'five']);

$form->addControl('dev', [\Atk4\Ui\Form\Control\Checkbox::class, 'caption' => 'I am a developer']);

$groupCode = $form->addGroup(['Check all language that apply']);
$groupCode->addControl('php', [\Atk4\Ui\Form\Control\Checkbox::class]);
$groupCode->addControl('js', [\Atk4\Ui\Form\Control\Checkbox::class]);
$groupCode->addControl('html', [\Atk4\Ui\Form\Control\Checkbox::class]);
$groupCode->addControl('css', [\Atk4\Ui\Form\Control\Checkbox::class]);

$groupOther = $form->addGroup(['Others']);
$groupOther->addControl('language', ['width' => 'eight']);
$groupOther->addControl('favorite_pet', ['width' => 'four']);

// to hide-show group simply select a field in that group
// show group where 'php' belong when dev is checked
// show group where 'language' belong when dev is checked

$form->setGroupDisplayRules([
    'php' => ['dev' => 'checked'],
    'language' => ['dev' => 'checked'],
]);
```

:::{todo}
MOVE THIS TO SEPARATE FILE
:::

:::{php:class} Form\Layout
Renders HTML outline encasing form controls.
:::

:::{php:attr} form
Form layout objects are always associated with a Form object.
:::

:::{php:method} addControl()
Same as {php:meth}`Form::addControl()` but will place a form control inside this specific layout
or sub-layout.
:::
