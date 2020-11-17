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
	 * @var resource
	 */
	private mixed $result = null;

	private int $iteratorPosition = 0;
	private int $affectedRows = 0;

	/**
	 * Key is the field name, val is the cast type
	 * @var array<string,string>
	 */
	private array $typeMap = [];

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

	private function createTypeMap(): void {
		assert($this->result !== null);

		$numberOfFields = \pg_num_fields($this->result);
		for ($i = 0; $i < $numberOfFields; $i++) {
			$fieldName = \pg_field_name($this->result, $i);
			$fieldType = match(\pg_field_type($this->result, $i)) {
				'bool'			=> 'bool',
				'int2','int4','int8'	=> 'int',
				'float4','float8'	=> 'float',
				default			=> 'string',
			};
			$this->typeMap[$fieldName] = $fieldType;
		}
	}

	public function getAffectedRows() : int
	{
		return $this->affectedRows;
	}

	public function rewind() : void
	{
		assert($this->result !== null);
		assert(\pg_num_rows($this->result) > 0);

		\pg_result_seek($this->result, 0);
	}

	public function valid() : bool
	{
		assert($this->result !== null);

		return $this->iteratorPosition < \pg_num_rows($this->result);
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

		return \pg_num_rows($this->result);
	}

	/**
	 * @return ResultRow|null
	 */
	public function fetch() : ?ResultRow
	{
		$result = $this->fetchObject(ResultRow::class);

		assert($result === null || $result instanceof ResultRow);
		
		if ($result) {
			if (empty($this->typeMap)) {
				$this->createTypeMap();
			}

			foreach ($this->typeMap as $key => $type) {
				\settype($result->{$key}, $type);
			}
		}

		return $result;
	}

	/**
	 * @return array<mixed>|null
	 */
	public function fetchNum() : ?array
	{
		assert($this->result !== null);
		assert(\pg_num_rows($this->result) > 0);

		/** @var array<string>|false $array */
		$array = \pg_fetch_array($this->result, null, \PGSQL_NUM);

		if ($array) {
			$numberOfFields = \pg_num_fields($this->result);
			for ($i = 0; $i < $numberOfFields; $i++) {
				$fieldType = match(\pg_field_type($this->result, $i)) {
					'bool'			=> 'bool',
					'int2','int4','int8'	=> 'int',
					'float4','float8'	=> 'float',
					default			=> 'string',
				};
				\settype($array[$i], $fieldType);
			}
		}

		return $array ?: null;
	}

	/**
	 * @return array<string, mixed>|null
	 */
	public function fetchAssoc() : ?array
	{
		assert($this->result !== null);
		assert(\pg_num_rows($this->result) > 0);

		/** @var array<string, string>|false $assoc */
		$assoc = \pg_fetch_assoc($this->result, null);

		if ($assoc) {
			if (empty($this->typeMap)) {
				$this->createTypeMap();
			}

			foreach ($this->typeMap as $key => $type) {
				\settype($assoc[$key], $type);
			}
		}

		return $assoc ?: null;
	}

	/**
	 * @return object|null
	 */
	public function fetchObject(string $className, array $parameters = null) : ?object
	{
		assert($this->result !== null);
		assert(\pg_num_rows($this->result) > 0);

		if ($parameters !== null) {
			/** @var object|false $object */
			$object = \pg_fetch_object($this->result, null, $className, $parameters);
		} else {
			/** @var object|false $object */
			$object = \pg_fetch_object($this->result, null, $className);
		}
		
		return $object ?: null;
	}
}