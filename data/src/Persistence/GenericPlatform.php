<?php

declare(strict_types=1);

namespace Atk4\Data\Persistence;

use Doctrine\DBAL\Exception as DbalException;
use Doctrine\DBAL\Platforms;

class GenericPlatform extends Platforms\AbstractPlatform
{
    private function createNotSupportedException(): \Exception // DbalException once DBAL 2.x support is dropped
    {
        if (\Atk4\Data\Persistence\Sql\Connection::isComposerDbal2x()) {
            // hack for PHPStan, keep ignored error count for DBAL 2.x and DBAL 3.x the same
            if (\PHP_MAJOR_VERSION === 0) {
                \Doctrine\DBAL\DBALException::notSupported('SQL');
                \Doctrine\DBAL\DBALException::notSupported('SQL');
                \Doctrine\DBAL\DBALException::notSupported('SQL');
                \Doctrine\DBAL\DBALException::notSupported('SQL');
                \Doctrine\DBAL\DBALException::notSupported('SQL');
                \Doctrine\DBAL\DBALException::notSupported('SQL');
                \Doctrine\DBAL\DBALException::notSupported('SQL');
            }

            return \Doctrine\DBAL\DBALException::notSupported('SQL');
        }

        return DbalException::notSupported('SQL');
    }

    public function getName(): string
    {
        return 'atk4_data_generic';
    }

    protected function initializeDoctrineTypeMappings(): void
    {
    }

    protected function _getCommonIntegerTypeDeclarationSQL(array $columnDef): string
    {
        throw $this->createNotSupportedException();
    }

    public function getBigIntTypeDeclarationSQL(array $columnDef): string
    {
        throw $this->createNotSupportedException();
    }

    public function getBlobTypeDeclarationSQL(array $field): string
    {
        throw $this->createNotSupportedException();
    }

    public function getBooleanTypeDeclarationSQL(array $columnDef): string
    {
        throw $this->createNotSupportedException();
    }

    public function getClobTypeDeclarationSQL(array $field): string
    {
        throw $this->createNotSupportedException();
    }

    public function getIntegerTypeDeclarationSQL(array $columnDef): string
    {
        throw $this->createNotSupportedException();
    }

    public function getSmallIntTypeDeclarationSQL(array $columnDef): string
    {
        throw $this->createNotSupportedException();
    }

    public function getCurrentDatabaseExpression(): string
    {
        throw $this->createNotSupportedException();
    }
}
