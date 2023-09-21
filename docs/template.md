:::{php:namespace} Atk4\Ui
:::

(Template)=

# Introduction

Agile UI relies on a lightweight built-in template engine to manipulate templates.
The design goals of a template engine are:

- Avoid any logic inside template
- Keep easy-to-understand templates
- Allow preserving template content as much as possible

# Example Template

Assuming that you have the following template:

```
Hello, {mytag}world{/}
```

## Tags

the usage of `{` denotes a "tag" inside your HTML, which must be followed by
alpha-numeric identifier and a closing `}`. Tag needs to be closed with either
`{/mytag}` or `{/}`.

The following code will initialize template inside a PHP code:

```
$t = new Template('Hello, {mytag}world{/}');
```

Once template is initialized you can `renderToHtml()` it any-time to get string
"Hello, world". You can also change tag value:

```
$t->set('mytag', 'Agile UI');

echo $t->renderToHtml(); // "Hello, Agile UI"
```

Tags may also be self-closing:

```
Hello, {$mytag}
```

is identical to:

```
Hello, {mytag}{/}
```

## Regions

We call region a tag, that may contain other tags. Example:

```
Hello, {$name}

{Content}
User {$user} has sent you {$amount} dollars.
{/Content}
```

When this template is parsed, region 'Content' will contain tags
$user and $amount. Although technically you can still use `set()`
to change value of a tag even if it's inside a region, we often
use Region to delegate rendering to another View (more about this
in section for Views).

There are some operations you can do with a region, such as:

```
$content = $mainTemplate->cloneRegion('Content');

$mainTemplate->del('Content');

$content->set('user', 'Joe')->set('amount', 100);
$mainTemplate->dangerouslyAppendHtml('Content', $content->renderToHtml());

$content->set('user', 'Billy')->set('amount', 50);
$mainTemplate->dangerouslyAppendHtml('Content', $content->renderToHtml());
```

## Usage in Agile UI

In practice, however, you will rarely have to work with the template
engine directly, but you would be able to use it through views:

```
$v = new View('my_template.html');
$v->template->set('name', 'Mr. Boss');

$lister = new Lister($v, 'Content');
$lister->setModel($userlist);

echo $v->renderToHtml();
```

The code above will work like this:

1. View will load and parse template.
2. Using $v->template->set('name', ...) will set value of the tag inside template directly.
3. Lister will clone region 'Content' from my_template.
4. Lister will associate itself with provided model.
5. When rendering is executed, lister will iterate through the data,
   appending value of the rendered region back to $v. Finally the
   $v will render itself and echo result.

# Detailed Template Manipulation

As I have mentioned, most Views will handle template for you. You need to
learn about template manipulations if you are designing custom view that
needs to follow some advanced patterns.

:::{php:class} Template
:::

## Template Loading

Array containing a structural representation of the template. When you
create new template object, you can pass template as an argument to a
constructor:

:::{php:method} __construct($templateString)
Will parse template specified as an argument.
:::

Alternatively, if you wish to load template from a file:

:::{php:method} loadFromFile($filename)
Read file and load contents as a template.
:::

:::{php:method} tryLoadFromFile($filename)
Try loading the template. Returns false if template couldn't be loaded. This can be used
if you attempt to load template from various locations.
:::

:::{php:method} loadFromString($string)
Same as using constructor.
:::

If the template is already loaded, you can load another template from
another source which will override the existing one.

## Template Parsing

:::{note}
Older documentation......
:::

Opening Tag — alphanumeric sequence of characters surrounded by `{`
and `}` (example `{elephant}`)

Closing tag — very similar to opening tag but surrounded by `{/` and
`}`. If name of the tag is omitted, then it closes a recently opened tag.
(example `{/elephant}` or `{/}`)

Empty tag — consists of tag immediately followed by closing tag (such as
`{elephant}{/}`)

Self-closing tag — another way to define empty tag. It works in exactly
same way as empty tag. (`{$elephant}`)

Region — typically a multiple lines HTML and text between opening and
closing tag which can contain a nested tags. Regions are typically named
with PascalCase, while other tags are named using `snake_case`:

```
some text before
{ElephantBlock}
    Hello, {$name}.

    by {sender}John Smith{/}
{/ElephantBlock}
some text after
```

