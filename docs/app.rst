

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

Hooks
=====

Application implements HookTrait (http://agile-core.readthedocs.io/en/develop/hook.html)
and the following hooks are available:

 - beforeRender
 - beforeOutput

