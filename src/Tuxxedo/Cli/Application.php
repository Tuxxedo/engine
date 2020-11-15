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

use Tuxxedo\Application as BaseApplication;
use Tuxxedo\Di;
use Tuxxedo\Route;

abstract class Application extends BaseApplication
{
	protected Dispatcher $dispatcher;
	protected Router $router;

	final public function __construct()
	{
		assert(\PHP_SAPI === 'cli');

		$di = new Di;

		$di->register(Router::class, static function() : Router {
			return new Router;
		});

		$di->register(Dispatcher::class, static function(Di $di) : Dispatcher {
			return new Dispatcher(
				di: $di,
				router: $di->need(Router::class),
			);
		});

		$this->router = $di->need(Router::class);
		$this->dispatcher = $di->need(Dispatcher::class);

		parent::__construct($di);

		$this->commands();
	}

	public function add(string $command, string $controller, string $action) : void
	{
		$this->router->addAny(
			new Route(
				regex: $command,
				controller: $controller,
				action: $action,
			)
		);
	}

	public function default(string $controller, string $action) : void
	{
		$this->dispatcher->setFallback(static function(Dispatcher $dispatcher) use($controller, $action): void {
			$dispatcher->forward(
				new Route(
					regex: '',
					controller: $controller,
					action: $action,
				)
			);
		});
	}

	// @todo Remove this temporary in favor of a Cli RequestInterface implementation
	public function getCommandLineArgs() : string
	{
		$args = $_SERVER['argv'];

		\array_shift($args);

		return \join(' ', $args);
	}

	public function run() : void
	{
		assert($this->di !== null);

		$this->di->need(Dispatcher::class)->handle(
			Router::METHOD_ANY,
			$this->getCommandLineArgs(),
		);
	}

	abstract protected function commands() : void;
}