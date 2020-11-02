<?php

declare(strict_types = 1);

use PHPUnit\Framework\TestCase;
use Tuxxedo\Config;
use Tuxxedo\Config\Reader\Json;
use Tuxxedo\Config\GroupMap;
use Tuxxedo\Di;
use Tuxxedo\Version;

final class DiTest extends TestCase
{
	public function diServicesDataProvider() : \Generator
	{
		yield [
			Config::class,
			static function(Di $di) : Config {
				return new Config(
					Json::fromString(
						'{"app":{"name": "Demo", "version": 2}}',
						new GroupMap(
							app: AppDiTest::class
						)
					)
				);
			},
			function(Config $config) : void {
				/** @var AppDiTest $app */
				$app = $config->getGroup('app');

				$this->assertSame($app->name, 'Demo');
				$this->assertSame($app->version, 2);
			}
		];

		yield [
			'version',
			static function(Di $di) : string {
				return Version::FULL;
			},
			function(string $version) : void {
				$this->assertSame(Version::FULL, $version);
			}
		];
	}

	/**
	 * @dataProvider diServicesDataProvider
	 */
	public function testDiRegister(string $name, \Closure $initializer, \Closure $expectance) : void
	{
		$di = new Di;
		$di->register($name, $initializer);

		$this->assertTrue($di->isRegistered($name));
		$this->assertFalse($di->isLoaded($name));

		$expectance($di->get($name));

		$this->assertTrue($di->isLoaded($name));
	}

	/**
	 * @dataProvider diServicesDataProvider
	 */
	public function testDiUnregister(string $name, \Closure $initializer, \Closure $expectance) : void
	{
		$di = new Di;
		$di->register($name, $initializer);

		$this->assertTrue($di->isRegistered($name));
		$this->assertFalse($di->isLoaded($name));

		$expectance($di->get($name));

		$this->assertTrue($di->isRegistered($name));
		$this->assertTrue($di->isLoaded($name));

		$di->unregister($name);

		$this->assertFalse($di->isLoaded($name));
		$this->assertFalse($di->isRegistered($name));
	}

	public function testDiMultiple() : void
	{
		$di = new Di;

		$this->assertFalse($di->isRegistered('version'));
		$this->assertFalse($di->isLoaded('test'));

		$di->register('version', fn(Di $di) : string => Version::FULL);
		$di->register('test', fn(Di $di) : bool => $di->get('version') === Version::FULL);

		$this->assertTrue($di->get('test'));
	}
}

class AppDiTest
{
	public ?string $name = null;
	public ?int $version = null;
}