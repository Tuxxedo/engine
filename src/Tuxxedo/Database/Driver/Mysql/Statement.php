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

	public function bind(string $var, string | float | int $value) : void
	{
		$this->bindings[$var] = $value;
	}

	public function execute() : ResultInterface
	{
		$stmt = $this->link->getLink()->prepare($this->sql);

		if (!$stmt) {
			$link = $this->link->getLink();

			throw new QueryException(
				$link->errno,
				$link->error,
				$this->sql,
			);
		}

		/**
		 * @todo This needs to account for the key ($var) once there is a neutral syntax
		 */
		foreach ($this->bindings as $value) {
			$stmt->bind_param(
				self::getParameterModifier($value),
				$value,
			);
		}

		/**
		 * @todo This needs to handle multiple resultsets returned
		 */
		if (!$stmt->execute()) {
			$link = $this->link->getLink();

			throw new QueryException(
				$link->errno,
				$link->error,
				$this->sql,
			);
		}

		return new Result(
			$this->link,
			$stmt,
		);
	}

	private static function getParameterModifier(string | float | int $value) : string
	{
		return match(\gettype($value)) {
			'string' => 's',
			'float' => 'f',
			'integer' => 'i',
		};
	}
}