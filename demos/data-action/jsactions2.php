<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Data\Model;
use Atk4\Ui\Button;
use Atk4\Ui\Card;
use Atk4\Ui\Form;
use Atk4\Ui\GridLayout;
use Atk4\Ui\Header;
use Atk4\Ui\Icon;
use Atk4\Ui\Menu;
use Atk4\Ui\Message;
use Atk4\Ui\UserAction\ModalExecutor;
use Atk4\Ui\View;

// Demo for Model action

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

$country = new Country($app->db);
$entity = $country->loadAny();
$countryId = $entity->getId();

// Model actions for this file are setup in DemoActionUtil.
DemoActionsUtil::setupDemoActions($country);

Header::addTo($app, ['Assign Model action to button event', 'subHeader' => 'Execute model action on this country record by clicking on the appropriate button on the right.']);

$msg = Message::addTo($app, ['Notes', 'type' => 'info']);
$msg->text->addParagraph('When passing an action to a button event, Ui will determine what executor is required base on the action properties.');
$msg->text->addParagraph('If action require arguments, fields and/or preview, then a ModalExecutor will be use.');

View::addTo($app, ['ui' => 'ui clearing divider']);

$gl = GridLayout::addTo($app, ['rows' => 1, 'columns' => 2]);
$c = Card::addTo($gl, ['useLabel' => true], ['r1c1']);
$c->addContent(new Header(['Using country: ']));
$c->setModel($entity, [$country->fieldName()->iso, $country->fieldName()->iso3, $country->fieldName()->phonecode]);

$buttons = View::addTo($gl, ['ui' => 'vertical basic buttons'], ['r1c2']);

// a specific executor for a specific action can be registered using ExecutorFactory::registerExecutor()
$buttons->getApp()->getExecutorFactory()->registerTypeExecutor(
    ExecutorFactory::STEP_EXECUTOR,
    [get_class(new class() extends ModalExecutor {
        protected function addFormTo(View $view): Form
        {
            $form = new class() extends Form {
                protected function init(): void
                {
                    parent::init();

                    // demo - allow custom modal form executor to be recognized easily
                    $this->style['padding'] = '10px';
                    $this->style['background-color'] = '#ccf';
                }

                public function addControl(string $name, $control = [], $field = []): Form\Control
                {
                    // demo - handle self::addControl() calls
                    // the calls are made by StepExecutorTrait::doArgs(), the result is is unused there,
                    // but you should create the form controls and place/add them via custom logic to desired places/layouts
                    return $this->layout->addControl($name, $control, $field);
                }
            };
            $view->add($form);
            $form->buttonSave->destroy();

            return $form;
        }

        protected function createButtonBar(Model\UserAction $action): View
        {
            $res = parent::createButtonBar($action);

            // demo - simple submit button customize (but keep original structure)
            $this->execActionBtn->style['background-color'] = '#f88';
            $anotherButton = Button::addTo($res, ['Another action', 'class.floating' => true]);

            // demo - advanced submit button customize (incl. owning DOM change)
            // like add minor actions to submit button (which requires Button to be placed in View/Menu with "ui buttons" style, see https://fomantic-ui.com/modules/dropdown.html#floating)
            $btnExec = $this->execActionBtn;
            // this does not work as buttons/$res is not added/init yet
            // $btn->getOwner()->removeElement($btnExec->shortName);
            foreach ($res->_addLater as $k => [$obj]) {
                if ($obj === $btnExec) {
                    // 1/2 this does not work either, removing added element is not universally supported
                    // if support is wanted, View::add/init/removeElement must be reworked to support
                    // remove by object ID before/after init is called
                    // unset($res->_addLater[$k]);
                }
            }
            $btnGroup = View::addTo($res, ['class.ui buttons' => true]);
            // 2/2 $btnExec->add($btnGroup);

            $btnDropdown = Button::addTo($btnGroup, ['class.floating dropdown icon' => true]);
            Icon::addTo($btnDropdown, ['dropdown']);
            $btnDropdown->js('click')->dropdown('show');
            $menu = Menu::addTo($btnDropdown, ['style' => ['display' => 'hidden']]);
            foreach (range(1, 5) as $i) {
                $menuItem = $menu->addItem('Action #' . $i);
            }

            return $res;
        }
    })]
);

// Create a button for every action in Country model.
foreach ($country->getUserActions() as $action) {
    $b = Button::addTo($buttons, [$action->getCaption()]);
    // Assign action to button using current model id as url arguments.
    $b->on('click', $action, ['args' => ['id' => $countryId]]);
}
