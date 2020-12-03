<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\Form;
use Atk4\Ui\View;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

/**
 * Example implementation of a dynamic view which support session.
 *
 * Cart will memorize and restore its items into session. Cart will also
 * render the items.
 */

/** @var \Atk4\Ui\Lister $cartClass */
$cartClass = get_class(new class() extends \Atk4\Ui\Lister {
    use \Atk4\Core\SessionTrait;
    public $items = [];

    public $defaultTemplate = 'lister.html';

    protected function init(): void
    {
        parent::init();

        $this->items = $this->recall('items', []);

        // Clicking on any URL produced by this Lister will carry on an extra GET argument.
        $this->stickyGet($this->name . '_remove', '1');

        // Set default description for our row template. Normally this is replaced by the 'descr' field
        // of a model, but we don't have it, so it will stay like this.
        $this->t_row->set('descr', 'click on link to remove item');

        // We link to ourselves with this special GET argument to indicate that item must be removed.
        if (isset($_GET[$this->name . '_remove'])) {
            $this->removeItem($_GET['id']);

            // redirect again, since we don't want this to stay in the URL
            $this->getApp()->redirect([$this->name . '_remove' => false]);
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
    protected function renderView(): void
    {
        // memorize items

        $this->setSource($this->items);

        parent::renderView();
    }
});

/**
 * Implementation of a generic item shelf. Shows selection of products and allow to bind click event.
 *
 * Method linkCart allow you to link ItemShelf with Cart. Clicking on a shelf item will place that
 * item inside a cart reloading it afterwards.
 */

/** @var \Atk4\Ui\View $itemShelfClass */
$itemShelfClass = get_class(new class() extends \Atk4\Ui\View {
    public $ui = 'green segment';

    protected function init(): void
    {
        parent::init();

        $v = \Atk4\Ui\View::addTo($this, ['ui' => 'fluid']);
        $cols = \Atk4\Ui\Columns::addTo($v, ['ui' => 'relaxed divided grid']);

        $c1 = $cols->addColumn();
        \Atk4\Ui\Header::addTo($c1, ['size' => 'small'])->set('Snacks');
        $l1 = \Atk4\Ui\View::addTo($c1, ['ui' => 'list']);
        \Atk4\Ui\Item::addTo($l1, ['content' => 'Crisps', 'ui' => 'item'])->setElement('a');
        \Atk4\Ui\Item::addTo($l1, ['content' => 'Pork Scratchings', 'ui' => 'item'])->setElement('a');
        \Atk4\Ui\Item::addTo($l1, ['content' => 'Candies', 'ui' => 'item'])->setElement('a');
        \Atk4\Ui\Item::addTo($l1, ['content' => 'Sweets', 'ui' => 'item'])->setElement('a');

        $c2 = $cols->addColumn();
        \Atk4\Ui\Header::addTo($c2, ['size' => 'small'])->set('Drinks');
        $l2 = \Atk4\Ui\View::addTo($c2, ['ui' => 'list']);
        \Atk4\Ui\Item::addTo($l2, ['content' => 'Fizzy Drink', 'ui' => 'item'])->setElement('a');
        \Atk4\Ui\Item::addTo($l2, ['content' => 'Hot Latte', 'ui' => 'item'])->setElement('a');
        \Atk4\Ui\Item::addTo($l2, ['content' => 'Water', 'ui' => 'item'])->setElement('a');
        \Atk4\Ui\Item::addTo($l2, ['content' => 'Apple Juice', 'ui' => 'item'])->setElement('a');

        $c3 = $cols->addColumn();
        \Atk4\Ui\Header::addTo($c3, ['size' => 'small'])->set('Mains');
        $l3 = \Atk4\Ui\View::addTo($c3, ['ui' => 'list']);
        \Atk4\Ui\Item::addTo($l3, ['content' => 'Chicken Tikka', 'ui' => 'item'])->setElement('a');
        \Atk4\Ui\Item::addTo($l3, ['content' => 'Green Curry', 'ui' => 'item'])->setElement('a');
        \Atk4\Ui\Item::addTo($l3, ['content' => 'Pastries', 'ui' => 'item'])->setElement('a');
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
        }, [(new \Atk4\Ui\Jquery())->text()]);
    }
});

\Atk4\Ui\Header::addTo($app)->set('Menu popup');
$menu = \Atk4\Ui\Menu::addTo($app);

// You may add popup on top of menu items or dropdowns. Dropdowns have a slightly different
// look, with that triangle on the right. You don't have to add pop-up right away, it can be
// added later.
$browse = \Atk4\Ui\Dropdown::addTo($menu, ['Browse']);

// Add cart item into the menu, with a popup inside
$cartItem = $menu->addItem([$cartClass, 'icon' => 'cart'])->set('Cart');

