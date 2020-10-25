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

interface ReaderInterface
{
	public function groupExists(string $group) : bool;

	public function valueExists(string $directive) : bool;

	public function valueExistsInGroup(string $group, string $directive) : bool;

	public function group(string $group) : array;

	public function value(string $directive) : mixed;

	public function valueInGroup(string $group, string $directive) : mixed;
}