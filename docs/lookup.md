:::{php:namespace} Atk4\Ui
:::

(lookup)=

# Lookup Form Control

:::{php:class} Form\Control\Lookup
:::

Agile UI uses "Form\Control\Dropdown" by default on the form, but there is also implementation
for Lookup form control. Although they look similar, there are some differences:

- Lookup will perform callback to fetch values.
- Lookup can use callback to format options (both keys and values).
- Lookup can search in multiple fields.
- Lookup can use form current (dirty) values to apply dependency and limit options.
- Lookup can have multiple selection.
- Lookup has additional feature called "Plus"
- Lookup only works with models. Won't work for pre-defined value lists.

Lookup can be a drop-in replacement for Dropdown.

## Using Plus mode

In your application, it is handy if you can automatically add a missing "client" from the form
where you add an invoice. Lookup implements "Plus" mode which will automatically open a modal
form where you can enter new record details.

The form save will re-use the model of your auto-complete, so be sure to set() defaults and
addCondition()s:

```
$form->addControl('test', [\Atk4\Ui\Form\Control\Lookup::class, 'plus' => true])
    ->setModel(new Country($db));
```

## Specifying in Model

You can also specify that you prefer to use Lookup inside your model definition:

```
$model->hasOne('country_id', ['model' => [Country::class], 'ui' => ['form' => [\Atk4\Ui\Form\Control\Lookup::class]]]);
```

## Advanced Usage

You can do much more with Lookup form control by passing dropdown settings:

```
$form->addControl('test', [
    \Atk4\Ui\Form\Control\Lookup::class,
    'settings' => [
        'allowReselection' => true,
        'selectOnKeydown' => false,
        'onChange' => new \Atk4\Ui\Js\JsExpression('function (value, t, c) {
            if ($(this).data(\'value\') !== value) {
                $(this).parents(\'.form\').form(\'submit\');
                $(this).data(\'value\', value);
            }}'),
    ],
])->setModel(new Country($db));
```
