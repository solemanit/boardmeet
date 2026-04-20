<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Meeting;
use App\Models\MeetingAgendaSection;
use App\Models\MeetingAgendaMemo;
use App\Models\MeetingMemoDocument;
use App\Models\MeetingMemoVote;
use App\Models\MeetingVoteLink;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AgendaBuilderController extends Controller
{
    private const MAX_FILE_SIZE = 10240;

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

    // ═══════════════════════════════════════════════════════════════
    // PAGE
    // ═══════════════════════════════════════════════════════════════

    public function index(Meeting $meeting)
    {
        $this->authorizeView($meeting);

        $meeting->load([
            'agendaSections.memos.documents',
            'agendaSections.memos.votes',
            'attendees.user',
        ]);

        $userId = auth()->id();
        foreach ($meeting->agendaSections as $section) {
            foreach ($section->memos as $memo) {
                $memo->setRelation('myVote', $memo->votes->firstWhere('user_id', $userId));
            }
        }

        return view('meetings.agenda-builder', compact('meeting'));
    }

    // ═══════════════════════════════════════════════════════════════
    // SECTIONS
    // ═══════════════════════════════════════════════════════════════

    public function storeSection(Request $request, Meeting $meeting): JsonResponse
    {
        $this->authorizeAdmin($meeting);
        $data     = $request->validate(['title' => 'required|string|max:255']);
        $maxOrder = $meeting->agendaSections()->max('order') ?? 0;
        $section  = $meeting->agendaSections()->create(['title' => $data['title'], 'order' => $maxOrder + 1]);

        return response()->json(['success' => true, 'section' => $section->only(['id', 'title', 'order'])]);
    }

    public function updateSection(Request $request, Meeting $meeting, MeetingAgendaSection $section): JsonResponse
    {
        $this->authorizeAdmin($meeting);
        abort_if($section->meeting_id !== $meeting->id, 404);
        $data = $request->validate(['title' => 'required|string|max:255']);
        $section->update($data);

        return response()->json(['success' => true, 'section' => $section->only(['id', 'title'])]);
    }

    public function destroySection(Meeting $meeting, MeetingAgendaSection $section): JsonResponse
    {
        $this->authorizeAdmin($meeting);
        abort_if($section->meeting_id !== $meeting->id, 404);

        foreach ($section->memos as $memo) {
            foreach ($memo->documents as $doc) {
                Storage::disk('public')->delete($doc->file_path);
            }
        }
        $section->delete();

        return response()->json(['success' => true]);
    }

    public function reorderSections(Request $request, Meeting $meeting): JsonResponse
    {
        $this->authorizeAdmin($meeting);
        $request->validate(['order' => 'required|array', 'order.*' => 'integer']);

        foreach ($request->order as $pos => $id) {
            $meeting->agendaSections()->where('id', $id)->update(['order' => $pos + 1]);
        }

        return response()->json(['success' => true]);
    }

    // ═══════════════════════════════════════════════════════════════
    // MEMOS
    // ═══════════════════════════════════════════════════════════════

    public function storeMemo(Request $request, Meeting $meeting, MeetingAgendaSection $section): JsonResponse
    {
        $this->authorizeAdmin($meeting);
        abort_if($section->meeting_id !== $meeting->id, 404);

        $data = $request->validate([
            'title'         => 'required|string|max:255',
            'description'   => 'nullable|string|max:5000',
            'vote_required' => 'required|in:none,optional,required',
            'documents.*'   => 'file|mimes:' . implode(',', self::ALLOWED_TYPES) . '|max:' . self::MAX_FILE_SIZE,
        ]);

        DB::beginTransaction();
        try {
            $maxOrder = $section->memos()->max('order') ?? 0;
            $memo = $section->memos()->create([
                'meeting_id'    => $meeting->id,
                'title'         => $data['title'],
                'description'   => $data['description'] ?? null,
                'vote_required' => $data['vote_required'],
                'order'         => $maxOrder + 1,
            ]);

            if ($request->hasFile('documents')) {
                foreach ($request->file('documents') as $file) {
                    $this->saveDocument($memo, $file);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'memo'    => $this->memoPayload($memo->fresh('documents')),
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed to create memo: ' . $e->getMessage()], 500);
        }
    }

    /**
     * UPDATE memo.
     * Called via POST + _method=PUT (because FormData + file upload cannot use PUT in browsers).
     * Route is: PUT /meetings/{meeting}/memos/{memo}
     * Laravel's method-spoofing middleware handles _method automatically.
     */
    public function updateMemo(Request $request, Meeting $meeting, MeetingAgendaMemo $memo): JsonResponse
    {
        $this->authorizeAdmin($meeting);
        abort_if($memo->meeting_id !== $meeting->id, 404);

        $data = $request->validate([
            'title'         => 'required|string|max:255',
            'description'   => 'nullable|string|max:5000',
            'vote_required' => 'required|in:none,optional,required',
            'documents.*'   => 'file|mimes:' . implode(',', self::ALLOWED_TYPES) . '|max:' . self::MAX_FILE_SIZE,
        ]);

        DB::beginTransaction();
        try {
            $memo->update([
                'title'         => $data['title'],
                'description'   => $data['description'] ?? null,
                'vote_required' => $data['vote_required'],
            ]);

            if ($request->hasFile('documents')) {
                foreach ($request->file('documents') as $file) {
                    $this->saveDocument($memo, $file);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'memo'    => $this->memoPayload($memo->fresh('documents')),
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed to update memo: ' . $e->getMessage()], 500);
        }
    }

    public function destroyMemo(Meeting $meeting, MeetingAgendaMemo $memo): JsonResponse
    {
        $this->authorizeAdmin($meeting);
        abort_if($memo->meeting_id !== $meeting->id, 404);

        foreach ($memo->documents as $doc) {
            Storage::disk('public')->delete($doc->file_path);
        }
        $memo->delete();

        return response()->json(['success' => true]);
    }

    public function reorderMemos(Request $request, Meeting $meeting, MeetingAgendaSection $section): JsonResponse
    {
        $this->authorizeAdmin($meeting);
        abort_if($section->meeting_id !== $meeting->id, 404);
        $request->validate(['order' => 'required|array', 'order.*' => 'integer']);

        foreach ($request->order as $pos => $id) {
            $section->memos()->where('id', $id)->update(['order' => $pos + 1]);
        }

        return response()->json(['success' => true]);
    }

    public function moveMemo(Request $request, Meeting $meeting, MeetingAgendaMemo $memo): JsonResponse
    {
        $this->authorizeAdmin($meeting);
        abort_if($memo->meeting_id !== $meeting->id, 404);

        $data = $request->validate([
            'section_id' => 'required|exists:meeting_agenda_sections,id',
            'order'      => 'required|integer|min:1',
        ]);

        $target = MeetingAgendaSection::where('id', $data['section_id'])
            ->where('meeting_id', $meeting->id)
            ->firstOrFail();

        $memo->update(['section_id' => $target->id, 'order' => $data['order']]);

        return response()->json(['success' => true]);
    }

    // ═══════════════════════════════════════════════════════════════
    // DOCUMENTS
    // ═══════════════════════════════════════════════════════════════

    public function uploadDocument(Request $request, Meeting $meeting, MeetingAgendaMemo $memo): JsonResponse
    {
        $this->authorizeAdmin($meeting);
        abort_if($memo->meeting_id !== $meeting->id, 404);

        $request->validate([
            'file' => 'required|file|mimes:' . implode(',', self::ALLOWED_TYPES) . '|max:' . self::MAX_FILE_SIZE,
        ]);

        $doc = $this->saveDocument($memo, $request->file('file'));

        return response()->json([
            'success'  => true,
            'document' => $this->docPayload($doc),
        ]);
    }

    public function deleteDocument(Meeting $meeting, MeetingAgendaMemo $memo, MeetingMemoDocument $document): JsonResponse
    {
        $this->authorizeAdmin($meeting);
        abort_if($memo->meeting_id   !== $meeting->id, 404);
        abort_if($document->memo_id  !== $memo->id,    404);

        Storage::disk('public')->delete($document->file_path);
        $document->delete();

        return response()->json(['success' => true]);
    }

    // ═══════════════════════════════════════════════════════════════
    // VOTES
    // ═══════════════════════════════════════════════════════════════

    public function castVote(Request $request, Meeting $meeting, MeetingAgendaMemo $memo): JsonResponse
    {
        abort_if($memo->meeting_id !== $meeting->id, 404);

        $isAttendee = $meeting->attendees()->where('user_id', auth()->id())->exists()
            || auth()->user()->hasAnyRole(['admin', 'md']);
        abort_unless($isAttendee, 403, 'You are not an attendee of this meeting.');

        if ($memo->vote_required === 'none') {
            return response()->json(['success' => false, 'message' => 'This memo does not require a vote.'], 422);
        }

        $data = $request->validate([
            'vote'    => 'required|in:approve,reject,abstain',
            'comment' => 'nullable|string|max:1000',
        ]);

        MeetingMemoVote::updateOrCreate(
            ['memo_id' => $memo->id, 'user_id' => auth()->id()],
            [
                'meeting_id' => $meeting->id,
                'vote'       => $data['vote'],
                'comment'    => $data['comment'] ?? null,
                'voted_at'   => now(),
            ]
        );

        $fresh   = $memo->fresh();
        $tally   = $fresh->voteTally();
        $outcome = $fresh->voteOutcome();

        return response()->json([
            'success' => true,
            'tally'   => $tally,
            'outcome' => $outcome,
        ]);
    }

    public function voteResults(Meeting $meeting, MeetingAgendaMemo $memo): JsonResponse
    {
        abort_if($memo->meeting_id !== $meeting->id, 404);

        $tally   = $memo->voteTally();
        $outcome = $memo->voteOutcome();

        if (!auth()->user()->hasAnyRole(['admin', 'md'])) {
            return response()->json(['tally' => $tally, 'outcome' => $outcome]);
        }

        $memo->load('votes.user');

        return response()->json([
            'tally'   => $tally,
            'outcome' => $outcome,
            'votes'   => $memo->votes->map(fn($v) => [
                'user'     => $v->user?->name,
                'vote'     => $v->vote,
                'comment'  => $v->comment,
                'voted_at' => $v->voted_at?->format('d M Y H:i'),
            ]),
        ]);
    }

    // ═══════════════════════════════════════════════════════════════
    // MINUTES (per memo note)
    // ═══════════════════════════════════════════════════════════════

    public function saveMemoMinutes(Request $request, Meeting $meeting, MeetingAgendaMemo $memo): JsonResponse
    {
        $this->authorizeAdmin($meeting);
        abort_if($memo->meeting_id !== $meeting->id, 404);
        $data = $request->validate(['minutes_note' => 'nullable|string|max:10000']);
        $memo->update(['minutes_note' => $data['minutes_note']]);

        return response()->json(['success' => true]);
    }

    // ═══════════════════════════════════════════════════════════════
    // PRIVATE HELPERS
    // ═══════════════════════════════════════════════════════════════

    private function saveDocument(MeetingAgendaMemo $memo, $file): MeetingMemoDocument
    {
        $ext  = strtolower($file->getClientOriginalExtension());
        $name = Str::uuid() . ($ext ? ".{$ext}" : '');
        $path = $file->storeAs("meetings/{$memo->meeting_id}/memos/{$memo->id}", $name, 'public');

        return MeetingMemoDocument::create([
            'memo_id'       => $memo->id,
            'file_name'     => $name,
            'original_name' => $file->getClientOriginalName(),
            'file_path'     => $path,
            'file_type'     => $ext,
            'file_size'     => $file->getSize(),
            'uploaded_by'   => auth()->id(),
        ]);
    }

    /**
     * Consistent memo JSON shape returned to JS.
     */
    private function memoPayload(MeetingAgendaMemo $memo): array
    {
        return [
            'id'            => $memo->id,
            'title'         => $memo->title,
            'description'   => $memo->description,
            'vote_required' => $memo->vote_required,
            'order'         => $memo->order,
            'documents'     => $memo->documents->map(fn($d) => $this->docPayload($d))->toArray(),
        ];
    }

    /**
     * Consistent document JSON shape returned to JS.
     */
    private function docPayload(MeetingMemoDocument $doc): array
    {
        return [
            'id'            => $doc->id,
            'original_name' => $doc->original_name,
            'file_type'     => $doc->file_type,
            'file_size_fmt' => $doc->file_size_formatted,
            'download_url'  => $doc->download_url,
            'icon_emoji'    => $doc->icon_emoji,
        ];
    }

    private function authorizeAdmin(Meeting $meeting): void
    {
        $ok = auth()->user()->hasAnyRole(['admin', 'md']) || $meeting->created_by === auth()->id();
        abort_unless($ok, 403, 'You do not have permission to edit this meeting agenda.');
    }

    private function authorizeView(Meeting $meeting): void
    {
        if (auth()->user()->hasAnyRole(['admin', 'md'])) return;
        $ok = $meeting->created_by === auth()->id()
            || $meeting->attendees()->where('user_id', auth()->id())->exists();
        abort_unless($ok, 403, 'Access denied.');
    }

    // ═══════════════════════════════════════════════════════════════
    // VOTE LINK MANAGEMENT
    // ═══════════════════════════════════════════════════════════════

    public function generateVoteLink(Request $request, Meeting $meeting): JsonResponse
    {
        $this->authorizeAdmin($meeting);

        $data = $request->validate([
            'expires_at' => 'nullable|date|after:now',
            'max_uses'   => 'nullable|integer|min:1|max:10000',
        ]);

        // Revoke any existing active link for this meeting
        MeetingVoteLink::where('meeting_id', $meeting->id)
            ->where('is_active', true)
            ->update(['is_active' => false]);

        $link = MeetingVoteLink::create([
            'meeting_id' => $meeting->id,
            'token'      => \Illuminate\Support\Str::random(48),
            'expires_at' => $data['expires_at'] ?? null,
            'max_uses'   => $data['max_uses']   ?? null,
            'is_active'  => true,
            'created_by' => auth()->id(),
        ]);

        return response()->json([
            'success'    => true,
            'url'        => $link->getPublicUrl(),
            'expires_at' => $link->expires_at?->format('d M Y H:i'),
            'link_id'    => $link->id,
        ]);
    }

    public function revokeVoteLink(Meeting $meeting): JsonResponse
    {
        $this->authorizeAdmin($meeting);

        MeetingVoteLink::where('meeting_id', $meeting->id)
            ->where('is_active', true)
            ->update(['is_active' => false]);

        return response()->json(['success' => true]);
    }

    public function getVoteLink(Meeting $meeting): JsonResponse
    {
        $this->authorizeAdmin($meeting);

        $link = MeetingVoteLink::where('meeting_id', $meeting->id)
            ->where('is_active', true)
            ->latest()
            ->first();

        if (!$link) {
            return response()->json(['active' => false]);
        }

        return response()->json([
            'active'     => true,
            'url'        => $link->getPublicUrl(),
            'expires_at' => $link->expires_at?->format('d M Y H:i'),
            'use_count'  => $link->use_count,
            'max_uses'   => $link->max_uses,
            'is_expired' => $link->isExpired(),
        ]);
    }
}
