<?php

declare(strict_types=1);

namespace Atk4\Ui;

use Atk4\Core\Factory;
use Atk4\Core\HookTrait;
use Atk4\Data\Field;
use Atk4\Data\Model;
use Atk4\Ui\Js\Jquery;
use Atk4\Ui\Js\JsExpressionable;
use Atk4\Ui\Js\JsReload;
use Atk4\Ui\UserAction\ConfirmationExecutor;
use Atk4\Ui\UserAction\ExecutorFactory;
use Atk4\Ui\UserAction\ExecutorInterface;

/**
 * @phpstan-type JsCallbackSetClosure \Closure(Jquery, mixed, mixed, mixed, mixed, mixed, mixed, mixed, mixed, mixed, mixed): (JsExpressionable|View|string|void)
 */
class Grid extends View
{
    use HookTrait;

    /** @var Menu|array|false Will be initialized to Menu object, however you can set this to false to disable menu. */
    public $menu;

    /** @var JsSearch|null */
    public $quickSearch;

    /** @var array Field names to search for in Model. It will automatically add quicksearch component to grid if set. */
    public $searchFieldNames = [];

    /**
     * Paginator is automatically added below the table and will divide long tables into pages.
     *
     * You can provide your own Paginator object here to customize.
     *
     * @var Paginator|false
     */
    public $paginator;

    /** @var int Number of items per page to display. */
    public $ipp = 50;

    /**
     * Calling addActionButton will add a new column inside $table, and will be re-used
     * for next addActionButton().
     *
     * @var Table\Column\ActionButtons|null
     */
    public $actionButtons;

    /**
     * Calling addActionMenuItem will add a new column inside $table with dropdown menu,
     * and will be re-used for next addActionMenuItem().
     *
     * @var Table\Column|null
     */
    public $actionMenu;

    /**
     * Calling addSelection will add a new column inside $table, containing checkboxes.
     * This column will be stored here, in case you want to access it.
     *
     * @var Table\Column\Checkbox
     */
    public $selection;

    /**
     * Grid can be sorted by clicking on column headers. This will be automatically enabled
     * if Model supports ordering. You may override by setting true/false.
     *
     * @var bool
     */
    public $sortable;

    /** @var string|null Set this if you want GET argument name to look beautifully for sorting. */
    public $sortTrigger;

    /** @var Table|false Component that actually renders data rows / columns and possibly totals. */
    public $table;

    /** @var View The container for table and paginator. */
    public $container;

    public $defaultTemplate = 'grid.html';

    /** @var array Defines which Table Decorator to use for ActionButtons. */
    protected $actionButtonsDecorator = [Table\Column\ActionButtons::class];

    /** @var array Defines which Table Decorator to use for ActionMenu. */
    protected $actionMenuDecorator = [Table\Column\ActionMenu::class, 'label' => 'Actions...'];

    protected function init(): void
    {
        parent::init();

        $this->container = View::addTo($this, ['template' => $this->template->cloneRegion('Container')]);
        $this->template->del('Container');

        if (!$this->sortTrigger) {
            $this->sortTrigger = $this->name . '_sort';
        }

        if ($this->menu !== false && !is_object($this->menu)) {
            $this->menu = $this->add(Factory::factory([Menu::class, 'activateOnClick' => false], $this->menu), 'Menu');
        }

        $this->table = $this->initTable();

        if ($this->paginator !== false) {
            $seg = View::addTo($this->container, [], ['Paginator'])->setStyle('text-align', 'center');
            $this->paginator = $seg->add(Factory::factory([Paginator::class, 'reload' => $this->container], $this->paginator));
            $this->stickyGet($this->paginator->name);
        }

        // TODO dirty way to set stickyGet - add addQuickSearch to find the expected search input component ID and then remove it
        if ($this->menu !== false) {
            $appUniqueHashesBackup = $this->getApp()->uniqueNameHashes;
            $menuElementNameCountsBackup = \Closure::bind(fn () => $this->_elementNameCounts, $this->menu, AbstractView::class)();
            try {
                $menuRight = $this->menu->addMenuRight(); // @phpstan-ignore-line
                $menuItemView = View::addTo($menuRight->addItem()->setElement('div'));
                $quickSearch = JsSearch::addTo($menuItemView);
                $this->stickyGet($quickSearch->name . '_q');
                $this->menu->removeElement($menuRight->shortName);
            } finally {
                $this->getApp()->uniqueNameHashes = $appUniqueHashesBackup;
                \Closure::bind(fn () => $this->_elementNameCounts = $menuElementNameCountsBackup, $this->menu, AbstractView::class)();
            }
        }
    }

