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

use Tuxxedo\Config\GroupMap;
use Tuxxedo\Config\ReaderTrait;
use Tuxxedo\Config\ReaderException;
use Tuxxedo\Config\ReaderInterface;

class Json implements ReaderInterface
{
	use ReaderTrait
	{
		index as private;
	}

	private ?GroupMap $groupMap = null;

	/**
	 * @var array<string, array<string, mixed>|object>
	 */
	private array $groups = [];

	/**
	 * @var array<string, mixed>
	 */
	private array $values = [];

	private function __construct(array $config, GroupMap $groupMap = null)
	{
		$this->groupMap = $groupMap;

		$this->index($config);
	}

	/**
	 * @throws ReaderException
	 */
	public static function fromString(string $json, GroupMap $groupMap = null, int $depth = 512) : self
	{
		try {
			$json = \json_decode($json, true, $depth, \JSON_THROW_ON_ERROR);
		} catch (\JsonException $e) {
			// @todo Look at rethrow logic to preserve trace
			throw new ReaderException($e->getMessage());
		}

		return new self(
			$json,
			$groupMap,
		);
	}

	/**
	 * @throws ReaderException
	 */
	public static function fromFile(string $jsonFile, GroupMap $groupMap = null, int $depth = 512) : self
	{
		$json = @\file_get_contents($jsonFile);

		if (!$json) {
			throw new ReaderException('Unable to read JSON file');
		}

		return self::fromString(
			$json,
			$groupMap,
			$depth
		);
	}
}