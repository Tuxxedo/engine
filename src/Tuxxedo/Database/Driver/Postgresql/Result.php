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

namespace Tuxxedo\Database\Driver\Postgresql;

use Tuxxedo\Database\ConnectionInterface;
use Tuxxedo\Database\ResultInterface;
use Tuxxedo\Database\ResultRow;

class Result implements ResultInterface
{
	/**
	 * @var resource<mixed>
	 */
	private ?\mixed $result = null;

	private int $iteratorPosition = 0;
	private int $affectedRows = 0;

	/**
	 * @param Connection $link
	 * @param mixed $result
	 */
	public function __construct(ConnectionInterface $link, mixed $result)
	{
		assert($link->isConnected());
		assert($link instanceof Connection);
		assert(\is_resource($result));

		$this->affectedRows = \pg_affected_rows($result);

		$this->result = $result;
	}

	public function getAffectedRows() : int
	{
		return $this->affectedRows;
	}

	public function rewind() : void
	{
		assert($this->result !== null);
		assert($this->result->num_rows > 0);

		\pg_result_seek($this->result, 0);
	}

	public function valid() : bool
	{
		assert($this->result !== null);

		return $this->iteratorPosition < $this->result->num_rows;
	}

	public function next() : void
	{
		assert($this->result !== null);

		$this->iteratorPosition++;
	}

	public function key() : int
	{
		assert($this->result !== null);

		return $this->iteratorPosition;
	}

	/**
	 * @return object|null
	 */
	public function current() : mixed
	{
		assert($this->result !== null);

		\pg_result_seek(
			$this->result,
			$this->iteratorPosition
		);

		return $this->fetch();
	}

	public function count() : int
	{
		if ($this->result === null) {
			return 0;
		}

		return $this->result->num_rows;
	}

	/**
	 * @return object|null
	 */
	public function fetch() : ?object
	{
		return $this->fetchObject(ResultRow::class);
	}

	/**
	 * @return array<mixed>|null
	 */
	public function fetchNum() : ?array
	{
		assert($this->result !== null);
		assert($this->result->num_rows > 0);

		return \pg_fetch_array($this->result, null, \ PGSQL_NUM) ?: null;
	}

	/**
	 * @return array<string, mixed>|null
	 */
	public function fetchAssoc() : ?array
	{
		assert($this->result !== null);
		assert($this->result->num_rows > 0);

		return \pg_fetch_assoc($this->result, null) ?: null;
	}

	/**
	 * @return object|null
	 */
	public function fetchObject(string $className, array $parameters = null) : ?object
	{
		assert($this->result !== null);
		assert($this->result->num_rows > 0);

		return \pg_fetch_object($this->result, null, $className, $parameters) ?: null;
	}
}