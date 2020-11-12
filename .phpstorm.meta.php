<?php

namespace PHPSTORM_META
{

	use Tuxxedo\Collection;
	use Tuxxedo\Di;

	override(Di::get(0), map([
		'' => '@',
	]));

	override(Di::need(0), map([
		'' => '@',
	]));

	override(Collection::get(0), map([
		'' => '@'
	]));
}