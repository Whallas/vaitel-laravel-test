<?php

namespace Tests\Feature\Http\Pages\Numbers;

use App\Models\User;
use Inertia\Testing\Assert;
use Tests\Feature\FeatureTestCase;

class CreateTest extends FeatureTestCase
{
    public function test_can_view_create_number_page()
    {
        $this->actingAs(User::factory()->asAccountOwner()->create())
            ->get(route('numbers.create'))
            ->assertInertia(
                fn (Assert $assert) => $assert->component('Numbers/Create')
            );
    }

    public function test_cannot_view_create_number_page_without_permission()
    {
        $this->actingAs(User::factory()->asAccountUser()->create())
            ->get(route('numbers.create'))
            ->assertForbidden();
    }

    public function test_can_view_create_number_page_as_an_user_with_permissions()
    {
        $this->actingAs(
            User::factory()->asAccountUser(['number.view', 'number.create'])->create()
        )
            ->get(route('numbers.create'))
            ->assertOk();
    }
}
