<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Customer;
use App\Models\Number;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $account = Account::query()->firstOrCreate(['name' => 'Acme Corporation']);

        $user = User::query()->firstOrCreate(
            ['email' => 'johndoe@example.com'],
            User::factory()->raw([
                'account_id' => $account->id,
                'first_name' => 'John',
                'last_name'  => 'Doe',
                'owner'      => true,
            ])
        );

        User::factory(5)->create(['account_id' => $account->id]);

        $customers = Customer::factory(10)
            ->create([
                'account_id' => $account->id,
                'user_id'    => $user->id,
            ]);

        Number::factory(30)
            ->create([
                'account_id'  => $account->id,
                'customer_id' => fn () => $customers->random()->id,
            ]);
    }
}
