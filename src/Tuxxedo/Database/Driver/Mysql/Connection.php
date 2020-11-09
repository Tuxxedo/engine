<?php
/**
 * ^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^
 * Tuxxedo Engine
 * ^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^
 *
 * @copyright 	2006-2020 Kalle Sommer Nielsen <kalle@tuxxedo.app>
 * @license 	MIT
 *
 * ^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^
 */

declare(strict_types = 1);

namespace Tuxxedo\Database\Driver\Mysql;

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
	public const OPTION_USERNAME = 'username';
	public const OPTION_PASSWORD = 'password';
	public const OPTION_DATABASE = 'database';
	public const OPTION_TIMEOUT = 'timeout';
	public const OPTION_PORT = 'port';
	public const OPTION_SOCKET = 'socket';
	public const OPTION_PERSISTENT = 'persistent';
	public const OPTION_SSL = 'ssl';
	public const OPTION_CLIENT_ENCODING = 'encoding';

	private ?\mysqli $link = null;

	/**
	 * @var array<string, mixed>
	 */
	private array $options = [
		self::OPTION_HOST => '',
		self::OPTION_USERNAME => '',
		self::OPTION_PASSWORD => '',
		self::OPTION_DATABASE => '',
		self::OPTION_TIMEOUT => 3,
		self::OPTION_PORT => 3306,
		self::OPTION_SOCKET => '',
		self::OPTION_PERSISTENT => false,
		self::OPTION_SSL => false,
		self::OPTION_CLIENT_ENCODING => 'UTF-8',
	];

	/**
	 * @param array<string, mixed>|iterable<object> $options
	 */
	public function __construct(array | object $options)
	{
		$this->setOptions($options);
	}

	/**
	 * @throws ConnectionException
	 */
	private function getInternalLink() : \mysqli
	{
		if ($this->link !== null) {
			return $this->link;
		}

		$link = new \mysqli;

		if ($timeout = $this->options[self::OPTION_TIMEOUT]) {
			$link->options(\MYSQLI_OPT_CONNECT_TIMEOUT, $timeout);
		}

		@$link->real_connect(
			($this->options[self::OPTION_PERSISTENT] ? 'p:' : '') . $this->options[self::OPTION_HOST],
			$this->options[self::OPTION_USERNAME],
			$this->options[self::OPTION_PASSWORD],
			$this->options[self::OPTION_DATABASE],
			$this->options[self::OPTION_PORT] ?: null,
			$this->options[self::OPTION_SOCKET] ?: null,
			$this->options[self::OPTION_SSL] ? \MYSQLI_CLIENT_SSL : 0,
		);

		if ($link->connect_errno) {
			throw new ConnectionException(
				$link->connect_errno,
				$link->connect_error
			);
		}

		if ($encoding = $this->options[self::OPTION_CLIENT_ENCODING]) {
			$link->set_charset($encoding);
		}

		if ($link->errno) {
			throw new ConnectionException(
				$link->errno,
				$link->error
			);
		}

		$this->link = $link;

		return $link;
	}

	public function getLink() : ?\mysqli
	{
		return $this->link;
	}

	public function isConnected() : bool
	{
		return $this->link instanceof \mysqli;
	}

	public function ping() : bool
	{
		try {
			$this->getInternalLink()->ping();
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
		return (int) $this->getInternalLink()->insert_id;
	}

	/**
	 * @throws ConnectionException
	 */
	public function escape(string $input) : string
	{
		return $this->getInternalLink()->real_escape_string($input);
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
		$stmt = $link->prepare($sql);

		if (!$stmt || !$stmt->execute()) {
			throw new QueryException(
				$link->errno,
				$link->error,
				$sql,
			);
		}

		return new Result(
			$this,
			$stmt,
		);
	}
}