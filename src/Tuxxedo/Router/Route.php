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

namespace Tuxxedo\Router;

class Route
{
	private string $regex;
	private ?string $namespace = null;
	private string $controller;
	private string $action;

	/**
	 * @var array<string | int, mixed>
	 */
	private array $arguments;

	/**
	 * @param string $controller
	 * @param string $action
	 * @param string|null $namespace
	 * @param array<string | int, mixed> $arguments
	 */
	public function __construct(string $regex, string $controller, string $action, ?string $namespace = null, array $arguments = [])
	{
		assert($namespace === null || $namespace[0] === '\\');

		$this->regex = $regex;
		$this->namespace = $namespace;
		$this->controller = $controller;
		$this->action = $action;
		$this->arguments = $arguments;
	}

	public function getRegex() : string
	{
		return $this->regex;
	}

	public function getNamespace() : string
	{
		assert($this->namespace !== null);

		return $this->namespace;
	}

	public function hasNamespace() : bool
	{
		return $this->namespace !== null;
	}

	public function getController() : string
	{
		return $this->controller;
	}

	public function isNamespacedController() : bool
	{
		return $this->controller[0] === '\\';
	}

	public function getFullyQualifiedController() : string
	{
		if ($this->controller[0] === '\\') {
			return $this->controller;
		}

		$namespace = '';

		if ($this->namespace !== null) {
			$namespace .= $this->namespace;
		}

		return $namespace . $this->controller;
	}

	public function getAction() : string
	{
		return $this->action;
	}

	/**
	 * @return array<string | int, mixed>
	 */
	public function getArguments() : array
	{
		assert(\sizeof($this->arguments) > 0);

		return $this->arguments;
	}

	public function addArgument(string $name, mixed $value) : void
	{
		$this->arguments[$name] = $value;
	}

	public function hasArguments() : bool
	{
		return \sizeof($this->arguments) > 0;
	}
}