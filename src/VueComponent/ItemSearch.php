<?php

declare(strict_types=1);

namespace Atk4\Ui\VueComponent;

use Atk4\Data\Model;
use Atk4\Ui\View;

/**
 * Will send query with define callback and reload a specific view.
 */
class ItemSearch extends View
{
    /** @var View|string the atk4 View to be reloaded or a id selector string View to be reloaded that contains data to be filtered. */
    public $reload;

    /** @var string The initial query. */
    public $q;

    /** @var string The CSS for the input field. */
    public $inputCss = 'ui input right icon transparent';

    /**
     * Keyboard debounce time in ms for the input.
     * Will limit network request while user is typing search criteria.
     *
     * @var int
     */
    public $inputTimeOut = 250;

    /**
     * The jQuery selector where you need to add the Fomantic-UI 'loading' class.
     * Default to reload selector.
     *
     * @var View
     */
    public $context;

    /** @var string|null The URL argument name use for query. If null, then->>name will be assigned. */
    public $queryArg;

    public $defaultTemplate = 'item-search.html';

    protected function init(): void
    {
        parent::init();

        if (!$this->queryArg) {
            $this->queryArg = $this->name;
        }

        if (!$this->q) {
            $this->q = $this->getQuery();
        }
    }

    /**
     * @return string
     */
    public function getQuery()
    {
        return $this->getApp()->tryGetRequestQueryParam($this->queryArg) ?? '';
    }

    public function setModelCondition(Model $model): void
    {
        $q = $this->getQuery();
        if ($q) {
            $model->addCondition($model->titleField, 'like', '%' . $q . '%');
        }
    }

    protected function renderView(): void
    {
        $this->class = [];
        parent::renderView();

        // $reloadId is the view ID selector name that needs to be reloaded
        // this will be pass as get argument to __atk_reload
        if ($this->reload instanceof View) {
            $reloadId = $this->reload->name;
        } else {
            $reloadId = $this->reload;
        }

        $this->vue('AtkItemSearch', [
            'reload' => $reloadId,
            'queryArg' => $this->queryArg,
            'url' => $this->reload->jsUrl(),
            'q' => $this->q,
            'context' => $this->context,
            'options' => [
                'inputTimeOut' => $this->inputTimeOut,
                'inputCss' => $this->inputCss,
            ],
        ]);
    }
}
