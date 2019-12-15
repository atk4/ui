

.. _view:

=====
Views
=====

Agile UI is a component framework, which follows a software patterns known as
`Render Tree` and `Two pass HTML rendering`.

.. php:namespace:: atk4\ui

.. php:class:: View

    A View is a most fundamental object that can take part in the Render tree. All
    of the other components descend from the `View` class.


View object is recursive. You can take one view and add another View inside of it::

    $v = new \atk4\ui\View(['ui'=>'segment', 'inverted']);
    $v->add(new \atk4\ui\Button(['Orange', 'inverted orange']));

The above code will produce the following HTML block:

.. code-block:: html

    <div class="ui inverted segment">
      <button class="ui inverted orange button">Orange</button>
    </div>

All of the views combined form a ``Render Tree``. In order to get the HTML output
from all the `Views` in `Render Tree` you need to execute ``render()`` for the top-most
leaf::

    echo $v->render();

Each of the views will automatically render all of the child views.



Initializing Render Tree
========================

Views use a principle of `delayed init`, which allow you to manipulate View objects
in any way you wish, before they will actuallized.

.. php:method:: add($object, $region = 'Content')

    Add child view as a parent of the this view.

    In addition to adding a child object, sets up it's template
    and associate it's output with the region in our template.

    Will copy $this->app into $object->app.

    If this object is initialized, will also initialize $object

    :param $object: Object or :ref:`seed` to add into render tree.
    :param $region: When outputing HTML, which region in :php:attr:`View::$template` to use.


.. php:method:: init()

    View will automatically execute an init() method. This will happen as soon as
    values for properties properties `app`, `id` and `path` can be determined.

    You should override `init` method for composite views, so that you can `add()`
    additional sub-views into it.

In the next example I'll be creating 3 views, but it at the time their __constructor
is executed it will be impossible to determine each view's position inside render tree::

    $middle = new \atk4\ui\View(['ui'=>'segment', 'red']);
    $top = new \atk4\ui\View(['ui'=>'segments']);
    $bottom = new \atk4\ui\Button(['Hello World', 'orange']);

    // not arranged into render-tree yet

    $middle->add($bottom);
    $top->add($middle);


    // Still not sure if finished adding

    $app = new \atk4\ui\App('My App');
    $app->setLayout($top);

    // Calls init() for all elements recursively.

