<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Customer::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id'    => User::factory(),
            'account_id' => fn ($attributes) => User::find($attributes['user_id'])->account_id,
            'name'       => $this->faker->name,
            'document'   => str_replace('-', '', $this->faker->ssn),
            'status'     => $this->faker->randomElement(Customer::ALL_STATUSES),
        ];
    }
}
