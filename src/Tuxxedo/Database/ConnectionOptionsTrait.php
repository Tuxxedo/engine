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

use Tuxxedo\AssertionException;

/**
 * @property array<string, mixed> $options
 */
trait ConnectionOptionsTrait
{
	public function hasOption(string $name) : bool
	{
		return isset($this->options[$name]);
	}

	/**
	 * @throws AssertionException
	 */
	public function setOption(string $name, mixed $value) : void
	{
		assert(
			\array_key_exists($name, $this->options),
			new AssertionException(
				'Invalid option `%s` supplied',
				$name
			)
		);

		$this->options[$name] = $value;
	}

	/**
	 * @throws AssertionException
	 */
	public function getOption(string $name) : mixed
	{
		assert(
			\array_key_exists($name, $this->options),
			new AssertionException(
				'Invalid option `%s` supplied',
				$name
			)
		);

		return $this->options[$name];
	}

	/**
	 * @param array<string, mixed>|object $options
	 *
	 * @throws AssertionException
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