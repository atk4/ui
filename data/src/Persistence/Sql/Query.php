<?php

declare(strict_types=1);

namespace Atk4\Data\Persistence\Sql;

use Doctrine\DBAL\Result as DbalResult;

/**
 * Perform query operation on SQL server (such as select, insert, delete, etc).
 */
class Query extends Expression
{
    /**
     * Query will use one of the predefined templates. The $mode will contain
     * name of template used. Basically it's part of Query property name -
     * Query::template_[$mode].
     *
     * @var string
     */
    public $mode = 'select';

    /**
     * If no fields are defined, this field is used.
     *
     * @var string|Expression
     */
    public $defaultField = '*';

    /** @var string Expression classname */
    protected $expression_class = Expression::class;

    /** @var bool */
    public $wrapInParentheses = true;

    /** @var string */
    protected $template_select = '[with]select[option] [field] [from] [table][join][where][group][having][order][limit]';

    /** @var string */
    protected $template_insert = 'insert[option] into [table_noalias] ([set_fields]) values ([set_values])';

    /** @var string */
    protected $template_replace = 'replace[option] into [table_noalias] ([set_fields]) values ([set_values])';

    /** @var string */
    protected $template_delete = '[with]delete [from] [table_noalias][where][having]';

    /** @var string */
    protected $template_update = '[with]update [table_noalias] set [set] [where]';

    /** @var string */
    protected $template_truncate = 'truncate table [table_noalias]';

    /**
     * Name or alias of base table to use when using default join().
     *
     * This is set by table(). If you are using multiple tables,
     * then $main_table is set to false as it is irrelevant.
     *
     * @var false|string|null
     */
    protected $main_table;

    // {{{ Field specification and rendering

    /**
     * Adds new column to resulting select by querying $field.
     *
     * Examples:
     *  $q->field('name');
     *
     * You can use a dot to prepend table name to the field:
     *  $q->field('user.name');
     *  $q->field('user.name')->field('address.line1');
     *
     * Array as a first argument will specify multiple fields, same as calling field() multiple times
     *  $q->field(['name', 'surname', 'address.line1']);
     *
     * You can pass first argument as Expression or Query
     *  $q->field( $q->expr('2+2'), 'alias');   // must always use alias
     *
     * You can use $q->dsql() for subqueries. Subqueries will be wrapped in
     * brackets.
     *  $q->field( $q->dsql()->table('x')... , 'alias');
     *
     * Associative array will assume that "key" holds the field alias.
     * Value may be field name, Expression or Query.
     *  $q->field(['alias' => 'name', 'alias2' => 'mother.surname']);
     *  $q->field(['alias' => $q->expr(..), 'alias2' => $q->dsql()->.. ]);
     *
     * If you need to use funky name for the field (e.g, one containing
     * a dot or a space), you should wrap it into expression:
     *  $q->field($q->expr('{}', ['fun...ky.field']), 'f');
     *
     * @param mixed  $field Specifies field to select
     * @param string $alias Specify alias for this field
     *
     * @return $this
     */
    public function field($field, $alias = null)
    {
        // field is passed as string, may contain commas
        if (is_string($field) && strpos($field, ',') !== false) {
            $field = explode(',', $field);
        }

        // recursively add array fields
        if (is_array($field)) {
            if ($alias !== null) {
                throw (new Exception('Alias must not be specified when $field is an array'))
                    ->addMoreInfo('alias', $alias);
            }

            foreach ($field as $alias => $f) {
                $this->field($f, is_numeric($alias) ? null : $alias);
            }

            return $this;
        }

        // save field in args
        $this->_set_args('field', $alias, $field);

        return $this;
    }

    /**
     * Returns template component for [field].
     *
     * @param bool $add_alias Should we add aliases, see _render_field_noalias()
     *
     * @return string Parsed template chunk
     */
    protected function _render_field($add_alias = true): string
    {
        // will be joined for output
        $ret = [];

        // If no fields were defined, use defaultField
        if (empty($this->args['field'])) {
            if ($this->defaultField instanceof Expression) {
                return $this->consume($this->defaultField);
            }

            return (string) $this->defaultField;
        }

        // process each defined field
        foreach ($this->args['field'] as $alias => $field) {
            // Do not add alias, if:
            //  - we don't want aliases OR
            //  - alias is the same as field OR
            //  - alias is numeric
            if (
                $add_alias === false
                || (is_string($field) && $alias === $field)
                || is_numeric($alias)
            ) {
                $alias = null;
            }

            // Will parameterize the value and escape if necessary
            $field = $this->consume($field, self::ESCAPE_IDENTIFIER_SOFT);

            if ($alias) {
                // field alias cannot be expression, so simply escape it
                $field .= ' ' . $this->escapeIdentifier($alias);
            }

            $ret[] = $field;
        }

        return implode(', ', $ret);
    }

