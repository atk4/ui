<?php

declare(strict_types=1);

namespace Atk4\Ui\Behat;

use Atk4\Data\Model;
use Atk4\Data\Persistence;
use Doctrine\DBAL\Types\Type;

trait RwDemosContextTrait
{
    protected string $demosDir = __DIR__ . '/../../demos';

    protected bool $needDatabaseRestore = false;

    /** @var list<string> */
    protected array $databaseBackupTables = [
        'client',
        'country',
        'file',
        'stat',
        'product_category',
        'product_sub_category',
        'product',
        'multiline_item',
        'multiline_delivery',
    ];

    /** @var array<string, Model>|null */
    protected ?array $databaseBackupModels = null;

    /** @var array<string, array<int, array<string, mixed>>>|null */
    protected ?array $databaseBackupData = null;

    protected function getDemosDb(): Persistence\Sql
    {
        static $db = null;
        if ($db === null) {
            try {
                /** @var Persistence\Sql $db */
                require_once $this->demosDir . '/init-db.php'; // @phpstan-ignore-line
            } catch (\Throwable $e) {
                throw new \Exception('Database error: ' . $e->getMessage());
            }
        }

        return $db;
    }

    protected function createDatabaseModelFromTable(string $table): Model
    {
        $db = $this->getDemosDb();
        $schemaManager = $db->getConnection()->createSchemaManager();
        $tableColumns = $schemaManager->listTableColumns($table);

        $model = new Model($db, ['table' => $table]);
        $model->removeField('id');
        foreach ($tableColumns as $tableColumn) {
            $model->addField($tableColumn->getName(), [
                'type' => Type::getTypeRegistry()->lookupName($tableColumn->getType()), // TODO simplify once https://github.com/doctrine/dbal/pull/6130 is merged
                'nullable' => !$tableColumn->getNotnull(),
            ]);
        }
        $model->idField = array_key_first($model->getFields());

        return $model;
    }

    protected function createDatabaseModels(): void
    {
        $modelByTable = [];
        foreach ($this->databaseBackupTables as $table) {
            $modelByTable[$table] = $this->createDatabaseModelFromTable($table);
        }

        $this->databaseBackupModels = $modelByTable;
    }

    protected function createDatabaseBackup(): void
    {
        $dataByTable = [];
        foreach ($this->databaseBackupTables as $table) {
            $model = $this->databaseBackupModels[$table];

            $data = [];
            foreach ($model as $entity) {
                $data[$entity->getId()] = $entity->get();
            }

            $dataByTable[$table] = $data;
        }

        $this->databaseBackupData = $dataByTable;
    }

    /**
     * @return array<string, \stdClass&object{ addedIds: list<int>, changedIds: list<int>, deletedIds: list<int> }>
     */
    protected function discoverDatabaseChanges(): array
    {
        $changesByTable = [];
        foreach ($this->databaseBackupTables as $table) {
            $model = $this->databaseBackupModels[$table];
            $data = $this->databaseBackupData[$table];

            $changes = new \stdClass();
            $changes->addedIds = [];
            $changes->changedIds = [];
            $changes->deletedIds = array_fill_keys(array_keys($data), true);
            foreach ($model as $entity) {
                $id = $entity->getId();
                if (!isset($data[$id])) {
                    $changes->addedIds[] = $id;
                } else {
                    $isChanged = false;
                    foreach ($data[$id] as $k => $v) {
                        if (!$entity->compare($k, $v)) {
                            $isChanged = true;

                            break;
                        }
                    }

                    if ($isChanged) {
                        $changes->changedIds[] = $id;
                    }

                    unset($changes->deletedIds[$id]);
                }
            }
            $changes->deletedIds = array_keys($changes->deletedIds);

            if (count($changes->addedIds) > 0 || count($changes->changedIds) > 0 || count($changes->deletedIds) > 0) {
                $changesByTable[$table] = $changes;
            }
        }

        return $changesByTable; // @phpstan-ignore-line https://github.com/phpstan/phpstan/issues/9252
    }

    protected function restoreDatabaseBackup(): void
    {
        $changesByTable = $this->discoverDatabaseChanges();

        if (count($changesByTable) > 0) {
            // TODO disable FK checks
            // unfortunately there is no DBAL API - https://github.com/doctrine/dbal/pull/2620
            try {
                $this->getDemosDb()->atomic(function () use ($changesByTable) {
                    foreach ($changesByTable as $table => $changes) {
                        $model = $this->databaseBackupModels[$table];
                        $data = $this->databaseBackupData[$table];

                        foreach ($changes->addedIds as $id) {
                            $model->delete($id);
                        }

                        foreach ([...$changes->changedIds, ...$changes->deletedIds] as $id) {
                            $entity = in_array($id, $changes->changedIds, true) ? $model->load($id) : $model->createEntity();
                            $entity->setMulti($data[$id]);
                            $entity->save();
                        }
                    }
                });
            } finally {
                // TODO enable FK checks
            }
        }
    }

    /**
     * @AfterScenario
     */
    public function restoreDatabase(): void
    {
        if ($this->needDatabaseRestore) {
            $this->needDatabaseRestore = false;
            unlink($this->demosDir . '/db-behat-rw.txt');

            $this->restoreDatabaseBackup();
        }
    }

    /**
     * @When I persist DB changes across requests
     */
    public function iPersistDbChangesAcrossRequests(): void
    {
        if ($this->databaseBackupData === null) {
            if (file_exists($this->demosDir . '/db-behat-rw.txt')) {
                throw new \Exception('Database was not restored cleanly');
            }

            $this->createDatabaseModels();
            $this->createDatabaseBackup();
        }

        $this->needDatabaseRestore = true;
        file_put_contents($this->demosDir . '/db-behat-rw.txt', '');
    }
}
