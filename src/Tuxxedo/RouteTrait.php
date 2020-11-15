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

/**
 * @property ?string $namespace
 * @property string $controller
 * @property string $action
 * @property array<string | int, mixed> $arguments
 */
trait RouteTrait
{

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

		if ($this->namespace !== null) {
			return $this->namespace . $this->controller;
		}

		return $this->controller;
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