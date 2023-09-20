:::{php:namespace} Atk4\Ui
:::

(form-control)=

# Form Controls

:::{php:class} Form\Control
:::

Agile UI dedicates a separate namespace for the Form Controls. Those are
quite simple components that present themselves as input controls: line, select, checkbox.

## Relationship with Form

All Form Control Decorators can be integrated with {php:class}`Form` which will
facilitate collection and processing of data in a form. Form Control decorators can also
be used as stand-alone controls.

### Stand-alone use

:::{php:method} set()
:::

:::{php:method} jsInput()
:::

Add any form control to your application like this:

```
$control = Line::addTo($app);
```

You can set default value and interact with a form control using JavaScript:

```
$control->set('hello world');

$button = \Atk4\Ui\Button::addTo($app, ['check value']);
$button->on('click', new \Atk4\Ui\Js\JsExpression('alert(\'control value is: \' + [])', [$control->jsInput()->val()]));
```

When used stand-alone, Form\Controls will produce a basic HTML (I have omitted id=):

```
<div class="ui input">
    <input name="line" type="text" placeholder="" value="hello world">
</div>
```

### Using in-form

:::{php:attr} form
Form Control objects can be associated with a Form object.
:::

Form Control can also be used inside a form like this:

```
$form = \Atk4\Ui\Form::addTo($app);
$control = $form->addControl('name', new \Atk4\Ui\Form\Control\Line());
```

If you execute this example, you'll notice that Field now has a label, it uses full width of the
page and the following HTML is now produced:

```
<div class="field">
    <label for="atk_admin_form_generic_name_input">Name</label>
    <div id="atk_admin_form_generic_name" class="ui input">
        <input name="name" type="text" placeholder="" id="atk_admin_form_generic_name_input" value="">
    </div>
</div>
```

The markup that surronds the button which includes Label and formatting is produced by
{php:class}`Form\Layout`, which does draw some of the information from the Form Control
itself.

### Using in Form Layouts

Form may have multiple Form Layouts and that's very useful if you need to split up form
into multiple Tabs or detach form control groups or even create nested layouts:

```
$form = \Atk4\Ui\Form::addTo($app);
$tabs = \Atk4\Ui\Tabs::addTo($form, [], ['AboveControls']);
\Atk4\Ui\View::addTo($form, ['ui' => 'divider'], ['AboveControls']);

$formPage = Form\Layout::addTo($tabs->addTab('Basic Info'), ['form' => $form]);
$formPage->addControl('name', new \Atk4\Ui\Form\Control\Line());

$formPage = Form\Layout::addTo($tabs->addTab('Other Info'), ['form' => $form]);
$formPage->addControl('age', new \Atk4\Ui\Form\Control\Line());

$form->onSubmit(function (Form $form) {
    return $form->model->get('name') . ' has age ' . $form->model->get('age');
});
```

This is further explained in documentation for {php:class}`Form\Layout` class,
however if you do plan on adding your own form control types, it's important that you extend it
properly:

- Generic (abstract, extends View) - Use this if form control is NOT based on `<input>`
- Input (abstract, extends Generic) - Easiest since it already implements `<input>` and various
  ways to attach button to the input with markup of Fomantic-UI form control.

### Hints

:::{php:attr} hint
:::

When Form Control appears in a Form, then you can specify a Hint also. It appears below the form control and
although it intends to be "extra info" or "extra help" due to current limitation of Fomantic-UI
the only way we can display hint is using a gray bubble. In the future version of Agile UI we
will update to use a more suitable form control.

Hint can be specified either inside Form Control decorator seed or inside the Field::ui attribute:

```
$form->addControl('title', [], ['values' => ['Mr', 'Mrs', 'Miss'], 'hint' => 'select one']);

$form->addControl('name', ['hint' => 'Full Name Only']);
```

Text will have HTML characters escaped. You may also specify hint value as an object:

```
$form->addControl('name', ['hint' => new \Atk4\Ui\Text(
    'Click <a href="https://example.com/" target="_blank">here</a>'
)]);
```

or you can inject a view with a custom template:

```
$form->addControl('name', ['hint' => ['template' => new \Atk4\Ui\HtmlTemplate(
    'Click <a href="https://example.com/" target="_blank">here</a>'
)]]);
```

### Read only and disabled form controls

:::{php:attr} readOnly
:::

