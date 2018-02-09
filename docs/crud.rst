
.. _crud:

====
CRUD
====

.. php:namespace:: atk4\ui
.. php:class:: CRUD

CRUD class offers a very usable extension to :php:class:`Grid` class, which automatically adds actions for deleting,
updating and adding records as well as linking them with corresponding Model actions.

.. important:: If you only wish to display a non-interractive table use :php:class:`Table` class. If you need to
    display Data Grid with some custom actions (not update/delete/add) or if you want to use your own editing
    mechanism (such as edit data on separate page, not inside a modal), use :php:class:`Grid`


.. important:: ATK Addon - MasterCRUD implements a higher-level multi-model management solution, that takes
    advantage of model relations and traversal to create multiple levels of CRUDs: https://github.com/atk4/mastercrud

Using CRUD
==========

The basic usage of CRUD is::

    $app->add('CRUD')->setModel(new Country($app->db));

Users are now able to fully interract with the table. There are ways to restrict which "rows" and which "columns" user
can access. First we can only allow user to read, manage and delete only countries that are part of European Union::

    $eu_countries = new Country($app->db);
    $eu_countries->addCondition('is_eu', true);

    $app->add('CRUD')->setModel($eu_countries);

After that column `is_eu` will not be editable to the user anymore as it will be marked `system` by `addCondition`.

You can also specify which columns you would like to see on the grid::

    $crud->setModel($eu_countries, ['name']);

This restriction will apply to both viewing and editing, but you can fine-tune that by specifying one of many
parameters to CRUD.

Disabling Actions
=================

.. php:attr:: canCreate
.. php:attr:: canUpdate
.. php:attr:: canDelete

By default CRUD allows all the four operations - reading, creating, updating and deleting. CRUD cannot function
without read operation, but the other operations can be explicitly disabled::

    $app->add(['CRUD', 'canDelete'=>false]);

Specifying Fields
=================

.. php:attr:: fieldsDefault
.. php:attr:: fieldsCreate
.. php:attr:: fieldsRead
.. php:attr:: fieldsUpdate

Through those properties you can specify which fields to use. setModel() second argument will set `fieldsDefault` but
if it's not passed, then you can inject fieldsDefault property during creation of setModel. Alternatively
you can override which fields will be used for the corresponding mode by specifying the property::

    $crud=$this->add([
        'CRUD', 
        'fieldsRead'=>['name'], 
        'fieldsUpdate'=>['name', 'surname']
    ]);

Custom Form
===========

:php:class:`Form` in Agile UI allows you to use many different things, such as custom layouts. With CRUD you can
specify your own form to use, which can be either an object or a seed::


    class UserForm extends \atk4\ui\Form {
        function setModel($m, $fields = null) {
            parent::setModel($m, false);

            $gr = $this->addGroup('Name');
            $gr->addField('first_name');
            $gr->addField('middle_name');
            $gr->addField('last_name');

            $this->addField('email');

            return $this->model;
        }
    }

    $crud=$this->add([
        'CRUD',
        'formDefault'=>new UserForm();
    ])->setModel($big_model);


.. todo:: add example / test implementation

Custom Page
===========

.. php:attr:: pageDefault
.. php:attr:: pageCreate
.. php:attr:: pageUpdate

You can also specify a custom class for your Page. Normally it's a :php:class:`VirtualPage` but you
can extend it to introduce your own style or add more components that just a form::


    class TwoPanels extends \atk4\ui\VirtualPage {

        function add($v, $p = null) {

            // is called with the form
            $col = parent::add('Columns');

            $col_l = $col->addColumn();
            $v = $col_l->add($v);

            $col_r = $col->addColumn();
            $col_r->add('Table')->setModel($this->owner->model->ref('Invoices'));

            return $v;
        }
    }

    $crud=$this->add([
        'CRUD',
        'pageDefault'=>new TwoPanels();
    ])->setModel(new Client($app->db));


Notification
============

.. php:attr:: notify

When data is saved, property `$notify` can contain a custom notification action. By default it uses :php:class:`jsNotify`
which will display green strip on top of the page. You can either override it or add additional actions::

    $crud=$this->add([
        'CRUD',
        'notify'=>[
            new \atk4\ui\jsNotify(['Custom Notification', 'color'=>'blue']),
            $otherview->jsReload();
            // both actions will be executed
        ]
    ])->setModel(new Client($app->db));

