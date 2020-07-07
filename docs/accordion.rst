
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

    $acc = Accordion::addTo($app);


Adding a static content section is pretty simple::

    Loremipsum::addTo($acc->addSection('Static Tab'));

You can add multiple elements into a single accordion section, like any other view.

.. php:method:: addSection($name, $action = null, $icon = 'dropdown')

Use addSection() method to add more section in an Accordion view. First parameter is a title of the section.

Section can be static or dynamic. Dynamic sections use :php:class:`Virtualpage` implementation mentioned above.
You should pass callable action as a second parameter.

Example::

    $t = Accordion::addTo($layout);

    // add static section
    Helloworld::addTo($t->addSection('Static Content'));

    // add dynamic section
    $t->addSection('Dynamically Loading', function ($section) {
        Loremipsum::addTo($section);
    });

Dynamic Accordion Section
=========================

Dynamic sections are based around implementation of :php:class:`Virtualpage` and allow you
to pass a call-back which will be triggered when user clicks on the section title.::

    $acc = Accordion::addTo($app);

    // dynamic section
    $acc->addSection('Dynamic Lorem Ipsum', function ($section) {
        Loremipsum::addTo($section, ['size'=>2]);
    });

Controlling Accordion Section via Javascript
============================================

Accordion class has some wrapper method in order to control the accordion module behavior.

.. php:method:: jsOpen($section, $action = null)
.. php:method:: jsToggle($section, $action = null)
.. php:method:: jsClose($section, $action = null)

For example, you can set a button that, when clicked, will toggle an accordion section::

    $btn = Button::addTo($bar, ['Toggle Section 1']);

    $acc = Accordion::addTo($app, ['type' => ['styled', 'fluid']]);
    $section1 = Loremipsum::addTo($acc->addSection('Static Text'));
    $section2 = Loremipsum::addTo($acc->addSection('Static Text'));

    $btn->on('click', $acc->jsToggle($section_1));

Accordion Module settings
=========================

It is possible to change Accordion module settings via the settings property.::

    Accordion::addTo($app, ['settings' => []]);

For a complete list of all settings for the Accordion module, please visit: https://fomantic-ui.com/modules/accordion.html#/settings
