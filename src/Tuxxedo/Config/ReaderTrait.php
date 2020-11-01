<?php
/**
 * ^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^
 * Tuxxedo Engine
 * ^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^
 *
 * @copyright   2006-2020 Kalle Sommer Nielsen <kalle@tuxxedo.app>
 * @license     MIT
 *
 * ^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^
 */

declare(strict_types = 1);

namespace Tuxxedo\Config;

use Tuxxedo\AssertionException;

/**
 * @property array<string, array<string, mixed>> $groups
 * @property array<string, mixed> $values
 */
trait ReaderTrait
{
	/**
	 * @param array<string, array<string, mixed>>
	 * @return void
	 */
	public function index(array $config) : void
	{
		foreach ($config as $section => $values) {
			if (!$values) {
				continue;
			}

			$this->groups[$section] = [];

			foreach ($values as $name => $value) {
				$this->groups[$section][$name] = $value;
				$this->values[$section . '.' . $name] = $this->groups[$section][$name];
			}
		}
	}


	public function hasGroup(string $group) : bool
	{
		return isset($this->groups[$group]);
	}

	public function hasValue(string $directive) : bool
	{
		return isset($this->values[$directive]);
	}

	public function hasValueInGroup(string $group, string $directive) : bool
	{
		return $this->hasGroup($group) && isset($this->groups[$group][$directive]);
	}

	public function group(string $group) : array
	{
		assert(
			$this->hasGroup($group),
			new AssertionException(
				'Invalid group: `%s`',
				$group,
			)
		);

		return $this->groups[$group];
	}

	public function value(string $directive) : mixed
	{
		assert(
			$this->hasValue($directive),
			new AssertionException(
				'Invalid directive: `%s`',
				$directive,
			)
		);

		return $this->values[$directive];
	}

	public function valueFromGroup(string $group, string $directive) : mixed
	{
		assert(
			$this->hasGroup($group),
			new AssertionException(
				'Invalid group: `%s`',
				$group,
			)
		);

		assert(
			$this->hasValueInGroup($group, $directive),
			new AssertionException(
				'Invalid direction: `%s` in group `%s`',
				$directive,
				$group,
			)
		);

		return $this->groups[$group][$directive];
	}
}