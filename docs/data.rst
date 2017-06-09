

.. _data:

Agile Data Integration
======================

Agile UI relies on Agile Data library for flexible access to user defined data sources. The purpose of this integration
is to relieve developer from manually creating data fetching and storing code.

Other benefits of relying on Agile Data models is ability to store meta information of the models themselves. Without
Agile UI as hard dependency, Agile UI would have to re-implement all those features on it's own resulting in much
bigger code footprint.

There are no way to use Agile UI without Agile Data, however Agile Data is flexibly enough to work with your own
data sources. The rest of this chapter will explain how you can map various data structures.

Static Data Arrays
------------------

Agile Data contains Persistence_Array (http://agile-data.readthedocs.io/en/develop/design.html?highlight=array#domain-model-actions)
implementation that load and store data in a regular PHP arrays. For the "quick and easy" solution Agile UI Views provide a
method :php:meth:`View::setSource` which will work-around complexities and give you a syntax::

    $grid->setSource([
        1 => ['name'=>'John', 'surname'=>'Smith', 'age'=>10],
        2 => ['name'=>'Sarah', 'surname'=>'Kelly', 'age'=>20],
    ]);

.. note:: 
    Dynamic views will not be able to identify that you are working with static data and some features may not work properly.
    There are no plans in Agile UI to improve ways of using "setSource", instead you should learn more how to use Agile Data
    for expressing your native data source. Agile UI is not optimized for setSource so it's performance will generally be
    slower too.

Raw SQL Queries
---------------

Writing raw SQL queries is source of many errors, both with a business logic and security. Agile Data provides great ways
for abstracting your SQL queries, but if you have to use a raw query::

    // not sure how TODO - write this section.

.. note::
    The above way to using raw queries has a performance implications, because Agile UI is optimised to work with Agile
    Data.

