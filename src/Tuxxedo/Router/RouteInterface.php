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

namespace Tuxxedo\Router;

interface RouteInterface
{
	public function getNamespace() : string;

	public function hasNamespace() : bool;

	public function getController() : string;

	public function isNamespacedController() : bool;

	public function getFullyQualifiedController() : string;

	public function getAction() : string;

	/**
	 * @return array<string | int, mixed>
	 */
	public function getArguments() : array;

	public function addArgument(string $name, mixed $value) : void;

	public function hasArguments() : bool;
}