    protected function initTable(): Table
    {
        /** @var Table */
        $table = $this->container->add(Factory::factory([Table::class, 'class.very compact very basic striped single line' => true, 'reload' => $this->container], $this->table), 'Table');

        return $table;
    }

    /**
     * Add new column to grid. If column with this name already exists,
     * an. Simply calls Table::addColumn(), so check that method out.
     *
     * @param string|null                             $name            Data model field name
     * @param array|Table\Column                      $columnDecorator
     * @param ($name is null ? array{} : array|Field) $field
     *
     * @return Table\Column
     */
    public function addColumn(?string $name, $columnDecorator = [], $field = [])
    {
        return $this->table->addColumn($name, $columnDecorator, $field);
    }

    /**
     * Add additional decorator for existing column.
     *
     * @param array|Table\Column $seed
     *
     * @return Table\Column
     */
    public function addDecorator(string $name, $seed)
    {
        return $this->table->addDecorator($name, $seed);
    }

    /**
     * Add a new button to the Grid Menu with a given text.
     *
     * @param string $label
     */
    public function addButton($label): Button
    {
        if (!$this->menu) {
            throw new Exception('Unable to add Button without Menu');
        }

        return Button::addTo($this->menu->addItem(), [$label]);
    }

    /**
     * Set item per page value.
     *
     * If an array is passed, it will also add an ItemPerPageSelector to paginator.
     *
     * @param int|list<int> $ipp
     * @param string        $label
     */
    public function setIpp($ipp, $label = 'Items per page:'): void
    {
        if (is_array($ipp)) {
            $this->addItemsPerPageSelector($ipp, $label);
        } else {
            $this->ipp = $ipp;
        }
    }

    /**
     * Add ItemsPerPageSelector View in grid menu or paginator in order to dynamically setup number of item per page.
     *
     * @param list<int> $items an array of item's per page value
     * @param string    $label the memu item label
     *
     * @return $this
     */
    public function addItemsPerPageSelector(array $items = [10, 100, 1000], $label = 'Items per page:')
    {
        $ipp = (int) $this->container->stickyGet('ipp');
        if ($ipp) {
            $this->ipp = $ipp;
        } else {
            $this->ipp = $items[0];
        }

        $pageLength = ItemsPerPageSelector::addTo($this->paginator, ['pageLengthItems' => $items, 'label' => $label, 'currentIpp' => $this->ipp], ['afterPaginator']);
        $this->paginator->template->trySet('PaginatorType', 'ui grid');

        $sortBy = $this->getSortBy();
        if ($sortBy) {
            $pageLength->stickyGet($this->sortTrigger, $sortBy);
        }

        $pageLength->onPageLengthSelect(function (int $ipp) {
            $this->ipp = $ipp;
            $this->setModelLimitFromPaginator();
            // add ipp to quicksearch
            if ($this->quickSearch instanceof JsSearch) {
                $this->container->js(true, $this->quickSearch->js()->atkJsSearch('setUrlArgs', ['ipp', $this->ipp]));
            }
            $this->applySort();

            // return the view to reload
            return $this->container;
        });

        return $this;
    }

    /**
     * Add dynamic scrolling paginator.
     *
     * @param int    $ipp          number of item per page to start with
     * @param array  $options      an array with JS Scroll plugin options
     * @param View   $container    the container holding the lister for scrolling purpose
     * @param string $scrollRegion A specific template region to render. Render output is append to container HTML element.
     *
     * @return $this
     */
    public function addJsPaginator($ipp, $options = [], $container = null, $scrollRegion = 'Body')
    {
        if ($this->paginator) {
            $this->paginator->destroy();
            // prevent action(count) to be output twice
            $this->paginator = null;
        }

        $sortBy = $this->getSortBy();
        if ($sortBy) {
            $this->stickyGet($this->sortTrigger, $sortBy);
        }
        $this->applySort();

        $this->table->addJsPaginator($ipp, $options, $container, $scrollRegion);

        return $this;
    }

