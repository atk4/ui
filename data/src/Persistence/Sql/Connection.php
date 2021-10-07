<?php

declare(strict_types=1);

namespace Atk4\Data\Persistence\Sql;

use Atk4\Core\DiContainerTrait;
use Doctrine\DBAL\Connection as DbalConnection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Platforms\OraclePlatform;
use Doctrine\DBAL\Platforms\PostgreSQL94Platform;
use Doctrine\DBAL\Platforms\SQLServer2012Platform;
use Doctrine\DBAL\Result as DbalResult;

/**
 * Class for establishing and maintaining connection with your database.
 */
abstract class Connection
{
    use DiContainerTrait;

    /** @var string Query classname */
    protected $query_class = Query::class;

    /** @var string Expression classname */
    protected $expression_class = Expression::class;

    /** @var DbalConnection */
    protected $connection;

    /**
     * Stores the driverSchema => connectionClass array for resolving.
     *
     * @var array
     */
    protected static $connectionClassRegistry = [
        'sqlite' => Sqlite\Connection::class,
        'mysql' => Mysql\Connection::class,
        'pgsql' => Postgresql\Connection::class,
        'oci' => Oracle\Connection::class,
        'sqlsrv' => Mssql\Connection::class,
    ];

    /**
     * Specifying $properties to constructors will override default
     * property values of this class.
     *
     * @param array $properties
     */
    public function __construct($properties = [])
    {
        if (!is_array($properties)) {
            throw (new Exception('Invalid properties for "new Connection()". Did you mean to call Connection::connect()?'))
                ->addMoreInfo('properties', $properties);
        }

        $this->setDefaults($properties);
    }

    public function __destruct()
    {
        // needed for DBAL connection to be released immeditelly
        if ($this->connection !== null) {
            $this->connection->close();
        }
    }

    /**
     * Normalize DSN connection string.
     *
     * Returns normalized DSN as array ['dsn', 'user', 'pass', 'driverSchema', 'rest'].
     *
     * @param array|string $dsn  DSN string
     * @param string       $user Optional username, this takes precedence over dsn string
     * @param string       $pass Optional password, this takes precedence over dsn string
     *
     * @return array
     */
    public static function normalizeDsn($dsn, $user = null, $pass = null)
    {
        // Try to dissect DSN into parts
        $parts = is_array($dsn) ? $dsn : parse_url($dsn);

        // If parts are usable, convert DSN format
        if ($parts !== false && isset($parts['host'], $parts['path'])) {
            // DSN is using URL-like format, so we need to convert it
            $dsn = $parts['scheme'] . ':host=' . $parts['host']
                . (isset($parts['port']) ? ';port=' . $parts['port'] : '')
                . ';dbname=' . substr($parts['path'], 1);
            $user ??= $parts['user'] ?? null;
            $pass ??= $parts['pass'] ?? null;
        }

        // If it's still array, then simply use it
        if (is_array($dsn)) {
            return $dsn;
        }

        // If it's string, then find driver
        if (is_string($dsn)) {
            if (strpos($dsn, ':') === false) {
                throw (new Exception('Your DSN format is invalid. Must be in "driverSchema:host;options" format'))
                    ->addMoreInfo('dsn', $dsn);
            }
            [$driverSchema, $rest] = explode(':', $dsn, 2);
            $driverSchema = strtolower($driverSchema);
        } else {
            // currently impossible to be like this, but we don't want ugly exceptions here
            $driverSchema = null;
            $rest = null;
        }

        return ['dsn' => $dsn, 'user' => $user ?: null, 'pass' => $pass ?: null, 'driverSchema' => $driverSchema, 'rest' => $rest];
    }

