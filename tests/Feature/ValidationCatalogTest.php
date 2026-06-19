<?php

namespace Tests\Feature;

use App\Models\ValidationProduct;
use App\Models\ValidationModule;
use App\Models\ValidationWorkflow;
use App\Models\ValidationTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\QueryException;
use Tests\TestCase;

class ValidationCatalogTest extends TestCase
{
    use RefreshDatabase;

    public function test_full_catalog_chain_resolves(): void
    {
        $product  = ValidationProduct::factory()->create();
        $module   = ValidationModule::factory()->create(['validation_product_id' => $product->id]);
        $workflow = ValidationWorkflow::factory()->create(['validation_module_id' => $module->id]);
        $testCase = ValidationTestCase::factory()->create(['validation_workflow_id' => $workflow->id]);

        $this->assertTrue($product->modules->contains($module));
        $this->assertTrue($module->workflows->contains($workflow));
        $this->assertTrue($workflow->testCases->contains($testCase));
        $this->assertEquals($product->id, $module->product->id);
        $this->assertEquals($workflow->id, $testCase->workflow->id);
    }

    public function test_module_code_unique_per_product(): void
    {
        $product = ValidationProduct::factory()->create();
        ValidationModule::factory()->create(['validation_product_id' => $product->id, 'code' => 'dupe']);

        $this->expectException(QueryException::class);
        ValidationModule::factory()->create(['validation_product_id' => $product->id, 'code' => 'dupe']);
    }
}
