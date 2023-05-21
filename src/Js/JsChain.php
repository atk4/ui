<?php

declare(strict_types=1);

namespace Atk4\Ui\Js;

/**
 * Transparent mapper that will actually translate into JavaScript code. Used
 * as a glue between Views and your actual JavaScript code.
 *
 * IMPORTANT: all methods in this class are prepended with '_', to avoid clashes with JS mapping
 * IMPORTANT: extend first, see Jquery class for example
 */
class JsChain extends JsExpression
{
    public string $_library;

    /**
     * This will represent constructor argument. If no arguments are set, then the library will be executed like this:.
     * $.hello().
     *
     * If arguments are specified they are passed to constructor initializer:
     * $('foo', 'bar').hello().
     *
     * @var list<mixed>
     */
    public $_constructorArgs = [];

    /**
     * Call chain. All calls to this mapper will be recorded here. Property traversal
     * is also stored here.
     *
     * $js->foo()->bar(1)->baz->test(['abc' => 123']);
     * will be stored in $chain as
     * [['foo'], ['bar', [1]], 'baz', ['test', ['abc => 123]]]
     * will map into:
     * $.foo().bar(1).baz.test({ abc: 123 ]);
     *
     * @var list<string|int|array{string, list<mixed>}>
     */
    public $_chain = [];

    public function __construct(string $library)
    {
        parent::__construct();

        $this->_library = $library;
    }

    /**
     * Records all calls to this chain returning itself every time.
     *
     * @param list<mixed> $args
     *
     * @return $this
     */
    public function __call(string $name, $args)
    {
        $this->_chain[] = [$name, $args];

        return $this;
    }

    /**
     * Allows you to use syntax like this.
     *
     * $js->offset()->top
     * that maps into
     * $.offset()->top
     *
     * @return $this
     */
    public function &__get(string $name)
    {
        $this->_chain[] = $name;

        return $this;
    }

    /**
     * Renders JS chain arguments.
     *
     * @param list<mixed> $args
     */
    private function _renderArgs(array $args = []): string
    {
        return '('
            . implode(', ', array_map(function ($arg) {
                return $this->_jsEncode($arg);
            }, $args))
            . ')';
    }

    public function jsRender(): string
    {
        $res = $this->_library;

        if ($this->_constructorArgs) {
            $res .= $this->_renderArgs($this->_constructorArgs);
        }

        foreach ($this->_chain as $chain) {
            $args = null;
            if (is_int($chain)) {
                $name = (string) $chain;
            } elseif (is_string($chain)) {
                $name = $chain;
            } else {
                $name = $chain[0];
                $args = $chain[1];
            }

            $res .= preg_match('~^(?!\d)\w+$~Du', $name) ? '.' . $name : '[' . $this->_jsEncode($name) . ']';
            if ($args !== null) {
                $res .= $this->_renderArgs($args);
            }
        }

        return $res;
    }
}
