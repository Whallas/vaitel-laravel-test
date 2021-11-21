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
        return Inertia::render('Numbers/Index', [
            'filters' => Request::all('search', 'trashed'),
            'numbers' => user()->account->numbers()
                ->with(['customer' => fn ($query) => $query->select('id', 'name')])
                ->orderByName()
                ->filter(Request::only('search', 'trashed'))
                ->paginate(10)
                ->withQueryString(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Numbers/Create', [
            'customers' => user()->account
                ->customers()
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function store(NumberRequest $request)
    {
        user()->account->numbers()->create($request->validated());

        return Redirect::route('numbers.index')->with('success', 'Number created.');
    }

    public function edit(Number $number)
    {
        return Inertia::render('Numbers/Edit', [
            'number' => $number->load(['customer' => fn ($query) => $query->select('id', 'name')]),
        ]);
    }

    public function update(Number $number, NumberRequest $request)
    {
        $number->update($request->validated());

        return Redirect::back()->with('success', 'Number updated.');
    }

    public function destroy(Number $number)
    {
        $number->delete();

        return Redirect::back()->with('success', 'Number deleted.');
    }

    public function restore(Number $number)
    {
        $number->restore();

        return Redirect::back()->with('success', 'Number restored.');
    }
}
