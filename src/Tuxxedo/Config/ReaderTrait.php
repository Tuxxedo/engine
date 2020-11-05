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
use Tuxxedo\ImmutableCollection;

/**
 * @property ImmutableCollection<string>|null $groupMap
 * @property array<string, object> $groups
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
		foreach ($config as $group => $values) {
			if (!$values) {
				continue;
			}

			if (isset($this->groupMap[$group])) {
				$this->groups[$group] = new $this->groupMap[$group];
			} else {
				$this->groups[$group] = new \stdClass;
			}

			foreach ($values as $name => $value) {
				$this->groups[$group]->{$name} = $value;
				$this->values[$group . '.' . $name] = $this->groups[$group]->{$name};
			}
		}
	}

	public function isGroupMapped(string $group) : bool
	{
		return $this->hasGroup($group) && isset($this->groupMap[$group]);
	}

	public function getGroupMap() : ?ImmutableCollection
	{
		return $this->groupMap;
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
		return $this->hasGroup($group) && isset($this->groups[$group]->{$directive});
	}

	public function group(string $group) : object
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

		return $this->groups[$group]->{$directive};
	}
}