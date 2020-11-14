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

namespace Tuxxedo;

class Router
{
	public const METHOD_ANY = 'ANY';
	public const METHOD_GET = 'GET';
	public const METHOD_POST = 'POST';
	public const METHOD_PUT = 'PUT';
	public const METHOD_HEAD = 'HEAD';
	public const METHOD_DELETE = 'DELETE';
	public const METHOD_CONNECT = 'CONNECT';
	public const METHOD_OPTIONS = 'OPTIONS';
	public const METHOD_TRACE = 'TRACE';
	public const METHOD_PATCH = 'PATCH';

	/**
	 * @var array<string, array<int, Route>>
	 */
	protected array $routes = [
		self::METHOD_ANY => [],
		self::METHOD_GET => [],
		self::METHOD_POST => [],
		self::METHOD_PUT => [],
		self::METHOD_HEAD => [],
		self::METHOD_DELETE => [],
		self::METHOD_CONNECT => [],
		self::METHOD_OPTIONS => [],
		self::METHOD_TRACE => [],
		self::METHOD_PATCH => [],
	];

	private static function isValidMethod(string $method) : bool
	{
		return $method === self::METHOD_ANY
			|| $method === self::METHOD_GET
			|| $method === self::METHOD_POST
			|| $method === self::METHOD_PUT
			|| $method === self::METHOD_HEAD
			|| $method === self::METHOD_DELETE
			|| $method === self::METHOD_CONNECT
			|| $method === self::METHOD_OPTIONS
			|| $method === self::METHOD_TRACE
			|| $method === self::METHOD_PATCH;
	}

	public function add(string $method, Route $route) : void
	{
		assert(self::isValidMethod($method));

		$this->routes[$method][] = $route;
	}

	public function addAny(Route $route) : void
	{
		$this->add(self::METHOD_ANY, $route);
	}

	public function addGet(Route $route) : void
	{
		$this->add(self::METHOD_GET, $route);
	}

	public function addPost(Route $route) : void
	{
		$this->add(self::METHOD_POST, $route);
	}

	public function addPut(Route $route) : void
	{
		$this->add(self::METHOD_PUT, $route);
	}

	public function addHead(Route $route) : void
	{
		$this->add(self::METHOD_HEAD, $route);
	}

	public function addDelete(Route $route) : void
	{
		$this->add(self::METHOD_DELETE, $route);
	}

	public function addConnect(Route $route) : void
	{
		$this->add(self::METHOD_CONNECT, $route);
	}

	public function addOptions(Route $route) : void
	{
		$this->add(self::METHOD_OPTIONS, $route);
	}

	public function addTrace(Route $route) : void
	{
		$this->add(self::METHOD_TRACE, $route);
	}

	public function addPatch(Route $route) : void
	{
		$this->add(self::METHOD_PATCH, $route);
	}

	/**
	 * @return array<int, Route>
	 */
	public function getRoutes(string $method) : array
	{
		assert(self::isValidMethod($method));

		$routes = $this->routes[$method];

		if ($method !== self::METHOD_ANY) {
			$routes = \array_merge(
				$this->routes[self::METHOD_ANY],
				$routes,
			);
		}

		return $routes;
	}

	public function findRoute(string $method, string $path) : ?Route
	{
		assert(self::isValidMethod($method));

		$routes = self::getRoutes($method);

		if (!\sizeof($routes)) {
			return null;
		}

		/** @var Route $route */
		foreach ($routes as $route) {
			if (\preg_match('#' . $route->getRegex() . '#', $path)) {
				return $route;
			}
		}

		return null;
	}
}