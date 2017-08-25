==========
Components
==========

Classes that extend from :php:class:`View` are called `Components` and inherit abilities to render themselves (see :ref:`render`)


Core Components
===============

Some components serve as a foundation of entire set of other components. A lot of qualities implemented by a core component is
inherited by its descendants.

.. toctree::
    :maxdepth: 1

    view
    lister
    table
    field

Simple components
=================

Simple component exist for the purpose of abstraction and creating a decent interface which you can rely on when programming your
PHP application with Agile UI. In some cases it may make sense to rely on HTML templates for the simple elements such an Icons,
but when you are working with dynamic and generic components quite often you need to abstract HTML yet let user to have decent
control even over the small elements.

.. toctree::
    :maxdepth: 1

    button
    label
    text
    loremipsum
    header
    icon
    image
    item
    message
    helloworld

Composite components
====================

Composete elements such as CRUD or Form are the bread-and-butter of Agile UI. They will consist out of many sub-elements while
making themselves easy-to-use.

Most of composite elements are designed to work with `Data Models <http://agile-data.readthedocs.io/>`_

.. toctree::
    :maxdepth: 1

    columns
    crud
    grid
    form
    paginator
    dropdown

    misc
