# Agile UI

[![Build Status](https://travis-ci.org/atk4/ui.png?branch=develop)](https://travis-ci.org/atk4/ui)
[![Code Climate](https://codeclimate.com/github/atk4/ui/badges/gpa.svg)](https://codeclimate.com/github/atk4/ui)
[![StyleCI](https://styleci.io/repos/68417565/shield)](https://styleci.io/repos/68417565)
[![Test Coverage](https://codeclimate.com/github/atk4/ui/badges/coverage.svg)](https://codeclimate.com/github/atk4/ui/coverage)
[![Version](https://badge.fury.io/gh/atk4%2Fui.svg)](https://packagist.org/packages/atk4/ui)

**Web UI Component library.**

PHP object-oriented implementation for an interractive Web User Interface, that will work out-of-the-box in your app.

Install with `composer require atk4/ui`. Next, run this code:

``` php
require'vendor/loader.php';

$form = new \atk4\UI\Form();

$form->addField('name', ['mandatory'=>true, 'caption'=>'Full Name']);
$form->addField('joined', ['type'=>'date']);
$form->addSubmit();

echo $form->render();
```

This will provide you with a "Form View" object, capable of rendering HTML markup for Semantic UI CSS framework. To use in your app, you need to include jQuery and Semantic UI:

```html
<link rel="stylesheet" type="text/css" href="http://semantic-ui.com/dist/semantic.css">
<script src="https://code.jquery.com/jquery-3.1.1.js"></script>
<script src="http://semantic-ui.com/dist/semantic.js"></script>
```

So Form class technically is a HTML Generator similar to "Laravel Form", "Yii Form", "Zend Form", make your pick.

Agile UI however, comes with many unique features, such as:

-   JS Events - bind events on view with an interface to jQuery and its various plugins.
-   Interactivity - views can submit data back, execute server-side PHP code.
-   Recursion - views can contain other views forming a rather complex UI patterns.
-   Flexibility - support 90% of CSS framework features trpansparently:  size, color, effects, icons, etc. 
-   Custom Templates - for the remaining 10%, use views.
-   Data integration - when generic CRUD View reads field names, types, lables and captions from Domain Model.

## Component Architectural Design

Many frameworks rely on "views" that consist of logic-infused HTML templates (or even use PHP as template). Agile UI takes logic out of the template entirely and moves it into View - PHP classes extending from `\atk4\UI\View`. Templates become view-specific and not page-specific or action-specific:

``` php
$ui = new \atk4\UI\Columns();
$ui->addColumn()->add(new View\LogInForm());
$ui->addColumn()->add(new View\RegisterForm());
```

Here you can define `View\LogInForm` in a separate class that implement that specific component UI logic. Once defined, View can be used anywhere in your application. View class may choose to replace default template with a custom one, but since it's an implementation detail, that wouldn't impact rest of the UI.

Combination of Views create a **Render Tree** - concept very familiar to Desktop/Mobile developers.

### Models and Views

Most of the Views integrate with a [Domain Models](https://github.com/atk4/data) objects. In this instance, using `setModel` will automatically populate all form fields, types, labels and hints: 

``` php
$db = new \atk4\data\Persistence::connect($dsn);
$model = new User($db);

$form = new \atk4\ui\Form();
$form->setModel($db);

echo $form->render(); // populated
```

It's very important that your View classes only implement UI logic, while Domain logic will still reside inside the Model class:

``` php
class User extends \atk4\data\Model {
  public $table = 'user';
  function init() {
    parent::init();
    $this->addField('name', ['mandatory'=>true, 'caption'=>'Full Name']);
    $this->addField('joined', ['type'=>'date']);
  }
  function sendWelcomeEmail() {
    ...
  }
}
```

For more information of Agile Data, see https://github.com/atk4/data.

### Semantic UI customization

Semantic UI offers wide range of customization through classes. Agile UI does not attempt to mimic every feature, but instead offer transparency. If you want to have black bar with orange button and the markup like this:

``` html
<div class="ui inverted segment">
  <button class="ui inverted orange button">Orange</button>
</div>
```

you can use the following code in Agile UI:

``` php
$v = new \atk4\ui\View(['ui'=>'segment', 'inverted']);
$v->add(new \atk4\ui\Button(['Orange', 'inverted orange']));
```

### Custom Templates

Agile UI does not try to eliminate your ability to write HTML and in many cases having a template is better and more efficient way. Let's implement a "User Card" View using our own custom markup. Create file `card.html`:

``` html
<div id="{$_id}" class="ui card">
  <div class="image">
    <img src="{image_url}/images/avatar2/large/kristy.png{/}">
  </div>
  <div class="content">
    <a class="header">{name}Kristy{/}</a>
    <div class="meta">
      <span class="date">{date}Joined in 2013{/}</span>
    </div>
    <div class="description">
      {description}Kristy is an art director living in New York.{/}
    </div>
  </div>
  <div class="extra content">
    {Extra}<a>
      <i class="user icon"></i>
      22 Friends
    </a>{/}
  </div>
</div>
```

Then use it as a template for a View:

``` php
$card = new \atk4\ui\View(['template'=>'card.html']);
```

The "{$_id}" tag will be supplied by Agile UI automatically, but the rest of the tags can be set through a model:

``` php
$user = new User($db);
$user->load(123); // ID of the person
$card->setModel($user);
```

Templates can also be used for a form. In fact we can use that same "card.html" as a form layout:

```php
$form = new \atk4\UI\Form();
$form->setLayout('card.html');
$form->setModel($user);
```

Finally - templates are extremely useful with Lister class for producting non-tabular repeating HTML markup.

## Full Page Render

Agile UI can be used as a layout engine for your entire page:

``` php
$app = new \atk4\ui\App('Hello World');
$app->setLayout(new \atk4\ui\Layout\Centered());

$app->layout->add($card);
$app->layout->add(new \atk4\ui\View(['ui'=>'divider']));
$app->layout->add($form);

$app->run();
```

This will take care of the HTML boilerplate JS/CSS includes and everything else.

## Factory

Sometimes you want to override, which class should be used for certain widget on an application level. Application can map into a more consise syntax for `add()` and allow you to re-route components into appropriate namespace.

``` php
$app = new \atk4\ui\App('Hello World');
$app->map('BigButton', 'View\MyBigButton');

$view = $app->add('View');

$b1 = $view->add('Button', 'Button 1');
$b2 = $view->add('Button', 'Button 2');
$bb = $view->add('BigButton');
```

Re-routing can be used to replaced standard UI elements with a different classes. If you rather prefer to rely on "use" and hinting in your IDE, then use this syntax instead:

``` php
use \atk4\ui\View;
use \atk4\ui\Button;
use \View\MyBigButton as BigButton;

$view = new View();
$b1 = new Button('Button 1');
$b2 = new Button('Button 2');
$bb = new BigButton();

$app->add($view);
$view->add([$b1, $b2, $bb]);
```

It does not matter matter in which order you add the elements, as long as all of the views become part of the same Render Tree.

## Events

Agile UI supports events on any view:

``` php
$b1 = $view->add('Button', 'Button 1');
$b2 = $view->add('Button', 'Butotn 2');

$b1->on('click')->toggle('active');
$b2->on('hover', $b1->js()->hide('slow'));
```

Method calls to "toggle" and "hide" translate into jQuery chain systax:

``` javascript
$('b1').on('click', function(){ $('b1').toggle('active'); });
$('b2').on('click', function(){ $('b1').hide('slow'); });
```

## Server-Side Events

All the Views you initialize have a unique identifier in the render tree. This makes it possible for events to communicate with the PHP logic:

``` php
$b1 = $view->add('Button', 'Rand: '.rand(1,100));
$b2 = $view->add('Button', 'Button 2');

$b1->on('click')->reload(); // reload view dynamically
$b2->on('click', function($b){
  return $b->js()->text(rand(101, 200));
});
```

Eeach button now implements a call-back handler that works reliably, transparently and does not confilct with multiple Button instances on te same page.

## Virtual Pages

You have your own routing preferences, however sometimes a component needs to open a pop-up or dialog box with additional UI inside. Agile UI offers concept of "Virtual Pages". Those pages have unique URL which will be accessible as long as the original element is accessible.

``` php
$b1 = $view->add('Button', 'Click to Open Dialog');

$b1->add('VirtualPage')->bindEvent()->set(function($page) {
  $page->add('LoremIpsum');
});;
```

## Advanced Component Features

With all of the advanced features available for the components, they can implement a higher level of view logic abstraction. Form submit handler is used for dynamic form submissions:

``` php
$form->onSubmit(function($js){
  return $js->hide('slow');
});
```

Similarly a 'CRUD' view relies on 'Form' and 'Grid' views to create a fully-interractive CRUD experience:

``` php
$view->add('CRUD')->setModel('User');
```

Producing the following:

![crud](docs/crud.png)

## More Features

There are many other features, which I wanted to mentioned briefly:

-   Integration with RequireJS for dynamic JS includes
-   Debug support
-   Hooks / Callbacks
-   Support for hierarchical trees
-   Extensions
    -   Real-time terminal
    -   Access control

## Current Status

Agile UI is currently in the **early development stage**. Our development process is open to anyone and we welcome any curious person in our Gitter chat:

[![Gitter](https://img.shields.io/gitter/room/atk4/atk4.svg?maxAge=2592000)](https://gitter.im/atk4/atk4?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge) [![License](https://poser.pugx.org/atk4/ui/license)](https://packagist.org/packages/atk4/ui)



## Roadmap

| Version    | Features                                 |
| ---------- | ---------------------------------------- |
| 0.1 [done] | Bootstrap the test-suite, continious integration and UI-testing. |
| 0.2 [done] | Implement template engine and a static "View". |
| 0.3        | Implement JavaScript mechanics, integrate RequireJS, jQuery and Semantic UI |
| 0.4        | Implement URL mechanics and reloading    |
| 0.5        | Implement standard set of UI elements - Button, Menu, Label, etc. |
| 0.6        | Implement Form                           |
| 0.7        | Implement Grid                           |
| 0.8        | Implement CRUD                           |
| 0.9        | Implement real-time code execution (for consoles, progress-bars, spinners) |
