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

namespace Tuxxedo\Database;

interface ConnectionInterface
{
	/**
	 * @param array<string, mixed>|object $options
	 */
	public function __construct(array | object $options);

	public function hasOption(string $name) : bool;

	public function setOption(string $name, mixed $value) : void;

	public function getOption(string $name) : mixed;

	/**
	 * @param array<string, mixed>|iterable<object> $options
	 * @return void
	 */
	public function setOptions(array | object $options) : void;

	/**
	 * @return array<string, mixed>
	 */
	public function getOptions() : array;

	public function getLink() : mixed;

	public function isConnected() : bool;

	public function ping() : bool;

	/**
	 * @throws ConnectionException
	 */
	public function getInsertId() : int;

	/**
	 * @throws ConnectionException
	 */
	public function escape(string $input) : string;

	/**
	 * @throws ConnectionException
	 * @throws QueryException
	 */
	public function prepare(string $sql) : StatementInterface;

	/**
	 * @throws ConnectionException
	 * @throws QueryException
	 */
	public function query(string $sql) : ResultInterface;
}