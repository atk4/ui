Multiline Form Field

The Multiline Form Field is not a single field, but is used to edit several Model records.
A good example is a user who can have many addresses. In this example, the Model 'User' containsMany 'Addresses'.
This means that the addresses are not stored into a separate database table but into the field 'addresses' of user table:

/**
 * User model.
 */
class User extends \atk4\data\Model
{
    public $table = 'user';

    public function init()
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
class Address extends \atk4\data\Model
{
    public $table = 'addresses';

    public function init()
    {
        parent::init();

        $this->addField('street_and_number', ['type' => 'string']);
        $this->addField('zip', ['type' => 'string']);
        $this->addField('city', ['type' => 'string']);
        $this->addField('country', ['type' => 'string']);
    }
}


//Create some sample record of user Model
$user_data = [];
$user = new User(new \atk4\data\Persistence\Array_($user_data));
$user->set('firstname', 'Hans');
$user->set('lastname', 'Test');
$user->save();


//Add a Form to the UI and set User as Model
$user_form = $app->add('Form');
$user_form->setModel($user);

This leads to a Multiline component automatically rendered for adding, editing and deleting Addresses of the user:

---- SCREENSHOT HERE ---

You can also check LINK_TO_DEMO/multiline.php for this example





--- Manually setting up Multiline ---

Multiline FormField is used by default if a Model containsMany() or containsOne() other Model, but you can set up the multiline component manually. For example, if you wish to edit
a hasMany() relation of a Model. Lets use the  (In contrary to containsMany(), the records of the related Model are stored in a separate table. Lets say a User can have many emails,
but you want to store them in a separate table. Uncomment the line //$this->hasMany('Email', [Email::class]); in User Model to use it.


/**
 * Email Model
 */
class Email extends \atk4\data\Model
{
    public $table = 'email';

    public function init()
    {
        parent::init();

        $this->addField('email_address', ['type' => 'string']);

        $this->hasOne('user_id', [User::class]);
    }
}

Now when we use a Form for User records, it won't automatically add a Multiline to edit the email addresses.
If you want to edit them along with the user, Multiline is set up in a few lines:

//Create some sample record of user Model
$user_data = [];
$user = new User(new \atk4\data\Persistence\Array_($user_data));
$user->id = 1;
$user->set('firstname', 'Hans');
$user->set('lastname', 'Test');
$user->save();

//Add a form to UI to edit User record
$user_form = $app->add('Form');
$user_form->setModel($user);
$ml = $user_form->addField('email_addresses', ['MultiLine']);
$ml->setModel($user->ref('Email'));

//set up saving of Email on Form submit
$user_form->onSubmit(function($form) use ($ml) {
    $form->model->save();
    $ml->saveRows();
    //show saved data for testing purposes
    return new jsToast(var_export($ml->model->export(), true));
});


Now, there is another MultiLine FormField to add, edit or delete the users email addresses:

SCREENSHOT



--- Multiline and Expressions ---
Use Invoice/Line sample code here as this has sensible expressions



--- Changing appearance of Multiline ---
Parts of multiline component
- header
    - uses FormFields caption, if caption is empty string its hidden
- Data rows with inputs
    - (pass args to Field->ui['multiline'] to edit the way they are displayed)
- Footer
    - if additional expressions are defined, they are shown here (provide example)

