<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Collection $customers;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'account_id' => Account::create(['name' => 'Acme Corporation'])->id,
            'first_name' => 'John',
            'last_name'  => 'Doe',
            'email'      => 'johndoe@example.com',
            'owner'      => true,
        ]);

        $this->customers = $this->user->account->customers()->saveMany(
            Customer::factory()->count(2)->make([
                'user_id' => $this->user->id,
            ])
        );
    }

    public function test_can_view_customers()
    {
        $this->actingAs($this->user)
            ->get(route('customers.index'))
            ->assertInertia(fn ($assert) => $assert
                ->component('Customers/Index')
                ->has('customers.data', 2)
                ->has('customers.data.0', fn ($assert) => $assert
                    ->where('id', $this->customers->first()->id)
                    ->where('name', $this->customers->first()->name)
                    ->where('document', $this->customers->first()->document)
                    ->where('status', $this->customers->first()->status)
                    ->where('deleted_at', null)
                )
                ->has('customers.data.1', fn ($assert) => $assert
                    ->where('id', $this->customers->last()->id)
                    ->where('name', $this->customers->last()->name)
                    ->where('document', $this->customers->last()->document)
                    ->where('status', $this->customers->last()->status)
                    ->where('deleted_at', null)
                )
            );
    }

    public function test_can_search_for_customers()
    {
        $this->actingAs($this->user)
            ->get(route('customers.index', ['search' => $this->customers->first()->name]))
            ->assertInertia(fn ($assert) => $assert
                ->component('Customers/Index')
                ->where('filters.search', $this->customers->first()->name)
                ->has('customers.data', 1)
                ->has('customers.data.0', fn ($assert) => $assert
                    ->where('id', $this->customers->first()->id)
                    ->where('name', $this->customers->first()->name)
                    ->where('document', $this->customers->first()->document)
                    ->where('status', $this->customers->first()->status)
                    ->where('deleted_at', null)
                )
            );
    }

    public function test_cannot_view_deleted_customers()
    {
        $this->customers->first()->delete();

        $this->actingAs($this->user)
            ->get(route('customers.index'))
            ->assertInertia(fn ($assert) => $assert
                ->component('Customers/Index')
                ->has('customers.data', 1)
                ->where('customers.data.0.name', $this->customers->last()->name)
            );
    }

    public function test_can_filter_to_view_deleted_customers()
    {
        $this->customers->first()->delete();

        $this->actingAs($this->user)
            ->get(route('customers.index', ['trashed' => 'with']))
            ->assertInertia(fn ($assert) => $assert
                ->component('Customers/Index')
                ->has('customers.data', 2)
                ->where('customers.data.0.name', $this->customers->first()->name)
                ->where('customers.data.1.name', $this->customers->last()->name)
            );
    }
}
