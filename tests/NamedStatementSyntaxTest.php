<?php

use PHPUnit\Framework\TestCase;
use Tuxxedo\Database\NamedStatementSyntax;

class NamedStatementSyntaxTest extends TestCase
{
	public function basicDataProvider() : \Generator
	{
		yield [
			NamedStatementSyntax::FLAVOR_MYSQL,
			[
				'orderId' => 12345
			],
			'SELECT * FROM `orders` WHERE `id` = ?',
		];

		yield [
			NamedStatementSyntax::FLAVOR_PGSQL,
			[
				'orderId' => 12345,
			],
			'SELECT * FROM `orders` WHERE `id` = $0',
		];
	}

	/**
	 * @dataProvider basicDataProvider
	 */
	public function testBasic(string $flavor, array $bindings, string $expectedSql) : void
	{
		$syntax = new NamedStatementSyntax(
			$flavor,
			'SELECT * FROM `orders` WHERE `id` = :orderId:',
			$bindings,
		);

		$this->assertSame($flavor, $syntax->getFlavor());
		$this->assertSame($expectedSql, $syntax->getSql());
	}

	public function multiBindingsDataProvider() : \Generator
	{
		yield [
			NamedStatementSyntax::FLAVOR_MYSQL,
			'SELECT * FROM `orders` WHERE (`id` = ? AND `draft` = ?) OR (`id` = ? AND `assignee` = ?)',
			[
				'ordersId' => 12345,
				'draftStatus' => 1,
				'orderId' => 12345,
				'assignee' => 'Tuxxedo',
			]
		];

		yield [
			NamedStatementSyntax::FLAVOR_PGSQL,
			'SELECT * FROM `orders` WHERE (`id` = $0 AND `draft` = $1) OR (`id` = $2 AND `assignee` = $3)',
			[
				'ordersId' => 12345,
				'draftStatus' => 1,
				'orderId' => 12345,
				'assignee' => 'Tuxxedo',
			]
		];
	}

	/**
	 * @dataProvider multiBindingsDataProvider
	 */
	public function testMultiBindings(string $flavor, string $expectedSql, array $expectedBindings) : void
	{
		$this->assertTrue(NamedStatementSyntax::isSupportedFlavor($flavor));

		$syntax = new NamedStatementSyntax(
			$flavor,
			'SELECT * FROM `orders` WHERE (`id` = :orderId: AND `draft` = :draftStatus:) OR (`id` = :orderId: AND `assignee` = :assignee:)',
			[
				'orderId' => 12345,
				'draftStatus' => 1,
				'assignee' => 'Tuxxedo',
			],
		);

		$this->assertSame($expectedSql, $syntax->getSql());

		$usesTypes = NamedStatementSyntax::getFlavorRule(
			$flavor,
			NamedStatementSyntax::RULE_TYPES,
		);

		foreach ($syntax->getBindings() as $varname => $value) {
			if ($usesTypes) {
				[$value, ] = $value;
			}

			$this->assertTrue(isset($expectedBindings[$varname]));
			$this->assertSame($value, $expectedBindings[$varname]);
		}
	}
}