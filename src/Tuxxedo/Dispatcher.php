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
	private Di $di;

	public function __construct(Di $di)
	{
		$this->di = $di;
	}

	public function forward(Route $route) : void
	{
		$callaback = [
			new ($route->getFullyQualifiedController())(
				$this->di,
			),
			$route->getAction()
		];

		assert($callaback[0] instanceof Controller);
		assert(\is_callable($callaback));

		if ($route->hasArguments()) {
			\call_user_func($callaback, ...$route->getArguments());

			return;
		}

		\call_user_func($callaback);
	}
}