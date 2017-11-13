.. _text:

====
Text
====

.. php:namespace:: atk4\ui

.. php:class:: Text

Text is a component for abstracting several paragraphs of text. It's usage is simple and straightforward:

Basic Usage
===========

First argument of constructor or first element in array passed to constructor will be the text that will
appear on the label::

    $text = $app->add(['Text', 'here goes some text']);

Paragraphs
==========

You can define multiple paragraphs with text like this::

    $text = $app->add('Text')
        ->addParagraph('First Paragraph')
        ->addParagraph('Second Paragraph');

HTML escaping
=============

By default Text will not escape HTML so this will render as a bold text::

    $text = $app->add(['Text', 'here goes <b>some bold text</b>']);


.. warning:: If you are using Text for output HTML then you are doing it wrong. You should
    use a generic View and specify your HTML as a template.

When you use paragraphs, escaping is performed by default::

    $text = $app->add('Text')
        ->addParagraph('No alerts')
        ->addParagraph('<script>alert(1)</script>');

Usage
=====

Text is usable in generic components, where you want to leave possibility of text injection. For instance,
:php:class:`Message` uses text allowing you to add few parapraphs of text::

    $message = $app->add(['Message', 'Message Title']);
    $message->addClass('warning');

    $message->text->addParagraph('First para');
    $message->text->addParagraph('Second para');

Limitations
===========

Text may not have embedded elements, although that may change in the future.

