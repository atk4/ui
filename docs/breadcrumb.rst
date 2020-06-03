
.. _breadcrumb:

==========
BreadCrumb
==========

.. php:namespace:: atk4\ui
.. php:class:: BreadCrumb

Implement navigational BreadCrumb, by using https://fomantic-ui.com/collections/breadcrumb.html

Basic Usage
===========

.. php:method:: addCrumb()
.. php:method:: set()

Here is a simple usage::

    $crumb = BreadCrumb::addTo($app);
    $crumb->addCrumb('User', ['user']);
    $crumb->addCrumb('Preferences', ['user_preferences']);
    $crumb->set('Change Password');

Every time you call addCrumb a new one is added. With set() you can specify the name of the current page.
addCrumb also requires a URL passed as second argument which can be either a string or array (which would then
be passed to url() (:php:meth:`View::url`).

Changing Divider
================

.. php:attr:: $dividerClass

By default value `right angle icon` is used, but you can change it to `right chevron icon` or simply set to empty string `""`
to use slash.


Working with Path
=================

.. php:attr:: $path
.. php:method: popTitle()

Calling addCrumb adds more elements into the $path property. Each element there would contain 3 hash values:

 - section - name that will appear to the user
 - link - where to go if clicked
 - divider - which divider to use after the crumb

By default `divider` is set to :php:attr:`BreadCrumb::dividerClass`. You may also manipulate $path array yourself.
For example the next code will use some logic::

    $crumb = BreadCrumb::addTo($app);
    $crumb->addCrumb('Users', []);

    $m = new User($app->db);

    if ($id = $app->stickyGet('user_id')) {

        // perhaps we edit individual user?
        $m->load($id);
        $crumb->addCrumb($m->get('name'), []);


        // here we can check for additional criteria and display a deeper level on the crumb


        Form::addTo($app)->setModel($m);
    } else {

        // display list of users
        $table = Table::addTo($app);
        $table->setModel($m);
        $table->addDecorator(['name', [\atk4\ui\TableColumn\Link::class, [], ['user_id'=>'id']);
    }

    $crumb->popTitle();


