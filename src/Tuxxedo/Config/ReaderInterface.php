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

use Tuxxedo\AssertionException;

interface ReaderInterface
{
	public function hasGroup(string $group) : bool;

	public function hasValue(string $directive) : bool;

	public function hasValueInGroup(string $group, string $directive) : bool;

	/**
	 * @throws AssertionException
	 */
	public function group(string $group) : array;

	/**
	 * @throws AssertionException
	 */
	public function value(string $directive) : mixed;

	/**
	 * @throws AssertionException
	 */
	public function valueFromGroup(string $group, string $directive) : mixed;
}