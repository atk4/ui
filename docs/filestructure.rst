.. _filestructure:

==================================
File structure example & first app
==================================

We will deal here with a suggestion how you could structure your files and folders for your individual atk4 project.
Let's assume you are planning to create at least several pages with some models and views. This example can be expanded
and modified to your needs and shows just one concept of how to setup an atk4 project.


File structure example
======================

This file structure is a recommendation and no must. It is a best practice example.
Feel free to experiment with it and find the ideal file structure for your project.

* config

    * db.php

* public_html

    * images

    * index.php

    * init.php

    * admin.php (if needed)

* projectfolder (could be named "app" for example)

    * Forms

        * ExampleForm1.php

        * ExampleForm2.php

        * UserDetailForm.php

    * Models

        * ExampleClass1.php

        * ExampleClass2.php

        * LoadAllUsers.php

    * Views

        * View1.php

        * View2.php

        * GridUserList.php

* vendor (contains all needed composer modules - don't touch them)

    * atk4

        * ...

    * autoload.php

    * ...

* composer.json



Composer configuration
======================

Configure your composer.json to load the atk4 AND your project folder.
Your composer.json could look like this::

  {
    "require":{
      "atk4/ui": "*"
    },
    "autoload": {
      "psr-4": {
          "MyProject\\": "projectfolder/"
      }
    }
  }


What does that mean?
--------------------
As soon as you start a "composer update" or "composer dump-autoload" in the public_html directory, all needed atk4 files
and all your projectfiles from the subdirectory "projectfolder" are processed by Composer and the autoload.php file is generated.
Read below how to load the autoload.php into your project.

The "require" section within composer.json loads plublically available composer packages from the server.

The "autoload" section within composer.json loads your individual project files (that are saved locally on your computer).
"MyProject" defines the namespace you are using in your classes later on.


Why "public_html"?
------------------

It is a good idea to keep away sensible configuration files that contain passwords (like database connection setups etc.)
from the public and make it only available to your application.
If you go live with your app, you load everything up to the webspace (config & public_html) and point the domain to the
public_html directory.

If you call www.myexampledomain.com it should show the content of public_html.
Within a php file from public_html you are still able to access and include files from config.
But you can't call it directly through the domain (that means in our case "db.php" can't be accessed through the domain).



Create your application
=======================

To initalize your application we need to do the following steps:
    #) Create db.php for database

    #) Create init.php

    #) Load Composer autoload.php (which loads up atk4) in init.php

    #) Initialize the app class in init.php

    #) Create index.php and admin.php


Create db.php for database
--------------------------

We initialize a reusable database connection in db.php through a mysql persistence.
Create a file called "db.php" in the directory "config"::

  <?php
  $db = \atk4\data\Persistence::connect("mysql://localhost/#myexampledatabase", "#username", "#password");

Please remember to use a database that still exists.

Create init.php, index.php and / or admin.php files
---------------------------------------------------

Create a new file in "public_html/projectfolder" and name it "init.php".
In this file we load up our app (later) and load the database configuration::

  <?php
  $rootdir = "../";    // the public_html directory
  require_once $rootdir."../config/db.php";  // contains database configuration outside the public_html directory

Load Composer autoload.php (which loads up atk4) in init.php
------------------------------------------------------------

::

  require_once $rootdir."vendor/autoload.php";   // loads up atk4 and our project files from Composer

Initialize the app class in init.php
------------------------------------

::

  $app = new \atk4\ui\App('Welcome to my first app'); // initialisation of our app
  $app->db = $db;   // defines our database for reuse in other classes

Create index.php and admin.php
------------------------------

If you want to write an app with a backend, create a file called "admin.php"::

  <?php
  $rootdir = "../";
  require_once __DIR__ . "init.php";
  $app->initLayout(\atk4\ui\Layout\Admin::class);

If you want to write an app with a frontend, create a file called "index.php"::

  <?php
  $rootdir = "../";
  require_once __DIR__ . "init.php";
  $app->initLayout(\atk4\ui\Layout\Centered::class);


Create your own classes
=======================

Now as your basic app is set up and running, we start implementing our own classes that build the core of our app.
Following the PSR-4 specifiations all class names and file names have to correspond to each other.

If we want to create a class called "myFirstClass" we have to save it to a file called "myFirstClass.php".

Let's do our first class. Please create a new file in the directory "projectfolder/Views" and call it "View1.php".

Now comes a tricky part: you have to define a namespace within your class file that corresponds with the namespace you have
defined in your composer.json.
Do you remember? - If no, take a look at the beginning of this document. We defined there "MyProject" as our namespace for
the directory "projectfolder".

Open the created file "View1.php" in your editor and add the following lines::

  <?php
  namespace MyProject\Views;

  class View1 extends \atk4\data\View {
      function init(): void {
          parent::init();

          $text = \atk4\ui\Text::addTo($this->app, ['here goes some text']);
      }
  }

"namespace MyProject\\Views;" defines the namespace to use. It reflects the folder structure of the app.
The file located in "projectfolder/Views/View1.php" becomes "MyProject\\Views\\View1" in the namespace.

For each of your classes create a separate file. As long as you follow the name conventions all your class
files will be autoloaded by Composer.

.. warning:: Keep in mind that as soon as you have created one or more new file(s) within the projectfolder you have to run "composer dump-autoload"!!! Otherwise the newly generated file(s) and classes will not be autoloaded and are therefore unavailable in your application.


Load your class in index.php
============================

To use our class in our app, we have to include it into our app. This can be done either through index.php or admin.php.

Please add the following lines into your index.php::

  \MyProject\Views\View1::addTo($app);

or if you have added at the beginning of your index.php "use MyProject\\Views\\View1;" you can write::

  View1::addTo($app);

See also :ref:`using-namespaces` on this topic...