    /**
     * Add dynamic scrolling paginator in container.
     * Use this to make table headers fixed.
     *
     * @param int    $ipp             number of item per page to start with
     * @param int    $containerHeight number of pixel the table container should be
     * @param array  $options         an array with JS Scroll plugin options
     * @param View   $container       the container holding the lister for scrolling purpose
     * @param string $scrollRegion    A specific template region to render. Render output is append to container HTML element.
     *
     * @return $this
     */
    public function addJsPaginatorInContainer($ipp, $containerHeight, $options = [], $container = null, $scrollRegion = 'Body')
    {
        $this->table->hasCollapsingCssActionColumn = false;
        $options = array_merge($options, [
            'hasFixTableHeader' => true,
            'tableContainerHeight' => $containerHeight,
        ]);
        // adding a state context to JS scroll plugin
        $options = array_merge(['stateContext' => $this->container], $options);

        return $this->addJsPaginator($ipp, $options, $container, $scrollRegion);
    }

    /**
     * Add Search input field using JS action.
     * By default, will query server when using Enter key on input search field.
     * You can change it to query server on each keystroke by passing $autoQuery true,.
     *
     * @param array $fields       the list of fields to search for
     * @param bool  $hasAutoQuery will query server on each key pressed
     */
    public function addQuickSearch($fields = [], $hasAutoQuery = false): void
    {
        if (!$this->model) {
            throw new Exception('Call setModel() before addQuickSearch()');
        }

        if (!$fields) {
            $fields = [$this->model->titleField];
        }

        if (!$this->menu) {
            throw new Exception('Unable to add QuickSearch without Menu');
        }

        $view = View::addTo($this->menu->addMenuRight()->addItem()->setElement('div'));

        $this->quickSearch = JsSearch::addTo($view, ['reload' => $this->container, 'autoQuery' => $hasAutoQuery]);
        $q = $this->stickyGet($this->quickSearch->name . '_q') ?? '';
        $qWords = preg_split('~\s+~', $q, -1, \PREG_SPLIT_NO_EMPTY);
        if (count($qWords) > 0) {
            $andScope = Model\Scope::createAnd();
            foreach ($qWords as $v) {
                $orScope = Model\Scope::createOr();
                foreach ($fields as $field) {
                    $orScope->addCondition($field, 'like', '%' . $v . '%');
                }
                $andScope->addCondition($orScope);
            }
            $this->model->addCondition($andScope);
        }
        $this->quickSearch->initValue = $q;
    }

    public function jsReload($args = [], $afterSuccess = null, $apiConfig = []): JsExpressionable
    {
        return new JsReload($this->container, $args, $afterSuccess, $apiConfig);
    }

    /**
     * Adds a new button into the action column on the right. For Crud this
     * column will already contain "delete" and "edit" buttons.
     *
     * @param string|array|View                     $button     Label text, object or seed for the Button
     * @param JsExpressionable|JsCallbackSetClosure $action
     * @param bool                                  $isDisabled
     *
     * @return View
     */
    public function addActionButton($button, $action = null, string $confirmMsg = '', $isDisabled = false)
    {
        return $this->getActionButtons()->addButton($button, $action, $confirmMsg, $isDisabled);
    }

    /**
     * Add a button for executing a model action via an action executor.
     *
     * @return View
     */
    public function addExecutorButton(UserAction\ExecutorInterface $executor, Button $button = null)
    {
        if ($button !== null) {
            $this->add($button);
        } else {
            $button = $this->getExecutorFactory()->createTrigger($executor->getAction(), ExecutorFactory::TABLE_BUTTON);
        }

        $confirmation = $executor->getAction()->getConfirmation();
        if (!$confirmation) {
            $confirmation = '';
        }
        $disabled = is_bool($executor->getAction()->enabled) ? !$executor->getAction()->enabled : $executor->getAction()->enabled;

        return $this->getActionButtons()->addButton($button, $executor, $confirmation, $disabled);
    }

    private function getActionButtons(): Table\Column\ActionButtons
    {
        if ($this->actionButtons === null) {
            $this->actionButtons = $this->table->addColumn(null, $this->actionButtonsDecorator);
        }

        return $this->actionButtons; // @phpstan-ignore-line
    }

    /**
     * Similar to addActionButton. Will add Button that when click will display
     * a Dropdown menu.
     *
     * @param View|string                           $view
     * @param JsExpressionable|JsCallbackSetClosure $action
     *
     * @return View
     */
    public function addActionMenuItem($view, $action = null, string $confirmMsg = '', bool $isDisabled = false)
    {
        return $this->getActionMenu()->addActionMenuItem($view, $action, $confirmMsg, $isDisabled);
    }

