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

use Tuxxedo\AssertionException;

interface ConnectionInterface
{
	/**
	 * @param array<string, mixed> $options
	 *
	 * @throws AssertionException
	 */
	public function __construct(array $options);

	public function hasOption(string $name) : bool;

	/**
	 * @throws AssertionException
	 */
	public function setOption(string $name, mixed $value) : void;

	/**
	 * @throws AssertionException
	 */
	public function getOption(string $name) : mixed;

	/**
	 * @param array<string, mixed> $options
	 * @return void
	 *
	 * @throws AssertionException
	 */
	public function setOptions(array $options) : void;

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