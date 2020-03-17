<?php
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
 * the id available via javascript (new jQuery())->data('id')
 */

namespace atk4\ui;

use atk4\data\Model;
use atk4\data\UserAction\Generic;
use atk4\ui\ActionExecutor\Event;
use atk4\ui\ActionExecutor\UserAction;

class Card extends View
{
    public $ui = 'card atk-card';

    public $defaultTemplate = 'card.html';

    /** @var null|View A View that hold the image. */
    public $imageContainer = null;

    /** @var string Card box type. */
    public $cardCss = 'segment';

    /** @var null|string|Image A path to the image src or the image view. */
    public $image = null;

    /** @var null|CardSection The main card section of this card */
    public $section = null;

    /** @var string The CardSection default class name. */
    public $cardSection = CardSection::class;

    /** @var null | View The extra content view container for the card. */
    public $extraContainer = null;

    /** @var null|string|View A description inside the Card content. */
    public $description = null;

    /** @var null|array|Button A button or an array of Buttons */
    public $buttons = null;

    /** @var bool How buttons are display inside button container */
    public $hasFluidButton = true;

    /** @var null|View The button Container for Button */
    public $btnContainer = null;

    /** @var string Table css class */
    // public $tableClass = 'ui fixed small';

    /** @var bool Display model field as table inside card holder content */
    public $useTable = false;

    /** @var bool Use Field label with value data. */
    public $useLabel = false;

    /** @var string Default executor class. */
    public $executor = UserAction::class;

    /** @var array Array of columns css wide classes */
    protected $words = [
        '', 'fluid', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine', 'ten', 'eleven', 'twelve',
        'thirteen', 'fourteen', 'fifteen', 'sixteen',
    ];

    /** @var int The number of buttons */
    private $btnCount = 0;

    public function init()
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
     * @throws Exception
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
     * @throws Exception
     *
     * @return View|null
     */
    public function getImageContainer()
    {
        if (!$this->imageContainer) {
            $this->imageContainer = $this->add(['View', 'class' => ['image']], 'Image');
        }

        return $this->imageContainer;
    }

    /**
     * Get the ExtraContainer of this card.
     *
     * @throws Exception
     *
     * @return View|null
     */
    public function getExtraContainer()
    {
        if (!$this->extraContainer) {
            $this->extraContainer = $this->add(['View', 'class' => ['extra content']], 'ExtraContent');
        }

        return $this->extraContainer;
    }

    /**
     * Get the button container of this card.
     *
     * @throws Exception
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
     * @param View $view
     *
     * @throws Exception
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
     * @param \atk4\data\Model $m      The model.
     * @param array|false      $fields An array of fields name to display in content.
     *
     * @throws Exception
     * @throws \atk4\data\Exception
     *
     * @return \atk4\data\Model|void
     */
    public function setModel(Model $m, $fields = null)
    {
        if (!$m->loaded()) {
            throw new Exception('Model need to be loaded.');
        }

        if (!$this->model) {
            $m = parent::setModel($m);
        }

        if ($fields === null) {
            $fields = array_keys($this->model->getFields(['editable', 'visible']));
        } elseif ($fields === false) {
            $fields = [];
        }

        $this->setDataId($this->model->get($this->model->id_field));

        if ($fields && is_array($fields)) {
            $this->getSection()->add(['View', $m->getTitle(), ['class' => 'header']]);
            $this->getSection()->addFields($m, $fields, $this->useLabel, $this->useTable);
        }

        return $m;
    }

    /**
     * Set data-id attribute of this card.
     *
     * @param $id
     */
    public function setDataId($id)
    {
        $this->template->trySet('dataId', $id);
    }

    /**
     * Add actions from various model.
     *
     * @param array $models
     *
     * @throws Exception
     * @throws \atk4\core\Exception
     * @throws \atk4\data\Exception
     */
    public function addModelsActions(array $models)
    {
        foreach ($models as $model) {
            $this->addModelActions($model);
        }
    }

    /**
     * Add action from Model.
     *
     * @param Model $model
     *
     * @throws Exception
     * @throws \atk4\core\Exception
     * @throws \atk4\data\Exception
     */
    public function addModelActions(Model $model)
    {
        if ($singleActions = $model->getActions(Generic::SINGLE_RECORD)) {
            $this->setModel($model);
            foreach ($singleActions as $action) {
                $this->addAction($action, $this->executor);
            }
        }

        if ($noRecordAction = $model->getActions(GENERIC::NO_RECORDS)) {
            foreach ($noRecordAction as $action) {
                $this->addAction($action, $this->executor);
            }
        }
    }

