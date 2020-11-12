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
	 * @var array<string, \Closure>
	 */
	private array $services = [];

	/**
	 * @var array<string, true>
	 */
	private array $loaded = [];

	private function __construct()
	{
	}

	private function __clone()
	{
	}

	public static function init() : self
	{
		if (!self::$instance instanceof self) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	public static function reset() : void
	{
		if (self::$instance === null) {
			return;
		}

		self::$instance->services = [];
		self::$instance->loaded = [];
	}

	public function register(string $name, \Closure $callback) : void
	{
		assert(!isset($this->services[$name]));

		$this->services[$name] = $callback;
	}

	public function unregister(string $name) : void
	{
		assert(isset($this->services[$name]));

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
		if (!isset($this->services[$name])) {
			return null;
		}

		if (!isset($this->loaded[$name])) {
			$this->services[$name] = $this->services[$name]($this);
			$this->loaded[$name] = true;
		}

		return $this->services[$name];
	}

	public function need(string $name) : mixed
	{
		$service = $this->get($name);

		if ($service === null) {
			throw new Exception(
				'Unable to find DI service: %s',
				$name
			);
		}

		return $service;
	}
}