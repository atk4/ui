<?php

declare(strict_types=1);

namespace Atk4\Data\Persistence\Sql\Mssql;

use Doctrine\DBAL\Result as DbalResult;

trait ExpressionTrait
{
    protected function escapeIdentifier(string $value): string
    {
        return $this->fixOpenEscapeChar(parent::escapeIdentifier($value));
    }

    protected function escapeIdentifierSoft(string $value): string
    {
        return $this->fixOpenEscapeChar(parent::escapeIdentifierSoft($value));
    }

    private function fixOpenEscapeChar(string $v): string
    {
        return preg_replace('~(?:\'(?:\'\'|\\\\\'|[^\'])*\')?+\K\]([^\[\]\'"(){}]*?)\]~s', '[$1]', $v);
    }

    // {{{ MSSQL does not support named parameters, so convert them to numerical inside execute

    /** @var array|null */
    private $numQueryParamsBackup;
    /** @var string|null */
    private $numQueryRender;

    /**
     * @return DbalResult|\PDOStatement PDOStatement iff for DBAL 2.x
     */
    public function execute(object $connection = null): object
    {
        if ($this->numQueryParamsBackup !== null) {
            return parent::execute($connection);
        }

        $this->numQueryParamsBackup = $this->params;
        try {
            $numParams = [];
            $i = 0;
            $j = 0;
            $this->numQueryRender = preg_replace_callback(
                '~(?:\'(?:\'\'|\\\\\'|[^\'])*\')?+\K(?:\?|:\w+)~s',
                function ($matches) use (&$numParams, &$i, &$j) {
                    $numParams[++$i] = $this->params[$matches[0] === '?' ? ++$j : $matches[0]];

                    return '?';
                },
                parent::render()
            );
            $this->params = $numParams;

            return parent::execute($connection);
        } finally {
            $this->params = $this->numQueryParamsBackup;
            $this->numQueryParamsBackup = null;
            $this->numQueryRender = null;
        }
    }

    public function render(): string
    {
        if ($this->numQueryParamsBackup !== null) {
            return $this->numQueryRender;
        }

        return parent::render();
    }

    public function getDebugQuery(): string
    {
        if ($this->numQueryParamsBackup === null) {
            return parent::getDebugQuery();
        }

        $paramsBackup = $this->params;
        $numQueryRenderBackupBackup = $this->numQueryParamsBackup;
        $numQueryRenderBackup = $this->numQueryRender;
        try {
            $this->params = $this->numQueryParamsBackup;
            $this->numQueryParamsBackup = null;
            $this->numQueryRender = null;

            return parent::getDebugQuery();
        } finally {
            $this->params = $paramsBackup;
            $this->numQueryParamsBackup = $numQueryRenderBackupBackup;
            $this->numQueryRender = $numQueryRenderBackup;
        }
    }

    /// }}}
}
