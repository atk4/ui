<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\Code;
use Atk4\Ui\Columns;
use Atk4\Ui\Exception;
use Atk4\Ui\Js\JsExpression;
use Atk4\Ui\Js\JsFunction;
use Atk4\Ui\View;

class Demo extends Columns
{
    /** @var View */
    public $left;
    /** @var View */
    public $right;

    /** @var int */
    public $leftWidth = 8;
    /** @var int */
    public $rightWidth = 8;

    #[\Override]
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

        $minIndent = min(array_map(static function (string $l): int {
            return strlen($l) - strlen(ltrim($l, ' '));
        }, array_filter($codeArr, static fn ($v) => $v !== '')));

        return implode("\n", array_map(static function (string $l) use ($minIndent) {
            return substr($l, $minIndent);
        }, $codeArr));
    }

    /**
     * @param \Closure(View): void $fx
     */
    public function setCodeAndCall(\Closure $fx, string $lang = 'php'): void
    {
        $code = $this->extractCodeFromClosure($fx);

        (new Code())->setApp($this->getApp())->invokeInit();
        View::addTo(View::addTo($this->left, ['element' => 'pre']), ['element' => 'code'])
            ->addClass('language-' . $lang)
            ->set($code)
            ->js(true)->each(new JsFunction(['i, el'], [new JsExpression('hljs.highlightElement(el)')]));

        $fx($this->right);
    }
}
