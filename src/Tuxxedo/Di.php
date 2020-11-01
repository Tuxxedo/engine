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

class Di
{
	private static ?self $instance = null;

	/**
	 * @var array<string, callable>
	 */
	private array $services = [];

	/**
	 * @var array<string, true>
	 */
	private array $loaded = [];

	public static function init() : self
	{
		if (!self::$instance instanceof self) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	public function register(string $name, callable $callback) : void
	{
		assert(
			!isset($this->services[$name]),
			new AssertionException(
				'Service `%s` is already registered',
				$name
			)
		);

		$this->services[$name] = $callback;
	}

	public function unregister(string $name) : void
	{
		assert(
			isset($this->services[$name]),
			new AssertionException(
				'Service `%s` is not registered',
				$name
			)
		);

		unset($this->services[$name]);
		unset($this->loaded[$name]);
	}

	public function isRegistered(string $name) : bool
	{
		return isset($this->services[$name]);
	}

	public function isLoaded(string $name) : bool
	{
		return isset($this->loaded[$name]);
	}

	public function get(string $name) : mixed
	{
		assert(
			isset($this->services[$name]),
			new AssertionException(
				'Service `%s` is not registered',
				$name
			)
		);

		if (!isset($this->loaded[$name])) {
			$this->services[$name] = $this->services[$name]($this);
			$this->loaded[$name] = true;
		}

		return $this->services[$name];
	}
}