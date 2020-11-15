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

use Tuxxedo\Router\RouteInterface;

/**
 * @property array<string, array<int, RouteInterface>> $routes
 */
trait RouterTrait
{
	public function add(string $method, RouteInterface $route) : void
	{
		assert(self::isValidMethod($method));

		$this->routes[$method][] = $route;
	}

	public function addAny(RouteInterface $route) : void
	{
		$this->add(self::METHOD_ANY, $route);
	}

	/**
	 * @return RouteInterface[]
	 */
	public function getRoutes(string $method) : array
	{
		assert(self::isValidMethod($method));

		$routes = $this->routes[$method];

		if ($method !== self::METHOD_ANY) {
			$routes = \array_merge(
				$this->routes[self::METHOD_ANY],
				$routes,
			);
		}

		return $routes;
	}
}