Each View's `init()` method will be executed first before calling the same method for
child elements. To make your execution more straightforward we recommend you to create
App class first and then continue with Layout initialization::

    $app = new \atk4\ui\App('My App');
    $top = $app->setLayout(new \atk4\ui\View(['ui'=>'segments']));

    $middle = $top->add(new \atk4\ui\View(['ui'=>'segment', 'red']);

    $bottom = $middle->add(new \atk4\ui\Button(['Hello World', 'orange']);

Finally, if you prefer a more consise code, you can also use the following format::

    $app = new \atk4\ui\App('My App');
    $top = $app->setLayout('View', ['ui'=>'segments']);

    $middle = $top->add('View', ['ui'=>'segment', 'red']);

    $bottom = $middle->add('Button', ['Hello World', 'orange']);

The rest of documentaiton will use thi sconsise code to keep things readable, however if
you value type-hinting of your IDE, you can keep using "new" keyword. I must also
mention that if you specify first argument to add() as a string it will be passed
to `$app->factory()`, which will be responsible of instantiating the actual object.

(TODO: link to App:Factory)

Use of $app property and Dependency Injeciton
=============================================

.. php:attr:: app

    Each View has a property $app that is defined through \atk4\core\AppScopeTrait.
    View elements rely on persistence of the app class in order to perform Dependency
    Injection.

Consider the following example::

    $app->debug = new Logger('log');  // Monolog

    // next, somewhere in a render tree
    $view->app->debug->log('Foo Bar');

Agile UI will automatically pass your $app class to all the views.

Integration with Agile Data
===========================

.. php:method:: setModel($m)

    Associate current view with a domain model.

.. php:attr:: model

    Stores currently associated model until time of rendering.


If you have used Agile Data, you should be familiar with a concept of creating
Models::

    $db = new \atk4\data\Persistence_SQL::connect($dsn);

    $client = new Client($db);  // extends \atk4\data\Model();

Once you have a model, you can associate it with a View such as Form or Grid
so that those Views would be able to interact with your persistence directly::

    $form->setModel($client);

In most environments, however, your application will rely on a primary Database, which
can be set through your $app class::

    $app->db = new \atk4\data\Persistence_SQL::connect($dsn);

    // next, anywhere in a view
    $client = new Client($this->app->db);
    $form->setModel($client);

Or if you prefer a more consise code::

    $app->db = new \atk4\data\Persistence_SQL::connect($dsn);

    // next, anywhere in a view
    $form->setModel('Client');

Again, this will use `Factory` feature of your application to let you determine how
to properly initialize the class corresponding to string 'Client'.

UI Role and Classes
===================


.. php:method:: __construct($defaults = [])

    :param $defaults: set of default properties and classes.

.. php:attr:: ui

    Indicates a role of a view for CSS framework.



A constructor of a view often maps into a ``<div>`` tag that has a specific role
in a CSS framework. According to the principles of Agile UI, we support a
wide varietty of roles. In some cases, a dedicated object will exist, for
example a Button. In other cases, you can use a View and specify a UI role
explicitly::

    $view = $app->add('View', ['ui'=>'segment']);

If you happen to pass more key/values to the constructor or as second argument
to add() they will be treated as default values for the properties of that
specific view::

    $view = $app->add('View', ['ui'=>'segment', 'id'=>'test-id']);

For a more IDE-friendly format, however, I recommend to use the following syntax::

    $view = $app->add('View', ['ui'=>'segment']);
    $view->id = 'test-id';

You must be aware of a difference here - passing array to constructor will
override default property before call to `init()`. Most of the components
have been designed to work consistently either way and delay all the
property processing until the render stage, so it should be no difference
which syntax you are using.



If you are don't specify key for the properties, they will be considered an
extra class for a view::

    $view = $app->add('View', ['inverted', 'orange', 'ui'=>'segment']);
    $view->id = 'test-id';

You can either specify multiple classes one-by-one or as a single string
"inverted orange".

.. php:attr:: class

    List of classes that will be added to the top-most element during render.

.. php:method:: addClass($class)

    Add CSS class to element. Previously added classes are not affected.
    Multiple CSS classes can also be added if passed as space separated
    string or array of class names.

    :type $class: string|array
    :param $class: CSS class name or array of class names
    :returns: $this

.. php:method:: removeClass($remove_class)

    :param $remove_class: string|array one or multiple clases to be removed.

In addition to the UI / Role classes during the render, element will
receive extra classes from the $class property. To add extra class to
existing object::

    $button->addClass('blue large');

Classes on a view will appear in the following order: "ui blue large button"

Special-purpose properties
==========================

A view may define a special-purpose properties, that may modify how the
view is rendered. For example, Button has a property 'icon', that is implemented
by creating instance of \atk4\ui\Icon() inside the button.

The same pattern can be used for other scenarios::

    $button = $app->add('Button', ['icon'=>'book']);

This code will have same effect as::

    $button = $app->add('Button');
    $button->icon = 'book';

During the Render of a button, the following code will be executed::

    $button->add('Icon', ['book']);

If you wish to use a different icon-set, you can change Factory's route for 'Icon'
to your own implementation OR you can pass icon as a view::

    $button = $app->add('Button', ['icon'=>new MyAwesomeIcon('book'));


Rendering of a Tree
===================

.. php:method:: render()

    Perform render of this View and all the child Views recursively returning a valid HTML string.

Any view has the ability to render itself. Once executed, render will perform the following:

 - call renderView() of a current object.
 - call recursiveRender() to recursively render sub-elements.
 - returns ``<script>`` with on-dom-ready instructions along with rendering of a current view.

You must not override render() in your objects. If you are integrating Agile UI into your
framework you shouldn't even use ``render()``, but instead use ``getHTML`` and ``getJS``.

.. php:method:: getHTML()

    Returns HTML for this View as well as all the child views.

.. php:method:: getJS()

    Return array of JS chains that was assigned to current element or it's children.


Modifying rendering logic
=========================

When you creating your own View, you most likely will want to change it's rendering mechanics.
The most suitable location for that is inside ``renderView`` method.

.. php:method:: renderView()

Perform necessary changes in the $template property according to the presentation logic
of this view.

You should override this method when necessary and don't forget to execute parent::renderView()::

    function renderView() {
        if (str_len($this->info) > 100) {
             $this->addClass('tiny');
        }

        parent::renderView();
    }

It's important when you call parent. You wouldn't be able to affect template of a current view
anymore after calling renderView.

Also, note that child classes are rendered already before invocation of rederView. If you wish
to do something before child render, override method :php:meth:`View::recursiveRender()`

.. php:attr:: template

Template of a current view. This attribute contains an object of a class :php:class:`Template`.
You may secify this value explicitly::

    $app->add(['template'=>new \atk4\ui\Template('<b>hello</b>')]);

.. php:attr:: defaultTemplate

By default, if value of :php:attr:`View::$template` is not set, then it is loaded from class
specified in `defaultTemplate`::

    $app->add(['defaultTemplate'=>'./mytpl.html']);

You should specify defaultTemplate using relative path to your project root or, for add-ons,
relative to a current file::

    // in Add-on
    $app->add(['defaultTemplate'=>__DIR__.'/../templates/mytpl.httml']);

Agile UI does not currently provide advanced search path for templates, by default the
template is loaded from folder `vendor/atk4/ui/template/semantic-ui/`. To change this
behaviour, see :php:class:`App::loadTemplate()`.

.. php:attr:: region

Name of the region in the owner's template where this object
will output itself. By default 'Content'.


Here is a best practice for using custom template::

    class MyView extends View {
        public $template = 'custom.html';

        public $title = 'Default Title';

        function renderView() {
            parent::renderView();
            $this->template['title'] = $this->title;
        }

    }

As soon as the view becomes part of a render-tree, the Template object will also be allocated.
At this point it's also possible to override default template::

    $app->add(new MyView(), ['template'=>$template->cloneRegion('MyRegion')]);

Or you can set $template into object inside your constructor, in which case it will be left as-is.

On other hand, if your 'template' property is null, then the process of adding View inside RenderTree
will automatically clone region of a parent.

``Lister`` is a class that has no default template, and therefore you can add it like this::

    $profile = $app->add('View', ['template'=>'myview.html']);
    $profile->setModel($user);
    $profile->add('Lister', 'Tags')->setModel($user->ref('Tags'));

In this set-up a template ``myview.html`` will be populated with fields from ``$user`` model. Next,
a Lister is added inside Tags region which will use the contents of a given tag as a default
template, which will be repeated according to the number of referenced 'Tags' for given users and
re-inserted back into the 'Tags' region.

See also :php:class:`Template`.

Unique ID tag
=============

.. php:attr:: region

    ID to be used with the top-most element.

Agile UI will maintain unique ID for all the elements. The tag is set through 'id' property::

    $b = new \atk4\ui\Button(['id'=>'my-button3']);
    echo $b->render();

Outputs:

.. code-block:: html

    <div class="ui button" id="my-button3">Button</div>

If ID is not specified it will be set automatically. The top-most element of a Render Tree will
use ``id=atk`` and all of the child elements will create a derrived ID based on it's UI role.

.. code-block:: yaml

    atk:
        atk-button:
        atk-button2:
        atk-form:
            atk-form-name:
            atk-form-surname:
            atk-form-button:

If role is unspecified then 'view' will be used. The main benefit here is to have automatic
allocation of all the IDs througout the render-tree ensuring that those ID's are consistent
between page requests.

It is also possible to set the "last" bit of the ID postfix. When Form fields are populated,
the name of the field will be used instead of the role. This is done by setting 'name' propoerty.


.. php:attr:: name

    Specify a name for the element. If container already has object with specified name, exception
    will be thrown.

.. php:method:: getJSID

    Return a unique ID for a given element based on owner's ID and our name.

Example::

    $layout = new \atk4\ui\Layout(['id'=>'foo'])
    $butt = $layout->add('Button', ['name'=>'bar']);o

    echo $butt->getJSID();  // foo_bar


Reloading a View
================

.. php:method:: jsReload($get_arguments)

Agile UI makes it easy to reload any View on the page. Starting with v1.4 you can now use View::jsReload(),
which will respond with JavaScript Action for reloading the view::

    $b1 = $app->add(['Button', 'Click me']);
    $b2 = $app->add(['Button', 'Rand: '.rand(1,100)]);

    $b1->on('click', $b2->jsReload());

    // Previously:
    // $b1->on('click', new \atk4\ui\jsReload($b2));




Modifying Basic Elements
========================

TODO: Move to Element.



Most of the basic elements will allow you to manipulate their content, HTML attributes or even
add custom styles::

    $view->setElement('A');
    $view->addStyle('align', 'right');
    $view->addAttr('href', '





Rest of yet-to-document/implement methods and properties
========================================================


    .. php:attr:: skin

        protected

        Just here temporarily, until App picks it up


    .. php:attr:: content

        Set static contents of this view.


    .. php:method:: setProperties($properties)

        :param $properties:

    .. php:method:: setProperty($key, $val)

        :param $key:
        :param $val:



    .. php:method:: initDefaultApp()

        For the absence of the application, we would add a very
        simple one

    .. php:method:: set($arg1 = [], $arg2 = null)

        :param $arg1:
        :param $arg2:

    .. php:method:: recursiveRender()



