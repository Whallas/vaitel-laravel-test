<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Support\Arr;
use Tests\Feature\FeatureTestCase;

class CustomerControllerTest extends FeatureTestCase
{
    /**
     * @testWith ["account_owner"]
     *           ["account_user", ["customer.view", "customer.create"]]
     */
    public function test_store_customer(string $role, array | string $permissions = null)
    {
        $user         = User::factory()->as($role, $permissions)->create();
        $customerData = Arr::except(
            Customer::factory()->make()->toArray(),
            ['user_id', 'account_id']
        );

        $this->actingAs($user)
            ->post(route('customers.store', $customerData))
            ->assertRedirect();

        $this->assertDatabaseHas('customers', $customerData + [
            'user_id'    => $user->id,
            'account_id' => $user->account_id,
        ]);
    }

    public function test_cannot_store_customer_without_permission()
    {
        $user         = User::factory()->asAccountUser()->create();
        $customerData = Arr::except(
            Customer::factory()->make()->toArray(),
            ['user_id', 'account_id']
        );

        $this->actingAs($user)
            ->post(route('customers.store', $customerData))
            ->assertForbidden();
    }

    /**
     * @testWith ["account_owner"]
     *           ["account_user", ["customer.view", "customer.update"]]
     */
    public function test_update_customer(string $role, array | string $permissions = null)
    {
        $customer = Customer::factory()
            ->for(User::factory()->as($role, $permissions))
            ->create();

        $newCustomerData = Arr::except(
            Customer::factory()->make()->toArray(),
            ['user_id', 'account_id']
        );

        $this->actingAs($customer->user)
            ->put(route('customers.update', $customer), $newCustomerData)
            ->assertRedirect();

        $this->assertDatabaseHas(
            'customers',
            $newCustomerData + $customer->only(['id', 'user_id', 'account_id'])
        );
    }

    public function test_cannot_update_customer_without_permission()
    {
        $customer = Customer::factory()
            ->for(User::factory()->asAccountUser())
            ->create();

        $newCustomerData = Arr::except(
            Customer::factory()->make()->toArray(),
            ['user_id', 'account_id']
        );

        $this->actingAs($customer->user)
            ->put(route('customers.update', $customer), $newCustomerData)
            ->assertForbidden();
    }

    /**
     * @testWith ["account_owner"]
     *           ["account_user", ["customer.view", "customer.update"]]
     */
    public function test_destroy_customer(string $role, array | string $permissions = null)
    {
        $customer = Customer::factory()
            ->for(User::factory()->as($role, $permissions))
            ->create();

        $this->actingAs($customer->user)
            ->delete(route('customers.destroy', $customer))
            ->assertRedirect();

        $this->assertSoftDeleted('customers', $customer->only(['id']));
    }

    public function test_cannot_destroy_customer_without_permission()
    {
        $customer = Customer::factory()
            ->for(User::factory()->asAccountUser())
            ->create();

        $this->actingAs($customer->user)
            ->delete(route('customers.destroy', $customer))
            ->assertForbidden();
    }

    /**
     * @testWith ["account_owner"]
     *           ["account_user", ["customer.view", "customer.update"]]
     */
    public function test_restore_customer(string $role, array | string $permissions = null)
    {
        $customer = Customer::factory()
            ->for(User::factory()->as($role, $permissions))
            ->create(['deleted_at' => now()]);

        $this->actingAs($customer->user)
            ->put(route('customers.restore', $customer))
            ->assertRedirect();

        $this->assertDatabaseHas(
            'customers',
            $customer->only(['id']) + ['deleted_at' => null]
        );
    }

    public function test_cannot_restore_customer_without_permission()
    {
        $customer = Customer::factory()
            ->for(User::factory()->asAccountUser())
            ->create(['deleted_at' => now()]);

        $this->actingAs($customer->user)
            ->put(route('customers.restore', $customer))
            ->assertForbidden();
    }
}
