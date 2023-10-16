[ATK UI](https://github.com/atk4/ui) is a UI library for building UI interfaces that has a built-in [CRUD](http://ui.agiletoolkit.org/demos/crud.php) component. It can be used to create complex admin systems, but it requires you to populate multiple pages and inter-link them together yourself. 

![mastercrud](docs/images/mastercrud.png)

**MasterCRUD** is an add-on for ATK UI and ATK Data, which will orchestrate navigation between multiple CRUD pages by respecting relations and conditions. You can use **MasterCRUD** to:

-   Manage list of clients, and their individual invoices and payments.
-   Manage user groups and users within them
-   Manage multi-level catalogue and products in them

The syntax of **MasterCRUD** is incredibly simple and short. It automatically takes care of many details like:

-   record and track `id` of various records you have clicked on (BreadCrumb)
-   display multi-Tab pages with model details and optional relations
-   support `hasOne` and `hasMany` relations
-   allow flexible linking to a higher tree level (user - invoice - allocated_payment -> payment (drops invoice_id))

**MasterCRUD** can also be extended to contain your own views, you can interact with the menu and even place **MasterCRUD** inside a more complex layouts.

### Example Use Case (see demos/clients.php for full demo):

Assuming you have Clients with Invoices and Payments and you also want to add "Line"s for each Invoice, you may want to add this interface for the admin, where user can use drill-downs to navigate through data:

![step1](docs/images/step1.png)

Clicking on `Client 2` would bring you to a different page. Extra tabs Invoices and Payments offer you further way in:

![step2](docs/images/step2.png)

clicking on specific invoice, you can edit it's lines:

![step3](docs/images/step3.png)

On this screen however we turned off deletion of lines (because it is a demo). However clicking Edit brings up a Modal where you can easily update record data:

![step4](docs/images/step4.png)



All this UI can be created in just a few lines of code!



MasterCRUD operates like a regular CRUD, and you can easily substitute it in:

``` php
$crud = $app->add('\atk4\mastercrud\MasterCRUD');
$crud->setModel('Client');
```

You'll noticed that you can now click on the client name to get full details about this client. Next, we want to be able to see and manage Client invoices:

``` php
$crud = $app->add('\atk4\mastercrud\MasterCRUD');
$crud->setModel('Client', ['Invoices'=>[]]);
```

This will add 2nd tab to the "Client Details" screen listing invoices of said client. If you invoice is further broken down into "Lines", you can go one level deeper:

``` php
$crud = $app->add('\atk4\mastercrud\MasterCRUD');
$crud->setModel('Client', ['Invoices'=>['Lines'=>[]]]);
```

If `Client hasMany('Payments')` then you can also add that relation:

``` php
$crud = $app->add('\atk4\mastercrud\MasterCRUD');
$crud->setModel('Client', ['Invoices'=>['Lines'=>[]], 'Payments'=>[]]);
```

With some cleanup, this syntax is readable and nice:

``` php
$crud = $app->add('\atk4\mastercrud\MasterCRUD');
$crud->setModel('Client', [
  'Invoices'=>[
    'Lines'=>[]
  ], 
  'Payments'=>[]
]);
```

## Support for actions

MasterCRUD is awesome for quickly creating admin systems. But basic C,R,U,D operations are not enough. Sometimes you want to invoke custom actions for individual element. MasterCRUD now supports that too:

```php
$app->layout->add(new \atk4\mastercrud\MasterCRUD())
    ->setModel(new \saasty\Model\App($app->db), 
    [
        'columnActions'=>[
            'repair'=>['icon'=>'wrench'],
        ],
        'Models'=>[
            'columnActions'=>[
                'migrate'=>['icon'=>'database'],
            ],
            'Fields'=>[
                'ValidationRules'=>[],
            
            ],
            'Relations'=>[
                'ImportedFields'=>[],
            ],
        ],
```

 ![actions](docs/images/actions.png)

There are various invocation methods allowing you to specify icon, label, custom callbacks etc.

This also adds "MethodInvocator" - a view which asks you for arguments and then executes them.

This next example will use form to ask for an email, which will then be passed as argument to sendEmail($email)

```php
[
    'columnActions'=>[
         'sendEmail' => ['icon'=>'wrench', 'email'=>'string']
   ]
]
```





### Installation

Install through composer: 

``` bash
 composer require atk4/mastercrud
```

Also see introduction for [ATK UI](https://github.com/atk4/ui) on how to render HTML.

## Roadmap

- [x] Allow to specify custom CRUD seed. You can ever replace it with your own compatible view.
- [x] Add custom actions and function invocation
- [ ] Create decent "View" mode (relies on ATK UI Card)
- [ ] Traverse hasOne references (see below)















-------------------------

> NOT IMPLEMENTED BELOW

Suppose that `Invoice hasMany(Allocation)`and `Payment hasMany(Allocation)` while allocation can have one Payment and one Invoice.

``` php
$crud = $app->add('\atk4\mastercrud\MasterCRUD');
$crud->setModel('Client', [
  'Invoices'=>[
    'Lines'=>[],
    'Allocations'=>[]
  ], 
  'Payments'=>[
    'Allocations'=>[]
  ]
]);
```

That's cool, but if you go through the route of `Invoice -> allocation ->` you should be able to click on the "payment":

``` php
$crud = $app->add('\atk4\mastercrud\MasterCRUD');
$crud->setModel('Client', [
  'Invoices'=>[
    'Lines'=>[],
    'Allocations'=>[
      'payment_id'=>['path'=>'Payments', 'payment_id'=>'payment_id']
    ]
  ], 
  'Payments'=>[
    'Allocations'=>[
      'invoice_id'=>['path'=>'Invoices', 'invoice_id'=>'invoice_id']
    ]
  ]
]);
```

Now you will be able to jump from `Invoice->allocation` to `Payment` and other way around.


