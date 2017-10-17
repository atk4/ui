# Agile UI

[![Build Status](https://travis-ci.org/atk4/ui.png?branch=develop)](https://travis-ci.org/atk4/ui)
[![Code Climate](https://codeclimate.com/github/atk4/ui/badges/gpa.svg)](https://codeclimate.com/github/atk4/ui)
[![StyleCI](https://styleci.io/repos/68417565/shield)](https://styleci.io/repos/68417565)
[![codecov](https://codecov.io/gh/atk4/ui/branch/develop/graph/badge.svg)](https://codecov.io/gh/atk4/ui)
[![Version](https://badge.fury.io/gh/atk4%2Fui.svg)](https://packagist.org/packages/atk4/ui)

**Agile UI is a high-level PHP framework for creating User Interfaces and Web Apps**

You might need a CRUD for your "Admin Interface" or perhaps a basic Contact Form or you simply need Web UI to access [your database in SQL or NoSQL](https://github.com/atk4/data) - Agile UI provides universal and and extensible open-source platform for developing interactive PHP components.

Any component can then be added to your existing Web App in just few lines of code:

``` php
$crud = new \atk4\ui\CRUD();
$crud->setModel(new User($db));
echo $crud->render();
```

## Building Admin in Agile UI

Agile UI is ideal platform for "Admin Systems". You need one, but don't want to spend too much time on it. Here are few examples showing what you can accomplish:

### Admin+CRUD in 15 lines

``` php
  $app = new \atk4\ui\App('My App');
  $app->initLayout(new \atk4\ui\Layout\Admin());

  $db = \atk4\data\Persistence::connect($DSN);

  class User extends \atk4\data\Model {
      public $table = 'user';
      function init() {
          parent::init();

          $this->addField('name');
          $this->addField('email', ['required'=>true]);
          $this->addField('password', ['type'=>'password']);
      }
  }

  $app->layout->add(new \atk4\ui\CRUD())
    ->setModel(new User($db));
```

A total of 15 lines to create a fully working Admin system with a CRUD:

![](docs/images/admin-in-15-lines.png)

## Registration Page - 15 lines

Agile UI is not for admin systems only. It can be used to render your front-end UI too. Lets look at how to create a simple user registration page in.

``` php
$app = new \atk4\ui\App('My App');
$app->initLayout(new \atk4\ui\Layout\Centered());

$db = \atk4\data\Persistence::connect($DSN);

$form = $app->layout->add(new \atk4\ui\Form());
$form->setModel(new User($db));
$form->buttonSave->set(['Register', 'icon'=>'right arrow']);
$form->onSubmit(function($form){
    $u = $form->model->newInstance()->tryLoadBy('email', $form->model['email']);
    if ($u->loaded()) {
        return $form->error('email', 'There is already account with this email.');
    }
    $form->model->save();
    return $form->success('Thank you. You may login now.');
});
```

Again - the result is beautiful and interractive:

![](docs/images/register-in-15-lines.png)

## What can be done in few hours?

With some knowledge and training you will be able to build entire business-class applications just within few hours. 

-   Warehouse app - source: https://github.com/atk4/warehouse
-   Warehouse app - demo: https://atk-warehouse.herokuapp.com (free hosting, so wait 5 seconds)

WARNING: this specification is intentionally kept simple for demonstration purposes.

## Reinventing Data Persistence

Conventionally you have to work with ORMs or direct data queries. Agile UI relies on a entirely different class framework for database persistence - [Agile Data](https://github.com/atk4/data). Not only it has all the benefits of ORM, but it also addresses all the faults and shortcomings to deliver powerful features like:

-   Aggregation, scoping, behaviours, audit and custom actions
-   NoSQL and RestAPI persistence support for Cloud Databases and Services
-   SQL Expressions, Deep traversal and working with multiple persistences

Through tight integration with Agile Data it makes it possible for your Forms to automatically explore relations and provide values for drop-downs:

![](./docs/images/relation-exploration.png)

## Add-ons and extensions

Agile UI has been developed from ground-up to be extensible in a whole new way. Each add-on delivers wide range of classes you can incorporate into your application without worrying about UI and Data compatibility.

### Components

Components present a UI element which you can place anywhere in your layout. How about a UI for performing all user management operations - reset password, block account or edit user data:

``` php
$user_admin = $app->layout->add(new \atk4\login\UserAdmin(new User($db)));
```

### Grid Actions

What about also adding a button for a popup to send a custom message to a user implemented in entirely different add-on?

``` php
$user_admin->addAction(\atk4\sendgrid\Actions\SendUserMessage());
```

### Entire Application Parts

Even entire parts of your applications can be provided by add-on. The following code adds Log-in, Registration and Password reminder for your application:

``` php
$app->add(new \atk4\login\Auth([
  new User($db),
  'reminder'=>true, 'register'=>true
  'outbox' = new \atk4\sendgrid\Outbox(['key'=>$apiKey])
]));
```

![Login](./docs/images/login-demo.png)

### Extending Layouts

An add-on can add new menu items. It does not happen automatically but through a very simple line of code:

``` php
$auth->addChangePassword($app->layout->menu_user);
```

### Other things you can extend

There are many other things you can extend:

-   application layouts (e.g. Admin and Centered)
-   form fields (e.g. CheckBox and Calendar)
-   table columns (e.g. Status and Links)
-   action-column actions (e.g. Button, Expander)
-   data types (e.g. money, date)
-   persistences (APIs and Services)
-   models (e.g. User, Country)

Because Agile UI takes care of the hard work, each add-on is very minimalistic and does not contain anything unnecessary. Your application will remain lean and you always have option to customize.

## Bundled componens

Agile UI comes with many built-in components;

| Component                                | Description                              | Introduced |
| ---------------------------------------- | ---------------------------------------- | ---------- |
| [View](http://ui.agiletoolkit.org/demos/view.php) | Template, Render Tree and various patterns | 0.1        |
| [Button](http://ui.agiletoolkit.org/demos/button.php) | Button in various variations including icons, labels, styles and tags | 0.1        |
| [Input](http://ui.agiletoolkit.org/demos/field.php) | Decoration of input fields, integration with buttons. | 0.2        |
| [JS](http://ui.agiletoolkit.org/demos/button2.php) | Assign JS events and abstraction of PHP callbacks. | 0.2        |
| [Header](http://ui.agiletoolkit.org/demos/header.php) | Simple view for header.                  | 0.3        |
| [Menu](http://ui.agiletoolkit.org/demos/layout2.php) | Horizontal and vertical multi-dimensional menus with icons. | 0.4        |
| [Form](http://ui.agiletoolkit.org/demos/form.php) | Validation, Interactivity, Feedback, Layouts, Field types. | 0.4        |
| [Layouts](http://ui.agiletoolkit.org/demos/layouts.php) | Admin, Centered.                         | 0.4        |
| [Table](http://ui.agiletoolkit.org/demos/table.php) | Formatting, Columns, Status, Link, Template, Delete. | 1.0        |
| [Grid](http://ui.agiletoolkit.org/demos/grid.php) | Toolbar, Paginator, Quick-search, Expander, Actions. | 1.1        |
| [Message](http://ui.agiletoolkit.org/demos/message.php) | Such as "Info", "Error", "Warning" or "Tip" for easy use. | 1.1        |
| [Modal](https://ui.agiletoolkit.org/demos/modal.php) | Modal dialog with dynamically loaded content. | 1.1        |
| [Reloading](http://ui.agiletoolkit.org/demos/reloading.php) | Dynamically re-render part of the UI.    | 1.1        |
| [Actions](https://ui.agiletoolkit.org/demos/reloading.php) | Extended buttons with various interactions | 1.1        |
| [CRUD](http://ui.agiletoolkit.org/demos/crud.php) | Create, List, Edit and Delete records (based on Advanced Grid) | 1.1        |
| [Tabs](https://ui.agiletoolkit.org/demos/tabs.php) | 4 Responsive: Admin, Centered, Site, Wide. | 1.2        |
| Breadcrumb                               | Push links to pages for navigation. Wizard. | 1.3 *      |
| Items, Cards                             | Responsive Items and Card implementaiton. | 1.4 *      |
| Wizard                                   | Multi-step, wizard with temporary data storing. | 1.5 *      |
|                                          |                                          |            |

-- * Component is not implemented yet.

All bundled components are free and licensed under MIT license. They are installed together with Agile UI.

External and 3rd party components may be subject to different licensing terms.

## Installing and Using

In your command line type `composer require atk4/ui`.

Afterwards, use this code to see a Hello World message:

``` php
require "vendor/autoload.php";

$app = new \atk4\ui\App('My First App');
$app->initLayout('Centered');

$app->layout->add('HelloWorld');
```

That's right! We have [Hello World component](https://github.com/atk4/ui/blob/develop/src/HelloWorld.php)!! When you are ready to do something bit more advanced:

``` php
require "vendor/autoload.php";

$db = new \atk4\data\Persistence_SQL('mysql:dbname=test;host=localhost','root','root');
$app = new \atk4\ui\App('My Second App');
$app->initLayout('Admin');

$m_comp = $app->layout->menu->addMenu(['Layouts', 'icon'=>'puzzle']);
$m_comp->addItem('Centered', 'centered');
$m_comp->addItem('Admin', 'admin');

$m_comp = $app->layout->menu->addMenu(['Component Demo', 'icon'=>'puzzle']);
$m_form = $m_comp->addMenu('Forms');
$m_form->addItem('Form Elements', 'from');
$m_form->addItem('Form Layouts', 'layout');
$m_comp->addItem('CRUD', 'crud');

$app->layout->leftMenu->addItem(['Home', 'icon'=>'home']);
$app->layout->leftMenu->addItem(['Topics', 'icon'=>'block layout']);
$app->layout->leftMenu->addItem(['Friends', 'icon'=>'smile']);
$app->layout->leftMenu->addItem(['Historty', 'icon'=>'calendar']);
$app->layout->leftMenu->addItem(['Settings', 'icon'=>'cogs']);

$f = $app->layout->add(new \atk4\ui\Form(['segment']));

$f_group = $f->addGroup('Name');
$f_group->addField('first_name', ['width'=>'eight']);
$f_group->addField('middle_name', ['width'=>'three']);
$f_group->addField('last_name', ['width'=>'five']);

$f_group = $f->addGroup('Address');
$f_group->addField('address', ['width'=>'twelve']);
$f_group->addField('zip', ['Post Code', 'width'=>'four']);

$f->onSubmit(function ($f) {
    $errors = [];

    foreach (['first_name', 'last_name', 'address'] as $field) {
        if (!$f->model[$field]) {
            $errors[] = $f->error($field, 'Field '.$field.' is mandatory');
        }
    }

    return $errors ?: $f->success('No more errors', 'so we have saved everything into the database');
});
```

For more examples simply look into [demo folder](https://github.com/atk4/ui/tree/develop/demos).

### Single component render

If you need to render only one component without boilerplate HTML, use render() method.

``` html
 <head>
    <link rel="stylesheet" type="text/css" href="http://semantic-ui.com/dist/semantic.css">
    <script src="https://code.jquery.com/jquery-3.1.1.js"></script>
    <script src="http://semantic-ui.com/dist/semantic.js"></script>
</head>
<body>
  
<?php 
  $component = new \atk4\ui\HelloWorld();
  echo $component->render();
?>
 
</body>
```

## Documentation

Because Agile UI makes active use of Agile Core and Agile data, I'm linking all related documentation here:

-   [Agile UI Documentation](http://agile-ui.readthedocs.io)
-   [Agile Data Documentation](http://agile-data.readthedocs.io)
-   [Agile Core Documentation](http://agile-core.readthedocs.io)

If anything is unclear or you want to get in touch with other awesome people who use Agile UI:

-   [Forum](https://forum.agiletoolkit.org) - use label Agile UI or Agile Data.
-   [Developer Gitter Live Chat](https://gitter.im/atk4/atk4) - if you wish to say Thanks to those who created Agile UI (for free!)

 ## Scope and Goals of Agile UI

What makes this UI toolkit stand out from the others UI libraries is a commitment to bring rich and interractive web components that can be used for 90% of web applications without any custom-HTML/JS. Additionally, Agile UI provides a very controlled and consistent ways to develop "add-ons" that include visual components and other re-usable elements.

To achieve its goal, Agile UI offers both the tools for creating components and a wide selection of built-in components that provides the "minimum standard Web UI":

![agile-ui](docs/images/agile-ui.png)

Agile UI follows 'best development practices' and looks to create an eco-system of 3rd party "UI components" that follow the Web UI standard solving issues such as:

- Incompatible HTML code produced by and excessive CSS/JS assets
- Differences in UI styles between your main theme and add-on UI
- Extensibility standard of all UI components based on principles of Dependency Injection, Template Injection and Inheritance
- Full control over JavaScript events and integration with jQuery and its plugins
- Controlled access between UI componets and domain model data with persistence abstraction
- **And most importantly: a responsive and modern interface based on Semantic UI CSS**

## Q&A

**Q: HTML-generating frameworks are lame and inefficient, real coders prefer to manually write HTML/CSS in Twig or Smarty.**

Agile UI was created for those who are in a hurry and not immediately concerned about the shades of their UI buttons. We have created a solid looking UI and diverse set of components that, like all applications, can be adapted and released with any UI.

Our goal was to create an out-of-the-box UI which you can "use", not "reinvent". 

**Q: What about Angular-JS, VueJS and all the other JS frameworks?**

We went with the default pattern that allows you to write the entire application in ONE language: PHP.

However, the "component" in Agile UI does not conflict if you choose to use a different  JavaScript framework. We found that **jQuery** and its plug-ins are most suitable for our design patterns. However, you can build a highly interractive component that relies on a different JavaScript frameworks or principles.

**Q: I used "X" component framework and found it extremely limiting.**

In the past, many UI / Component frameworks have been unable to find a good balance between flexiblity and convenience. Some out-of-the-box CRUD systems are too generic while other Form-builders are just too overengineered and unattractive.

Agile UI follows these core principles in it's design:

-   Instead of focusing on generic HTML, create HTML for a specific CSS framework (Semantic UI)
-   Allow developers to use all the features of CSS framework without leaving PHP
-   No custom proprietary JS code. Keep all the HTML simple
-   Allow developers to customise or extend components
-   Keep Agile UI as a open-source project under MIT license

Following those principles gives us the perfect combination of flexibility, elegance and performance.

**Q: I prefer Bootstrap CSS (or other CSS) over Semantic UI**

We considered several CSS frameworks.  We decided to focus on Semantic UI implementation as our primary framework for the following reasons:

-   Great theming and customisation variables
-   Clear patterns in class definitions
-   Extensive selection of core components
-   jQuery and JavaScript API integrations

Bearing in mind the popularity of Bootstrap CSS, we are working towards an extension that will allow you to switch your entire UI between Semantic UI and Bootstrap in the future.

## List of core Features in Agile UI

While many UI toolkits focus on giving you ready-to-use advance components, we produced a foundation of basic ones as building blocks then create more advanced components that are easily integrated.

1.  Rendering HTML - Agile UI is about initializing UI objects then rendering them. Each component is driven by the UI logic and all play a vital part in the Render Tree.
2.  Templates - We know that as developer you want control. We offer the ability to create custom templates for custom Views to improve performance and memory usage.
3.  Composite Views - This allows View object to implement itself by relying on other Views.
4.  JavaScript actions - This technique is designed to bind PHP Views with generic JavaScript routines and also to eliminate the need for you to write custom JavaScript code (e.g. `('#myid').dropdown();`).
5.  JavaScript events - JS actions can be asigned to events. For instance, you can instruct a "Button" object to refresh your "Grid" object in a single line of PHP.
6.  Buttons, Fields, Dropdown, Boxes, Panels, Icons, Messages - All those basic Views can be used 'out-of-the-box' and are utilising principles described above to remain flexible and configurable.
7.  Callbacks - A concept where a client-side component's rendering can execute an AJAX request to its PHP code triggering a server-side event. Agile UI ensures full isolation and robustness with this approach.
8.  Agile Data - Integration with data and business logic framework allows you to structure your growing PHP application's business logic properly and conforming to best practices of web development.
9.  Form - Culmination of concepts "callbacks", "composite views" and reliance on a dozen basic views creates a very simple way to create a modern and flexible Form implementation. Capable of rendering, submitting it's own data back, handling errors and server-side validation just in a few lines of PHP code - Form is the most elegant "Form Builder" you will find for PHP.
10.  Grid and Lister - An interractive and extensible UI component for outputing your data as a table or as a formatted list. When you have a lot of records to render, those components will prove to be very performance efficient.
11.  Dialogs - Introduces ability for you to use pop-ups and JavaScript dialog frame to reveal underlying UI.
12.  CRUD - A composite view that combines Form, Dialogs and Grids to create a fully interractive interface for managing your Model Entity data. Extensible and compatible with various add-ons, you will find CRUD to be a most efficient way to build your Admin system.
13.  Layouts, Menus, Pages, Tabs, Accordion - Several objects to create a bigger picture and link together the entire UI of your application.
14.  Application - This is an abstract interface that can be implemented using your full-stack framework of choice. The "App" class is built for stand-alone applications.  For any other framework, a custom application class can be provided to make the entire UI section adjust accordingly.



[![Gitter](https://img.shields.io/gitter/room/atk4/atk4.svg?maxAge=2592000)](https://gitter.im/atk4/atk4?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)
