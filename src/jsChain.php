<?php

namespace atk4\ui;

/**
 * Implements a transparent mapper that will actually translate into JavaScript code. Used
 * as a GLUE between Views and your actual JavaScript code.
 *
 * IMPORTANT: extend first. see jQuery.php for example.
 *
 * IMPORTANT: don't write any JavaScript logic in PHP, only bind.
 *
 * IMPORTANT: all methods in this class are pre-pended with '_', to avoid clashes with js-mapping.
 */
class jsChain extends jsExpression
{
    /**
     * Name of the include file where this library is implemented.
     *
     * @var string
     */
    public $_include = null;

    /**
     * Default version to use.
     *
     * @var string
     */
    public $_version = null;

    /**
     * Integrity code of default version of this library.
     *
     * @var string
     */
    public $_integrity = null;

    /**
     * Set this to the object of your library. Most libraries prefer '$', although you might want to use 'jQuery' or 'new google.maps.Map';.
     *
     * @var string
     */
    public $_library = '$';

    /**
     * This will represent constructor argument. If no arguments are set, then the library will be executed like this:.
     *
     * $.hello();
     *
     * If arguments are specified they are passed to constructor initializer:
     *
     * $('foo', 'bar').hello();
     *
     * @var array
     */
    public $_constructorArgs = [];

    /**
     * Call chain. All calls to this mapper will be recorded here. Property traversal
     * is also stored here.
     *
     * $js->foo()->bar(1)->baz->test(['abc'=>123']);
     *
     * will be stored in $chain as
     *
     * [ ['foo'], ['bar', [1]], 'baz', ['test', ['abc=>123]] ]
     *
     * will map into:
     *
     * $.foo().bar(1).baz.test({abc: 123]);
     *
     * @var array
     */
    public $_chain = [];

    /**
     * Override a library when executing constructor. For instance if you wish to use jQuery3 instead of jQuery.
     *
     * @param string $library
     */
    public function __construct($library = null)
    {
        if ($library) {
            $this->_library = $library;
        }
    }

    /**
     * Records all calls to this chain returning itself every time.
     *
     * @param string $name
     * @param mixed  $args
     *
     * @return $this
     */
    public function __call($name, $args)
    {
        $this->_chain[] = [$name, $args];

        return $this;
    }

    /**
     * Allows you to use syntax like this.
     *
     * $js->offset()->top
     *
     * that maps into
     *
     * $.offset()->top
     *
     * @param string $property
     *
     * @return $this
     */
    public function __get($property)
    {
        $this->_chain[] = $property;

        return $this;
    }

    /**
     * Convert reserved words or used methods into js calls, such as "_fn('class')" or "_fn('_fn')".
     *
     * @param string $name
     * @param array  $args
     *
     * @return $this
     */
    public function _fn($name, $args = [])
    {
        // Wrapper for functions which use reserved words
        return $this->__call($name, $args);
    }

    /**
     * Renders JS chain arguments.
     *
     * @param array $args
     *
     * @return string
     */
    private function _renderArgs($args = [])
    {
        return '('.
            implode(',', array_map(function ($arg) {
                if ($arg instanceof jsExpressionable) {
                    return $arg->jsRender();
                }

                return $this->_json_encode($arg);
            }, $args)).
            ')';
    }

    /**
     * Produce String representing this JavaScript extension.
     *
     * @return string
     */
    public function jsRender()
    {
        $ret = '';

        // start with constructor
        $ret .= $this->_library;

        // next perhaps we have arguments
        if ($this->_constructorArgs) {
            $ret .= $this->_renderArgs($this->_constructorArgs);
        }

        // next we do same with the calls
        foreach ($this->_chain as $chain) {
            if (is_array($chain)) {
                $ret .= '.'.$chain[0].$this->_renderArgs($chain[1]);
            } elseif (is_numeric($chain)) {
                $ret .= '['.$chain.']';
            } else {
                $ret .= '.'.$chain;
            }
        }

        return $ret;
    }
}
