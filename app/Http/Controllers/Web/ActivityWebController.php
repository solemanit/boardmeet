<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class ActivityWebController extends Controller
{
  public function logs(Request $request)
  {
    $activities = auth()->user()->activityLogs()
      ->orderBy('created_at', 'desc')
      ->paginate(20);

    $totalCount = auth()->user()->activityLogs()->count();

    return view('activity.index', compact('activities', 'totalCount'));
  }

  public function index(Request $request)
  {
    $activities = auth()->user()->activityLogs()
      ->orderBy('created_at', 'desc')
      ->paginate(20);

    $totalCount = auth()->user()->activityLogs()->count();

    return view('activity.index', compact('activities', 'totalCount'));
  }

  public function userLogs(User $user)
  {
    $this->authorize('view', $user);

    $activities = $user->activityLogs()
      ->orderBy('created_at', 'desc')
      ->paginate(20);

    $totalCount = $user->activityLogs()->count();

    return view('activity.user', compact('user', 'activities', 'totalCount'));
  }
}
