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

use Tuxxedo\Config\ReaderTrait;
use Tuxxedo\Config\ReaderException;
use Tuxxedo\Config\ReaderInterface;

class Ini implements ReaderInterface
{
	use ReaderTrait
	{
		index as private;
	}

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
		$this->index($config);
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
		$iniFile = \parse_ini_file($iniFile, true, \INI_SCANNER_TYPED);

		if (!$iniFile) {
			throw new ReaderException('Unable to parse ini file');
		}

		return new self($iniFile);
	}
}