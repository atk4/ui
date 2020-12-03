
.. php:namespace:: Atk4\Ui

.. php:class:: Tabs

====
Tabs
====

Tabs implement a yet another way to organize your data. The implementation is based on: https://fomantic-ui.com/elements/icon.html.


Demo: https://ui.agiletoolkit.org/demos/tabs.php


Basic Usage
===========

Once you create Tabs container you can then mix and match static and dynamic tabs::

    $tabs = Tabs::addTo($app);


Adding a static content is pretty simple::

    LoremIpsum::addTo($tabs->addTab('Static Tab'));

You can add multiple elements into a single tab, like any other view.

.. php:method:: addTab($name, $action = null)

Use addTab() method to add more tabs in Tabs view. First parameter is a title of the tab.

Tabs can be static or dynamic. Dynamic tabs use :php:class:`VirtualPage` implementation mentioned above.
You should pass Closure action as a second parameter.

Example::

    $t = Tabs::addTo($layout);

    // add static tab
    HelloWorld::addTo($t->addTab('Static Tab'));

    // add dynamic tab
    $t->addTab('Dynamically Loading', function ($tab) {
        LoremIpsum::addTo($tab);
    });

Dynamic Tabs
============

Dynamic tabs are based around implementation of :php:class:`VirtualPage` and allow you
to pass a call-back which will be triggered when user clicks on the tab.

Note that tab contents are refreshed including any values you put on the form::

    $t = Tabs::addTo($app);

    // dynamic tab
    $t->addTab('Dynamic Lorem Ipsum', function ($tab) {
        LoremIpsum::addTo($tab, ['size'=>2]);
    });

    // dynamic tab
    $t->addTab('Dynamic Form', function ($tab) {
        $m_register = new \Atk4\Data\Model(new \Atk4\Data\Persistence_Array($a));
        $m_register->addField('name', ['caption'=>'Please enter your name (John)']);

        $form = Form::addTo($tab, ['segment'=>true]);
        $form->setModel($m_register);
        $form->onSubmit(function ($form) {
            if ($form->model->get('name') !== 'John') {
                return $form->error('name', 'Your name is not John! It is "'.$form->model->get('name').'". It should be John. Pleeease!');
            }
        });
    });


URL Tabs
========

.. php:method:: addTabUrl($name, $url)

Tab can load external URL or a different page if you prefer that instead of VirtualPage. This works similar to iframe::

    $t = Tabs::addTo($app);

    $t->addTabUrl('Terms and Condition', 'terms.html');

