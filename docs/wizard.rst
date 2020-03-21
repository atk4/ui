

.. php:namespace:: atk4\ui

.. php:class:: Wizard

======
Wizard
======

Wizard is a high-level component, which makes use of callback to track step progression through the stages. It has an incredibly
simple syntax for building UI and display a lovely UI for you.

    .. image:: images/wizard.png


Demo: https://ui.agiletoolkit.org/demos/wizard.php

Introduced in UI v1.4


Basic Usage
===========

.. php:method:: addStep($title, $callback)
.. php:method:: addFinish($callback)

Start by creating Wizard inside your render tree::

    $wizard = Wizard::addTo($app);

Next add as many steps as you need specifying title and a PHP callback code for each::

    $wizard->addStep('Welcome', function ($wizard) {

        Message::addTo($wizard, ['Welcome to wizard demonstration'])->text
            ->addParagraph('Use button "Next" to advance')
            ->addParagraph('You can specify your existing database connection string which will be used
            to create a table for model of your choice');

    });

Your callback will also receive `$wizard` as the first argument. Method addStep returns :php:class:`Step`,
which is described below. You can also provide first argument to addStep as a seed or an object::

    $wizard->addStep([
        'Set DSN',
        'icon'=>'configure',
        'description'=>'Database Connection String'
    ], function ($p) {
        // some code here
    });

You may also specify a single Finish callback, which will be used when wizard is complete::

    $wizard->addFinish(function ($wizard) {
        Header::addTo($wizard, ['You are DONE', 'huge centered']);
    });

Properties
==========

When you create wizard you may specify some of the following options:

.. php:attr:: defaultIcon

Other properties are used during the execution of the wizard.

Step Tracking
=============

.. php:attr:: stepCallback

Wizard employs :php:class:`Callback` to maintain which step you currently are on. All steps are numbered
started with 0.

.. important:: Wizard currently does not enforce step completion. Changing step number in the URL manually can
    take you to any step. You can also go backwards and re-do steps. Section below explains how to make wizard
    enforce some restrictions.

.. php:attr:: currentStep

When Wizard is initialized, it will set currentStep to a number (0, 1, 2, ..) corresponding to your steps
and finish callback, if you have specified it.

.. php:attr:: buttonPrev
.. php:attr:: buttonNext
.. php:attr:: buttonFinish

Those properties will be initialized with the buttons, but some of them may be destroyed by the render step,
if the button is not applicable. For example, first step should not have "prev" button. You can change label
or icon on existing button.


Code Placement
==============

As you build up your wizard, you can place code inside callback or outside. It will have a different effect
on your wizard::

    $wizard->buttonNext->icon = 'person';

    $wizard->addStep('Step 3', function($wizard) {
        $wizard->buttonNext->icon = 'book';
    });


Step defines the callback and will execute it instantly if the step is active. If step 3 is active, the code
is executed to change icon to the book. Otherwise icon will remain 'person'. Another handy technique is
disabling the button by adding "disabled" class.

Navigation
==========

Wizard has few methods to help you to navigate between steps.

.. php:method:: urlNext()
.. php:method:: jsNext()

Methods starting with `url` will return a URL towards the next step. jsNext() method returns javascript action
which will take you to the next step.

If you wish to to go to specific step, you can use `$wizard->stepCallback->getURL($step);`

Finally you can get url of the current step with `$wizard->url()` (see :php:meth:`View::url`)

Step
====

.. php:class:: Step

.. php:attr:: title

.. php:attr:: description

.. php:attr:: icon

.. php:attr:: wizard

Each step of your wizard serves two roles. First is to render title and icon above the wizard and second is
to contain a callback code.