In the example above, `sender` and `name` are nested tags.

Region cloning - a process when a region becomes a standalone template and
all of it's nested tags are also preserved.

Top Tag - a tag representing a Region containing all of the template. Typically
is called _top.

## Manually template usage pattern

Template engine in Agile Toolkit can be used independently, without views
if you require so. A typical workflow would be:

1. Load template using {php:meth}`HtmlTemplate::loadTemplate` or
   {php:meth}`HtmlTemplate::loadFromString`.
2. Set tag and region values with {php:meth}`HtmlTemplate::set`.
3. Render template with {php:meth}`HtmlTemplate::renderToHtml`.

## Template use together with Views

A UI Framework such as Agile Toolkit puts quite specific requirements
on template system. In case with Agile Toolkit, the following pattern
is used.

- Each object corresponds to one template.
- View inserted into another view is assigned a region inside parents
  template, called `spot`.
- Developer may decide to use a default template, clone region of parents
  template or use a region of a user-defined template.
- Each View is responsible for it's unique logic such as repeats, substitutions
  or conditions.

As example, I would like to look at how {php:class}`Form` is rendered. The template of form
contains a region called "FormLine" - it represents a label and a input.

When an input is added into a Form, it adopts a "FormLine" region. While the
nested tags would be identical, the markup around them would be dependent on
form layout.

This approach allows you affect the way how {php:class}`Form\Control` is rendered
without having to provide it with custom template, but simply relying on template
of a Form.

| Popular use patterns for template engines         | How Agile Toolkit implements it                                               |
| ------------------------------------------------- | ----------------------------------------------------------------------------- |
| Repeat section of template                        | {php:class}`Lister` will duplicate Region                                     |
| Associate nested tags with models record          | {php:class}`View` with setModel() can do that                                 |
| Various cases within templates based on condition | cloneRegion or get, then use set()                                            |
| Filters (to-upper, escape)                        | all tags are escaped automatically, but other filters are not supported (yet) |

# Using Template Engine directly

Although you might never need to use template engine, understanding
how it's done is important to completely grasp Agile Toolkit underpinnings.

## Loading template

:::{php:method} loadFromString(string)
Initialize current template from the supplied string
:::

:::{php:method} loadFromFile(filename)
Locate (using {php:class}`PathFinder`) and read template from file
:::

:::{php:method} __clone()
Will create duplicate of this template object.
:::

:::{php:attr} template
Array structure containing a parsed variant of your template.
:::

:::{php:attr} tags
Indexed list of tags and regions within the template for speedy access.
:::

:::{php:attr} template_source
Simply contains information about where the template have been loaded from.
:::

:::{php:attr} original_filename
Original template filename, if loaded from file
:::

Template can be loaded from either file or string by using one of
following commands:

```
$template = HtmlTemplate::addTo($this);

$template->loadFromString('Hello, {name}world{/}');
```

To load template from file:

```
$template->loadFromFile('mytemplate');
```

And place the following inside `template/mytemplate.html`:

```
Hello, {name}world{/}
```

HtmlTemplate will use {php:class}`PathFinder` to locate template in one of the
directories of {ref}`resource` `template`.

## Changing template contents

:::{php:method} set(tag, value)
Escapes and inserts value inside a tag. If passed a hash, then each
key is used as a tag, and corresponding value is inserted.
:::

:::{php:method} dangerouslySetHtml(tag, value)
Identical but will not escape. Will also accept hash similar to set()
:::

:::{php:method} append(tag, value)
Escape and add value to existing tag.
:::

:::{php:method} tryAppend(tag, value)
Attempts to append value to existing but will do nothing if tag does not exist.
:::

:::{php:method} dangerouslyAppendHtml(tag, value)
Similar to append, but will not escape.
:::

:::{php:method} tryDangerouslyAppendHtml(tag, value)
Attempts to append non-escaped value, but will do nothing if tag does not exist.
:::

Example:

```
$template = HtmlTemplate::addTo($this);

$template->loadFromString('Hello, {name}world{/}');

$template->set('name', 'John');
$template->dangerouslyAppendHtml('name', '&nbsp;<i class="icon-heart"></i>');

echo $template->renderToHtml();
```

