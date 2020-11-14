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
	protected ?\Closure $errorHandler = null;

	public function __construct(Di $di, \Closure $errorHandler = null)
	{
		$this->di = $di;
		$this->errorHandler = $errorHandler;
	}

	public function setErrorHandler(?\Closure $errorHandler) : void
	{
		$this->errorHandler = $errorHandler;
	}

	public function getErrorHandler() : ?\Closure
	{
		return $this->errorHandler;
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
			if ($this->errorHandler !== null) {
				($this->errorHandler)();

				return;
			}

			throw new NotFoundException(
				'The controller \'%\' was not found',
				$route->getFullyQualifiedController()
			);
		}

		if ($route->hasArguments()) {
			($callaback)(...$route->getArguments());

			return;
		}

		($callaback)();
	}
}