<?php

declare(strict_types=1);

namespace Atk4\Data\Persistence\Sql\Oracle;

use Atk4\Data\Persistence\Sql\Connection as BaseConnection;
use Doctrine\Common\EventManager;
use Doctrine\DBAL\Event\Listeners\OracleSessionInit;

class Connection extends BaseConnection
{
    protected $query_class = Query::class;

    protected static function createDbalEventManager(): EventManager
    {
        $evm = new EventManager();

        // setup connection globalization to use standard datetime format incl. microseconds support
        $dateFormat = 'YYYY-MM-DD';
        $timeFormat = 'HH24:MI:SS.FF6';
        $tzFormat = 'TZH:TZM';
        $evm->addEventSubscriber(new OracleSessionInit([
            'NLS_DATE_FORMAT' => $dateFormat,
            'NLS_TIME_FORMAT' => $timeFormat,
            'NLS_TIMESTAMP_FORMAT' => $dateFormat . ' ' . $timeFormat,
            'NLS_TIME_TZ_FORMAT' => $timeFormat . ' ' . $tzFormat,
            'NLS_TIMESTAMP_TZ_FORMAT' => $dateFormat . ' ' . $timeFormat . ' ' . $tzFormat,
        ]));

        return $evm;
    }

    // {{{ fix for too many connections for CI testing

    /** @var int */
    private static $ciDifferentDsnCounter = 0;
    /** @var array */
    private static $ciLastConnectDsn;
    /** @var \PDO|null */
    private static $ciLastConnectPdo;

    protected static function connectDbalConnection(array $dsn)
    {
        // for some reasons, the following error:
        // PDOException: SQLSTATE[HY000]: pdo_oci_handle_factory: ORA-12516: TNS:listener could not find available handler with matching protocol stack
        // is shown randomly when a lot of connections are created in tests,
        // so for CI, fix this issue by reusing the previous PDO connection
        // TODO remove once atk4/data tests can be run consistently without errors
        if (class_exists(\PHPUnit\Framework\TestCase::class, false)) { // called from phpunit
            $notReusableFunc = function (string $message): void {
                echo "\n" . 'connection for CI cannot be reused:' . "\n" . $message . "\n";
                self::$ciLastConnectPdo = null;
            };

            if (self::$ciLastConnectDsn !== $dsn) {
                ++self::$ciDifferentDsnCounter;
                if (self::$ciDifferentDsnCounter >= 4) {
                    $notReusableFunc('different DSN');
                }
            } elseif (self::$ciLastConnectPdo !== null) {
                try {
                    self::$ciLastConnectPdo->query('select 1 from dual')->fetch();
                } catch (\PDOException $e) {
                    $notReusableFunc((string) $e);
                }
            }

            if (self::$ciLastConnectPdo !== null && self::$ciLastConnectPdo->inTransaction()) {
                $notReusableFunc('inside transaction');
            }

            if (self::$ciLastConnectPdo !== null) {
                $dbalConnection = parent::connectDbalConnection(['pdo' => self::$ciLastConnectPdo]);
            } else {
                $dbalConnection = parent::connectDbalConnection($dsn);
            }

            if (BaseConnection::isComposerDbal2x()) {
                self::$ciLastConnectPdo = $dbalConnection->getWrappedConnection(); // @phpstan-ignore-line
            } else {
                self::$ciLastConnectPdo = $dbalConnection->getWrappedConnection()->getWrappedConnection(); // @phpstan-ignore-line
            }
            self::$ciLastConnectDsn = $dsn;

            return $dbalConnection;
        }

        return parent::connectDbalConnection($dsn);
    }

    /// }}}

    /**
     * Return last inserted ID value.
     *
     * Drivers like PostgreSQL need to receive sequence name to get ID because PDO doesn't support this method.
     */
    public function lastInsertId(string $sequence = null): string
    {
        if ($sequence) {
            /** @var AbstractQuery */
            $query = $this->dsql()->mode('seq_currval');

            return $query->sequence($sequence)->getOne();
        }

        // fallback
        return parent::lastInsertId($sequence);
    }
}
