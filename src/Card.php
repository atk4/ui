<?php

declare(strict_types=1);

namespace Atk4\Ui;

use Atk4\Core\Factory;
use Atk4\Data\Model;
use Atk4\Ui\UserAction\ExecutorFactory;
use Atk4\Ui\UserAction\ExecutorInterface;
use Atk4\Ui\UserAction\SharedExecutor;

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
 * will have it's idField set as data-id HTML attribute for the card. Thus making
 * the ID available via javascript (new Jquery())->data('id')
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

    /** @var array|Button|null */
    public $buttons;

    /** @var bool How buttons are display inside button container */
    public $hasFluidButton = true;

    /** @var View|null */
    public $buttonContainer;

    /** @var bool Display model field as table inside card holder content */
    public $useTable = false;

    /** @var bool Use Field label with value data. */
    public $useLabel = false;

    /** @var string Default executor class. */
    public $executor = UserAction\ModalExecutor::class;

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
        if (!$this->buttonContainer) {
            $this->buttonContainer = $this->addExtraContent(new View(['ui' => 'buttons']));
            $this->getButtonContainer()->addClass('wrapping');
            if ($this->hasFluidButton) {
                $this->getButtonContainer()->addClass('fluid');
            }
        }

        return $this->buttonContainer;
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
     * If Fields are past with $model that field will be add
     * to the main section of this card.
     *
     * @param array<int, string>|null $fields
     */
    public function setModel(Model $entity, array $fields = null): void
    {
        $entity->assertIsLoaded();

        parent::setModel($entity);

        if ($fields === null) {
            $fields = array_keys($this->model->getFields(['editable', 'visible']));
        }

        $this->template->trySet('dataId', (string) $this->model->getId());

        View::addTo($this->getSection(), [$entity->getTitle(), 'class.header' => true]);
        $this->getSection()->addFields($entity, $fields, $this->useLabel, $this->useTable);
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
     * Execute Model user action via button in Card.
     *
     * @return $this
     */
    public function addClickAction(Model\UserAction $action, Button $button = null, array $args = [], string $confirm = null): self
    {
        $button = $this->addButton($button ?? $this->getExecutorFactory()->createTrigger($action, ExecutorFactory::CARD_BUTTON));

        $cardDeck = $this->getClosestOwner(CardDeck::class);

        $defaults = [];

        // setting arg for model ID
        // $args[0] is consider to hold a model ID, i.e. as a JS expression
        if ($this->model && $this->model->isLoaded() && !isset($args[0])) {
            $defaults[] = $this->model->getId();
            if ($cardDeck === null && !$action->isOwnerEntity()) {
                $action = $action->getActionForEntity($this->model);
            }
        }

        if ($args !== []) {
            $defaults['args'] = $args;
        }

        if ($confirm) {
            $defaults['confirm'] = $confirm;
        }

        if ($cardDeck !== null) {
            // mimic https://github.com/atk4/ui/blob/3c592b8f10fe67c61f179c5c8723b07f8ab754b9/src/Crud.php#L140
            // based on https://github.com/atk4/ui/blob/3c592b8f10fe67c61f179c5c8723b07f8ab754b9/src/UserAction/SharedExecutorsContainer.php#L24
            $isNew = !isset($cardDeck->sharedExecutorsContainer->sharedExecutors[$action->shortName]);
            if ($isNew) {
                $ex = $cardDeck->sharedExecutorsContainer->getExecutorFactory()->createExecutor($action, $cardDeck->sharedExecutorsContainer);

                $ex->onHook(UserAction\BasicExecutor::HOOK_AFTER_EXECUTE, \Closure::bind(static function (ExecutorInterface $ex, $return, $id) use ($cardDeck, $action) { // @phpstan-ignore-line
                    return $cardDeck->jsExecute($return, $action);
                }, null, CardDeck::class));

                $ex->executeModelAction();
                $cardDeck->sharedExecutorsContainer->sharedExecutors[$action->shortName] = new SharedExecutor($ex);
            }
        }

        $button->on('click', $cardDeck !== null ? $cardDeck->sharedExecutorsContainer->getExecutor($action) : $action, $defaults);

        return $this;
    }

    /**
     * Set extra content using model field.
     */
    public function addExtraFields(Model $model, array $fields, string $glue = null): void
    {
        // display extra field in line
        if ($glue) {
            $extra = '';
            foreach ($fields as $field) {
                $extra .= $model->get($field) . $glue;
            }
            $extra = rtrim($extra, $glue);
            $this->addExtraContent(new View([$extra, 'ui' => 'basic fitted segment']));
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
        if (!is_object($seed)) {
            $seed = Factory::factory([Button::class], $seed);
        }

        $button = $this->getButtonContainer()->add($seed);

        return $button;
    }
}
