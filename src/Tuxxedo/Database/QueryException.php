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

class QueryException extends Exception
{
	private int $errno;
	private string $error;
	private ?string $sql;

	public function __construct(int $code, string $message, string $sql = null)
	{
		$this->errno = $code;
		$this->error = $message;
		$this->sql = $sql;

		parent::__construct(
			'Unable to execute query: [%d] %s',
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

	public function getSql() : ?string
	{
		return $this->sql;
	}
}