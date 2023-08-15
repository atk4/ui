:::{php:namespace} Atk4\Ui
:::

(message)=

# Message

:::{php:class} Message
:::

Outputs a rectangular segment with a distinctive color to convey message to the user, based around: https://fomantic-ui.com/collections/message.html

Demo: https://ui.atk4.org/demos/message.php

## Basic Usage

Implements basic image:

```
$message = new \Atk4\Ui\Message('Message Title');
$app->add($message);
```

Although typically you would want to specify what type of message is that:

```
$message = new \Atk4\Ui\Message(['Warning Message Title', 'type' => 'warning']);
$app->add($message);
```

Here is the alternative syntax:

```
$message = Message::addTo($app, ['Warning Message Title', 'type' => 'warning']);
```

## Adding message text

:::{php:attr} text
:::

Property $text is automatically initialized to {php:class}`Text` so you can call {php:meth}`Text::addParagraph`
to add more text inside your message:

```
$message = Message::addTo($app, ['Message Title']);
$message->addClass('warning');
$message->text->addParagraph('First para');
$message->text->addParagraph('Second para');
```

## Message Icon

:::{php:attr} icon
:::

You can specify icon also:

```
$message = Message::addTo($app, [
    'Battery low',
    'class.red' => true,
    'icon' => 'battery low',
])->text->addParagraph('Your battery is getting low. Re-charge your Web App');
```
