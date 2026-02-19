<?php

use App\Enums\OrderStatus;
use App\Filament\Resources\Shop\Orders\Pages\CreateOrder;
use App\Models\Shop\Customer;
use App\Models\Shop\Order;
use App\Models\Shop\Product;
use Filament\Forms\Components\Repeater;
use Livewire\Livewire;

it('can render the create page', function () {
    Livewire::test(CreateOrder::class)
        ->assertOk();
});

it('can create a record', function () {
    $undoRepeaterFake = Repeater::fake();

    $customer = Customer::factory()->create();
    $product = Product::factory()->create();

    Livewire::test(CreateOrder::class)
        ->fillForm([
            'customer_id' => $customer->id,
            'status' => OrderStatus::New,
            'currency' => 'usd',
            'items' => [
                [
                    'product_id' => $product->id,
                    'qty' => 2,
                    'unit_price' => $product->price,
                ],
            ],
        ])
        ->call('create')
        ->assertHasNoFormErrors()
        ->assertNotified();

    $this->assertDatabaseHas(Order::class, ['customer_id' => $customer->id]);

    $undoRepeaterFake();
});

it('sends database notification after order creation', function () {
    $undoRepeaterFake = Repeater::fake();

    $customer = Customer::factory()->create();
    $product = Product::factory()->create();

    Livewire::test(CreateOrder::class)
        ->fillForm([
            'customer_id' => $customer->id,
            'status' => OrderStatus::New,
            'currency' => 'usd',
            'items' => [
                [
                    'product_id' => $product->id,
                    'qty' => 2,
                    'unit_price' => $product->price,
                ],
            ],
        ])
        ->call('create')
        ->assertHasNoFormErrors()
        ->assertNotified();

    $user = auth()->user();
    expect($user->notifications)->toHaveCount(1);
    expect($user->notifications->first()->data['title'])->toBe('New order');

    $undoRepeaterFake();
});

it('validates the form data', function (array $data, array $errors) {
    $customer = Customer::factory()->create();

    Livewire::test(CreateOrder::class)
        ->fillForm([
            'customer_id' => $customer->id,
            'status' => OrderStatus::New,
            'currency' => 'usd',
            ...$data,
        ])
        ->call('create')
        ->assertHasFormErrors($errors)
        ->assertNotNotified()
        ->assertNoRedirect();
})->with([
    '`customer_id` is required' => [['customer_id' => null], ['customer_id' => 'required']],
    '`status` is required' => [['status' => null], ['status' => 'required']],
    '`currency` is required' => [['currency' => null], ['currency' => 'required']],
]);
