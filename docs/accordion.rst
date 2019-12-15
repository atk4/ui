
.. php:namespace:: atk4\ui

.. php:class:: Accordion

=========
Accordion
=========

Accordion implement another way to organise your data. The implementation is based on: https://fomantic-ui.com/modules/accordion.html.


Demo: https://ui.agiletoolkit.org/demos/accordion.php


Basic Usage
===========

Once you create an Accordion container you can then mix and match static and dynamic accodion section::

    $acc = $app->add('Accordion');


Adding a static content section is pretty simple::

    $acc->addSection('Static Tab')->add('LoremIpsum');

You can add multiple elements into a single accordion section, like any other view.

.. php:method:: addSection($name, $action = null, $icon = 'dropdown')

Use addSection() method to add more section in an Accordion view. First parameter is a title of the section.

Section can be static or dynamic. Dynamic sections use :php:class:`VirtualPage` implementation mentioned above.
You should pass callable action as a second parameter.

Example::

    $t = $layout->add('Accordion');

    // add static section
    $t->addSection('Static Content')->add('HelloWorld');

    // add dynamic section
    $t->addSection('Dynamically Loading', function ($section) {
        $section->add('LoremIpsum');
    });

Dynamic Accordion Section
=========================

Dynamic sections are based around implementation of :php:class:`VirtualPage` and allow you
to pass a call-back which will be triggered when user clicks on the section title.::

    $acc = $app->add('Accordion');

    // dynamic section
    $acc->addSection('Dynamic Lorem Ipsum', function ($section) {
        $section->add(['LoremIpsum', 'size'=>2]);
    });

Controlling Accordion Section via Javascript
============================================

Accordion class has some wrapper method in order to control the accordion module behavior.

.. php:method:: jsOpen($section, $action = null)
.. php:method:: jsToggle($section, $action = null)
.. php:method:: jsClose($section, $action = null)

For example, you can set a button that, when clicked, will toggle an accordion section::

    $btn = $bar->add(['Button', 'Toggle Section 1']);

    $acc = $app->add(['Accordion', 'type' => ['styled', 'fluid']]);
    $section1 = $acc->addSection('Static Text')->add('LoremIpsum');
    $section2 = $acc->addSection('Static Text')->add('LoremIpsum');

    $btn->on('click', $acc->jsToggle($section_1));

Accordion Module settings
=========================

It is possible to change Accordion module settings via the settings property.::

    $app->add(['Accordion', 'settings' => []]);

For a complete list of all settings for the Accordion module, please visit: https://fomantic-ui.com/modules/accordion.html#/settings
