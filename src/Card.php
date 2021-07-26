<?php

declare(strict_types=1);
/**
 * A Card container.
 *
 * Card can contain arbitrary information.
 *
 * Card contains one main CardSection for adding content
 * but it can contains other CardSection using addSection method.
 *
 * Each section can have it's own model field to be display has
 * field value, field label, field value or as table.
 *
 * Card also has an extra content section which is formatted
 * separately from Section content. Extra content may also have
 * model field display.
 *
 * Multiple model can be used to display various content on each card section.
 * When using model or models, the first model that get set via setModel method
 * will have it's id_field set as data-id html attribute for the card. Thus making
 * the id available via javascript (new Jquery())->data('id')
 */

namespace Atk4\Ui;

use Atk4\Core\Factory;
use Atk4\Data\Model;

class Card extends View
{
    public $ui = 'card atk-card';

    public $defaultTemplate = 'card.html';

    /** @var View|null A View that hold the image. */
    public $imageContainer;

    /** @var string Card box type. */
    public $cardCss = 'segment';

    /** @var string|Image|null A path to the image src or the image view. */
    public $image;

    /** @var CardSection|null The main card section of this card */
    public $section;

    /** @var string The CardSection default class name. */
    public $cardSection = CardSection::class;

    /** @var View|null The extra content view container for the card. */
    public $extraContainer;

    /** @var string|View|null A description inside the Card content. */
    public $description;

    /** @var array|Button|null A button or an array of Buttons */
    public $buttons;

    /** @var bool How buttons are display inside button container */
    public $hasFluidButton = true;

    /** @var View|null The button Container for Button */
    public $btnContainer;

    /** @var string Table css class */
    // public $tableClass = 'ui fixed small';

    /** @var bool Display model field as table inside card holder content */
    public $useTable = false;

    /** @var bool Use Field label with value data. */
    public $useLabel = false;

    /** @var string Default executor class. */
    public $executor = UserAction\ModalExecutor::class;

    /** @var array Array of columns css wide classes */
    protected $words = [
        '', 'fluid', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine', 'ten', 'eleven', 'twelve',
        'thirteen', 'fourteen', 'fifteen', 'sixteen',
    ];

    /** @var int The number of buttons */
    private $btnCount = 0;

    protected function init(): void
    {
        parent::init();

        $this->addClass($this->cardCss);
        if ($this->imageContainer) {
            $this->add($this->imageContainer, 'Image');
        }

        if ($this->description) {
            $this->addDescription($this->description);
        }

        if ($this->image) {
            $this->addImage($this->image);
        }

        if ($this->buttons) {
            $this->addButton($this->buttons);
        }
    }

    /**
     * Get main section of this card.
     *
     * @return CardSection|View|null
     */
    public function getSection()
    {
        if (!$this->section) {
            $this->section = $this->add([$this->cardSection, 'card' => $this]);
        }

        return $this->section;
    }

    /**
     * Get the image container of this card.
     *
     * @return View|null
     */
    public function getImageContainer()
    {
        if (!$this->imageContainer) {
            $this->imageContainer = View::addTo($this, ['class' => ['image']], ['Image']);
        }

        return $this->imageContainer;
    }

    /**
     * Get the ExtraContainer of this card.
     *
     * @return View|null
     */
    public function getExtraContainer()
    {
        if (!$this->extraContainer) {
            $this->extraContainer = View::addTo($this, ['class' => ['extra content']], ['ExtraContent']);
        }

        return $this->extraContainer;
    }

    /**
     * Get the button container of this card.
     *
     * @return View|null
     */
    public function getButtonContainer()
    {
        if (!$this->btnContainer) {
            $this->btnContainer = $this->addExtraContent(new View(['ui' => 'buttons']));
        }

        return $this->btnContainer;
    }

    /**
     * Add Content to card.
     *
     * @return View|null
     */
    public function addContent(View $view)
    {
        return $this->getSection()->add($view);
    }

    /**
     * Set model.
     *
     * If Fields are past with $model that field will be add
     * to the main section of this card.
     *
     * @param array|false $fields an array of fields name to display in content
     *
     * @return \Atk4\Data\Model
     */
    public function setModel(Model $model, $fields = null)
    {
        if (!$model->loaded()) {
            throw new Exception('Model need to be loaded.');
        }

        if (!$this->model) {
            $model = parent::setModel($model);
        }

        if ($fields === null) {
            $fields = array_keys($this->model->getFields(['editable', 'visible']));
        } elseif ($fields === false) {
            $fields = [];
        }

        $this->setDataId($this->model->getId());

        if (is_array($fields)) {
            View::addTo($this->getSection(), [$model->getTitle(), ['class' => 'header']]);
            $this->getSection()->addFields($model, $fields, $this->useLabel, $this->useTable);
        }

        return $model;
    }

    /**
     * Set data-id attribute of this card.
     */
    public function setDataId($id)
    {
        $this->template->trySet('dataId', $id);
    }

