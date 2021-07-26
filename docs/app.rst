

.. _app:


Purpose of App class
====================

.. php:namespace:: Atk4\Ui
.. php:class:: App

App is a mandatory object that's essential for Agile UI to operate. If you don't create App object explicitly, it
will be automatically created if you execute `$component->invokeInit()` or `$component->render()`.

In most use-scenarios, however, you would create instance of an App class yourself before other components::

    $app = new \Atk4\Ui\App('My App');
    $app->initLayout([\Atk4\Ui\Layout\Centered::class]);
    LoremIpsum::addTo($app);

As you add one component into another, they will automatically inherit reference to App class. App
class is an ideal place to have all your environment configured and all the dependencies defined that
other parts of your applications may require.

Most standard classes, however, will refrain from having too much assumptions about the App class,
to keep overall code portable.

There may be some cases, when it's necessary to have multiple $app objects, for example if you are
executing unit-tests, you may want to create new App instance. If your application encounters
exception, it will catch it and create a new App instance to display error message ensuring that the
error is not repeated.

Using App for Injecting Dependencies
------------------------------------
Since App class becomes available for all objects and components of Agile Toolkit, you may add
properties into the App class::

    $app->db = new \Atk4\Ui\Persistence_SQL($dsn);

    // later anywhere in the code:

    $m = new MyModel($this->getApp()->db);

.. IMPORTANT:: $app->db is NOT a standard property. If you use this property, that's your own convention.

Using App for Injecting Behavior
--------------------------------
You may use App class hook to impact behavior of your application:

 - using hooks to globally impact object initialization
 - override methods to create different behavior, for example url() method may use advanced router logic
   to create beautiful URLs.
 - you may re-define set-up of :php:class:`Persistence\Ui` and affect how data is loaded from UI.
 - load templates from different files
 - use a different CDN settings for static files


Using App as Initializer Object
-------------------------------
App class may initialize some resources for you including user authentication and work with session.
My next example defines property `$user` and `$system` for the app class to indicate a system which is currently
active. (See :ref:`system_pattern`)::

    class Warehouse extends \Atk4\Ui\App
    {
        public $user;
        public $company;

        function __construct($auth = true) {
            parent::__construct('Warehouse App v0.4');

            // My App class will establish database connection
            $this->db = new \Atk4\Data\Persistence_SQL($_CLEARDB_DATABASE_URL['DSN']);
            $this->db->setApp($this);

            // My App class provides access to a currently logged user and currently selected system.
            $this->user = new User($this->db);
            $this->company = new Company($this->db);
            session_start();

            // App class may be used for pages that do not require authentication
            if (!$auth) {
                $this->initLayout([\Atk4\Ui\Layout\Centered::class]);
                return;
            }

            // Load User from database based on session data
            if (isset($_SESSION['user_id'])) {
                $this->user = $this->user->tryLoad($_SESSION['user_id']);
            }

            // Make sure user is valid
            if(!$this->user->loaded()) {
                $this->initLayout([\Atk4\Ui\Layout\Centered::class]);
                Message::addTo($this, ['Login Required', 'error']);
                Button::addTo($this, ['Login', 'primary'])->link('index.php');
                exit;
            }

            // Load company data (System) for present user
            $this->company = $this->user->ref('company_id');

            $this->initLayout([\Atk4\Ui\Layout\Admin::class]);

            // Add more initialization here, such as a populating menu.
        }
    }

After declaring your Application class like this, you can use it conveniently anywhere::

    include'vendor/autoload.php';
    $app = new Warehouse();
    Crud::addTo($app)
        ->setModel($app->system->ref('Order'));


Quick Usage and Page pattern
----------------------------

A lot of the documentation for Agile UI uses a principle of initializing App object first, then, manually
add the UI elements using a procedural approach::

    HelloWorld::addTo($app);

There is another approach in which your application will determine which Page class should be used for
executing the request, subsequently creating setting it up and letting it populate UI (This behavior is
similar to Agile Toolkit prior to 4.3).

In Agile UI this pattern is implemented through a 3rd party add-on for :ref:`page_manager` and routing. See also
:php:meth:`App::url()`

Clean-up and simplification
---------------------------

.. php:method:: run()
.. php:attr:: run_called
.. php:attr:: is_rendering
.. php:attr:: always_run

App also does certain actions to simplify handling of the application. For instance, App class will
render itself automatically at the end of the application, so you can safely add objects into the `App`
without actually triggering a global execution process::

    HelloWorld::addTo($app);

    // Next line is optional
    $app->run();

If you do not want the application to automatically execute `run()` you can either set `$always_run` to false
or use :php:meth:`terminate()` to the app with desired output.

