<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            //'auth' => [
                'user' => function () use ($request) {
                    if ($user = $request->user()) {
                        return [
                            //'user'=> $user, // (para enviar todo la información de user)
                            'id' => $user->id,
                            'name' => $user->name,
                            'email' => $user->email,
                            'profile_photo_path' => $user->profile_photo_path,
                            'status' => $user->status,
                            //'roles' => $user->roles->pluck('name'), //(para enviar todos lo roles)
                            'isAdmin' => $user->hasRole('Admin'),
                        ];
                    }
                },
            //],
        ]);
    }
}
