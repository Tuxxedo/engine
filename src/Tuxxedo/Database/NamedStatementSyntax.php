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

use Tuxxedo\Database\Driver\Mysql;
use Tuxxedo\Database\Driver\Postgresql;
use Tuxxedo\Exception;

class NamedStatementSyntax
{
	public const FLAVOR_MYSQL = 'mysql';
	public const FLAVOR_PGSQL = 'pgsql';

	public const RULE_MODIFIER  = 'replacementModifier';
	public const RULE_NUMERIC = 'numericModifier';
	public const RULE_TYPES = 'bindingTypes';

	/**
	 * @var array<string, array<string, string | bool>>
	 */
	private static array $flavorRules = [
		self::FLAVOR_MYSQL => [
			self::RULE_MODIFIER => '?',
			self::RULE_NUMERIC => false,
			self::RULE_TYPES => true,
		],
		self::FLAVOR_PGSQL => [
			self::RULE_MODIFIER => '$',
			self::RULE_NUMERIC => true,
			self::RULE_TYPES => false,
		]
	];

	private string $flavor;
	private string $sql;

	/**
	 * @var array<int, array<string, string | float | int | array<int, string | float | int>>>
	 */
	private array $bindings;

	/**
	 * @param array<string, string | float | int> $bindings
	 */
	public function __construct(string $flavor, string $sql, array $bindings)
	{
		assert(self::isSupportedFlavor($flavor));

		$this->flavor = $flavor;

		$this->parse(
			$flavor,
			$sql,
			$bindings,
		);
	}

	public static function isSupportedFlavor(string $flavor) : bool
	{
		return isset(self::$flavorRules[$flavor]);
	}

	public static function getFlavorRule(string $flavor, string $rule) : mixed
	{
		assert(self::isSupportedFlavor($flavor));
		assert(isset(self::$flavorRules[$flavor][$rule]));

		return self::$flavorRules[$flavor][$rule];
	}

	/**
	 * @return array<string, string | bool>
	 */
	public static function getFlavorRules(string $flavor) : array
	{
		assert(self::isSupportedFlavor($flavor));

		return self::$flavorRules[$flavor];
	}

	public static function getDeterminedFlavor(ConnectionInterface $connection) : ?string
	{
		return match($connection::class) {
			Mysql\Connection::class	=> self::FLAVOR_MYSQL,
			Postgresql\Connection::class => self::FLAVOR_PGSQL,
			default	=> null,
		};
	}

	/**
	 * @param array<string, string | float | int> $bindings
	 */
	protected function parse(string $flavor, string $sql, array $bindings) : void
	{
		$newBindings = [];
		$rules = self::$flavorRules[$flavor];

		$this->sql = (string) \preg_replace_callback(
			'/:([^:]+):/',
			static function(array $matches) use($flavor, $rules, $newBindings, $bindings) : string {
				static $n = 0;

				if (!isset($bindings[$matches[1]])) {
					throw new Exception(
						'Named statement variable (%s) found, but not bound',
						$matches[1]
					);
				}

				if ($rules[self::RULE_TYPES]) {
					$newBindings[$matches[1]] = [
						$bindings[$matches[1]],
						self::getTypeModifier(
							$flavor,
							\gettype($bindings[$matches[1]])
						)
					];
				} else {
					$newBindings[$matches[1]] = $bindings[$matches[1]];
				}

				if ($rules[self::RULE_NUMERIC]) {
					return $rules[self::RULE_MODIFIER] . $n++;
				}

				return (string) $rules[self::RULE_MODIFIER];
			},
			$sql,
		);
		$this->bindings = $newBindings;
	}

	protected static function getTypeModifier(string $flavor, string $phpType) : string
	{
		assert($flavor === self::FLAVOR_MYSQL);
		assert($phpType === 'string' || $phpType === 'integer' || $phpType === 'float');

		return match($phpType) {
			'string' => 's',
			'float' => 'f',
			'integer' => 'i',
		};
	}

	public function getFlavor() : string
	{
		return $this->flavor;
	}

	public function getSql() : string
	{
		return $this->sql;
	}

	/**
	 * @return array<int, array<string, string | float | int | array<int, string | float | int>>>
	 */
	public function getBindings() : array
	{
		return $this->bindings;
	}
}