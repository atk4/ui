# Agile UI - User Interface framework for Agile Toolkit

[Agile Toolkit](https://agiletoolkit.org/) is a Low Code framework written in PHP. Agile UI implement server side rendering engine and over 50 UI generic components for interacting with your Data Model.

Agile UI is quickest way for building back-end UI, admin interfaces, data management systems for medium and large projects designed around roles, complex logic, formulas.

 - Agile UI relies on abstract data. It could be stored in SQL, NoSQL or in external API.
 - Agile UI adjusts to your data model. If you change your model structure, UI will reflect that.
 - Agile UI offers out-of-the-box components, you don't need front-end development experience.
 - Agile UI is interactive, making it very easy to trigger PHP code on JS events.
 - Agile UI is compact - single file, several lines of code - that's all it takes.
 - Agile UI is extensible - integrates VueJS for custom components and interactive behaviours.

[![Build Status](https://travis-ci.org/atk4/ui.png?branch=develop)](https://travis-ci.org/atk4/ui)
![Build](https://github.com/atk4/ui/workflows/Unit%20Testing/badge.svg)
[![Code Climate](https://codeclimate.com/github/atk4/ui/badges/gpa.svg)](https://codeclimate.com/github/atk4/ui)
[![StyleCI](https://styleci.io/repos/68417565/shield)](https://styleci.io/repos/68417565)
[![codecov](https://codecov.io/gh/atk4/ui/branch/develop/graph/badge.svg)](https://codecov.io/gh/atk4/ui)
[![GitHub release](https://img.shields.io/github/release/atk4/ui.svg)](CHANGELOG.md)
[![Codefresh build status](https://g.codefresh.io/api/badges/pipeline/romaninsh/atk4%2Fui.agiletoolkit.org?type=cf-1)](https://g.codefresh.io/public/accounts/romaninsh/pipelines/new/5e0496e57ab3a7bb752ed818)

Quick-Links: [Documentation](https://agile-ui.readthedocs.io). [Namespaces](https://www.agiletoolkit.org/dox/namespaces.html). [Demo-site](https://ui.agiletoolkit.org). [ATK Data](https://github.com/atk4/data). [Forum](https://forum.agiletoolkit.org/). [Chat](https://gitter.im/atk4/atk4). [Commercial support](https://www.agiletoolkit.org/contact). [Udemy Course](https://forum.agiletoolkit.org/t/udemy-com-atk-course-early-access-limited-time-free/413).

Our localization is done using the amazing services of [Lokalise.com](https://www.lokalise.com) (Thanks)

## How does Agile Toolkit work?

The goal of Agile Toolkit is to reduce amount of coding to build general purpose web applications. There are
three steps involved:

 1. Define your "Data Model" through [Agile Data Framework](https://github.com/atk4/data) and associate with SQL, NoSQL or API.
 2. Initialize UI components, connecting them to Data Model to build User Interface for your application.
 3. If needed - Use [Agile API](https://github.com/atk4/api) to provide API access for your Mobile/React app or IoT devices.

Agile Data allows you to define models, fields, relations, formulas, aggregates, expressions, user action and
access control rules. Both Agile UI and Agile API will follow those rules. 

### Integrations and Apps using Agile UI

Agile UI can integrate with frameworks like Laravel or Symfony, has integration with Wordpress and there are several
high-level projects developed entirely on Agile Toolkit.

### Who uses Agile Toolkit?

Companies use Agile Toolkit to implement admin interface and in some cases even user-facing interface.

 - www.linkedfinance.com
 - www.sortmybooks.com
 - If you have a project built with Agile Toolkit - add it here!

### How does it work?

Download from www.agiletoolkit.org or Install ATK UI with `composer require atk4/ui`

Create "index.php" file with:

``` php
<?php
require_once __DIR__ . '/vendor/autoload.php';

$app = new \atk4\ui\App();   // That's your UI application
$app->initLayout('Centered');

$form = \atk4\ui\Form::addTo($app); // Yeah, that's a form!

$form->addField('email');    // adds field
$form->onSubmit(function ($form) {
    // implement subscribe here

    return $form->success('Subscribed ' . $form->model->get('email') . ' to newsletter.');
});

// Decorate anything
$form->buttonSave->set('Subscribe');
$form->buttonSave->icon = 'mail';

// everything renders automatically
```

Open PHP in the browser and observe a fully working and good looking form:

![subscribe](docs/images/subscribe.png)

ATK UI relies on https://fomantic-ui.com CSS framework to render the form beautifully. It also implements submission call-back in a very straightforward way. The demo also demonstrates use of JavaScript action, which can make objects interract with each-other (e.g. Form submit reloads Table).

### Database Integration with ATK Data

To get most of ATK UI, use [ATK Data](https://github.com/atk4/data) to describe your business models such as "User" or "Purchase". When you define models, you can start using some more advanced components:

[CRUD](https://ui.agiletoolkit.org/demos/crud.php) is a fully-interractive component that supports pagination, reloading, conditions, data formatting, sorting, quick-search, ordering, custom actions and modals, but at the same time is very easy to use:

``` php
$app = new \atk4\ui\App('hello world');
$app->initLayout('Admin');
$app->dbConnect('mysql://user:pass@localhost/atk')

\atk4\ui\CRUD::addTo($app)->setModel(new User($app->db));
```

ATK Data allows you to set up relations between models:

``` php
class User extends Model {
    function init(): void {
        parent::init();

        $this->addField('name');
        $this->addField('gender', ['enum'=>'female','male','other']);
        $this->hasMany('Purchases', new Purchase());
    }
}
```

Conventional CRUD works only with a single model, but with add-on you can take advantage this relationship information: https://github.com/atk4/mastercrud

``` php
use \atk4\mastercrud\MasterCRUD;

// set up $app here

$master_crud = MasterCRUD::addTo($app);
$master_crud->setModel(new User($app->db), [
  'Purchases'=>[]
]);

```

### Agile UI can be styled

It's easy to create your own application styling. Here are some example UI:

![subscribe](docs/images/saasty.png)

## Actions

As of version 2.0 - Agile Toolkit offers support for User Actions. Those are easy to define in your Data Model declaration:

``` php
$this->addAction('archive', function(Model $m) { $m->get('is_archived') = true; $this->saveAndUnload(); });
```

User interface such as CRUD or Card will automatically recognize new action and offer user to execute it. You can also
control who has permission to execute actions through our ACL system.

# Agile UI Feature highlights

Agile UI has some unique features:

## Callbacks. Callbacks everywhere!

One of the fundamental features of ATK is Callback - ability to dynamically generate a route then have JS part of the component invoke it. Thanks to this approach, code can be fluid, simple and readable:

``` php
$tabs = \atk4\ui\Tabs::addTo($app);
\atk4\ui\Message::addTo($tabs->addTab('Intro'), ['Other tabs are loaded dynamically!']);

$tabs->addTab('Users', function($p) use($app) {

    // This tab is loaded dynamically, but also contains dynamic component
    \atk4\ui\CRUD::addTo($p)->setModel(new User($app->db));
});

$tabs->addTab('Settings', function($p) use($app) {

    // Second tab contains an AJAX form that stores itself back to DB.
    $m = new Settings($app->db);
    $m->load(2);
    \atk4\ui\Form::addTo($p)->setModel($m);
});
```

## Wizard

Another component implementation using a very friendly PHP syntax:

![wizard](docs/images/wizard.png)

You get most benefit when you use various ATK UI Components together. Try the following demo: https://ui.agiletoolkit.org/demos/wizard.php. The demo implements:

-   Multi-step wizard with ability to navigate forward and backward
-   Form with validation
-   Data memorization in the session
-   Table with column formatter, Messages
-   Real-time output console

With ATK it [takes about 50 lines of PHP code only](https://github.com/atk4/ui/blob/develop/demos/wizard.php) to build it all.

## Getting Started: Build your admin

It's really easy to put together a complex Admin system. Add this code to a new PHP file (tweak it with your database details, table and fields):

``` php
<?php

$app = new \atk4\ui\App('My App');
$app->initLayout('Admin');
$app->dbConnect('mysql://user:pass@localhost/yourdb');

class User extends \atk4\data\Model {
    public $table = 'user';
    function init(): void {
        parent::init();

        $this->addField('name');
        $this->addField('email', ['required'=>true]);
        $this->addField('password', ['type'=>'password']);
    }
}

\atk4\ui\CRUD::addTo($app)->setModel(new User($app->db));
```

The result is here:

![](docs/images/admin-in-15-lines.png)

## Bundled and Planned components

Agile UI comes with many built-in components:

| Component                                                    | Description                                                  | Introduced |
| ------------------------------------------------------------ | ------------------------------------------------------------ | ---------- |
| [View](https://ui.agiletoolkit.org/demos/view.php)            | Template, Render Tree and various patterns                   | 0.1        |
| [Button](https://ui.agiletoolkit.org/demos/button.php)        | Button in various variations including icons, labels, styles and tags | 0.1        |
| [Input](https://ui.agiletoolkit.org/demos/field.php)          | Decoration of input fields, integration with buttons.        | 0.2        |
| [JS](https://ui.agiletoolkit.org/demos/button2.php)           | Assign JS events and abstraction of PHP callbacks.           | 0.2        |
| [Header](https://ui.agiletoolkit.org/demos/header.php)        | Simple view for header.                                      | 0.3        |
| [Menu](https://ui.agiletoolkit.org/demos/layout2.php)         | Horizontal and vertical multi-dimensional menus with icons.  | 0.4        |
| [Form](https://ui.agiletoolkit.org/demos/form.php)            | Validation, Interactivity, Feedback, Layouts, Field types.   | 0.4        |
| [Layouts](https://ui.agiletoolkit.org/demos/layouts.php)      | Admin, Centered.                                             | 0.4        |
| [Table](https://ui.agiletoolkit.org/demos/table.php)          | Formatting, Columns, Status, Link, Template, Delete.         | 1.0        |
| [Grid](https://ui.agiletoolkit.org/demos/grid.php)            | Toolbar, Paginator, Quick-search, Expander, Actions.         | 1.1        |
| [Message](https://ui.agiletoolkit.org/demos/message.php)      | Such as "Info", "Error", "Warning" or "Tip" for easy use.    | 1.1        |
| [Modal](https://ui.agiletoolkit.org/demos/modal.php)         | Modal dialog with dynamically loaded content.                | 1.1        |
| [Reloading](https://ui.agiletoolkit.org/demos/reloading.php)  | Dynamically re-render part of the UI.                        | 1.1        |
| [Actions](https://ui.agiletoolkit.org/demos/reloading.php)   | Extended buttons with various interactions                   | 1.1        |
| [CRUD](https://ui.agiletoolkit.org/demos/crud.php)            | Create, List, Edit and Delete records (based on Advanced Grid) | 1.1        |
| [Tabs](https://ui.agiletoolkit.org/demos/tabs.php)           | 4 Responsive: Admin, Centered, Site, Wide.                   | 1.2        |
| [Loader](https://ui.agiletoolkit.org/demos/loader.php)        | Dynamically load itself and contained components inside.     | 1.3        |
| [Modal View](https://ui.agiletoolkit.org/demos/modal2.php)    | Open/Load contained components in a dialog.                  | 1.3        |
| [Breadcrumb](https://ui.agiletoolkit.org/demos/breadcrumb.php) | Push links to pages for navigation. Wizard.                  | 1.4        |
| [ProgressBar](https://ui.agiletoolkit.org/demos/progress.php) | Interactive display of a multi-step PHP code execution progress | 1.4        |
| [Console](https://ui.agiletoolkit.org/demos/console.php)      | Execute server/shell commands and display progress live      | 1.4        |
| [Items and Lists](https://ui.agiletoolkit.org/demos/lister.php) | Flexible and high-performance way to display lists of items. | 1.4        |
| [Wizard](https://ui.agiletoolkit.org/demos/wizard.php)        | Multi-step, wizard with temporary data storing.              | 1.4        |
| [Actions](https://ui.agiletoolkit.org/demos/actions.php)      | Vizualization of user-defined actions              | 2.0        |
|                                                              |                                                              |            |

## Add-ons and integrations

Add-ons:

-   [MasterCRUD](https://github.com/atk4/mastercrud) - Create multi-level CRUD system with BreadCrumb
-   [Filestore](https://github.com/atk4/filestore) - Integrate your Form with Flysystem, uploading and storing files
-   [User Authentication and ACL](https://github.com/atk4/login) - User Log-in, Registration and Access Control for Agile UI
-   [Charts add-on](https://github.com/atk4/chart) - Modern looking and free charts with [chartJS](https://www.chartjs.org/)
-   [Audit for Models](https://github.com/atk4/audit) - Record all DB operations with Undo/Redo support for Agile Data
-   [Data for Reports](https://github.com/atk4/report) - Implement data aggregation and union models for Agile Data
-   [Schema and Migration](https://github.com/atk4/schema) - Tools to migrate your database structure

Integrations:

-   [Agile UI for Wordpress](https://github.com/ibelar/atk-wordpress) - Write Wordpress plugin using Agile UI
-   [Laravel Agile Data](https://github.com/atk4/laravel-ad) - ServiceProvider for Agile Data

All bundled components are free and licensed under MIT license. They are installed together with Agile UI.

External and 3rd party components may be subject to different licensing terms.

## Documentation and Community

ATK UI makes active use of ATK Core and ATK Data frameworks.

-   [Agile UI Documentation](https://agile-ui.readthedocs.io)
-   [Agile Data Documentation](https://agile-data.readthedocs.io)
-   [Agile Core Documentation](https://agile-core.readthedocs.io)

## ATK UI Schematic

![agile-ui](docs/images/agile-ui.png)

## Credits and License

Agile UI, Data and API are projects we develop in our free time and offer you free of charge under terms of MIT license. If you wish to say thanks to our core team or take part in the project, please contact us through our chat on Gitter.

[![Gitter](https://img.shields.io/gitter/room/atk4/atk4.svg?maxAge=2592000)](https://gitter.im/atk4/atk4?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)