    protected function _render_field_noalias(): string
    {
        return $this->_render_field(false);
    }

    // }}}

    // {{{ Table specification and rendering

    /**
     * Specify a table to be used in a query.
     *
     * @param mixed  $table Specifies table
     * @param string $alias Specify alias for this table
     *
     * @return $this
     */
    public function table($table, $alias = null)
    {
        // comma-separated table names
        if (is_string($table) && strpos($table, ',') !== false) {
            $table = explode(',', $table);
        }

        // array of tables - recursively process each
        if (is_array($table)) {
            if ($alias !== null) {
                throw (new Exception('You cannot use single alias with multiple tables'))
                    ->addMoreInfo('alias', $alias);
            }

            foreach ($table as $alias => $t) {
                if (is_numeric($alias)) {
                    $alias = null;
                }
                $this->table($t, $alias);
            }

            return $this;
        }

        // if table is set as sub-Query, then alias is mandatory
        if ($table instanceof self && $alias === null) {
            throw new Exception('If table is set as Query, then table alias is mandatory');
        }

        if (is_string($table) && $alias === null) {
            $alias = $table;
        }

        // main_table will be set only if table() is called once.
        // it's used as "default table" when joining with other tables, see join().
        // on multiple calls, main_table will be false and we won't
        // be able to join easily anymore.
        $this->main_table = ($this->main_table === null && $alias !== null ? $alias : false);

        // save table in args
        $this->_set_args('table', $alias, $table);

        return $this;
    }

    /**
     * @param bool $add_alias Should we add aliases, see _render_table_noalias()
     */
    protected function _render_table($add_alias = true): ?string
    {
        // will be joined for output
        $ret = [];

        if (empty($this->args['table'])) {
            return '';
        }

        // process tables one by one
        foreach ($this->args['table'] as $alias => $table) {
            // throw exception if we don't want to add alias and table is defined as Expression
            if ($add_alias === false && $table instanceof self) {
                throw new Exception('Table cannot be Query in UPDATE, INSERT etc. query modes');
            }

            // Do not add alias, if:
            //  - we don't want aliases OR
            //  - alias is the same as table name OR
            //  - alias is numeric
            if (
                $add_alias === false
                || (is_string($table) && $alias === $table)
                || is_numeric($alias)
            ) {
                $alias = null;
            }

            // consume or escape table
            $table = $this->consume($table, self::ESCAPE_IDENTIFIER_SOFT);

            // add alias if needed
            if ($alias) {
                $table .= ' ' . $this->escapeIdentifier($alias);
            }

            $ret[] = $table;
        }

        return implode(', ', $ret);
    }

    protected function _render_table_noalias(): ?string
    {
        return $this->_render_table(false);
    }

    protected function _render_from(): ?string
    {
        return empty($this->args['table']) ? '' : 'from';
    }

    /// }}}

    // {{{ with()

    /**
     * Specify WITH query to be used.
     *
     * @param Query  $cursor    Specifies cursor query or array [alias => query] for adding multiple
     * @param string $alias     Specify alias for this cursor
     * @param array  $fields    Optional array of field names used in cursor
     * @param bool   $recursive Is it recursive?
     *
     * @return $this
     */
    public function with(self $cursor, string $alias, array $fields = null, bool $recursive = false)
    {
        // save cursor in args
        $this->_set_args('with', $alias, [
            'cursor' => $cursor,
            'fields' => $fields,
            'recursive' => $recursive,
        ]);

        return $this;
    }

    /**
     * Recursive WITH query.
     *
     * @param Query  $cursor Specifies cursor query or array [alias => query] for adding multiple
     * @param string $alias  Specify alias for this cursor
     * @param array  $fields Optional array of field names used in cursor
     *
     * @return $this
     */
    public function withRecursive(self $cursor, string $alias, array $fields = null)
    {
        return $this->with($cursor, $alias, $fields, true);
    }

