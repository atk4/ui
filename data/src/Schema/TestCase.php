<?php

declare(strict_types=1);

namespace Atk4\Data\Schema;

use Atk4\Core\Phpunit\TestCase as BaseTestCase;
use Atk4\Data\Model;
use Atk4\Data\Persistence;
use Doctrine\DBAL\Logging\SQLLogger;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Schema\AbstractSchemaManager;

class TestCase extends BaseTestCase
{
    /** @var Persistence|Persistence\Sql Persistence instance */
    public $db;

    /** @var array Array of database table names */
    public $tables;

    /** @var bool Debug mode enabled/disabled. In debug mode SQL queries are dumped. */
    public $debug = false;

    /**
     * Setup test database.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // establish connection
        $dsn = $GLOBALS['DB_DSN'] ?? 'sqlite::memory:';
        $user = $GLOBALS['DB_USER'] ?? null;
        $pass = $GLOBALS['DB_PASSWD'] ?? null;

        $this->db = Persistence::connect($dsn, $user, $pass);

        // reset DB autoincrement to 1, tests rely on it
        if ($this->getDatabasePlatform() instanceof MySQLPlatform) {
            $this->db->connection->expr('SET @@auto_increment_offset=1, @@auto_increment_increment=1')->execute();
        }

        if ($this->debug) {
            $this->db->connection->connection()->getConfiguration()->setSQLLogger(
                new class($this) implements SQLLogger {
                    /** @var TestCase */
                    public $testCase;

                    public function __construct(TestCase $testCase)
                    {
                        $this->testCase = $testCase;
                    }

                    public function startQuery($sql, $params = null, $types = null): void
                    {
                        if (!$this->testCase->debug) {
                            return;
                        }

                        echo "\n" . $sql . "\n" . print_r($params, true) . "\n\n";
                    }

                    public function stopQuery(): void
                    {
                    }
                }
            );
        }
    }

    protected function getDatabasePlatform(): AbstractPlatform
    {
        return $this->db->connection->getDatabasePlatform();
    }

    protected function getSchemaManager(): AbstractSchemaManager
    {
        return $this->db->connection->connection()->getSchemaManager();
    }

    private function convertSqlFromSqlite(string $sql): string
    {
        return preg_replace_callback(
            '~\'(?:[^\'\\\\]+|\\\\.)*\'|"(?:[^"\\\\]+|\\\\.)*"~s',
            function ($matches) {
                $str = substr(preg_replace('~\\\\(.)~s', '$1', $matches[0]), 1, -1);
                if (substr($matches[0], 0, 1) === '"') {
                    return $this->getDatabasePlatform()->quoteSingleIdentifier($str);
                }

                return $this->getDatabasePlatform()->quoteStringLiteral($str);
            },
            $sql
        );
    }

    protected function assertSameSql(string $expectedSqliteSql, string $actualSql, string $message = ''): void
    {
        $this->assertSame($this->convertSqlFromSqlite($expectedSqliteSql), $actualSql, $message);
    }

    public function createMigrator(Model $model = null): Migration
    {
        return new Migration($model ?: $this->db);
    }

    /**
     * Use this method to clean up tables after you have created them,
     * so that your database would be ready for the next test.
     */
    public function dropTableIfExists(string $tableName): self
    {
        // we cannot use SchemaManager::dropTable directly because of
        // our custom Oracle sequence for PK/AI
        $this->createMigrator()->table($tableName)->dropIfExists();

        return $this;
    }

    /**
     * Sets database into a specific test.
     */
    public function setDb(array $dbData, bool $importData = true): void
    {
        $this->tables = array_keys($dbData);

        // create tables
        foreach ($dbData as $tableName => $data) {
            $this->dropTableIfExists($tableName);

            $first_row = current($data);
            if ($first_row) {
                $migrator = $this->createMigrator()->table($tableName);

                $migrator->id('id');

                foreach ($first_row as $field => $row) {
                    if ($field === 'id') {
                        continue;
                    }

                    if (is_int($row)) {
                        $fieldType = 'integer';
                    } elseif (is_float($row)) {
                        $fieldType = 'float';
                    } elseif ($row instanceof \DateTimeInterface) {
                        $fieldType = 'datetime';
                    } else {
                        $fieldType = 'string';
                    }

                    $migrator->field($field, ['type' => $fieldType]);
                }

                $migrator->create();
            }

            // import data
            if ($importData) {
                $hasId = (bool) key($data);

                foreach ($data as $id => $row) {
                    $query = $this->db->dsql();
                    if ($id === '_') {
                        continue;
                    }

                    $query->table($tableName);
                    $query->set($row);

                    if (!isset($row['id']) && $hasId) {
                        $query->set('id', $id);
                    }

                    $query->insert();
                }
            }
        }
    }

    /**
     * Return database data.
     */
    public function getDb(array $tableNames = null, bool $noId = false): array
    {
        if ($tableNames === null) {
            $tableNames = $this->tables;
        }

        $ret = [];

        foreach ($tableNames as $table) {
            $data2 = [];

            $s = $this->db->dsql();
            $data = $s->table($table)->getRows();

            foreach ($data as &$row) {
                foreach ($row as &$val) {
                    if (is_int($val)) {
                        $val = (int) $val;
                    }
                }

                if ($noId) {
                    unset($row['id']);
                    $data2[] = $row;
                } else {
                    $data2[$row['id']] = $row;
                }
            }

            $ret[$table] = $data2;
        }

        return $ret;
    }
}
