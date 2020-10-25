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

class Config
{
	protected ReaderInterface $reader;

	public function __construct(ReaderInterface $reader)
	{
		$this->reader = $reader;
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
		return $this->reader->groupExists($group);
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
		return $this->reader->valueExists($directive);
	}

	/**
	 * @throws AssertionException
	 */
	public function getValueFromGroup(string $group, string $directive) : mixed
	{
		return $this->reader->valueInGroup($group, $directive);
	}

	public function hasValueFromGroup(string $group, string $directive) : bool
	{
		return $this->reader->valueExistsInGroup($group, $directive);
	}
}