<?php

namespace App\Http\Controllers;

use App\Http\Requests\NumberPreferenceRequest;
use App\Models\NumberPreference;
use Illuminate\Support\Facades\Redirect;

class NumberPreferenceController extends Controller
{
    public function store(NumberPreferenceRequest $request)
    {
        $this->authorize('create', NumberPreference::class);
        NumberPreference::create($request->validated());

        return Redirect::back()->with('success', 'Number preference created.');
    }

    public function update(NumberPreference $numberPreference, NumberPreferenceRequest $request)
    {
        $this->authorize('update', $numberPreference);
        $numberPreference->update($request->validated());

        return Redirect::back()->with('success', 'Number preference updated.');
    }

    public function destroy(NumberPreference $numberPreference)
    {
        $this->authorize('delete', $numberPreference);
        $numberPreference->delete();

        return Redirect::back()->with('success', 'Number preference deleted.');
    }
}
