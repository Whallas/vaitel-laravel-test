<?php

namespace App\Http\Middleware;

use App\Models\Customer;
use App\Models\Number;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    public function version(Request $request)
    {
        return parent::version($request);
    }

    /**
     * Defines the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function share(Request $request)
    {
        return array_merge(parent::share($request), [
            'auth' => function () use ($request) {
                return [
                    'user' => $request->user() ? [
                        'id'         => $request->user()->id,
                        'first_name' => $request->user()->first_name,
                        'last_name'  => $request->user()->last_name,
                        'email'      => $request->user()->email,
                        'owner'      => $request->user()->is_account_owner,
                        'account'    => [
                            'id'   => $request->user()->account->id,
                            'name' => $request->user()->account->name,
                        ],
                        'canViewCustomers'      => user()->can('viewAny', Customer::class),
                        'canViewNumbers'        => user()->can('viewAny', Number::class),
                        'canManageAccountUsers' => user()->is_account_owner,
                    ] : null,
                ];
            },
            'flash' => function () use ($request) {
                return [
                    'success' => $request->session()->get('success'),
                    'error'   => $request->session()->get('error'),
                ];
            },
        ]);
    }
}
