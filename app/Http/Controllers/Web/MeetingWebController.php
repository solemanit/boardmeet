<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Meeting;
use App\Models\MeetingAgendaItem;
use App\Models\MeetingDocument;
use App\Models\User;
use App\Notifications\MeetingNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MeetingWebController extends Controller
{
    private const MAX_SIZE = 10240; // 10MB

    private const ALLOWED_TYPES = [
        'pdf',
        'doc',
        'docx',
        'xls',
        'xlsx',
        'ppt',
        'pptx',
        'jpg',
        'jpeg',
        'png',
        'gif',
        'bmp',
        'svg',
        'webp',
    ];

    private const MEETING_TYPES = ['board', 'committee', 'agm', 'egm', 'executive', 'other'];
    private const VENUE_TYPES   = ['physical', 'virtual', 'hybrid'];

    // ── Index ───────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $query = Meeting::with(['creator', 'chairperson'])->latest('date');

        if (!auth()->user()->hasAnyRole(['admin', 'md'])) {
            $query->where(function ($q) {
                $q->where('created_by', auth()->id())
                    ->orWhereHas('attendees', fn($a) => $a->where('user_id', auth()->id()));
            });
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('title', 'like', "%{$s}%")
                    ->orWhere('meeting_number', 'like', "%{$s}%");
            });
        }

        foreach (['status', 'type', 'venue_type'] as $filter) {
            if ($request->filled($filter)) $query->where($filter, $request->$filter);
        }

        if ($request->filled('date_from')) $query->whereDate('date', '>=', $request->date_from);
        if ($request->filled('date_to'))   $query->whereDate('date', '<=', $request->date_to);

        $meetings = $query->paginate(15)->withQueryString();

        $stats = [
            'total'     => Meeting::count(),
            'scheduled' => Meeting::where('status', 'scheduled')->count(),
            'ongoing'   => Meeting::where('status', 'ongoing')->count(),
            'completed' => Meeting::where('status', 'completed')->count(),
            'cancelled' => Meeting::where('status', 'cancelled')->count(),
        ];

        $calendarMeetings = Meeting::whereIn('status', ['scheduled', 'ongoing'])
            ->whereBetween('date', [now()->startOfMonth(), now()->addMonths(3)->endOfMonth()])
            ->get(['id', 'title', 'date', 'start_time', 'status', 'type']);

        return view('meetings.index', compact('meetings', 'stats', 'calendarMeetings'));
    }

    // ── Create ──────────────────────────────────────────────────────

    public function create()
    {
        $meetingNumber = Meeting::generateMeetingNumber();
        $users         = User::where('is_active', true)->get();
        $pastMeetings  = Meeting::where('status', 'completed')->latest('date')->limit(20)->get();

        return view('meetings.create', compact('meetingNumber', 'users', 'pastMeetings'));
    }

    // ── Store ───────────────────────────────────────────────────────

    public function store(Request $request)
    {
        $request->validate([
            'title'               => 'required|string|max:255',
            'type'                => 'required|in:' . implode(',', self::MEETING_TYPES),
            'date'                => 'required|date|after_or_equal:today',
            'start_time'          => 'required',
            'end_time'            => 'nullable|after:start_time',
            'venue_type'          => 'required|in:' . implode(',', self::VENUE_TYPES),
            'venue'               => 'nullable|string|max:255',
            'meeting_link'        => 'nullable|url',
            'description'         => 'nullable|string',
            'chairperson_id'      => 'nullable|exists:users,id',
            'secretary_id'        => 'nullable|exists:users,id',
            'previous_meeting_id' => 'nullable|exists:meetings,id',
            'attendees'           => 'nullable|array',
            'attendees.*'         => 'exists:users,id',
            'documents.*'         => 'file|mimes:' . implode(',', self::ALLOWED_TYPES) . '|max:' . self::MAX_SIZE,
        ]);

        DB::beginTransaction();

        $meeting = Meeting::create([
            'meeting_number'      => Meeting::generateMeetingNumber(),
            'title'               => $request->title,
            'type'                => $request->type,
            'status'              => 'scheduled',
            'date'                => $request->date,
            'start_time'          => $request->start_time,
            'end_time'            => $request->end_time,
            'venue_type'          => $request->venue_type,
            'venue'               => $request->venue,
            'meeting_link' => [
                'nullable',
                'url',
                function ($attribute, $value, $fail) use ($request) {
                    if (in_array($request->venue_type, ['virtual', 'hybrid']) && empty($value)) {
                        $fail('A meeting link is required for virtual or hybrid meetings.');
                    }
                },
            ],
            'description'         => $request->description,
            'chairperson_id'      => $request->chairperson_id,
            'secretary_id'        => $request->secretary_id,
            'previous_meeting_id' => $request->previous_meeting_id,
            'created_by'          => auth()->id(),
        ]);

        if ($request->filled('attendees')) {
            $meeting->attendees()->createMany(
                collect($request->attendees)->map(fn($uid) => [
                    'user_id' => $uid,
                    'role'    => 'member',
                    'status'  => 'invited',
                ])->toArray()
            );
        }

        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $file) {
                $this->uploadDocument($meeting, $file, 'supporting');
            }
        }

        DB::commit();

        // ✅ Redirect to Agenda Builder after creation
        return redirect()
            ->route('meetings.agenda-builder', $meeting)
            ->with('success', "Meeting {$meeting->meeting_number} created! Now build the agenda.");
    }

    // ── Show ────────────────────────────────────────────────────────

    public function show(Meeting $meeting)
    {
        $meeting->load([
            'attendees.user.role',
            'documents',
            'agendaItems',                          // keep for legacy / minutes
            'agendaSections.memos.documents',       // ← add this
            'creator',
            'chairperson',
            'secretary',
            'previousMeeting',
            'rescheduledFrom',
        ]);

        return view('meetings.show', compact('meeting'));
    }

    // ── Edit ────────────────────────────────────────────────────────

    public function edit(Meeting $meeting)
    {
        if (!$meeting->canBeEdited()) {
            return redirect()->route('meetings.show', $meeting)
                ->with('error', 'This meeting cannot be edited in its current status.');
        }

        $meeting->load(['attendees', 'agendaItems', 'documents']);
        $users        = User::where('is_active', true)->get();
        $pastMeetings = Meeting::where('status', 'completed')->latest('date')->limit(20)->get();

        return view('meetings.edit', compact('meeting', 'users', 'pastMeetings'));
    }

    // ── Update ──────────────────────────────────────────────────────

    public function update(Request $request, Meeting $meeting)
    {
        if (!$meeting->canBeEdited()) {
            return redirect()->route('meetings.show', $meeting)
                ->with('error', 'This meeting cannot be edited.');
        }

        $request->validate([
            'title'          => 'required|string|max:255',
            'type'           => 'required|in:' . implode(',', self::MEETING_TYPES),
            'date'           => 'required|date',
            'start_time'     => 'required',
            'end_time' => 'nullable|string',
            'venue_type'     => 'required|in:' . implode(',', self::VENUE_TYPES),
            'venue'          => 'nullable|string|max:255',
            'meeting_link' => [
                'nullable',
                'url',
                function ($attribute, $value, $fail) use ($request) {
                    if (in_array($request->venue_type, ['virtual', 'hybrid']) && empty($value)) {
                        $fail('A meeting link is required for virtual or hybrid meetings.');
                    }
                },
            ],
            'description'    => 'nullable|string',
            'chairperson_id' => 'nullable|exists:users,id',
            'secretary_id'   => 'nullable|exists:users,id',
            'attendees'      => 'nullable|array',
            'documents.*'    => 'file|mimes:' . implode(',', self::ALLOWED_TYPES) . '|max:' . self::MAX_SIZE,
        ]);

        DB::beginTransaction();
        try {
            $meeting->update($request->only([
                'title',
                'type',
                'date',
                'start_time',
                'end_time',
                'venue_type',
                'venue',
                'meeting_link',
                'description',
                'chairperson_id',
                'secretary_id',
                'previous_meeting_id',
            ]));

            // Re-sync attendees
            $meeting->attendees()->delete();
            if ($request->filled('attendees')) {
                $meeting->attendees()->createMany(
                    collect($request->attendees)->map(fn($uid) => [
                        'user_id' => $uid,
                        'role'    => 'member',
                        'status'  => 'invited',
                    ])->toArray()
                );
            }

            if ($request->hasFile('documents')) {
                foreach ($request->file('documents') as $file) {
                    $this->uploadDocument($meeting, $file, 'supporting');
                }
            }

            DB::commit();

            return redirect()->route('meetings.show', $meeting)
                ->with('success', 'Meeting updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to update meeting.');
        }
    }

    // ── Destroy ─────────────────────────────────────────────────────

    public function destroy(Meeting $meeting)
    {
        if ($meeting->isCompleted()) {
            return redirect()->route('meetings.index')
                ->with('error', 'Completed meetings cannot be deleted.');
        }

        foreach ($meeting->documents as $doc) {
            Storage::disk('public')->delete($doc->file_path);
        }

        $meeting->delete();

        return redirect()->route('meetings.index')
            ->with('success', 'Meeting deleted successfully!');
    }

    // ── Cancel ──────────────────────────────────────────────────────

    public function cancel(Request $request, Meeting $meeting)
    {
        if (!$meeting->canBeCancelled()) {
            return redirect()->route('meetings.show', $meeting)
                ->with('error', 'This meeting cannot be cancelled.');
        }

        $request->validate(['cancellation_reason' => 'required|string|max:1000']);

        $meeting->update([
            'status'              => 'cancelled',
            'cancellation_reason' => $request->cancellation_reason,
            'cancelled_at'        => now(),
        ]);

        $this->notifyAttendees($meeting, 'cancel');

        return redirect()->route('meetings.show', $meeting)
            ->with('success', 'Meeting cancelled and attendees notified.');
    }

    // ── Reschedule ───────────────────────────────────────────────────

    public function reschedule(Request $request, Meeting $meeting)
    {
        if (!$meeting->canBeRescheduled()) {
            return redirect()->route('meetings.show', $meeting)
                ->with('error', 'This meeting cannot be rescheduled.');
        }

        $request->validate([
            'date'       => 'required|date|after_or_equal:today',
            'start_time' => 'required',
            'end_time'   => 'nullable',
            'venue'      => 'nullable|string|max:255',
            'reason'     => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();
        try {
            $oldMeeting                 = $meeting->replicate();
            $oldMeeting->status         = 'postponed';
            $oldMeeting->meeting_number = Meeting::generateMeetingNumber();
            $oldMeeting->save();

            $meeting->update([
                'date'                => $request->date,
                'start_time'          => $request->start_time,
                'end_time'            => $request->end_time ?? $meeting->end_time,
                'venue'               => $request->venue   ?? $meeting->venue,
                'status'              => 'scheduled',
                'rescheduled_from_id' => $oldMeeting->id,
                'rescheduled_at'      => now(),
            ]);

            $this->notifyAttendees($meeting, 'reschedule');

            DB::commit();

            return redirect()->route('meetings.show', $meeting)
                ->with('success', 'Meeting rescheduled and attendees notified.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to reschedule meeting.');
        }
    }

    // ── Complete ─────────────────────────────────────────────────────

    public function complete(Meeting $meeting)
    {
        if (!$meeting->isOngoing() && !$meeting->isScheduled()) {
            return redirect()->route('meetings.show', $meeting)
                ->with('error', 'Only scheduled or ongoing meetings can be marked as completed.');
        }

        $meeting->update(['status' => 'completed']);

        return redirect()->route('meetings.show', $meeting)
            ->with('success', 'Meeting marked as completed.');
    }

    // ── Send Notifications ────────────────────────────────────────────

    public function sendNotifications(Meeting $meeting)
    {
        $this->notifyAttendees($meeting, 'invite');
        $meeting->update(['notification_sent_at' => now()]);

        return redirect()->route('meetings.show', $meeting)
            ->with('success', 'Notifications sent to all attendees!');
    }

    // ── RSVP ─────────────────────────────────────────────────────────

    public function rsvp(Request $request, Meeting $meeting)
    {
        $request->validate(['status' => 'required|in:confirmed,declined']);

        $meeting->attendees()
            ->where('user_id', auth()->id())
            ->update(['status' => $request->status]);

        return redirect()->route('meetings.show', $meeting)
            ->with('success', 'RSVP updated successfully!');
    }

    // ── Download iCal ─────────────────────────────────────────────────

    public function downloadIcal(Meeting $meeting)
    {
        $dtStart = $meeting->date->format('Ymd') . 'T' . str_replace(':', '', $meeting->start_time) . '00';
        $dtEnd   = $meeting->end_time
            ? $meeting->date->format('Ymd') . 'T' . str_replace(':', '', $meeting->end_time) . '00'
            : $dtStart;

        $ical = implode("\r\n", [
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:-//BoardMeeting//EN',
            'BEGIN:VEVENT',
            'UID:' . $meeting->meeting_number . '@boardmeeting',
            'DTSTAMP:' . now()->format('Ymd\THis\Z'),
            'DTSTART:' . $dtStart,
            'DTEND:' . $dtEnd,
            'SUMMARY:' . $meeting->meeting_number . ' - ' . $meeting->title,
            'DESCRIPTION:' . strip_tags($meeting->description ?? ''),
            'LOCATION:' . ($meeting->venue ?? ($meeting->meeting_link ?? 'TBA')),
            'STATUS:CONFIRMED',
            'END:VEVENT',
            'END:VCALENDAR',
        ]);

        return response($ical, 200, [
            'Content-Type'        => 'text/calendar; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $meeting->meeting_number . '.ics"',
        ]);
    }

    // ── Delete Document ───────────────────────────────────────────────

    public function deleteDocument(Meeting $meeting, MeetingDocument $document)
    {
        if ($document->meeting_id !== $meeting->id) abort(404);

        Storage::disk('public')->delete($document->file_path);
        $document->delete();

        return redirect()->route('meetings.edit', $meeting)
            ->with('success', 'Document deleted.');
    }

    // ═══════════════════════════════════════════════════════════════
    // AGENDA BUILDER
    // ═══════════════════════════════════════════════════════════════

    // ── Agenda Builder page ───────────────────────────────────────────

    public function agendaBuilder(Meeting $meeting)
    {
        $meeting->load('agendaItems');

        return view('meetings.agenda-builder', compact('meeting'));
    }

    // ── Store Agenda Item (AJAX) ──────────────────────────────────────

    public function storeAgendaItem(Request $request, Meeting $meeting)
    {
        $request->validate([
            'title'   => 'required|string|max:255',
            'details' => 'nullable|string|max:5000',
        ]);

        $maxOrder = $meeting->agendaItems()->max('order') ?? 0;

        $item = $meeting->agendaItems()->create([
            'title'   => $request->title,
            'details' => $request->details,
            'order'   => $maxOrder + 1,
        ]);

        return response()->json(['success' => true, 'item' => $item]);
    }

    // ── Update Agenda Item (AJAX) ─────────────────────────────────────

    public function updateAgendaItem(Request $request, Meeting $meeting, MeetingAgendaItem $item)
    {
        abort_if($item->meeting_id !== $meeting->id, 404);

        $request->validate([
            'title'   => 'required|string|max:255',
            'details' => 'nullable|string|max:5000',
        ]);

        $item->update($request->only('title', 'details'));

        return response()->json(['success' => true, 'item' => $item]);
    }

    // ── Destroy Agenda Item (AJAX) ────────────────────────────────────

    public function destroyAgendaItem(Meeting $meeting, MeetingAgendaItem $item)
    {
        abort_if($item->meeting_id !== $meeting->id, 404);

        $item->delete();

        return response()->json(['success' => true]);
    }

    // ── Reorder Agenda Items (AJAX) ───────────────────────────────────

    public function reorderAgendaItems(Request $request, Meeting $meeting)
    {
        $request->validate([
            'order'   => 'required|array',
            'order.*' => 'integer',
        ]);

        foreach ($request->order as $position => $itemId) {
            $meeting->agendaItems()
                ->where('id', $itemId)
                ->update(['order' => $position + 1]);
        }

        return response()->json(['success' => true]);
    }

    // ═══════════════════════════════════════════════════════════════
    // MINUTES
    // ═══════════════════════════════════════════════════════════════

    public function minutes(Meeting $meeting)
    {
        $meeting->load([
            'creator',
            'chairperson',
            'secretary',
            'attendees.user.role',
            'agendaSections.memos.documents',   // ← new structured agenda
            'agendaSections.memos.votes',        // ← votes per memo
            'documents.uploader',
        ]);

        // Attach current user's vote to each memo
        $userId = auth()->id();
        foreach ($meeting->agendaSections as $section) {
            foreach ($section->memos as $memo) {
                $memo->setRelation('myVote', $memo->votes->firstWhere('user_id', $userId));
            }
        }

        return view('meetings.minutes', compact('meeting'));
    }

    public function saveMinutes(Request $request, Meeting $meeting)
    {
        $request->validate([
            'memos'              => 'nullable|array',
            'memos.*.note'       => 'nullable|string|max:10000',
            'attendees'          => 'nullable|array',
            'attendees.*.status' => 'nullable|in:invited,confirmed,declined,attended,absent',
            'attendees.*.notes'  => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            // Save per-memo minutes notes
            foreach ($request->input('memos', []) as $memoId => $data) {
                \App\Models\MeetingAgendaMemo::where('id', $memoId)
                    ->where('meeting_id', $meeting->id)  // security: only this meeting's memos
                    ->update(['minutes_note' => $data['note'] ?? null]);
            }

            // Save attendee statuses
            foreach ($request->input('attendees', []) as $attendeeId => $data) {
                $meeting->attendees()
                    ->where('id', $attendeeId)
                    ->update([
                        'status' => $data['status'] ?? 'invited',
                        'notes'  => $data['notes']  ?? null,
                    ]);
            }

            DB::commit();

            return redirect()
                ->route('meetings.minutes', $meeting)
                ->with('success', 'Minutes saved successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to save minutes. Please try again.');
        }
    }

    // ═══════════════════════════════════════════════════════════════
    // PRIVATE HELPERS
    // ═══════════════════════════════════════════════════════════════

    private function uploadDocument(Meeting $meeting, $file, string $type): MeetingDocument
    {
        $ext  = $file->getClientOriginalExtension();
        $name = \Illuminate\Support\Str::uuid() . ($ext ? ".{$ext}" : '');
        $path = $file->storeAs("meetings/{$meeting->id}", $name, 'public');

        return MeetingDocument::create([
            'meeting_id'    => $meeting->id,
            'file_name'     => $name,
            'original_name' => $file->getClientOriginalName(),
            'file_path'     => $path,
            'file_type'     => strtolower($ext),
            'file_size'     => $file->getSize(),
            'document_type' => $type,
            'uploaded_by'   => auth()->id(),
        ]);
    }

    private function notifyAttendees(Meeting $meeting, string $type): void
    {
        $meeting->load('attendees.user');

        foreach ($meeting->attendees as $attendee) {
            if ($attendee->user) {
                try {
                    $attendee->user->notify(new MeetingNotification($meeting, $type));
                } catch (\Exception $e) {
                    \Log::warning("Failed to notify user {$attendee->user_id}: " . $e->getMessage());
                }
            }
        }
    }

    // ═══════════════════════════════════════════════════════════════
    // EXPORT / PRINT
    // ═══════════════════════════════════════════════════════════════

    public function exportAgendaPdf(Meeting $meeting)
    {
        $meeting->load(['agendaItems', 'chairperson', 'secretary', 'creator']);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('meetings.pdf.agenda', compact('meeting'))
            ->setPaper('a4', 'portrait');

        return $pdf->download("{$meeting->meeting_number}-agenda.pdf");
    }

    public function exportMinutesPdf(Meeting $meeting)
    {
        $meeting->load(['agendaItems', 'attendees.user.role', 'chairperson', 'secretary', 'creator']);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('meetings.pdf.minutes', compact('meeting'))
            ->setPaper('a4', 'portrait');

        return $pdf->download("{$meeting->meeting_number}-minutes.pdf");
    }

    public function printAgenda(Meeting $meeting)
    {
        $meeting->load(['agendaItems', 'chairperson', 'secretary', 'creator']);
        return view('meetings.pdf.agenda', compact('meeting'));
    }

    public function printMinutes(Meeting $meeting)
    {
        $meeting->load(['agendaItems', 'attendees.user.role', 'chairperson', 'secretary', 'creator']);
        return view('meetings.pdf.minutes', compact('meeting'));
    }
}
