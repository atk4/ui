
.. php:namespace:: atk4\ui

.. php:class:: Tabs

====
Tabs
====

Tabs implement a yet another way to organise your data. The implementation is based on: http://semantic-ui.com/elements/icon.html.


Demo: http://ui.agiletoolkit.org/demos/tabs.php


Basic Usage
===========

Once you create Tabs container you can then mix and match static and dynamic tabs::

    $tabs = $app->add('Tabs');


Adding a static conten is pretty simple::

    $tabs->addTab('Static Tab')->add('LoremIpsum');

You can add multiple elements into a single tab, like any other view.

.. php:method:: addTab($name, $action = null)

Use addTab() method to add more tabs in Tabs view. First parameter is a title of the tab.

Tabs can be static or dynamic. Dynamic tabs use :php:class:`VirtualPage` implementation mentioned above.
You should pass callable action as a second parameter.

Example::

    $t = $layout->add('Tabs');

    // add static tab
    $t->addTab('Static Tab')->add('HelloWorld');

    // add dynamic tab
    $t->addTab('Dynamically Loading', function ($tab) {
        $tab->add('LoremIpsum');
    });

Dynamic Tabs
============

Dynamic tabs are based around implementation of :php:class:`VirtualPage` and allow you
to pass a call-back which will be triggered when user clicks on the tab.

Note that tab contents are refreshed including any values you put on the form::

    $t = $app->add('Tabs');

    // dynamic tab
    $t->addTab('Dynamic Lorem Ipsum', function ($tab) {
        $tab->add(['LoremIpsum', 'size'=>2]);
    });

    // dynamic tab
    $t->addTab('Dynamic Form', function ($tab) {
        $m_register = new \atk4\data\Model(new \atk4\data\Persistence_Array($a));
        $m_register->addField('name', ['caption'=>'Please enter your name (John)']);

        $f = $tab->add(new \atk4\ui\Form(['segment'=>true]));
        $f->setModel($m_register);
        $f->onSubmit(function ($f) {
            if ($f->model['name'] != 'John') {
                return $f->error('name', 'Your name is not John! It is "'.$f->model['name'].'". It should be John. Pleeease!');
            }
        });
    });


URL Tabs
========

.. php:method:: addTabURL($name, $url)

Tab can load external URL or a different page if you prefer that instead of VirtualPage. This works similar to iframe::

    $t = $app->add('Tabs');

    $t->addTabURL('Terms and Condition', 'terms.html');

