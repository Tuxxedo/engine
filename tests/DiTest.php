<?php

declare(strict_types = 1);

use PHPUnit\Framework\TestCase;
use Tuxxedo\Config;
use Tuxxedo\Config\Reader\Json;
use Tuxxedo\Di;
use Tuxxedo\Exception;
use Tuxxedo\ImmutableCollection;
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
						new ImmutableCollection(
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

		yield [
			AppDiTest::class,
			null,
			function(AppDiTest $test) : void {
				$this->assertIsObject($test);
			}
		];
	}

	/**
	 * @dataProvider diServicesDataProvider
	 */
	public function testDiRegister(string $name, ?\Closure $initializer, \Closure $expectance) : void
	{
		$di = new Di;

		if ($initializer !== null) {
			$di->register($name, $initializer);
		} else {
			$di->register($name);
		}

		$this->assertTrue($di->isRegistered($name));
		$this->assertFalse($di->isLoaded($name));

		$expectance($di->get($name));

		$this->assertTrue($di->isLoaded($name));
	}

	/**
	 * @dataProvider diServicesDataProvider
	 */
	public function testDiUnregister(string $name, ?\Closure $initializer, \Closure $expectance) : void
	{
		$di = new Di;

		if ($initializer !== null) {
			$di->register($name, $initializer);
		} else {
			$di->register($name);
		}

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

	public function testNeeds() : void
	{
		$this->expectException(Exception::class);

		(new Di)->need('unknown');
	}

	public function testMulti() : void
	{
		$di1 = new Di;

		$di1->register('version', fn() : string => Version::FULL);

		$this->assertTrue($di1->isRegistered('version'));

		$di2 = clone $di1;

		$this->assertTrue($di2->isRegistered('version'));
		$this->assertFalse($di2->isLoaded('version'));

		$this->assertSame($di1->isRegistered('version'), $di2->isRegistered('version'));
		$this->assertSame($di1->isLoaded('version'), $di2->isLoaded('version'));
	}

	public function testReset() : void
	{
		$di = new Di;

		$this->assertFalse($di->isRegistered('version'));
		$this->assertFalse($di->isLoaded('version'));

		$di->register('version', fn() : string => Version::FULL);

		$this->assertSame(Version::FULL, $di->get('version'));

		$this->assertTrue($di->isRegistered('version'));
		$this->assertTrue($di->isLoaded('version'));

		$di->reset();

		$this->assertFalse($di->isRegistered('version'));
		$this->assertFalse($di->isLoaded('version'));
	}
}

class AppDiTest
{
	public ?string $name = null;
	public ?int $version = null;

	public function __construct(Di $di = null)
	{
	}
}