### Using ArrayAccess with Templates

You may use template object as array for simplified syntax:

```
$template->set('name', 'John');

if ($template->hasTag('has_title')) {
    $template->del('has_title');
}
```

## Rendering template

:::{php:method} renderToHtml
Converts template into one string by removing tag markers.
:::

Ultimately we want to convert template into something useful. Rendering
will return contents of the template without tags:

```
$result = $template->renderToHtml();

\Atk4\Ui\Text::addTo($this)->set($result);
// will output "Hello, World"
```

## Template cloning

When you have nested tags, you might want to extract some part of your
template and render it separately. For example, you may have 2 tags
SenderAddress and ReceiverAddress each containing nested tags such as
"name", "city", "zip". You can't use set('name') because it will affect
both names for sender and receiver. Therefore you need to use cloning.
Let's assume you have the following template in `template/envelope.html`:

```
<div class="sender">
{Sender}
    {$name},
    Address: {$street}
             {$city} {$zip}
{/Sender}
</div>

<div class="recipient">
{Recipient}
    {$name},
    Address: {$street}
             {$city} {$zip}
{/Recipient}
</div>
```

You can use the following code to manipulate the template above:

```
$template = HtmlTemplate::addTo($this);
$template->loadFromFile('envelope'); // templates/envelope.html

// split into multiple objects for processing
$sender = $template->cloneRegion('Sender');
$recipient = $template->cloneRegion('Recipient');

// set data to each sub-template separately
$sender->set($senderData);
$recipient->set($recipientData);

// render sub-templates, insert into master template
$template->dangerouslySetHtml('Sender', $sender->renderToHtml());
$template->dangerouslySetHtml('Recipient', $recipient->renderToHtml());

// get final result
$result = $template->renderToHtml();
```

Same thing using Agile Toolkit Views:

```
$envelope = \Atk4\Ui\View::addTo($this, [], [null], null, ['envelope']);

$sender = \Atk4\Ui\View::addTo($envelope, [], [null], 'Sender', 'Sender');
$recipient = \Atk4\Ui\View::addTo($envelope, [], [null], 'Recipient', 'Recipient');

$sender->template->set($senderData);
$recipient->template->set($recipientData);
```

We do not need to manually render anything in this scenario. Also the
template of $sender and $recipient objects will be appropriately cloned
from regions of $envelope and then substituted back after render.

In this example I've usd a basic {php:class}`View` class, however I could
have used my own View object with some more sophisticated presentation logic.
The only affect on the example would be name of the class, the rest of
presentation logic would be abstracted inside view's `renderToHtml()` method.

## Other operations with tags

:::{php:method} del(tag)
Empties contents of tag within a template.
:::

:::{php:method} hasTag(tag)
Returns `true` if tag exists in a template.
:::

:::{php:method} trySet(name, value)
Attempts to set a tag, if it exists within template
:::

:::{php:method} tryDel(name)
Attempts to empty a tag. Does nothing if tag with name does not exist.
:::

## Repeating tags

Agile Toolkit template engine allows you to use same tag several times:

```
Roses are {color}red{/}
Violets are {color}blue{/}
```

If you execute `set('color', 'green')` then contents of both tags will
be affected. Similarly if you call `append('color', '-ish')` then the
text will be appended to both tags.

## Conditional tags

Agile Toolkit template engine allows you to use so called conditional tags
which will automatically remove template regions if tag value is empty.
Conditional tags notation is trailing question mark symbol.

Consider this example:

```
My {email?}e-mail {$email}{/email?} {phone?}phone {$phone}{/?}.
```

This will only show text "e-mail" and email address if email tag value is
set to not empty value. Same for "phone" tag.
So if you execute `set('email', null)` and `set('phone', 123)` then this
template will automatically render as:

```
My  phone 123.
```

Note that zero value is treated as not empty value!

# Views and Templates

Let's look how templates work together with View objects.

## Default template for a view

:::{php:method} defaultTemplate()
Specify default template for a view.
:::

By default view object will execute {php:meth}`defaultTemplate()` method which
returns name of the template. This function must return array with
one or two elements. First element is the name of the template which
will be passed to `loadFromFile()`. Second argument is optional and is
name of the region, which will be cloned. This allows you to have
multiple views load data from same template but use different region.

