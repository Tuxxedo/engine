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

namespace Tuxxedo\Cli;

use Tuxxedo\Router\RouteInterface;
use Tuxxedo\RouterInterface;
use Tuxxedo\RouterTrait;

class Router implements RouterInterface
{
	use RouterTrait;

	/**
	 * @var array<string, array<int, RouteInterface>>
	 */
	protected array $routes = [
		self::METHOD_ANY => [],
	];

	private static function isValidMethod(string $method) : bool
	{
		return $method === self::METHOD_ANY;
	}

	/**
	 * @return array<string | int, string | bool>
	 */
	protected function getParsedArguments(string $path, Command $route) : array
	{
		\preg_match_all(
			'/[--]?((?<argument>[a-zA-Z_\x80-\xff][a-zA-Z0-9_\x80-\xff]+)(?:[=:](?<value>"[^"]+"|[^\s"]+))?)(?:\s+|$)/',
			\substr($path, \strlen($route->getCommand())),
			$matches,
			\PREG_SET_ORDER,
		);

		if (!\sizeof($matches)) {
			return [];
		}

		$arguments = [];

		foreach ($matches as $match) {
			$arguments[$match['argument']] = $match['value'] ?? true;
		}

		return $arguments;
	}

	public function findRoute(string $method, string $path) : ?RouteInterface
	{
		assert(self::isValidMethod($method));

		$routes = $this->getRoutes($method);

		if (!\sizeof($routes)) {
			return null;
		}

		foreach ($routes as $route) {
			assert($route instanceof Command);

			if (!\preg_match('/^' . $route->getCommand() . '/', $path, $matches)) {
				continue;
			}

			$action = new \ReflectionMethod(
				$route->getFullyQualifiedController(),
				$route->getAction(),
			);

			if ($action->getNumberOfParameters()) {
				$argc = $action->getNumberOfRequiredParameters();
				$argv = $this->getParsedArguments($path, $route);

				foreach ($action->getParameters() as $parameter) {
					if (!isset($argv[$parameter->getName()]) || !$parameter->hasType()) {
						if ($argc) {
							// @todo Throw too few arguments exception?

							return null;
						}

						continue;
					}

					assert($parameter->getType() !== null);
					assert(\method_exists($parameter->getType(), 'getName'));

					\settype($argv[$parameter->getName()], $parameter->getType()->getName());

					$route->addArgument(
						$parameter->getName(),
						$argv[$parameter->getName()]
					);

					--$argc;
				}
			}

			return $route;
		}

		return null;
	}
}