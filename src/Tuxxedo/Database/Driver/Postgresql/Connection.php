<?php

/**
 * ^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^
 * Tuxxedo Engine
 * ^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^
 *
 * @copyright   2006-2020 DColt <dev@dcolt.org>
 * @license     MIT
 *
 * ^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^
 */

declare(strict_types=1);

namespace Tuxxedo\Database\Driver\Postgresql;

use Tuxxedo\Database\ConnectionException;
use Tuxxedo\Database\ConnectionInterface;
use Tuxxedo\Database\ConnectionOptionsTrait;

class Connection implements ConnectionInterface
{
    use ConnectionOptionsTrait;

    public const OPTION_HOST = 'host';
    public const OPTION_HOST_ADDRESS = 'hostaddr';
    public const OPTION_PORT = 'port';
    public const OPTION_DATABASE = 'dbname';
    public const OPTION_USERNAME = 'user';
    public const OPTION_PASSWORD = 'password';
    public const OPTION_TIMEOUT = 'connect_timeout';
    public const OPTION_CLIENT_ENCODING = 'client_encoding';
    public const OPTION_OPTIONS = 'options';
    public const OPTION_APPLICATION_NAME = 'application_name';
    public const OPTION_KEEPALIVES = 'keepalives';
    public const OPTION_KEEPALIVES_IDLE = 'keepalives_idle';
    public const OPTION_KEEPALIVES_INTERVAL = 'keepalives_interval';
    public const OPTION_KEEPALIVES_COUNT = 'keepalives_count';
    public const OPTION_SSLMODE = 'sslmode';
    public const OPTION_SSLCOMPRESSION = 'sslcompression';
    public const OPTION_SSLCERT = 'sslcert';
    public const OPTION_SSLKEY = 'sslkey';
    public const OPTION_SSLROOTCERT = 'sslrootcert';
    public const OPTION_SSLCRL = 'sslcrl';
    public const OPTION_REQUIREPEER = 'requirepeer';
    public const OPTION_KRBSRVNAME = 'krbsrvname';
    public const OPTION_GSSLIB = 'gsslib';
    public const OPTION_SERVICE = 'service';
    public const OPTION_TARGET_SESSION_ATTRS = 'target_session_attrs';
    public const OPTION_PERSISTENT = 'persistent';

    public const SSLMODE_DISABLE = 'disable';
    public const SSLMODE_ALLOW = 'allow';
    public const SSLMODE_PREFER = 'prefer';
    public const SSLMODE_REQUIRE = 'require';
    public const SSLMODE_VERIFY_CA = 'verify-ca';
    public const SSLMODE_VERIFY_FULL = 'verify-full';

    /**
     * @var resource|null
     */
    private $link = null;

    /**
     * @var array<string, mixed>
     */
    private array $options = [
        self::OPTION_HOST => '',
        self::OPTION_HOST_ADDRESS => '',
        self::OPTION_PORT => 5432,
        self::OPTION_DATABASE => '',
        self::OPTION_USERNAME => '',
        self::OPTION_PASSWORD => '',
        self::OPTION_TIMEOUT => 0,
        self::OPTION_CLIENT_ENCODING => '',
        self::OPTION_OPTIONS => '',
        self::OPTION_APPLICATION_NAME => 'Tuxxedo Engine',
        self::OPTION_KEEPALIVES => 1,
        self::OPTION_KEEPALIVES_IDLE => 0,
        self::OPTION_KEEPALIVES_INTERVAL => 0,
        self::OPTION_KEEPALIVES_COUNT => 0,
        self::OPTION_SSLMODE => self::SSLMODE_PREFER,
        self::OPTION_SSLCOMPRESSION => 1,
        self::OPTION_SSLCERT => '',
        self::OPTION_SSLKEY => '',
        self::OPTION_SSLROOTCERT => '',
        self::OPTION_SSLCRL => '',
        self::OPTION_REQUIREPEER => '',
        self::OPTION_KRBSRVNAME => '',
        self::OPTION_GSSLIB => '',
        self::OPTION_SERVICE => '',
        self::OPTION_TARGET_SESSION_ATTRS => '',
        self::OPTION_PERSISTENT => false,
    ];

    public function __construct(array $options)
    {
        $this->setOptions($options);
    }

    private function createConnectionString(): string
    {
        $connectionString = '';

        foreach ($this->options as $key => $val) {
            if ($key === self::OPTION_PERSISTENT) {
                continue;
            }

            if ($val !== '' && $val !== null) {
                if (!empty($connectionString)) {
                    $connectionString .= ' ';
                }

                $connectionString .= $key . '=' . $val;
            }
        }

        return $connectionString;
    }

    /**
     * @throws ConnectionException
     */
    private function getInternalLink()
    {
        if ($this->link !== null) {
            return $this->link;
        }

        $connectionString = $this->createConnectionString();

        if ($this->options[self::OPTION_PERSISTENT]) {
            $link = \pg_pconnect($connectionString);
        } else {
            $link = \pg_connect($connectionString);
        }

        if (\pg_connection_status($link) !== \PGSQL_CONNECTION_OK) {
            throw new ConnectionException(
                0,
                \pg_last_error($link)
            );
        }

        $this->link = $link;

        return $link;
    }

    public function getLink()
    {
        return $this->link;
    }

    public function isConnected(): bool
    {
        return \is_resource($this->link) && \pg_connection_status($this->link) === \PGSQL_CONNECTION_OK;
    }
}