Function can also return a string, in which case view will attempt to
clone region with such a name from parent's template. This can be used
by your "menu" implementation, which will clone parent's template's tag
instead to hook into some specific template:

```
public function defaultTemplate()
{
    return ['greeting']; // uses templates/greeting.html
}
```

## Redefining template for view during adding

When you are adding new object, you can specify a different template to
use. This is passed as 4th argument to `add()` method and has the same
format as return value of `defaultTemplate()` method. Using this
approach you can use existing objects with your own templates. This
allows you to change the look and feel of certain object for only one or
some pages. If you frequently use view with a different template, it
might be better to define a new View class and re-define
`defaultTemplate()` method instead:

```
MyObject::addTo($this, ['greeting']);
```

## Accessing view's template

Template is available by the time `init()` is called and you can
access it from inside the object or from outside through "template"
property:

```
$grid = \Atk4\Ui\Grid::addTo($this, [], [null], null, array('grid_with_hint'));
$grid->template->trySet('my_hint', 'Changing value of a grid hint here!');
```

In this example we have instructed to use a different template for grid,
which would contain a new tag "my_hint" somewhere. If you try to change
existing tags, their output can be overwritten during rendering of the
view.

## How views render themselves

Agile Toolkit perform object initialization first. When all the objects
are initialized global rendering takes place. Each object's `renderToHtml()`
method is executed in order. The job of each view is to create output
based on it's template and then insert it into the region of owner's
template. It's actually quite similar to our Sender/Recipient example
above. Views, however, perform that automatically.

In order to know "where" in parent's template output should be placed,
the 3rd argument to `add()` exists — "spot". By default spot is
"Content", however changing that will result in output being placed
elsewhere. Let's see how our previous example with addresses can be
implemented using generic views.

```
$envelope = \Atk4\Ui\View::addTo($this, [], [null], null, array('envelope'));

// 3rd argument is output region, 4th is template location
$sender = \Atk4\Ui\View::addTo($envelope, [], [null], 'Sender', 'Sender');
$receiver = \Atk4\Ui\View::addTo($envelope, [], [null], 'Receiver', 'Receiver');

$sender->template->trySet($senderData);
$receiver->template->trySet($receiverData);
```

# Best Practices with Views

## Don't use Template Engine without Views

It is strongly advised not to use templates directly unless you have no
other choice. Views implement consistent and flexible layer on top of
HtmlTemplate as well as integrate with many other components of Agile Toolkit.
The only cases when direct use of SMlite is suggested is if you are not
working with HTML or the output will not be rendered in a regular way
(such as RSS feed generation or TMail)

## Organize templates into directories

Typically templates directory will have sub-directories: "page", "view",
"form" etc. Your custom template for one of the pages should be inside
"page" directory, such as page/contact.html. If you are willing to have
a generic layout which you will use by multiple pages, then instead of
putting it into "page" directory, call it `page_two_columns.html`.

You can find similar structure inside atk4/templates/shared or in some
other projects developed using Agile Toolkit.

## Naming of tags

Tags use two type of naming - PascalCase and underscore_lowercase. Tags
are case sensitive. The larger regions which are typically used for
cloning or by adding new objects into it are named with PascalCase.
Examples would be: "Menu", "Content" and "Recipient". The lowercase and
underscore is used for short variables which would be inserted into
template directly such as "name" or "zip".

# Globally Recognized Tags

Agile Toolkit View will automatically substitute several tags with the values.
The tag {$attributes} is automatically replaced with a attributes incl. `id`
(unique name of a View), `class` and `style`.

There are more templates which are being substituted:

- {page}logout{/} - will be replaced with relative URL to the page
- {public}logo.png{/} - will replace with URL to a public asset
- {css}css/file.css{/} - will replace with URL link to a CSS file
- {js}jquery.validator.js{/} - will replace with URL to JavaScript file

Application (API) has a function {php:meth}`App_Web::setTags` which is called for
every view in the system. It's used to resolve "template" and "page"
tags, however you can add more interesting things here. For example if
you miss ability to include other templates from Smarty, you can
implement custom handling for `{include}` tag here.

Be considered that there are a lot of objects in Agile Toolkit and do
not put any slow code in this function.
