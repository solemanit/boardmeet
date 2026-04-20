<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Agenda;
use App\Models\AgendaDocument;
use App\Models\AgendaApproval;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;

class AgendaWebController extends Controller
{
    private const MAX_FILE_SIZE = 10240;

    private const ALLOWED_FILE_TYPES = [
        'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx',
        'jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg', 'webp',
    ];

    private const CATEGORIES = [
        'financial', 'operational', 'strategic', 'legal',
        'hr', 'it', 'marketing', 'governance', 'other',
    ];

    // ── Index ───────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $query = Agenda::with(['creator', 'assignee'])->latest();

        if (!auth()->user()->hasAnyRole(['admin', 'md'])) {
            $query->where('created_by', auth()->id());
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('agenda_number', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status'))   $query->where('status', $request->status);
        if ($request->filled('priority')) $query->where('priority', $request->priority);
        if ($request->filled('category')) $query->where('category', $request->category);

        $agendas    = $query->paginate(15)->withQueryString();
        $totalCount = $query->toBase()->getCountForPagination();

        $stats = [
            'total'          => Agenda::count(),
            'draft'          => Agenda::where('status', 'draft')->count(),
            'pending_review' => Agenda::where('status', 'pending_review')->count(),
            'approved'       => Agenda::where('status', 'approved')->count(),
            'rejected'       => Agenda::where('status', 'rejected')->count(),
        ];

        return view('agendas.index', compact('agendas', 'stats', 'totalCount'));
    }

    // ── Export PDF ──────────────────────────────────────────────────

    public function exportPdf(Request $request)
    {
        $query = Agenda::with(['creator', 'assignee'])->latest();

        if (!auth()->user()->hasAnyRole(['admin', 'md'])) {
            $query->where('created_by', auth()->id());
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('agenda_number', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status'))   $query->where('status', $request->status);
        if ($request->filled('priority')) $query->where('priority', $request->priority);
        if ($request->filled('category')) $query->where('category', $request->category);

        $agendas    = $query->get();
        $filters    = $request->only(['search', 'status', 'priority', 'category']);
        $exportedAt = now()->format('d M Y, H:i');

        $pdf = Pdf::loadView('agendas.exports.pdf', compact('agendas', 'filters', 'exportedAt'))
            ->setPaper('a4', 'landscape')
            ->setOptions([
                'defaultFont'     => 'sans-serif',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => false,
            ]);

        $filename = 'agendas-' . now()->format('Y-m-d') . '.pdf';

        return $pdf->download($filename);
    }

    // ── Print View ──────────────────────────────────────────────────

    public function printView(Request $request)
    {
        $query = Agenda::with(['creator', 'assignee'])->latest();

        if (!auth()->user()->hasAnyRole(['admin', 'md'])) {
            $query->where('created_by', auth()->id());
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('agenda_number', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status'))   $query->where('status', $request->status);
        if ($request->filled('priority')) $query->where('priority', $request->priority);
        if ($request->filled('category')) $query->where('category', $request->category);

        $agendas    = $query->get();
        $filters    = $request->only(['search', 'status', 'priority', 'category']);
        $exportedAt = now()->format('d M Y, H:i');

        return view('agendas.exports.print', compact('agendas', 'filters', 'exportedAt'));
    }

    // ── Create ──────────────────────────────────────────────────────

    public function create()
    {
        $agendaNumber = Agenda::generateAgendaNumber();
        $categories   = self::CATEGORIES;
        $users        = User::where('is_active', true)->get();

        return view('agendas.create', compact('agendaNumber', 'categories', 'users'));
    }

    // ── Store ───────────────────────────────────────────────────────

    public function store(Request $request)
    {
        $request->validate([
            'title'               => 'required|string|max:255',
            'description'         => 'nullable|string',
            'priority'            => 'required|in:low,medium,high,urgent',
            'category'            => 'nullable|string',
            'assigned_to'         => 'nullable|exists:users,id',
            'meeting_date'        => 'nullable|date',
            'main_document'       => 'nullable|file|mimes:' . implode(',', self::ALLOWED_FILE_TYPES) . '|max:' . self::MAX_FILE_SIZE,
            'supporting_docs'     => 'nullable|array',
            'supporting_docs.*'   => 'file|mimes:' . implode(',', self::ALLOWED_FILE_TYPES) . '|max:' . self::MAX_FILE_SIZE,
        ]);

        DB::beginTransaction();
        try {
            $agenda = Agenda::create([
                'agenda_number' => Agenda::generateAgendaNumber(),
                'title'         => $request->title,
                'description'   => $request->description,
                'priority'      => $request->priority,
                'category'      => $request->category,
                'assigned_to'   => $request->assigned_to,
                'meeting_date'  => $request->meeting_date,
                'status'        => 'draft',
                'version'       => 1,
                'created_by'    => auth()->id(),
            ]);

            if ($request->hasFile('main_document')) {
                $this->uploadDocument($agenda, $request->file('main_document'), 'main');
            }

            if ($request->hasFile('supporting_docs')) {
                foreach ($request->file('supporting_docs') as $file) {
                    $this->uploadDocument($agenda, $file, 'supporting');
                }
            }

            AgendaApproval::create([
                'agenda_id' => $agenda->id,
                'user_id'   => auth()->id(),
                'action'    => 'submitted',
                'comment'   => 'Agenda created as draft.',
                'version'   => 1,
            ]);

            DB::commit();

            return redirect()
                ->route('agendas.show', $agenda)
                ->with('success', "Agenda {$agenda->agenda_number} created successfully!");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to create agenda. Please try again.');
        }
    }

    // ── Show ────────────────────────────────────────────────────────

    public function show(Agenda $agenda)
    {
        $agenda->load([
            'creator', 'assignee', 'approver',
            'documents.uploader',
            'approvalHistory.user',
        ]);

        return view('agendas.show', compact('agenda'));
    }

    // ── Edit ────────────────────────────────────────────────────────

    public function edit(Agenda $agenda)
    {
        if (!$agenda->canBeEdited()) {
            return redirect()
                ->route('agendas.show', $agenda)
                ->with('error', 'This agenda cannot be edited in its current status.');
        }

        $categories = self::CATEGORIES;
        $users      = User::where('is_active', true)->get();
        $agenda->load('documents');

        return view('agendas.edit', compact('agenda', 'categories', 'users'));
    }

    // ── Update ──────────────────────────────────────────────────────

    public function update(Request $request, Agenda $agenda)
    {
        if (!$agenda->canBeEdited()) {
            return redirect()
                ->route('agendas.show', $agenda)
                ->with('error', 'This agenda cannot be edited in its current status.');
        }

        $request->validate([
            'title'               => 'required|string|max:255',
            'description'         => 'nullable|string',
            'priority'            => 'required|in:low,medium,high,urgent',
            'category'            => 'nullable|string',
            'assigned_to'         => 'nullable|exists:users,id',
            'meeting_date'        => 'nullable|date',
            'revision_notes'      => 'nullable|string',
            'main_document'       => 'nullable|file|mimes:' . implode(',', self::ALLOWED_FILE_TYPES) . '|max:' . self::MAX_FILE_SIZE,
            'supporting_docs'     => 'nullable|array',
            'supporting_docs.*'   => 'file|mimes:' . implode(',', self::ALLOWED_FILE_TYPES) . '|max:' . self::MAX_FILE_SIZE,
        ]);

        DB::beginTransaction();
        try {
            $isRevision = $agenda->isRejected();

            $agenda->update([
                'title'          => $request->title,
                'description'    => $request->description,
                'priority'       => $request->priority,
                'category'       => $request->category,
                'assigned_to'    => $request->assigned_to,
                'meeting_date'   => $request->meeting_date,
                'revision_notes' => $request->revision_notes,
                'status'         => $isRevision ? 'revised' : $agenda->status,
                'version'        => $isRevision ? $agenda->version + 1 : $agenda->version,
            ]);

            if ($request->hasFile('main_document')) {
                $existing = $agenda->mainDocument;
                if ($existing) {
                    Storage::disk('public')->delete($existing->file_path);
                    $existing->delete();
                }
                $this->uploadDocument($agenda, $request->file('main_document'), 'main');
            }

            if ($request->hasFile('supporting_docs')) {
                foreach ($request->file('supporting_docs') as $file) {
                    $this->uploadDocument($agenda, $file, 'supporting');
                }
            }

            if ($isRevision) {
                AgendaApproval::create([
                    'agenda_id' => $agenda->id,
                    'user_id'   => auth()->id(),
                    'action'    => 'revised',
                    'comment'   => $request->revision_notes ?? 'Agenda revised after rejection.',
                    'version'   => $agenda->version,
                ]);
            }

            DB::commit();

            return redirect()
                ->route('agendas.show', $agenda)
                ->with('success', 'Agenda updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to update agenda. Please try again.');
        }
    }

    // ── Delete ──────────────────────────────────────────────────────

    public function destroy(Agenda $agenda)
    {
        if ($agenda->isApproved()) {
            return redirect()
                ->route('agendas.index')
                ->with('error', 'Approved agendas cannot be deleted.');
        }

        foreach ($agenda->documents as $doc) {
            Storage::disk('public')->delete($doc->file_path);
        }

        $agenda->delete();

        return redirect()
            ->route('agendas.index')
            ->with('success', 'Agenda deleted successfully!');
    }

    // ── Submit for Review ───────────────────────────────────────────

    public function submit(Request $request, Agenda $agenda)
    {
        if (!$agenda->canBeSubmitted()) {
            return redirect()
                ->route('agendas.show', $agenda)
                ->with('error', 'This agenda cannot be submitted for review.');
        }

        $agenda->update([
            'status'       => 'pending_review',
            'submitted_at' => now(),
        ]);

        AgendaApproval::create([
            'agenda_id' => $agenda->id,
            'user_id'   => auth()->id(),
            'action'    => 'submitted',
            'comment'   => $request->input('comment', 'Submitted for approval.'),
            'version'   => $agenda->version,
        ]);

        return redirect()
            ->route('agendas.show', $agenda)
            ->with('success', 'Agenda submitted for review successfully!');
    }

    // ── Approve ─────────────────────────────────────────────────────

    public function approve(Request $request, Agenda $agenda)
    {
        if (!auth()->user()->hasAnyRole(['admin', 'md'])) {
            abort(403, 'Unauthorized action.');
        }

        if (!$agenda->isPendingReview()) {
            return redirect()
                ->route('agendas.show', $agenda)
                ->with('error', 'Only pending agendas can be approved.');
        }

        $request->validate(['comment' => 'nullable|string|max:1000']);

        $agenda->update([
            'status'      => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        AgendaApproval::create([
            'agenda_id' => $agenda->id,
            'user_id'   => auth()->id(),
            'action'    => 'approved',
            'comment'   => $request->comment ?? 'Agenda approved.',
            'version'   => $agenda->version,
        ]);

        return redirect()
            ->route('agendas.show', $agenda)
            ->with('success', 'Agenda approved successfully!');
    }

    // ── Reject ──────────────────────────────────────────────────────

    public function reject(Request $request, Agenda $agenda)
    {
        if (!auth()->user()->hasAnyRole(['admin', 'md'])) {
            abort(403, 'Unauthorized action.');
        }

        if (!$agenda->isPendingReview()) {
            return redirect()
                ->route('agendas.show', $agenda)
                ->with('error', 'Only pending agendas can be rejected.');
        }

        $request->validate(['rejection_reason' => 'required|string|max:1000']);

        $agenda->update([
            'status'           => 'rejected',
            'rejection_reason' => $request->rejection_reason,
        ]);

        AgendaApproval::create([
            'agenda_id' => $agenda->id,
            'user_id'   => auth()->id(),
            'action'    => 'rejected',
            'comment'   => $request->rejection_reason,
            'version'   => $agenda->version,
        ]);

        return redirect()
            ->route('agendas.show', $agenda)
            ->with('success', 'Agenda rejected. Creator has been notified.');
    }

    // ── Delete Document ─────────────────────────────────────────────

    public function deleteDocument(Agenda $agenda, AgendaDocument $document)
    {
        if ($document->agenda_id !== $agenda->id) abort(404);

        if (!$agenda->canBeEdited()) {
            return response()->json(['error' => 'Cannot delete document in current status.'], 422);
        }

        Storage::disk('public')->delete($document->file_path);
        $document->delete();

        return redirect()
            ->route('agendas.edit', $agenda)
            ->with('success', 'Document deleted successfully!');
    }

    // ── Private Helpers ─────────────────────────────────────────────

    private function uploadDocument(Agenda $agenda, $file, string $type): AgendaDocument
    {
        $ext      = $file->getClientOriginalExtension();
        $fileName = Str::uuid() . ($ext ? ".{$ext}" : '');
        $path     = $file->storeAs("agendas/{$agenda->id}", $fileName, 'public');

        return AgendaDocument::create([
            'agenda_id'     => $agenda->id,
            'file_name'     => $fileName,
            'original_name' => $file->getClientOriginalName(),
            'file_path'     => $path,
            'file_type'     => strtolower($ext),
            'file_size'     => $file->getSize(),
            'document_type' => $type,
            'uploaded_by'   => auth()->id(),
        ]);
    }
}