Read only form controls can be seen in form, can be focused and will be submitted, but we don't allow to
change their value.

:::{php:attr} disabled
:::

Disabled form controls can be seen in form, cannot be focused and will not be submitted. And of course we
don't allow to change their value. Disabled form controls are used for read only model fields for example.

## Relationship with Model

In the examples above, we looked at how to create Form Control Decorator object explicitly.
The most common use-case in large application is the use with Models. You would need a model, such as
`Country` model as well as
[Persistence $db](https://atk4-data.readthedocs.io/en/develop/persistence.html):

```
class Country extends \Atk4\Data\Model
{
    public $table = 'country';

    protected function init(): void
    {
        parent::init();

        $this->addField('name', ['actual' => 'nicename', 'required' => true, 'type' => 'string']);
        $this->addField('sys_name', ['actual' => 'name', 'system' => true]);

        $this->addField('iso', ['caption' => 'ISO', 'required' => true, 'type' => 'string']);
        $this->addField('iso3', ['caption' => 'ISO3', 'required' => true, 'type' => 'string']);
        $this->addField('numcode', ['caption' => 'ISO Numeric Code', 'type' => 'integer', 'required' => true]);
        $this->addField('phonecode', ['caption' => 'Phone Prefix', 'type' => 'integer']);
    }
}
```

To create a form, the following is sufficient:

```
$form = \Atk4\Ui\Form::addTo($app);
$form->setModel(new Country($db);
```

The above will populate fields from model into the form automatically. You can use second
argument to {php:meth}`Form::setModel()` to indicate which fields to display
or rely on {ref}`field_visibility`.

When Form controls are populated, then {php:meth}`Form::controlFactory` is
consulted to make a decision on how to translate
[Model Field](https://atk4-data.readthedocs.io/en/develop/fields.html) into
Form Control Decorator.

The rules are rather straightforward but may change in future versions of Agile UI:

- if [enum](https://atk4-data.readthedocs.io/en/develop/fields.html#Field::$enum) is defined, use {php:class}`Form\Control\Dropdown`
- consult {php:attr}`Form::$typeToDecorator` property for type-to-seed association
- type=password will use {php:class}`Form\Control\Password`

You always have an option to explicitly specify which field you would like to use:

```
$model->addField('long_text', ['ui' => ['rorm' => \Atk4\Ui\Form\Control\TextArea::class]]);
```

It is recommended however, that you use type when possible, because types will be universally supported
by all components:

```
$model->addField('long_text', ['type' => 'text']);
```

:::{note}
All forms will be associated with a model. If form is not explicitly linked with a model, it will create
a ProxyModel and all form controls will be created automatically in that model. As a result, all Form Control Decorators
will be linked with Model Fields.
:::

### Link to Model Field

:::{php:attr} field
:::

Form decorator defines $field property which will be pointing to a field object of a model, so technically
the value of the field would be read from `$decorator->entityField->get()`.

## Line Input Form control

:::{php:class} Form\Control\Input
Implements View for presenting Input form controls. Based around https://fomantic-ui.com/elements/input.html.
:::

Similar to other views, Input has various properties that you can specify directly
or inject through constructor. Those properties will affect the look of the input
element. For example, `icon` property:

:::{php:attr} icon
:::

:::{php:attr} iconLeft
Adds icon into the input form control. Default - `icon` will appear on the right, while `leftIcon`
will display icon on the left.
:::

Here are few ways to specify `icon` to an Input/Line:

```
// compact
Line::addTo($page, ['icon' => 'search']);

// type-hinting friendly
$line = new \Atk4\Ui\Form\Control\Line();
$line->icon = 'search';
$page->add($line);

// using class factory
Line::addTo($page, ['icon' => 'search']);
```

The 'icon' property can be either string or a View. The string is for convenience and will
be automatically substituted with `new Icon($icon)`. If you wish to be more specific
and pass some arguments to the icon, there are two options:

```
// compact
$line->icon = ['search', 'class.big' => true];

// type-hinting friendly
$line->icon = new Icon('search');
$line->icon->addClass('big');
```

To see how Icon interprets `new Icon(['search', 'class.big' => true])`, refer to {php:class}`Icon`.

:::{note}
View's constructor will map received arguments into object properties, if they are defined
or addClass() if not. See {php:meth}`View::setProperties`.
:::

:::{php:attr} placeholder
Will set placeholder property.
:::

:::{php:attr} loading
Set to "left" or "right" to display spinning loading indicator.
:::

:::{php:attr} label
:::

:::{php:attr} labelRight
Convert text into {php:class}`Label` and insert it into the form control.
:::

:::{php:attr} action
:::

:::{php:attr} actionLeft
Convert text into {php:class}`Button` and insert it into the form control.
:::

To see various examples of form controls and their attributes see `demos/form-control/`.

### Integration with Form

When you use {php:meth}`Form::addControl()` it will create 'Form Control Decorator'

### JavaScript on Input

:::{php:method} jsInput([$event, [$otherChain]])
:::

Input class implements method jsInput which is identical to {php:meth}`View::js`, except
that it would target the INPUT element rather then the whole form control:

```
$control->jsInput(true)->val(123);
```

### onChange event

:::{php:method} onChange($expression)
:::

It's preferable to use this short-hand version of on('change', 'input', $expression) method.
$expression argument can be JS expression or PHP callback function.

```
// simple string
$f1 = $form->addControl('f1');
$f1->onChange(\Atk4\Ui\Js\JsExpression('console.log(\'f1 changed\')'));

// callback
$f2 = $form->addControl('f2');
$f2->onChange(function () {
    return new \Atk4\Ui\Js\JsExpression('console.log(\'f2 changed\')');
});

// Calendar form control - wraps in function call with arguments date, text and mode
$c1 = $form->addControl('c1', new \Atk4\Ui\Form\Control\Calendar(['type' => 'date']));
$c1->onChange(\Atk4\Ui\Js\JsExpression('console.log(\'c1 changed: \' + date + \', \' + text + \', \' + mode)'));
```

## Dropdown

:::{php:class} Form\Control\Dropdown
:::

Dropdown uses Fomantic-UI Dropdown (https://fomantic-ui.com/modules/dropdown.html). A Dropdown can be used in two ways:

1. Set a Model to $model property. The Dropdown will render all records of the model that matches the model's conditions.
2. You can define $values property to create custom Dropdown items.

### Usage with a Model

A Dropdown is not used as default Form Control decorator (`$model->hasOne()` uses {php:class}`Form\Control\Lookup`), but in your Model, you can define that
UI should render a Field as Dropdown. For example, this makes sense when a `hasOne()` relationship only has a very limited amount (like 20)
of records to display. Dropdown renders all records when the paged is rendered, while Lookup always sends an additional request to the server.
{php:class}`Form\Control\Lookup` on the other hand is the better choice if there is lots of records (like more than 50).

To render a model field as Dropdown, use the ui property of the field:

```
$model->addField('someField', ['ui' => ['form' => [\Atk4\Ui\Form\Control\Dropdown::class]]]);
```

### Customizing how entities are displayed in Dropdown

As default, Dropdown will use the `$model->idField` as value, and `$model->titleField` as title for each menu item.
If you want to customize how a record is displayed and/or add an icon, Dropdown has the {php:meth}`Form::renderRowFunction()` to do this.
This function is called with each model record and needs to return an array:

```
$dropdown->renderRowFunction = function (Model $record) {
    return [
        'value' => $record->idField,
        'title' => $record->getTitle() . ' (' . $record->get('subtitle') . ')',
    ];
}
```

You can also use this function to add an Icon to a record:

```
$dropdown->renderRowFunction = function (Model $record) {
    return [
        'value' => $record->idField,
        'title' => $record->getTitle() . ' (' . $record->get('subtitle') . ')',
        'icon' => $record->get('value') > 100 ? 'money' : 'coins',
    ];
}
```

If you'd like to even further adjust How each item is displayed (e.g. complex HTML and more model fields), you can extend the Dropdown class and create your own template with the complex HTML:

```
class MyDropdown extends \Atk4\Ui\Dropdown
{
    public $defaultTemplate = 'my_dropdown.html';

    /**
     * used when a custom callback is defined for row rendering. Sets
     * values to item template and appends it to main template
     */
    protected function _addCallBackRow($row, $key = null)
    {
        $res = ($this->renderRowFunction)($row, $key);
        $this->_tItem->set('value', (string) $res['value']);
        $this->_tItem->set('title', $res['title']);
        $this->_tItem->set('someOtherField', $res['someOtherField]);
        $this->_tItem->set('someOtherField2', $res['someOtherField2]);
        // add item to template
        $this->template->dangerouslyAppendHtml('Item', $this->_tItem->render());
    }
}
```

With the according renderRowFunction:

```
function (Model $record) {
    return [
        'value' => $record->getId(),
        'title' => $record->getTitle,
        'icon' => $record->value > 100 ? 'money' : 'coins',
        'someOtherField' => $record->get('SomeOtherField'),
        'someOtherField2' => $record->get('SomeOtherField2'),
    ];
}
```

Of course, the tags `value`, `title`, `icon`, `someOtherField` and `someOtherField2` need to be set in my_dropdown.html.

### Usage with $values property

If not used with a model, you can define the Dropdown values in $values array. The pattern is value => title:

```
$dropdown->values = [
    'decline' => 'No thanks',
    'postprone' => 'Maybe later',
    'accept' => 'Yes, I want to!',
];
```

You can also define an Icon right away:

```
$dropdown->values = [
    'tag' => ['Tag', 'icon' => 'tag'],
    'globe' => ['Globe', 'icon' => 'globe'],
    'registered' => ['Registered', 'icon' => 'registered'],
    'file' => ['File', 'icon' => 'file'],
];
```

If using $values property, you can also use the {php:meth}`Form::renderRowFunction()`, though there usually is no need for it.
If you use it, use the second parameter as well, its the array key:

```
function (string $value, $key) {
    return [
        'value' => $key,
        'title' => strtoupper($value),
    ];
}
```

### Dropdown Settings

There's a bunch of settings to influence Dropdown behaviour.

:::{php:attr} empty
:::

Define a string for the empty option (no selection). Standard is non-breaking space symbol.

:::{php:attr} dropdownOptions
:::

Here you can pass an array of Fomantic-UI dropdown options (https://fomantic-ui.com/modules/dropdown.html#/settings) e.g.:

```
$dropdown = new Dropdown(['dropdownOptions' => [
    'selectOnKeydown' => false,
]]);
```

:::{php:attr} multiple
:::

If set to true, multiple items can be selected in Dropdown. They will be sent comma separated (value1,value2,value3) on form submit.

By default Dropdown will save values as comma-separated string value in data model, but it also supports model fields with array type.
See this example from Model class init method:

```
$exprModel = $this->ref('Expressions');
$this->addField('expressions', [
    'type' => 'json',
    'required' => true,
    'ui' => [
        'form' => [
            \Atk4\Ui\Form\Control\Dropdown::class,
            'multiple' => true,
            'model' => $exprModel,
        ],
        'table' => [
            'Labels',
            'values' => $exprModel->getTitles(),
        ],
    ],
]);
```

## DropdownCascade

:::{php:class} Form\Control\DropdownCascade
:::

DropdownCascade input are extend from Dropdown input. They rely on `cascadeFrom` and `reference` property.
For example, it could be useful when you need to narrow a product selection base on a category and a sub category.
User will select a Category from a list, then sub category input will automatically load sub category values based on
user category selection. Same with product list values based on sub category selection and etc.

:::{php:attr} cascadeFrom
:::

This property represent an input form control, mostly another Dropdown or DropdownCascade form control.
The list values of this form control will be build base off the selected value of cascadeFrom input.

:::{php:attr} reference
:::

This property represent a model hasMany reference and should be an hasMany reference of the cascadeFrom input model.
In other word, the model that will generated list value for this dropdown input is an hasMany reference of the cascadeFrom
input model.

Assume that each data model are defined and model Category has many Sub-Category and Sub-Category has many Product:

```
$form = \Atk4\Ui\Form::addTo($app);
$form->addControl('category_id', [Dropdown::class, 'model' => new Category($db)]);
$form->addControl('sub_category_id', [DropdownCascade::class, 'cascadeFrom' => 'category_id', 'reference' => Category::hinting()->fieldName()->SubCategories]);
$form->addControl('product_id', [DropdownCascade::class, 'cascadeFrom' => 'sub_category_id', 'reference' => SubCategory::hinting()->fieldName()->Products]);
```

## Lookup

:::{php:class} Form\Control\Lookup
:::

Lookup input is also based on Fomantic-UI dropdown module but with ability to dynamically request server for data it's
data value.

When clicking on a Lookup form control, it will send a query to server and start building it's list value. Typing into the
input form control will reload list value according to search criteria.
