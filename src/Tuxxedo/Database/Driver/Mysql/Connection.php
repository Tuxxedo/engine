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

namespace Tuxxedo\Database\Driver\Mysql;

use Tuxxedo\Database\ConnectionInterface;
use Tuxxedo\Database\ConnectionOptionsTrait;

class Connection implements ConnectionInterface
{
	use ConnectionOptionsTrait;

	public const OPTION_HOST = 'host';
	public const OPTION_USERNAME = 'username';
	public const OPTION_PASSWORD = 'password';
	public const OPTION_DATABASE = 'database';
	public const OPTION_TIMEOUT = 'timeout';
	public const OPTION_PORT = 'port';
	public const OPTION_SOCKET = 'socket';
	public const OPTION_PERSISTENT = 'persistent';
	public const OPTION_SSL = 'ssl';

	private \mysqli $link;

	/**
	 * @var array<string, mixed>
	 */
	private array $options = [
		self::OPTION_HOST => null,
		self::OPTION_USERNAME => '',
		self::OPTION_PASSWORD => '',
		self::OPTION_DATABASE => null,
		self::OPTION_TIMEOUT => 3,
		self::OPTION_PORT => 3306,
		self::OPTION_SOCKET => '',
		self::OPTION_PERSISTENT => false,
		self::OPTION_SSL => false,
	];
}