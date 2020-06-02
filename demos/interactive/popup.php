<?php

namespace atk4\ui\demo;

require_once __DIR__ . '/../atk-init.php';

/**
 * Example implementation of a dynamic view which support session.
 *
 * Cart will memorize and restore its items into session. Cart will also
 * render the items.
 */
$cartClass = get_class(new class() extends \atk4\ui\Lister {
    use \atk4\core\SessionTrait;

    public $items = [];

    public $defaultTemplate = 'lister.html';

    public function init(): void
    {
        parent::init();
        $this->items = $this->recall('items', []);

        // Clicking on any URL produced by this Lister will carry on an extra GET argument
        $this->stickyGet($this->name . '_remove', true);

        // Set default description for our row template. Normally this is replaced by the 'descr' field
        // of a model, but we don't have it, so it will stay like this.
        $this->t_row['descr'] = 'click on link to remove item';

        // We link to ourselves with this special GET argument to indicate that item must be removed.
        if (isset($_GET[$this->name . '_remove'])) {
            $this->removeItem($_GET['id']);

            // redirect again, since we don't want this to stay in the URL
            $this->app->redirect([$this->name . '_remove' => false]);
        }
    }

    /**
     * adding an item into the cart.
     */
    public function addItem($item)
    {
        $this->items[] = $item;
        $this->memorize('items', $this->items);
    }

    /**
     * remove item form the cart with specified index.
     */
    public function removeItem($no)
    {
        unset($this->items[$no]);
        $this->memorize('items', $this->items);
    }

    /**
     * renders as a regular lister, but source is the items.
     */
    public function renderView()
    {
        // memorize items

        $this->setSource($this->items);

        return parent::renderView();
    }
});

/**
 * Implementation of a generic item shelf. Shows selection of products and allow to bind click event.
 *
 * Method linkCart allow you to link ItemShelf with Cart. Clicking on a shelf item will place that
 * item inside a cart reloading it afterwards.
 */
$itemShelfClass = get_class(new class() extends \atk4\ui\View {
    public $ui = 'green segment';

    public function init(): void
    {
        parent::init();
        $v = \atk4\ui\View::addTo($this, ['ui' => 'fluid']);
        $cols = \atk4\ui\Columns::addTo($v, ['ui' => 'relaxed divided grid']);

        $c1 = $cols->addColumn();
        \atk4\ui\Header::addTo($c1, ['size' => 'small'])->set('Snacks');
        $l1 = \atk4\ui\View::addTo($c1, ['ui' => 'list']);
        \atk4\ui\Item::addTo($l1, ['content' => 'Crisps', 'ui' => 'item'])->setElement('a');
        \atk4\ui\Item::addTo($l1, ['content' => 'Pork Scratchings', 'ui' => 'item'])->setElement('a');
        \atk4\ui\Item::addTo($l1, ['content' => 'Candies', 'ui' => 'item'])->setElement('a');
        \atk4\ui\Item::addTo($l1, ['content' => 'Sweets', 'ui' => 'item'])->setElement('a');

        $c2 = $cols->addColumn();
        \atk4\ui\Header::addTo($c2, ['size' => 'small'])->set('Drinks');
        $l2 = \atk4\ui\View::addTo($c2, ['ui' => 'list']);
        \atk4\ui\Item::addTo($l2, ['content' => 'Fizzy Drink', 'ui' => 'item'])->setElement('a');
        \atk4\ui\Item::addTo($l2, ['content' => 'Hot Latte', 'ui' => 'item'])->setElement('a');
        \atk4\ui\Item::addTo($l2, ['content' => 'Water', 'ui' => 'item'])->setElement('a');
        \atk4\ui\Item::addTo($l2, ['content' => 'Apple Juice', 'ui' => 'item'])->setElement('a');

        $c3 = $cols->addColumn();
        \atk4\ui\Header::addTo($c3, ['size' => 'small'])->set('Mains');
        $l3 = \atk4\ui\View::addTo($c3, ['ui' => 'list']);
        \atk4\ui\Item::addTo($l3, ['content' => 'Chicken Tikka', 'ui' => 'item'])->setElement('a');
        \atk4\ui\Item::addTo($l3, ['content' => 'Green Curry', 'ui' => 'item'])->setElement('a');
        \atk4\ui\Item::addTo($l3, ['content' => 'Pastries', 'ui' => 'item'])->setElement('a');
    }

    /**
     * Associate your shelf with cart, so that when item is clicked, the content of a
     * cart is updated.
     *
     * Also - you can supply jsAction to execute when this happens.
     */
    public function linkCart($cart, $jsAction = null)
    {
        $this->on('click', '.item', function ($a, $b) use ($cart, $jsAction) {
            $cart->addItem($b);

            return $jsAction;
        }, [(new \atk4\ui\jQuery())->text()]);
    }
});

\atk4\ui\Header::addTo($app)->set('Menu popup');
$m = \atk4\ui\Menu::addTo($app);

// You may add popup on top of menu items or dropdowns. Dropdowns have a slightly different
// look, with that triangle on the right. You don't have to add pop-up right away, it can be
// added later.
$browse = \atk4\ui\DropDown::addTo($m, ['Browse']);

// Add cart item into the menu, with a popup inside
$cart_item = $m->addItem([$cartClass, 'icon' => 'cart'], 'cart');

$cart_popup = \atk4\ui\Popup::addTo($app, [$cart_item, 'position' => 'bottom left']);
// Popup won't dissapear as you hover over it.
$cart_popup->setHoverable();

$shelf = $itemShelfClass::addTo($app);

