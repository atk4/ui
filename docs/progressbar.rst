
.. php:namespace:: atk4\\ui

.. php:class:: ProgressBar

===========
ProgressBar
===========

ProgressBar is actually a quite simple element, but it can be made quite interractive along with
:php:class:`jsSSE`.

Demo: https://ui.agiletoolkit.org/demos/progressbar.php


Basic Usage
===========

.. php:method:: jsValue($value)

After adding a console to your :ref:`render_tree`, you just need to set a call-back::

    // Add progressbar showing 0 (out of 100)
    $bar = $app->add('ProgressBar');

    // Add with some other value of 20% and label
    $bar2 = $app->add(['ProgressBar', 20, '% Battery']);

The value of the progress bar can be changed either before rendering, inside PHP, or after rendering
with JavaScript::

    $bar->value = 5;  // sets this value instead of 0

    $app->add(['Button', 'charge up the battery'])->on('click', $bar2->jsValue(100));

Updating Progress in RealTime
=============================

You can use real-time element such as jsSSE or Console (which relies on jsSSE) to execute
jsValue() of your progress bar and adjust the display value.

Demo: https://ui.agiletoolkit.org/demos/progressbar.php

:php:class:`Console` also implements method :php:meth:`Console::send`  so you can use it to send progress
updates of your progress-bar.

