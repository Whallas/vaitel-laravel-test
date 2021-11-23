<?php

namespace Tests\Feature\Http\Pages\Customers;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\Assert;
use Tests\Feature\FeatureTestCase;

class CreateTest extends FeatureTestCase
{
    use RefreshDatabase;

    public function test_can_view_create_customer_page()
    {
        $this->actingAs(User::factory()->asAccountOwner()->create())
            ->get(route('customers.create'))
            ->assertInertia(
                fn (Assert $assert) => $assert->component('Customers/Create')
            );
    }

    public function test_cannot_view_create_customer_page_without_permission()
    {
        $this->actingAs(User::factory()->asAccountUser()->create())
            ->get(route('customers.create'))
            ->assertForbidden();
    }

    public function test_can_view_create_customer_page_as_an_user_with_permissions()
    {
        $this->actingAs(
            User::factory()->asAccountUser(['customer.view', 'customer.create'])->create()
        )
            ->get(route('customers.create'))
            ->assertOk();
    }
}
