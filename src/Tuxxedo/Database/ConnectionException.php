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

namespace Tuxxedo\Database;

use Tuxxedo\Exception;

class ConnectionException extends Exception
{
	private int $errno;
	private string $error;

	public function __construct(int $code, string $message)
	{
		$this->errno = $code;
		$this->error = $message;

		parent::__construct(
			'Unable to connect to database: [%d] %s',
			$code,
			$message
		);
	}

	public function getErrno() : int
	{
		return $this->errno;
	}

	public function getError() : string
	{
		return $this->error;
	}
}