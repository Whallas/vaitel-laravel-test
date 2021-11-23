<?php

namespace Tests\Feature\Http\Pages\Customers;

use App\Models\Customer;
use App\Models\User;
use Inertia\Testing\Assert;
use Tests\Feature\FeatureTestCase;

class EditTest extends FeatureTestCase
{
    public function test_can_view_edit_customer_page()
    {
        $user     = User::factory()->asAccountOwner()->create();
        $customer = Customer::factory()->create([
            'account_id' => $user->account_id,
            'user_id'    => $user->id,
        ]);

        $this->actingAs($user)
            ->get(route('customers.edit', $customer))
            ->assertInertia(
                fn (Assert $assert) => $assert->component('Customers/Edit')
                    ->where('customer.id', $customer->id)
                    ->where('customer.name', $customer->name)
            );
    }

    public function test_cannot_view_edit_customer_page_without_permission()
    {
        $user     = User::factory()->asAccountUser()->create();
        $customer = Customer::factory()->create([
            'account_id' => $user->account_id,
            'user_id'    => $user->id,
        ]);

        $this->actingAs($user)
            ->get(route('customers.edit', $customer))
            ->assertForbidden();
    }

    public function test_can_view_edit_customer_page_as_an_user_with_permissions()
    {
        $user     = User::factory()->asAccountUser(['customer.view', 'customer.update'])
            ->create();
        $customer = Customer::factory()->create([
            'account_id' => $user->account_id,
            'user_id'    => $user->id,
        ]);

        $this->actingAs($user)
            ->get(route('customers.edit', $customer))
            ->assertOk();
    }
}
