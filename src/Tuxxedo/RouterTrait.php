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

namespace Tuxxedo;

/**
 * @property array<string, array<int, string>> $routes
 */
trait RouterTrait
{
	public function add(string $method, Route $route) : void
	{
		assert(self::isValidMethod($method));

		$this->routes[$method][] = $route;
	}

	public function addAny(Route $route) : void
	{
		$this->add(self::METHOD_ANY, $route);
	}

	/**
	 * @return Route[]
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

	public function findRoute(string $method, string $path) : ?Route
	{
		assert(self::isValidMethod($method));

		$routes = self::getRoutes($method);

		if (!\sizeof($routes)) {
			return null;
		}

		foreach ($routes as $route) {
			assert($route->getRawRegex() !== null);
			assert($route->getTransformedRegex($this::class) !== null);

			if (\preg_match_all($route->getTransformedRegex($this::class), $path, $matches)) {
				if ($route->hasRegexCaptures()) {
					foreach ($route->getRegexCaptures() as $arg => $type) {
						\settype($matches[$arg][0], $type);

						$route->addArgument(
							$arg,
							$matches[$arg][0],
						);
					}
				}

				return $route;
			}
		}

		return null;
	}
}