    /**
     * Connect to database and return connection class.
     *
     * @param string|\PDO|DbalConnection $dsn
     * @param string|null                $user
     * @param string|null                $password
     * @param array                      $args
     *
     * @return Connection
     */
    public static function connect($dsn, $user = null, $password = null, $args = [])
    {
        // If it's already PDO or DbalConnection object, then we simply use it
        if ($dsn instanceof \PDO) {
            $connectionClass = self::resolveConnectionClass($dsn->getAttribute(\PDO::ATTR_DRIVER_NAME));
            $connectionArg = $connectionClass::connectDbalConnection(['pdo' => $dsn]);
        } elseif ($dsn instanceof DbalConnection) {
            /** @var \PDO */
            $pdo = self::isComposerDbal2x()
                ? $dsn->getWrappedConnection()
                : $dsn->getWrappedConnection()->getWrappedConnection();
            $connectionClass = self::resolveConnectionClass($pdo->getAttribute(\PDO::ATTR_DRIVER_NAME));
            $connectionArg = $dsn;
        } else {
            $dsn = static::normalizeDsn($dsn, $user, $password);
            $connectionClass = self::resolveConnectionClass($dsn['driverSchema']);
            $connectionArg = $connectionClass::connectDbalConnection($dsn);
        }

        return new $connectionClass(array_merge([
            'connection' => $connectionArg,
        ], $args));
    }

    /**
     * Adds connection class to the registry for resolving in Connection::resolve method.
     *
     * Can be used as:
     *
     * Connection::registerConnection(MySQL\Connection::class, 'mysql'), or
     * MySQL\Connection::registerConnectionClass()
     *
     * CustomDriver\Connection must be descendant of Connection class.
     *
     * @param string $connectionClass
     * @param string $driverSchema
     */
    public static function registerConnectionClass($connectionClass = null, $driverSchema = null): void
    {
        if ($connectionClass === null) {
            $connectionClass = static::class;
        }

        if ($driverSchema === null) {
            /** @var static $c */
            $c = (new \ReflectionClass($connectionClass))->newInstanceWithoutConstructor();
            $driverSchema = $c->getDatabasePlatform()->getName();
        }

        self::$connectionClassRegistry[$driverSchema] = $connectionClass;
    }

    /**
     * Resolves the connection class to use based on driver type.
     */
    public static function resolveConnectionClass(string $driverSchema): string
    {
        if (!isset(self::$connectionClassRegistry[$driverSchema])) {
            throw (new Exception('Driver schema is not registered'))
                ->addMoreInfo('driver_schema', $driverSchema);
        }

        return self::$connectionClassRegistry[$driverSchema];
    }

    final public static function isComposerDbal2x(): bool
    {
        return !class_exists(DbalResult::class);
    }

