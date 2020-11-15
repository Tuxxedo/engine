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

abstract class Dispatcher
{
	protected Di $di;
	protected RouterInterface $router;
	protected ?\Closure $fallback = null;

	public function __construct(Di $di, RouterInterface $router, \Closure $fallback = null)
	{
		assert(\is_a($router, $this->getRouterClass()));

		$this->di = $di;
		$this->router = $router;
		$this->fallback = $fallback;
	}

	public function getRouter() : RouterInterface
	{
		return $this->router;
	}

	public function setFallback(?\Closure $fallback) : void
	{
		$this->fallback = $fallback;
	}

	public function getFallback() : ?\Closure
	{
		return $this->fallback;
	}

	public function handle(string $method, string $path) : void
	{
		$route = $this->router->findRoute(
			$method,
			$path,
		);

		if ($route === null) {
			$this->fallback();

			return;
		}

		$this->forward(
			$route,
		);
	}

	protected function fallback(?RouteInterface $route = null) : void
	{
		if ($this->fallback !== null) {
			($this->fallback)(
				$this,
				$route,
			);

			return;
		}

		if ($route !== null) {
			throw new NotFoundException(
				'The controller \'%s\' was not found',
				$route->getFullyQualifiedController()
			);
		}

		throw new NotFoundException(
			'No route found for the requested method and path'
		);
	}

	public function forward(RouteInterface $route) : void
	{
		$callaback = [
			new ($route->getFullyQualifiedController())(
				$this->di,
			),
			$route->getAction()
		];

		if (!$callaback[0] instanceof Controller || !\is_callable($callaback)) {
			$this->fallback($route);

			return;
		}

		if ($route->hasArguments()) {
			($callaback)(
				...$route->getArguments()
			);

			return;
		}

		($callaback)();
	}

	abstract public function getRouterClass() : string;
}