

.. _app:

===
App
===

.. php:class:: App

App is the object that is a life-supply function for all of your views. App can
either implement features on it's own or rely on a Full Stack PHP framework (or Application)
that incapsulates Agile UI.

    $app = new App();


.. php:method:: terminate(output)

Used when application flow needs to be terminated preemptievely. For example when
call-back is triggered and need to respond with some JSON.

Execution state
===============

.. php:attr:: is_rendering

Will be true if application is currently rendering recursively through the Render Tree.

Links
=====

.. php:method:: url(page, extension)

Method to generate links between pages. Specified with associative array::

    $url = $app->url(['contact', 'from'=>'John Smith']);

this method must respond with a properly formatted url such as::

    contact.php?from=John+Smith

If your pages use extension other than .php, then you should pass that extension too.

You may redefine this metod if you are using beautiful URLs and advanced
routing::

    /app/contact/John+Smith

Rest of Agile UI code can rely on url() wrapper.

Includes
========

.. php:method:: requireJS($url)

Method to include additional JavaScript file in page::

    $app->requireJS('https://code.jquery.com/jquery-3.1.1.js');
    $app->requireJS('https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.2.10/semantic.min.js');

Using of CDN servers is always better than storing external libraries locally.
Most of the time CDN servers are faster (cached) and more reliable.

.. php:method:: requireCSS($url)

Method to include additional CSS stylesheet in page::

    $app->requireCSS('http://semantic-ui.com/dist/semantic.css');

Hooks
=====

Application implements HookTrait (http://agile-core.readthedocs.io/en/develop/hook.html)
and the following hooks are available:

 - beforeRender
 - beforeOutput

