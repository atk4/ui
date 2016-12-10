# Agile UI

**Web UI Component library.**

All developers understand and agree with DRY (Don't Repeat Yourself) principle, yet web developers continue to copy snippets of HTML code no their Template files.

Component libraries such as Agile UI are designed to create Views (aka widgets) that can render themselves into HTML with purpose of providing consisteng UI.

There are 3 major requiremetns that Component Library must address in order to be viable:

-   Data access mechanics. 
    -   View need a reliable way to access data in your database.
    -   Use of SQL queries or ORM would require developer to provide "glue".
    -   Data Source should be abstracted.
-   Logic-less HTML template engine.
    -   PHP developers are mostly familiar with "smart" template engines like Smarty, Twig.
    -   View contains presentation logic, so it synergise better with logic-less template engines.
    -   Template engine must be designed to work with component framework
-   Developers must have no desire to tweak HTML code structure.
    -   You must be comfortable with CSS framework such as Symantic UI.
    -   CSS framework needs to support theeming and variables
    -   Component-driven design encourages consistent UI.

Agile UI solves call the requirements while remaining beautiful to web developer (code) and the user (ui).

## Show me the code

There are dozens of various UI elements in Agile UI. Most views are quite extensive and configurable, but they all work very well out of the box. The following code will place 2 CRUD tables on your page, that are fully interractive and integrate with your database fields, types and rules:

``` php
$v = new \atk4\ui\View();

$cr = $v->add(new \atk4\ui\CRUD())->setModel(new Order($db));
$cl = $v->add(new \atk4\ui\CRUD())->setModel(new Payment($db));

echo $cr->render();
```

Should be placed inside a `<body>` of a page that has Semantic UI included. For full page output, see next example.

![crud](docs/crud.png)

## Features of Agile UI

We try to stay ahead from other UI libraries and bring innovation to PHP apps, so Agile UI offers a lot of unique features:

-   Works anywhere. Place inside any framework or application and it will be fully funcitonal.
-   Client-side event mapping from PHP to jQuery.
-   Fully interractive. Any view can execute server-side PHP code through AJAX callbacks.
-   Fully context-aware. Support selective rendering. Fast performance.
-   Very lightweight and has minimum dependencies.
-   Reliable standard for designing portable UI widgets.
-   Extensible through 3rd party widgets, themes and data sources.

## Challenges solved by Agile UI

Creating a robust UI library is not a simple feat. Many have tried and failed. We have spent over a decade trying to get it right. The first public releases of AModules2 date back to 2004 followed by AModules3 and finally Agile Toolkit 4, which was released in 2011 as a full-stack framework.

Agile UI takes the best parts from Agile Toolkit to bring it to your favourite framework and it does that under MIT license.

-   No custom JavaScript libraries. Uses only jQuery and Semantic UI API.
-   No conflicts between multiple View on the same page. All Views have unique "id".
-   2-pass execution. View objects are established first, then rendering convers objects to HTML.
-   Support for JavaScript and Debug streams. Support for custom includes.
-   Implements "Virtual" pages that allow any View to create nested pages for pop-ups.
-   Automatic escaping and formatting of user data.
-   Support for connecting independent View tree branches.
-   Fully responsive HTML output that can also work without JavaScript.

## How can you use Agile UI

Well, start with a form:

``` php
require'vendor/loader.php';

$form = new \atk4\UI\Form();

$form->addField('name', ['required'=>true]);
$form->addField('surname');

$form->onSubmit(function($form){
  return "Hello, ".$form['name'];
});

$app = new \atk4\ui\App();
$app->add($form);
$app->run();
```

This will display a nicely looking form on a neatly looking page. Try submitting empty form. You will see validation in action and if you do submit form successfully, you will see a UI response, all without single page reload. So how does this work:

-   Form is a view, so it renders itself and its fields as HTML.
-   Default layout uses clean centered Layout and boilerplate HTML.
-   Form uses Semantic UI API to submit data dynamically.
-   Submission response carries JavaScript events. In our case it may display error or success message.

## What kind of interface can be created with Agile UI

Actually any interface can be done with Agile UI. If you ever feel a need to have your own HTML code anywhere, simply use a View with your own template. It will play nice with the rest of the objects.

### Data Access

Through [Agile Data](http://git.io/ad), you can use any SQL and NoSQL to load and store data. If you wish to list the files inside your S3 bucket, use this code:

``` php
$config = ['key'=>'..your key', 'secret'=>'..your secret'];

$bucket = new \atk4\s3\Persistence\Bucket('my-bucket', $config);
$file = new \atk4\s3\Model\File($bucket);

$file->setPath('/');

$tree = new \atk4\ui\View\Tree();
$tree->setModel($file);

$app = new \atk4\ui\App();
$app->add($tree);
$app->run();
```

This should automatically fetch the objects from your S3 drive and display them as a folder structure. There are many 3rd party persistence drivers and you can create more of your own.

### Layouts

You can use a professional app layout, that comes with some pre-initialized objects such as Menus, Panels and toolbars:

![layouts](docs/layouts.png)

Please note that in my next example I'm initializing application view first. It's not important how exactly you initialize views, as long as you don't forget to add them, you should be OK:

``` php
$app = new \atk4\ui\App();
$ui = $app->add(new \atk4\ui\Layout\TopMenu());

$m_file = $ui->menu->addMenu('File');
$m_file->addItems(['New..', 'Save', 'Save As..']);
$ui->menu->addItem(['Logout', 'icon'=>'exit', 'link'=>'logout.php']);

// Add some interractive elements  
$button = new \atk4\ui\Button(['Register', 'green']);
$lorem = new \atk4\ui\LoremIpsum();

$button->js('click', $lorem->reload());

$ui->add($lorem);
$ui->add($button, 'TopBar_Right');

$app->run();
```

When you execute "run()" method, Agile UI will automatically output standard-compliant HTML, load JavaScript / CSS dependencies and bind events.

## Standart UI Components and Extensions

Agile UI comes integrates with "3rd party extension" library where other developers can share "Views" that they have created. The extension library is designed to help you sit down with your client and browse through available widgets and re-use some of the code rather than re-writing those every time. 

Here is an example View for a "Login Form", sample 3rd party extension:

 ![login](docs/login.png)

All the Views (including 3rd party ones) follow these patterns:

-   Produce correct HTML code that fits into the style of your app.
-   Fully interractive out of the box.
-   Always re-use basic elements elements (Button, Forms)
-   Will work with your database (SQL or NoSQL)
-   You tweak or replace any template, or insert more views such as extra buttons etc.

## Security and Performance

When using 3rd party code, product owners have two major concerns: performance and security.

Agile Data addresses both through the use of "[Agile Data](http://git.io/ad)" - data access framework. To further improve security, there is a special extension that will enable ACL on per-object level for your domain model data.

## Based on Agile Toolkit Concepts

Tens of thousands of developers have already used Agile Toolkit 4.3 or earlier, which today is one of the [most popular PHP UI Frameworks](https://www.google.co.uk/search?q=php+ui+framework&ie=UTF-8&oe=UTF-8&gfe_rd=cr&ei=Na7iV8mbN8GBaK7Ju7AD). Unfortunatelly the current verison of Agile Toolkit does not play well with other frameworks or applications. 

Agile UI is a refactor of the core functionality of Agile Toolkit, designed to address the following problems:

- Can be used in any framework.
- Has minimum dependencies.
- Full documentation and test-code coverage.
- Makes extensive use of Semantic UI and jQuery.
- Improved performance, memory management and security.

Once Aglie UI is finished, it will be available in "Agile Toolkit 4.4", but also as a plugin into other popular PHP platforms - Wordpress, Drupal, Magento, Laravel, Yii.

As a developer you will get the benefit of accessing unified UI library regardless of which PHP framework you are using.

## Render Tree Concept

Agile Toolkit pioneered Render View Tree concept in 2011 and it's a fundamental principle of Agile UI framework today. It allows composing one view from another creating infinite posibilities. Here is a snippet demonstrating how to add a interactive Button inside a standard CRUD View:

``` php
$b = new Button(['Hello', 'icon' => 'bell']);
$b->on('click', $b->dialog('Hello', function($page){
     $page->add(new View('hello.jade'));
     $page->add(new Button('Close'))
           ->js()->univ()->closeDialog();
}));

$cr->add($b, ['spot' => 'toolbar']);
```

 ![crud2](docs/crud2.png)

We strive to create an extensive set of UI Views as well as educate 3rd party component developers to keep their components flexible and configurable. 

## Installation

To start using Agile UI in your project:

```
composer require atk4/ui
```

Depending on the framework/application platform that you are using, there might be an easier way to install Agile UI.

## Current Status

Agile UI is currently in the **early development stage**. Our development process is open to anyone and we welcome any curious person in our Gitter chat:

[![Gitter](https://img.shields.io/gitter/room/atk4/atk4.svg?maxAge=2592000)](https://gitter.im/atk4/atk4?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge) [![License](https://poser.pugx.org/atk4/ui/license)](https://packagist.org/packages/atk4/ui)



## Roadmap

| Version    | Features                                 |
| ---------- | ---------------------------------------- |
| 0.1 [done] | Bootstrap the test-suite, continious integration and UI-testing. |
| 0.2 [done] | Implement template engine and a static "View". |
| 0.3        | Implement JavaScript mechanics, integrate RequireJS, jQuery and Semantic UI |
| 0.4        | Implement URL mechanics and reloading    |
| 0.5        | Implement standard set of UI elements - Button, Menu, Label, etc. |
| 0.6        | Implement Form                           |
| 0.7        | Implement Grid                           |
| 0.8        | Implement CRUD                           |
| 0.9        | Implement real-time code execution (for consoles, progress-bars, spinners) |