    protected function _render_with(): ?string
    {
        // will be joined for output
        $ret = [];

        if (empty($this->args['with'])) {
            return '';
        }

        // process each defined cursor
        $isRecursive = false;
        foreach ($this->args['with'] as $alias => ['cursor' => $cursor, 'fields' => $fields, 'recursive' => $recursive]) {
            // cursor alias cannot be expression, so simply escape it
            $s = $this->escapeIdentifier($alias) . ' ';

            // set cursor fields
            if ($fields !== null) {
                $s .= '(' . implode(', ', array_map([$this, 'escapeIdentifier'], $fields)) . ') ';
            }

            // will parameterize the value and escape if necessary
            $s .= 'as ' . $this->consume($cursor, self::ESCAPE_IDENTIFIER_SOFT);

            // is at least one recursive ?
            $isRecursive = $isRecursive || $recursive;

            $ret[] = $s;
        }

        return 'with ' . ($isRecursive ? 'recursive ' : '') . implode(', ', $ret) . ' ';
    }

    /// }}}

    // {{{ join()

    /**
     * Joins your query with another table. Join will use $main_table
     * to reference the main table, unless you specify it explicitly.
     *
     * Examples:
     *  $q->join('address');         // on user.address_id=address.id
     *  $q->join('address.user_id'); // on address.user_id=user.id
     *  $q->join('address a');       // With alias
     *  $q->join(array('a' => 'address')); // Also alias
     *
     * Second argument may specify the field of the master table
     *  $q->join('address', 'billing_id');
     *  $q->join('address.code', 'code');
     *  $q->join('address.code', 'user.code');
     *
     * Third argument may specify which kind of join to use.
     *  $q->join('address', null, 'left');
     *  $q->join('address.code', 'user.code', 'inner');
     *
     * Using array syntax you can join multiple tables too
     *  $q->join(array('a' => 'address', 'p' => 'portfolio'));
     *
     * You can use expression for more complex joins
     *  $q->join('address',
     *      $q->orExpr()
     *          ->where('user.billing_id=address.id')
     *          ->where('user.technical_id=address.id')
     *  )
     *
     * @param string|array $foreign_table  Table to join with
     * @param mixed        $master_field   Field in master table
     * @param string       $join_kind      'left' or 'inner', etc
     * @param string       $_foreign_alias Internal, don't use
     *
     * @return $this
     */
    public function join(
        $foreign_table,
        $master_field = null,
        $join_kind = null,
        $_foreign_alias = null
    ) {
        // If array - add recursively
        if (is_array($foreign_table)) {
            foreach ($foreign_table as $alias => $foreign) {
                if (is_numeric($alias)) {
                    $alias = null;
                }

                $this->join($foreign, $master_field, $join_kind, $alias);
            }

            return $this;
        }
        $j = [];

        // try to find alias in foreign table definition. this behaviour should be deprecated
        if ($_foreign_alias === null) {
            [$foreign_table, $_foreign_alias] = array_pad(explode(' ', $foreign_table, 2), 2, null);
        }

        // Split and deduce fields
        // NOTE that this will not allow table names with dots in there !!!
        [$f1, $f2] = array_pad(explode('.', $foreign_table, 2), 2, null);

        if (is_object($master_field)) {
            $j['expr'] = $master_field;
        } else {
            // Split and deduce primary table
            if ($master_field === null) {
                [$m1, $m2] = [null, null];
            } else {
                [$m1, $m2] = array_pad(explode('.', $master_field, 2), 2, null);
            }
            if ($m2 === null) {
                $m2 = $m1;
                $m1 = null;
            }
            if ($m1 === null) {
                $m1 = $this->main_table;
            }

            // Identify fields we use for joins
            if ($f2 === null && $m2 === null) {
                $m2 = $f1 . '_id';
            }
            if ($m2 === null) {
                $m2 = 'id';
            }
            $j['m1'] = $m1;
            $j['m2'] = $m2;
        }

        $j['f1'] = $f1;
        if ($f2 === null) {
            $f2 = 'id';
        }
        $j['f2'] = $f2;

        $j['t'] = $join_kind ?: 'left';
        $j['fa'] = $_foreign_alias;

        $this->args['join'][] = $j;

        return $this;
    }

    public function _render_join(): ?string
    {
        if (!isset($this->args['join'])) {
            return '';
        }
        $joins = [];
        foreach ($this->args['join'] as $j) {
            $jj = '';

            $jj .= $j['t'] . ' join ';

            $jj .= $this->escapeIdentifierSoft($j['f1']);

            if ($j['fa'] !== null) {
                $jj .= ' ' . $this->escapeIdentifier($j['fa']);
            }

            $jj .= ' on ';

            if (isset($j['expr'])) {
                $jj .= $this->consume($j['expr']);
            } else {
                $jj .=
                    $this->escapeIdentifier($j['fa'] ?: $j['f1']) . '.' .
                    $this->escapeIdentifier($j['f2']) . ' = ' .
                    ($j['m1'] === null ? '' : $this->escapeIdentifier($j['m1']) . '.') .
                    $this->escapeIdentifier($j['m2']);
            }
            $joins[] = $jj;
        }

        return ' ' . implode(' ', $joins);
    }

