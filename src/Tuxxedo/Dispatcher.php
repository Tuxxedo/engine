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

class Dispatcher
{
	protected Di $di;
	protected Router $router;
	protected ?\Closure $errorHandler = null;

	public function __construct(Di $di, Router $router, \Closure $errorHandler = null)
	{
		$this->di = $di;
		$this->router = $router;
		$this->errorHandler = $errorHandler;
	}

	public function getRouter() : Router
	{
		return $this->router;
	}

	public function setErrorHandler(?\Closure $errorHandler) : void
	{
		$this->errorHandler = $errorHandler;
	}

	public function getErrorHandler() : ?\Closure
	{
		return $this->errorHandler;
	}

	public function dispatch(string $method, string $path) : void
	{
		$route = $this->router->findRoute(
			$method,
			$path,
		);

		if ($route === null) {
			$this->handleError();

			return;
		}

		$this->forward(
			$route,
		);
	}

	public function forward(Route $route) : void
	{
		$callaback = [
			new ($route->getFullyQualifiedController())(
				$this->di,
			),
			$route->getAction()
		];

		if (!$callaback[0] instanceof Controller || !\is_callable($callaback)) {
			$this->handleError($route);

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

	protected function handleError(?Route $route = null) : void
	{
		if ($this->errorHandler !== null) {
			($this->errorHandler)(
				$this,
				$route,
			);

			return;
		}

		if ($route !== null) {
			throw new NotFoundException(
				'The controller \'%\' was not found',
				$route->getFullyQualifiedController()
			);
		}

		throw new NotFoundException(
			'No route found for the requested method and path'
		);
	}
}