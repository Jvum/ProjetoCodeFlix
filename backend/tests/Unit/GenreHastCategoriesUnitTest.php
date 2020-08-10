<?php


class GenreHastCategoriesUnitTest extends TestCase
{
    public function testCategoriesIdField()
    {
        $rule = new \App\Rules\GenresHasCategoriesRule(
            [1, 1, 2, 2]
        );
        $reflectionClass = new ReflectionClass(\App\Rules\GenresHasCategoriesRule::class);
        $reflectionProperty = $reflectionClass->getProperty('categoriesId');
        $reflectionProperty->setAccessible(true);

        $categoriesId = $reflectionProperty->getValue($rule);
        $this->assertEqualsCanonicalizing([1,2], $categoriesId);
    }

    public function testGenresIdValue()
    {
        $rule = $this->createRuleMock([]);

        $rule
            ->shouldReceive('getRows')
            ->withAnyArgs()
            ->andReturnNull();
        $rule = new \App\Rules\GenresHasCategoriesRule([]);
        $rule->passes('',[1,1,2,2]);

        $reflectionClass = new ReflectionClass(\App\Rules\GenresHasCategoriesRule::class);
        $reflectionProperty = $reflectionClass->getProperty('genresId');
        $reflectionProperty->setAccessible(true);

        $genresId = $reflectionProperty->getValue($rule);
        $this->assertEqualsCanonicalizing([1,2], $genresId);
    }

    public function testPassesReturnsFalseWhenCategoriesOrGenresIsArrayEmpty()
    {
        $rule = $this->createRuleMock([1]);
        $this->assertFalse($rule->passes('', []));

        $rule = $this->createRuleMock([]);
        $this->assertFalse($rule->passes('', [1]));
    }

    public function testPassesReturnsFalseWhenGetRowsIsEmpty()
    {
        $rule = $this->createRuleMock([1]);
        $rule
            ->shouldReceive('getRows')
            ->withAnyArgs()
            ->andReturn(collect());
        $this->assertFalse($rule->passes('', [1]));
    }

    public function testPassesReturnsFalseWhenHasCategoriesWithoutGenres()
    {
        $rule = $this->createRuleMock([1,2]);
        $rule
            ->shouldReceive('getRows')
            ->withAnyArgs()
            ->andReturn(collect(['category_id' => 1]));
        $this->assertFalse($rule->passes('', [1]));
    }

    public function testPassesValidId()
    {
        $rule = $this->createRuleMock();
        $rule
            ->shouldReceive('getRows')
            ->withAnyArgs()
            ->andReturn(collect([
                ['category_id' => 1],
                ['category_id' => 2],
            ]));
        $this->assertTrue($rule->passes('',[1]));

        $rule = $this->createRuleMock();
        $rule
            ->shouldReceive('getRows')
            ->withAnyArgs()
            ->andReturn(collect([
                ['category_id' => 1],
                ['category_id' => 2],
                ['category_id' => 1],
                ['category_id' => 2],
            ]));
        $this->assertTrue($rule->passes('',[1]));
    }

    public function createRuleMock(array $categoriesId): \Mockery\MockInterface
    {
        return \Mockery::mock(\App\Rules\GenresHasCategoriesRule::class, [$categoriesId])
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();
    }

}
