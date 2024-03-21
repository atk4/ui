:::{php:namespace} Atk4\Ui
:::

# Purpose of the Seed

Agile UI relies on wide variety of objects. For example {php:class}`Button` relies on
{php:class}`Icon` object for its rendering. As a developer can create Icon object first,
then pass it to the button:

```
$icon = new Iron('book');
$button = new Button('Hello');
$button->icon = $icon;
```

or you can divert icon creation until later by using Array / String for {php:attr}`Button::$icon`
property:

```
$button = new Button('Hello');
$button->icon = 'book';
```

When you don't provide an object - string/array value is called "Seed" and will be used to
locate and load class dynamically just when it's needed.

Seed has many advantages:

- more readable and shorter syntax
- easier concept for new developers and non-developers
- can be namespace-specific
- can improve performance - not all seeds are initialized
- recursive syntax with property and constructor argument injection
- allow App logic to further enhance mechanics

## Growing Seed

To grow a seed you need a factory. Factory is a trait implemented in atk4/core and used by all
ATK UI classes:

```
$object = Factory::factory($seed);
```

In most cases you don't need to call factory yourself, methods which accept object/seed combinations
will do it for you:

```
Button::addTo($app);
// app will create instance of class \Atk4\Ui\Button
```

## Seed, Object and Render Tree

When calling {php:meth}`View::add()` not only your seed becomes an object, but it is also added to
the {ref}`render tree`.

# Seed Components

For more information about seeds, merging seeds, factories and namespaces, see https://atk4-core.readthedocs.io/.

The most important points of a seed such as this one:

```
$seed = [Button::class, 'hello', 'class.big red' => true, 'icon' => ['book', 'red']];
```

are:

- Element with index 0 is name of the class mapped into namespace \Atk4\Ui (by default).
- Elements with numeric indexes 'hello' and 'big red' are passed to constructor of Button
- Elements with named arguments are assigned to properties after invocation of constructor

## Alternative ways to use Seed

Some constructors may accept array as the first argument. It is also treated as a seed
but without class (because class is already set):

```
$button = new Button(['hello', 'class.big red' => true, 'icon' => ['book', 'class.red' => true]]);
```

It is alternatively possible to pass object as index 0 of the seed. In this case
constructor is already invoked, so passing numeric values is not possible, but
you still can pass some property values:

```
$seed = [new Button('hello', 'class.big red' => true), 'icon' => ['book', 'class.red' => true]];
```

## Additional cases

An individual object may add more ways to deal with seed. For example, when adding columns
to your Table you can specify seed for the decorator: {php:class}`Table\Column`:

```
$table->addColumn('salary', [\Atk4\Ui\Table\Column\Money::class]);

// or

$table->addColumn('salary', [\Atk4\Ui\Table\Column\Money::class]);

// or

$table->addColumn('salary', new \Atk4\Ui\Table\Column\Money());

// or

$table->addColumn('salary', [new \Atk4\Ui\Table\Column\Money()]);
```

Note that addColumn uses default namespace of `\Atk4\Ui\Table\Column` when seeding objects. Some
other methods that use seeds are:

- {php:meth}`Table::addColumn()`
- {php:meth}`Form::addControl()`
