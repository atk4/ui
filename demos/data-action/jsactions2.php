<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Data\Model;
use Atk4\Ui\Button;
use Atk4\Ui\Card;
use Atk4\Ui\Form;
use Atk4\Ui\GridLayout;
use Atk4\Ui\Header;
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

class CustomForm extends Form
{
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
}

class CustomModalExecutor extends ModalExecutor
{
}

// a specific executor for a specific action can be registered using ExecutorFactory::registerExecutor()
$buttons->getApp()->getExecutorFactory()->registerExecutor(
    $country->getUserAction('edit'),
    [CustomModalExecutor::class, 'formSeed' => [CustomForm::class]]
);

// Create a button for every action in Country model.
foreach ($country->getUserActions() as $action) {
    $b = Button::addTo($buttons, [$action->getCaption()]);
    // Assign action to button using current model id as url arguments.
    $b->on('click', $action, ['args' => ['id' => $countryId]]);
}
