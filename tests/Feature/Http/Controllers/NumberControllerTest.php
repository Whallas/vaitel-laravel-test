<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Number;
use App\Models\User;
use Illuminate\Support\Arr;
use Tests\Feature\FeatureTestCase;

class NumberControllerTest extends FeatureTestCase
{
    /**
     * @testWith ["account_owner"]
     *           ["account_user", ["number.view", "number.create"]]
     */
    public function test_store_number(string $role, array | string $permissions = null)
    {
        /** @var Number $number */
        $number     = Number::factory()->make();
        $numberData = $number->withoutRelations()->toArray();
        $user       = $number->customer->user;
        $user->syncRoles($role)->syncPermissions($permissions);

        $this->actingAs($user)
            ->post(route('numbers.store', $numberData))
            ->assertRedirect();

        $this->assertDatabaseHas('numbers', $numberData);
    }

    public function test_cannot_store_number_without_permission()
    {
        /** @var Number $number */
        $number     = Number::factory()->make();
        $user       = $number->customer->user;
        $user->syncRoles('account_user')->syncPermissions([]);

        $this->actingAs($user)
            ->post(route('numbers.store', $number->withoutRelations()->toArray()))
            ->assertForbidden();
    }

    /**
     * @testWith ["account_owner"]
     *           ["account_user", ["number.view", "number.update"]]
     */
    public function test_update_number(string $role, array | string $permissions = null)
    {
        /** @var Number $number */
        $number     = Number::factory()->create();
        $user       = $number->customer->user;
        $user->syncRoles($role)->syncPermissions($permissions);

        $newNumberData = Arr::except(
            Number::factory()->make()->toArray(),
            ['account_id', 'customer_id']
        );

        $this->actingAs($user)
            ->put(route('numbers.update', $number), $newNumberData)
            ->assertRedirect();

        $this->assertDatabaseHas(
            'numbers',
            array_merge($newNumberData, $number->only(['id', 'account_id', 'customer_id']))
        );
    }

    public function test_cannot_update_number_without_permission()
    {
        /** @var Number $number */
        $number     = Number::factory()->create();
        $user       = $number->customer->user;
        $user->syncRoles('account_user')->syncPermissions([]);

        $newNumberData = Arr::except(
            Number::factory()->make()->toArray(),
            ['customer_id', 'account_id']
        );

        $this->actingAs($user)
            ->put(route('numbers.update', $number), $newNumberData)
            ->assertForbidden();
    }

    /**
     * @testWith ["account_owner"]
     *           ["account_user", ["number.view", "number.update"]]
     */
    public function test_destroy_number(string $role, array | string $permissions = null)
    {
        /** @var Number $number */
        $number     = Number::factory()->create();
        $user       = $number->customer->user;
        $user->syncRoles($role)->syncPermissions($permissions);

        $this->actingAs($user)
            ->delete(route('numbers.destroy', $number))
            ->assertRedirect();

        $this->assertSoftDeleted('numbers', $number->only(['id']));
    }

    public function test_cannot_destroy_number_without_permission()
    {
        /** @var Number $number */
        $number     = Number::factory()->create();
        $user       = $number->customer->user;
        $user->syncRoles('account_user')->syncPermissions([]);

        $this->actingAs($user)
            ->delete(route('numbers.destroy', $number))
            ->assertForbidden();
    }

    /**
     * @testWith ["account_owner"]
     *           ["account_user", ["number.view", "number.update"]]
     */
    public function test_restore_number(string $role, array | string $permissions = null)
    {
        /** @var Number $number */
        $number     = Number::factory()->create(['deleted_at' => now()]);
        $user       = $number->customer->user;
        $user->syncRoles($role)->syncPermissions($permissions);

        $this->actingAs($user)
            ->put(route('numbers.restore', $number))
            ->assertRedirect();

        $this->assertDatabaseHas(
            'numbers',
            $number->only(['id']) + ['deleted_at' => null]
        );
    }

    public function test_cannot_restore_number_without_permission()
    {
        /** @var Number $number */
        $number     = Number::factory()->create(['deleted_at' => now()]);
        $user       = $number->customer->user;
        $user->syncRoles('account_user')->syncPermissions([]);

        $this->actingAs($user)
            ->put(route('numbers.restore', $number))
            ->assertForbidden();
    }
}
