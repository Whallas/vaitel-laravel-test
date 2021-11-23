<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Number;
use Illuminate\Database\Eloquent\Factories\Factory;

class NumberFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Number::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'customer_id' => Customer::factory(),
            'account_id'  => fn ($attributes)  => Customer::find($attributes['customer_id'])->account_id,
            'number'      => $this->faker->e164PhoneNumber,
            'status'      => $this->faker->randomElement(Number::ALL_STATUSES),
        ];
    }
}
