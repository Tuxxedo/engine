<?php
/**
 * ^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^
 * Tuxxedo Engine
 * ^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^
 *
 * @copyright        2006-2020 Kalle Sommer Nielsen <kalle@tuxxedo.app>
 * @license        MIT
 *
 * ^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^
 */

declare(strict_types = 1);

namespace Tuxxedo\Config;

class GroupMap implements \ArrayAccess
{
	private $classMap = [];

	public function offsetExists(mixed $alias) : bool
	{
		return isset($this->classMap[$alias]);
	}

	public function offsetGet(mixed $alias) : mixed
	{
		assert(isset($this->classMap[$alias]));

		return $this->classMap[$alias];
	}

	public function offsetSet(mixed $alias, mixed $className) : void
	{
		assert(\is_string($className));

		$this->classMap[$alias] = $className;
	}

	public function offsetUnset(mixed $alias) : void
	{
		unset($this->classMap[$alias]);
	}
}