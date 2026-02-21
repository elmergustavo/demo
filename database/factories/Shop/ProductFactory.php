<?php

namespace Database\Factories\Shop;

use App\Models\Shop\Product;
use Database\Seeders\LocalImages;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * @var string
     */
    protected $model = Product::class;

    /** @var list<string> */
    protected static array $modifiers = [
        'Alpine', 'Artisan', 'Bamboo', 'Carbon', 'Cedar', 'Ceramic', 'Classic',
        'Coastal', 'Copper', 'Cotton', 'Diamond', 'Elite', 'Ember', 'Glacier',
        'Granite', 'Heritage', 'Indigo', 'Iron', 'Jade', 'Linen', 'Maple',
        'Marine', 'Merino', 'Nordic', 'Oak', 'Onyx', 'Pacific', 'Pine',
        'Platinum', 'Porto', 'Premium', 'Quartz', 'Raven', 'Rustic', 'Sierra',
        'Slate', 'Solar', 'Sterling', 'Summit', 'Terra', 'Timber', 'Titanium',
        'Urban', 'Venture', 'Vintage', 'Walnut', 'Zenith',
    ];

    /** @var list<string> */
    protected static array $products = [
        'Backpack', 'Baking Mat', 'Belt', 'Blanket', 'Bluetooth Speaker',
        'Bottle Opener', 'Candle Set', 'Card Holder', 'Carafe', 'Charging Pad',
        'Chef Knife', 'Coaster Set', 'Coffee Mug', 'Cutting Board', 'Desk Lamp',
        'Desk Organizer', 'Duffel Bag', 'Flashlight', 'French Press', 'Garden Kit',
        'Hammock', 'Headphones', 'Hiking Boots', 'Hoodie', 'Jump Rope',
        'Kitchen Scale', 'Laptop Stand', 'Lunch Box', 'Notebook', 'Phone Mount',
        'Pillow', 'Running Shoes', 'Serving Board', 'Skillet', 'Socks',
        'Sunglasses', 'T-Shirt', 'Thermos', 'Tote Bag', 'Travel Mug',
        'Tumbler', 'Umbrella', 'Vase', 'Wallet', 'Water Bottle',
        'Wine Tote', 'Wristwatch', 'Yoga Mat',
    ];

    /** @var array<string, true> */
    protected static array $usedNames = [];

    protected function generateUniqueName(): string
    {
        do {
            $name = $this->faker->randomElement(static::$modifiers) . ' ' . $this->faker->randomElement(static::$products);
        } while (isset(static::$usedNames[$name]));

        static::$usedNames[$name] = true;

        return $name;
    }

    public function definition(): array
    {
        return [
            'name' => $name = $this->generateUniqueName(),
            'slug' => Str::slug($name),
            'sku' => $this->faker->unique()->ean8(),
            'barcode' => $this->faker->ean13(),
            'description' => $this->faker->realText(),
            'old_price' => $price = $this->faker->randomFloat(2, 5, 500),
            'price' => round($price * $this->faker->randomFloat(2, 0.7, 1.0), 2),
            'cost' => round($price * $this->faker->randomFloat(2, 0.3, 0.6), 2),
            // Inverse correlation: cheap products have more stock, expensive ones less
            'qty' => max(1, (int) round((500 - $price) / 5 + $this->faker->numberBetween(-10, 10))),
            'security_stock' => $this->faker->randomDigitNotNull(),
            'featured' => $this->faker->boolean(),
            'is_visible' => $this->faker->boolean(),
            'type' => $this->faker->randomElement(['deliverable', 'downloadable']),
            'published_at' => $this->faker->dateTimeBetween('-1 year', '+1 year'),
            'created_at' => $this->faker->dateTimeBetween('-1 year', '-6 month'),
            'updated_at' => $this->faker->dateTimeBetween('-5 month', 'now'),
        ];
    }

    public function configure(): ProductFactory
    {
        return $this->afterCreating(function (Product $product): void {
            $product
                ->addMedia(LocalImages::getRandomFile(LocalImages::SIZE_200x200))
                ->preservingOriginal()
                ->toMediaCollection('product-images');
        });
    }
}
