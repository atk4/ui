:::{php:namespace} Atk4\Ui
:::

(type-presentation)=

# Formatters vs Decorators

This chapter describes a common technique used by various components that wish to preserve
extensible nature when dealing with used-defined types. Reading this chapter will also help
you understand some of the thinking behind major decisions when designing the type system.

When looking into the default money field in Agile UI, which does carry amount, but not
the currency, there are a number of considerations when dealing with the field. The first
important concept to understand is the distinction between data Presentation and Decoration.

- Data Presentation: displaying value of the data in a different format, e.g. 123,123.00 vs 123.123,00
- Data Decoration: adding currency symbol or calendar icon.

Agile UI believes that presentation must be consistent throughout the system. A monetary
field will use same format on the {php:class}`Form`, {php:class}`Table` and even inside a
custom HTML template specified into generic {php:class}`View`.

When it comes to decoration, the method is very dependent on the context. A form may present
Calendar (DatePicker) or enable control icon to indicate currency.

Presentation in Agile Toolkit is handled by {php:class}`Persistence\Ui`.

Decoration is performed by helper classes, such as {php:class}`Form\Control\Calendar` or
{php:class}`Table\Column\Money`. The decorator is in control of the final output, so it can decide if
it uses the value from presentation or do some decoration on its own.

# Extending Data Types

If you are looking to add a new data type, such as "money + currency" combination, which would
allow user to specify both the currency and the monetary value, you should start by adding
support for a new type.

In the below steps, the #1 and #2 are a minimum to achieve. #3 and #4 will improve experience
of your integration.

1. Extend UI persistence and use your class in `$app->uiPersistence`.

   You need to define how to output your data as well as read it.

2. Try your new type with a standard Form control.

   The value you output should read and stored back correctly.
   This ensures that standard UI will work with your new data type.

3. Create your new decorator.

   Such as use drop-down to select currency from a pre-defined list inside your specific class
   while extending {php:class}`Form\Control\Input` class. Make sure it can interpret input correctly.
   The process is explained further down in this chapter.

4. Associate the types with your decorator.

   This happens in {php:meth}`Form::controlFactory` and {php:meth}`Table::decoratorFactory`.

For the third party add-ons it is only possible to provide decorators. They must rely on one of
the standard types, unless they also offer a dedicated model.

# Manually Specifying Decorators

When working with components, they allow to specify decorators manually, even if the type
of the field does not seem compatible:

```
$table->addColumn('field_name', new \Atk4\Ui\Table\Column\Password());

// or

$form->addControl('field_name', new \Atk4\Ui\Form\Control\Password());
```

Selecting the decorator is done in the following order:

- specified in second argument to UI `addColumn()` or `addControl()` (as shown above)
- specified using `ui` property of {php:class}`\Atk4\Data\Field`:

  ```
  $field->ui['form'] = new \Atk4\Ui\Form\Control\Password();
  ```

- fallback to {php:meth}`Form::controlFactory`

:::{note}
When talking about "fields": you need to know what kind of field you are talking about (Data or UI).
Both **models** (Data) as well as some **views** (UI: form) use fields. They are not the same.
Notably, Model field `ui` property contains flags like editable, visible and hidden,
which do have some impact on rendering, whereas UI field `ui` property (not used here)
designates the Fomantic-UI element to use.
:::

# Examples

Let's explore various use cases and how to properly deal with scenarios

## Display password in plain-text for Admin

Normally password is presented as asterisks on the Grid and Form. But what if you want to
show it without masking just for the admin? Change type in-line for the model field:

```
$model = new User($app->db);
$model->getField('password')->type = 'string';

$crud->setModel($model);
```

:::{note}
Changing element's type to string will certainly not perform any password encryption.
:::

## Hide account_number in specific Table

This is reverse scenario. Field `account_number` needs to be stored as-is but should be
hidden when presented. To hide it from Table:

```
$model = new User($app->db);

$table->setModel($model);
$model->addDecorator('account_number', new \Atk4\Ui\Table\Column\Password());
```

## Create a decorator for hiding credit card number

If you happen to store card numbers and you only want to display the last digits in tables,
yet make it available when editing, you could create your own {php:class}`Table\Column` decorator:

```
class Masker extends \Atk4\Ui\Table\Column
{
    public function getDataCellTemplate(\Atk4\Data\Field $field = null): string
    {
        return '**** **** **** {$mask}';
    }

    public function getHtmlTags(\Atk4\Data\Model $row, ?\Atk4\Data\Field $field): array
    {
        return [
            'mask' => substr($field->get($row), -4),
        ];
    }
}
```

If you are wondering, why I'm not overriding by providing HTML tag equal to the field name,
it's because this technique is unreliable due to ability to exclude HTML tags with
{php:attr}`Table::$useHtmlTags`.

## Display credit card number with spaces

If we always have to display card numbers with spaces, e.g. "1234 1234 1234 1234" but have
the database store them without spaces, then this is a data formatting task best done by
extending {php:class}`Persistence\Ui`:

```
class MyPersistence extends Persistence\Ui
{
    protected function _typecastSaveField(\Atk4\Data\Field $field, $value)
    {
        switch ($field->type) {
            case 'card':
                $parts = str_split($value, 4);

                return implode(' ', $parts);
        }

        return parent::_typecastSaveField($field, $value);
    }

    public function _typecastLoadField(\Atk4\Data\Field $field, $value)
    {
        switch ($field->type) {
            case 'card':
                return str_replace(' ', '', $value);
        }

        return parent::_typecastLoadField($field, $value);
    }
}

class MyApp extends App
{
    public function __construct(array $defaults = [])
    {
        $this->uiPersistence = new MyPersistence()

        parent::__construct($defaults);
    }
}
```

Now your 'card' type will work system-wide.
