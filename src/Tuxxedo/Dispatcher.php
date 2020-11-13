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
	public function forward(Route $route) : void
	{
		$callaback = [
			new ($route->getFullyQualifiedController()),
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