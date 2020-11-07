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

namespace Tuxxedo\Database;

use Tuxxedo\Exception;

class NamedStatementSyntax
{
	public const FLAVOR_MYSQL = 'mysql';
	public const FLAVOR_PGSQL = 'pgsql';

	protected const RULE_MODIFIER  = 'replacementModifier';
	protected const RULE_NUMERIC = 'numericModifier';
	protected const RULE_TYPES = 'bindingTypes';

	protected static array $flavorRules = [
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

	protected function parse(string $flavor, string $sql, array $bindings) : void
	{
		\preg_match_all(
			'/:([^:]+):/',
			$sql,
			$matches,
			\PREG_OFFSET_CAPTURE,
		);

		$replacementOffset = 1;
		$newBindings = [];
		$usesTypes = self::$flavorRules[$flavor][self::RULE_TYPES];
		$usesNumeric = self::$flavorRules[$flavor][self::RULE_NUMERIC];
		$replacementModifier = self::$flavorRules[$flavor][self::RULE_MODIFIER];

		foreach ($matches[1] as $index => [$varname, $offset]) {
			if (!isset($bindings[$varname])) {
				// @todo Should maybe be a different exception
				throw new Exception(
					'Named statement variable (%s) found, but not bound',
					$varname
				);
			}

			if ($usesTypes) {
				$newBindings[$varname] = [
					$bindings[$varname],
					self::getTypeModifier(
						$flavor,
						\gettype($bindings[$varname])
					),
				];
			} else {
				$newBindings[$varname] = $bindings[$varname];
			}

			$varnameLength = \strlen($varname);

			if ($usesNumeric) {
				$sql = \substr_replace(
					$sql,
					$replacementModifier . $index,
					$offset - $replacementOffset + ($index ? 1 : 0),
					$varnameLength + 2,
				);

				$replacementOffset += $varnameLength + 1  + ($index ? 1 : 0);
			} else {
				$sql = \substr_replace(
					$sql,
					$replacementModifier,
					$offset - $replacementOffset,
					$varnameLength + 2,
				);

				$replacementOffset += $varnameLength + 1;
			}
		}

		$this->sql = $sql;
		$this->bindings = $newBindings;
	}

	protected function getTypeModifier(string $flavor, string $phpType) : string
	{
		assert($flavor === self::FLAVOR_MYSQL);

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