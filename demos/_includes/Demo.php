<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\Columns;
use Atk4\Ui\Exception;
use Atk4\Ui\JsChain;
use Atk4\Ui\View;

class Demo extends Columns
{
    /** @var View */
    public $left;
    /** @var View */
    public $right;

    /** @var bool */
    public static $isInitialized = false;

    /** @var string */
    public $highlightDefaultStyle = 'dark';

    /** @var int */
    public $leftWidth = 8;
    /** @var int */
    public $rightWidth = 8;

    protected function init(): void
    {
        parent::init();

        $this->addClass('celled');

        $this->left = $this->addColumn($this->leftWidth);
        $this->right = $this->addColumn($this->rightWidth);
    }

    protected function extractCodeFromClosure(\Closure $fx): string
    {
        $funcRefl = new \ReflectionFunction($fx);
        if ($funcRefl->getEndLine() === $funcRefl->getStartLine()) {
            throw new Exception('Closure body to extract must be on separate lines');
        }

        $codeArr = array_slice(
            explode("\n", file_get_contents($funcRefl->getFileName())),
            $funcRefl->getStartLine(),
            $funcRefl->getEndLine() - $funcRefl->getStartLine() - 1
        );

        $minIndent = min(array_map(function (string $l): int {
            return strlen($l) - strlen(ltrim($l, ' '));
        }, array_filter($codeArr)));

        return implode("\n", array_map(function (string $l) use ($minIndent) {
            return substr($l, $minIndent);
        }, $codeArr));
    }

    public function setCodeAndCall(\Closure $fx, string $lang = 'php'): void
    {
        $code = $this->extractCodeFromClosure($fx);

        $this->highLightCode();
        View::addTo(View::addTo($this->left, ['element' => 'pre']), ['element' => 'code'])->addClass($lang)->set($code);

        $fx($this->right);
    }

    public function highLightCode(): void
    {
        if (!self::$isInitialized) {
            $this->getApp()->requireCss('https://cdnjs.cloudflare.com/ajax/libs/highlight.js/9.16.2/styles/' . $this->highlightDefaultStyle . '.min.css');
            $this->getApp()->requireJs('https://cdnjs.cloudflare.com/ajax/libs/highlight.js/9.16.2/highlight.min.js');
            $this->js(true, (new JsChain('hljs'))->initHighlighting());
            self::$isInitialized = true;
        }
    }
}