    /**
     * @return View
     */
    public function addExecutorMenuItem(ExecutorInterface $executor)
    {
        $item = $this->getExecutorFactory()->createTrigger($executor->getAction(), ExecutorFactory::TABLE_MENU_ITEM);
        // ConfirmationExecutor take care of showing the user confirmation, thus make it empty
        $confirmation = !$executor instanceof ConfirmationExecutor ? $executor->getAction()->getConfirmation() : '';
        if (!$confirmation) {
            $confirmation = '';
        }
        $disabled = is_bool($executor->getAction()->enabled) ? !$executor->getAction()->enabled : $executor->getAction()->enabled;

        return $this->getActionMenu()->addActionMenuItem($item, $executor, $confirmation, $disabled);
    }

    /**
     * @return Table\Column\ActionMenu
     */
    private function getActionMenu()
    {
        if (!$this->actionMenu) {
            $this->actionMenu = $this->table->addColumn(null, $this->actionMenuDecorator);
        }

        return $this->actionMenu; // @phpstan-ignore-line
    }

    /**
     * Add action menu items using Model.
     * You may specify the scope of actions to be added.
     *
     * @param string|null $appliesTo the scope of model action
     */
    public function addActionMenuFromModel(string $appliesTo = null): void
    {
        foreach ($this->model->getUserActions($appliesTo) as $action) {
            $this->addActionMenuItem($action);
        }
    }

    /**
     * An array of column name where filter is needed.
     * Leave empty to include all column in grid.
     *
     * @param array|null $names an array with the name of column
     *
     * @return $this
     */
    public function addFilterColumn($names = null)
    {
        if (!$this->menu) {
            throw new Exception('Unable to add Filter Column without Menu');
        }
        $this->menu->addItem(['Clear Filters'], new JsReload($this->table->reload, ['atk_clear_filter' => 1]));
        $this->table->setFilterColumn($names);

        return $this;
    }

    /**
     * Add a dropdown menu to header column.
     *
     * @param string                                                $columnName the name of column where to add dropdown
     * @param array                                                 $items      the menu items to add
     * @param \Closure(string): (JsExpressionable|View|string|void) $fx         the callback function to execute when an item is selected
     * @param string                                                $icon       the icon
     * @param string                                                $menuId     the menu ID return by callback
     */
    public function addDropdown(string $columnName, $items, \Closure $fx, $icon = 'caret square down', $menuId = null): void
    {
        $column = $this->table->columns[$columnName];

        if (!$menuId) {
            $menuId = $columnName;
        }

        $column->addDropdown($items, static function (string $item) use ($fx) {
            return $fx($item);
        }, $icon, $menuId);
    }

    /**
     * Add a popup to header column.
     *
     * @param string $columnName the name of column where to add popup
     * @param Popup  $popup      popup view
     * @param string $icon       the icon
     *
     * @return mixed
     */
    public function addPopup($columnName, $popup = null, $icon = 'caret square down')
    {
        $column = $this->table->columns[$columnName];

        return $column->addPopup($popup, $icon);
    }

    /**
     * Similar to addActionButton but when button is clicked, modal is displayed
     * with the $title and $callback is executed.
     *
     * @param string|array|View                 $button
     * @param string                            $title
     * @param \Closure(View, string|null): void $callback
     * @param array                             $args     extra URL argument for callback
     *
     * @return View
     */
    public function addModalAction($button, $title, \Closure $callback, $args = [])
    {
        return $this->getActionButtons()->addModal($button, $title, $callback, $this, $args);
    }

    /**
     * @return list<string>
     */
    private function explodeSelectionValue(string $value): array
    {
        return $value === '' ? [] : explode(',', $value);
    }

    /**
     * Similar to addActionButton but apply to a multiple records selection and display in menu.
     * When menu item is clicked, $callback is executed.
     *
     * @param string|array|MenuItem                               $item
     * @param \Closure(Js\Jquery, list<string>): JsExpressionable $callback
     * @param array                                               $args     extra URL argument for callback
     *
     * @return View
     */
    public function addBulkAction($item, \Closure $callback, $args = [])
    {
        $menuItem = $this->menu->addItem($item);
        $menuItem->on('click', function (Js\Jquery $j, string $value) use ($callback) {
            return $callback($j, $this->explodeSelectionValue($value));
        }, [$this->selection->jsChecked()]);

        return $menuItem;
    }

