# Agile UI

[![Build Status](https://travis-ci.org/atk4/ui.png?branch=develop)](https://travis-ci.org/atk4/ui)
[![Code Climate](https://codeclimate.com/github/atk4/ui/badges/gpa.svg)](https://codeclimate.com/github/atk4/ui)
[![StyleCI](https://styleci.io/repos/68417565/shield)](https://styleci.io/repos/68417565)
[![Test Coverage](https://codeclimate.com/github/atk4/ui/badges/coverage.svg)](https://codeclimate.com/github/atk4/ui/coverage)
[![Version](https://badge.fury.io/gh/atk4%2Fui.svg)](https://packagist.org/packages/atk4/ui)

**Web UI Component library.**

Most PHP frameworks come with some HTML-builder code - Laravel, Symfony and Yii all offer developers a way to define HTML Form through PHP logic.

Agile UI explores and expands this pattern to all HTML produced by web applications creating a high-level server-side abstraction for the User Interface.

When Agile UI is used inside your app / framework, it offers the following:

- Creating objects for Forms, Grids, CRUDs, Menus, Buttons, Pages and Layouts through Object code
- Rendering the tree into HTML/JS
- Reading data from form submissions, button clicks and other interractions

Agile UI's role in your web app be best described as: 

> Out-of-the-box UI that you can "use", not "reinvent".

 ## Scope and Goals of Agile UI

What makes this UI toolkit stand out from the others is its commitment to bring rich and interractive web components that can be used for 90% of web applications without any custom-HTML/JS. Additionally, Agile UI provides a very controlled and consistent ways to develop "add-ons" that include visual components and other re-usable elements.

To achieve its goal, Agile UI offers both the tools for creating components and a wide selection of built-in components that provides the "minimum standard Web UI":

![agile-ui](docs/images/agile-ui.png)

We have developed Agile UI to be easy to integrate into existing PHP frameworks but, more importantly, **with your legacy applications**.

Agile UI follows 'best development practices' and looks to create an eco-system of 3rd party "UI components" that follow the Web UI standard solving issues such as:

- Incompatible HTML code produced by and excessive CSS/JS assets
- Differences in UI styles between your main theme and add-on UI
- Extensibility standard of all UI components based on principles of Dependency Injection, Template Injection and Inheritance
- Full control over JavaScript events and integration with jQuery and its plugins
- Controlled access between UI componets and domain model data with persistence abstraction
- **And most importantly: a responsive and modern interface based on Semantic UI CSS**

## Q&A

**Q: HTML-generating frameworks are lame and inefficient, real coders prefer to manually write HTML/CSS in Twig or Smarty.**

Agile UI was created for those who are in a hurry and are not very picky about the shades of their UI buttons. We make all the efforts to create a solid looking UI and diverse set of components that, like all applications, can be adapted and released with any UI.

Our goal was to create an out-of-the-box UI which you can "use", not "reinvent". 

**Q: What about Angular-JS, VueJS and all the other JS frameworks?**

We went with the default pattern that allows you to write the entire application in ONE language: PHP.

However, the "component" in Agile UI does not conflict with if you choose to use a different  JavaScript framework. We found that **jQuery** and its plug-ins are most suitable for our design patterns. However, you can build a highly interractive component that relies on a different JavaScript frameworks or principles.

**Q: I used "X" component framework and found it extremely limiting.**

In the past, many UI / Component frameworks have been unable to find a good balance between flexiblity and convenience. Some out-of-the-box CRUD systems are too generic while other Form-builders are just too overengineered and ugly looking.

Agile UI follows these core principles in it's design:

-   Instead of focusing on generic HTML, create HTML for a specific CSS framework (Semantic UI)
-   Allow developer to use all the features of CSS framework without leaving PHP
-   No custom proprietary JS code. Keep all the HTML simple
-   Allow developer to customise or extend components
-   Keep Agile UI as a open-source project under MIT license

Following those principles gives us the perfect combination of flexibility, elegancy and performance.

**Q: I prefer Bootstrap CSS (or other CSS) over Semantic UI**

We considered several CSS frameworks.  We decided to focus on Semantic UI implementation as our primary framework for the following reasons:

-   Great theming and customisation variables
-   Clear patterns in class definitions
-   Extensive selection of core components
-   jQuery and JavaScript API integrations

Bearing in mind the popularity of Bootstrap CSS, we are working towards an extension that will allow you to switch your entire UI between Semantic UI and Bootstrap in the future.

## List of main Features in Agile UI

While many UI toolkits focus on giving you ready-to-use advance components, we decided to start with basic ones as building blocks then create more advanced compoents that could be easily integrated.

1.  Rendering HTML - Agile UI is about initializing UI objects then rendering them. Each component is driven by the UI logic and all plays a vital part in the Render Tree.
2.  Templates - We know that as developer you want control. We offer you the ability to create custom templates for your custom Views to improve performance and memory usage.
3.  Composite Views - This allows View object to implement itself by relying on other Views.
4.  JavaScript actions - This technique is designed to bind PHP Views with generic JavaScript routines and also to eliminate the need for you to write custom JavaScript code (e.g. `('#myid').dropdown();`).
5.  JavaScript events - JS actions can be asigned to events. For instance, you can instruct a "Button" object to refresh your "Grid" object in a single line of PHP.
6.  Buttons, Fields, Dropdown, Boxes, Panels, Icons, Messages - All those basic Views can be used 'out-of-the-box' and are utilising principles described above to remain flexible and configurable.
7.  Callbacks - A concept where a client-side component's rendering can execute an AJAX request to it's PHP code triggering a server-side event. Agile UI ensures full isolation and robustness with this approach.
8.  Agile Data - Integration with data and business logic framework allows you to structure your growing PHP application's business logic properly and conforming to best practices of web development.
9.  Form - Culmination of concepts "callbacks", "composite views" and reliance on a dozen basic views creates a very simple way to create a modern and flexible Form implementation. Capable of rendering, submitting it's own data back, handling errors and server-side validation just in a few lines of PHP code - Form is the most elegant "Form Builder" you will find for PHP.
10.  Grid and Lister - An interractive and extensible UI component for outputing your data as a table or as a formatted list. When you have a lot of records to render, those components will prove to be very performance efficient.
11.  Dialogs - Introduces ability for you to use pop-ups and JavaScript dialog frame to reveal underlying UI.
12.  CRUD - A composite view that combines Form, Dialogs and Grids to create a fully interractive interface for managing your Model Entity data. Extensible and compatible with various add-ons, you will find CRUD to be a most efficient way to build your Admin system.
13.  Layouts, Menus, Pages, Tabs, Accordion - Several objects to create a bigger picture and link together the entire UI of your application.
14.  Application - This is an abstract interface that can be implemented using your full-stack framework of choice. The "App" class is built for stand-alone applications.  For any other framework, a custom application class can be provided to make the entire UI section adjust accordingly.

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

Agile UI was created to be usable inside your existing application. Whether it is a legacy app or another open-source app, it's always possible to integrate Agile UI into it.

That means -- NO REWRITING CODE. No letting go of the PHP framework you love. No hard decisions you might regret in the future.

1.  Install Agile UI through composer.
2.  Describe your Business Models through Agile Data.
3.  Select most appropriate Application class.
4.  Create any UI in minutes.

## Getting Started with Agile UI

Without a doubt, Agile UI offers A LOT.

We have designed Agile UI to be both 'beginner-friendly and for those who need to get the job done. If you have experience with other programming languages and are new to PHP or even if you haven't built web apps in the past, Agile UI is a good learning investment than can turn you into a very efficient developer.

We have put together a Learning program that will teach you just the things you need to know. By completing this program, you will acquire an "Agile Toolkit Developer Certificate". Should you require additional assistance with your project, we offer a "Commercial Support Agreement" that  gives you access to dozens of commercial extensions, development assistance, training and advice.

And if you wish to learn the inner-workings of Agile UI so you can develop sophisticated add-ons and extensions, we have full documentation for the framework. We can provide you with a further guidance and training to help you achieve the status of "Agile Toolkit Certified Partner". This makes your organisation eligible to advise others on how to integrate Agile Toolkit as well as allow you to create and distribute commercial add-ons and extensions through the plaftorm.

## Current Status

Agile UI is currently in the **late development stage**. Our development process is open to anyone and we welcome "the curious" to join us in the Gitter chat:

[![Gitter](https://img.shields.io/gitter/room/atk4/atk4.svg?maxAge=2592000)](https://gitter.im/atk4/atk4?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)

## Roadmap

| Version    | Features                                 |
| ---------- | ---------------------------------------- |
| 0.1 [done] | Bootstrap the test-suite, continious integration and UI-testing. |
| 0.2 [done] | Rendering HTML, Composite Views and Templates
| 0.3 [done] | JavaScript Actions, Callback, and Server Events
| 0.4        | Reloading of any arbitrary view
| 0.5        | Buttons, Fields, Dropdown, Boxes, Panels, Icons, Messages
| 0.6        | Form Implementation incuding Fields, Validation, Submission and Events
| 0.7        | Grid, Lister and Card
| 0.8        | Dialogs and CRUD
| 0.9        | Layouts, Wizard, Menus, Pages, Routing, Tabs, Accordion |
| 1.0        | Finish standalone App class as well as interface for integration with 3rd party frameworks
