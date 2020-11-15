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
	 * @var array<string, string>
	 */
	private array $captures = [];

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

	public function getRawRegex() : string
	{
		return $this->regex;
	}

	public function getTransformedRegex(string $routerClass) : ?string
	{
		assert(\is_a($routerClass, RouterInterface::class, true));

		// @todo This needs to support CLI by checking $routerClass for the syntax:
		//
		// Note that quotes can be optional, and the quote style cannot be mixed
		//
		// OLD REGEX: make:route {a:[a-z]} {b:int[0-9]+}
		// NEW REGEX: make:route \-\-a="(?<a>[a-z])" \-\-b=(?<b>[0-9]+)
		//  CAPTURES: a, string
		//            b, int

		$regex = \preg_replace_callback(
			'/{((?<arg>[a-zA-Z_\x80-\xff][a-zA-Z0-9_\x80-\xff]*):(?:(?<type>[^:]+):)?(?<regex>.*?))}/',
			function (array $match) : string {
				$this->addRegexCapture(
					$match['arg'],
					self::isValidType($match['type']) ? $match['type'] : 'string',
				);

				return \sprintf(
					'(?<%s>%s)',
					$match['arg'],
					$match['regex'],
				);
			},
			$this->regex,
			\PREG_SET_ORDER
		);

		if ($regex === null) {
			return null;
		}

		return '/' . \str_replace('/', '\\/', $regex) . '/';
	}

	protected function addRegexCapture(string $name, string $type) : void
	{
		assert(self::isValidType($type));

		$this->captures[$name] = $type;
	}

	public function hasRegexCaptures() : bool
	{
		return \sizeof($this->captures) > 0;
	}

	/**
	 * @return array<string, string>
	 */
	public function getRegexCaptures() : array
	{
		assert($this->hasRegexCaptures());

		return $this->captures;
	}

	private static function isValidType(string $type) : bool
	{
		return $type === 'string' || $type === 'int' || $type === 'float';
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
