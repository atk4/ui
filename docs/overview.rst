
.. _overview:

====================
Overview of Agile UI
====================

Agile UI is a component framework that makes you type less HTML in your
PHP project. Integrating with a most popular PHP CSS frameworks, with
Agile UI you can generate a cutting-edge responsive HTML layout that is
lightweight, look beautiful and is fully interractive.

Agile UI Design Goals
=====================

If you are just starting with Agile UI, you need to know the following 5 things:

1. Agile UI Works anywhere
--------------------------

Agile UI is not tied specifically to any full-stack framework or application.
You are welcome to use it anywhere and it should just work out of the box.

2. Agile UI saves you time
--------------------------

Agile UI exists for applications where you need to provide user UI in HTML.
Most typical usage would be Admin Interfaces, User Preferences page and
SaaS applications.

Design of Agile UI allows you to avoid duplicating HTML blocks and lets you
focus on getting your project done.

3. Agile UI is simpler than raw PHP
-----------------------------------

If you use Raw PHP then you are expected to know HTML/CSS/JS. With Agile UI
you can start if you have no web development experience at all. You will
have plenty of components, layouts and add-ons to start your application.

As you progress you will learn how to write JavaScript routines to enhance
your user experience.


4. Agile UI can render all your UI
----------------------------------

It's not only about the `Form` or `CRUD`. All of your HTML rendering can
be done through Agile UI. 

Agile UI is inspired by Desktop toolkits but has been designed entirely
for the Web.

5. Agile UI is lightweight
--------------------------

There are no unnecessary JavaScript, hidden HTML or excessive traffic
between browser and the website. Agile UI is very transparent, lightweight
and should be used by anyone in any situation.


Structure of this documentation
===============================

You should start by looking into QuickStart section, that will introduce
you to some of the code examples. Using a quick-start sections you will
see how powerful UI can be built in a shortest time possible.

If you are ready to start learning Agile UI, the next seciton will talk
about the View class, a core concept behind entire framework. You will
learn how rendering works and various techniques used in the core
of Agile UI.

Next section "Components" will walk you through the basic set of built-in
components. All of the basic components are very similar and they directly
extend 'View', so there are a lot of things you can do with them.

Continue with the "Creating Components" where you will learn how you can
create your own components and layouts.

Next few sections will focus on some of the important advanced techniques
explaining how Virtual Pages work, how to build advanced JavaScript
interractions between your elements.

Agile UI bundles some advanced components, such as Form, Grid, CRUD and
Console. Those all are highly interractive and are built using set of
basic components. 

The final sections of documentation will tell you how to install 3rd party
components and how to use them securily in your project. You will also
earn how to create components for yourself and share them with others.

Agile Data
==========

Agile Data is a business logic and data persistance framework. It's a
separate library that has been specifically designed and developed
for use in Agile UI.

With Agile Data you can easily connect your UI with your data and make
UI components store your data in SQL, NoSQL or RestAPI. On top of the
existing persistences, Agile UI introduces a new persistence class: "UI".

This UI persistence will be extensively used when data needs to be
displayed to the user through UI elements or when input must be
received from the UI layer.

If you do not intend to store data anywhere or are using your own
ORM, the Agile Data will still be used to some extent and therefore
it appears as requirement.

Most of the ORMs lack several important features that are necessary
for UI framework design:

 - ability to load/store data safely with conditions.
 - built-in support for column meta-information
 - field, type and table mapping
 - "onlyFields" support for efficient querying
 - domain-level model references.

Agile Data is distributed under same open-source license as Agile UI
and the rest of this documentation will assume you are using Agile
Data for the purpose of overal clarity. 

Interface Stability
===================

Agile UI is based on Agile Toolkit 4.3 which has been a maintained
UI framework that can trace it's roots back to 2003. As a result, the
object interface is highly stable and all of the documented methods,
models and properties will not change even in the major releases.

If we do have to change something we will keep things backwards
compatible for a period of a few years.

We expect you to extend base classes to build your UI as it is a
best practice to use Agile UI.

Testing and Enterprise Use
==========================

Agile UI is designed with corporate use in mind. The main aim of
the framework is to make your application consistent, modern and
fast.

We understand the importante of testing and all of the Agile UI
components come fully tested across multiple browsers. In most cases
browser compatibilty is defined by the underlying CSS framework.

With Agile UI we will provide you with a guide how to test your
own components. 

Unit Tests
----------

You only need to unit-test you own classes and controllers. For
example if your application creates a separate class that deals
with APR calculation, you need to include unit-test for that
specific class.

Business Logic Unit Tests
-------------------------

Those tests are most suitable for testing your business logic,
that is included in Agile Data. Use "array" persistences to
pre-set model with the necessary data, execute your business
logic with mock objects.

1. set up mock database arrays
2. instatiate model(s)
3. execute business operation
4. assert new content of array.

In most cases the Integration tests are easier to make, and
give you equal testability.

Integration Database Tests
--------------------------

This test-suite will operate with SQL database by executing
various database operations in Agile Data and then asserting
business logic changes.

1. load "safe" database schema
2. each test starts transaction and is finished with a roll-back.
3. perform changes such as adding new invocie
4. assert through other models e.g. by running client report model.

Component Tests
---------------

All of the basic components are tested for you using UI tests,
but you should test your own components. This test will place
your component under various configurations and will make sure
that it continues to work. 

If your component relies on a model, this can also attempt
various model combinations for an extensive test.

User Testing
------------

Once you place your components on your pages and associate
them with your actual data you can perform user tests.
