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

/**
 * @extends \Iterator<object>
 */
interface ResultInterface extends \Iterator, \Countable
{
	public function __construct(ConnectionInterface $link, mixed $stmt);

	public function getAffectedRows() : int;

	public function fetch() : ?ResultRow;

	/**
	 * @return array<int, mixed>|null
	 */
	public function fetchNum() : ?array;

	/**
	 * @return array<string | int, mixed>|null
	 */
	public function fetchAssoc() : ?array;

	/**
	 * @param array<int, mixed> $parameters
	 */
	public function fetchObject(string $className, array $parameters = null) : ?object;
}