    /**
     * Similar to addModalAction but apply to a multiple records selection and display in menu.
     * When menu item is clicked, modal is displayed with the $title and $callback is executed.
     *
     * @param string|array|MenuItem              $item
     * @param string                             $title
     * @param \Closure(View, list<string>): void $callback
     * @param array                              $args     extra URL argument for callback
     *
     * @return View
     */
    public function addModalBulkAction($item, $title, \Closure $callback, $args = [])
    {
        $modalDefaults = is_string($title) ? ['title' => $title] : []; // @phpstan-ignore-line

        $modal = Modal::addTo($this->getOwner(), $modalDefaults);
        $modal->set(function (View $t) use ($callback) {
            $callback($t, $this->explodeSelectionValue($t->stickyGet($this->name) ?? ''));
        });

        $menuItem = $this->menu->addItem($item);
        $menuItem->on('click', $modal->jsShow(array_merge([$this->name => $this->selection->jsChecked()], $args)));

        return $menuItem;
    }

    /**
     * Get sortBy value from URL parameter.
     */
    public function getSortBy(): ?string
    {
        return $this->getApp()->tryGetRequestQueryParam($this->sortTrigger);
    }

    /**
     * Apply ordering to the current model as per the sort parameters.
     */
    public function applySort(): void
    {
        if ($this->sortable === false) {
            return;
        }

        $sortBy = $this->getSortBy();

        if ($sortBy && $this->paginator) {
            $this->paginator->addReloadArgs([$this->sortTrigger => $sortBy]);
        }

        $isDesc = false;
        if ($sortBy && substr($sortBy, 0, 1) === '-') {
            $isDesc = true;
            $sortBy = substr($sortBy, 1);
        }

        $this->table->sortable = true;

        if ($sortBy && isset($this->table->columns[$sortBy]) && $this->model->hasField($sortBy)) {
            $this->model->setOrder($sortBy, $isDesc ? 'desc' : 'asc');
            $this->table->sortBy = $sortBy;
            $this->table->sortDirection = $isDesc ? 'desc' : 'asc';
        }

        $this->table->on(
            'click',
            'thead>tr>th.sortable',
            new JsReload($this->container, [$this->sortTrigger => (new Jquery())->data('sort')])
        );
    }

    /**
     * Sets data Model of Grid.
     *
     * If $columns is not defined, then automatically will add columns for all
     * visible model fields. If $columns is set to false, then will not add
     * columns at all.
     *
     * @param array<int, string>|null $columns
     */
    public function setModel(Model $model, array $columns = null): void
    {
        $this->table->setModel($model, $columns);

        parent::setModel($model);

        if ($this->searchFieldNames) {
            $this->addQuickSearch($this->searchFieldNames, true);
        }
    }

    /**
     * Makes rows of this grid selectable by creating new column on the left with
     * checkboxes.
     *
     * @return Table\Column\Checkbox
     */
    public function addSelection()
    {
        $this->selection = $this->table->addColumn(null, [Table\Column\Checkbox::class]);

        // move last column to the beginning in table column array
        array_unshift($this->table->columns, array_pop($this->table->columns));

        return $this->selection;
    }

    /**
     * Add column with drag handler on each row.
     * Drag handler allow to reorder table via drag and drop.
     *
     * @return Table\Column
     */
    public function addDragHandler()
    {
        $handler = $this->table->addColumn(null, [Table\Column\DragHandler::class]);

        // move last column to the beginning in table column array
        array_unshift($this->table->columns, array_pop($this->table->columns));

        return $handler;
    }

    private function setModelLimitFromPaginator(): void
    {
        $this->paginator->setTotal((int) ceil($this->model->executeCountQuery() / $this->ipp));
        $this->model->setLimit($this->ipp, ($this->paginator->page - 1) * $this->ipp);
    }

    /**
     * Before rendering take care of data sorting.
     */
    protected function renderView(): void
    {
        // take care of sorting
        if (!$this->table->jsPaginator) {
            $this->applySort();
        }

        parent::renderView();
    }

    protected function recursiveRender(): void
    {
        // bind with paginator
        if ($this->paginator) {
            $this->setModelLimitFromPaginator();
        }

        if ($this->quickSearch instanceof JsSearch) {
            $sortBy = $this->getSortBy();
            if ($sortBy) {
                $this->container->js(true, $this->quickSearch->js()->atkJsSearch('setUrlArgs', [$this->sortTrigger, $sortBy]));
            }
        }

        parent::recursiveRender();
    }

    /**
     * Proxy function for Table::jsRow().
     *
     * @return Jquery
     */
    public function jsRow(): JsExpressionable
    {
        return $this->table->jsRow();
    }
}
