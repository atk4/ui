<?php

namespace atk4\ui;

/**
 * Implements a class that can be mapped into arbitrary JavaScript expression.
 */
class jsExpression implements jsExpressionable
{
    use \atk4\core\DIContainerTrait;

    /**
     * @var string
     */
    public $template = null;

    /**
     * @var array
     */
    public $args = [];

    /**
     * Constructor.
     *
     * @param string $template
     * @param array  $args
     */
    public function __construct($template = '', $args = [])
    {
        $this->template = $template;
        $this->args = $args;
    }

    /**
     * Converts this arbitrary JavaScript expression into string.
     *
     * @return string
     */
    public function jsRender()
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
                    throw new Exception([
                        'Tag not defined in template for jsExpression',
                        'tag'      => $identifier,
                        'template' => $this->template,
                    ]);
                }

                $value = $this->args[$identifier];

                // No escaping for {}
                if ($matches[0][0] === '{') {
                    return $value;
                }

                if (is_object($value) && $value instanceof jsExpressionable) {
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
     * Provides replacement for json_encode that will respect jsExpressionable objects
     * and call jsRender() for them instead of escaping.
     *
     * @param mixed $arg anything
     *
     * @return string valid JSON expression
     */
    protected function _json_encode($arg)
    {
        /*
         * This function is very similar to json_encode, however it will traverse array
         * before encoding in search of jsExpressionable objects. Those would
         * be replaced with their jsRendering.
         */
        if (is_object($arg)) {
            if ($arg instanceof jsExpressionable) {
                $result = $arg->jsRender();

                return $result;
            } else {
                throw new Exception(['Not sure how to represent this object in JSON', 'obj' => $arg]);
            }
        } elseif (is_array($arg)) {
            $array = [];
            // is array associative? (hash)
            $assoc = $arg != array_values($arg);

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
            $string = '"' . $this->_safe_js_string($arg) . '"';
        } elseif (is_bool($arg)) {
            $string = json_encode($arg);
        } elseif (is_numeric($arg)) {
            $string = json_encode($arg);
        } elseif (is_null($arg)) {
            $string = json_encode($arg);
        } else {
            throw new Exception(['Unable to json_encode value - unknown type', 'arg' => var_export($arg, true)]);
        }

        return $string;
    }

    /**
     * TODO: Escapes the string, but needs a reference to where this code has been from.
     *
     * @internal
     *
     * @param string $str
     */
    public function _safe_js_string($str)
    {
        $length = strlen($str);
        $ret = '';
        for ($i = 0; $i < $length; $i++) {
            switch ($str[$i]) {
                case "\r":
                    $ret .= '\\r';
                    break;
                case "\n":
                    $ret .= '\\n';
                    break;
                case '"':
                case "'":
                case '<':
                case '>':
                case '&':
                case '\\':
                    $ret .= '\x' . dechex(ord($str[$i]));
                    break;
                default:
                    $ret .= $str[$i];
                    break;
            }
        }

        return $ret;
    }
}
