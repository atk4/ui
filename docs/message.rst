
.. _message:

=======
Message
=======

.. php:namespace:: atk4\ui

.. php:class:: Message

Outputs a rectangular segment with a distinctive color to convey message to the user, based around: https://fomantic-ui.com/collections/message.html

Demo: https://ui.agiletoolkit.org/demos/message.php

Basic Usage
===========

Implements basic image::

    $message = new \atk4\ui\Message('Message Title');
    $app->add($message);

Although typically you would want to specify what type of message is that::

    $message = new \atk4\ui\Message(['Warning Message Title', 'warning']);
    $app->add($message);

Here is the alternative syntax::

    $message = $app->add(['Message', 'Warning Message Title', 'warning']));

Adding message text
===================

.. php:attr:: text

Property $text is automatically initialized to :php:class:`Text` so you can call :php:meth:`Text::addParagraph`
to add more text inside your message::

    $message = $app->add(['Message', 'Message Title']);
    $message->addClass('warning');
    $message->text->addParagraph('First para');
    $message->text->addParagraph('Second para');


Message Icon
============

.. php:attr:: icon

You can specify icon also::

    $message = $app->add([
        'Message',
        'Battery low',
        'red',
        'icon'=>'battery low'
    ])->text->addParagraph('Your battery is getting low. Recharge your Web App');


