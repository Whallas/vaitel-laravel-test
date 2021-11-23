<?php

namespace Tests\Feature\Http\Pages\Numbers;

use App\Models\Number;
use App\Models\User;
use Inertia\Testing\Assert;
use Tests\Feature\FeatureTestCase;

class EditTest extends FeatureTestCase
{
    public function test_can_view_edit_number_page()
    {
        $user   = User::factory()->asAccountOwner()->create();
        $number = Number::factory()->create([
            'account_id' => $user->account_id,
        ]);

        $this->actingAs($user)
            ->get(route('numbers.edit', $number))
            ->assertInertia(
                fn (Assert $assert) => $assert->component('Numbers/Edit')
                    ->where('number.id', $number->id)
                    ->where('number.number', $number->number)
                    ->where('number.customer.id', $number->customer->id)
                    ->where('number.customer.name', $number->customer->name)
            );
    }

    public function test_cannot_view_edit_number_page_without_permission()
    {
        $user   = User::factory()->asAccountUser()->create();
        $number = Number::factory()->create([
            'account_id' => $user->account_id,
        ]);

        $this->actingAs($user)
            ->get(route('numbers.edit', $number))
            ->assertForbidden();
    }

    public function test_can_view_edit_number_page_as_an_user_with_permissions()
    {
        $user   = User::factory()->asAccountUser(['number.view', 'number.update'])
            ->create();
        $number = Number::factory()->create([
            'account_id' => $user->account_id,
        ]);

        $this->actingAs($user)
            ->get(route('numbers.edit', $number))
            ->assertOk();
    }
}
