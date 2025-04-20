<?php

declare(strict_types=1);

namespace Atk4\Ui\Js;

use Atk4\Core\WarnDynamicPropertyTrait;

class JsCallbackLoadableValue implements JsExpressionable
{
    use WarnDynamicPropertyTrait;

    private JsExpressionable $jsValue;

    /** @var \Closure(string): mixed */
    private \Closure $loadTypecastFx;

    /**
     * @param \Closure(string): mixed $loadTypecastFx
     */
    public function __construct(?JsExpressionable $jsValue, \Closure $loadTypecastFx)
    {
        if ($jsValue !== null) {
            $this->jsValue = $jsValue;
        }
        $this->loadTypecastFx = $loadTypecastFx;
    }

    #[\Override]
    public function jsRender(): string
    {
        return $this->jsValue->jsRender();
    }

    /**
     * @return mixed
     */
    public function typecastLoadValue(string $value)
    {
        return ($this->loadTypecastFx)($value);
    }
}
