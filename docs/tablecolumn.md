:::{php:namespace} Atk4\Ui
:::

(tablecolumn)=

# Table Column Decorators

Classes like {php:class}`Table` and {php:class}`Card` do not render their cell
contents themselves. Instead they rely on Column Decorator class to position content within the
cell.

This is in contrast to {php:class}`View` and {php:class}`Lister` which do not
use Table/Cell and therefore Column decorator is not required.

All column decorators in Agile UI have a base class {php:class}`Table\Column`. Decorators will often
look at the content of the associated value, for example {php:class}`Money` will add cell class `negative`
only if monetary value is less than zero. The value is taken from Model's Field object.

Column decorators can also function without associated value. {php:class}`Template` may have no
fields or perhaps display multiple field values. {php:class}`Action` displays interactive buttons
in the table. {php:class}`CheckBox` makes grid rows selectable. {php:class}`Ordering` displays
a draggable handle for re-ordering rows within the table.

A final mention is about {php:class}`Multiformat`, which is a column decorator that can swap-in
any other decorator based on condition. This allows you to change button [Archive] for active records,
but if record is already archived, use a template "Archived on {$archive_date}".

## Generic Column Decorator

:::{php:class} Table\Column
Generic description of a column for {php:class}`Table`
:::

Table object relies on a separate class: `\Atk4\Ui\Table\Column` to present most of the values. The goals
of the column object is to format anything around the actual values. The type = 'atk4_money' will result in
a custom formatting of the value, but will also require column to be right-aligned. To simplify this,
type = 'atk4_money' will use a different column class - {php:class}`Table\Column\Money`. There are several others,
but first we need to look at the generic column and understand it's base capabilities:

A class resposnible for cell formatting. This class defines 3 main methods that is used by the Table
when constructing HTML:

:::{php:method} getHeaderCellHtml(\Atk4\Data\Field $field): string
:::

Must respond with HTML for the header cell (`<th>`) and an appropriate caption. If necessary
will include "sorting" icons or any other controls that go in the header of the table.

:::{php:method} getTotalsCellHtml(\Atk4\Data\Field $field, $value): string
:::

Provided with the field and the value, format the cell for the footer "totals" row. Table
can rely on various strategies for calculating totals. See {php:meth}`Table::addTotals`.

:::{php:method} getDataCellHtml(\Atk4\Data\Field $field): string
:::

Provided with a field, this method will respond with HTML **template**. In order to keep
performance of Web Application at the maximum, Table will execute getDataCellHtml for all the
fields once. When iterating, a combined template will be used to display the values.

The template must not incorporate field values (simply because related model will not be
loaded just yet), but instead should resort to tags and syntax compatible with {php:class}`Template`.

A sample template could be:

```
<td><b>{$name}</b></td>
```

Note that the "name" here must correspond with the field name inside the Model. You may use
multiple field names to format the column:

```
<td><b>{$year}-{$month}-{$day}</b></td>
```

The above 3 methods define first argument as a field, however it's possible to define column
without a physical field. This makes sense for situations when column contains multiple field
values or if it doesn't contain any values at all.

Sometimes you do want to inject HTML instead of using row values:

:::{php:method} getHtmlTags($model, $field = null)
:::

Return array of HTML tags that will be injected into the row template. See
{php:ref}`table_html` for further example.

## Column Menus and Popups

Table column may have a menu as seen in https://ui.atk4.org/demos/tablecolumnmenu.php. Menu is added
into table column and can be linked with Popup or Menu.

### Basic Use

The simplest way to use Menus and Popups is through a wrappers: {php:meth}`Grid::addDropdown` and {php:meth}`Grid::addPopup`:

```
View::addTo($grid->addPopup('iso'))
    ->set('Grid column popup text');

// OR

$grid->addDropdown('name', ['Sort A-Z', 'Sort by Relevance'], function (string $item) {
    return $item;
});
```

Those wrappers will invoke methods {php:meth}`Table\Column::addDropdown` and {php:meth}`Table\Column::addPopup` for
a specified column, which are documented below.

### Popups

:::{php:method} addPopup()
:::

To create a popup, you need to get the column decorator object. This must be the first decorator, which
is responsible for rendering of the TH box. If you are adding column manually, {php:meth}`Table::addColumn()`
will return it. When using model, use {php:meth}`Table::getColumnDecorators`:

```
$table = Table::addTo($app, ['class.celled' => true]);
$table->setModel(new Country($app->db));

$nameColumn = $table->getColumnDecorators('name');
LoremIpsum::addTo($nameColumn[0]->addPopup());
```

:::{important}
If content of a pop-up is too large, it may not be possible to display it on-screen. Watch for warning.
:::

You may also use {php:meth}`Popup::set` method to dynamically load the content:

```
$table = Table::addTo($app, ['class.celled' => true]);
$table->setModel(new Country($app->db));

$nameColumn = $table->getColumnDecorators('name');
$nameColumn[0]->addPopup()->set(function (View $p) {
    HelloWorld::addTo($p);
});
```

### Dropdown Menus

:::{php:method} addDropdown()
:::

Menus will show item selection and will trigger a callback when user selects one of them:

```
$someColumn->addDropdown(['Change', 'Reorder', 'Update'], function (string $item) {
    return 'Title item: ' . $item;
});
```

## Decorators for data types

In addition to {php:class}`Table\Column`, Agile UI includes several column implementations.

### Link

:::{php:class} Table\Column\Link
:::

Put `<a href..` link over the value of the cell. The page property can be specified to constructor. There
are two usage patterns. With the first you can specify full URL as a string:

