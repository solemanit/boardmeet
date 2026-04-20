<?php
// app/Http/Controllers/Web/CircularWebController.php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCircularRequest;
use App\Http\Requests\UpdateCircularRequest;
use App\Mail\CircularReminderMail;
use App\Models\Circular;
use App\Models\CircularAttachment;
use App\Models\CircularReminderLog;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CircularWebController extends Controller
{
    // ── Index ─────────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $query = Circular::with(['createdBy', 'attachments'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(fn($b) =>
                $b->where('title', 'like', "%$q%")
                  ->orWhere('reference_number', 'like', "%$q%")
                  ->orWhere('body', 'like', "%$q%")
            );
        }

        $circulars = $query->paginate(15)->withQueryString();

        $stats = [
            'total'     => Circular::count(),
            'published' => Circular::published()->count(),
            'draft'     => Circular::draft()->count(),
        ];

        return view('circulars.index', compact('circulars', 'stats'));
    }

    // ── Create ────────────────────────────────────────────────────────────
    public function create()
    {
        return view('circulars.create');
    }

    // ── Store ─────────────────────────────────────────────────────────────
    public function store(StoreCircularRequest $request)
    {
        DB::transaction(function () use ($request, &$circular) {
            $data = $request->safe()->except('attachments');
            $data['created_by'] = auth()->id();
            $data['updated_by'] = auth()->id();

            if ($data['status'] === 'published') {
                $data['published_at'] = now();
            }

            $circular = Circular::create($data);
            $this->handleAttachments($request, $circular);
        });

        return redirect()->route('circulars.show', $circular)
            ->with('success', 'Circular created successfully.');
    }

    // ── Show ──────────────────────────────────────────────────────────────
    public function show(Circular $circular)
    {
        $circular->load(['attachments', 'createdBy', 'updatedBy', 'reminderLogs.sentBy']);
        return view('circulars.show', compact('circular'));
    }

    // ── Edit ──────────────────────────────────────────────────────────────
    public function edit(Circular $circular)
    {
        $circular->load('attachments');
        return view('circulars.edit', compact('circular'));
    }

    // ── Update ────────────────────────────────────────────────────────────
    public function update(UpdateCircularRequest $request, Circular $circular)
    {
        DB::transaction(function () use ($request, $circular) {
            $data = $request->safe()->except('attachments');
            $data['updated_by'] = auth()->id();

            // Set published_at only on first publish
            if ($data['status'] === 'published' && ! $circular->published_at) {
                $data['published_at'] = now();
            }

            $circular->update($data);
            $this->handleAttachments($request, $circular);
        });

        return redirect()->route('circulars.show', $circular)
            ->with('success', 'Circular updated successfully.');
    }

    // ── Destroy ───────────────────────────────────────────────────────────
    public function destroy(Circular $circular)
    {
        // Delete physical files
        foreach ($circular->attachments as $attachment) {
            Storage::disk('public')->delete($attachment->path);
        }

        $circular->delete();

        return redirect()->route('circulars.index')
            ->with('success', 'Circular deleted successfully.');
    }

    // ── Publish / Unpublish ───────────────────────────────────────────────
    public function publish(Circular $circular)
    {
        $circular->update([
            'status'       => 'published',
            'published_at' => $circular->published_at ?? now(),
            'updated_by'   => auth()->id(),
        ]);

        return back()->with('success', 'Circular published successfully.');
    }

    public function unpublish(Circular $circular)
    {
        $circular->update([
            'status'     => 'draft',
            'updated_by' => auth()->id(),
        ]);

        return back()->with('success', 'Circular moved back to draft.');
    }

    // ── Send Reminder to All Active Members ───────────────────────────────
    public function sendReminder(Circular $circular)
    {
        if (! $circular->isPublished()) {
            return back()->with('error', 'Only published circulars can be sent as reminders.');
        }

        $members = Member::active()->whereNotNull('email')->get();

        if ($members->isEmpty()) {
            return back()->with('warning', 'No active members with email found.');
        }

        $circular->load('attachments');

        foreach ($members as $member) {
            Mail::to($member->email)
                ->queue(new CircularReminderMail($circular, $member->name));
        }

        CircularReminderLog::create([
            'circular_id'     => $circular->id,
            'sent_by'         => auth()->id(),
            'recipient_count' => $members->count(),
            'sent_at'         => now(),
        ]);

        return back()->with('success', "Reminder queued for {$members->count()} member(s) successfully.");
    }

    // ── Delete Single Attachment ───────────────────────────────────────────
    public function deleteAttachment(Circular $circular, CircularAttachment $attachment)
    {
        abort_if($attachment->circular_id !== $circular->id, 404);

        Storage::disk('public')->delete($attachment->path);
        $attachment->delete();

        return back()->with('success', 'Attachment removed.');
    }

    // ── Private: Handle File Uploads ──────────────────────────────────────
    private function handleAttachments(Request $request, Circular $circular): void
    {
        if (! $request->hasFile('attachments')) {
            return;
        }

        foreach ($request->file('attachments') as $file) {
            $path = $file->store(
                'circulars/' . $circular->id,
                'public'
            );

            $circular->attachments()->create([
                'original_name' => $file->getClientOriginalName(),
                'path'          => $path,
                'mime_type'     => $file->getMimeType(),
                'size'          => $file->getSize(),
            ]);
        }
    }
}
