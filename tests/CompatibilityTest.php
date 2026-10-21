<?php
namespace IDFocus\TinyQ;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class CompatibilityTest extends TestCase
{
    public static function comparisonValues(): iterable
    {
        $operators = ['equal' => '.e', 'notEqual' => '.n', 'greater' => '.g',
            'greaterOrEqual' => '.h', 'less' => '.l', 'lessOrEqual' => '.m', 'like' => '.k'];
        $values = ['null' => [null, ''], 'empty' => ['', ''], 'false' => [false, ''],
            'true' => [true, '1'], 'zero' => [0, '0'], 'integer' => [15, '15'],
            'decimal' => [12.5, '12..5'], 'unicode' => ['één.naam', 'één..naam']];
        foreach ($operators as $method => $operator) {
            foreach ($values as $label => [$value, $escaped]) {
                yield $method . '/' . $label => [$method, $operator, $value, $method === 'like' ? str_replace('..', '....', $escaped) : $escaped];
            }
        }
    }

    #[DataProvider('comparisonValues')]
    public function testComparisonValuesPreserveLegacyCoercion($method, $operator, $value, $escaped): void
    {
        $builder = new Builder();
        self::assertSame($builder, $builder->$method('profile.name', $value));
        self::assertSame('profile..name' . $operator . $escaped, $builder->build());
    }

    public static function likePatterns(): array
    {
        return [
            'unescaped wildcards' => ['some_text-', false, 'name.ksome_text-'],
            'explicit literals' => ['some_text-', true, 'name.ksome._text.-'],
            'dots and literals' => ['a.b_-', true, 'name.ka....b._.-'],
            'literal dot before underscore' => ['a._', true, 'name.ka....._'],
            'trailing dot' => ['a.', false, 'name.ka....'],
            'multiple dots' => ['a..b', false, 'name.ka........b'],
            'literal underscore with wildcard suffix' => ['some_text', true, 'name.ksome._text-', '-'],
            'literal dash with wildcard prefix' => ['some-text', true, 'name.k-some.-text', '', '-'],
        ];
    }

    #[DataProvider('likePatterns')]
    public function testLikeEscapingIsExplicit($value, $escape, $expected, $suffix = '', $prefix = ''): void
    {
        $builder = new Builder();
        $value = $escape ? $builder->escapeLike($value) : $value;
        self::assertSame($expected, $builder->like('name', $prefix . $value . $suffix)->build());
    }

    public function testNullEscapeLikeRetainsEmptyStringBehavior(): void
    {
        self::assertSame('', (new Builder())->escapeLike(null));
    }

    public function testNonLikeOperatorsKeepWildcardCharactersLiteral(): void
    {
        self::assertSame('name.ea_-', (new Builder())->equal('name', 'a_-')->build());
    }

    public function testResetAndGroupingRemainFluent(): void
    {
        $builder = new Builder();
        self::assertSame('.t.sname.ea.aage.h18.oage.m65.f',
            $builder->not()->lparen()->equal('name', 'a')->and()->greaterOrEqual('age', 18)
                ->or()->lessOrEqual('age', 65)->rparen()->build());
        self::assertSame($builder, $builder->reset());
        self::assertSame('', $builder->build());
    }
}
