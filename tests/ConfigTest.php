<?php

declare(strict_types = 1);

use PHPUnit\Framework\TestCase;
use Tuxxedo\AssertionException;
use Tuxxedo\Config;
use Tuxxedo\Config\Reader\Ini;
use Tuxxedo\Config\Reader\Json;
use Tuxxedo\Config\ReaderException;
use Tuxxedo\ImmutableException;

final class ConfigTest extends TestCase
{
	public function configInterfaceDataProvider() : \Generator
	{
		yield [
			AssertionException::class,
			fn(Config $config) => $config->getValue('unknown.test')
		];

		yield [
			AssertionException::class,
			fn(Config $config) => $config['unknown.test']
		];

		yield [
			AssertionException::class,
			fn(Config $config) => $config->getGroup('unknown')
		];

		yield [
			AssertionException::class,
			fn(Config $config) => $config->getValueFromGroup('unknown', 'test')
		];

		yield [
			ImmutableException::class,
			fn(Config $config) => $config['test'] = 'a'
		];

		yield [
			ImmutableException::class,
			static function(Config $config) : void {
				unset($config['unknown.test']);
			}
		];
	}

	/**
	 * @dataProvider configInterfaceDataProvider
	 */
	public function testIniConfigInterface(string $exceptionClassName, \Closure $testCase) : void
	{
		$config = new Config(
			Ini::fromString('[empty]')
		);

		$this->expectException($exceptionClassName);

		$testCase($config);
	}

	/**
	 * @dataProvider configInterfaceDataProvider
	 */
	public function testJsonConfigInterface(string $exceptionClassName, \Closure $testCase) : void
	{
		$config = new Config(
			Json::fromString('{}')
		);

		$this->expectException($exceptionClassName);

		$testCase($config);
	}

	public function iniStringDataProvider() : \Generator
	{
		yield [
			"[app]\nname = 'Demo 1'\nversion = 1"
		];

		yield [
			"[app]\nname = 'Demo 2'\nversion = 2"
		];
	}

	public function iniBadStringDataProvider() : \Generator
	{
		yield [
			''
		];

		yield [
			'['
		];

		yield [
			'='
		];
	}

	/**
	 * @dataProvider iniStringDataProvider
	 */
	public function testIniString(string $ini) : void
	{
		$config = new Config(
			Ini::fromString($ini)
		);

		$this->configGenericTest(
			Ini::class,
			$config
		);
	}

	/**
	 * @dataProvider iniBadStringDataProvider
	 */
	public function testIniBadString(string $ini) : void
	{
		$this->expectException(ReaderException::class);

		new Config(
			Ini::fromString($ini)
		);
	}

	/**
	 * @dataProvider iniStringDataProvider
	 */
	public function testIniFile(string $ini) : void
	{
		$tempIniFile = $this->configTempFile($ini);

		$config = new Config(
			Ini::fromFile($tempIniFile)
		);

		$this->configGenericTest(
			Ini::class,
			$config
		);

		@\unlink($tempIniFile);
	}

	public function jsonStringDataProvider() : \Generator
	{
		yield [
			'{"app":{"name": "Demo 1", "version": 1}}'
		];

		yield [
			'{"app":{"name": "Demo 2", "version": 2}}'
		];
	}

	public function jsonBadStringDataProvider() : \Generator
	{
		yield [
			'{x}'
		];

		yield [
			'{"app"{}}'
		];
	}

	/**
	 * @dataProvider jsonStringDataProvider
	 */
	public function testJsonString(string $json) : void
	{
		$config = new Config(
			Json::fromString($json)
		);

		$this->configGenericTest(Json::class, $config);
	}

	/**
	 * @dataProvider jsonBadStringDataProvider
	 */
	public function testJsonBadString(string $json) : void
	{
		$this->expectException(ReaderException::class);

		new Config(
			Json::fromString($json)
		);
	}

	/**
	 * @dataProvider jsonStringDataProvider
	 */
	public function testJsonFile(string $json) : void
	{
		$tempJsonFile = $this->configTempFile($json);

		$config = new Config(
			Json::fromFile($tempJsonFile)
		);

		$this->configGenericTest(
			Json::class,
			$config
		);

		@\unlink($tempJsonFile);
	}

	private function configTempFile(string $contents) : string
	{
		$file = \tempnam(\sys_get_temp_dir(), 'tux');

		$this->assertIsInt(\file_put_contents($file, $contents));

		return $file;
	}

	private function configGenericTest(string $readerClassName, Config $config) : void
	{
		$this->assertInstanceOf(Config::class, $config);
		$this->assertInstanceOf($readerClassName, $config->getReader());
		$this->assertSame($readerClassName, $config->getReaderType());

		$this->assertTrue(isset($config['app.name']));
		$this->assertNotTrue(isset($config['invalid']));

		$this->assertTrue($config->hasGroup('app'));
		$this->assertTrue($config->hasValue('app.name'));
		$this->assertTrue($config->hasValueFromGroup('app', 'name'));
		$this->assertTrue(\str_starts_with($config->getValue('app.name'), 'Demo'));

		$this->assertTrue($config->hasValueFromGroup('app', 'version'));
		$this->assertIsInt($config->getValueFromGroup('app', 'version'));
		$this->assertSame($config->getValueFromGroup('app', 'version'), $config['app.version']);

		$group = $config->getGroup('app');

		$this->assertIsArray($group);
		$this->assertTrue(isset($group['name']));
	}
}