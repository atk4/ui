
.. _ui_persistence:

==============
UI Persistence
==============

.. php:namespace:: atk4\ui\Persistence

.. php:class:: UI

.. warning:: This documentation is incomplete, does NOT include all aspects
    of Persistence UI, for now it is a simple listing of class properties,
    and partial information related to :php:class:`\\atk4\\data\\Model` field
    definitions, with a few usage examples.

Persistence UI extends :php:class:`\\atk4\\data\\Persistence`, a simplified summary explains this class as
a means of allowing data types and parameters within data models to be utilized in UI, it also contains
global properties affecting formatting and output.


Class Properties
================

.. php:attr:: date_format

Output format for date fields::

    $this->ui_persistence->date_format = 'M d, Y'; // Default
    $this->ui_persistence->date_format = 'Y-m-d';  // International Standard


.. php:attr:: time_format

Output format for time fields::

    $this->ui_persistence->time_format = 'H:i'; // Default


.. php:attr:: datetime_format

Output format for datetime fields::

    $this->ui_persistence->datetime_format = 'M d, Y H:i:s' // Default
    $this->ui_persistence->datetime_format = 'Y-m-d h:ia';


.. php:attr:: firstDayOfWeek

For calendars, Monday is 1, Sunday is 0 (Default).


.. php:attr:: currency

Field type :php:class:`\\atk4\\ui\\Persistence\\Money` use this as a prefix in output, or tag on
form input fields::

    $this->ui_persistence->currency = '€';   // Default 
    $this->ui_persistence->currency = '';    // No prefix
    $this->ui_persistence->currency = '$';
    $this->ui_persistence->currency = 'NOK';


.. php:attr:: currency_decimals

Field type :php:class:`\\atk4\\ui\\Persistence\\Money` use this value as decimal count for
number_format() in forms and data output::

    $this->ui_persistence->currency_decimals = 2;  // 2,000,000.43 (Default)
    $this->ui_persistence->currency_decimals = 0;  // 2,000,000
    $this->ui_persistence->currency_decimals = 4;  // 2,000,000.4321


.. php:attr:: yes

string 'Yes'
Documentation Needed.


.. php:attr:: no

string 'No'
Documentation Needed.


.. php:attr:: calendar_options

array []
Documentation Needed.


Using Data\Model parameters
===========================

When adding fields in :php:class:`\\atk4\\data\\Model` you can set the 'ui' property to
an array containing values used in ui. To find more information on how the ui field
property is typically used with forms and decorators, see :ref:`field`.

.. warning:: This section is very incomplete, only includes a few examples, and it is
    possible/likely that all or some of this may be moved elsewhere.



Money/Currency Settings per Field
---------------------------------

The global properties for Currency Prefix and Currency Decimals may be overridden at the field level
in the data model::

    // Local cost quoting is US based - use that for global currency prefix
    $this->ui_persistence->currency = 'USD';


    $data_model->addFields([

        // Local cost is exact and uses defaults
        ['local_cost', type => 'money'],

        // Foreign cost is an estimate, uses no fractions and sets currency prefix
        [
            'estimated_cost_norway',
            'type' => 'money',
            'ui' => [
                'persistence' => [
                    'currency' => 'NOK',
                    'currency_decimals' => 0
                ]
            ]
        ],

        // Energy unit cost is usually very accurate
        ['energy_kw_price', 'type' => 'money', 'ui' => ['persistence' => ['currency_decimals' => 4 ]]]

    ]);

