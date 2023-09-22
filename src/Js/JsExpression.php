<?php

declare(strict_types=1);

namespace Atk4\Ui\Js;

use Atk4\Core\DiContainerTrait;
use Atk4\Data\Persistence;
use Atk4\Ui\Exception;
use Atk4\Ui\View;

class JsExpression implements JsExpressionable
{
    use DiContainerTrait;

    public string $template;

    /** @var array<int|string, mixed> */
    public array $args;

    /**
     * @param array<int|string, mixed> $args
     */
    public function __construct(string $template = '', array $args = [])
    {
        $this->template = $template;
        $this->args = $args;
    }

    public function jsRender(): string
    {
        $namelessCount = 0;
        $res = preg_replace_callback(
            '~\[[\w]*\]|{[\w]*}~',
            function ($matches) use (&$namelessCount): string {
                $identifier = substr($matches[0], 1, -1);

                // allow template to contain []
                if ($identifier === '') {
                    $identifier = $namelessCount++;
                }

                if (!isset($this->args[$identifier])) {
                    throw (new Exception('Tag is not defined in template'))
                        ->addMoreInfo('tag', $identifier)
                        ->addMoreInfo('template', $this->template);
                }

                $value = $this->args[$identifier];

                // no escaping for "{}"
                if ($matches[0][0] === '{' && is_string($value)) {
                    return $value;
                }

                $valueStr = $this->_jsEncode($value);
                if ($value instanceof JsExpressionable && !str_ends_with($valueStr, ';')) {
                    $valueStr = '(' . $valueStr . ')';
                }

                return $valueStr;
            },
            $this->template
        );

        return trim($res);
    }

    /**
     * @param mixed $value
     */
    protected function _jsEncode($value): string
    {
        if ($value instanceof JsExpressionable) {
            $res = $value->jsRender();
        } elseif ($value instanceof View) {
            $res = $this->_jsEncode('#' . $value->getHtmlId());
        } elseif (is_array($value)) {
            $array = [];
            $assoc = !array_is_list($value);

            foreach ($value as $k => $v) {
                $v = $this->_jsEncode($v);
                $k = $this->_jsEncode($k);
                if (!$assoc) {
                    $array[] = $v;
                } else {
                    $array[] = $k . ': ' . $v;
                }
            }

            if ($assoc) {
                $res = '{' . implode(', ', $array) . '}';
            } else {
                $res = '[' . implode(', ', $array) . ']';
            }
        } elseif (is_string($value)) {
            $res = json_encode($value, \JSON_UNESCAPED_SLASHES | \JSON_UNESCAPED_UNICODE | \JSON_THROW_ON_ERROR);
            $res = '\'' . str_replace('\'', '\\\'', str_replace('\\"', '"', substr($res, 1, -1))) . '\'';
        } elseif (is_bool($value)) {
            $res = $value ? 'true' : 'false';
        } elseif (is_int($value)) {
            // IMPORTANT: always convert large integers to string, otherwise numbers can be rounded by JS
            $res = abs($value) < (2 ** 53) ? (string) $value : $this->_jsEncode((string) $value);
        } elseif (is_float($value)) {
            $res = Persistence\Sql\Expression::castFloatToString($value);
        } elseif ($value === null) {
            $res = 'null';
        } else {
            throw (new Exception('Argument is not renderable to JS'))
                ->addMoreInfo('arg', $value);
        }

        return $res;
    }
}
