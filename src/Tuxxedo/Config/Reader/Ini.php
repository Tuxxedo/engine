<?php
/**
 * ^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^
 * Tuxxedo Engine
 * ^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^
 *
 * @copyright   2006-2020 Kalle Sommer Nielsen <kalle@tuxxedo.app>
 * @license     MIT
 *
 * ^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^-^
 */

declare(strict_types = 1);

namespace Tuxxedo\Config\Reader;

use Tuxxedo\AssertionException;
use Tuxxedo\Config\ReaderException;
use Tuxxedo\Config\ReaderInterface;

class Ini implements ReaderInterface
{
	/**
	 * @var array<string, array<string, mixed>>
	 */
	private array $groups = [];

	/**
	 * @var array<string, mixed>
	 */
	private array $values = [];

	private function __construct(array $config)
	{
		foreach ($config as $section => $values) {
			if (!$values) {
				continue;
			}

			$this->groups[$section] = [];

			foreach ($values as $name => $value) {
				$this->groups[$section][$name] = $value;
				$this->values[$section . '.' . $name] = &$this->groups[$section][$name];
			}
		}
	}

	/**
	 * @throws ReaderException
	 */
	public static function fromString(string $ini) : self
	{
		$ini = \parse_ini_string($ini, true, \INI_SCANNER_TYPED);

		if (!$ini) {
			throw new ReaderException('Unable to parse ini string');
		}

		return new self($ini);
	}

	/**
	 * @throws ReaderException
	 */
	public static function fromFile(string $iniFile) : self
	{
		$ini = \parse_ini_file($iniFile, true, \INI_SCANNER_TYPED);

		if (!$ini) {
			throw new ReaderException('Unable to parse ini file');
		}

		return new self($ini);
	}

	public function groupExists(string $group) : bool
	{
		return isset($this->groups[$group]);
	}

	public function valueExists(string $directive) : bool
	{
		return isset($this->values[$directive]);
	}

	public function valueExistsInGroup(string $group, string $directive) : bool
	{
		return $this->groupExists($group) && isset($this->groups[$group][$directive]);
	}

	/**
	 * @throws AssertionException
	 */
	public function group(string $group) : array
	{
		assert(
			$this->groupExists($group),
			new AssertionException(
				'Invalid group: `%s`',
				$group,
			)
		);

		return $this->groups[$group];
	}

	/**
	 * @throws AssertionException
	 */
	public function value(string $directive) : mixed
	{
		assert(
			$this->valueExists($directive),
			new AssertionException(
				'Invalid directive: `%s`',
				$directive,
			)
		);

		return $this->values[$directive];
	}

	/**
	 * @throws AssertionException
	 */
	public function valueInGroup(string $group, string $directive) : mixed
	{
		assert(
			$this->groupExists($group),
			new AssertionException(
				'Invalid group: `%s`',
				$group,
			)
		);

		assert(
			$this->valueExistsInGroup($group, $directive),
			new AssertionException(
				'Invalid direction: `%s` in group `%s`',
				$directive,
				$group,
			)
		);

		return $this->groups[$group][$directive];
	}
}