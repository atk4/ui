<?php

declare(strict_types=1);

namespace Atk4\Ui;

/**
 * Implements a class that can be mapped into arbitrary JavaScript expression.
 */
class JsExpression implements JsExpressionable
{
    use \Atk4\Core\DiContainerTrait;

    /**
     * @var string
     */
    public $template;

    /**
     * @var array
     */
    public $args = [];

    public function __construct(string $template = '', array $args = [])
    {
        $this->template = $template;
        $this->args = $args;
    }

    /**
     * Converts this arbitrary JavaScript expression into string.
     */
    public function jsRender(): string
    {
        $namelessCount = 0;

        $res = preg_replace_callback(
            '/\[[a-z0-9_]*\]|{[a-z0-9_]*}/',
            function ($matches) use (&$namelessCount) {
                $identifier = substr($matches[0], 1, -1);

                // Allow template to contain []
                if ($identifier === '') {
                    $identifier = $namelessCount++;
                }

                if (!isset($this->args[$identifier])) {
                    throw (new Exception('Tag not defined in template for JsExpression'))
                        ->addMoreInfo('tag', $identifier)
                        ->addMoreInfo('template', $this->template);
                }

                $value = $this->args[$identifier];

                // No escaping for {}
                if ($matches[0][0] === '{') {
                    return $value;
                }

                if (is_object($value) && $value instanceof JsExpressionable) {
                    $value = '(' . $value->jsRender() . ')';
                } elseif (is_object($value)) {
                    $value = $this->_json_encode($value->toString());
                } else {
                    $value = $this->_json_encode($value);
                }

                return $value;
            },
            $this->template
        );

        return trim($res);
    }

    /**
     * Provides replacement for json_encode() that will respect JsExpressionable objects
     * and call jsRender() for them instead of escaping.
     */
    protected function _json_encode($arg): string
    {
        /*
         * This function is very similar to json_encode, however it will traverse array
         * before encoding in search of JsExpressionable objects. Those would
         * be replaced with their jsRendering.
         */
        if (is_object($arg)) {
            if ($arg instanceof JsExpressionable) {
                $result = $arg->jsRender();

                return $result;
            }

            throw (new Exception('Not sure how to represent this object in JSON'))
                ->addMoreInfo('obj', $arg);
        } elseif (is_array($arg)) {
            $array = [];
            // is array associative? (hash)
            $assoc = $arg !== array_values($arg);

            foreach ($arg as $key => $value) {
                $value = $this->_json_encode($value);
                $key = $this->_json_encode($key);
                if (!$assoc) {
                    $array[] = $value;
                } else {
                    $array[] = $key . ':' . $value;
                }
            }

            if ($assoc) {
                $string = '{' . implode(',', $array) . '}';
            } else {
                $string = '[' . implode(',', $array) . ']';
            }
        } elseif (is_string($arg)) {
            $string = json_encode($arg, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
        } elseif (is_bool($arg)) {
            $string = json_encode($arg);
        } elseif (is_int($arg)) {
            // IMPORTANT: always convert large integers to string, otherwise numbers can be rounded by JS
            $string = json_encode(abs($arg) < (2 ** 53) ? $arg : (string) $arg);
        } elseif (is_float($arg)) {
            $string = json_encode($arg);
        } elseif ($arg === null) {
            $string = json_encode($arg);
        } else {
            throw (new Exception('Unable to json_encode value - unknown type'))
                ->addMoreInfo('arg', var_export($arg, true));
        }

        return $string;
    }
}
