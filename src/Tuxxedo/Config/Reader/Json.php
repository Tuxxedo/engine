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

class Json implements ReaderInterface
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
	 * @param string $json
	 * @param ImmutableCollection<string>|null $groupMap
	 * @return self
	 *
	 * @throws ReaderException
	 */
	public static function fromString(string $json, ImmutableCollection $groupMap = null) : self
	{
		try {
			$json = \json_decode($json, true, 512, \JSON_THROW_ON_ERROR);
		} catch (\JsonException $e) {
			throw new ReaderException($e->getMessage());
		}

		return new self(
			$json,
			$groupMap,
		);
	}

	/**
	 * @param string $jsonFile
	 * @param ImmutableCollection<string>|null $groupMap
	 * @return self
	 *
	 * @throws ReaderException
	 */
	public static function fromFile(string $jsonFile, ImmutableCollection $groupMap = null) : self
	{
		$jsonFile = @\file_get_contents($jsonFile);

		if (!$jsonFile) {
			throw new ReaderException('Unable to read JSON file');
		}

		return self::fromString(
			$jsonFile,
			$groupMap,
		);
	}
}