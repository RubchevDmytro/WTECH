<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            \Log::warning('AdminMiddleware: User not authenticated', [
                'path' => $request->path(),
            ]);
            return redirect('/login')->with('error', 'Please log in to access the admin panel.');
        }

        $user = auth()->user();

        if (!$user->is_admin) {
            return redirect('/')->with('error', 'You do not have admin access.');
        }

        return $next($request);
    }}