$cartPopup = \Atk4\Ui\Popup::addTo($app, [$cartItem, 'position' => 'bottom left']);
// Popup won't dissapear as you hover over it.
$cartPopup->setHoverable();

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
$cartOutterLabel = \Atk4\Ui\Label::addTo($cartItem, [count($cart->items), 'floating red ']);
if (!$cart->items) {
    $cartOutterLabel->addStyle('display', 'none');
}

$cartPopup->set(function ($popup) use ($cart) {
    $cartInnerLabel = \Atk4\Ui\Label::addTo($popup, ['Number of items:']);

    // cart is already initialized, so init() is not called again. However, cart will be rendered
    // as a child of a pop-up now.
    $cart = $popup->add($cart);

    $cartInnerLabel->detail = count($cart->items);
    \Atk4\Ui\Item::addTo($popup)->setElement('hr');
    \Atk4\Ui\Button::addTo($popup, ['Checkout', 'primary small']);
});

// Add item shelf below menu and link it with the cart
$shelf->linkCart($cart, [
    // array is a valid js action. Will relad cart item (along with drop-down and label)
    $cartOutterLabel->jsReload(),

    // also will hide current item from the shelf
    (new \Atk4\Ui\Jquery())->hide(),
]);

// label placed on top of menu item, not in the popup

$pop = \Atk4\Ui\Popup::addTo($app, [$browse, 'position' => 'bottom left', 'minWidth' => '500px'])
    ->setHoverable()
    ->setOption('delay', ['show' => 100, 'hide' => 400]);
$shelf2 = $itemShelfClass::addTo($pop);
//$shelf2->linkCart($cart, $cartItem->jsReload());

//////////////////////////////////////////////////////////////////////////////

$userMenu = \Atk4\Ui\Menu::addTo($menu, ['ui' => false], ['RightMenu'])
    ->addClass('right menu')->removeClass('item');
$rightMenu = $userMenu->addMenu(['', 'icon' => 'user']);

// If you add popup right inside the view, it will link itself with the element. If you are adding it into other container,
// you can still manually link it and specify an event.
$signup = \Atk4\Ui\Popup::addTo($app, [$rightMenu, 'position' => 'bottom right'])->setHoverable();

// This popup will be dynamically loaded.
$signup->stickyGet('logged');
$signup->set(function ($pop) {
    // contetn of the popup will be different depending on this condition.
    if (isset($_GET['logged'])) {
        \Atk4\Ui\Message::addTo($pop, ['You are already logged in as ' . $_GET['logged']]);
        \Atk4\Ui\Button::addTo($pop, ['Logout', 'primary', 'icon' => 'sign out'])
            ->link($pop->getApp()->url());
    } else {
        $form = Form::addTo($pop);
        $form->addControl('email', null, ['required' => true]);
        $form->addControl('password', [Form\Control\Password::class], ['required' => true]);
        $form->buttonSave->set('Login');

        // popup handles callbacks properly, so dynamic element such as form works
        // perfectly inside a popup.
        $form->onSubmit(function (Form $form) {
            if ($form->model->get('password') !== '123') {
                return $form->error('password', 'Please use password "123"');
            }

            // refreshes entire page
            return $form->getApp()->jsRedirect(['logged' => $form->model->get('email')]);
            //return new \Atk4\Ui\JsExpression('alert([])', ['Thank you ' . $form->model->get('email')]);
        });
    }
});

//////////////////////////////////////////////////////////////////////////////

\Atk4\Ui\Header::addTo($app)->set('Specifying trigger');

$button = \Atk4\Ui\Button::addTo($app, ['Click Me', 'primary']);

$buttonPopup = \Atk4\Ui\Popup::addTo($app, [$button]);

\Atk4\Ui\Header::addTo($buttonPopup)->set('Using click events');
\Atk4\Ui\View::addTo($buttonPopup)->set('Adding popup into button activates on click by default. Clicked popups will close if you click away.');

$input = Form\Control\Line::addTo($app, ['placeholder' => 'Search users', 'icon' => 'circular search link']);

$inputPopup = \Atk4\Ui\Popup::addTo($app, [$input, 'triggerOn' => 'focus']);
\Atk4\Ui\View::addTo($inputPopup)->set('You can use this field to search data.');

$button = \Atk4\Ui\Button::addTo($app, [null, 'icon' => 'volume down']);
$buttonPopup = \Atk4\Ui\Popup::addTo($app, [$button, 'triggerOn' => 'hover'])->setHoverable();

Form\Control\Checkbox::addTo($buttonPopup, ['Just On/Off', 'slider'])->on('change', $button->js()->find('.icon')->toggleClass('up down'));