    // }}}

    // {{{ where() and having() specification and rendering

    /**
     * Adds condition to your query.
     *
     * Examples:
     *  $q->where('id',1);
     *
     * By default condition implies equality. You can specify a different comparison
     * operator by either including it along with the field or using 3-argument
     * format:
     *  $q->where('id>','1');
     *  $q->where('id','>',1);
     *
     * You may use Expression as any part of the query.
     *  $q->where($q->expr('a=b'));
     *  $q->where('date>',$q->expr('now()'));
     *  $q->where($q->expr('length(password)'),'>',5);
     *
     * If you specify Query as an argument, it will be automatically
     * surrounded by brackets:
     *  $q->where('user_id',$q->dsql()->table('users')->field('id'));
     *
     * To specify OR conditions:
     *  $q->where($q->orExpr()->where('a',1)->where('b',1));
     *
     * @param mixed  $field    Field or Expression
     * @param mixed  $cond     Condition such as '=', '>' or 'is not'
     * @param mixed  $value    Value. Will be quoted unless you pass expression
     * @param string $kind     Do not use directly. Use having()
     * @param int    $num_args when $kind is passed, we can't determine number of
     *                         actual arguments, so this argument must be specified
     *
     * @return $this
     */
    public function where($field, $cond = null, $value = null, $kind = 'where', $num_args = null)
    {
        // Number of passed arguments will be used to determine if arguments were specified or not
        if ($num_args === null) {
            $num_args = func_num_args();
        }

        // Array as first argument means we have to replace it with orExpr()
        // remove in v2.5
        if (is_array($field)) {
            throw new Exception('Array input / OR conditions is no longer supported');
        }

        // first argument is string containing more than just a field name and no more than 2
        // arguments means that we either have a string expression or embedded condition.
        if ($num_args === 2 && is_string($field) && !preg_match('/^[.a-zA-Z0-9_]*$/', $field)) {
            // field contains non-alphanumeric values. Look for condition
            preg_match(
                '/^([^ <>!=]*)([><!=]*|( *(not|is|in|like))*) *$/',
                $field,
                $matches
            );

            // matches[2] will contain the condition, but $cond will contain the value
            $value = $cond;
            $cond = $matches[2];

            // if we couldn't clearly identify the condition, we might be dealing with
            // a more complex expression. If expression is followed by another argument
            // we need to add equation sign  where('now()',123).
            if (!$cond) {
                $matches[1] = $this->expr($field);

                $cond = '=';
            } else {
                ++$num_args;
            }

            $field = $matches[1];
        }

        switch ($num_args) {
            case 1:
                if (is_string($field)) {
                    $field = $this->expr($field);
                    $field->wrapInParentheses = true;
                } elseif (!$field->wrapInParentheses) {
                    $field = $this->expr('[]', [$field]);
                    $field->wrapInParentheses = true;
                }

                $this->args[$kind][] = [$field];

                break;
            case 2:
                if (is_object($cond) && !$cond instanceof Expressionable) {
                    throw (new Exception('Value cannot be converted to SQL-compatible expression'))
                        ->addMoreInfo('field', $field)
                        ->addMoreInfo('value', $cond);
                }

                $this->args[$kind][] = [$field, $cond];

                break;
            case 3:
                if (is_object($value) && !$value instanceof Expressionable) {
                    throw (new Exception('Value cannot be converted to SQL-compatible expression'))
                        ->addMoreInfo('field', $field)
                        ->addMoreInfo('cond', $cond)
                        ->addMoreInfo('value', $value);
                }

                $this->args[$kind][] = [$field, $cond, $value];

                break;
        }

        return $this;
    }

    /**
     * Same syntax as where().
     *
     * @param mixed $field Field or Expression
     * @param mixed $cond  Condition such as '=', '>' or 'is not'
     * @param mixed $value Value. Will be quoted unless you pass expression
     *
     * @return $this
     */
    public function having($field, $cond = null, $value = null)
    {
        return $this->where($field, $cond, $value, 'having', func_num_args());
    }

    /**
     * Subroutine which renders either [where] or [having].
     *
     * @param string $kind 'where' or 'having'
     *
     * @return string[]
     */
    protected function _sub_render_where($kind): array
    {
        // will be joined for output
        $ret = [];

        // where() might have been called multiple times. Collect all conditions,
        // then join them with AND keyword
        foreach ($this->args[$kind] as $row) {
            $ret[] = $this->_sub_render_condition($row);
        }

        return $ret;
    }

