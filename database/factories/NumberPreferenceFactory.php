<?php

namespace Database\Factories;

use App\Models\NumberPreference;
use Illuminate\Database\Eloquent\Factories\Factory;

class NumberPreferenceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = NumberPreference::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name'  => $this->faker->randomElement(['auto_attendant', 'voicemail_enabled']),
            'value' => '1',
        ];
    }
}
