<?php

declare(strict_types=1);

namespace Atk4\Ui;

use Atk4\Core\Factory;
use Atk4\Data\Model;
use Atk4\Ui\UserAction\ExecutorFactory;

/**
 * Card can contain arbitrary information.
 *
 * Card contains one main CardSection for adding content
 * but it can contains other CardSection using addSection method.
 *
 * Each section can have it's own model, field to be displayed has
 * field value, field label, field value or as table.
 *
 * Card also has an extra content section which is formatted
 * separately from Section content. Extra content may also have
 * model field display.
 *
 * Multiple model can be used to display various content on each card section.
 * When using model or models, the first model that get set via setModel method
 * will have it's idField set as data-id html attribute for the card. Thus making
 * the id available via javascript (new Jquery())->data('id')
 */
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

    /** @var bool Display model field as table inside card holder content */
    public $useTable = false;

    /** @var bool Use Field label with value data. */
    public $useLabel = false;

    /** @var string Default executor class. */
    public $executor = UserAction\ModalExecutor::class;

    /** @var array Columns CSS wide classes */
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
     * @return CardSection
     */
    public function getSection()
    {
        if (!$this->section) {
            $this->section = CardSection::addToWithCl($this, [$this->cardSection, 'card' => $this]);
        }

        return $this->section;
    }

    /**
     * Get the image container of this card.
     *
     * @return View
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
     * @return View
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
     * @return View
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
     * @return View
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
     * @param array<int, string>|null $fields
     */
    public function setModel(Model $model, array $fields = null): void
    {
        $model->assertIsLoaded();

        parent::setModel($model);

        if ($fields === null) {
            $fields = array_keys($this->model->getFields(['editable', 'visible']));
        }

        $this->setDataId($this->model->getId());

        View::addTo($this->getSection(), [$model->getTitle(), 'class.header' => true]);
        $this->getSection()->addFields($model, $fields, $this->useLabel, $this->useTable);
    }

    /**
     * Set data-id attribute of this card.
     *
     * @param string $id
     */
    public function setDataId($id): void
    {
        $this->template->trySet('dataId', $id);
    }

    /**
     * Add action from Model.
     */
    public function addModelActions(Model $model): void
    {
        $this->setModel($model);

        $actions = $model->getUserActions(Model\UserAction::APPLIES_TO_SINGLE_RECORD);
        foreach ($actions as $action) {
            $this->addAction($action, $this->executor);
        }

        $actions = $model->getUserActions(Model\UserAction::APPLIES_TO_NO_RECORDS);
        foreach ($actions as $action) {
            $this->addAction($action, $this->executor);
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
            View::addTo($section, [$title, 'class.header' => true]);
        }

        if ($model && $fields) {
            $section->setModel($model);
            $section->addFields($model, $fields, $useTable, $useLabel);
        }

        return $section;
    }

    /**
     * Add action executor to card.
     *
     * @param class-string<View&UserAction\ExecutorInterface> $executorClass
     * @param Button|array                                    $button
     */
    public function addAction(Model\UserAction $action, $executorClass, $button = null): void
    {
        if (!$button) {
            $button = new Button([$action->caption]);
        }
        $btn = $this->addButton($button);

        $vp = VirtualPage::addTo($this)->set(function (View $page) use ($executorClass, $action) {
            $id = $this->stickyGet($this->name);

            $executor = $page->add(new $executorClass());

            $action = $action->getActionForEntity($action->getModel()->load($id));

            $executor->setAction($action);
        });

        $btn->on('click', new JsModal($action->caption, $vp, [$this->name => (new Jquery())->parents('.atk-card')->data('id')]));
    }

    /**
     * Execute Model user action via button in Card.
     */
    public function addClickAction(Model\UserAction $action, Button $button = null, array $args = [], string $confirm = null): self
    {
        $btn = $this->addButton($button ?? $this->getExecutorFactory()->createTrigger($action, ExecutorFactory::CARD_BUTTON));

        $defaults = [];

        // Setting arg for model id. $args[0] is consider to hold a model id, i.e. as a js expression.
        if ($this->model && $this->model->isLoaded() && !isset($args[0])) {
            $defaults[] = $this->model->getId();
        }

        if ($args !== []) {
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
    public function addExtraFields(Model $model, array $fields, string $glue = null): void
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
                $this->addExtraContent(new View([$model->get($field), 'class.ui basic fitted segment' => true]));
            }
        }
    }

    /**
     * Add Description to main card content.
     *
     * @param string|View $description
     *
     * @return View
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
     * @return View
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
     * @param Button|array $seed
     *
     * @return View
     */
    public function addButton($seed)
    {
        if ($this->hasFluidButton && $this->btnCount > 0) {
            $this->getButtonContainer()->removeClass($this->words[$this->btnCount]);
        }

        if (!is_object($seed)) {
            $seed = Factory::factory([Button::class], $seed);
        }

        $btn = $this->getButtonContainer()->add($seed);
        ++$this->btnCount;

        if ($this->hasFluidButton && $this->btnCount > 0) {
            $this->getButtonContainer()->addClass($this->words[$this->btnCount]);
        }

        return $btn;
    }
}
