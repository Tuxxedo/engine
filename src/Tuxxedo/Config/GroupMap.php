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

namespace Tuxxedo\Config;

use Tuxxedo\Design\Immutable;
use Tuxxedo\ImmutableException;

class GroupMap implements \ArrayAccess, Immutable
{
	private $classMap = [];

	public function __construct(string ...$aliases)
	{
		$this->classMap = $aliases;
	}

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
		throw new ImmutableException;
	}

	public function offsetUnset(mixed $alias) : void
	{
		throw new ImmutableException;
	}
}