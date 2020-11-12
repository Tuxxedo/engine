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
use Tuxxedo\ImmutableCollection;

class Ini implements ReaderInterface
{
	use ReaderTrait
	{
		index as private;
	}

	/**
	 * @var ImmutableCollection<string>|null
	 */
	private ?ImmutableCollection $groupMap = null;

	/**
	 * @var array<string, object>
	 */
	private array $groups = [];

	/**
	 * @var array<string, mixed>
	 */
	private array $values = [];

	/**
	 * @param array<string, array<string, mixed>> $config
	 * @param ImmutableCollection<string>|null $groupMap
	 */
	private function __construct(array $config, ImmutableCollection $groupMap = null)
	{
		$this->groupMap = $groupMap;

		$this->index($config);
	}

	/**
	 * @param string $ini
	 * @param ImmutableCollection<string>|null $groupMap
	 *
	 * @throws ReaderException
	 */
	public static function fromString(string $ini, ImmutableCollection $groupMap = null) : self
	{
		$ini = @\parse_ini_string($ini, true, \INI_SCANNER_TYPED);

		if (!$ini) {
			throw new ReaderException('Unable to parse ini string');
		}

		return new self(
			$ini,
			$groupMap
		);
	}

	/**
	 * @param string $iniFile
	 * @param ImmutableCollection<string>|null $groupMap
	 *
	 * @throws ReaderException
	 */
	public static function fromFile(string $iniFile, ImmutableCollection $groupMap = null) : self
	{
		$iniFile = @\parse_ini_file($iniFile, true, \INI_SCANNER_TYPED);

		if (!$iniFile) {
			throw new ReaderException('Unable to parse ini file');
		}

		return new self(
			$iniFile,
			$groupMap
		);
	}
}