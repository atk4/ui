


.. _callback:

=========
Callbacks
=========

Agile UI pursues a goal of creating full-featured, interractive user interface. Part of that relies
on abstraction of Browser/Server communication. Here is a simple syntax in Agile UI::

    $button = new Buttion();

    // clicking button generates random number every time
    $button->on('click', function($action){
        return $action->text(rand(1,100));
    });

For comparison, the code without PHP callback would look like this::

    $button = new Button();

    // clicking button will change text only once, because
    // random is generated during render and stored statically.
    $button->on('click')->text(rand(1,100));


Lets spend a moment to discuss how this actually works and why. Agile UI works under the following
assumptions:

 - Sending same GET request will execute the Button creation code (as displayed above) once again.
 - Adding additional GET parameters will not affect results of GET request, assuming that those arguments don't conflict.
 - URL is in format <...>?param=1&param2=2
 - Executing "echo" followed by "exit" will not be punished by the environment.

If any of the above are false inside your environment (e.g. you use custom output buffering), you will have to
fine-tune Agile Data Application class to work with your environment requirement.

From the above assumptions, Agile UI derrives at conclusion, that by executing AJAX request from the
browser with a special GET argument a certain code can be triggered inside the on() method that short-circuts
output producing response specifically for button.

Callback Logic Explained
------------------------

1. When your code include a call-back format, an instance of '\atk4\ui\Callback' is created and added into
the render-tree.

2. As any other object in Render-tree, Callback receives a unique name.

3. Callback asks Application class to build a new URL based on the current arguments plus a new GET argument that it
will be able to recognize, typically &my_full_name=callback

4. Callback will verify $_GET to see if the current request contains the said GET argument.

5. If yes - Callback will execute PHP method supplied, then "echo" response and "exit".

6. The client-side will contain JavaScript event to request callback URL and handle it's response.

Typicall call-back will respond with JavaScript code that will be evaluated by the Client. This allows the PHP
call-back to decide on-the-spot the correct response action for supplied data. This approach is used to allow
PHP developer to invoke actions without writing custom handlers in the JavaScript.


.. php::class: Callback

This class implements the actuall callback functionality. You can use it to perform arbitrary actions::

    $button = $layout->add('Button');
    $button->set('Click to do something')->link(
         $button
             ->add('Callback')
             ->set(function(){  
                 do_something(); 
             })
            ->getURL()
     );

In this example `do_something` will be executed, but the execution will continue.

.. php::method: getURL()

Returns a generated URL which will cause callback to be executed.

.. php:method: set(callback, arguments)

Will specify a callback to be executed if the URL is triggered.


.. php::class: CallbackLater

This class is very similar to Callback but it will not execute immediatelly. Instead it will be executed
either at the end at beforeRender or beforeOutput hook from inside App, whichever comes first.


