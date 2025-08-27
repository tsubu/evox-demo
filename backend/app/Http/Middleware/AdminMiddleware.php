<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // セッションを開始
        if (!$request->session()->isStarted()) {
            $request->session()->start();
        }

        // セッションベース認証をチェック
        if (!Auth::guard('admin')->check()) {
            \Log::warning('Admin middleware: Not authenticated', [
                'url' => $request->url(),
                'session_id' => session()->getId(),
                'session_data' => session()->all()
            ]);

            // APIリクエストの場合はJSONレスポンスを返す
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => '認証が必要です。'
                ], 401);
            }

            // Webリクエストの場合はログインページにリダイレクト
            return redirect('/admin-' . env('ADMIN_URL_HASH', 'evox2025'));
        }

        \Log::info('Admin middleware: Authentication successful', [
            'admin_id' => Auth::guard('admin')->id(),
            'admin_name' => Auth::guard('admin')->user()->admin_name,
            'url' => $request->url(),
            'session_id' => session()->getId()
        ]);

        return $next($request);
    }
}
