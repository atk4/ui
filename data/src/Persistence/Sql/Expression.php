<?php

declare(strict_types=1);

namespace Atk4\Data\Persistence\Sql;

use Atk4\Core\WarnDynamicPropertyTrait;
use Doctrine\DBAL\Connection as DbalConnection;
use Doctrine\DBAL\Exception as DbalException;
use Doctrine\DBAL\Platforms\PostgreSQL94Platform;
use Doctrine\DBAL\Result as DbalResult;

/**
 * @phpstan-implements \ArrayAccess<int|string, mixed>
 */
class Expression implements Expressionable, \ArrayAccess
{
    use WarnDynamicPropertyTrait;

    /** @const string "[]" in template, escape as parameter */
    protected const ESCAPE_PARAM = 'param';
    /** @const string "{}" in template, escape as identifier */
    protected const ESCAPE_IDENTIFIER = 'identifier';
    /** @const string "{{}}" in template, escape as identifier, but keep input with special characters like "." or "(" unescaped */
    protected const ESCAPE_IDENTIFIER_SOFT = 'identifier-soft';
    /** @const string keep input as is */
    protected const ESCAPE_NONE = 'none';

    /** @var string */
    protected $template;

    /**
     * Configuration accumulated by calling methods such as Query::field(), Query::table(), etc.
     *
     * $args['custom'] is used to store hash of custom template replacements.
     *
     * This property is made public to ease customization and make it accessible
     * from Connection class for example.
     *
     * @var array<int|string, mixed>
     */
    public $args = ['custom' => []];

    /**
     * As per PDO, escapeParam() will convert value into :a, :b, :c .. :aa .. etc.
     *
     * @var string
     */
    protected $paramBase = 'a';

    /**
     * Identifier (table, column, ...) escaping symbol. By SQL Standard it's double
     * quote, but MySQL uses backtick.
     *
     * @var string
     */
    protected $escape_char = '"';

    /** @var string|null used for linking */
    private $_paramBase;

    /** @var array Populated with actual values by escapeParam() */
    public $params = [];

    /** @var Connection|null */
    public $connection;

    /** @var bool Wrap the expression in parentheses when consumed by another expression or not. */
    public $wrapInParentheses = false;

    /**
     * Specifying options to constructors will override default
     * attribute values of this class.
     *
     * If $properties is passed as string, then it's treated as template.
     *
     * @param string|array $properties
     * @param array        $arguments
     */
    public function __construct($properties = [], $arguments = null)
    {
        // save template
        if (is_string($properties)) {
            $properties = ['template' => $properties];
        } elseif (!is_array($properties)) {
            throw (new Exception('Incorrect use of Expression constructor'))
                ->addMoreInfo('properties', $properties)
                ->addMoreInfo('arguments', $arguments);
        }

        // supports passing template as property value without key 'template'
        if (isset($properties[0])) {
            $properties['template'] = $properties[0];
            unset($properties[0]);
        }

        // save arguments
        if ($arguments !== null) {
            if (!is_array($arguments)) {
                throw (new Exception('Expression arguments must be an array'))
                    ->addMoreInfo('properties', $properties)
                    ->addMoreInfo('arguments', $arguments);
            }
            $this->args['custom'] = $arguments;
        }

        // deal with remaining properties
        foreach ($properties as $key => $val) {
            $this->{$key} = $val;
        }
    }

    /**
     * @deprecated will be removed in v2.5
     */
    public function __toString()
    {
        'trigger_error'('Method is deprecated. Use $this->getOne() instead', \E_USER_DEPRECATED);

        return $this->getOne();
    }

    /**
     * @return $this
     */
    public function getDsqlExpression(self $expression): self
    {
        return $this;
    }

    /**
     * Whether or not an offset exists.
     *
     * @param int|string $offset
     */
    public function offsetExists($offset): bool
    {
        return array_key_exists($offset, $this->args['custom']);
    }

