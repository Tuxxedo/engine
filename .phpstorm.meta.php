<?php

namespace PHPSTORM_META
{
	use Tuxxedo\Di;

	override(Di::get(0), map([
		'' => '@',
	]));
}