    protected function _sub_render_condition(array $row): string
    {
        if (count($row) === 3) {
            [$field, $cond, $value] = $row;
        } elseif (count($row) === 2) {
            [$field, $cond] = $row;
        } elseif (count($row) === 1) {
            [$field] = $row;
        } else {
            throw new \InvalidArgumentException();
        }

        $field = $this->consume($field, self::ESCAPE_IDENTIFIER_SOFT);

        if (count($row) === 1) {
            // Only a single parameter was passed, so we simply include all
            return $field;
        }

        // below are only cases when 2 or 3 arguments are passed

        // if no condition defined - set default condition
        if (count($row) === 2) {
            $value = $cond; // @phpstan-ignore-line see https://github.com/phpstan/phpstan/issues/4173

            if ($value instanceof Expressionable) {
                $value = $value->getDsqlExpression($this);
            }

            if (is_array($value)) {
                $cond = 'in';
            } elseif ($value instanceof self && $value->mode === 'select') {
                $cond = 'in';
            } else {
                $cond = '=';
            }
        } else {
            $cond = trim(strtolower($cond)); // @phpstan-ignore-line see https://github.com/phpstan/phpstan/issues/4173
        }

        // below we can be sure that all 3 arguments has been passed

        // special conditions (IS | IS NOT) if value is null
        if ($value === null) { // @phpstan-ignore-line see https://github.com/phpstan/phpstan/issues/4173
            if (in_array($cond, ['=', 'is'], true)) {
                return $field . ' is null';
            } elseif (in_array($cond, ['!=', '<>', 'not', 'is not'], true)) {
                return $field . ' is not null';
            }
        }

        // value should be array for such conditions
        if (in_array($cond, ['in', 'not in', 'not'], true) && is_string($value)) {
            $value = array_map('trim', explode(',', $value));
        }

        // special conditions (IN | NOT IN) if value is array
        if (is_array($value)) {
            $cond = in_array($cond, ['!=', '<>', 'not', 'not in'], true) ? 'not in' : 'in';

            // special treatment of empty array condition
            if (empty($value)) {
                if ($cond === 'in') {
                    return '1 = 0'; // never true
                }

                return '1 = 1'; // always true
            }

            $value = '(' . implode(', ', array_map(function ($v) { return $this->escapeParam($v); }, $value)) . ')';

            return $field . ' ' . $cond . ' ' . $value;
        }

        // if value is object, then it should be Expression or Query itself
        // otherwise just escape value
        $value = $this->consume($value, self::ESCAPE_PARAM);

        return $field . ' ' . $cond . ' ' . $value;
    }

    protected function _render_where(): ?string
    {
        if (!isset($this->args['where'])) {
            return null;
        }

        return ' where ' . implode(' and ', $this->_sub_render_where('where'));
    }

    protected function _render_orwhere(): ?string
    {
        if (isset($this->args['where']) && isset($this->args['having'])) {
            throw new Exception('Mixing of WHERE and HAVING conditions not allowed in query expression');
        }

        foreach (['where', 'having'] as $kind) {
            if (isset($this->args[$kind])) {
                return implode(' or ', $this->_sub_render_where($kind));
            }
        }

        return null;
    }

    protected function _render_andwhere(): ?string
    {
        if (isset($this->args['where']) && isset($this->args['having'])) {
            throw new Exception('Mixing of WHERE and HAVING conditions not allowed in query expression');
        }

        foreach (['where', 'having'] as $kind) {
            if (isset($this->args[$kind])) {
                return implode(' and ', $this->_sub_render_where($kind));
            }
        }

        return null;
    }

    protected function _render_having(): ?string
    {
        if (!isset($this->args['having'])) {
            return null;
        }

        return ' having ' . implode(' and ', $this->_sub_render_where('having'));
    }

    // }}}

    // {{{ group()

    /**
     * Implements GROUP BY functionality. Simply pass either field name
     * as string or expression.
     *
     * @param mixed $group Group by this
     *
     * @return $this
     */
    public function group($group)
    {
        // Case with comma-separated fields
        if (is_string($group) && !$this->isUnescapablePattern($group) && strpos($group, ',') !== false) {
            $group = explode(',', $group);
        }

        if (is_array($group)) {
            foreach ($group as $g) {
                $this->args['group'][] = $g;
            }

            return $this;
        }

        $this->args['group'][] = $group;

        return $this;
    }

