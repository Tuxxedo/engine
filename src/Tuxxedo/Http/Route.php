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

namespace Tuxxedo\Http;

use Tuxxedo\Router\RouteInterface;
use Tuxxedo\RouteTrait;

class Route implements RouteInterface
{
	use RouteTrait;

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

	public function getTransformedRegex() : ?string
	{
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
}
