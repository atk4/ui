<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

class Demo extends \Atk4\Ui\Columns
{
    public $left;
    public $right;
    public static $isInitialized = false;
    public $highlightDefaultStyle = 'dark';
    public $left_width = 8;
    public $right_width = 8;

    protected function init(): void
    {
        parent::init();
        $this->addClass('celled');

        $this->left = $this->addColumn($this->left_width);
        $this->right = $this->addColumn($this->right_width);
    }

    protected function extractCodeFromClosure(\Closure $fx): string
    {
        $funcRefl = new \ReflectionFunction($fx);
        if ($funcRefl->getEndLine() === $funcRefl->getStartLine()) {
            throw new \Atk4\Ui\Exception('Closure body to extract must be on separate lines');
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

    public function setCodeAndCall(\Closure $fx, $lang = 'php')
    {
        $code = $this->extractCodeFromClosure($fx);

        $this->highLightCode();
        \Atk4\Ui\View::addTo(\Atk4\Ui\View::addTo($this->left, ['element' => 'pre']), ['element' => 'code'])->addClass($lang)->set($code);

        $fx($this->right);
    }

    public function highLightCode()
    {
        if (!self::$isInitialized) {
            $this->getApp()->requireCss('https://cdnjs.cloudflare.com/ajax/libs/highlight.js/9.16.2/styles/' . $this->highlightDefaultStyle . '.min.css');
            $this->getApp()->requireJs('https://cdnjs.cloudflare.com/ajax/libs/highlight.js/9.16.2/highlight.min.js');
            $this->js(true, (new \Atk4\Ui\JsChain('hljs'))->initHighlighting());
            self::$isInitialized = true;
        }
    }
}
