<?php
declare(strict_types=1);

namespace Tests\Traits;

use Illuminate\Foundation\Testing\TestResponse;

trait TestValidations
{
    protected abstract function model();
    protected abstract function routeStore();
    protected abstract function routeUpdate();
    
    protected function assetInvalidationInStoreAction(array $data, string $rule, $ruleParams = [])
    {
        $response = $this->json('POST', $this->routeStore(), []);
        $fields = array_keys($data);
        $this->assertInvalidationFields($response, $fields, $rule, $ruleParams);
    }

    protected function assetInvalidationInUpdateAction(array $data, string $rule, $ruleParams = [])
    {
        $response = $this->json('PUT', $this->routeUpdate(), []);
        $fields = array_keys($data);
        $this->assertInvalidationFields($response, $fields, $rule, $ruleParams);
    }

    protected function assertInvalidationFields(TestResponse $response, array $fields, string $rule, array $ruleParams = [])
    {
        $response->assertStatus(422)->assertJsonValidationErros($fields);

        foreach($fields as $field)
        {
            $fieldName = str_replace('_', ' ', $field);
            $response->assertJsonFragment([
                \Lang::trans("validation.{$rule}", ['attribute' => $fieldName] + $ruleParams)
            ])
        }
    }
}