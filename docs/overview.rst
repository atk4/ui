
.. _overview:

====================
Overview of Agile UI
====================

Agile UI is a PHP component framework for building User Interfaces entirely in PHP.
Although the components and Agile UI will typically on HTML, JavaScript, jQuery and 
CSS the goal of Agile UI is to abstract them away behind easy-to-use component object.

As a framework it's closely coupled with Agile Data (http://agile-data.readthedocs.io),
which abstricts away database interaction operations. The default UI template set
uses Semantic UI (https://semantic-ui.com) for presentation.

Agile UI is designed and built for Agile Toolkit (http://agiletoolkit.org/), which
is a backend platform for easily building data-heavy API / UI backends.

Agile UI Design Goals
=====================

Fundamentally, our goal for Agile UI is that a developer with a very basic understanding
of Web Technologies could compose a web application out of ready-made components
using a simple PHP language syntax.

If you seek to use React or AngularJS in your web application, you should hook it up
to the RestAPI interface provided by Agile Data / API server.

Then again, you could create your admin system simply by creating one PHP file::

    <?php
    require'vendor/autoload.php';

    // Define your data structure
    class User extends \atk4\data\Model {

        public $table = 'user';

        function init() {
            parent::init();

            // Persistence may not have structure, so we define here
            $this->addFields(['name', 'email']);
            $this->addField('password', ['type'=>'password']);
        }
    }

    // Create Application object and initialize Admin Layout
    $app = new \atk4\ui\App('Single file web application');
    $app->initLayout('Admin');

    // Connect to database and place a fully-interractive CRUD
    $db = new \atk4\data\Persistence_SQL($dsn);
    $app->layout->add(new \atk4\ui\CRUD())
        ->setModel(new User($db));

Through the course of this example, I am performing several core actions:

  - `$app` is an object representing your Web Application and abstracting
    all the input, output, error-handling and other technical implementation
    details of a standard web application.

    In most applications you would want to extend this class yourself. When
    integrating Agile UI with MVC framework, you would be using a different
    App class, that properly integrates that framework capabilities.
   
    For a :ref:`component` the App class provides level of abstraction and
    utility.

    For full documentation see :ref:`app`.

  - `$db` this is database persistence object. It may be a Database which is
    either SQL or NoSQL but can also be RestAPI, a cache or a pseudo-persistence.

    I have used Persistence_SQL class, which takes advantage of standard-compliant
    database server to speed up aggregation, multi-table and multi-record operations.

    For a :ref:`component` the Persistence class provides data storage abstraction
    through the use of a Model class.

    Agile Data has full documentation at http://agile-data.readthedocs.io.

  - `User` is a Model - a database-agnostic declaration of your business entity.
    Model object represents a data-set for a specific persistence and conditions.

    In our example, object is created representing all User records then passed
    into the CRUD :ref:`component`. 

    For a :ref:`component` the Model represents information about the structure
    and offers mechanism to retrieve, store and delete date from `$db` persistence.


  - `CRUD` is a :ref:`component` class. Particularly CRUD is bundled with Agile UI
    and implements out-of-the-box interface for displaynig data in a table format
    with operations to add, delete or edit record.

    Although it's not obvious from the code, CRUD relies on multiple other components
    such as :php:class:`Grid`, :php:class:`Form`, :php:class:`Menu`, :php:class:`Paginator`,
    :php:class:`Button`.


To sum this up, Agile UI:

 - Full abstraction of Web technologies through components.

 - Concise syntax to define UI layouts in PHP.

 - Built-in security and safety.

 - Decoupled from data storage/retrievel mechanism.

 - Designed to be integrated into full-stack frameworks.

 - Abstaining from duplicating field names, types or validation logic outside of Model
   class.


The best use cases for Agile UI include:

 - Creating admin backend UI for data entry and dashboards in shortest time and with
   minimum amount of code.

 - Building UI components which you are willing to use across multiple environments
   (Laravel, Wordpress, Drupal, etc)

 - Creating MVP prototype for Web Apps.


.. _component:

Component
=========
The component is a fundamental builing block of Agile UI. Each component is fully
self-sufficient and creating a class instance is enough to make component work.

That means that components may rely on each-other and even though some may appear
very basic to you, they are relied on by some other components for maximum
felxibility. The next example adds "Cancel" button to a form::

    $button = $form->add(new \atk4\ui\Button([
        'Cancel',
        'icon'=>new \atk4\ui\Icon('pencil')
    ]))->link('dashboard.php');


Usage
-----
In the main example above, component `GRID` was made part of application layout.

A more advanced integration may require you to render component individually:: 

    $form = new Form();
    $form->init();
    $form->setModel(new User($db));

    $html = $form->render();


This would render an individual component and will return HTML / JavaScript::

    <script>
      ..form submit callback setup..
    </script>
    <div class="ui form">
        <form id="atk_form">
           ... fields
           ... buttons
        </form>
    </div>

For more advanced ways to render components see :php:meth:`View::render()`

Factory
-------
Factory is a mechanism which allow you to use shorter syntax for creating objects.
Agile UI goal is to be simple to use and readable, so taking advantage of PHP language
having loose types allows us to use a shorter syntax::

    $form->add(['Button', 'Cancel', 'icon'=>'pencil'])
        ->link('dashboard.php');

By default classes specified as 1st element of array passed to the add() method are
resolved to namespace `atk4\ui`, however the application class can fine-tune the
search.

Usage of factory is optional. For more information see:
http://agile-core.readthedocs.io/en/develop/factory.html

Templates
---------
Components rely on :php:class:`Template` class for parsing and rendering their
HTML. The default template is written for Semantic UI framework, which makes sure
that elements will look good.


Layouts
-------
Using App class will utilise a minimum of 2 templates:

 - html.html - boilerplate HTML code (<head>, <script>, <meta> and empty <body>)
 - layout/admin.html - responsive layout containing page elements (menu, footer, etc)

As you add more components, they will appear inside your layout.

You'll also find that layout class such as :php:class:`Layout\Admin` is initializing
some components on its own - sidebar menu, top menu.

Having those components initializes allows framework to make use of application
layout. for instance, authentication contoller may want to put a user-menu on the
right-hand side of your top menu (with logout link).


Advanced techniques
===================
By design we make sure that adding component into a Render Tree (See :ref:`view`)
is enough, so App provides a mechanism for components to:

 - Depending on JS, CSS and other assets
 - Define event handlers and actions
 - Handle callbacks

Non-PHP dependencies
--------------------
Your componet may depend on additional JavaScript library, CSS or other files.
At a present time you have to make them available through CDN and HTTPS.
See: :php:meth:`App::requireJS`


Events and Actions
------------------
Agile UI allows you to initiate come JavaScript actions from inside PHP. The
amount of application is quite narrow and is only intended for binding events
between the components inside your component without involving developers
who use your component in this process.

Callbacks
---------
Some actions can be done only on the server side. For example adding a new
record into the database.

Agile UI allows for component to do just that without no extra effort from
you (such as setting up API routes). To make this possible, a component
must be able to use unique URLs which will trigger the call-back.

To see how this is implemented, read about :ref:`callback`

Virtual Pages
-------------
.. image:: images/ui-component-diagram.png
    :width: 30%
    :align: right

Extending the concept of Callbacks, you can also define Virtual Pages. It
is a dynamically generated URL which will respond with a partial render of
your components.

Virtual Pages are useful for displaying UI on dynamic dialogs. As with
everything else, virtual pages can be contained within the components so
that no extra effort from you is required when component wishes to use
dynamic modal dialog.

Extending with Add-ons
----------------------
Agile UI is designed for data-agnostic UI components which you can add inside
your application with a single line of code, but Agile Toolkit goes one way
further by offering you a directory of published add-ons and install them
by using a simple wizard.


Using Agile UI
==============
Technologies advance forward to make it simpler and faster to build web
apps. In some cases you can use ReactJS + Firebase but in most cases
you will need to have a backend.

Agile Data is a most powerful framework for defining data-driven business
model and Agile UI offers a very straightforward extension to attach your
data to a wide range of stardard UI widgets.

With this approach even the most complex business apps can be implemented
in one day.

You can still implement ReactJS application by connecting it to the RestAPI
endpoint provided by Agile Toolkit.

.. warning:: information on setting up API endpoints is coming soon.

Learning Agile Toolkit
----------------------

We recommend that you start looking at Agile UI first. Continue reading through the
:ref:`quickstart` section and try building some basic apps. You will need to
have a basic understanding of "code" and some familiarity with PHP language.


 - QuickStart - 20-minute read and some code examples you can try.
 - Core Concept - Read if you plan to design and build your own components.

   - Patterns and Principles
   - Views and common component properties/methods
   - Component Design and UI code refactoring
   - Injecting HTML Templates and Full-page Layouts
   - JavaScript Event Bindings and Actions
   - App class and Framework Integration
   - Usage Patterns

 - Components - Reference for UI component classes

   - Button, Label, Header, Message, Menu, Column
   - Table and TableColumn
   - Form and Field
   - Grid and CRUD
   - Paginator

 - Advanced Topics


If you are not interested in UI and only need Rest API, we recommend that you look
into documentation for Agile Data (http://agile-data.readthedocs.io) and the
Rest API extension (coming soon).

Application Tutorials
---------------------

We have wrote few working cloud applications ourselves with Agile Toolkit and are
offering you to look at their code. Some of them come with tutorials that teach you
how to build application step-by-step.

Education
---------

If you represent a group of students that wish to learn Agile Toolkit contact us
about our education materials. We offer special support for those that want to
learn how to develop Web Apps using Agile Toolkit.

Commercial Project Strategy
---------------------------

If you maintain a legacy PHP application and would like to have a free chat with
us about some support and assistance, do not hesitate to reach out.


What you DO NOT need to know
============================

Some technologies are "prerequirements" in other PHP frameworks, but Agile Toolkit
lets you develop a perfectly functional web application even if you are NOT familiar
with technologies like:

 - HTML and Asset Management
 - JavaScript, jQuery, NPM
 - CSS styling, LESS
 - Linux, Infrastructure, Docker
 - Rest API and JSON

We do recommend that you come back and learn those technologies **after** you have mastered
Agile Toolkit.

Database abstraction
--------------------

Agile Data offers abstraction of database servers and will use appropriate query
language to fetch your data. You may need to use SQL/NoSQL language of your database
for some more advanced usage cases.

Cloud deployment
----------------

There are also ways to deploy your application into the cloud without knowledge of
infrastructure, Linux and SSH. A good place to start is Heroku (https://www.heroku.com/).
We reference Heroku in our tutorials, but Agile Toolkit can work with any cloud
hosting that runs PHP apps.

