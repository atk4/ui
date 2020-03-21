
.. _field:

=====================
Form Field Decorators
=====================

.. php:namespace:: atk4\ui\FormField

Agile UI dedicates a separate namespace for the Form Field Decorator. Those are
quite simple components that present themselves as input controls: line, select, checkbox.

Relationship with Form
======================

All Field Decorators can be integrated with :php:class:`atk4\\ui\\Form` which will
facilitate collection and processing of data in a form. Field decorators can also
be used as stand-alone controls.

Stand-alone use
---------------

.. php:method:: set()
.. php:method:: jsInput()

Add any field decorator to your application like this::

    $field = Line::addTo($app);

You can set default value and ineract with a field using JavaScript::

    $field->set('hello world');


    $button = \atk4\ui\Button::addTo($app, ['check value']);
    $button->on('click', new \atk4\ui\jsExpression('alert("field value is: "+[])', [$field->jsInput()->val()]));


When used stand-alone, FormFields will produce a basic HTML (I have omitted id=)::

    <div class="ui  input">
        <input name="line" type="text" placeholder="" value="hello world"/>
    </div>


Using in-form
-------------

Field can also be used inside a form like this::

    $form = \atk4\ui\Form::addTo($app);
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
:php:class:`atk4\\ui\\FormLayout\\Generic`, which does draw some of the information from the Field
itself.

Using in Form Layouts
---------------------

Form may have multiple Form Layouts and that's very useful if you need to split up form
into multiple Tabs or detach field groups or even create nested layouts::

    $form = \atk4\ui\Form::addTo($app);
    $tabs = \atk4\ui\Tabs::addTo($form, [], ['AboveFields']);
    \atk4\ui\View::addTo($form, ['ui'=>'divider'], ['AboveFields']);

    $form_page = Generic::addTo($tabs->addTab('Basic Info'), ['form'=>$form]);
    $form_page->addField('name', new \atk4\ui\FormField\Line());

    $form_page = Generic::addTo($tabs->addTab('Other Info'), ['form'=>$form]);
    $form_page->addField('age', new \atk4\ui\FormField\Line());

    $form->onSubmit(function($f) {  return $f->model['name'].' has age '.$f->model['age']; });

This is further explained in documentation for :php:class:`atk4\\ui\\FormLayout\\Generic` class,
however if you do plan on adding your own field types, it's important that you extend it
properly:

 - Generic (abstract, extends View) - Use this if field is NOT based on `<input>`
 - Input (abstract, extends Generic) - Easiest since it alrady implements `<input>` and various
   ways to attach button to the input with markup of Fomantic UI field.

Hints
-----

.. php:attr: hint

When Field appears in a Form, then you can specify a Hint also. It appears below the field and
although it intends to be "extra info" or "extra help" due to current limitation of Fomantic UI
the only way we can display hint is using a gray bubble. In the future version of Agile UI we
will update to use a more suitable control.

Hint can be specified either inside field decorator seed or inside the Field::ui attribute::


    $form->addField('title', null, ['values'=>['Mr', 'Mrs', 'Miss'], 'hint'=>'select one']);

    $form->addField('name', ['hint'=>'Full Name Only']);

Text will have HTML characters escaped. You may also specify hint value as an object::

    $form->addField('name', ['hint'=>new \atk4\ui\Text(
        'Click <a href="https://example.com/" target="_blank">here</a>'
    )]);

or you can inject a view with a custom template::

    $form->addField('name', ['hint'=>['template'=>new \atk4\ui\Template(
        'Click <a href="https://example.com/" target="_blank">here</a>'
    )]]);

Read only and disabled fields
-----------------------------

.. php:attr: readonly

Read only fields can be seen in form, can be focused and will be submitted, but we don't allow to
change their value.

.. php:attr: disabled

Disabled fields can be  seend in form, can not be focused and will not be submitted. And of course we
don't allow to change their value. Disabled form fields are used for read only model fields for example.


Relationship with Model
=======================

