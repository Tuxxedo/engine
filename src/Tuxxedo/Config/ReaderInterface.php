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
use Tuxxedo\ImmutableCollection;

interface ReaderInterface
{
	public static function fromString(string $string, ImmutableCollection $groupMap = null) : self;

	public static function fromFile(string $file, ImmutableCollection $groupMap = null) : self;

	public function isGroupMapped(string $group) : bool;

	public function getGroupMap() : ?ImmutableCollection;

	public function hasGroup(string $group) : bool;

	public function hasValue(string $directive) : bool;

	public function hasValueInGroup(string $group, string $directive) : bool;

	/**
	 * @throws AssertionException
	 */
	public function group(string $group) : object;

	/**
	 * @throws AssertionException
	 */
	public function value(string $directive) : mixed;

	/**
	 * @throws AssertionException
	 */
	public function valueFromGroup(string $group, string $directive) : mixed;
}