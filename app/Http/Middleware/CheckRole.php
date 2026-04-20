<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
  /**
   * Handle an incoming request.
   *
   * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
   */
  public function handle(Request $request, Closure $next, ...$roles): Response
  {
    if (!$request->user()) {
      if ($request->expectsJson()) {
        return response()->json([
          'success' => false,
          'message' => 'Unauthenticated',
        ], 401);
      }
      return redirect()->route('login');
    }

    // Check if user is active
    if (!$request->user()->is_active) {
      if ($request->expectsJson()) {
        return response()->json([
          'success' => false,
          'message' => 'User account is not activated',
        ], 403);
      }
      return redirect()->route('dashboard.index')->with('error', 'Your account is not activated');
    }

    // Check if user has one of the required roles
    $userRole = $request->user()->role;
    if (!$userRole || !in_array($userRole->slug, $roles)) {
      if ($request->expectsJson()) {
        return response()->json([
          'success' => false,
          'message' => 'Unauthorized: Required role(s): ' . implode(', ', $roles),
        ], 403);
      }
      abort(403, 'Unauthorized. Required role(s): ' . implode(', ', $roles));
    }

    return $next($request);
  }
}
