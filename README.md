# ATK UI is stable - Stop wasting your time

When using General Purpose PHP framework, a lot of time is spent on writing HTML, CSS, JS as well as planning routes, call-backs, adding interactivity and integrating your APIs or a database. 

**ATK UI is a high-level object-oriented PHP framework with 30+ popular UI components that seamlessly integrate with your SQL, NoSQL or API backend.**

[![Build Status](https://travis-ci.org/atk4/ui.png?branch=develop)](https://travis-ci.org/atk4/ui)
[![Code Climate](https://codeclimate.com/github/atk4/ui/badges/gpa.svg)](https://codeclimate.com/github/atk4/ui)
[![StyleCI](https://styleci.io/repos/68417565/shield)](https://styleci.io/repos/68417565)
[![codecov](https://codecov.io/gh/atk4/ui/branch/develop/graph/badge.svg)](https://codecov.io/gh/atk4/ui)
[![Version](https://badge.fury.io/gh/atk4%2Fui.svg)](https://packagist.org/packages/atk4/ui)

Quick-Links: [Documentation](http://agile-ui.readthedocs.io). [Demo-site](https://ui.agiletoolkit.org). [ATK Data](https://github.com/atk4/ui). [Forum](https://forum.agiletoolkit.org/). [Chat](https://gitter.im/atk4/atk4). [Commercial support](https://www.agiletoolkit.org/contact). [Udemy Course](https://forum.agiletoolkit.org/t/udemy-com-atk-course-early-access-limited-time-free/413).

### Q: How ATK UI compares to Laravel/Wordpress/Symfony/.. ?

ATK UI does not compete with general-purpose frameworks, instead it attempts to "compliment" the existing eco-system with extensive set of standard-compliant HTML-generator components.

### Q: What does ATK UI accomplish?

CSS frameworks give developer option not to open a can-of-worms called "CSS" and create clean and responsive UI.

ATK UI takes this concept to the next level by connecting this UI to your database / api through a server-side PHP-only logic.

### Q: When is best to use ATK?

A most popular use for ATK UI is for building admin / backend systems. Some popular extensions can be a huge time-saver:

-   https://github.com/atk4/mastercrud - Create hierarchical CRUD system with integrated breadcrumb
-   https://github.com/atk4/filestore - Associate your database records with S3/Dropbox file-store.
-   https://github.com/atk4/login - Out-of-the-box authentication for your ATK Web App.
-   https://github.com/atk4/audit - Transparent data audit for ATK Data.

Add-ons above combine existing components (such as Form, CRUD, Bredcrumb) to create a high-level enterprise-level systems for data management.

There are also many in-house data management applications that are built with ATK.

### Q: Is ATK good for beginners?

Yes, it can save months or years of your learning time and you will be able to create a fully-functional Web Apps as per specification.

If you are an absolute beginner and have a half hour to spare, [watch this video demonstrating how to create a most basic PHP app from scratch that works with a database and publish it to the cloud](https://youtu.be/OCoK9ZQbt0E).

### Q: Why I haven't heard about ATK before?

The current version of ATK Data / ATK UI have been developed and published in 2016 / 2017. Some ideas presented by the frameworks are quite revolutionary, but are different to the standard development practices. (https://youtu.be/a3imXsrvpVk)

The adoption rate is growing and we are seeing a lot of new members in our community. It will take some more time for others to discover ATK. We are pleased that those who have tried ATK are extremely happy and are starting to gradually contribute more and more.

ATK is a FREE and open-source component framework and if you enjoy it and want to help, spread the ❤.

## Enough talk, show me the code!

[CRUD](http://ui.agiletoolkit.org/demos/crud.php) is a fully-interractive component that supports pagination, reloading, conditions, data formatting, sorting, quick-search, ordering, custom actions and modals, but at the same time is very easy to use: 

``` php
$crud = new \atk4\ui\CRUD();
$crud->setModel(new User($db));
$HTML = $crud->render();
```

If you don't need $HTML from only CRUD, why not use our [Layout](http://ui.agiletoolkit.org/demos/layouts.php) component? Here is a fully functioning example:

``` php
<?php
$app = new \atk4\ui\App('hello world');
$app->initLayout('Centered');
$app->dbConnect('mysql://user:pass@localhost/atk')

$app->add('CRUD')->setModel(new User($app->db));
```

CRUD is one of 30+ components that come bundled with ATK UI and all of them are easy to use.

If you have 1 minute of time, [download the stable bundle](https://www.agiletoolkit.org) and try some examples yourself.

## Callbacks. Callbacks everywhere!

In the conventional web application, you have to design and declare "routes", which can be used to render HTML or respond with JSON. Routes have to be connected to your front-end logic. 

One of the fundamental features of ATK is Callback - ability to dynamically generate a route then have JS part of the component invoke it. Thanks to this approach, code can be fluid, simple and readable:

``` php
$tabs = $app->add('Tabs');
$tab->addTab('Intro')->add(['Message', 'Other tabs are loaded dynamically!']);
$tab->addTab('Users', function($p) use($app) {
    
    // This tab is loaded dynamically, but also contains dynamic component
    $p->add('CRUD')->setModel(new User($app->db));
});
$tab->addTab('Settings', function($p) use($app) {
    
    // Second tab contains an AJAX form that stores itself back to DB.
    $m = new Settings($app->db);
    $m->load(2);
    $p->add('Form')->setModel($m);
});
```

## Semantic UI

We love and support Semantic UI CSS. All of the components in ATK UI rely on this awesome framework. In fact, there is [almost no CSS](https://github.com/atk4/ui/blob/develop/public/agileui.less) that we add or tweak. Perhaps we just suck at CSS and are much rather work on building some [awesome SaaS projects](https://saasty.io).

However, by no means we restrict or limit YOUR options at writing custom HTML, JS or CSS. It's [easy to make your own view or tweak an existing one in ATK](http://ui.agiletoolkit.org/demos/view.php). We offer a [flexible way to extend JS services](https://github.com/atk4/ui/tree/develop/js) or integrate custom Layouts and CSS.

## Wizard

That's one of the coolest components we've got (at the time of writing!):

![wizard](docs/images/wizard.png)

Try the demo:  http://ui.agiletoolkit.org/demos/wizard.php and think how many PHP frameworks could implement this wizard in under 100 lines of code? Here are some of the features included:

-   Multi-step wizard with ability to navigate forward and backward
-   Form with validation
-   Data memorization in the session
-   Table with column formatter, Messages
-   Real-time output console

ATK [does it in about 50 lines](https://github.com/atk4/ui/blob/develop/demos/wizard.php) and with no extra files, so consider it for your next "Web Installer Wizard".

## ATK UI is part of [Agile Toolkit](https://agiletoolkit.org/)

Comparing to some other CRUD / Admin builders, the UI components rely on a very powerful ATK Data framework, which can be also used separately and can be used to power your [RestAPI](https://github.com/atk4/api) end-points. 

See how ATK Data compares with other ORM engines and you'll understand why we choose it over some of the alternatives: http://socialcompare.com/en/comparison/php-data-access-libraries-orm-activerecord-persistence

To help you understand the real power behind ATK Data integration, look at this aggregation / reporting addon: https://github.com/atk4/report. Compared to any open-source report suites that you can find for PHP, this is the only implementation that relies on "Model Domain Logic" rather then SQL queries for expressing your report criteria and can be used for ANY component in ATK UI as well as addons, such as [Charts](https://github.com/atk4/chart). There are no performance implications, because all the expressions and aggregations are executed inside your database through the means of SQL.

## ATK is commercially supported

The MIT license gives you absolute freedom, but no warranty. To compliment that, the team who created ATK as well as some early contributors joined together to run a consultancy company. We help you deliver your projects by:

-   Fixing bugs in ATK or add-ons - free of charge
-   Building add-ons that extend functionality - moderate hourly rate fee.
-   Integration tasks or building parts of your project - quotation based.

Our motto is to "always give back to open-source community and be fair to our clients". We are hiring PHP and JavaScript developers who are passionate about ATK and are active within our community.

# Getting Started

If you are new to PHP and Development download bundle of Agile UI  from www.agiletoolkit.org that includes some examples and dependencies, and check our our [Udemy course](https://forum.agiletoolkit.org/t/udemy-com-atk-course-early-access-limited-time-free/413). 

Those who are confident with composer should use:  `composer require atk4/ui`.

Start with components such as [CRUD](http://ui.agiletoolkit.org/demos/crud.php), [Form](http://ui.agiletoolkit.org/demos/form3.php) and [Wizard](http://ui.agiletoolkit.org/demos/wizard.php).

## Try this: Build your admin

It's really easy to put together a complex Admin system, here is how. Add this code to a new PHP file (tweak it with your database details, table and fields):

``` php
<?php
  
  $app = new \atk4\ui\App('My App');
  $app->initLayout('Admin');
  $db = \atk4\data\Persistence::connect('mysql://user:pass@localhost/yourdb');

  class User extends \atk4\data\Model {
      public $table = 'user';
      function init() {
          parent::init();

          $this->addField('name');
          $this->addField('email', ['required'=>true]);
          $this->addField('password', ['type'=>'password']);
      }
  }

  $app->add('CRUD')->setModel(new User($db));
```

The result is here:

![](docs/images/admin-in-15-lines.png)

## What's new in 1.4

Last release of Agile UI has put emphasis on high-level components and real-time interactivity.:

-   Wizard - ideal for sign-up process
-   Login - add-on implementing authentication control
-   Console - real-time output tracking
-   ProgressBar - execute long process in PHP and show progress-bar to user
-   Upload - Form field for uploading files and images
-   AutoComplete - drop-in replacement for DropDowns
-   Password field - store passwords encrypted
-   Lister - show information as a list
-   Radio buttons - yet another alternative to a drop-down
-   Static data - provide data to Table in array.

## What's new in 1.3

Previous release has introduced:

-   [Loader](http://ui.agiletoolkit.org/demos/loader.php) which can be nested, carry arguments, integrate with events and more.
-   [Notifyer](http://ui.agiletoolkit.org/demos/notifyer.php) flashes a dynamic success/error message
-   [Modal View](http://ui.agiletoolkit.org/demos/modal2.php) and [Dynamic jsModal](http://ui.agiletoolkit.org/demos/modal.php) are similar but use different techniques for Dynamic Dialogs
-   [AutoComplete](http://ui.agiletoolkit.org/demos/autocomplete.php) is a new Form Field that will automatically traverse [referenced](http://agile-data.readthedocs.io/en/develop/references.html) Model and even open a Modal dialog for adding a new record. Very useful for web apps!
-   [jsSSE](http://ui.agiletoolkit.org/demos/sse.php) is an easy-to-use module for running background jobs in PHP and displaying progress visually through a Progress-bar or Console.

## Bundled and Planned components

Agile UI comes with many built-in components:

| Component                                                    | Description                                                  | Introduced |
| ------------------------------------------------------------ | ------------------------------------------------------------ | ---------- |
| [View](http://ui.agiletoolkit.org/demos/view.php)            | Template, Render Tree and various patterns                   | 0.1        |
| [Button](http://ui.agiletoolkit.org/demos/button.php)        | Button in various variations including icons, labels, styles and tags | 0.1        |
| [Input](http://ui.agiletoolkit.org/demos/field.php)          | Decoration of input fields, integration with buttons.        | 0.2        |
| [JS](http://ui.agiletoolkit.org/demos/button2.php)           | Assign JS events and abstraction of PHP callbacks.           | 0.2        |
| [Header](http://ui.agiletoolkit.org/demos/header.php)        | Simple view for header.                                      | 0.3        |
| [Menu](http://ui.agiletoolkit.org/demos/layout2.php)         | Horizontal and vertical multi-dimensional menus with icons.  | 0.4        |
| [Form](http://ui.agiletoolkit.org/demos/form.php)            | Validation, Interactivity, Feedback, Layouts, Field types.   | 0.4        |
| [Layouts](http://ui.agiletoolkit.org/demos/layouts.php)      | Admin, Centered.                                             | 0.4        |
| [Table](http://ui.agiletoolkit.org/demos/table.php)          | Formatting, Columns, Status, Link, Template, Delete.         | 1.0        |
| [Grid](http://ui.agiletoolkit.org/demos/grid.php)            | Toolbar, Paginator, Quick-search, Expander, Actions.         | 1.1        |
| [Message](http://ui.agiletoolkit.org/demos/message.php)      | Such as "Info", "Error", "Warning" or "Tip" for easy use.    | 1.1        |
| [Modal](https://ui.agiletoolkit.org/demos/modal.php)         | Modal dialog with dynamically loaded content.                | 1.1        |
| [Reloading](http://ui.agiletoolkit.org/demos/reloading.php)  | Dynamically re-render part of the UI.                        | 1.1        |
| [Actions](https://ui.agiletoolkit.org/demos/reloading.php)   | Extended buttons with various interactions                   | 1.1        |
| [CRUD](http://ui.agiletoolkit.org/demos/crud.php)            | Create, List, Edit and Delete records (based on Advanced Grid) | 1.1        |
| [Tabs](https://ui.agiletoolkit.org/demos/tabs.php)           | 4 Responsive: Admin, Centered, Site, Wide.                   | 1.2        |
| [Loader](http://ui.agiletoolkit.org/demos/loader.php)        | Dynamically load itself and contained components inside.     | 1.3        |
| [Modal View](http://ui.agiletoolkit.org/demos/modal2.php)    | Open/Load contained components in a dialog.                  | 1.3        |
| [Breadcrumb](http://ui.agiletoolkit.org/demos/breadcrumb.php) | Push links to pages for navigation. Wizard.                  | 1.4        |
| [ProgressBar](http://ui.agiletoolkit.org/demos/progress.php) | Interactive display of a multi-step PHP code execution progress | 1.4        |
| [Console](http://ui.agiletoolkit.org/demos/console.php)      | Execute server/shell commands and display progress live      | 1.4        |
| [Items and Lists](http://ui.agiletoolkit.org/demos/lister.php) | Flexible and high-performance way to display lists of items. | 1.4        |
| [Wizard](http://ui.agiletoolkit.org/demos/wizard.php)        | Multi-step, wizard with temporary data storing.              | 1.4        |
|                                                              |                                                              |            |

## Add-ons and integrations

Add-ons:

-   [MasterCRUD](https://github.com/atk4/mastercrud) - Create multi-level CRUD system with BreadCrumb
-   [Filestore](https://github.com/atk4/report) - Integration your Form with Flysystem, uploading and storing files
-   [User Authentication](https://github.com/atk4/login) - User Log-in, Registration and Access Control for Agile UI
-   [Charts add-on](https://github.com/atk4/chart) - Modern looking and free charts with [chartJS](http://chartjs.org)
-   [Audit for Models](https://github.com/atk4/audit) - Record all DB operations with Undo/Redo support for Agile Data
-   [Data for Reports](https://github.com/atk4/report) - Implement data aggregation and union models for Agile Data
-   [Schema and Migration](https://github.com/atk4/schema) - Tools to migrate your database structure

Integrations:

-   [Agile UI for Wordpress](https://github.com/ibelar/atk-wordpress) - Write Wordpress plugin using Agile UI
-   [Laravel Agile Data](https://github.com/atk4/laravel-ad) - ServiceProvider for Agile Data
-   .. more connectors wanted. If you are working to integrate Agile UI or Agile Data, please list it here (even if incomplete).

## Roadmap

Agile UI has still more stuff ahead:

### 1.5 - Locale and Translations

-   Make all the texts and error messages translateable
-   Add "Developer Console" into UI
-   ..





All bundled components are free and licensed under MIT license. They are installed together with Agile UI.

External and 3rd party components may be subject to different licensing terms.

## Documentation and Community

ATK UI makes active use of ATK Core and ATK Data frameworks.

-   [Agile UI Documentation](http://agile-ui.readthedocs.io)
-   [Agile Data Documentation](http://agile-data.readthedocs.io)
-   [Agile Core Documentation](http://agile-core.readthedocs.io)

## ATK UI Schematic

![agile-ui](docs/images/agile-ui.png)

## Credits and License

Agile UI, Data and API are projects we develop in our free time and offer you free of charge under terms of MIT license. If you wish to say thanks to our core team or take part in the project, please contact us through our chat on Gitter.


[![Gitter](https://img.shields.io/gitter/room/atk4/atk4.svg?maxAge=2592000)](https://gitter.im/atk4/atk4?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)
