<?php

namespace Tests\Feature\Http\Pages\Numbers;

use App\Models\Account;
use App\Models\Customer;
use App\Models\Number;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Inertia\Testing\Assert;
use Tests\Feature\FeatureTestCase;

class IndexTest extends FeatureTestCase
{
    private User $user;

    private Collection $numbers;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->asAccountOwner()->create([
            'account_id' => Account::create(['name' => 'Acme Corporation'])->id,
            'first_name' => 'John',
            'last_name'  => 'Doe',
            'email'      => 'johndoe@example.com',
        ]);

        $customer = $this->user->customers()->save(Customer::factory()->make([
            'name'       => 'Example Customer Inc.',
            'account_id' => $this->user->account_id,
        ]));

        $this->numbers = $this->user->account->numbers()->saveMany(
            Number::factory()->count(2)->make(['customer_id' => $customer->id])
        );
        $this->numbers = $this->numbers->sortBy('updated_at');
    }

    public function test_can_view_numbers()
    {
        $this->actingAs($this->user)
            ->get(route('numbers.index'))
            ->assertInertia(
                fn ($assert) => $assert
                    ->component('Numbers/Index')
                    ->has('numbers.data', 2)
                    ->has(
                        'numbers.data.0',
                        fn (Assert $assert) => $assert
                            ->where('id', $this->numbers->first()->id)
                            ->where('number', $this->numbers->first()->number)
                            ->where('status', $this->numbers->first()->status)
                            ->where('deleted_at', null)
                            ->etc()
                            ->has(
                                'customer',
                                fn (Assert $assert) => $assert
                                    ->where('name', 'Example Customer Inc.')
                                    ->etc()
                            )
                    )
                    ->has(
                        'numbers.data.1',
                        fn (Assert $assert) => $assert
                            ->where('id', $this->numbers->last()->id)
                            ->where('number', $this->numbers->last()->number)
                            ->where('status', $this->numbers->last()->status)
                            ->where('deleted_at', null)
                            ->etc()
                            ->has(
                                'customer',
                                fn (Assert $assert) => $assert
                                    ->where('name', 'Example Customer Inc.')
                                    ->etc()
                            )
                    )
            );
    }

    public function test_cant_view_numbers_page_without_permission()
    {
        $this->actingAs(User::factory()->asAccountUser()->create())
            ->get(route('numbers.index'))
            ->assertForbidden();
    }

    public function test_can_view_numbers_page_as_an_user_with_permission()
    {
        $this->actingAs(User::factory()->asAccountUser('number.view')->create())
            ->get(route('numbers.index'))
            ->assertOk();
    }

    public function test_can_search_for_numbers()
    {
        $this->actingAs($this->user)
            ->get(route('numbers.index', ['search' => $this->numbers->first()->number]))
            ->assertInertia(
                fn (Assert $assert) => $assert
                    ->component('Numbers/Index')
                    ->where('filters.search', $this->numbers->first()->number)
                    ->has('numbers.data', 1)
                    ->has(
                        'numbers.data.0',
                        fn (Assert $assert) => $assert
                            ->where('id', $this->numbers->first()->id)
                            ->where('number', $this->numbers->first()->number)
                            ->where('status', $this->numbers->first()->status)
                            ->where('deleted_at', null)
                            ->etc()
                            ->has(
                                'customer',
                                fn (Assert $assert) => $assert
                                    ->where('name', 'Example Customer Inc.')
                                    ->etc()
                            )
                    )
            );
    }

    public function test_cannot_view_deleted_numbers()
    {
        $this->numbers->first()->delete();

        $this->actingAs($this->user)
            ->get(route('numbers.index'))
            ->assertInertia(
                fn (Assert $assert) => $assert
                    ->component('Numbers/Index')
                    ->has('numbers.data', 1)
                    ->where('numbers.data.0.number', $this->numbers->last()->number)
            );
    }

    public function test_can_filter_to_view_deleted_numbers()
    {
        $this->numbers->first()->delete();

        $this->actingAs($this->user)
            ->get(route('numbers.index', ['trashed' => 'with']))
            ->assertInertia(
                fn (Assert $assert) => $assert
                ->component('Numbers/Index')
                ->has('numbers.data', 2)
                ->where('numbers.data.0.number', $this->numbers->first()->number)
                ->where('numbers.data.1.number', $this->numbers->last()->number)
            );
    }
}
