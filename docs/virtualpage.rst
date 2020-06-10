
VirtualPage Introduction
------------------------

Before learning about VirtualPage, Loader and other ways of dynamic content loading, you should fully
understand :ref:`callback`.


.. php:class:: VirtualPage

Unlike any of the Callback classes, VirtualPage is a legit :php:class:`View`, but it's behaviour is a little
"different". In normal circumstances, rendering VirtualPage will result in empty string. Adding VirtualPage
anywhere inside your :ref:`render_tree` simply won't have any visible effect::

    $vp = \atk4\ui\VirtualPage::addTo($layout);
    \atk4\ui\LoremIpsum::addTo($vp);

However, VirtualPage has a special trigger argument. If found, then VirtualPage will interrupt normal rendering
progress and output HTML of itself and any other Components you added to that page.

To help you understand when to use VirtualPage here is the example:

 - Create a :php:class:`Button`
 - Add VirtualPage inside a button.
 - Add Form inside VirtualPage.
 - Clicking the Button would dynamically load contents of VirtualPage inside a Modal window.

This pattern is very easy to implement and is used by many components to transparently provide dynamic functionality.
Next is an example where :php:class:`Tabs` has support for call-back for generating dynamic content for the tab::

    $tabs->addTab('Dynamic Tab Content', function($vp) {
        \atk4\ui\LoremIpsum::addTo($vp);
    });

Using VirtualPage inside your component can significantly enhance usability without introducing any complexity
for developers.

(For situations when Component does not natively support VirtualPage, you can still use :php:class:`Loader`, documented
below).

.. php:attr:: $cb

VirtuaPage relies on :php:class:`CallbackLater` object, which is stored in a property $cb. If the Calllback is triggered
through a GET argument, then VirtualPage will change it's rendering technique. Lets examine it in more detail::

    $vp = \atk4\ui\VirtualPage::addTo($layout);
    \atk4\ui\LoremIpsum::addTo($vp);

    $label = \atk4\ui\Label::addTo($layout);

    $label->detail = $vp->cb->getURL();
    $label->link($vp->cb->getURL());

This code will only show the link containing a URL, but will not show LoremIpsum text.  If you do follow the link, you'll
see only the 'LoremIpsum' text.

.. php:attr:: $urlTrigger

See :php:attr:`Callback::urlTrigger`.


Output Modes
^^^^^^^^^^^^

.. php:method:: getURL($mode = 'callback')

VirtualPage can be used to provide you either with RAW HTML content or wrap it into boilerplate HTML.
As you may know, :php:meth:`Callback::getURL()` accepts an argument, and VirtualPage gives this argument meaning:

- getURL('cut') gives you URL which will return ONLY the HTML of virtual page, no Layout or boilerplate.
- getURL('popup') gives you URL which will return a very minimalistic layout inside a valid HTML boilerplate, suitable for iframes or popup windows.

You can experement with::

    $label->detail = $vp->cb->getURL('popup');
    $label->link($vp->cb->getURL('popup'));

Setting Callback
^^^^^^^^^^^^^^^^

.. php:method:: set($callback)

Although VirtualPage can work without defining a callback, using one is more reliable and is always recommended::

    $vp = \atk4\ui\VirtualPage::addTo($layout);
    $vp->set(function($vp){
        \atk4\ui\LoremIpsum::addTo($vp);
    });

    $label = \atk4\ui\Label::addTo($layout);

    $label->detail = $vp->cb->getURL();
    $label->link($vp->cb->getURL());

This code will perform identically as the previous example, however 'LoremIpsum' will never be initialized
unless you are requesting VirtualPage specifically, saving some CPU time. Capability of defining callback
also makes it possible for VirtualPage to be embedded into any :ref:`component` quite reliably.

To illustrate, see how :php:class:`Tabs` component rely on VirtualPage, the following code::

    $t = \atk4\ui\Tabs::addTo($layout);

    \atk4\ui\LoremIpsum::addTo($t->addTab('Tab1')); // regular tab
    $t->addTab('Tab2', function($p){ \atk4\ui\LoremIpsum::addTo($p); }); // dynamic tab

.. php:method:: getURL($html_wrapping)

    You can use this shortcut method instead of $vp->cb->getURL().

.. php:attr:: $ui

When using 'popup' mode, the output appears inside a `<div class="ui container">`. If you want to change this
class, you can set $ui property to something else. Try::

    $vp = \atk4\ui\VirtualPage::addTo($layout);
    \atk4\ui\LoremIpsum::addTo($vp);
    $vp->ui = 'red inverted segment';

    $label = \atk4\ui\Label::addTo($layout);

    $label->detail = $vp->cb->getURL('popup');
    $label->link($vp->cb->getURL('popup'));





