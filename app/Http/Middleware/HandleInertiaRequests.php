<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user(),
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
                'notify' => function () use ($request) {
                    $notify = $request->session()->get('notify');
                    if (! $notify) {
                        return null;
                    }

                    return [
                        'type' => $notify['type'] instanceof \BackedEnum ? $notify['type']->value : (string) ($notify['type'] ?? 'info'),
                        'model' => $notify['model'] instanceof \BackedEnum ? $notify['model']->value : (string) ($notify['model'] ?? 'toast'),
                        'title' => $notify['title'] ?? null,
                        'message' => $notify['message'] ?? null,
                        'icon' => $notify['icon'] ?? null,
                        'duration' => (int) ($notify['duration'] ?? config('notify.timeout', 5000)),
                        'actions' => $notify['actions'] ?? [],
                    ];
                },
            ],
        ];
    }
}