    /**
     * Add a CardSection to this card.
     *
     * @param string|null $title
     * @param Model|null  $model
     * @param array|null  $fields
     * @param bool        $useTable
     * @param bool        $useLabel
     *
     * @throws Exception
     * @throws \atk4\core\Exception
     * @throws \atk4\data\Exception
     *
     * @return View
     */
    public function addSection(string $title = null, Model $model = null, array $fields = null, bool $useTable = false, bool $useLabel = false)
    {
        $section = $this->add([$this->cardSection, 'card' => $this], 'Section');
        if ($title) {
            $section->add(['View', $title, ['class' => 'header']]);
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
     * @param $action
     * @param $executor
     * @param null $button
     *
     * @throws Exception
     */
    public function addAction($action, $executor, $button = null)
    {
        if (!$button) {
            $button = new Button([$action->caption]);
        }
        $btn = $this->addButton($button);

        $vp = $this->add('VirtualPage')->set(function ($page) use ($executor, $action) {
            $id = $this->stickyGet($this->name);

            $page->add($executor = new $executor());

            $action->owner->load($id);

            $executor->setAction($action);
        });

        $btn->on('click', new jsModal($action->caption, $vp, [$this->name => (new jQuery())->parents('.atk-card')->data('id')]));
    }

    /**
     * Add an Event action executor of type 'click' using a button
     * as target.
     *
     * @param Generic $action
     * @param null    $button
     * @param []      $args    The action argument
     * @param string  $confirm The confirmation message.
     *
     * @throws Exception
     *
     * @return Card
     */
    public function addClickAction(Generic $action, $button = null, $args = [], $confirm = null)
    {
        $defaults = [];
        if (!$button) {
            $button = $action->ui['button'] ?? new Button([$action->caption]);
        }
        $btn = $this->addButton($button);

        // Setting arg for model id. $args[0] is consider to hold a model id, i.e. as a js expression.
        if ($this->model && $this->model->loaded() && !isset($args[0])) {
            $defaults[] = $this->model->id;
        }

        if (!empty($args)) {
            $defaults['args'] = $args;
        }

        if ($confirm) {
            $defaults['confirm'] = $confirm;
        } elseif (isset($action->ui['confirm'])) {
            $defaults['confirm'] = $action->ui['confirm'];
        }

        $btn->on('click', $action, $defaults);

        return $this;
    }

    /**
     * Set extra content using model field.
     *
     * @param Model  $m      The model
     * @param array  $fields An array of fields name.
     * @param string $glue   A separator string between each field.
     *
     * @throws Exception
     * @throws \atk4\data\Exception
     */
    public function addExtraFields($m, $fields, $glue = null)
    {
        $this->setModel($m, false);

        // display extra field in line.
        if ($glue) {
            $extra = '';
            foreach ($fields as $field) {
                $extra .= $m->get($field).$glue;
            }
            $extra = rtrim($extra, $glue);
            $this->addExtraContent(new View([$extra, 'ui'=>'ui basic fitted segment']));
        } else {
            foreach ($fields as $field) {
                $this->addExtraContent(new View([$m->get($field), 'ui basic fitted segment']));
            }
        }
    }

    /**
     * Add Description to main card content.
     *
     * @param string|View $description
     *
     * @throws Exception
     *
     * @return View|string|null The description to add.
     */
    public function addDescription($description)
    {
        return $this->getSection()->addDescription($description);
    }

    /**
     * Add Extra content to the Card.
     * Extra content is added at the bottom of the card.
     *
     * @param View $view
     *
     * @throws Exception
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
     * @throws Exception
     *
     * @return View|null
     */
    public function addImage($img)
    {
        if (is_string($img)) {
            $img = $this->getImageContainer()->add(new Image([$img]));
        } else {
            $img = $this->getImageContainer()->add($img);
        }

        return $img;
    }

    /**
     * Add button to card.
     *
     * @param Button $button  A Button.
     * @param bool   $isFluid Make the buttons spread evenly in Card.
     *
     * @throws Exception
     *
     * @return View|null
     */
    public function addButton($button)
    {
        if ($this->hasFluidButton && $this->btnCount > 0) {
            $this->getButtonContainer()->removeClass($this->words[$this->btnCount]);
        }

        if (!is_object($button)) {
            $button = $this->factory(Button::class, [$button]);
        }

        $btn = $this->getButtonContainer()->add($button);
        $this->btnCount++;

        if ($this->hasFluidButton && $this->btnCount > 0) {
            $this->getButtonContainer()->addClass($this->words[$this->btnCount]);
        }

        return $btn;
    }

    /**
     * Add a series of buttons to this card.
     *
     * @param $buttons
     *
     * @throws Exception
     *
     * @return View|null
     */
    public function addButtons($buttons)
    {
        foreach ($buttons as $btn) {
            $btn = $this->addButton($btn);
        }

        return $this->getButtonContainer();
    }
}
