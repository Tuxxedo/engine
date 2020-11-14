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

namespace Tuxxedo\Router;

class RouteRegex
{
	protected ?string $regex = null;

	/**
	 * @var array<string, string>
	 */
	protected array $captures = [];

	public function setRegex(string $regex) : void
	{
		$this->regex = $regex;
	}

	public function getRegex() : ?string
	{
		return $this->regex;
	}

	public function addCapture(string $name, string $type) : void
	{
		assert(!isset($this->captures[$name]));
		assert(self::isValidType($type));

		$this->captures[$name] = $type;
	}

	/**
	 * @return array<string, string>
	 */
	public function getCaptures() : array
	{
		return $this->captures;
	}

	public function hasCaptures() : bool
	{
		return \sizeof($this->captures) > 0;
	}

	private static function isValidType(string $type) : bool
	{
		return $type === 'string' || $type === 'int' || $type === 'float';
	}
}