    protected function _render_group(): ?string
    {
        if (!isset($this->args['group'])) {
            return '';
        }

        $g = array_map(function ($a) {
            return $this->consume($a, self::ESCAPE_IDENTIFIER_SOFT);
        }, $this->args['group']);

        return ' group by ' . implode(', ', $g);
    }

    // }}}

    // {{{ Set field implementation

    /**
     * Sets field value for INSERT or UPDATE statements.
     *
     * @param string|Expression|Expressionable|array $field Name of the field
     * @param mixed                                  $value Value of the field
     *
     * @return $this
     */
    public function set($field, $value = null)
    {
        if (is_array($value)) {
            throw (new Exception('Array values are not supported by SQL'))
                ->addMoreInfo('field', $field)
                ->addMoreInfo('value', $value);
        }

        if (is_array($field)) {
            foreach ($field as $key => $value) {
                $this->set($key, $value);
            }

            return $this;
        }

        if (is_string($field) || $field instanceof Expressionable) {
            $this->args['set'][] = [$field, $value];
        } else {
            throw (new Exception('Field name should be string or Expressionable'))
                ->addMoreInfo('field', $field);
        }

        return $this;
    }

    protected function _render_set(): ?string
    {
        // will be joined for output
        $ret = [];

        if (isset($this->args['set']) && $this->args['set']) {
            foreach ($this->args['set'] as [$field, $value]) {
                $field = $this->consume($field, self::ESCAPE_IDENTIFIER);
                $value = $this->consume($value, self::ESCAPE_PARAM);

                $ret[] = $field . '=' . $value;
            }
        }

        return implode(', ', $ret);
    }

    protected function _render_set_fields(): ?string
    {
        // will be joined for output
        $ret = [];

        if ($this->args['set']) {
            foreach ($this->args['set'] as [$field/*, $value*/]) {
                $field = $this->consume($field, self::ESCAPE_IDENTIFIER);

                $ret[] = $field;
            }
        }

        return implode(', ', $ret);
    }

    protected function _render_set_values(): ?string
    {
        // will be joined for output
        $ret = [];

        if ($this->args['set']) {
            foreach ($this->args['set'] as [/*$field*/ , $value]) {
                $value = $this->consume($value, self::ESCAPE_PARAM);

                $ret[] = $value;
            }
        }

        return implode(', ', $ret);
    }

    // }}}

    // {{{ Option

    /**
     * Set options for particular mode.
     *
     * @param mixed  $option
     * @param string $mode   select|insert|replace
     *
     * @return $this
     */
    public function option($option, $mode = 'select')
    {
        // Case with comma-separated options
        if (is_string($option) && strpos($option, ',') !== false) {
            $option = explode(',', $option);
        }

        if (is_array($option)) {
            foreach ($option as $opt) {
                $this->args['option'][$mode][] = $opt;
            }

            return $this;
        }

        $this->args['option'][$mode][] = $option;

        return $this;
    }

    protected function _render_option(): ?string
    {
        if (!isset($this->args['option'][$this->mode])) {
            return '';
        }

        return ' ' . implode(' ', $this->args['option'][$this->mode]);
    }

    // }}}

    // {{{ Query Modes

    /**
     * Execute select statement.
     *
     * @return DbalResult|\PDOStatement PDOStatement iff for DBAL 2.x
     */
    public function select(): object
    {
        return $this->mode('select')->execute();
    }

    /**
     * Execute insert statement.
     *
     * @return DbalResult|\PDOStatement PDOStatement iff for DBAL 2.x
     */
    public function insert(): object
    {
        return $this->mode('insert')->execute();
    }

    /**
     * Execute update statement.
     *
     * @return DbalResult|\PDOStatement PDOStatement iff for DBAL 2.x
     */
    public function update(): object
    {
        return $this->mode('update')->execute();
    }

    /**
     * Execute replace statement.
     *
     * @return DbalResult|\PDOStatement PDOStatement iff for DBAL 2.x
     */
    public function replace(): object
    {
        return $this->mode('replace')->execute();
    }

    /**
     * Execute delete statement.
     *
     * @return DbalResult|\PDOStatement PDOStatement iff for DBAL 2.x
     */
    public function delete(): object
    {
        return $this->mode('delete')->execute();
    }

    /**
     * Execute truncate statement.
     *
     * @return DbalResult|\PDOStatement PDOStatement iff for DBAL 2.x
     */
    public function truncate(): object
    {
        return $this->mode('truncate')->execute();
    }

    // }}}

    // {{{ Limit

