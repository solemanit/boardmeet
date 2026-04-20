<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Circular;
use App\Models\Meeting;
use App\Models\MeetingAgendaMemo;
use App\Models\Member;
use Illuminate\Support\Facades\DB;

class DashboardWebController extends Controller
{
    public function index()
    {
        $user   = auth()->user();
        $userId = $user->id;
        $year   = now()->year;

        $stats = [
            'total'               => Meeting::count(),
            'scheduled'           => Meeting::where('status', 'scheduled')->count(),
            'ongoing'             => Meeting::where('status', 'ongoing')->count(),
            'completed'           => Meeting::whereYear('date', $year)->where('status', 'completed')->count(),
            'cancelled'           => Meeting::whereYear('date', $year)->where('status', 'cancelled')->count(),
            'active_members'      => Member::where('status', 'active')->count(),
            'inactive_members'    => Member::where('status', 'inactive')->count(),
            'total_members'       => Member::count(),
            'published_circulars' => Circular::where('status', 'published')->count(),
            'draft_circulars'     => Circular::where('status', 'draft')->count(),
            'total_circulars'     => Circular::count(),
        ];

        $upcomingQuery = Meeting::with(['chairperson'])
            ->whereIn('status', ['scheduled', 'ongoing'])
            ->whereBetween('date', [now()->startOfDay(), now()->addDays(30)->endOfDay()])
            ->orderBy('date')
            ->orderBy('start_time');

        if (! $user->hasAnyRole(['admin', 'md'])) {
            $upcomingQuery->where(function ($q) use ($userId) {
                $q->where('created_by', $userId)
                  ->orWhereHas('attendees', fn ($a) => $a->where('user_id', $userId));
            });
        }

        $upcomingMeetings = $upcomingQuery->limit(8)->get();

        $meetingsByType = Meeting::whereYear('date', $year)
            ->select('type', DB::raw('count(*) as total'))
            ->groupBy('type')
            ->pluck('total', 'type')
            ->toArray();

        $recentCirculars = Circular::latest('issued_date')->limit(6)->get();

        $myPendingVotes = MeetingAgendaMemo::with(['section.meeting'])
            ->whereIn('vote_required', ['required', 'optional'])
            ->whereHas('section.meeting', function ($q) use ($userId) {
                $q->whereIn('status', ['scheduled', 'ongoing'])
                  ->whereHas('attendees', fn ($a) => $a->where('user_id', $userId));
            })
            ->whereDoesntHave('votes', fn ($v) => $v->where('user_id', $userId))
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        $recentActivity = collect();

        $hour     = now()->hour;
        $greeting = match (true) {
            $hour < 12 => 'morning',
            $hour < 17 => 'afternoon',
            default    => 'evening',
        };

        return view('dashboard.index', compact(
            'stats',
            'upcomingMeetings',
            'meetingsByType',
            'recentCirculars',
            'myPendingVotes',
            'recentActivity',
            'greeting',
        ));
    }
}
