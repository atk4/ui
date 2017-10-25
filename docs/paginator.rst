
.. _paginator:

=========
Paginator
=========

.. php:namespace:: atk4\ui
.. php:class:: Paginator

Paginator displays a horizontal UI menu providing links to pages when all of the content does not fit
on a page. Paginator is a stand-alone component but you can use it in conjunction with other compononents.

Adding and Using
================

.. php:attr: $total

.. php:attr: $page

Place paginator in a designated spot on your page. You also should specify what's the total number of pages
paginator should have::

    $paginator = $app->add('Paginator');
    $paginator->total = 20;

Paginator will not display links to all the 20 pages, instead it will show first, last, current page and few
pages around the current page. Paginator will automatically place links back to your current page through
:php:meth:`App::url()`.

After initializing paginator you can use it's properties to determine current page. Quite often you'll need
to display current page BEFORE the paginator on your page::

    $h = $page->add('Header');
    $page->add('LoremIpsum'); // some content here

    $p = $page->add('Paginator');
    $h->set('Page '.$p->page.' from '.$p->total);

Remember that values of 'page' and 'total' are integers, so you may need to do type-casting::

    $label->set($p->page); // will not work
    $label->set((string)$p->page); // works fine

Range and Logic
===============

You can configure Paginator through properties.

.. php:attr: $range

Reasonable values for $range would be 2 to 5, depending on how big you want your paganiator to appear. Provided
that you have enough pages, user should see $range*2+1 bars.

.. php:meth: getPaginatorItems

You can override this method to implement a different logic for calculating which page links to display given
the current and total pages.

.. php:meth: getCurrentPage

Returns number of current page.

Template
========

Paginator uses Semantic UI `ui pagination menu` so if you are unhappy with the styling (e.g: active element is not
sufficiently highlighted), you should refer to Semantic UI or use alternative theme.

The template for Paginator uses custom logic:

 - `rows` region will be populated with list of page items
 - `Item` region will be cloned and used to represent a regular page
 - `Spacer` region will be used to represent '...'
 - `FirstItem` if present, will be used for link to page "1". Otherwise `Item` is used.
 - `LastItem` if present, shows the link to last page. Otherwise `Item` is used.

Each of the above (except Spacer) may have `active`, `link` and `page` tags.


.. php:meth: renderItem($t, $page = null)

Dynamic Reloading
=================

.. php:attr: $reload

Specifying a view here will cause paginator to only reload this particular component and not all the page entirely.
Usually the View you specify here should also contain the paginator as well as possibly other components that
may be related to it. This technique is used by :php:class:`Grid` and some other components.


