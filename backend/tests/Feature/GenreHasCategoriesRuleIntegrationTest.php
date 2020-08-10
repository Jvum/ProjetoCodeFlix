<?php


class GenreHasCategoriesRuleIntegrationTest extends \Tests\TestCase
{
    private $categories;
    private $genres;

    protected function setUp(): void
    {
        parent::setUp();
        $this->categories = factory(\App\Models\Category::class, 4)->create();
        $this->genres = factory(\App\Models\Genre::class, 2)->create();

        $this->genres[0]->categories()->sync([
            $this->categories[0]->id,
            $this->categories[1]->id
        ]);
        $this->genres[1]->categories()->sync(
            $this->categories[2]->id
        );
    }

    public function testPassesIsValid()
    {
        $rule =  new \App\Rules\GenresHasCategoriesRule(
            [
                $this->categories[2]->id,
            ]
        );
        $isValid = $rule->passes('', [
           $this->genres[1]->id,
        ]);
        $this->assertTrue($isValid);

        $rule = new \App\Rules\GenresHasCategoriesRule(
            [
                $this->categories[0]->id,
                $this->categories[2]->id
            ]
        );

        $isValid = $rule->passes('', [
            $this->genres[0]->id,
            $this->genres[1]->id,
        ]);
        $this->assertTrue($isValid);

        $rule = new \App\Rules\GenresHasCategoriesRule(
            [
                $this->categories[0]->id,
                $this->categories[1]->id,
                $this->categories[2]->id
            ]
        );

        $isValid = $rule->passes('',[
            $this->genres[0]->id,
            $this->genres[1]->id
        ]);
        $this->assertTrue($isValid);
    }

    public function testPassesIsNotValid()
    {
        $rule = new \App\Rules\GenresHasCategoriesRule(
            [
                $this->categories[0]->id
            ]
        );

        $isValid = $rule->passes('', [
            $this->genres[0]->id,
            $this->genres[1]->id
        ]);

        $this->assertFalse($isValid);
    }
}
