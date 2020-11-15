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
use Tuxxedo\RouteTrait;

class Command implements RouteInterface
{
	use RouteTrait;

	protected string $command;
	protected ?string $namespace = null;
	protected string $controller;
	protected string $action;

	/**
	 * @var array<string | int, mixed>
	 */
	private array $arguments;

	/**
	 * @param array<string | int, mixed> $arguments
	 */
	public function __construct(string $command, string $controller, string $action, ?string $namespace = null, array $arguments = [])
	{
		assert($namespace === null || $namespace[0] === '\\');

		$this->command = $command;
		$this->namespace = $namespace;
		$this->controller = $controller;
		$this->action = $action;
		$this->arguments = $arguments;
	}

	public function getCommand() : string
	{
		return $this->command;
	}
}