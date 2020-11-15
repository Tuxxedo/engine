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

declare(strict_types = 1);

namespace Tuxxedo\Database\Driver\Postgresql;

use Tuxxedo\Database\ConnectionException;
use Tuxxedo\Database\ConnectionInterface;
use Tuxxedo\Database\ConnectionOptionsTrait;
use Tuxxedo\Database\QueryException;
use Tuxxedo\Database\ResultInterface;
use Tuxxedo\Database\StatementInterface;

class Connection implements ConnectionInterface
{
    use ConnectionOptionsTrait;

    public const OPTION_HOST = 'host';
    public const OPTION_USERNAME = 'user';
    public const OPTION_PASSWORD = 'password';
    public const OPTION_DATABASE = 'dbname';
    public const OPTION_TIMEOUT = 'connect_timeout';
    public const OPTION_PORT = 'port';
    public const OPTION_PERSISTENT = 'persistent';
    public const OPTION_SSL = 'ssl';
    public const OPTION_CLIENT_ENCODING = 'encoding';
    public const OPTION_APPLICATION_NAME = 'application_name';

    /**
     * @var resource|null
     */
    private mixed $link = null;

    /**
     * @var array<string, mixed>
     */
    private array $options = [
        self::OPTION_HOST => '',
        self::OPTION_USERNAME => '',
        self::OPTION_PASSWORD => '',
        self::OPTION_DATABASE => '',
        self::OPTION_TIMEOUT => 3,
        self::OPTION_PORT => 5432,
        self::OPTION_PERSISTENT => false,
	self::OPTION_SSL => false,
        self::OPTION_CLIENT_ENCODING => '',
        self::OPTION_APPLICATION_NAME => 'Tuxxedo Engine',
    ];

	/**
	 * @param array<string, mixed>|iterable<object> $options
	 */
    public function __construct(array | object $options)
    {
        $this->setOptions($options);
    }

    private function getConnectionOption(string $key, mixed $val) : string
    {
        if ($key === self::OPTION_CLIENT_ENCODING) {
            return 'client_encoding=' . $val;
        }

        if ($key === self::OPTION_SSL) {
            return 'sslmode=' . ($val ? 'require' : 'disable');
        }

        if (\str_contains((string) $val, ' ')) {
        	$val = \sprintf('\'%s\'', $val);
	}

        return $key . '=' . $val;
    }

    private function createConnectionString() : string
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

                $connectionString .= $this->getConnectionOption($key, $val);
            }
        }

        return $connectionString;
    }

    /**
     * @throws ConnectionException
     */
    private function getInternalLink() : mixed
    {
        if ($this->link !== null) {
            return $this->link;
        }

        $connectionString = $this->createConnectionString();

        if ($this->options[self::OPTION_PERSISTENT]) {
            $link = @\pg_pconnect($connectionString);
        } else {
            $link = @\pg_connect($connectionString);
        }

        if (!$link) {
            throw new ConnectionException(
                -1,
                'Failed to connect'
            );
        }

        if (\pg_connection_status($link) !== \PGSQL_CONNECTION_OK) {
            throw new ConnectionException(
                -1,
                \pg_last_error($link)
            );
        }

        $this->link = $link;

        return $link;
    }

    public function getLink() : mixed
    {
        return $this->link;
    }

    public function isConnected() : bool
    {
        return \is_resource($this->link) && \pg_connection_status($this->link) === \PGSQL_CONNECTION_OK;
    }

	public function ping() : bool
	{
		try {
			\pg_ping($this->getInternalLink());
		} catch (ConnectionException) {
			return false;
		}

		return true;
    }
    

	/**
	 * @return int
	 *
	 * @throws ConnectionException
	 */
	public function getInsertId() : int
	{
		return (int) \pg_last_oid($this->getInternalLink());
	}

	/**
	 * @throws ConnectionException
	 */
	public function escape(string $input) : string
	{
		return \pg_escape_string($this->getInternalLink(), $input);
	}

	/**
	 * @param string $sql
	 * @return Statement
	 *
	 * @throws QueryException
	 */
	public function prepare(string $sql) : StatementInterface
	{
		return new Statement(
			$this,
			$sql,
		);
	}

	/**
	 * @param string $sql
	 * @return Result
	 *
	 * @throws ConnectionException
	 * @throws QueryException
	 */
	public function query(string $sql) : ResultInterface
	{
		$link = $this->getInternalLink();
		$query = @\pg_query($link, $sql);

		if (!$query) {
			throw new QueryException(
				-1,
                		\pg_last_error($link),
				$sql,
			);
		}

		return new Result(
			$this,
			$query,
		);
	}
}
