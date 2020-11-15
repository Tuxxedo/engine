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

use Tuxxedo\Application as BaseApplication;
use Tuxxedo\Cli\Attributes\Command as CommandAttribute;
use Tuxxedo\Controller;
use Tuxxedo\Di;

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

		$this->mounts();
	}

	public function mount(string $controllerClass) : void
	{
		assert(\class_exists($controllerClass));
		assert(\is_a($controllerClass, Controller::class, true));

		$controller = new \ReflectionClass($controllerClass);

		foreach ($controller->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
			if ($method->isConstructor()) {
				continue;
			}

			$attributes = $method->getAttributes(CommandAttribute::class);

			if (\sizeof($attributes) === 0) {
				continue;
			}

			foreach ($attributes as $attribute) {
				assert(isset($attribute->getArguments()[0]));

				$splitControllerClass = \explode('\\', $controllerClass);
				$command = \sprintf(
					'%s:%s',
					\strtolower(\end($splitControllerClass)),
					$attribute->getArguments()[0],
				);

				$this->router->addAny(
					new Command(
						command: $command,
						controller: $controllerClass,
						action: $method->getName(),
					)
				);
			}
		}
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

	abstract protected function mounts() : void;
}