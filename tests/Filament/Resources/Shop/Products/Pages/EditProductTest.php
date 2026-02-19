<?php

use App\Filament\Resources\Shop\Products\Pages\EditProduct;
use App\Models\Shop\Product;
use App\Models\Shop\ProductCategory;
use Illuminate\Support\Str;
use Livewire\Livewire;

it('can render the edit page', function () {
    $record = Product::factory()->create();

    Livewire::test(EditProduct::class, ['record' => $record->getRouteKey()])
        ->assertOk();
});

it('can update a record', function () {
    $category = ProductCategory::factory()->create();
    $record = Product::factory()->create();
    $record->productCategories()->attach($category);
    $newData = Product::factory()->make();

    Livewire::test(EditProduct::class, ['record' => $record->getRouteKey()])
        ->fillForm([
            'name' => $newData->name,
            'price' => $newData->price,
        ])
        ->call('save')
        ->assertHasNoFormErrors()
        ->assertNotified();

    $this->assertDatabaseHas(Product::class, [
        'id' => $record->id,
        'name' => $newData->name,
    ]);
});

it('validates the form data', function (array $data, array $errors) {
    $category = ProductCategory::factory()->create();
    $record = Product::factory()->create();
    $record->productCategories()->attach($category);
    $newData = Product::factory()->make();

    Livewire::test(EditProduct::class, ['record' => $record->getRouteKey()])
        ->fillForm([
            'name' => $newData->name,
            'price' => $newData->price,
            ...$data,
        ])
        ->call('save')
        ->assertHasFormErrors($errors)
        ->assertNotNotified();
})->with([
    '`name` is required' => [['name' => null], ['name' => 'required']],
    '`name` is max 255 characters' => [['name' => Str::random(256)], ['name' => 'max']],
    '`price` is required' => [['price' => null], ['price' => 'required']],
    '`old_price` is required' => [['old_price' => null], ['old_price' => 'required']],
    '`cost` is required' => [['cost' => null], ['cost' => 'required']],
    '`sku` is required' => [['sku' => null], ['sku' => 'required']],
    '`barcode` is required' => [['barcode' => null], ['barcode' => 'required']],
    '`qty` is required' => [['qty' => null], ['qty' => 'required']],
    '`security_stock` is required' => [['security_stock' => null], ['security_stock' => 'required']],
    '`published_at` is required' => [['published_at' => null], ['published_at' => 'required']],
    '`productCategories` is required' => [['productCategories' => null], ['productCategories' => 'required']],
]);