    /**
     * Establishes connection based on a $dsn.
     *
     * @return DbalConnection
     */
    protected static function connectDbalConnection(array $dsn)
    {
        if (isset($dsn['pdo'])) {
            $pdo = $dsn['pdo'];
        } else {
            $pdo = new \PDO($dsn['dsn'], $dsn['user'], $dsn['pass']);
        }

        // Doctrine DBAL 3.x does not support to create DBAL Connection with already
        // instanced PDO, so create it without PDO first, see:
        // https://github.com/doctrine/dbal/blob/v2.10.1/lib/Doctrine/DBAL/DriverManager.php#L179
        // https://github.com/doctrine/dbal/blob/3.0.0/src/DriverManager.php#L142
        // TODO probably drop support later
        if (self::isComposerDbal2x()) {
            $dbalConnection = DriverManager::getConnection([
                'pdo' => $pdo,
            ]);
        } else {
            $pdoConnection = (new \ReflectionClass(\Doctrine\DBAL\Driver\PDO\Connection::class))
                ->newInstanceWithoutConstructor();
            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            \Closure::bind(function () use ($pdoConnection, $pdo): void {
                $pdoConnection->connection = $pdo;
            }, null, \Doctrine\DBAL\Driver\PDO\Connection::class)();
            $dbalConnection = DriverManager::getConnection([
                'driver' => 'pdo_' . $pdo->getAttribute(\PDO::ATTR_DRIVER_NAME),
            ]);
            \Closure::bind(function () use ($dbalConnection, $pdoConnection): void {
                $dbalConnection->_conn = $pdoConnection;
            }, null, \Doctrine\DBAL\Connection::class)();
        }

        // DBAL 3.x removed some old platforms, to support instanceof reliably,
        // make sure that DBAL 2.x platform is always supported in DBAL 3.x, see:
        // https://github.com/doctrine/dbal/pull/3912
        // TODO drop once DBAL 2.x support is dropped
        if (
            in_array(get_class($dbalConnection->getDatabasePlatform()) . 'ForPhpstan', [
                'Doctrine\DBAL\Platforms\SQLServerPlatform' . 'ForPhpstan',
                'Doctrine\DBAL\Platforms\SQLServer2005Platform' . 'ForPhpstan',
                'Doctrine\DBAL\Platforms\SQLServer2008Platform' . 'ForPhpstan',
            ], true) && !($dbalConnection->getDatabasePlatform() instanceof SQLServer2012Platform)
        ) {
            \Closure::bind(function () use ($dbalConnection) {
                $dbalConnection->platform = new SQLServer2012Platform();
            }, null, DbalConnection::class)();
        } elseif (
            in_array(get_class($dbalConnection->getDatabasePlatform()) . 'ForPhpstan', [
                'Doctrine\DBAL\Platforms\PostgreSqlPlatform' . 'ForPhpstan',
                'Doctrine\DBAL\Platforms\PostgreSQL91Platform' . 'ForPhpstan',
                'Doctrine\DBAL\Platforms\PostgreSQL92Platform' . 'ForPhpstan',
            ], true) && !($dbalConnection->getDatabasePlatform() instanceof PostgreSQL94Platform)
        ) {
            \Closure::bind(function () use ($dbalConnection) {
                $dbalConnection->platform = new PostgreSQL94Platform();
            }, null, DbalConnection::class)();
        }

        // SQL Server DBAL platform has buggy identifier escaping, fix until fixed officially, see:
        // https://github.com/doctrine/dbal/pull/4360
        if ($dbalConnection->getDatabasePlatform() instanceof SQLServer2012Platform) {
            \Closure::bind(function () use ($dbalConnection) {
                $dbalConnection->platform = new class() extends SQLServer2012Platform {
                    protected function getCreateColumnCommentSQL($tableName, $columnName, $comment)
                    {
                        if (strpos($tableName, '.') !== false) {
                            [$schemaName, $tableName] = explode('.', $tableName, 2);
                        } else {
                            $schemaName = $this->getDefaultSchemaName();
                        }

                        return $this->getAddExtendedPropertySQL(
                            'MS_Description',
                            (string) $comment,
                            'SCHEMA',
                            $schemaName,
                            'TABLE',
                            $tableName,
                            'COLUMN',
                            $columnName
                        );
                    }

                    protected function getAlterColumnCommentSQL($tableName, $columnName, $comment)
                    {
                        if (strpos($tableName, '.') !== false) {
                            [$schemaName, $tableName] = explode('.', $tableName, 2);
                        } else {
                            $schemaName = $this->getDefaultSchemaName();
                        }

                        return $this->getUpdateExtendedPropertySQL(
                            'MS_Description',
                            (string) $comment,
                            'SCHEMA',
                            $schemaName,
                            'TABLE',
                            $tableName,
                            'COLUMN',
                            $columnName
                        );
                    }

                    protected function getDropColumnCommentSQL($tableName, $columnName)
                    {
                        if (strpos($tableName, '.') !== false) {
                            [$schemaName, $tableName] = explode('.', $tableName, 2);
                        } else {
                            $schemaName = $this->getDefaultSchemaName();
                        }

                        return $this->getDropExtendedPropertySQL(
                            'MS_Description',
                            'SCHEMA',
                            $schemaName,
                            'TABLE',
                            $tableName,
                            'COLUMN',
                            $columnName
                        );
                    }

                    private function quoteSingleIdentifierAsStringLiteral(string $levelName): string
                    {
                        return $this->quoteStringLiteral(preg_replace('~^\[|\]$~s', '', $levelName));
                    }

                    public function getAddExtendedPropertySQL(
                        $name,
                        $value = null,
                        $level0Type = null,
                        $level0Name = null,
                        $level1Type = null,
                        $level1Name = null,
                        $level2Type = null,
                        $level2Name = null
                    ) {
                        return 'EXEC sp_addextendedproperty'
                            . ' N' . $this->quoteStringLiteral($name) . ', N' . $this->quoteStringLiteral((string) $value)
                            . ', N' . $this->quoteStringLiteral((string) $level0Type)
                            . ', ' . $this->quoteSingleIdentifierAsStringLiteral((string) $level0Name)
                            . ', N' . $this->quoteStringLiteral((string) $level1Type)
                            . ', ' . $this->quoteSingleIdentifierAsStringLiteral((string) $level1Name)
                            . (
                                $level2Type !== null || $level2Name !== null
                                ? ', N' . $this->quoteStringLiteral((string) $level2Type)
                                  . ', ' . $this->quoteSingleIdentifierAsStringLiteral((string) $level2Name)
                                : ''
                            );
                    }

                    public function getDropExtendedPropertySQL(
                        $name,
                        $level0Type = null,
                        $level0Name = null,
                        $level1Type = null,
                        $level1Name = null,
                        $level2Type = null,
                        $level2Name = null
                    ) {
                        return 'EXEC sp_dropextendedproperty'
                            . ' N' . $this->quoteStringLiteral($name)
                            . ', N' . $this->quoteStringLiteral((string) $level0Type)
                            . ', ' . $this->quoteSingleIdentifierAsStringLiteral((string) $level0Name)
                            . ', N' . $this->quoteStringLiteral((string) $level1Type)
                            . ', ' . $this->quoteSingleIdentifierAsStringLiteral((string) $level1Name)
                            . (
                                $level2Type !== null || $level2Name !== null
                                ? ', N' . $this->quoteStringLiteral((string) $level2Type)
                                  . ', ' . $this->quoteSingleIdentifierAsStringLiteral((string) $level2Name)
                                : ''
                            );
                    }

                    public function getUpdateExtendedPropertySQL(
                        $name,
                        $value = null,
                        $level0Type = null,
                        $level0Name = null,
                        $level1Type = null,
                        $level1Name = null,
                        $level2Type = null,
                        $level2Name = null
                    ) {
                        return 'EXEC sp_updateextendedproperty'
                            . ' N' . $this->quoteStringLiteral($name) . ', N' . $this->quoteStringLiteral((string) $value)
                            . ', N' . $this->quoteStringLiteral((string) $level0Type)
                            . ', ' . $this->quoteSingleIdentifierAsStringLiteral((string) $level0Name)
                            . ', N' . $this->quoteStringLiteral((string) $level1Type)
                            . ', ' . $this->quoteSingleIdentifierAsStringLiteral((string) $level1Name)
                            . (
                                $level2Type !== null || $level2Name !== null
                                ? ', N' . $this->quoteStringLiteral((string) $level2Type)
                                  . ', ' . $this->quoteSingleIdentifierAsStringLiteral((string) $level2Name)
                                : ''
                            );
                    }

                    protected function getCommentOnTableSQL(string $tableName, ?string $comment): string
                    {
                        if (strpos($tableName, '.') !== false) {
                            [$schemaName, $tableName] = explode('.', $tableName, 2);
                        } else {
                            $schemaName = $this->getDefaultSchemaName();
                        }

                        return $this->getAddExtendedPropertySQL(
                            'MS_Description',
                            (string) $comment,
                            'SCHEMA',
                            $schemaName,
                            'TABLE',
                            $tableName
                        );
                    }
                };
            }, null, DbalConnection::class)();
        }

        // Oracle CLOB/BLOB has limited SQL support, see:
        // https://stackoverflow.com/questions/12980038/ora-00932-inconsistent-datatypes-expected-got-clob#12980560
        // fix this Oracle inconsistency by using VARCHAR/VARBINARY instead (but limited to 4000 bytes)
        if ($dbalConnection->getDatabasePlatform() instanceof OraclePlatform) {
            \Closure::bind(function () use ($dbalConnection) {
                $dbalConnection->platform = new class() extends OraclePlatform {
                    private function forwardTypeDeclarationSQL(string $targetMethodName, array $column): string
                    {
                        $backtrace = debug_backtrace(\DEBUG_BACKTRACE_PROVIDE_OBJECT | \DEBUG_BACKTRACE_IGNORE_ARGS);
                        foreach ($backtrace as $frame) {
                            if ($this === ($frame['object'] ?? null)
                                && $targetMethodName === ($frame['function'] ?? null)) {
                                throw new Exception('Long CLOB/TEXT (4000+ bytes) is not supported for Oracle');
                            }
                        }

                        return $this->{$targetMethodName}($column);
                    }

                    public function getClobTypeDeclarationSQL(array $column)
                    {
                        $column['length'] = $this->getVarcharMaxLength();

                        return $this->forwardTypeDeclarationSQL('getVarcharTypeDeclarationSQL', $column);
                    }

                    public function getBlobTypeDeclarationSQL(array $column)
                    {
                        $column['length'] = $this->getBinaryMaxLength();

                        return $this->forwardTypeDeclarationSQL('getBinaryTypeDeclarationSQL', $column);
                    }
                };
            }, null, DbalConnection::class)();
        }

        return $dbalConnection;
    }

