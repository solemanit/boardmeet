<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
  /**
   * Handle an incoming request.
   *
   * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
   */
  public function handle(Request $request, Closure $next, ...$permissions): Response
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

    // Check if user has all the required permissions
    if (!$request->user()->hasAnyPermission($permissions)) {
      if ($request->expectsJson()) {
        return response()->json([
          'success' => false,
          'message' => 'Unauthorized: Insufficient permissions. Required: ' . implode(', ', $permissions),
        ], 403);
      }
      abort(403, 'Unauthorized. Insufficient permissions.');
    }

    return $next($request);
  }
}