In the examples above, we looked at how to create Field Decorator object explicitly.
The most common use-case in large application is the use with Models. You would need a model, such as
`Country` model (see demos/database.php) as well as
`Persistence $db <https://agile-data.readthedocs.io/en/develop/persistence.html>`_::

    class Country extends \atk4\data\Model
    {
        public $table = 'country';

        public function init()
        {
            parent::init();
            $this->addField('name', ['actual' => 'nicename', 'required' => true, 'type' => 'string']);
            $this->addField('sys_name', ['actual' => 'name', 'system' => true]);

            $this->addField('iso', ['caption' => 'ISO', 'required' => true, 'type' => 'string']);
            $this->addField('iso3', ['caption' => 'ISO3', 'required' => true, 'type' => 'string']);
            $this->addField('numcode', ['caption' => 'ISO Numeric Code', 'type' => 'number', 'required' => true]);
            $this->addField('phonecode', ['caption' => 'Phone Prefix', 'type' => 'number']);
        }
    }

To create a form, the following is sufficient::

    $form = \atk4\ui\Form::addTo($app);
    $form->setModel(new Country($db);

The above will populate fields from model into the form automatically. You can use second
argument to :php:meth:`atk4\ui\Form::setModel()` to indicate which fields to display
or rely on :ref:`field_visibility`.

When Form fields are populated, then :php:meth:`\atk4\ui\Form::_decoratorFactory` is
consulted to make a decision on how to translate
`Model Field <https://agile-data.readthedocs.io/en/develop/fields.html>`_ into
Form Field Decorator.

The rules are rather straightforward but may change in future versions of Agile UI:

 - if `enum <https://agile-data.readthedocs.io/en/develop/fields.html#Field::$enum>`_ is defined, use :php:class:`DropDown`
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

    Implements View for presenting Input fields. Based around https://fomantic-ui.com/elements/input.html.

Similar to other views, Input has various properties that you can specify directly
or inject through constructor. Those properties will affect the look of the input
element. For example, `icon` property:

.. php:attr: icon
.. php:attr: iconLeft

    Adds icon into the input field. Default - `icon` will appear on the right, while `leftIcon`
    will display icon on the left.

Here are few ways to specify `icon` to an Input::

    // compact
    Line::addTo($page, ['icon'=>'search']);

    // Type-hinting friendly
    $line = new \atk4\ui\FormField\Line();
    $line->icon='search';
    $page->add($line);

    // using class factory
    Line::addTo($page, [], [['icon'=>'search']]);

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

onChange event
--------------

.. php:method:: onChange($expression)

It's prefferable to use this short-hand version of on('change', 'input', $expression) method.
$expression argument can be string, jsExpression, array of jsExpressions or even PHP callback function.

    // simple string
    $f1 = $f->addField('f1');
    $f1->onChange('console.log("f1 changed")');

    // callback
    $f2 = $f->addField('f2');
    $f2->onChange(function(){return new \atk4\ui\jsExpression('console.log("f2 changed")');});

    // Calendar field - wraps in function call with arguments date, text and mode
    $c1 = $f->addField('c1', new \atk4\ui\FormField\Calendar(['type'=>'date']));
    $c1->onChange('console.log("c1 changed: "+date+","+text+","+mode)');





DropDown
========
DropDown uses Fomantic UI Dropdown (https://fomantic-ui.com/modules/dropdown.html). A DropDown can be used in two ways:
1) Set a Model to $model property. The DropDown will render all records of the model that matchs the model's conditions.
2) You can define $values property to create custom DropDown items.

Usage with a Model
------------------
A DropDown is not used as default Form Field decorator (`$model->hasOne()` uses :php:class:`Lookup`), but in your Model, you can define that
UI should render a Field as DropDown. For example, this makes sense when a `hasOne()` relationship only has a very limited amount (like 20)
of records to display. DropDown renders all records when the paged is rendered, while Lookup always sends an additional request to the server.
:php:class:`Lookup` on the other hand is the better choice if there is lots of records (like more than 50).

To render a model field as DropDown, use the ui property of the field::
    $model->addField('someField', ['ui' => ['form' =>['DropDown']]]);

