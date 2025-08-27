<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        // APIリクエストの場合はリダイレクトしない
        if ($request->expectsJson() || $request->is('api/*')) {
            return null;
        }

        // Webリクエストの場合はログインページにリダイレクト
        return '/login';
    }

    /**
     * Handle an unauthenticated user.
     */
    protected function unauthenticated($request, array $guards)
    {
        if ($request->expectsJson() || $request->is('api/*')) {
            abort(response()->json([
                'success' => false,
                'message' => '認証が必要です。'
            ], 401));
        }

        parent::unauthenticated($request, $guards);
    }
}

