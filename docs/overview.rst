
.. _overview:

====================
Overview of Agile UI
====================

Agile UI is a PHP component framework for building User Interfaces entirely in PHP.
As a framework it's closely coupled with Agile Data (http://agile-data.readthedocs.io),
to which it delegates all data operations and User Interface is using
(https://semantic-ui.com) for presentation.

**Agile Toolkit = Agile UI + Agile Data + Component Platform.**

Agile UI Design Goals
=====================

Agile UI works out of the box and is a very handy framework if you need to add
a casual CRUD or Form in your Web Application, Admin system or Homepage. There
are no files to install, simply declare composer dependency and write the code
in a way that it would fit your current application / framework::

    html...html...
        <?php 
            $crud = new \atk4\ui\CRUD();
            $crud->setModel(new Orders($db));
            echo $crud->render();
        ?>
    html.... html...

Agile UI can handle your applications HTML output entirely - starting with page layouts
and all the way, down to the form labels.

You should use Agile UI in your application if you seek to achieve the following goals:

1. UI Consistency
-----------------

Built out of base components that can be extended by you or 3rd parties, Agile UI gives
you and the rest of your development team clean and simple Object-Oriented toolkit for
building any web interface - the most basic or the most complex ones.

The framework is designed around re-usable components such as Form, CRUD, Menu etc that
can read your data structure and automatically configure fields/columns/labels. Each
component can be enhanced and placed inside other components.

Compared to "Form" implementation in other PHP Frameworks where you would still need
to concern yourself with HTML layouts, routes, JS submission handling, validation display,
integration with database and showing correct values in the drop-downs, the "Form" in
Agile UI is entirely self-sufficient and handles all of the above tasks automatically.

.. image:: images/ui-component-diagram.png
    :width: 30%
    :align: right

In addition to the components that you can use right away, Agile UI also gives you great tools
for building your own components. 

For exmaple, you can design "User Management" component, that could consist of several
sub-pages, forms, grids, menus and CRUDs but just like any other component could be 
displayed with just a few lines of code::

    $mgmt = $layout->add(new UserManagement());
    $mgmt->setModel(new User($db));

This code can place your complex component with all it's JavaScript bindings, stylings
and call-backs anywhere in your application - on the tab, in the dialog, inside another
component, in the grid system or inside your own custom HTML template.


2. Time Saving and prototyping
------------------------------

The major benefit is saving your development time by letting you use some off-the-shelf
UI components to create quick working prototype. If you follow Agile Methodology that's
the ideal goal for you. Once there, you can enhance your application and, if necessary,
replace standard components with your custom ones.

For example, you may be displaying your shopping basket items using the "Table" component
intitially, but then replace it with your own Lister w/ HTML template.

We have designed Agile Toolkit to make component substitution as simple as possible.
Often you can simply try few ways to vizualize your data rigth inside your app before
you / your client decides what's the best one.

3. Enterprise Features
----------------------

Agile Toolkit is most suitable for commercial projects: created from scratch or being refactored.
We have dedicated sections in this documentation that explain how you can clean up
your business logic, separate different application layers, introduce API between presentation
and business logic or make your application database-agnostic.

The code for Agile UI / Agile Data is based off it's predcessor (ATK4.3 and AModules) which
have been evolving since 2007 under a more restrictive license. 

We continue to support our open-source projects and make them available to PHP community
while we also offer commercial add-ons for those enterprise clients and complex projects.
Not only using our commercial components can save you some time, but they come with 
some advanced features.

We want to ensure that our free code is always available and maintained, so please
consider some our commercial extensions or tell us if you have idea for a new component.

Best ways to learn Agile Toolkit
================================

We recommend that you start looking at Agile UI first. Continue reading through the
:ref:`quickstart` section to learn how to build a simple TODO application in just
50 lines of code.

 - QuickStart - 20-minute read and some code examples you can try.
 - Core Concept - Read if you plan to design and build your own components.

   - Patterns and Principles
   - Views and common component properties/methods
   - Component Design and UI code refactoring
   - Injecting HTML Templates and Full-page Layouts
   - JavaScript Event Bindings and Actions
   - App class and Framework Integration
   - Usage Patterns

 - Components - Reference for UI component classes

   - Button, Label, Header, Message, Menu, Column
   - Table and TableColumn
   - Form and Field
   - Grid and CRUD
   - Paginator

 - Advanced Topics


If you are not interested in UI and only need Rest API, we recommend that you look
into documentation for Agile Data (http://agile-data.readthedocs.io) and the
Rest API extension (coming soon).

Application Tutorials
---------------------

We have wrote few working cloud applications ourselves with Agile Toolkit and are
offering you to look at their code. Some of them come with tutorials that teach you
how to build application step-by-step.

Education
---------

If you represent a group of students that wish to learn Agile Toolkit contact us
about our education materials. We offer special support for those that want to
learn how to develop Web Apps using Agile Toolkit.

Commercial Project Strategy
---------------------------

If you maintain a legacy PHP application and would like to have a free chat with
us about some support and assistance, do not hesitate to reach out.


What you DO NOT need to know
============================

Some technologies are "prerequirements" in other PHP frameworks, but Agile Toolkit
lets you develop a perfectly functional web application even if you are NOT familiar
with technologies like:

 - HTML and Asset Management
 - JavaScript, jQuery, NPM
 - CSS styling, LESS
 - Linux, Infrastructure, Docker
 - Rest API and JSON

We do recommend that you come back and learn those technologies **after** you have mastered
Agile Toolkit.

Database abstraction
--------------------

Agile Data offers abstraction of database servers and will use appropriate query
language to fetch your data. You may need to use SQL/NoSQL language of your database
for some more advanced usage cases.

Cloud deployment
----------------

There are also ways to deploy your application into the cloud without knowledge of
infrastructure, Linux and SSH. A good place to start is Heroku (https://www.heroku.com/).
We reference Heroku in our tutorials, but Agile Toolkit can work with any cloud
hosting that runs PHP apps.