Exception handling
------------------

.. php:method:: caugthException
.. php:attr:: catch_exception

By default, App will also catch unhandled exceptions and will present them nicely to the user. If you have a
better plan for exception, place your code inside a try-catch block.

When Exception is caught, it's displayed using a Layout\Centered layout and execution of original application is
terminated.

Integration with other Frameworks
---------------------------------
If you use Agile UI in conjunction with another framework, then you may be using a framework-specific App class,
that implements tighter integration with the host application or full-stack framework.


.. php:method:: requireJs()

Method to include additional JavaScript file in page::

    $app->requireJs('https://code.jquery.com/jquery-3.1.1.js');
    $app->requireJs('https://cdnjs.cloudflare.com/ajax/libs/fomantic-ui/2.7.4/semantic.min.js');

Using of CDN servers is always better than storing external libraries locally.
Most of the time CDN servers are faster (cached) and more reliable.

.. php:method:: requireCss($url)

Method to include additional CSS style sheet in page::

    $app->requireCss('//fomantic-ui.com/dist/semantic.css');

.. php:method:: initIncludes()

Initializes all includes required by Agile UI. You may extend this class to add more includes.

.. php:method:: getRequestUrl()

Decodes current request without any arguments. If you are changing URL generation pattern, you
probably need to change this method to properly identify the current page. See :php:class:`App::url()`

Loading Templates for Views
---------------------------

.. php:method:: loadTemplate($name)

Views use :php:attr:`View::$defaultTemplate` to specify which template they are using. By default
those are loaded from `vendor/atk4/ui/template/semantic-ui` however by overriding this method,
you can specify extended logic.

You may override this method if you are using a different CSS framework.


Utilities by App
================

App provides various utilities that are used by other components.

.. php:method:: getTag()
.. php:method:: encodeAttribute()
.. php:method:: encodeHtml()

Apart from basic utility, App class provides several mechanisms that are helpful for components.

Sticky GET Arguments
--------------------

.. php:method:: stickyGet()
.. php:method:: stickyForget()

Problem: sometimes certain PHP code will only be executed when GET arguments are passed. For example,
you may have a file `detail.php` which expects `order_id` parameter and would contain a `Crud` component.

Since the `Crud` component is interactive, it may want to generate requests to itself, but it must also
include `order_id` otherwise the scope will be incomplete. Agile UI solves that with StickyGet arguments::

    $order_id = $app->stickyGet('order_id');
    $crud->setModel($order->load($order_id)->ref('Payment'));

This make sure that pagination, editing, addition or any other operation that Crud implements will always
address same model scope.

If you need to generate URL that respects stickyGet arguments, use :php:meth:`App::url()`.

See also :php:meth:`View::stickyGet`

Redirects
---------

.. php:method:: redirect(page)
.. php:method:: jsRedirect(page)

App implements two handy methods for handling redirects between pages. The main purpose for those is
to provide a simple way to redirect for users who are not familiar with JavaScript and HTTP headers
so well.  Example::

    if (!isset($_GET['age'])) {
        $app->redirect(['age'=>18]);
    }

    Button::addTo($app, ['Increase age'])
        ->on('click', $app->jsRedirect(['age'=>$_GET['age']+1]));

No much magic in these methods.

Database Connection
-------------------

.. php:property:: db

If your `App` needs a DB connection, set this property to an instance of `Persistence`.

    Example:

    $app->db = \Atk4\Data\Persistence::connect('mysql://user:pass@localhost/atk');

See `Persistence::connect <https://agile-data.readthedocs.io/en/develop/persistence.html?highlight=connect#associating-with-persistence>`

Execution Termination
---------------------

.. php:method:: terminate(output)

Used when application flow needs to be terminated preemptively. For example when
call-back is triggered and need to respond with some JSON.

You can also use this method to output debug data. Here is comparison to var_dump::

    // var_dump($my_var);  // does not stop execution, draws UI anyway

    $this->getApp()->terminate(var_export($my_var)); // stops execution.


Execution state
---------------

.. php:attr:: is_rendering

Will be true if the application is currently rendering recursively through the Render Tree.

Links
-----

.. php:method:: url(page)

Method to generate links between pages. Specified with associative array::

    $url = $app->url(['contact', 'from'=>'John Smith']);

This method must respond with a properly formatted url, such as::

    contact.php?from=John+Smith

If value with key 0 is specified ('contact') it will be used as the name of the page. By
default url() will use page as "contact.php?.." however you can define different behavior
through :ref:`page_manager`.

The url() method will automatically append values of arguments mentioned to `stickyGet()`,
but if you need URL to drop any sticky value, specify value explicitly as `false`.

