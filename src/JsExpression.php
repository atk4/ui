<?php

declare(strict_types=1);

namespace Atk4\Ui;

use Atk4\Core\DiContainerTrait;
use Atk4\Data\Persistence;

/**
 * Implements a class that can be mapped into arbitrary JavaScript expression.
 */
class JsExpression implements JsExpressionable
{
    use DiContainerTrait;

    /** @var string */
    public $template;

    /** @var array */
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
            '~\[[\w]*\]|{[\w]*}~',
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

                // no escaping for "{}"
                if ($matches[0][0] === '{') {
                    return $value;
                }

                if ($value instanceof JsExpressionable) {
                    $value = '(' . $value->jsRender() . ')';
                } else {
                    $value = $this->_jsEncode($value);
                }

                return $value;
            },
            $this->template
        );

        return trim($res);
    }

    /**
     * @param mixed $arg
     */
    protected function _jsEncode($arg): string
    {
        if (is_object($arg)) {
            if ($arg instanceof JsExpressionable) {
                $result = $arg->jsRender();

                return $result;
            }

            throw (new Exception('Not sure how to represent this object in JSON'))
                ->addMoreInfo('obj', $arg);
        } elseif (is_array($arg)) {
            $array = [];
            $assoc = !array_is_list($arg);

            foreach ($arg as $key => $value) {
                $value = $this->_jsEncode($value);
                $key = $this->_jsEncode($key);
                if (!$assoc) {
                    $array[] = $value;
                } else {
                    $array[] = $key . ': ' . $value;
                }
            }

            if ($assoc) {
                $string = '{' . implode(', ', $array) . '}';
            } else {
                $string = '[' . implode(', ', $array) . ']';
            }
        } elseif (is_string($arg)) {
            $string = json_encode($arg, \JSON_UNESCAPED_SLASHES | \JSON_UNESCAPED_UNICODE | \JSON_THROW_ON_ERROR);
            $string = '\'' . str_replace('\'', '\\\'', str_replace('\\"', '"', substr($string, 1, -1))) . '\'';
        } elseif (is_bool($arg)) {
            $string = $arg ? 'true' : 'false';
        } elseif (is_int($arg)) {
            // IMPORTANT: always convert large integers to string, otherwise numbers can be rounded by JS
            $string = abs($arg) < (2 ** 53) ? (string) $arg : $this->_jsEncode((string) $arg);
        } elseif (is_float($arg)) {
            $string = Persistence\Sql\Expression::castFloatToString($arg);
        } elseif ($arg === null) {
            $string = 'null';
        } else {
            throw (new Exception('Unsupported argument type'))
                ->addMoreInfo('arg', $arg);
        }

        return $string;
    }
}
