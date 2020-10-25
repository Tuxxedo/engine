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

class Json implements ReaderInterface
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
	public static function fromString(string $json) : self
	{
		try {
			$json = \json_decode($json, true, 512, \JSON_THROW_ON_ERROR);
		} catch (\JsonException $e) {
			// @todo Look at rethrow logic to preserve trace
			throw new ReaderException($e->getMessage());
		}

		return new self($json);
	}

	/**
	 * @throws ReaderException
	 */
	public static function fromFile(string $jsonFile) : self
	{
		$json = @\file_get_contents($jsonFile);

		if (!$json) {
			throw new ReaderException('Unable to read JSON file');
		}

		return self::fromString($json);
	}
}