<?php

declare(strict_types=1);

namespace Atk4\Ui\VueComponent;

use Atk4\Data\Model;
use Atk4\Ui\JsVueService;
use Atk4\Ui\View;

/**
 * Will send query with define callback and reload a specific view.
 */
class ItemSearch extends View
{
    /** @var View|string the atk4 View to be reloaded or a jquery id selector string View to be reloaded that contains data to be filtered. */
    public $reload;

    /** @var string The initial query. */
    public $q;

    /** @var string The css for the input field. */
    public $inputCss = 'ui input right icon transparent';

    /**
     * Keyboard debounce time in ms for the input.
     * Will limit network request while user is typing search criteria.
     *
     * @var int
     */
    public $inputTimeOut = 250;

    /**
     * The jquery selector where you need to add the Fomantic-UI 'loading' class.
     * Default to reload selector.
     *
     * @var View
     */
    public $context;

    /** @var string|null The URL argument name use for query. If null, then->>name will be assiged. */
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
     * Return query string sent by request.
     *
     * @return string
     */
    public function getQuery()
    {
        return $_GET[$this->queryArg] ?? null;
    }

    /**
     * Set model condition base on search request.
     */
    public function setModelCondition(Model $model): void
    {
        if ($q = $this->getQuery()) {
            $model->addCondition($model->titleField, 'like', '%' . $q . '%');
        }
    }

    protected function renderView(): void
    {
        $this->class = [];
        parent::renderView();

        // reloadId is the view id selector name that needs to be reloaded.
        // this will be pass as get argument to __atk_reload.
        if ($this->reload instanceof View) {
            $reloadId = $this->reload->name;
        } else {
            $reloadId = $this->reload;
        }

        $this->js(true, (new JsVueService())->createAtkVue(
            '#' . $this->name,
            'atk-item-search',
            [
                'reload' => $reloadId,
                'queryArg' => $this->queryArg,
                'url' => $this->reload->jsUrl(),
                'q' => $this->q,
                'context' => $this->context,
                'options' => [
                    'inputTimeOut' => $this->inputTimeOut,
                    'inputCss' => $this->inputCss,
                ],
            ]
        ));
    }
}
