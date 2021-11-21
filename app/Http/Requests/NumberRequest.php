<?php

namespace App\Http\Requests;

use App\Models\Number;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class NumberRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'number' => ['required', 'string', 'min:8', 'max:14'],
            'status' => ['required', Rule::in(Number::ALL_STATUSES)],
        ];

        if ($this->isMethod('post')) {
            $rules['customer_id'] = ['nullable', Rule::exists('customers', 'id')->where(function ($query) {
                $query->where('account_id', user()->account_id);
            })];
        }

        return $rules;
    }
}