    /**
     * Returns the value at specified offset.
     *
     * @param int|string $offset
     *
     * @return mixed
     */
    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return $this->args['custom'][$offset];
    }

    /**
     * Assigns a value to the specified offset.
     *
     * @param int|string|null $offset
     * @param mixed           $value  The value to set
     */
    public function offsetSet($offset, $value): void
    {
        if ($offset === null) {
            $this->args['custom'][] = $value;
        } else {
            $this->args['custom'][$offset] = $value;
        }
    }

    /**
     * Unsets an offset.
     *
     * @param int|string $offset
     */
    public function offsetUnset($offset): void
    {
        unset($this->args['custom'][$offset]);
    }

    /**
     * Use this instead of "new Expression()" if you want to automatically bind
     * new expression to the same connection as the parent.
     *
     * @param string|array $properties
     * @param array        $arguments
     *
     * @return Expression
     */
    public function expr($properties = [], $arguments = null)
    {
        if ($this->connection !== null) {
            // TODO - condition above always satisfied when connection is set - adjust tests,
            // so connection is always set and remove the code below
            return $this->connection->expr($properties, $arguments);
        }

        // make a smart guess :) when connection is not set
        if ($this instanceof Query) {
            $e = new self($properties, $arguments);
        } else {
            $e = new static($properties, $arguments);
        }

        $e->escape_char = $this->escape_char;

        return $e;
    }

    /**
     * Resets arguments.
     *
     * @return $this
     */
    public function reset(string $tag = null)
    {
        // unset all arguments
        if ($tag === null) {
            $this->args = ['custom' => []];

            return $this;
        }

        // unset custom/argument or argument if such exists
        if ($this->offsetExists($tag)) {
            $this->offsetUnset($tag);
        } elseif (isset($this->args[$tag])) {
            unset($this->args[$tag]);
        }

        return $this;
    }

    /**
     * Recursively renders sub-query or expression, combining parameters.
     *
     * @param mixed  $expression Expression
     * @param string $escapeMode Fall-back escaping mode - using one of the Expression::ESCAPE_* constants
     *
     * @return string Quoted expression
     */
    protected function consume($expression, string $escapeMode = self::ESCAPE_PARAM)
    {
        if (!is_object($expression)) {
            switch ($escapeMode) {
                case self::ESCAPE_PARAM:
                    return $this->escapeParam($expression);
                case self::ESCAPE_IDENTIFIER:
                    return $this->escapeIdentifier($expression);
                case self::ESCAPE_IDENTIFIER_SOFT:
                    return $this->escapeIdentifierSoft($expression);
                case self::ESCAPE_NONE:
                    return $expression;
            }

            throw (new Exception('$escapeMode value is incorrect'))
                ->addMoreInfo('escapeMode', $escapeMode);
        }

        if ($expression instanceof Expressionable) {
            $expression = $expression->getDsqlExpression($this);
        }

        if (!$expression instanceof self) {
            throw (new Exception('Only Expressionable object type may be used in Expression'))
                ->addMoreInfo('object', $expression);
        }

        // at this point $sql_code is instance of Expression
        $expression->params = $this->params;
        $expression->_paramBase = $this->_paramBase;
        try {
            $ret = $expression->render();
            $this->params = $expression->params;
            $this->_paramBase = $expression->_paramBase;
        } finally {
            $expression->params = [];
            $expression->_paramBase = null;
        }

        // Wrap in parentheses if expression requires so
        if ($expression->wrapInParentheses === true) {
            $ret = '(' . $ret . ')';
        }

        return $ret;
    }

    /**
     * Creates new expression where $value appears escaped. Use this
     * method as a conventional means of specifying arguments when you
     * think they might have a nasty back-ticks or commas in the field
     * names.
     *
     * @param string $value
     *
     * @return Expression
     */
    public function escape($value)
    {
        return $this->expr('{}', [$value]);
    }

    /**
     * Converts value into parameter and returns reference. Use only during
     * query rendering. Consider using `consume()` instead, which will
     * also handle nested expressions properly.
     *
     * @param string|int|float $value
     *
     * @return string Name of parameter
     */
    protected function escapeParam($value): string
    {
        $name = ':' . $this->_paramBase;
        ++$this->_paramBase;
        $this->params[$name] = $value;

        return $name;
    }

    /**
     * Escapes argument by adding backticks around it.
     * This will allow you to use reserved SQL words as table or field
     * names such as "table" as well as other characters that SQL
     * permits in the identifiers (e.g. spaces or equation signs).
     */
    protected function escapeIdentifier(string $value): string
    {
        // in all other cases we should escape
        $c = $this->escape_char;

        return $c . str_replace($c, $c . $c, $value) . $c;
    }

    /**
     * Soft-escaping SQL identifier. This method will attempt to put
     * escaping char around the identifier, however will not do so if you
     * are using special characters like ".", "(" or escaping char.
     *
     * It will smartly escape table.field type of strings resulting
     * in "table"."field".
     */
    protected function escapeIdentifierSoft(string $value): string
    {
        // in some cases we should not escape
        if ($this->isUnescapablePattern($value)) {
            return $value;
        }

        if (strpos($value, '.') !== false) {
            return implode('.', array_map(__METHOD__, explode('.', $value)));
        }

        return $this->escape_char . trim($value) . $this->escape_char;
    }

    /**
     * Given the string parameter, it will detect some "deal-breaker" for our
     * soft escaping, such as "*" or "(".
     * Those will typically indicate that expression is passed and shouldn't
     * be escaped.
     *
     * @param self|string $value
     */
    protected function isUnescapablePattern($value): bool
    {
        return is_object($value)
        || $value === '*'
                || strpos($value, '(') !== false
                || strpos($value, $this->escape_char) !== false;
    }

    /**
     * Render expression and return it as string.
     */
    public function render(): string
    {
        $hadUnderscoreParamBase = $this->_paramBase !== null;
        if (!$hadUnderscoreParamBase) {
            $hadUnderscoreParamBase = false;
            $this->_paramBase = $this->paramBase;
        }

        if ($this->template === null) {
            throw new Exception('Template is not defined for Expression');
        }

        $nameless_count = 0;

        // - [xxx] = param
        // - {xxx} = escape
        // - {{xxx}} = escapeSoft
        $res = preg_replace_callback(
            <<<'EOF'
                ~
                 '(?:[^'\\]+|\\.|'')*'\K
                |"(?:[^"\\]+|\\.|"")*"\K
                |`(?:[^`\\]+|\\.|``)*`\K
                |\[\w*\]
                |\{\w*\}
                |\{\{\w*\}\}
                ~xs
                EOF,
            function ($matches) use (&$nameless_count) {
                if ($matches[0] === '') {
                    return '';
                }

                $identifier = substr($matches[0], 1, -1);

                $escaping = null;
                if (substr($matches[0], 0, 1) === '[') {
                    $escaping = self::ESCAPE_PARAM;
                } elseif (substr($matches[0], 0, 1) === '{') {
                    if (substr($matches[0], 1, 1) === '{') {
                        $escaping = self::ESCAPE_IDENTIFIER_SOFT;
                        $identifier = substr($identifier, 1, -1);
                    } else {
                        $escaping = self::ESCAPE_IDENTIFIER;
                    }
                }

                // allow template to contain []
                if ($identifier === '') {
                    $identifier = $nameless_count++;

                    // use rendering only with named tags
                }
                $fx = '_render_' . $identifier;

                if (array_key_exists($identifier, $this->args['custom'])) {
                    $value = $this->consume($this->args['custom'][$identifier], $escaping);
                } elseif (method_exists($this, $fx)) {
                    $value = $this->{$fx}();
                } else {
                    throw (new Exception('Expression could not render tag'))
                        ->addMoreInfo('tag', $identifier);
                }

                return $value;
            },
            $this->template
        );

        if (!$hadUnderscoreParamBase) {
            $this->_paramBase = null;
        }

        return trim($res);
    }

    /**
     * Return formatted debug SQL query.
     */
    public function getDebugQuery(): string
    {
        $result = $this->render();

        foreach (array_reverse($this->params) as $key => $val) {
            if (is_int($key)) {
                continue;
            }

            if ($val === null) {
                $replacement = 'NULL';
            } elseif (is_bool($val)) {
                $replacement = $val ? '1' : '0';
            } elseif (is_int($val) || is_float($val)) {
                $replacement = (string) $val;
            } elseif (is_string($val)) {
                $replacement = '\'' . addslashes($val) . '\'';
            } else {
                continue;
            }

            $result = preg_replace('~' . $key . '(?!\w)~', $replacement, $result);
        }

        if (class_exists('SqlFormatter')) { // requires optional "jdorn/sql-formatter" package
            $result = \SqlFormatter::format($result, false);
        }

        return $result;
    }

    public function __debugInfo(): array
    {
        $arr = [
            'R' => false,
            'template' => $this->template,
            'params' => $this->params,
            // 'connection' => $this->connection,
            'args' => $this->args,
        ];

        try {
            $arr['R'] = $this->getDebugQuery();
        } catch (\Exception $e) {
            $arr['R'] = $e->getMessage();
        }

        return $arr;
    }

    /**
     * @param DbalConnection|Connection $connection
     *
     * @return DbalResult|\PDOStatement PDOStatement iff for DBAL 2.x
     */
    public function execute(object $connection = null): object
    {
        if ($connection === null) {
            $connection = $this->connection;
        }

        // If it's a DBAL connection, we're cool
        if ($connection instanceof DbalConnection) {
            $query = $this->render();

            try {
                $statement = $connection->prepare($query);

                foreach ($this->params as $key => $val) {
                    if (is_int($val)) {
                        $type = \PDO::PARAM_INT;
                    } elseif (is_bool($val)) {
                        if ($this->connection->getDatabasePlatform() instanceof PostgreSQL94Platform) {
                            $type = \PDO::PARAM_STR;
                            $val = $val ? '1' : '0';
                        } else {
                            $type = \PDO::PARAM_INT;
                            $val = $val ? 1 : 0;
                        }
                    } elseif ($val === null) {
                        $type = \PDO::PARAM_NULL;
                    } elseif (is_string($val) || is_float($val)) {
                        $type = \PDO::PARAM_STR;
                    } elseif (is_resource($val)) {
                        throw new Exception('Resource type is not supported, set value as string instead');
                    } else {
                        throw (new Exception('Incorrect param type'))
                            ->addMoreInfo('key', $key)
                            ->addMoreInfo('value', $val)
                            ->addMoreInfo('type', gettype($val));
                    }

                    $bind = $statement->bindValue($key, $val, $type);
                    if ($bind === false) {
                        throw (new Exception('Unable to bind parameter'))
                            ->addMoreInfo('param', $key)
                            ->addMoreInfo('value', $val)
                            ->addMoreInfo('type', $type);
                    }
                }

                $result = $statement->execute();
                if (Connection::isComposerDbal2x()) {
                    return $statement; // @phpstan-ignore-line
                }

                return $result;
            } catch (DbalException|\Doctrine\DBAL\DBALException $e) {
                $firstException = $e;
                while ($firstException->getPrevious() !== null) {
                    $firstException = $firstException->getPrevious();
                }
                $errorInfo = $firstException instanceof \PDOException ? $firstException->errorInfo : null;

                $new = (new ExecuteException('Dsql execute error', $errorInfo[1] ?? 0, $e))
                    ->addMoreInfo('error', $errorInfo[2] ?? 'n/a (' . $errorInfo[0] . ')')
                    ->addMoreInfo('query', $this->getDebugQuery());

                throw $new;
            }
        }

        return $connection->execute($this);
    }

    /**
     * TODO drop method once we support DBAL 3.x only.
     *
     * @return \Traversable<array<mixed>>
     */
    public function getIterator(): \Traversable
    {
        if (Connection::isComposerDbal2x()) {
            return $this->execute();
        }

        return $this->execute()->iterateAssociative();
    }

    // {{{ Result Querying

    /**
     * @param string|int|float|bool|null $v
     */
    private function getCastValue($v): ?string
    {
        if (is_int($v) || is_float($v)) {
            return (string) $v;
        } elseif (is_bool($v)) {
            return $v ? '1' : '0';
        }

        // for Oracle CLOB/BLOB datatypes and PDO driver
        if (is_resource($v) && get_resource_type($v) === 'stream'
                && $this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\OraclePlatform) {
            $v = stream_get_contents($v);
        }

        return $v; // throw a type error if not null nor string
    }

    /**
     * @deprecated use "getRows" method instead - will be removed in v2.5
     */
    public function get(): array
    {
        'trigger_error'('Method is deprecated. Use getRows instead', \E_USER_DEPRECATED);

        return $this->getRows();
    }

    /**
     * Executes expression and return whole result-set in form of array of hashes.
     *
     * @return string[][]|null[][]
     */
    public function getRows(): array
    {
        if (Connection::isComposerDbal2x()) {
            $rows = $this->execute()->fetchAll();
        } else {
            $rows = $this->execute()->fetchAllAssociative();
        }

        return array_map(function ($row) {
            return array_map(function ($v) {
                return $this->getCastValue($v);
            }, $row);
        }, $rows);
    }

    /**
     * Executes expression and returns first row of data from result-set as a hash.
     *
     * @return string[]|null[]|null
     */
    public function getRow(): ?array
    {
        if (Connection::isComposerDbal2x()) {
            $row = $this->execute()->fetch();
        } else {
            $row = $this->execute()->fetchAssociative();
        }

        if ($row === false) {
            return null;
        }

        return array_map(function ($v) {
            return $this->getCastValue($v);
        }, $row);
    }

    /**
     * Executes expression and return first value of first row of data from result-set.
     */
    public function getOne(): ?string
    {
        $row = $this->getRow();
        if ($row === null || count($row) === 0) {
            throw (new Exception('Unable to fetch single cell of data for getOne from this query'))
                ->addMoreInfo('result', $row)
                ->addMoreInfo('query', $this->getDebugQuery());
        }

        return reset($row);
    }

    // }}}
}
