<?php

use PHPUnit\Framework\TestCase;
use Tuxxedo\Collection;
use Tuxxedo\ImmutableException;
use Tuxxedo\ImmutableCollection;

class CollectionTest extends TestCase
{
	public function testBasicGet() : void
	{
		/** @var Collection<int> $fruits */
		$fruits = new Collection(
			apple: 10,
			banana: 3,
			orange: 5,
			kiwi: 8,
		);

		$this->assertSame($fruits['kiwi'], 8);
		$this->assertSame(\sizeof($fruits), 4);
	}

	public function testIteration() : void
	{
		/** @var Collection<int> $fruits */
		$fruits = new Collection(
			apple: 0,
			orange: 0,
			banana: 0,
		);

		foreach ($fruits->all() as $value) {
			$this->assertSame($value, 0);
		}

		foreach ($fruits as $value) {
			$this->assertSame($value, 0);
		}
	}

	public function setTestDataProvider() : \Generator
	{
		yield [
			'apple',
			3,
			2,
			6,
		];

		yield [
			'orange',
			1,
			2,
			2,
		];

		yield [
			'banana',
			5,
			2,
			10,
		];
	}

	/**
	 * @dataProvider setTestDataProvider
	 */
	public function testBasicSet(string $fruit, int $amountPerBag, int $times, int $expectedTotal) : void
	{
		/** @var Collection<int> $fruits */
		$fruits = new Collection(
			...[
				$fruit => 0,
			]
		);

		for ($i = 0; $i < $times; $i++) {
			$fruits[$fruit] += $amountPerBag;

			$this->assertSame($fruits[$fruit], ($i + 1) * $amountPerBag);
		}

		$this->assertSame($fruits[$fruit], $expectedTotal);
	}

	public function immutableTestDataProvider() : \Generator
	{
		yield [
			'apple',
			10,
			static function(ImmutableCollection $collection) : void {
				$collection['apple'] = 11;
			}
		];

		yield [
			'apple',
			3,
			static function(ImmutableCollection $collection) : void {
				unset($collection['apple']);
			}
		];
	}

	/**
	 * @dataProvider immutableTestDataProvider
	 */
	public function testImmutable(string $initialKey, int $initalValue, \Closure $test)
	{
		$this->expectException(ImmutableException::class);

		/** @var ImmutableCollection<int> $fruits */
		$fruits = new ImmutableCollection(
			...[
				$initialKey => $initalValue
			]
		);

		$test($fruits);
	}
}