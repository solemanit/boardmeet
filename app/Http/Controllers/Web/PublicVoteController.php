<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\MeetingVoteLink;
use App\Models\MeetingMemoVote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PublicVoteController extends Controller
{
    // ── Show public voting page ──────────────────────────────────────

    public function show(string $token)
    {
        $link = MeetingVoteLink::where('token', $token)
            ->with([
                'meeting.agendaSections.memos.documents',
                'meeting.agendaSections.memos.votes',
            ])
            ->firstOrFail();

        if (!$link->isUsable()) {
            return view('vote.expired', compact('link'));
        }

        $meeting = $link->meeting;

        // Collect all votable memos
        $votableMemos = $meeting->agendaSections
            ->flatMap(fn($s) => $s->memos->where('vote_required', '!=', 'none')->values())
            ->values();

        return view('vote.show', compact('link', 'meeting', 'votableMemos'));
    }

    // ── Submit votes ─────────────────────────────────────────────────

    public function submit(Request $request, string $token)
    {
        $link = MeetingVoteLink::where('token', $token)
            ->with('meeting.agendaSections.memos')
            ->firstOrFail();

        if (!$link->isUsable()) {
            return back()->with('error', 'This voting link is no longer active.');
        }

        $validated = $request->validate([
            'voter_name'  => 'required|string|max:255',
            'voter_email' => 'required|email|max:255',
            'votes'       => 'required|array',
            'votes.*'     => 'required|in:approve,reject,abstain',
        ]);

        $email   = strtolower($validated['voter_email']);
        $meeting = $link->meeting;

        // Collect all votable memo IDs for this meeting
        $votableMemoIds = $meeting->agendaSections
            ->flatMap(fn($s) => $s->memos->where('vote_required', '!=', 'none'))
            ->pluck('id')
            ->toArray();

        // Check if voter already submitted via this link (any memo)
        $alreadyVoted = MeetingMemoVote::where('vote_link_id', $link->id)
            ->whereIn('memo_id', $votableMemoIds)
            ->where('voter_email', $email)
            ->exists();

        if ($alreadyVoted) {
            return back()
                ->withInput()
                ->with('error', 'You have already submitted your votes for this meeting.');
        }

        // Validate that all submitted memo IDs belong to this meeting
        $submittedMemoIds = array_keys($validated['votes']);
        $invalidIds = array_diff($submittedMemoIds, $votableMemoIds);
        if (!empty($invalidIds)) {
            return back()->with('error', 'Invalid vote submission.');
        }

        DB::beginTransaction();
        try {
            foreach ($validated['votes'] as $memoId => $vote) {
                MeetingMemoVote::create([
                    'memo_id'     => $memoId,
                    'meeting_id'  => $meeting->id,
                    'user_id'     => null,
                    'voter_name'  => $validated['voter_name'],
                    'voter_email' => $email,
                    'voter_type'  => 'guest',
                    'vote_link_id'=> $link->id,
                    'ip_address'  => $request->ip(),
                    'vote'        => $vote,
                    'voted_at'    => now(),
                ]);
            }

            $link->incrementUse();

            DB::commit();

            return redirect()
                ->route('vote.thanks', $token)
                ->with('voter_name', $validated['voter_name']);

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Vote submission failed. Please try again.');
        }
    }

    // ── Thank you page ───────────────────────────────────────────────

    public function thanks(string $token)
    {
        $link = MeetingVoteLink::where('token', $token)
            ->with('meeting')
            ->firstOrFail();

        return view('vote.thanks', [
            'link'       => $link,
            'meeting'    => $link->meeting,
            'voterName'  => session('voter_name'),
        ]);
    }
}
