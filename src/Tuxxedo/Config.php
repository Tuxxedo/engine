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

use Tuxxedo\Config\GroupMap;
use Tuxxedo\Config\ReaderInterface;
use Tuxxedo\Design\Immutable;

class Config implements \ArrayAccess, Immutable
{
	protected ReaderInterface $reader;

	public function __construct(ReaderInterface $reader)
	{
		$this->reader = $reader;
	}

	/**
	 * @param string $directive
	 * @return bool
	 */
	public function offsetExists(mixed $directive) : bool
	{
		return $this->hasValue($directive);
	}

	/**
	 * @param string $directive
	 * @return mixed
	 *
	 * @throws AssertionException
	 */
	public function offsetGet(mixed $directive) : mixed
	{
		return $this->getValue($directive);
	}

	/**
	 * @param string $directive
	 * @param mixed $value
	 * @return void
	 *
	 * @throws ImmutableException
	 */
	public function offsetSet(mixed $directive, mixed $value) : void
	{
		throw new ImmutableException;
	}

	/**
	 * @param string $directive
	 * @return void
	 *
	 * @throws ImmutableException
	 */
	public function offsetUnset(mixed $directive) : void
	{
		throw new ImmutableException;
	}

	public function getReader() : ReaderInterface
	{
		return $this->reader;
	}

	public function getReaderType() : string
	{
		return $this->reader::class;
	}

	public function isGroupMapped(string $group) : bool
	{
		return $this->reader->isGroupMapped($group);
	}

	public function getGroupMap() : ?GroupMap
	{
		return $this->reader->getGroupMap();
	}

	/**
	 * @throws AssertionException
	 */
	public function getGroup(string $group) : array | object
	{
		return $this->reader->group($group);
	}

	public function hasGroup(string $group) : bool
	{
		return $this->reader->hasGroup($group);
	}

	/**
	 * @throws AssertionException
	 */
	public function getValue(string $directive) : mixed
	{
		return $this->reader->value($directive);
	}

	public function hasValue(string $directive) : bool
	{
		return $this->reader->hasValue($directive);
	}

	/**
	 * @throws AssertionException
	 */
	public function getValueFromGroup(string $group, string $directive) : mixed
	{
		return $this->reader->valueFromGroup($group, $directive);
	}

	public function hasValueFromGroup(string $group, string $directive) : bool
	{
		return $this->reader->hasValueInGroup($group, $directive);
	}
}