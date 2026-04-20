<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Agenda;
use App\Models\Meeting;
use App\Models\MeetingAttendee;
use App\Models\Member;
use App\Models\User;
use App\Models\UserActivityLog;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportWebController extends Controller
{
    // ── Index ────────────────────────────────────────────────────────────────

    public function index()
    {
        $stats = [
            'total_meetings'  => Meeting::count(),
            'total_members'   => Member::count(),
            'total_agendas'   => Agenda::count(),
            'pending_agendas' => Agenda::where('status', 'pending_review')->count(),
        ];

        return view('reports.index', compact('stats'));
    }

    // ── 1. Meeting Report ────────────────────────────────────────────────────

    public function meetings(Request $request)
    {
        $query = Meeting::with(['chairperson', 'secretary', 'attendees'])
            ->withCount('attendees');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        $meetings = $query->orderByDesc('date')->get();

        $summary = [
            'total'     => $meetings->count(),
            'scheduled' => $meetings->where('status', 'scheduled')->count(),
            'completed' => $meetings->where('status', 'completed')->count(),
            'cancelled' => $meetings->where('status', 'cancelled')->count(),
        ];

        if ($request->export === 'pdf') {
            $pdf = Pdf::loadView('reports.pdf.meetings', compact('meetings', 'summary', 'request'))
                ->setPaper('a4', 'landscape');
            return $pdf->download('meeting-report-' . now()->format('Ymd') . '.pdf');
        }

        return view('reports.meetings', compact('meetings', 'summary'));
    }

    // ── 2. Attendance Report ─────────────────────────────────────────────────

    public function attendance(Request $request)
    {
        $query = Meeting::with([
            'attendees.user',
            'chairperson',
        ])->withCount([
            'attendees',
            'attendees as present_count' => fn ($q) => $q->where('status', 'present'),
            'attendees as absent_count'  => fn ($q) => $q->where('status', 'absent'),
        ]);

        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $meetings = $query->orderByDesc('date')->get();

        // Per-user attendance summary
        $userAttendance = MeetingAttendee::select('user_id',
                DB::raw('COUNT(*) as total_invited'),
                DB::raw("SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) as total_present"),
                DB::raw("SUM(CASE WHEN status = 'absent'  THEN 1 ELSE 0 END) as total_absent")
            )
            ->with('user:id,name,email')
            ->groupBy('user_id')
            ->get()
            ->map(function ($row) {
                $row->attendance_rate = $row->total_invited > 0
                    ? round(($row->total_present / $row->total_invited) * 100, 1)
                    : 0;
                return $row;
            })
            ->sortByDesc('attendance_rate');

        if ($request->export === 'pdf') {
            $pdf = Pdf::loadView('reports.pdf.attendance', compact('meetings', 'userAttendance', 'request'))
                ->setPaper('a4', 'landscape');
            return $pdf->download('attendance-report-' . now()->format('Ymd') . '.pdf');
        }

        return view('reports.attendance', compact('meetings', 'userAttendance'));
    }

    // ── 3. Minutes / Extract Report ──────────────────────────────────────────

    public function minutes(Request $request)
    {
        $meetingId = $request->meeting_id;

        $meetings = Meeting::where('status', 'completed')
            ->orderByDesc('date')
            ->get(['id', 'meeting_number', 'title', 'date']);

        $meeting = null;
        $agendaItems = collect();

        if ($meetingId) {
            $meeting = Meeting::with([
                'agendaItems',
                'attendees.user',
                'chairperson',
                'secretary',
            ])->findOrFail($meetingId);

            $agendaItems = $meeting->agendaItems()->orderBy('order')->get();
        }

        if ($request->export === 'pdf' && $meeting) {
            $pdf = Pdf::loadView('reports.pdf.minutes', compact('meeting', 'agendaItems'))
                ->setPaper('a4', 'portrait');
            return $pdf->download("minutes-{$meeting->meeting_number}.pdf");
        }

        return view('reports.minutes', compact('meetings', 'meeting', 'agendaItems'));
    }

    // ── 4. Department-wise Report ────────────────────────────────────────────

    public function departments(Request $request)
    {
        $departmentStats = Member::select('department',
                DB::raw('COUNT(*) as total'),
                DB::raw("SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active_count"),
                DB::raw("SUM(CASE WHEN status = 'inactive' THEN 1 ELSE 0 END) as inactive_count")
            )
            ->groupBy('department')
            ->orderBy('department')
            ->get();

        $members = Member::orderBy('department')->orderBy('name')->get();

        $byDepartment = $members->groupBy('department');

        if ($request->export === 'pdf') {
            $pdf = Pdf::loadView('reports.pdf.departments', compact('departmentStats', 'byDepartment'))
                ->setPaper('a4', 'portrait');
            return $pdf->download('department-report-' . now()->format('Ymd') . '.pdf');
        }

        return view('reports.departments', compact('departmentStats', 'byDepartment'));
    }

    // ── 5. Member Activity Report ────────────────────────────────────────────

    public function memberActivity(Request $request)
    {
        $query = Member::with(['createdBy'])
            ->withCount([
                // You can extend this when meetings link to members
            ]);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('department')) {
            $query->where('department', $request->department);
        }

        $members = $query->orderBy('name')->get();

        $departments = Member::distinct()->pluck('department')->filter()->sort()->values();

        // Attendance cross-reference by email (member <-> user)
        $attendanceSummary = MeetingAttendee::select(
                'users.name',
                'users.email',
                DB::raw('COUNT(*) as total_meetings'),
                DB::raw("SUM(CASE WHEN meeting_attendees.status = 'present' THEN 1 ELSE 0 END) as attended")
            )
            ->join('users', 'users.id', '=', 'meeting_attendees.user_id')
            ->groupBy('users.id', 'users.name', 'users.email')
            ->get()
            ->keyBy('email');

        if ($request->export === 'pdf') {
            $pdf = Pdf::loadView('reports.pdf.member-activity', compact('members', 'attendanceSummary', 'request'))
                ->setPaper('a4', 'portrait');
            return $pdf->download('member-activity-' . now()->format('Ymd') . '.pdf');
        }

        return view('reports.member-activity', compact('members', 'attendanceSummary', 'departments'));
    }

    // ── 6. Audit Trail ───────────────────────────────────────────────────────

    public function auditTrail(Request $request)
    {
        $query = UserActivityLog::with('user:id,name,email')
            ->latest();

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('action')) {
            $query->where('action', 'like', "%{$request->action}%");
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->paginate(50)->withQueryString();

        $users = User::orderBy('name')->get(['id', 'name', 'email']);

        if ($request->export === 'pdf') {
            // Export only current filtered page (avoid huge PDFs)
            $logsForExport = $query->limit(500)->get();
            $pdf = Pdf::loadView('reports.pdf.audit-trail', [
                'logs'    => $logsForExport,
                'request' => $request,
            ])->setPaper('a4', 'landscape');
            return $pdf->download('audit-trail-' . now()->format('Ymd') . '.pdf');
        }

        return view('reports.audit-trail', compact('logs', 'users'));
    }

    // ── 7. Agenda / Implementation Report ───────────────────────────────────

    public function agendas(Request $request)
    {
        $query = Agenda::with(['creator', 'assignee', 'approver'])
            ->withCount('documents');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('meeting_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('meeting_date', '<=', $request->date_to);
        }

        $agendas = $query->orderByDesc('created_at')->get();

        $summary = [
            'total'          => $agendas->count(),
            'draft'          => $agendas->where('status', 'draft')->count(),
            'pending_review' => $agendas->where('status', 'pending_review')->count(),
            'approved'       => $agendas->where('status', 'approved')->count(),
            'rejected'       => $agendas->where('status', 'rejected')->count(),
        ];

        if ($request->export === 'pdf') {
            $pdf = Pdf::loadView('reports.pdf.agendas', compact('agendas', 'summary', 'request'))
                ->setPaper('a4', 'landscape');
            return $pdf->download('agenda-report-' . now()->format('Ymd') . '.pdf');
        }

        return view('reports.agendas', compact('agendas', 'summary'));
    }
}
