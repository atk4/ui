
.. _text:

==========
LoremIpsum
==========

.. php:namespace:: atk4\ui

.. php:class:: LoremIpsum

This class implements a standard filler-text which is so popular amongst web developers and designers.
LoremIpsum will generate a dynamic filler text which should help you test :ref:`reloading` or layouts.

Basic Usage
===========

    $app->add('LoremIpsum');

Resizing
========

You can define the length of the LoremIpsum text::

    $text = $app->add('Text')
        ->addParagraph('First Paragraph')
        ->addParagraph('Second Paragraph');


You may specify amount of text to be generated with lorem::

    $app->layout->add(['LoremIpsum', 1]); // just add a little one

    // or

    $app->layout->add(new LoremIpsum(5)); // adds a lot of text


