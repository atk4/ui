
.. _icon:

====
Icon
====

.. php:namespace:: atk4\ui

.. php:class:: Icon

Implements basic icon::

    $icon = $app->add(new \atk4\ui\Icon('book'));

Alternatively::

    $icon = $app->add('Icon', 'flag')->addClass('outline');

Most commonly icon class is used for embedded icons on a :php:class:`Button`
or inside other components (see :ref:`icon_other_comp`)::

    $b1 = new \atk4\ui\Button(['Click Me', 'icon'=>'book']);

You can, of course, create instance of an Icon yourself::

    $icon = new \atk4\ui\Icon('book');
    $b1 = new \atk4\ui\Button(['Click Me', 'icon'=>$icon]);

You do not need to add an icon into the render tree when specifying like that. The icon is selected
through class. To find out what icons are available, refer to Fomantic-UI icon documentation:

https://fomantic-ui.com/elements/icon.html

You can also use States, Variations by passing classes to your button::

    $app->add(new \atk4\ui\Button(['Click Me', 'red', 'icon'=>'flipped big question']));

    $app->add(new \atk4\ui\Label(['Battery Low', 'green', 'icon'=>'battery low']));

.. _icon_other_comp:

Using on other Components
=========================

You can use icon on the following components: :php:class:`Button`, :php:class:`Label`, :php:class:`Header`
:php:class:`Message`, :php:class:`Menu` and possibly some others. Here are some examples::


    $app->add(new \atk4\ui\Header(['Header', 'red', 'icon'=>'flipped question']));
    $app->add(new \atk4\ui\Button(['Button', 'red', 'icon'=>'flipped question']));

    $menu = $app->add(new \atk4\ui\Menu());
    $menu->addItem(['Menu Item', 'icon'=>'flipped question']);
    $sub_menu = $menu->addMenu(['Sub-menu', 'icon'=>'flipped question']);
    $sub_menu->addItem(['Sub Item', 'icon'=>'flipped question']);

    $app->add(new \atk4\ui\Label(['Label', 'right ribbon red', 'icon'=>'flipped question']));



Groups
======

Fomantic UI support icon groups. The best way to implement is to supply :php:class:`Template` to an
icon::

    $app->add(new \atk4\ui\Icon(['template'=>new \atk4\ui\Template('<i class="huge icons">
      <i class="big thin circle icon"></i>
      <i class="user icon"></i>
    </i>'), false]));

However there are several other options you can use when working with your custom HTML. This is not
exclusive to Icon, but I'm adding a few examples here, just for your convenience.

Let's start with a View that contains your custom HTML loaded from file or embedded like this::

    $view = $app->add(['template'=>new \atk4\ui\Template('<div>Hello my {Icon}<i class="huge icons">
      <i class="big thin circle icon"></i>
      <i class="{Content}user{/} icon"></i>
    </i>{/}, It is me</div>')]);

Looking at the template it has a region `{Icon}..{/}`. Try by executing the code above, and you'll see
a text message with a user icon in a circle. You can replace this region by passing it as a template
into Icon class. For that you need to disable a standard Icon template and specify a correct Spot
when adding::

    $icon = $view->add(new \atk4\ui\Icon(['red book', 'template'=>false]), 'Icon');

This technique may be helpful for you if you are creating re-usable elements and you wish to store
Icon object in one of your public properties.

Composing
---------

Composing offers you another way to deal with Group icons::

    $no_users = new \atk4\ui\View([null, 'huge icons', 'element'=>'i']);
    $no_users->add(new \atk4\ui\Icon('big red dont'));
    $no_users->add(new \atk4\ui\Icon('black user icon'));

    $app->add($no_users);

Icon in Your Component
======================

Sometimes you want to build a component that will contain user-defined icon. Here you can find
an implementation for ``SocialAdd`` component that implements a friendly social button with
the following features:

 - has a very compact usage ``new SocialAdd('facebook')``
 - allow to customize icon by specifying it as string, object or injecting properties
 - allow to customize label

Here is the code with comments::

    /**
     * Implements a social network add button. You can initialize the button by passing
     * social network as a parameter: new SocialAdd('facebook')
     * or alternatively you can specify $social, $icon and content individually:
     * new SocialAdd(['Follow on Facebook', 'social'=>'facebook', 'icon'=>'facebook f']);
     *
     * For convenience use this with link(), which will automatically open a new window
     * too.
     */
    class SocialAdd extends \atk4\ui\View {
        public $social = null;
        public $icon = null;
        public $defaultTemplate = null;
        // public $defaultTemplate = __DIR__.'../templates/socialadd.html';

        function init() {
            parent::init();

            if (is_null($this->social)) {
                $this->social = $this->content;
                $this->content = 'Add on '.ucwords($this->content);
            }

            if (!$this->social) {
                throw new Exception('Specify social network to use');
            }

            if (is_null($this->icon)) {
                $this->icon = $this->social;
            }

            if (!$this->template) {
                // TODO: Place template into file and set defaultTemplate instead
                $this->template = new \atk4\ui\Template(
    '<{_element}button{/} class="ui '.$this->social.' button" {$attributes}>
      <i class="large icons">
        {$Icon}
        <i class="inverted corner add icon"></i>
      </i>
      {$Content}
    </{_element}button{/}>');
            }

            // Initialize icon
            if (!is_object($this->icon)) {
                $this->icon = new \atk4\ui\Icon($this->icon);
            }

            // Add icon into render tree
            $this->add($this->icon, 'Icon');
        }
    }

    // Usage Examples. Start with the most basic usage
    $app->add(new SocialAdd('instagram'));

    // Next specify label and separatly name of social network
    $app->add(new SocialAdd(['Follow on Twitter', 'social'=>'twitter']));

    // Finally provide custom icon and make the button clickable.
    $app->add(new SocialAdd(['facebook', 'icon'=>'facebook f']))
        ->link('https://facebook.com', '_blank');
