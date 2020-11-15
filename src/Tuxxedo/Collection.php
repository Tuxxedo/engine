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

namespace Tuxxedo;

/**
 * @template T
 * @implements \ArrayAccess<string | int, T>
 * @implements \Iterator<string | int, T>
 */
class Collection implements \ArrayAccess, \Countable, \Iterator
{
	/**
	 * @var array<string | int, T>
	 */
	protected array $collection = [];

	public function __construct(mixed ...$collection)
	{
		$this->collection = $collection;
	}

	/**
	 * @return T[]
	 */
	public function all() : mixed
	{
		return $this->collection;
	}

	/**
	 * @param string | int $name
	 * @return T
	 */
	public function get(string | int $name) : mixed
	{
		assert(isset($this->collection[$name]));

		return $this->collection[$name];
	}

	public function exists(string | int $name) : bool
	{
		return isset($this->collection[$name]);
	}

	public function count() : int
	{
		return \sizeof($this->collection);
	}

	public function offsetExists(mixed $name) : bool
	{
		return isset($this->collection[$name]);
	}

	/**
	 * @param mixed $name
	 * @return T
	 */
	public function offsetGet(mixed $name) : mixed
	{
		assert(isset($this->collection[$name]));

		return $this->collection[$name];
	}

	/**
	 * @param mixed $name
	 * @param T $value
	 */
	public function offsetSet(mixed $name, mixed $value) : void
	{
		$this->collection[$name] = $value;
	}

	/**
	 * @param mixed $name
	 * @return void
	 */
	public function offsetUnset(mixed $name) : void
	{
		assert(isset($this->collection[$name]));

		unset($this->collection[$name]);
	}

	public function key() : string | int
	{
		assert(\key($this->collection) !== null);

		return \key($this->collection);
	}

	public function valid() : bool
	{
		return \current($this->collection) !== false;
	}

	/**
	 * @return T
	 */
	public function current()
	{
		assert(\current($this->collection) !== false);

		return \current($this->collection);
	}

	public function next() : void
	{
		\next($this->collection);
	}

	public function rewind() : void
	{
		\reset($this->collection);
	}
}