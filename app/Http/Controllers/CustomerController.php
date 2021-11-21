<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerRequest;
use App\Models\Customer;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

class CustomerController extends Controller
{
    public function index()
    {
        return Inertia::render('Customers/Index', [
            'filters'   => Request::all('search', 'trashed'),
            'customers' => user()->account->customers()
                ->orderBy('name')
                ->filter(Request::only('search', 'trashed'))
                ->paginate(10)
                ->withQueryString(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Customers/Create');
    }

    public function store(CustomerRequest $request)
    {
        user()->account->customers()
            ->create(
                array_merge($request->validated(), ['user_id' => user()->id])
            );

        return Redirect::route('customers.index')->with('success', 'Customer created.');
    }

    public function edit(Customer $customer)
    {
        return Inertia::render('Customers/Edit', [
            'customer' => $customer->load([
                'numbers' => fn ($query) => $query->withTrashed()->orderBy('name'),
            ]),
        ]);
    }

    public function update(Customer $customer, CustomerRequest $request)
    {
        $customer->update($request->validated());

        return Redirect::back()->with('success', 'Customer updated.');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();

        return Redirect::back()->with('success', 'Customer deleted.');
    }

    public function restore(Customer $customer)
    {
        $customer->restore();

        return Redirect::back()->with('success', 'Customer restored.');
    }
}