    /**
     * Returns new Query object with connection already set.
     *
     * @param string|array $properties
     */
    public function dsql($properties = []): Query
    {
        $c = $this->query_class;
        $q = new $c($properties);
        $q->connection = $this;

        return $q;
    }

    /**
     * Returns Expression object with connection already set.
     *
     * @param string|array $properties
     * @param array        $arguments
     */
    public function expr($properties = [], $arguments = null): Expression
    {
        $c = $this->expression_class;
        $e = new $c($properties, $arguments);
        $e->connection = $this;

        return $e;
    }

    /**
     * @return DbalConnection
     */
    public function connection()
    {
        return $this->connection;
    }

    /**
     * Execute Expression by using this connection.
     *
     * @return DbalResult|\PDOStatement PDOStatement iff for DBAL 2.x
     */
    public function execute(Expression $expr): object
    {
        if ($this->connection === null) {
            throw new Exception('Queries cannot be executed through this connection');
        }

        return $expr->execute($this->connection);
    }

    /**
     * Atomic executes operations within one begin/end transaction, so if
     * the code inside callback will fail, then all of the transaction
     * will be also rolled back.
     *
     * @param mixed ...$args
     *
     * @return mixed
     */
    public function atomic(\Closure $fx, ...$args)
    {
        $this->beginTransaction();
        try {
            $res = $fx(...$args);
            $this->commit();

            return $res;
        } catch (\Exception $e) {
            $this->rollBack();

            throw $e;
        }
    }

