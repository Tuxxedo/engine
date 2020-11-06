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

use Tuxxedo\Database\ConnectionInterface;
use Tuxxedo\Database\ResultInterface;
use Tuxxedo\Database\ResultRow;

class Result implements ResultInterface
{
	private ?\mysqli_result $result = null;

	private int $iteratorPosition = 0;
	private int $affectedRows = 0;

	/**
	 * @param Connection $link
	 * @param \mysqli_stmt|true $result
	 */
	public function __construct(ConnectionInterface $link, mixed $stmt)
	{
		assert($link->isConnected());
		assert($link instanceof Connection);
		assert($stmt instanceof \mysqli_stmt);

		$this->affectedRows = $stmt->affected_rows;

		if ($result = $stmt->get_result()) {
			$this->result = $result;
		}

		assert($stmt->close());
	}

	public function getAffectedRows() : int
	{
		return $this->affectedRows;
	}

	public function rewind() : void
	{
		assert($this->result !== null);
		assert($this->result->num_rows > 0);

		$this->result->data_seek(0);
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

	public function current() : mixed
	{
		assert($this->result !== null);

		$this->result->data_seek(
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
	 * @return ResultRow
	 */
	public function fetch() : object
	{
		return $this->fetchObject(
			ResultRow::class
		);
	}

	/**
	 * @return array<int, mixed>
	 */
	public function fetchArray() : array
	{
		assert($this->result !== null);
		assert($this->result->num_rows > 0);

		return $this->result->fetch_array(
			\MYSQLI_NUM
		);
	}

	/**
	 * @return array<string, mixed>
	 */
	public function fetchAssoc() : array
	{
		assert($this->result !== null);
		assert($this->result->num_rows > 0);

		return $this->result->fetch_assoc();
	}

	public function fetchObject(string $className, array $parameters = null) : object
	{
		assert($this->result !== null);
		assert($this->result->num_rows > 0);

		if ($parameters !== null) {
			return $this->result->fetch_object(
				$className,
				$parameters
			);
		}

		return $this->result->fetch_object(
			$className
		);
	}
}