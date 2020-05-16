
.. _Lister:

======
Lister
======

.. php:namespace:: atk4\ui

.. php:class:: Lister

Lister can be used to output unstructured data with your own HTML template. If you wish to output
data in a table, see :php:class:`Table`. Lister is also the fastest way to render large amount of
output and will probably give you most flexibility.

Basic Usage
===========

The most common use is when you need to implement a certain HTML and if that HTML contains list of
items. If your HTML looks like this::

    <div class="ui header">Top 20 countries (alphabetically)</div>
      <div class="ui icon label"><i class="ae flag"></i> Andorra</div>
      <div class="ui icon label"><i class="cm flag"></i> Camerroon</div>
      <div class="ui icon label"><i class="ca flag"></i> Canada</div>
    </div>

you should put that into file `myview.html` then use it with a view::

    $view = View::addTo($app, ['template'=>'myview.html']);

Now your application should contain list of 3 sample countires as you have specified in HTML, but next
we need to add some tags into your template::

    <div class="ui header">Top {limit}20{/limit} countries (alphabetically)</div>
      {Countries}
      {rows}
      {row}
      <div class="ui icon label"><i class="ae flag"></i> Andorra</div>
      {/row}
      <div class="ui icon label"><i class="cm flag"></i> Camerroon</div>
      <div class="ui icon label"><i class="ca flag"></i> Canada</div>
      {/rows}
      {/Countries}
    </div>

Here the `{Countries}` region will be replaced with the lister, but the contents of
this region will be re-used as the list template. Refresh your page and your output
should not be affected at all, becuse View clears out all extra template tags.

Next I'll add Lister::

    Lister::addTo($view, [], ['Countries'])
        ->setModel(new Country($db))
        ->setLimit(20);

While most other objects in Agile UI come with their own templates, lister will prefer
to use template inside your region. It will look for "row" and "rows" tag:

 1. Create clone of {row} tag
 2. Delete contents of {rows} tag
 3. For each model row, populate values into {row}
 4. Render {row} and append into {rows}

If you refresh your page now, you should see "Andorra" duplicated 20 times. This is because
the {row} did not contain any field tags. Lets set them up::

      {row}
      <div class="ui icon label"><i class="{iso}ae{/} flag"></i> {name}Andorra{/name}</div>
      {/row}

Refresh your page and you should see list of countries as expected. The flags are not showing yet,
but I'll deal with in next section. For now, lets clean up the template by removing unnecessary tag content::

    <div class="ui header">Top {limit}20{/limit} countries (alphabetically)</div>
      {Countries}
      {rows}
      {row}
      <div class="ui icon label"><i class="{$iso} flag"></i> {$name}</div>
      {/row}
      {/rows}
      {/Countries}
    </div>

Finally, Lister permits you not to use {rows} and {row} tags if entire region can be considered as a row::

    <div class="ui header">Top {limit}20{/limit} countries (alphabetically)</div>
      {Countries}
      <div class="ui icon label"><i class="{$iso} flag"></i> {$name}</div>
      {/Countries}
    </div>

Tweaking the output
===================

Output is formatted using the standard :ref:`ui_persistence` routine, but you can also fine-tune the content
of your tags like this::

    $lister->onHook('beforeRow', function(\atk4\ui\Lister $lister){
        $lister->current_row['iso']=strtolower($lister->current_row['iso']);
    })

Model vs Static Source
======================

Since Lister is non-interractive, you can also set a static source for your lister to avoid hassle::

    $lister->setSource([
        ['flag'=>'ca', 'name'=>'Canada'],
        ['flag'=>'uk', 'name'=>'UK'],
    ]);

Special template tags
=====================

Your {row} tempalte may contain few special tags:

 - {$_id} - will be set to ID of the record (regardless of how your id field is called)
 - {$_title} - will be set to the title of your record (see $model->$title_field)
 - {$_href} - will point to current page but with ?id=123 extra GET argument.


Load page content dynamically when scrolling
============================================

You can make lister load page content dynamically when user is scrolling down page.

    $lister->addJsPaginator(20, $options = [], $container = null, $scrollRegion = null);

The first parameter is the number of item you wish to load per page.
The second parameter is options you want to pass to respective JS widget.
The third paramater is the $container view holding the lister and where scrolling is applicable.
And last parameter is CSS selector of element in which you want to do scrolling.

Using without Template
======================

Agile UI comes with a one sample template for your lister, although it's not set by default,
you can specify it explicitly::

    Lister::addTo($app, ['defaultTemplate'=>'lister.html']);

This should display a list nicely formatted by Fomantic UI, with header, links, icons and description area.