    /**
     * Add actions from various model.
     */
    public function addModelsActions(array $models)
    {
        foreach ($models as $model) {
            $this->addModelActions($model);
        }
    }

    /**
     * Add action from Model.
     */
    public function addModelActions(Model $model)
    {
        if ($singleActions = $model->getUserActions(Model\UserAction::APPLIES_TO_SINGLE_RECORD)) {
            $this->setModel($model);
            foreach ($singleActions as $action) {
                $this->addAction($action, $this->executor);
            }
        }

        if ($noRecordAction = $model->getUserActions(Model\UserAction::APPLIES_TO_NO_RECORDS)) {
            foreach ($noRecordAction as $action) {
                $this->addAction($action, $this->executor);
            }
        }
    }

    /**
     * Add a CardSection to this card.
     *
     * @return View
     */
    public function addSection(string $title = null, Model $model = null, array $fields = null, bool $useTable = false, bool $useLabel = false)
    {
        $section = CardSection::addToWithCl($this, [$this->cardSection, 'card' => $this], ['Section']);
        if ($title) {
            View::addTo($section, [$title, ['class' => 'header']]);
        }

        if ($model && $fields) {
            $section->setModel($model);
            $section->addFields($model, $fields, $useTable, $useLabel);
        }

        return $section;
    }

    /**
     * Add action executor to card.
     */
    public function addAction(Model\UserAction $action, $executor, $button = null)
    {
        if (!$button) {
            $button = new Button([$action->caption]);
        }
        $btn = $this->addButton($button);

        $vp = VirtualPage::addTo($this)->set(function ($page) use ($executor, $action) {
            $id = $this->stickyGet($this->name);

            $page->add($executor = new $executor());

            $action->setEntity($action->getModel()->load($id));

            $executor->setAction($action);
        });

        $btn->on('click', new JsModal($action->caption, $vp, [$this->name => (new Jquery())->parents('.atk-card')->data('id')]));
    }

    /**
     * Execute Model user action via button in Card.
     */
    public function addClickAction(Model\UserAction $action, Button $button = null, array $args = [], string $confirm = null): self
    {
        $defaults = [];

        $btn = $this->addButton($button ?? $this->getExecutorFactory()->createTrigger($action, $this->getExecutorFactory()::CARD_BUTTON));

        // Setting arg for model id. $args[0] is consider to hold a model id, i.e. as a js expression.
        if ($this->model && $this->model->loaded() && !isset($args[0])) {
            $defaults[] = $this->model->getId();
        }

        if (!empty($args)) {
            $defaults['args'] = $args;
        }

        if ($confirm) {
            $defaults['confirm'] = $confirm;
        }

        $btn->on('click', $action, $defaults);

        return $this;
    }

    /**
     * Set extra content using model field.
     */
    public function addExtraFields(Model $model, array $fields, string $glue = null)
    {
        // display extra field in line.
        if ($glue) {
            $extra = '';
            foreach ($fields as $field) {
                $extra .= $model->get($field) . $glue;
            }
            $extra = rtrim($extra, $glue);
            $this->addExtraContent(new View([$extra, 'ui' => 'ui basic fitted segment']));
        } else {
            foreach ($fields as $field) {
                $this->addExtraContent(new View([$model->get($field), 'ui basic fitted segment']));
            }
        }
    }

    /**
     * Add Description to main card content.
     *
     * @param string|View $description
     *
     * @return View|string|null the description to add
     */
    public function addDescription($description)
    {
        return $this->getSection()->addDescription($description);
    }

    /**
     * Add Extra content to the Card.
     * Extra content is added at the bottom of the card.
     *
     * @return View
     */
    public function addExtraContent(View $view)
    {
        return $this->getExtraContainer()->add($view);
    }

    /**
     * Add image to card.
     *
     * @param string|Image $img
     *
     * @return View|null
     */
    public function addImage($img)
    {
        if (is_string($img)) {
            $img = Image::addTo($this->getImageContainer(), [$img]);
        } else {
            $img = $this->getImageContainer()->add($img);
        }

        return $img;
    }

    /**
     * Add button to card.
     *
     * @param Button $button a Button
     *
     * @return View|null
     */
    public function addButton($button)
    {
        if ($this->hasFluidButton && $this->btnCount > 0) {
            $this->getButtonContainer()->removeClass($this->words[$this->btnCount]);
        }

        if (!is_object($button)) {
            $button = Factory::factory([Button::class], $button);
        }

        $btn = $this->getButtonContainer()->add($button);
        ++$this->btnCount;

        if ($this->hasFluidButton && $this->btnCount > 0) {
            $this->getButtonContainer()->addClass($this->words[$this->btnCount]);
        }

        return $btn;
    }

    /**
     * Add a series of buttons to this card.
     *
     * @return View|null
     */
    public function addButtons(array $buttons)
    {
        foreach ($buttons as $btn) {
            $btn = $this->addButton($btn);
        }

        return $this->getButtonContainer();
    }
}
