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
	 */
	public function offsetExists($directive) : bool
	{
		return $this->hasValue($directive);
	}

	/**
	 * @param string $directive
	 *
	 * @throws AssertionException
	 */
	public function offsetGet($directive) : mixed
	{
		return $this->getValue($directive);
	}

	/**
	 * @param string $directive
	 * @param mixed $value
	 *
	 * @throws ImmutableException
	 */
	public function offsetSet($directive, $value)
	{
		throw new ImmutableException;
	}

	/**
	 * @param string $directive
	 * @return void
	 *
	 * @throws ImmutableException
	 */
	public function offsetUnset($directive) : void
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

	/**
	 * @throws AssertionException
	 */
	public function getGroup(string $group) : array
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