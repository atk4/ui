:::{php:namespace} Atk4\Ui
:::

:::{php:class} Tabs
:::

# Tabs

Tabs implement a yet another way to organize your data. The implementation is based on: https://fomantic-ui.com/elements/icon.html.

Demo: https://ui.atk4.org/demos/tabs.php

## Basic Usage

Once you create Tabs container you can then mix and match static and dynamic tabs:

```
$tabs = Tabs::addTo($app);
```

Adding a static content is pretty simple:

```
LoremIpsum::addTo($tabs->addTab('Static Tab'));
```

You can add multiple elements into a single tab, like any other view.

:::{php:method} addTab($name, $action = null)
:::

Use addTab() method to add more tabs in Tabs view. First parameter is a title of the tab.

Tabs can be static or dynamic. Dynamic tabs use {php:class}`VirtualPage` implementation mentioned above.
You should pass Closure action as a second parameter.

Example:

```
$tabs = Tabs::addTo($layout);

// add static tab
HelloWorld::addTo($tabs->addTab('Static Tab'));

// add dynamic tab
$tabs->addTab('Dynamically Loading', function (VirtualPage $vp) {
    LoremIpsum::addTo($vp);
});
```

## Dynamic Tabs

Dynamic tabs are based around implementation of {php:class}`VirtualPage` and allow you
to pass a callback which will be triggered when user clicks on the tab.

Note that tab contents are refreshed including any values you put on the form:

```
$tabs = Tabs::addTo($app);

// dynamic tab
$tabs->addTab('Dynamic Lorem Ipsum', function (VirtualPage $vp) {
    LoremIpsum::addTo($vp, ['size' => 2]);
});

// dynamic tab
$tabs->addTab('Dynamic Form', function (VirtualPage $vp) {
    $mRegister = new \Atk4\Data\Model(new \Atk4\Data\Persistence\Array_($a));
    $mRegister->addField('name', ['caption' => 'Please enter your name (John)']);

    $form = Form::addTo($vp, ['class.segment' => true]);
    $form->setModel($mRegister);
    $form->onSubmit(function (Form $form) {
        if ($form->model->get('name') !== 'John') {
            return $form->jsError('name', 'Your name is not John! It is "' . $form->model->get('name') . '". It should be John. Pleeease!');
        }
    });
});
```

## URL Tabs

:::{php:method} addTabUrl($name, $url)
:::

Tab can load external URL or a different page if you prefer that instead of VirtualPage. This works similar to iframe:

```
$tabs = Tabs::addTo($app);

$tabs->addTabUrl('Terms and Condition', 'terms.html');
```
