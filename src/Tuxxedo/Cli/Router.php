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

namespace Tuxxedo\Cli;

use Tuxxedo\Route;
use Tuxxedo\RouterInterface;
use Tuxxedo\RouterTrait;

class Router implements RouterInterface
{
	use RouterTrait;

	/**
	 * @var array<string, array<int, Route>>
	 */
	protected array $routes = [
		self::METHOD_ANY => [],
	];

	private static function isValidMethod(string $method) : bool
	{
		return $method === self::METHOD_ANY;
	}
}