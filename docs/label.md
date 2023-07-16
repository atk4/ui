:::{php:namespace} Atk4\Ui
:::

(label)=

# Label

:::{php:class} Label
:::

Labels can be used in many different cases, either as a stand-alone objects, inside tables or inside
other components.

To see what possible classes you can use on the Label, see: https://fomantic-ui.com/elements/label.html.

Demo: https://ui.atk4.org/demos/label.php

## Basic Usage

First argument of constructor or first element in array passed to constructor will be the text that will
appear on the label:

```
$label = Label::addTo($app, ['hello world']);

// or

$label = new \Atk4\Ui\Label('hello world');
$app->add($label);
```

Label has the following properties:

:::{php:attr} icon
:::

:::{php:attr} iconRight
:::

:::{php:attr} image
:::

:::{php:attr} imageRight
:::

:::{php:attr} detail
:::

All the above can be string, array (passed to Icon, Image or View class) or an object.

## Icons

There are two properties (icon, iconRight) but you can set only one at a time:

```
Label::addTo($app, ['23', 'icon' => 'mail']);
Label::addTo($app, ['new', 'iconRight' => 'delete']);
```

You can also specify icon as an object:

```
Label::addTo($app, ['new', 'iconRight' => new \Atk4\Ui\Icon('delete')]);
```

For more information, see: {php:class}`Icon`

## Image

Image cannot be specified at the same time with the icon, but you can use PNG/GIF/JPG image on your label:

```
$img = $app->cdn['atk'] . '/logo.png';
Label::addTo($app, ['Coded in PHP', 'image' => $img]);
```

## Detail

You can specify "detail" component to your label:

```
Label::addTo($app, ['Number of lines', 'detail' => '33']);
```

## Groups

Label can be part of the group, but you would need to either use custom HTML template or
composition:

```
$group = View::addTo($app, ['class.blue tag' => true, 'ui' => 'labels']);
Label::addTo($group, ['$9.99']);
Label::addTo($group, ['$19.99', 'class.red tag' => true]);
Label::addTo($group, ['$24.99']);
```

## Combining classes

Based on Fomantic-UI documentation, you can add more classes to your labels:

```
$columns = Columns::addTo($app);

$c = $columns->addColumn();
$col = View::addTo($c, ['ui' => 'raised segment']);

// attach label to the top of left column
Label::addTo($col, ['Left Column', 'class.top attached' => true, 'icon' => 'book']);

// ribbon around left column
Label::addTo($col, ['Lorem', 'class.red ribbon' => true, 'icon' => 'cut']);

// add some content inside column
LoremIpsum::addTo($col, ['size' => 1]);

$c = $columns->addColumn();
$col = View::addTo($c, ['ui' => 'raised segment']);

// attach label to the top of right column
Label::addTo($col, ['Right Column', 'class.top attached' => true, 'icon' => 'book']);

// some content
LoremIpsum::addTo($col, ['size' => 1]);

// right bottom corner label
Label::addTo($col, ['Ipsum', 'class.orange bottom right attached' => true, 'icon' => 'cut']);
```

## Added labels into Table

You can even use label inside a table, but because table renders itself by repeating periodically, then
the following code is needed:

```
$table->onHook(\Atk4\Ui\Table\Column::HOOK_GET_HTML_TAGS, function (Table $table, Model $row) {
    if ($row->getId() == 1) {
        return [
            'name' => $table->getApp()->getTag('div', ['class' => 'ui ribbon label'], $row->get('name')),
        ];
    }
});
```

Now while $table will be rendered, if it finds a record with id=1, it will replace $name value with a HTML tag.
You need to make sure that 'name' column appears first on the left.