```
$table->addColumn('name', [\Atk4\Ui\Table\Column\Link::class, 'https://google.com/?q={$name}']);
```

The URL may also be specified as an array. It will be passed to App::url() which will encode arguments:

```
$table->addColumn('name', [\Atk4\Ui\Table\Column\Link::class, ['details', 'id' => 123, 'q' => $anything]]);
```

In this case even if `$anything = '{$name}'` the substitution will not take place for safety reasons. To
pass on some values from your model, use second argument to constructor:

```
$table->addColumn('name', [\Atk4\Ui\Table\Column\Link::class, ['details', 'id' => 123], ['q' => 'name']]);
```

### Money

:::{php:class} Table\Column\Money
:::

Helps decorating monetary values. Will align value to the right and if value is less than zero will also
use red text (td class "negative" for Fomantic-UI). The money cells are not wrapped.

For the actual number formatting, see {ref}`uiPersistence`

### Status

:::{php:class} Table\Column\Status
:::

Allow you to set highlight class and icon based on column value. This is most suitable for columns that
contain pre-defined values.

If your column "status" can be one of the following "pending", "declined", "archived" and "paid" and you would like
to use different icons and colors to emphasise status:

```
$states = [
    'positive' => ['paid', 'archived'],
    'negative' => ['declined'],
];

$table->addColumn('status', new \Atk4\Ui\Table\Column\Status($states));
```

Current list of states supported:

- positive (checkmark icon)
- negative (close icon)
- and the default/unspecified state (icon question)

(list of states may be expanded further)

### Template

:::{php:class} Table\Column\Template
:::

This column is suitable if you wish to have custom cell formatting but do not wish to go through
the trouble of setting up your own class.

If you wish to display movie rating "4 out of 10" based around the column "rating", you can use:

```
$table->addColumn('rating', new \Atk4\Ui\Table\Column\Template('{$rating} out of 10'));
```

Template may incorporate values from multiple fields in a data row, but current implementation
will only work if you assign it to a primary column (by passing 1st argument to addColumn).

(In the future it may be optional with the ability to specify caption).

### Image

:::{php:class} Table\Column\Image
:::

This column is suitable if you wish to have image in your table cell:

```
$table->addColumn('image_url', new \Atk4\Ui\Table\Column\Image);
```

## Interactive Decorators

### ActionButtons

:::{php:class} Table\Column\ActionButtons
:::

Can be used to add "action buttons" column to your table:

```
$action = $table->addColumn(null, [Table\Column\ActionButtons::class]);
```

If you want to have label above the action column, then:

```
$action = $table->addColumn(null, [Table\Column\ActionButtons::class, 'caption' => 'User Actions']);
```

:::{php:method} addButton($button, $action, $confirm = false)
:::

Adds another button into "Actions" column which will perform a certain JavaScript action when clicked.
See also {php:meth}`Grid::addActionButton()`:

```
$button = $action->addButton('Reload Table', $table->jsReload());
```

Normally you would also want to pass the ID of the row which was clicked. You can use {php:meth}`Table:jsRow()`
and jQuery's data() method to reference it:

```
$button = $action->addButton('Reload Table', $table->jsReload(['clicked' => $table->jsRow()->data('id')]));
```

Moreover you may pass $action argument as a PHP callback.

:::{php:method} addModal($button, $title, $callback)
:::

Triggers a modal dialog when you click on the button. See description on {php:meth}`Grid::addModalAction()`:

```
$action->addButton(['Say HI'], function (Jquery $j, $id) use ($g) {
    return 'Loaded "' . $g->model->load($id)->get('name') . '" from ID=' . $id;
});
```

Note that in this case ID is automatically passed to your callback.

### Checkbox

:::{php:class} Table\Column\Checkbox
:::

:::{php:method} jsChecked()
:::

Adding this column will render checkbox for each row. This column must not be used on a field.
CheckBox column provides you with a handy jsChecked() method, which you can use to reference
current item selection. The next code will allow you to select the checkboxes, and when you
click on the button, it will reload $segment component while passing all the id's:

```
$box = $table->addColumn(new \Atk4\Ui\Table\Column\CheckBox());

$button->on('click', new JsReload($segment, ['ids' => $box->jsChecked()]));
```

jsChecked expression represents a JavaScript string which you can place inside a form control,
use as argument etc.

### Multiformat

Sometimes your formatting may change depending on value. For example you may want to place link
only on certain rows. For this you can use an `\Atk4\Ui\Table\Column\Multiformat` decorator:

```
$table->addColumn('amount', [\Atk4\Ui\Table\Column\Multiformat::class, function (Model $model) {
    if ($model->get('is_invoiced') > 0) {
        return [\Atk4\Ui\Table\Column\Money::class, [\Atk4\Ui\Table\Column\Link::class, 'invoice', ['invoice_id' => 'id']]];
    } elseif (abs($model->get('is_refunded')) < 50) {
        return [[\Atk4\Ui\Table\Column\Template::class, 'Amount was <b>refunded</b>']];
    }

    return [[\Atk4\Ui\Table\Column\Money::class]];
}]);
```

You supply a callback to the Multiformat decorator, which will then be used to determine
the actual set of decorators to be used on a given row. The example above will look at various
fields of your models and will conditionally add Link on top of Money formatting.

The callback must return array of seeds like:

```
[[\Atk4\Ui\Table\Column\Link::class], \Atk4\Ui\Table\Column\Money::class]
```

Multiple decorators will be created and merged.

:::{note}
If you are operating with large tables, code your own decorator, which would be more CPU-efficient.
:::
