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
	private ConnectionInterface $link;
	private \mysqli_result | bool $result;

	/**
	 * @param Connection $link
	 * @param \mysqli_result|true $result
	 */
	public function __construct(ConnectionInterface $link, mixed $result)
	{
		assert($link->isConnected());
		assert($result instanceof \mysqli_result || $result === true);

		$this->link = $link;
		$this->result = $result;
	}

	public function getAffectedRows() : int
	{
		return $this->link->getLink()->affected_rows;
	}

	public function count() : int
	{
		if ($this->result === true) {
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
		assert($this->result instanceof \mysqli_result);
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
		assert($this->result instanceof \mysqli_result);
		assert($this->result->num_rows > 0);

		return $this->result->fetch_assoc();
	}

	public function fetchObject(string $className, array $parameters = null) : object
	{
		assert($this->result instanceof \mysqli_result);
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