    /**
     * Starts new transaction.
     *
     * Database driver supports statements for starting and committing
     * transactions. Unfortunately most of them don't allow to nest
     * transactions and commit gradually.
     * With this method you have some implementation of nested transactions.
     *
     * When you call it for the first time it will begin transaction. If you
     * call it more times, it will do nothing but will increase depth counter.
     * You will need to call commit() for each execution of beginTransactions()
     * and only the last commit will perform actual commit in database.
     *
     * So, if you have been working with the database and got un-handled
     * exception in the middle of your code, everything will be rolled back.
     */
    public function beginTransaction(): void
    {
        try {
            $this->connection->beginTransaction();
        } catch (\Doctrine\DBAL\ConnectionException $e) {
            throw new Exception('Begin transaction failed', 0, $e);
        }
    }

    /**
     * Will return true if currently running inside a transaction.
     * This is useful if you are logging anything into a database. If you are
     * inside a transaction, don't log or it may be rolled back.
     * Perhaps use a hook for this?
     */
    public function inTransaction(): bool
    {
        return $this->connection->isTransactionActive();
    }

    /**
     * Commits transaction.
     *
     * Each occurrence of beginTransaction() must be matched with commit().
     * Only when same amount of commits are executed, the actual commit will be
     * issued to the database.
     */
    public function commit(): void
    {
        try {
            $this->connection->commit();
        } catch (\Doctrine\DBAL\ConnectionException $e) {
            throw new Exception('Commit failed', 0, $e);
        }
    }

    /**
     * Rollbacks queries since beginTransaction and resets transaction depth.
     */
    public function rollBack(): void
    {
        try {
            $this->connection->rollBack();
        } catch (\Doctrine\DBAL\ConnectionException $e) {
            throw new Exception('Rollback failed', 0, $e);
        }
    }

    /**
     * Return last inserted ID value.
     *
     * Drivers like PostgreSQL need to receive sequence name to get ID because PDO doesn't support this method.
     */
    public function lastInsertId(string $sequence = null): string
    {
        return $this->connection()->lastInsertId($sequence);
    }

    public function getDatabasePlatform(): AbstractPlatform
    {
        return $this->connection->getDatabasePlatform();
    }
}