.. php:method:: jsUrl(callback_page)

Use jsUrl for creating callback, which return non-HTML output. This may be routed differently
by a host framework (https://github.com/atk4/ui/issues/369).



Includes
--------

.. php:method:: requireJs($url)

Includes header into the <head> class that will load JavaScript file from a specified URL.
This will be used by components that rely on external JavaScript libraries.

Hooks
-----

Application implements HookTrait (https://agile-core.readthedocs.io/en/develop/hook.html)
and the following hooks are available:

 - beforeRender
 - beforeOutput
 - beforeExit

Hook beforeExit is called just when application is about to be terminated. If you are
using :php:attr:`App::$always_run` = true, then this hook is guaranteed to execute always
after output was sent. ATK will avoid calling this hook multiple times.

.. note:: beforeOutput and beforeRender are not executed if $app->terminate() is called, even
    if parameter is passed.


Application and Layout
======================

When writing an application that uses Agile UI you can either select to use individual components
or make them part of a bigger layout. If you use the component individually, then it will
at some point initialize internal 'App' class that will assist with various tasks.

Having composition of multiple components will allow them to share the app object::

    $grid = new \Atk4\Ui\Grid();
    $grid->setModel($user);
    $grid->addPaginator();          // initialize and populate paginator
    $grid->addButton('Test');       // initialize and populate toolbar

    echo $grid->render();

All of the objects created above - button, grid, toolbar and paginator will share the same
value for the 'app' property. This value is carried into new objects through AppScopeTrait
(https://agile-core.readthedocs.io/en/develop/appscope.html).

Adding the App
--------------

You can create App object on your own then add elements into it::

    $app = new App('My App');
    $app->add($grid);

    echo $grid->render();

This does not change the output, but you can use the 'App' class to your advantage as a
"Property Bag" pattern to inject your configuration. You can even use a different "App"
class altogether, which is how you can affect the default generation of links, reading
of GET/POST data and more.

We are still not using the layout, however.

Adding the Layout
-----------------

Layout can be initialized through the app like this::

    $app->initLayout([\Atk4\Ui\Layout\Centered::class]);

This will initialize two new views inside the app::

    $app->html
    $app->layout

The first view is a HTML boilerplate - containing HEAD / BODY tags but not the body
contents. It is a standard html5 doctype template.

The layout will be selected based on your choice - Layout\Centered, Layout\Admin etc. This will
not only change the overall page outline, but will also introduce some additional views.

Each layout, depending on it's content, may come with several views that you can populate.

Admin Layout
------------
.. php:namespace:: Atk4\Ui\Layout
.. php:class:: Admin

Agile Toolkit comes with a ready to use admin layout for your application. The layout is built
with top, left and right menu objects.

.. php:attr:: menuLeft

Populating the left menu object is simply a matter of adding the right menu items to the layout menu::

    $app->initLayout([\Atk4\Ui\Layout\Admin::class]);
    $layout = $app->layout;

    // Add item into menu
    $layout->menuLeft->addItem(['Welcome Page', 'icon' => 'gift'], ['index']);
    $layout->menuLeft->addItem(['Layouts', 'icon' => 'object group'], ['layouts']);

    $EditGroup = $layout->menuLeft->addGroup(['Edit', 'icon' => 'edit']);
    $EditGroup->addItem('Basics', ['edit/basic']);

.. php:attr:: menu

This is the top menu of the admin layout. You can add other item to the top menu using::

    Button::addTo($layout->menu->addItem(), ['View Source', 'teal', 'icon' => 'github'])
        ->setAttr('target', '_blank')->on('click', new \Atk4\Ui\JsExpression('document.location=[];', [$url.$f]));

.. php:attr:: menuRight

The top right dropdown menu.

.. php:attr:: isMenuLeftVisible

Whether or not the left menu is open on page load or not. Default is true.


Integration with Legacy Apps
----------------------------

If you use Agile UI inside a legacy application, then you may already have layout and some
patterns or limitations may be imposed on the app. Your first job would be to properly
implement the "App" and either modification of your existing class or a new class.

Having a healthy "App" class will ensure that all of Agile UI components will perform
properly.

3rd party Layouts
-----------------

You should be able to find 3rd party Layout implementations that may even be coming with
some custom templates and views. The concept of a "Theme" in Agile UI consists of
offering of the following 3 things:

 - custom CSS build from Fomantic UI
 - custom Layout(s) along with documentation
 - additional or tweaked Views

Unique layouts can be used to change the default look and as a stand-in replacement to
some of standard layouts or as a new and entirely different layout.