// Here we are facing a pretty interesting problem. If you attempt to put "Cart" object inside a popup directly,
// it won't work, because it will be located inside the menu item's DOM tree and, although hidden, will be
// impacted by some css rules of the menu.
//
// This can happen when your popup content is non-trivial. So we are moving Popup into the app and linking up
// the triggers. Now, since it's outside, we can't use a single jsAction to reload menu item (along with label)
// and the contens. We could use 2 requests for reloading, but that's not good.
//
// The next idea is to make cart dynamic, so it loads when you move mouse over the menu. This probably is good,
// as it will always be accurate, even if you added items form multiple browser tabs.
//
// However in this case Cart object will exist only inside the popup callback, and we won't be able to get
// the label out.
//
// The final solution works like this - Cart is added to the application directly at first. It's initialized
// as i would be in the application. That's also impacts under which key 'memorize' is storing data - having
// two different objects won't work, since they won't share session data.

$cart = $cartClass::addTo($app);

// Next I am calling destroy. This won't actually destroy the cart, but it will remove it from the application.
// If i add unset($cart) afterwards, garbage collector will trigger destructor. Instead I'm passing $cart
// into the callback and making it part of the pop-up render tree.
$cart->destroy();

// Label now can be added referencing Cart's items. Init() was colled when I added it into app, so the
// item property is populated.
$cart_outter_label = \atk4\ui\Label::addTo($cart_item, [count($cart->items), 'floating red ']);
if (!$cart->items) {
    $cart_outter_label->addStyle('display', 'none');
}

$cart_popup->set(function ($popup) use ($shelf, $cart_outter_label, $cart) {
    $cart_inner_label = \atk4\ui\Label::addTo($popup, ['Number of items:']);

    // cart is already initialized, so init() is not called again. However, cart will be rendered
    // as a child of a pop-up now.
    $cart = $popup->add($cart);

    $cart_inner_label->detail = count($cart->items);
    \atk4\ui\Item::addTo($popup)->setElement('hr');
    $btn = \atk4\ui\Button::addTo($popup, ['Checkout', 'primary small']);
});

// Add item shelf below menu and link it with the cart
$shelf->linkCart($cart, [
    // array is a valid js action. Will relad cart item (along with drop-down and label)
    $cart_outter_label->jsReload(),

    // also will hide current item from the shelf
    (new \atk4\ui\jQuery())->hide(),
]);

// label placed on top of menu item, not in the popup

$pop = \atk4\ui\Popup::addTo($app, [$browse, 'position' => 'bottom left', 'minWidth' => '500px'])
    ->setHoverable()
    ->setOption('delay', ['show' => 100, 'hide' => 400]);
$shelf2 = $itemShelfClass::addTo($pop);
//$shelf2->linkCart($cart, $cart_item->jsReload());

//////////////////////////////////////////////////////////////////////////////

$um = \atk4\ui\Menu::addTo($m, ['ui' => false], ['RightMenu'])
    ->addClass('right menu')->removeClass('item');
$m_right = $um->addMenu(['', 'icon' => 'user']);

// If you add popup right inside the view, it will link itself with the element. If you are adding it into other container,
// you can still manually link it and specify an event.
$signup = \atk4\ui\Popup::addTo($app, [$m_right, 'position' => 'bottom right'])->setHoverable();

// This popup will be dynamically loaded.
$signup->stickyGet('logged');
$signup->set(function ($pop) {
    // contetn of the popup will be different depending on this condition.
    if (isset($_GET['logged'])) {
        \atk4\ui\Message::addTo($pop, ['You are already logged in as ' . $_GET['logged']]);
        \atk4\ui\Button::addTo($pop, ['Logout', 'primary', 'icon' => 'sign out'])
            ->link($pop->app->url());
    } else {
        $f = \atk4\ui\Form::addTo($pop);
        $f->addField('email', null, ['required' => true]);
        $f->addField('password', ['Password'], ['required' => true]);
        $f->buttonSave->set('Login');

        // popup handles callbacks properly, so dynamic element such as form works
        // perfectly inside a popup.
        $f->onSubmit(function (\atk4\ui\Form $form) {
            if ($form->model->get('password') !== '123') {
                return $form->error('password', 'Please use password "123"');
            }

            // refreshes entire page
            return $form->app->jsRedirect(['logged' => $form->model->get('email')]);
            //return new \atk4\ui\jsExpression('alert([])', ['Thank you ' . $f->model->get('email')]);
        });
    }
});

//////////////////////////////////////////////////////////////////////////////

\atk4\ui\Header::addTo($app)->set('Specifying trigger');

$button = \atk4\ui\Button::addTo($app, ['Click Me', 'primary']);

$b_pop = \atk4\ui\Popup::addTo($app, [$button]);

\atk4\ui\Header::addTo($b_pop)->set('Using click events');
\atk4\ui\View::addTo($b_pop)->set('Adding popup into button activates on click by default. Clicked popups will close if you click away.');

$input = \atk4\ui\FormField\Line::addTo($app, ['placeholder' => 'Search users', 'icon' => 'circular search link']);

$i_pop = \atk4\ui\Popup::addTo($app, [$input, 'triggerOn' => 'focus']);
\atk4\ui\View::addTo($i_pop)->set('You can use this field to search data.');

$button = \atk4\ui\Button::addTo($app, [null, 'icon' => 'volume down']);
$b_pop = \atk4\ui\Popup::addTo($app, [$button, 'triggerOn' => 'hover'])->setHoverable();

\atk4\ui\FormField\CheckBox::addTo($b_pop, ['Just On/Off', 'slider'])->on('change', $button->js()->find('.icon')->toggleClass('up down'));
