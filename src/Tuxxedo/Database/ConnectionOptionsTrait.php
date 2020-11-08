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

namespace Tuxxedo\Database;

/**
 * @property array<string, mixed> $options
 */
trait ConnectionOptionsTrait
{
	public function hasOption(string $name) : bool
	{
		return isset($this->options[$name]);
	}

	public function setOption(string $name, mixed $value) : void
	{
		assert(\array_key_exists($name, $this->options));

		$this->options[$name] = $value;
	}

	public function getOption(string $name) : mixed
	{
		assert(\array_key_exists($name, $this->options));

		return $this->options[$name];
	}

	/**
	 * @param array<string, mixed>|iterable<object> $options
	 */
	public function setOptions(array | object $options) : void
	{
		foreach ($options as $name => $value) {
			$this->setOption($name, $value);
		}
	}

	/**
	 * @return array<string, mixed>
	 */
	public function getOptions() : array
	{
		return $this->options;
	}
}