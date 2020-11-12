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
use Tuxxedo\Database\QueryException;
use Tuxxedo\Database\ResultInterface;
use Tuxxedo\Database\StatementInterface;

class Statement implements StatementInterface
{
	private string $sql;
	private Connection $link;

	private bool $isExecuted = false;

	/**
	 * @var array<string, string | float | int>
	 */
	private array $bindings = [];

	public function __construct(ConnectionInterface $link, string $sql)
	{
		assert($link->isConnected());
		assert($link instanceof Connection);

		$this->link = $link;
		$this->sql = $sql;
	}

	public function bindOne(string $varname, string | float | int $value) : void
	{
		assert(!$this->isExecuted || !isset($this->bindings[$varname]));

		$this->bindings[$varname] = $value;
	}

	public function bind(string | int | float ...$values) : void
	{
		/** @var string $varname */
		foreach ($values as $varname => $value) {
			assert(!$this->isExecuted && !isset($this->bindings[$varname]));
			assert(\is_string($varname));

			$this->bindings[$varname] = $value;
		}
	}

	public function execute() : ResultInterface
	{
		$link = $this->link->getLink();

		assert($link !== null);

		$result = \pg_query_params($link, $this->sql, $this->bindings);

		if (!$result) {
			throw new QueryException(
				-1,
                \pg_last_error($link),
				$sql,
			);
		}

		assert($this->isExecuted = true);

		return new Result(
			$this->link,
			$result,
		);
	}
}