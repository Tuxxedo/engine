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

		$stmt = $link->prepare($this->sql);

		if (!$stmt) {
			throw new QueryException(
				$link->errno,
				$link->error,
				$this->sql,
			);
		}

		foreach ($this->bindings as $value) {
			$stmt->bind_param(
				self::getParameterModifier($value),
				$value,
			);
		}

		if (!$stmt->execute()) {
			throw new QueryException(
				$link->errno,
				$link->error,
				$this->sql,
			);
		}

		assert($this->isExecuted = true);

		return new Result(
			$this->link,
			$stmt,
		);
	}

	private static function getParameterModifier(string | float | int $value) : string
	{
		$type = \gettype($value);

		assert($type === 'string' || $type === 'integer' || $type === 'float');

		return match($type) {
			'string' => 's',
			'float' => 'f',
			'integer' => 'i',
		};
	}
}