    /**
     * Limit how many rows will be returned.
     *
     * @param int $cnt   Number of rows to return
     * @param int $shift Offset, how many rows to skip
     *
     * @return $this
     */
    public function limit($cnt, $shift = null)
    {
        $this->args['limit'] = [
            'cnt' => $cnt,
            'shift' => $shift,
        ];

        return $this;
    }

    public function _render_limit(): ?string
    {
        if (!isset($this->args['limit'])) {
            return null;
        }

        return ' limit ' .
            (int) $this->args['limit']['shift'] .
            ', ' .
            (int) $this->args['limit']['cnt'];
    }

    // }}}

    // {{{ Order

    /**
     * Orders results by field or Expression. See documentation for full
     * list of possible arguments.
     *
     * $q->order('name');
     * $q->order('name desc');
     * $q->order('name desc, id asc')
     * $q->order('name',true);
     *
     * @param string|Expressionable|array $order order by
     * @param string|bool                 $desc  true to sort descending
     *
     * @return $this
     */
    public function order($order, $desc = null)
    {
        // Case with comma-separated fields or first argument being an array
        if (is_string($order) && strpos($order, ',') !== false) {
            $order = explode(',', $order);
        }

        if (is_array($order)) {
            if ($desc !== null) {
                throw new Exception(
                    'If first argument is array, second argument must not be used'
                );
            }
            foreach (array_reverse($order) as $o) {
                $this->order($o);
            }

            return $this;
        }

        // First argument may contain space, to divide field and ordering keyword.
        // Explode string only if ordering keyword is 'desc' or 'asc'.
        if ($desc === null && is_string($order) && strpos($order, ' ') !== false) {
            $_chunks = explode(' ', $order);
            $_desc = strtolower(array_pop($_chunks));
            if (in_array($_desc, ['desc', 'asc'], true)) {
                $order = implode(' ', $_chunks);
                $desc = $_desc;
            }
        }

        if (is_bool($desc)) {
            $desc = $desc ? 'desc' : '';
        } elseif (strtolower($desc ?? '') === 'asc') {
            $desc = '';
        }
        // no else - allow custom order like "order by name desc nulls last" for Oracle

        $this->args['order'][] = [$order, $desc];

        return $this;
    }

    public function _render_order(): ?string
    {
        if (!isset($this->args['order'])) {
            return '';
        }

        $x = [];
        foreach ($this->args['order'] as $tmp) {
            [$arg, $desc] = $tmp;
            $x[] = $this->consume($arg, self::ESCAPE_IDENTIFIER_SOFT) . ($desc ? (' ' . $desc) : '');
        }

        return ' order by ' . implode(', ', array_reverse($x));
    }

    // }}}

    // {{{ Exists

    /**
     * Creates 'select exists' query based on the query object.
     *
     * @return self
     */
    public function exists()
    {
        return $this->dsql()->mode('select')->option('exists')->field($this);
    }

    // }}}

    public function __debugInfo(): array
    {
        $arr = [
            'R' => false,
            'mode' => $this->mode,
            //'template' => $this->template,
            //'params' => $this->params,
            //'connection' => $this->connection,
            //'main_table' => $this->main_table,
            //'args' => $this->args,
        ];

        try {
            $arr['R'] = $this->getDebugQuery();
        } catch (\Exception $e) {
            $arr['R'] = $e->getMessage();
        }

        return $arr;
    }

    // {{{ Miscelanious

    /**
     * Renders query template. If the template is not explicitly set will use "select" mode.
     */
    public function render(): string
    {
        if (!$this->template) {
            $this->mode('select');
        }

        return parent::render();
    }

    /**
     * Switch template for this query. Determines what would be done
     * on execute.
     *
     * By default it is in SELECT mode
     *
     * @param string $mode
     *
     * @return $this
     */
    public function mode($mode)
    {
        $prop = 'template_' . $mode;

        if (@isset($this->{$prop})) { // @ is needed to pass phpunit without a deprecation warning
            $this->mode = $mode;
            $this->template = $this->{$prop};
        } else {
            throw (new Exception('Query does not have this mode'))
                ->addMoreInfo('mode', $mode);
        }

        return $this;
    }

    /**
     * Use this instead of "new Query()" if you want to automatically bind
     * query to the same connection as the parent.
     *
     * @param array $properties
     *
     * @return Query
     */
    public function dsql($properties = [])
    {
        $q = new static($properties);
        $q->connection = $this->connection;

        return $q;
    }

