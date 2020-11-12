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

use Tuxxedo\ImmutableCollection;

interface ReaderInterface
{
	/**
	 * @param ImmutableCollection<string>|null $groupMap
	 */
	public static function fromString(string $string, ImmutableCollection $groupMap = null) : self;

	/**
	 * @param ImmutableCollection<string>|null $groupMap
	 */
	public static function fromFile(string $file, ImmutableCollection $groupMap = null) : self;

	public function isGroupMapped(string $group) : bool;

	/**
	 * @return ImmutableCollection<string>|null
	 */
	public function getGroupMap() : ?ImmutableCollection;

	public function hasGroup(string $group) : bool;

	public function hasValue(string $directive) : bool;

	public function hasValueInGroup(string $group, string $directive) : bool;

	public function group(string $group) : object;

	public function value(string $directive) : mixed;

	public function valueFromGroup(string $group, string $directive) : mixed;
}