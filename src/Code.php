<?php

declare(strict_types=1);

namespace Atk4\Ui;

use Atk4\Ui\Js\JsExpression;
use Atk4\Ui\Js\JsFunction;

class Code extends View
{
    public static bool $isInitialized = false;

    public $defaultTemplate = 'code.html';
    public string $element = 'pre';

    /** @var lowercase-string One of the listed languages from https://github.com/highlightjs/highlight.js/blob/11.8.0/SUPPORTED_LANGUAGES.md. */
    public string $language;

    #[\Override]
    protected function init(): void
    {
        parent::init();

        if (!self::$isInitialized) {
            $this->getApp()->requireCss($this->getApp()->cdn['highlight.js'] . '/styles/github-dark-dimmed.min.css');
            $this->getApp()->requireJs($this->getApp()->cdn['highlight.js'] . '/highlight.min.js');
            self::$isInitialized = true;
        }
    }

    #[\Override]
    protected function renderView(): void
    {
        $this->template->set('language', $this->language);

        $this->js(true, new JsExpression('hljs.highlightElement(document.querySelector([]))', ['#' . $this->getHtmlId() . ' > code']));

        parent::renderView();
    }
}
