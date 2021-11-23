<?php

namespace Tests\Feature\Listeners;

use App\Events\NumberCreated;
use App\Listeners\CreateInitialNumberPreferences;
use App\Models\Number;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class CreateInitialNumberPreferencesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_should_be_subscribed_to_the_number_created_event()
    {
        Event::fake()
            ->assertListening(NumberCreated::class, CreateInitialNumberPreferences::class);
    }

    /** @test */
    public function it_should_create_the_initial_two_number_preferences()
    {
        $number  = Number::factory()->create();

        $this->assertDatabaseHas('number_preferences', [
            'number_id' => $number->id,
            'name'      => 'auto_attendant',
            'value'     => '1',
        ]);

        $this->assertDatabaseHas('number_preferences', [
            'number_id' => $number->id,
            'name'      => 'auto_attendant',
            'value'     => '1',
        ]);
    }
}