Loader
------

.. php:class:: Loader

.. php:method:: set()

Loader is designed to delay some slow-loading content by loading it dynamically, after main
page is rendered.

Comparing to VirtualPage which is a D.Y.I. solution - Loader can be used out of the box.
Loader extends VirtualPage and is quite similar to it.

Like with a VirtualPage - you should use `set()` to define content that will be loaded dynamically,
while a spinner is shown to a user::

    $loader = \atk4\ui\Loader::addTo($app);
    $loader->set(function($p) {

        // Simulate slow-loading component
        sleep(2);
        \atk4\ui\LoremIpsum::addTo($p);

    });


A good use-case example would be a dashboard graph. Unlike VirtualPage which is not visible to a regular render,
Loader needs to occupy some space.

.. php:attr:: $shim

By default it will display a white segment with 7em height, but you can specify any other view thorugh $shim
property::

    $loader = \atk4\ui\Loader::addTo($app, ['shim'=>['Message', 'Please wait until we load LoremIpsum...', 'red']]);
    $loader->set(function($p) {

        // Simulate slow-loading component
        sleep(2);
        \atk4\ui\LoremIpsum::addTo($p);

    });


Triggering Loader
^^^^^^^^^^^^^^^^^

By default, Loader will display a spinner and will start loading it's contents as soon as DOM Ready() event fires.
Sometimes you want to control the event.

.. php:method:: jsLoad($args = [])

Returns JS action which will trigger loading. The action will be carried out in 2 steps:

- loading indicator will be displayed
- JS will request content from $this->getURL() and provided by set()
- Content will be placed inside Loader's DIV replacing shiv (or previously loaded content)
- loading indicator will is hidden

.. php:attr:: $loadEvent = null

If you have NOT invoked jsLoad in your code, Loader will automatically assign it do DOM Ready(). If the automatic
behaviour does not work, you should set value for $loadEvent:

- null = load on DOM ready unless you have invoked jsLoad() in the code.
- true = load on DOM ready
- false = never load
- "string" - bind to custom JS event

To indicate how custom binding works::

    $loader = \atk4\ui\Loader::addTo($app, ['loadEvent' => 'kaboom']);

    $loader->set(function($p){
        \atk4\ui\LoremIpsum::addTo($p);
    });


    \atk4\ui\Button::addTo($app, ['Load data'])->on('click', $loader->js()->trigger('kaboom'));

This approach allow you to trigger loader from inside JavaScript easily. See also: https://api.jquery.com/trigger/

Reloading
^^^^^^^^^

If you execute :php:class:`jsReload` action on the Loader, it will return to original state.


Inline Editing Example
^^^^^^^^^^^^^^^^^^^^^^

Next example will display DataTable, but will allow you to repalce data with a form temporarily::


    $box = \atk4\ui\View::addTo($app, ['ui'=>'segment']);

    $loader = \atk4\ui\Loader::addTo($box, ['loadEvent'=>'edit']);
    \atk4\ui\Table::addTo($loader)
        ->setModel($data)
        ->addCondition('year', $app->stickyGet('year'));

    \atk4\ui\Button::addTo($box, ['Edit Data Settings'])->on('click', $loader->js()->trigger('edit'));

    $loader->set(function($p) use($loader) {
        $form = \atk4\ui\Form::addTo($p);
        $form->addField('year');

        $form->onSubmit(function($form) use ($loader) {
            return new \atk4\ui\jsReload($loader, ['year'=>$form->model->get('year')]);
        });
    });

Progress Bar
^^^^^^^^^^^^

.. php:attr:: $progressBar = null

Loader can have a progress bar. Imagine that your Loader has to run slow process 4 times::

    sleep(1);
    sleep(1);
    sleep(1);
    sleep(1);

You can notify user about this progress through a simple code::

    $loader = \atk4\ui\Loader::addTo($app, ['progressBar'=>true]);
    $loader->set(function($p) {

        // Simulate slow-loading component
        sleep(1);
        $p->setProgress(0.25);
        sleep(1);
        $p->setProgress(0.5);
        sleep(1);
        $p->setProgress(0.75);
        sleep(1);

        \atk4\ui\LoremIpsum::addTo($p);

    });

By setting progressBar to true, Loader component will use SSE (`Server Sent Events <https://www.w3schools.com/html/html5_serversentevents.asp>`_)
and will be sending notification about your progress. Note that currently Internet Explorer does not support SSE and it's
up to you to create a work-around.

Agile UI will test your browser and if SSE are not supported, $progressBar will be ignored.

