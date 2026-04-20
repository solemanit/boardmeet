<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Meeting;
use App\Models\MeetingMemoVote;
use Illuminate\Http\Request;

class VoteReportController extends Controller
{
    public function show(Meeting $meeting)
    {
        $this->authorizeAccess($meeting);

        $meeting->load([
            'agendaSections.memos.votes.user',
            'attendees.user',
        ]);

        $reportData = $this->buildReportData($meeting);

        return view('vote.report', compact('meeting', 'reportData'));
    }

    public function exportPdf(Meeting $meeting)
    {
        $this->authorizeAccess($meeting);

        $meeting->load([
            'agendaSections.memos.votes.user',
            'chairperson',
            'secretary',
        ]);

        $reportData = $this->buildReportData($meeting);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('vote.pdf.report', compact('meeting', 'reportData'))
            ->setPaper('a4', 'portrait');

        return $pdf->download("{$meeting->meeting_number}-vote-report.pdf");
    }

    // ── Build report data ────────────────────────────────────────────

    private function buildReportData(Meeting $meeting): array
    {
        $sections = [];

        foreach ($meeting->agendaSections as $section) {
            $memos = [];

            foreach ($section->memos as $memo) {
                if ($memo->vote_required === 'none') continue;

                $tally   = $memo->voteTally();
                $outcome = $memo->voteOutcome();

                // Group votes by type
                $userVotes  = $memo->votes->where('voter_type', 'user');
                $guestVotes = $memo->votes->where('voter_type', 'guest');

                // All unique voters
                $allVoters = $memo->votes->map(fn($v) => [
                    'name'      => $v->voter_name ?? $v->user?->name ?? '—',
                    'email'     => $v->voter_email ?? $v->user?->email ?? '—',
                    'type'      => $v->voter_type,
                    'vote'      => $v->vote,
                    'voted_at'  => $v->voted_at?->format('d M Y H:i'),
                    'comment'   => $v->comment,
                ]);

                $memos[] = [
                    'memo'        => $memo,
                    'tally'       => $tally,
                    'outcome'     => $outcome,
                    'pct_approve' => $tally['total'] ? round($tally['approve'] / $tally['total'] * 100) : 0,
                    'pct_reject'  => $tally['total'] ? round($tally['reject']  / $tally['total'] * 100) : 0,
                    'pct_abstain' => $tally['total'] ? round($tally['abstain'] / $tally['total'] * 100) : 0,
                    'all_voters'  => $allVoters,
                    'user_count'  => $userVotes->count(),
                    'guest_count' => $guestVotes->count(),
                ];
            }

            if (!empty($memos)) {
                $sections[] = [
                    'section' => $section,
                    'memos'   => $memos,
                ];
            }
        }

        // Overall stats
        $totalVotableMemos = $meeting->agendaSections
            ->flatMap->memos
            ->where('vote_required', '!=', 'none')
            ->count();

        $totalVotesCast = $meeting->memoVotes()->count();

        $linkStats = \App\Models\MeetingVoteLink::where('meeting_id', $meeting->id)
            ->latest()
            ->first();

        return [
            'sections'           => $sections,
            'total_votable'      => $totalVotableMemos,
            'total_votes_cast'   => $totalVotesCast,
            'total_passed'       => collect($sections)->flatMap(fn($s) => $s['memos'])
                                        ->where('outcome', 'passed')->count(),
            'total_rejected'     => collect($sections)->flatMap(fn($s) => $s['memos'])
                                        ->where('outcome', 'rejected')->count(),
            'link'               => $linkStats,
        ];
    }

    private function authorizeAccess(Meeting $meeting): void
    {
        $ok = auth()->user()->hasAnyRole(['admin', 'md'])
            || $meeting->created_by === auth()->id();
        abort_unless($ok, 403);
    }
}