    /**
     * Returns Expression object for the corresponding Query
     * sub-class (e.g. Mysql\Query will return Mysql\Expression).
     *
     * Connection is not mandatory, but if set, will be preserved. This
     * method should be used for building parts of the query internally.
     *
     * @param string|array $properties
     * @param array        $arguments
     *
     * @return Expression
     */
    public function expr($properties = [], $arguments = null)
    {
        $c = $this->expression_class;
        $e = new $c($properties, $arguments);
        $e->connection = $this->connection;

        return $e;
    }

    /**
     * Returns Expression object for NOW() or CURRENT_TIMESTAMP() method.
     */
    public function exprNow(int $precision = null): Expression
    {
        return $this->expr(
            'current_timestamp(' . ($precision !== null ? '[]' : '') . ')',
            $precision !== null ? [$precision] : []
        );
    }

    /**
     * Returns new Query object of [or] expression.
     *
     * @return Query
     */
    public function orExpr()
    {
        return $this->dsql(['template' => '[orwhere]']);
    }

    /**
     * Returns new Query object of [and] expression.
     *
     * @return Query
     */
    public function andExpr()
    {
        return $this->dsql(['template' => '[andwhere]']);
    }

    /**
     * Returns Query object of [case] expression.
     *
     * @param mixed $operand optional operand for case expression
     *
     * @return Query
     */
    public function caseExpr($operand = null)
    {
        $q = $this->dsql(['template' => '[case]']);

        if ($operand !== null) {
            $q->args['case_operand'] = $operand;
        }

        return $q;
    }

    /**
     * Returns a query for a function, which can be used as part of the GROUP
     * query which would concatenate all matching fields.
     *
     * MySQL, SQLite - group_concat
     * PostgreSQL - string_agg
     * Oracle - listagg
     *
     * @param mixed $field
     *
     * @return Expression
     */
    public function groupConcat($field, string $delimiter = ',')
    {
        throw new Exception('groupConcat() is SQL-dependent, so use a correct class');
    }

    /**
     * Add when/then condition for [case] expression.
     *
     * @param mixed $when Condition as array for normal form [case] statement or just value in case of short form [case] statement
     * @param mixed $then Then expression or value
     *
     * @return $this
     */
    public function when($when, $then)
    {
        $this->args['case_when'][] = [$when, $then];

        return $this;
    }

    /**
     * Add else condition for [case] expression.
     *
     * @param mixed $else Else expression or value
     *
     * @return $this
     */
    //public function else($else) // PHP 5.6 restricts to use such method name. PHP 7 is fine with it
    public function otherwise($else)
    {
        $this->args['case_else'] = $else;

        return $this;
    }

    protected function _render_case(): ?string
    {
        if (!isset($this->args['case_when'])) {
            return null;
        }

        $ret = '';

        // operand
        if ($short_form = isset($this->args['case_operand'])) {
            $ret .= ' ' . $this->consume($this->args['case_operand'], self::ESCAPE_IDENTIFIER_SOFT);
        }

        // when, then
        foreach ($this->args['case_when'] as $row) {
            if (!array_key_exists(0, $row) || !array_key_exists(1, $row)) {
                throw (new Exception('Incorrect use of "when" method parameters'))
                    ->addMoreInfo('row', $row);
            }

            $ret .= ' when ';
            if ($short_form) {
                // short-form
                if (is_array($row[0])) {
                    throw (new Exception('When using short form CASE statement, then you should not set array as when() method 1st parameter'))
                        ->addMoreInfo('when', $row[0]);
                }
                $ret .= $this->consume($row[0], self::ESCAPE_PARAM);
            } else {
                $ret .= $this->_sub_render_condition($row[0]);
            }

            // then
            $ret .= ' then ' . $this->consume($row[1], self::ESCAPE_PARAM);
        }

        // else
        if (array_key_exists('case_else', $this->args)) {
            $ret .= ' else ' . $this->consume($this->args['case_else'], self::ESCAPE_PARAM);
        }

        return ' case' . $ret . ' end';
    }

    /**
     * Sets value in args array. Doesn't allow duplicate aliases.
     *
     * @param string      $what  Where to set it - table|field
     * @param string|null $alias Alias name
     * @param mixed       $value Value to set in args array
     */
    protected function _set_args($what, $alias, $value): void
    {
        // save value in args
        if ($alias === null) {
            $this->args[$what][] = $value;
        } else {
            // don't allow multiple values with same alias
            if (isset($this->args[$what][$alias])) {
                throw (new Exception('Alias should be unique'))
                    ->addMoreInfo('what', $what)
                    ->addMoreInfo('alias', $alias);
            }

            $this->args[$what][$alias] = $value;
        }
    }

    /// }}}
}
