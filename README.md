# Agile UI

[![Build Status](https://travis-ci.org/atk4/ui.png?branch=develop)](https://travis-ci.org/atk4/ui)
[![Code Climate](https://codeclimate.com/github/atk4/ui/badges/gpa.svg)](https://codeclimate.com/github/atk4/ui)
[![StyleCI](https://styleci.io/repos/68417565/shield)](https://styleci.io/repos/68417565)
[![Test Coverage](https://codeclimate.com/github/atk4/ui/badges/coverage.svg)](https://codeclimate.com/github/atk4/ui/coverage)
[![Version](https://badge.fury.io/gh/atk4%2Fui.svg)](https://packagist.org/packages/atk4/ui)

**Web UI Component library.**

If you feel that the PHP frameworks are too complex and too hard to learn, then you will find that Agile UI has a fresh perspective on how to build web apps. Motivation to build Agile UI was sparked by the 3 coinciding events:

-   Growing popularity of CSS frameworks (Semantic UI, Bootstrap CSS etc).
-   Wide varietty of PHP frameworks with many different standards and approaches.
-   The second coming of NoSQL and SemiSQL databases and Rest APIs.

Agile UI integrates with your favourite framework/application and lets you go one step higher on the ladder of abstraction. You no longer need to think in terms of GET/POST requests or HTML/JS output. Agile UI components allow you to generalize your UI code.

Scroll down to see example.

## Q&A

**Q: HTML-generating frameworks are lame and inefficient, real coders prefer to manually write HTML/CSS in Twig or Smarty.**

Agile UI was created for those who are in a hurry and are not very picky about the shades of their UI buttons. We make all the efforts to create a stunningly looking UI and diverse set of components.

Our goal is to create out-of-the-box UI which you can "use" not "reinvent". 

**Q: What about Angular-JS, VueJS and all the other JS frameworks?**

We went with the default pattern that allows you to write entire application in ONE language: PHP.

However, the "component" in Agile UI does not conflict with your desire to use a different  JavaScript framework. We found that **jQuery** and it's plug-ins are most suitable for our design patterns, however you can build a highly interractive component that relies on a different JavaScript frameworks or principles.

**Q: I used "X" component framework and found it extremely limiting.**

Many UI / Component frameworks in the past have been unable to find a good balance between flexiblity and convenience. Some out-of-the-box CRUD systems are too generic while other Form-builders are just too overengineered.

Agile UI follows these core principles in it's design:

-   Instead of focusing on generic HTML, create HTML for a specific CSS framework (Semantic UI).
-   Allow developer to use all the features of CSS framework without leaving PHP.
-   No custom proprietary JS code or huge. Keep all the HTML simple.
-   Allow developer to customise or extend components.
-   Keep Agile UI as a open-source project under MIT license.

Following those principles gives us just the perfect combination of flexibility, elegancy and performance.

**Q: I prefer Bootstrap CSS (or other CSS) over Semantic UI**

We have considered several CSS frameworks, but decided to focus on Semantic UI implementation as our primary framework for the following reasons:

-   Great theeming and customisation variables;
-   Clear patterns in class definitions;
-   Extensive selection of core componetns;
-   jQuery and JavaScript API integrations.

Being considerate towards the popularity of Bootstrap CSS we will be working towards an extension that could allow you to switch your entire UI between Semantic UI / Bootstrap in the future.

## Why?

Our motivation for creating Agile UI goes beyond just helping you to write HTML. We pursue to complete the following goals:

-   for a UI component, abstract opinionated decisions of a PHP Framework making any component cross-framework.
-   make base component set universally available to anyone so that they can make complex UI out of base components.
-   maintain a cross-framework platform where all of the 3rd party components can run safely and with minimum overheads.

By making Agile UI and Agile Data available to all the developers for free under MIT license we can also focus on building Commercial Add-ons that would compete with other UI toolkits while also encouraging and promoting use of open-source in commercial environment.

## What's included in Agile UI

While many UI toolkits focus on giving you ready-to-use advance components, we decided to start from the basic ones than create a more advanced ones using the basics.

1.  Rendering HTML - Agile UI is about initializing UI objects and then rendering them. Each component is driven by the UI logic but play it's vital part in the Render Tree.
2.  Templates - We know that as developer you want control. We give you ability to create custom templates for your custom Views to improve performance and memory usage.
3.  Composite Views - This allows View object to implement itself by relying on other Views.
4.  JavaScript actions - This technique is designed to bind PHP Views with generic JavaScript routines and also to eliminate the need for you to write custom JavaScript code (e.g. `('#myid').dropdown();`).
5.  JavaScript events - JS actions can be asigned to events. For instance you can instruct a "Button" object to refresh your "Grid" object in a single line of PHP.
6.  Buttons, Fields, Dropdown, Boxes, Panels, Icons, Messages - All those basic Views can be used out-of-the-box and are utilising principles described above to remain flexible and configurable.
7.  Callbacks - A concept where a client-side component's rendering can execute AJAX request to it's PHP code triggering a server-side event. Aglie UI ensures full isolation and robustness of this approach.
8.  Agile Data - Integration with data and business logic framework allows you to structure your growing PHP application's business logic properly and conforming to best practices of web development.
9.  Form - Culmination of concepts "callbacks", "composite views" and reliance on dozen of basic views creates a very simple way to create a modern and flexible Form implementation. Capable of rendering, submitting it's own data back, handling errors and server-side validation just in a few lines of PHP code - Form is the most elegant "Form Builder" you will find for PHP.
10.  Grid and Lister - An interractive and extensible UI component for outputing your data as a table or as a formatted list. When you have a lot of records to render, those components will proove to be very performance-efficient.
11.  Dialogs - Introduces ability for you to use pop-ups and JavaScript dialog frame to revealing underlying UI.
12.  CRUD - A composite view that combines Form, Dialogs and Grids to create a fully interractive interface for managing your Model Entity data. Extensible and compatible with various add-ons you will find CRUD to be a most efficient way to build your Admin system.
13.  Layouts, Menus, Pages, Tabs, Accordion - Several objects to create a bigger picture and link together the entire UI of your application.
14.  Application - This is an abstract interface that can be implemented using your full-stack framework of choice. The "App" class is built for stand-alone applications, but for any other framework a custom application class can be provided to make the entire UI section adjust accordingly.

To demonstrate how the core components work, the next code snippet will show you how to build an entire web application in less than 30 lines of code:

``` php
$app = new \atk4\ui\App('Hello World');
$db = new \atk4\data\Persistence_SQL($dsn);

$app->initLayout('Fluid');

$app->menu->addItem(['Demo1', 'icon'=>'form'], ['demo'=>'form']);
$app->menu->addItem(['Demo2', 'icon'=>'crud'], ['demo'=>'crud']);

$this->layout->add('View', ['view box'])->set('Entire Web APP in 30 lines of code!');

$demo = $app->stickyGET('demo');
if ($demo == 'form') {
	$form = $this->layout->add('Form');
  	$form->setModel(new User($db), ['name', 'email']);
  	$form->onSubmit(function($form){
      	$form->model->save();
        return 'Created successfully';
  	});
} elseif ($demo == 'crud') {
  	$crud = $this->layout->add('CRUD');
  	$crud->setModel(new User($db));
}

echo $app->render();

class User extends \atk4\data\Model {
  	function init() {
      	parent::init();
      	$this->addField('name');
      	$this->addField('email', ['type'=>'email']);
      	$this->addField('created', ['type'=>'datetime', 'default'=>new DateTime()]);
  	}
}
```

LINK: Try this demo.

## Agile UI and your existing App

Agile UI was created to be usable inside your existing application. This could be a legacy app or another open-source app, it's always possible to integrate Agile UI into it.

That means -- NO REWRITING CODE. No letting go of the PHP framework you love. No hard decisions you might regret in the future.

1.  Install Agile UI through composer.
2.  Describe your Business Models through Agile Data.
3.  Select most appropriate Application class.
4.  Create any UI in minutes.

### Custom ORM / Template engine

Elegant design of Agile UI is only possible because we have designed many of the low-level components specifically for Agile UI. There were no suitable Template/ORM engines to fullfil the requirements, so we have created our own that follow these principles:

-   Very simple template engine that contains NO logic structures and would be extremely lightweight yet support recursive rendering.
-   Domain Model mapping framework with support for "Smart" fields (meta-information, data transformation, validation), "Conditions" (safely create CRUD for a sub-set of data records), "Only-Fields" (retrieve and store only sub-set of fields from a model), "Expressions" (define fields through expression and Database logic, e.g. aggregation).
-   Minimum dependencies, to make sure the code is cross-framework.
-   Offer many enterprise-level extensions and flexibility. 

Agile UI, Template and DSQL are all designed to fit well into Agile UI while also being fully-open source and licensed under MIT.

If you have to integrate with your own ORM or RestAPI, there are ways to do it correctly. Additionally - if you are creating your own components you can also rely on different Template engine as long as your component is consistent about rendering data.

Those features should enable you to restructure your applicaiton in such a way so that it's backwards compatible with your own older add-ons or legacy business logic implementation.

## Getting Started with Agile UI

Without doubt Agile UI offers A LOT.

We have designed for Agile UI to be friendly to beginners and those who need to get the job done. If you have used other programming languages and are new to PHP, even if you haven't built web apps in the past, Agile UI is a good learning investment than can quickly turn you into a very fast and efficient developer.

We have put together a Learning program that will teach you just the things you need to know. By completing this program you can acquire "Agile Toolkit Developer Certificate". Should you require additional assistance with your project, we offer a "Commercial Support Agreement" that  gives you access to dozens of commercial extensions, development assistance, training and advice.

However...

If you wish to learn Agile UI in-inside out, be capable of developing sophisticated add-ons and extensions by learning the inner-workings of the framework, we have made full documentation for Agile UI available. We can provide you with a further guidance and training to help you achieve the status of "Agile Toolkit Certified Partner". This makes your organisation eligible to advice others on how to integrate Agile Toolkit as well as allow you to create and distribute commercial add-ons and extensions through the plaftorm.

## Current Status

Agile UI is currently in the **late development stage**. Our development process is open to anyone and we welcome any curious person to join us in the Gitter chat:

[![Gitter](https://img.shields.io/gitter/room/atk4/atk4.svg?maxAge=2592000)](https://gitter.im/atk4/atk4?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)

## Roadmap

| Version    | Features                                 |
| ---------- | ---------------------------------------- |
| 0.1 [done] | Bootstrap the test-suite, continious integration and UI-testing. |
| 0.2 [done] | Implement template engine and a static "View". |
| 0.3 [done] | Implement JavaScript mechanics, integrate RequireJS, jQuery and Semantic UI |
| 0.4        | Implement URL mechanics and reloading    |
| 0.5 [done] | Implement standard set of UI elements - Button, Menu, Label, etc. |
| 0.6 [done] | Implement Form                           |
| 0.7        | Implement Grid                           |
| 0.8        | Implement CRUD                           |
| 0.9        | Implement real-time code execution (for consoles, progress-bars, spinners) |
