:::{php:namespace} Atk4\Ui
:::

:::{php:class} Form\Control\Multiline
:::

# Multiline Form Control

The Multiline form control is not simply a single control, but will add multiple control in order to be able to edit
multiple Model fields related to a single record reference into another Model.

A good example is a user who can have many addresses.
In this example, the Model `User` containsMany `Addresses`. Since the Model field addresses is defined with containsMany()
inside the main model, Multiline will store addresses content as JSON value inside the table blobl addresses field.

For example:

```
/**
 * User model
 */
class User extends \Atk4\Data\Model
{
    public $table = 'user';

    protected function init(): void
    {
        parent::init();

        $this->addField('firstname', ['type' => 'string']);
        $this->addField('lastname', ['type' => 'string']);

        $this->containsMany('addresses', [Address::class, 'system' => false]);
    }
}

/**
 * Address Model
 */
class Address extends \Atk4\Data\Model
{
    protected function init(): void
    {
        parent::init();

        $this->addField('street_and_number', ['type' => 'string']);
        $this->addField('zip', ['type' => 'string']);
        $this->addField('city', ['type' => 'string']);
        $this->addField('country', ['type' => 'string']);
    }
}

\Atk4\Ui\Crud::addTo($app)->setModel(new User($app->db));
```

This leads to a Multiline component automatically rendered for adding, editing and deleting Addresses of the current user record:

:::{image} images/multiline_user_addresses.png
:::

You can also check LINK_TO_DEMO/multiline.php for this example

## Using Multiline with HasMany relation

Multiline form control is used by default when a Model field used `containsMany()` or `containsOne()`, but you can set
up the multiline component to be used with hasMany() relation and edit related record accordingly.

Lets say a User can have many email addresses, but you want to store them in a separate table.:

```
/**
 * Email Model
 */
class Email extends \Atk4\Data\Model
{
    public $table = 'email';

    protected function init(): void
    {
        parent::init();

        $this->addField('email_address', ['type' => 'string']);

        $this->hasOne('user_id', [User::class]);
    }
}

/**
 * User model
 */
class User extends \Atk4\Data\Model
{
    public $table = 'user';

    protected function init(): void
    {
        parent::init();

        $this->addField('firstname', ['type' => 'string']);
        $this->addField('lastname', ['type' => 'string']);

        $this->hasMany('Emails', [Email::class]);
    }
}
```

Using a form with User model won't automatically add a Multiline to edit the related email addresses.

:::{php:method} setReferenceModel(string $refModelName, Model $entity = null, array $fieldNames = []): Model
:::

If you want to edit them along with the user, Multiline need to be set up accordingly using the setReferenceModel method:

```
// add a form to UI in order to edit User record
$userForm = \Atk4\Ui\Form::addTo($app);
$userForm->setModel($user->load($userId));

$ml = $userForm->addControl('emails', [\Atk4\Ui\Form\Control\Multiline::class]);
$ml->setReferenceModel('Emails');

// set up saving of Email on Form submit
$userForm->onSubmit(function (Form $form) use ($ml) {
    $form->model->save();
    // save emails record related to current user
    $ml->saveRows();

    return new JsToast(var_export($ml->model->export(), true));
});
```

Using the example above will create a form with control from the User model as well as a Multiline control for editing
the Email model's field.

### Important

It is important to note that for Email's record to be properly saved, in relation to their User record, the User model
needs to be loaded prior to call Multiline::setReferenceModel() method.

Also note that Multiline::saveRows() method need to be called for related record to be saved in related table. You would
normally call this method in your form onSubmit handler method.

## Multiline and Expressions

If a Model contains Expressions, there resulting values will automatically get updated when one of the form control value is changed.
A loading icon on the `+` button will indicates that the expression values are being update.

Lets use the example of `demos/multiline.php`:

```
class InventoryItem extends \Atk4\Data\Model
{
    protected function init(): void
    {
        parent::init();

        $this->addField('item', ['required' => true, 'default' => 'item']);
        $this->addField('qty', ['type' => 'integer', 'caption' => 'Qty / Box', 'required' => true, 'ui' => ['multiline' => [Form\Control\Multiline::TABLE_CELL => ['width' => 2]]]]);
        $this->addField('box', ['type' => 'integer', 'caption' => '# of Boxes', 'required' => true, 'ui' => ['multiline' => [Form\Control\Multiline::TABLE_CELL => ['width' => 2]]]]);
        $this->addExpression('total', ['expr' => function (Model $row) {
            return $row->get('qty') * $row->get('box');
        }, 'type' => 'integer']);
    }
}
```

The 'total' expression will get updated on each field change automatically.

## OnLineChange Callback

If you want to define a callback which gets executed when a field value in a Multiline row is changed,
you can do so using the `onLineChange()` method.
The first parameter is the callback function, the second one is an array containing field names that will trigger
the callback when values are changed.
You can return a single JsExpressionable or an array of JsExpressionables which then will be sent to the browser.

In this case we display a message when any of the control value for 'qty' and 'box' are changed:

```
$multiline->onLineChange(function (array $rows, Form $form) {
    $total = 0;
    foreach ($rows as $row => $cols) {
        $qty = $cols['qty'] ?? 0;
        $box = $cols['box'] ?? 0;
        $total += $qty * $box;
    }

    return new JsToast('The new Total is ' . $app->uiPersistence->typecastSaveField(new Field(['type' => 'atk4_money']), $total));
}, ['qty', 'box']);
```

## Multiline Vue Component

Multiline is a Vue component by itself and rely on many others Vue components to render itself.
Each control is render via a Vue component and the Vue component used will depend on the model
field type associated with Multiline control.

You will find a list of Vue component associated with each field type within the Multiline $fieldMapToComponent array.

:::{php:attr} fieldMapToComponent
:::

Each control being a Vue component means that they accept 'Props' that may change their look or behaviour.
Props on each component may be applied globally, i.e. to all control within Multiline that use that control, or
per component.

### Setting component Props globally

Use the $componentProps property of Multiline in order to apply 'Props' to component globally.

:::{php:attr} componentProps
:::

Example of changing all Dropdown(SuiDropdown) within Multiline:

```
$ml = $form->addControl('ml', [Multiline::class, 'compponentProps' => [Multiline::SELECT => ['floating' => true]]]);
```

### Setting component Props per field

Specific field components Props may be applied using the 'ui' field property when adding field to your model:

```
$this->addField('email', [
    'required' => true,
    'ui' => ['multiline' => [Multiline::INPUT => ['icon' => 'envelope', 'type' => 'email']]],
]);
$this->addField('password', [
    'required' => true,
    'ui' => ['multiline' => [Multiline::INPUT => ['icon' => 'key', 'type' => 'password']]],
]);
```

### Note on Multiline control

Each control inside Multiline is wrap within a table cell(SuiTableCell) component and this component can be customize as
well using the 'ui' property of the model's field:

```
$this->addExpression('total', [
    'expr' => function (Model $row) {
        return $row->get('qty') * $row->get('box');
    },
    'type' => 'integer',
    'ui' => ['multiline' => [Multiline::TABLE_CELL => ['width' => 1, 'class' => 'blue']]],
]);
```

### Table appearance within Multiline

Table(SuiTable) Props can be set using $tableProps property of Multiline:

```
$ml = $form->addControl('ml', [Multiline::class, 'tableProps' => ['color' => 'blue']]);
```

### Header

- The header uses the field's caption by default.
- You can edit it by setting the `$caption` property.
- If you want to hide the header, set the `$caption` property to an empty string `''`.