..  Customizing how a Model's records are displayed in DropDown
As default, DropDown will use the `$model->id_field` as value, and `$model->title_field` as title for each menu item.
If you want to customize how a record is displayed and/or add an icon, DropDown has the :php:meth:`Form::renderRowFunction()` to do this.
This function is called with each model record and needs to return an array::
    $dropdown->renderRowFunction = function($record) {
        return [
            'value' => $record->id_field,
            'title' => $record->getTitle().' ('.$record->get('subtitle').')',
        ];
    }
    
You can also use this function to add an Icon to a record::
    $dropdown->renderRowFunction = function($record) {
        return [
            'value' => $record->id_field,
            'title' => $record->getTitle().' ('.$record->get('subtitle').')',
            'icon'  => $record->get('value') > 100 ? 'money' : 'coins',
        ];
    }

If you'd like to even further adjust How each item is displayed (e.g. complex HTML and more model fields), you can extend the DropDown class and create your own template with the complex HTML::

    class MyDropDown extends \atk4\ui\DropDown {
        
        public $defaultTemplate = 'my_dropdown.html';
        
        /*
         * used when a custom callback is defined for row rendering. Sets
         * values to item template and appends it to main template
         */
        protected function _addCallBackRow($row, $key = null) {
            $res = call_user_func($this->renderRowFunction, $row, $key);
            $this->_tItem->set('value', (string) $res['value']);
            $this->_tItem->set('title', $res['title']);
            $this->_tItem->set('someOtherField', $res['someOtherField]);
            $this->_tItem->set('someOtherField2', $res['someOtherField2]);
            //add item to template
            $this->template->appendHTML('Item', $this->_tItem->render());
       }
   }


With the according renderRowFunction::
    function($record) {
        return [
            'value' => $record->id,
            'title' => $record->getTitle,
            'icon'  => $record->value > 100 ? 'money' : 'coins',
            'someOtherField' => $record->get('SomeOtherField'),
            'someOtherField2' => $record->get('SomeOtherField2'),
        ];
    }

Of course, the tags `value`, `title`, `icon`, `someOtherField` and `SomeOtherField2` need to be set in my_dropdown.html.


Usage with $values property
------------------
If not used with a model, you can define the DropDown values in $values array. The pattern is value => title::
    $dropdown->values = [
        'decline'   => 'No thanks',
        'postprone' => 'Maybe later',
        'accept'    => 'Yes, I want to!',
    ];
    
You can also define an Icon right away::
     $dropdown->values = [
         'tag'        => ['Tag', 'icon' => 'tag icon'],
         'globe'      => ['Globe', 'icon' => 'globe icon'],
         'registered' => ['Registered', 'icon' => 'registered icon'],
         'file'       => ['File', 'icon' => 'file icon']
     ].

If using $values property, you can also use the :php:meth:`Form::renderRowFunction()`, though there usually is no need for it.
If you use it, use the second parameter as well, its the array key::
    function($row, $key) {
        return [
            'value' => $key,
            'title' => strtoupper($row),
        ];
    }


DropDown Settings
-----------------
There's a bunch of settings to influence DropDown behaviour:

.. php:attr:: empty
Define a string for the empty option (no selection). Standard is non-breaking space symbol.

.. php:attr:: isValueRequired 
Whether or not this dropdown requires a value. When set to true, $empty is shown on page load but is not selectable once a value has been choosen.

..php:attr:: dropdownOptions
Here you can pass an array of Fomantic UI dropdown options (https://fomantic-ui.com/modules/dropdown.html#/settings) e.g. ::
    $dropdown = new DropDown(['dropdownOptions' => [
        'selectOnKeydown' => false,
    ]]);
    
 ..php:attr:: isMultiple
 If set to true, multiple items can be selected in DropDown. They will be sent comma seperated (value1,value2,value3) on form submit.

By default DropDown will save values as comma-separated string value in data model, but it also supports model fields with array type.
See this example from Model class init method::
    $expr_model = $this->ref('Expressions');
    $this->addField('expressions', [
        'type'      => 'array',
        'required'  => true,
        'serialize' => 'json',
        'ui' => [
            'form' => [
                'DropDown',
                'isMultiple' => true,
                'model' => $expr_model,
            ],
            'table' => [
                'Labels',
                'values' => $expr_model->getTitles(),
            ],
        ],
    ]);

AutoComplete
============

.. php:class:: AutoComplete

