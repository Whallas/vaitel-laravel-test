<?php

namespace App\Http\Controllers;

use App\Http\Requests\NumberRequest;
use App\Models\Number;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

class NumberController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Number::class);

        return Inertia::render('Numbers/Index', [
            'filters' => Request::all('search', 'trashed'),
            'numbers' => user()->account->numbers()
                ->with(['customer' => fn ($query) => $query->select('id', 'name')])
                ->orderBy('updated_at')
                ->filter(Request::only('search', 'trashed'))
                ->paginate(10)
                ->withQueryString()
                ->through(
                    fn ($number) => $number
                        ->setAttribute('can_i_edit', user()->can('update', $number))
                ),
            'canCreate' => user()->can('create', Number::class),
        ]);
    }

    public function create()
    {
        $this->authorize('create', Number::class);

        return Inertia::render('Numbers/Create', [
            'customers'  => user()->account
                ->customers()
                ->orderBy('name')
                ->get(),
            'customerId' => request('customer_id'),
        ]);
    }

    public function store(NumberRequest $request)
    {
        $this->authorize('create', Number::class);
        $number = user()->account->numbers()->create($request->validated());

        return Redirect::route('numbers.edit', $number)->with('success', 'Number created.');
    }

    public function edit(Number $number)
    {
        $this->authorize('update', $number);

        return Inertia::render('Numbers/Edit', [
            'number' => $number->load([
                'customer' => fn ($query) => $query->select('id', 'name'),
                'preferences',
            ]),
        ]);
    }

    public function update(Number $number, NumberRequest $request)
    {
        $this->authorize('update', $number);
        $number->update($request->validated());

        return Redirect::back()->with('success', 'Number updated.');
    }

    public function destroy(Number $number)
    {
        $this->authorize('delete', $number);
        $number->delete();

        return Redirect::back()->with('success', 'Number deleted.');
    }

    public function restore(Number $number)
    {
        $this->authorize('restore', $number);
        $number->restore();

        return Redirect::back()->with('success', 'Number restored.');
    }
}
