
.. php:namespace:: Atk4\Ui\Form\Control

.. php:class:: Multiline


======================
Multiline Form Control
======================


The Multiline form control is not a single field, but is used to edit several Model records.
A good example is a user who can have many addresses. In this example, the Model `User` containsMany `Addresses`.
This means that the addresses are not stored into a separate database table but into the field `addresses` of user table::

    /**
     * User model
     */
    class User extends \Atk4\Data\Model
    {
        public $table = 'user';

        protected function init(): void
        {
            parent:: init();

            $this->addField('firstname', ['type' => 'string']);
            $this->addField('lastname', ['type' => 'string']);

            $this->containsMany('addresses', [Address::class, 'system' => false]);
            //$this->hasMany('Email', [Email::class]);
        }
    }

    /**
     * Address Model
     */
    class Address extends \Atk4\Data\Model
    {
        public $table = 'addresses';

        protected function init(): void
        {
            parent::init();

            $this->addField('street_and_number', ['type' => 'string']);
            $this->addField('zip', ['type' => 'string']);
            $this->addField('city', ['type' => 'string']);
            $this->addField('country', ['type' => 'string']);
        }
    }

    // Create some sample record of user Model
    $user = new User(new \Atk4\Data\Persistence\Array_());
    $user->set('firstname', 'Hans');
    $user->set('lastname', 'Test');
    $user->save();


    // Add a Form to the UI and set User as Model
    $user_form = \Atk4\Ui\Form::addTo($app);
    $user_form->setModel($user);

This leads to a Multiline component automatically rendered for adding, editing and deleting Addresses of the user:

.. image:: images/multiline_user_addresses.png

You can also check LINK_TO_DEMO/multiline.php for this example





Manually setting up Multiline
=============================

Multiline form control is used by default if a Model `containsMany()` or `containsOne()` other Model, but you can set up the multiline component manually. For example, if you wish to edit
a `hasMany()` relation of a Model along with the Model itself. (In contrary to containsMany(), the records of the related Model are stored in a separate table). Lets say a User can have many email addresses,
but you want to store them in a separate table. Uncomment the line `//$this->hasMany('Email', [Email::class]);` in User Model to use it::

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

Now when we use a Form for User records, it won't automatically add a Multiline to edit the email addresses.
If you want to edit them along with the user, Multiline is set up in a few lines::

    // Create some sample record of user Model
    $user = new User(new \Atk4\Data\Persistence\Array_());
    $user->setId(1);
    $user->set('firstname', 'Hans');
    $user->set('lastname', 'Test');
    $user->save();

    // Add a form to UI to edit User record
    $user_form = \Atk4\Ui\Form::addTo($app);
    $user_form->setModel($user);
    $ml = $user_form->addField('email_addresses', [\Atk4\Ui\Form\Control\Multiline::class]);
    $ml->setModel($user->ref('Email'));

    // set up saving of Email on Form submit
    $user_form->onSubmit(function($form) use ($ml) {
        $form->model->save();
        $ml->saveRows();
        // show saved data for testing purposes
        return new JsToast(var_export($ml->model->export(), true));
    });


Now, there is another Multiline form contol to add, edit or delete the users email addresses:

.. image:: images/multiline_email_address.png


Multiline and Expressions
=========================
If a Model has Expressions, they automatically get updated when a form control value is changed. A loading icon on the ``+`` sign indicates that the expression values are updated.
Lets use the example of demos/multiline.php::

    class InventoryItem extends \Atk4\Data\Model
    {
        protected function init(): void
        {
            parent::init();
            $this->addField('item', ['required' => true, 'default' => 'item']);
            $this->addField('qty', ['type' => 'number', 'caption' => 'Qty / Box', 'required' => true, 'ui' => ['multiline' => ['width' => 2]]]);
            $this->addField('box', ['type' => 'number', 'caption' => '# of Boxes', 'required' => true, 'ui' => ['multiline' => ['width' => 2]]]);
            $this->addExpression('total', ['expr' => function (Model $row) {
                return $row->get('qty') * $row->get('box');
            }, 'type' => 'number']);
        }
    }
    
The 'total' expression will get updated on each field change automatically when InventoryItem is set as model to Multiline.


Manually adding actions on a form control value change
======================================================
If you want to define a callback which gets executed if a field value is changed, you can do so using the ``onLineChange()`` method. The first parameter is the callback, the second one an array including the field names which trigger the callback when changed. You can return a single JsExpressionable or an array of JsExpressionables which then will be sent to the browser. In this case we display a Toast with some message::

    $multiline->onLineChange(function ($rows, $form) {
        $total = 0;
        foreach ($rows as $row => $cols) {
            $qty = array_column($cols, 'qty')[0];
            $box = array_column($cols, 'box')[0];
            $total = $total + ($qty * $box);
        }
        return new JsToast('The new Total is '.number_format($total, 2));
    }, ['field1', 'field2']);


Changing appearance of Multiline
================================

Header
------
- The header uses the field's caption by default. 
- You can edit it by setting the ``header`` property. 
- If you want to hide the header, set the ``$header`` property to an empty string ``''``.

Changing how fields are displayed
---------------------------------
If you want to change how single inputs are displayed in the multiline, you can use field's ui property::

    $model->addFields([
        ['name', 'type' => 'string', 'ui' => ['multiline' => ['input', ['icon' => 'user', 'type' => 'text']]]],
        ['value', 'type' => 'string', 'ui' => ['multiline' => ['input', ['type' => 'number']]]],
        ['description', 'type' => 'string', 'ui' => ['multiline' => ['textarea']]],
    ]);
    
This above will display a 'name', 'value' and 'description' form controls within a multiline form control. The 'value' form control input will use the html attribute type set to number and the
'description' form control will be displayed as a textarea input.

The `$ui['multiline']` property can be set using an array. The first element of the array is the field type to render as html in multiline form control and should contain a string value. The supported form control types are input, textarea, dropdown or checkbox.
The second element of the array represent the options associated with the field type and should contains an array.
Since Multiline form control used some of Semantic-ui Vue component to render the field type in html, the options accepted
are based on Semantic-ui vue supported property. For example, input control type, or component in Semantic-ui Vue can have its html type attribute set using the type option, like the 'value' form control set above.

You may see each option you can use by looking at Semantic-ui vue component property:
- `input <https://semantic-ui-vue.github.io/#/elements/input>`_
- `dropdown <https://semantic-ui-vue.github.io/#/modules/dropdown>`_
- `checkbox <https://semantic-ui-vue.github.io/#/modules/checkbox>`_

Note: There is no option available for textarea.

Footer
------
You can add a footer to Multiline form control by adding a sublayout to it. In this example, we add a footer containing a read-only input which could get the value from ``onLineChange`` callback (see above)::
   
    $ml = $form->addControl('ml', [\Atk4\Ui\FormField\Multiline::class, 'options' => ['color' => 'blue']]);
    $ml->setModel($inventory);
    // Add sublayout with total form control.
    $sub_layout = $form->layout->addSublayout([\Atk4\Ui\Form\Layout\Section\Columns::class]);
    $sub_layout->addColumn(12);
    $c = $sub_layout->addColumn(4);
    $f_total = $c->addControl('total', ['readonly' => true])->set($total);
