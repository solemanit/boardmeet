{{-- views/meetings/show.blade.php --}}
@extends('layouts.app')
@section('page-title', $meeting->meeting_number)
@section('title', 'Meeting: ' . $meeting->title)

@section('content')

    <div class="page-header pb-7 d-flex justify-content-between align-items-center gap-6 flex-wrap">
        <div>
            <div class="d-flex align-items-center gap-3 mb-1">
                <h2 class="fw-semibold fs-7 mb-0">{{ $meeting->meeting_number }}</h2>
                <span class="badge {{ $meeting->getStatusBadgeClass() }} fs-6">{{ ucfirst($meeting->status) }}</span>
                <span class="badge {{ $meeting->getTypeBadgeClass() }}">{{ strtoupper($meeting->type) }}</span>
            </div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('meetings.index') }}">Meetings</a></li>
                    <li class="breadcrumb-item active">{{ $meeting->meeting_number }}</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">

            {{-- Agenda Builder --}}
            <a href="{{ route('meetings.agenda-builder', $meeting) }}"
                class="btn btn-outline-secondary d-flex align-items-center gap-2">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none">
                    <path
                        d="M9 12H15M9 16H12M7 3H5C3.89543 3 3 3.89543 3 5V19C3 20.1046 3.89543 21 5 21H19C20.1046 21 21 20.1046 21 19V5C21 3.89543 20.1046 3 19 3H17M7 3C7 3 7 5 12 5C17 5 17 3 17 3"
                        stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                </svg>
                Agenda
            </a>

            {{-- Minutes --}}
            <a href="{{ route('meetings.minutes', $meeting) }}"
                class="btn btn-outline-primary d-flex align-items-center gap-2">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none">
                    <path
                        d="M11 4H4C3.44772 4 3 4.44772 3 5V20C3 20.5523 3.44772 21 4 21H19C19.5523 21 20 20.5523 20 19V12M18.5 2.5C19.3284 1.67157 20.6716 1.67157 21.5 2.5C22.3284 3.32843 22.3284 4.67157 21.5 5.5L12 15L8 16L9 12L18.5 2.5Z"
                        stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                </svg>
                Minutes
            </a>

            {{-- Edit --}}
            @if ($meeting->canBeEdited())
                <a href="{{ route('meetings.edit', $meeting) }}"
                    class="btn btn-outline-secondary d-flex align-items-center gap-2">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none">
                        <path
                            d="M11 4H4C3.44772 4 3 4.44772 3 5V20C3 20.5523 3.44772 21 4 21H19C19.5523 21 20 20.5523 20 19V12M18.5 2.5C19.3284 1.67157 20.6716 1.67157 21.5 2.5C22.3284 3.32843 22.3284 4.67157 21.5 5.5L12 15L8 16L9 12L18.5 2.5Z"
                            stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                    </svg>
                    Edit
                </a>
            @endif

            {{-- iCal download --}}
            <a href="{{ route('meetings.ical', $meeting) }}"
                class="btn btn-outline-secondary d-flex align-items-center gap-2">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none">
                    <rect x="3" y="4" width="18" height="18" rx="2" stroke="currentColor"
                        stroke-width="1.5" />
                    <path d="M16 2V6M8 2V6M3 10H21" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                </svg>
                iCal
            </a>

            {{-- Complete --}}
            @if ($meeting->isScheduled() || $meeting->isOngoing())
                <form action="{{ route('meetings.complete', $meeting) }}" method="POST" class="d-inline"
                    onsubmit="return confirm('Mark this meeting as completed?')">
                    @csrf
                    <button type="submit" class="btn btn-success d-flex align-items-center gap-2">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none">
                            <path d="M20 6L9 17L4 12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                        </svg>
                        Complete
                    </button>
                </form>
            @endif

            {{-- Cancel --}}
            @if ($meeting->canBeCancelled())
                <button class="btn btn-outline-danger d-flex align-items-center gap-2" data-bs-toggle="modal"
                    data-bs-target="#cancelModal">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none">
                        <path d="M18 6L6 18M6 6L18 18" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                    </svg>
                    Cancel
                </button>
            @endif

            {{-- Export Agenda PDF --}}
            <a href="{{ route('meetings.print.agenda', $meeting) }}" target="_blank"
                class="btn btn-outline-secondary d-flex align-items-center gap-2">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none">
                    <path
                        d="M9 12H15M9 16H12M7 3H5C3.89543 3 3 3.89543 3 5V19C3 20.1046 3.89543 21 5 21H19C20.1046 21 21 20.1046 21 19V5C21 3.89543 20.1046 3 19 3H17M7 3C7 3 7 5 12 5C17 5 17 3 17 3"
                        stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                </svg>
                Agenda PDF
            </a>

            {{-- Export Minutes PDF --}}
            <a href="{{ route('meetings.print.minutes', $meeting) }}" target="_blank"
                class="btn btn-outline-secondary d-flex align-items-center gap-2">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none">
                    <path
                        d="M7 17H17M7 13H17M7 9H11M5 3H19C19.5523 3 20 3.44772 20 4V20C20 20.5523 19.5523 21 19 21H5C4.44772 21 4 20.5523 4 20V4C4 3.44772 4.44772 3 5 3Z"
                        stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                </svg>
                Minutes PDF
            </a>

            <a href="{{ route('meetings.index') }}" class="btn btn-outline-secondary">Back</a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4">
            {{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-4">
            {{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if ($meeting->isCancelled() && $meeting->cancellation_reason)
        <div class="alert alert-danger d-flex align-items-start gap-3 mb-5">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" class="flex-shrink-0 mt-1">
                <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.5" />
                <path d="M12 8V12M12 16H12.01" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
            </svg>
            <div>
                <strong>Cancellation Reason:</strong>
                <p class="mb-0 mt-1">{{ $meeting->cancellation_reason }}</p>
            </div>
        </div>
    @endif

    <div class="row g-6">

        {{-- ── Main ──────────────────────────────────────────────────── --}}
        <div class="col-lg-8">

            {{-- Details --}}
            <div class="pure-card rounded-custom card-bg shadow-custom mb-5">
                <div class="pure-card-header">
                    <h3 class="pure-card-title m-0">Meeting Details</h3>
                </div>
                <div class="pure-card-body">
                    <h4 class="fw-semibold mb-3">{{ $meeting->title }}</h4>

                    @if ($meeting->description)
                        <p class="text-custom-body mb-4" style="white-space:pre-line;">{{ $meeting->description }}</p>
                    @else
                        <p class="text-custom-paragraph fst-italic mb-4">No description provided.</p>
                    @endif

                    <div class="row g-4">
                        <div class="col-sm-6 col-md-3">
                            <small class="text-custom-paragraph d-block mb-1">Date</small>
                            <span class="fw-medium">{{ $meeting->date->format('d M Y') }}</span>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <small class="text-custom-paragraph d-block mb-1">Time</small>
                            <span class="fw-medium">
                                {{ $meeting->start_time }}{{ $meeting->end_time ? ' – ' . $meeting->end_time : '' }}
                            </span>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <small class="text-custom-paragraph d-block mb-1">Venue Type</small>
                            <span class="fw-medium">{{ ucfirst($meeting->venue_type) }}</span>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <small class="text-custom-paragraph d-block mb-1">Location</small>
                            @if ($meeting->venue)
                                <span class="fw-medium">{{ $meeting->venue }}</span>
                            @elseif($meeting->meeting_link)
                                <a href="{{ $meeting->meeting_link }}" target="_blank" class="fw-medium small">Join
                                    Link</a>
                            @else
                                <span class="text-custom-paragraph">—</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

{{-- Agenda Items (structured: sections → memos) --}}
<div class="pure-card rounded-custom card-bg shadow-custom mb-5">
    <div class="pure-card-header d-flex align-items-center justify-content-between">
        <h3 class="pure-card-title m-0">Agenda Items</h3>
        <a href="{{ route('meetings.agenda-builder', $meeting) }}"
            class="btn btn-sm btn-outline-secondary">Edit Agenda</a>
    </div>
    <div class="pure-card-body p-0">
        @php $hasMemos = $meeting->agendaSections->flatMap->memos->isNotEmpty(); @endphp

        @forelse($meeting->agendaSections as $si => $section)
            {{-- Section header --}}
            <div class="d-flex align-items-center gap-2 px-4 py-2 border-bottom"
                 style="background: var(--bs-gray-100, #f8f9fa);">
                <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-primary text-white fw-semibold flex-shrink-0"
                      style="width:22px;height:22px;font-size:10px;min-width:22px;">
                    {{ $si + 1 }}
                </span>
                <span class="fw-semibold small">{{ $section->title }}</span>
                <span class="badge bg-secondary ms-auto" style="font-size:10px;">
                    {{ $section->memos->count() }} memo{{ $section->memos->count() !== 1 ? 's' : '' }}
                </span>
            </div>

            {{-- Memos under this section --}}
            @forelse($section->memos as $mi => $memo)
                <div class="d-flex gap-3 px-4 py-3 border-bottom" style="padding-left: 3rem !important;">
                    <span class="d-inline-flex align-items-center justify-content-center rounded-circle text-white fw-semibold flex-shrink-0"
                          style="width:22px;height:22px;font-size:10px;min-width:22px;margin-top:3px;
                                 background: var(--bs-secondary, #6c757d);">
                        {{ $mi + 1 }}
                    </span>
                    <div class="flex-grow-1">
                        <p class="mb-0 fw-medium small">{{ $memo->title }}</p>

                        @if($memo->description)
                            <p class="mb-0 small text-custom-paragraph mt-1" style="white-space:pre-line;">
                                {{ $memo->description }}
                            </p>
                        @endif

                        {{-- Vote badge --}}
                        @if($memo->vote_required !== 'none')
                            <span class="badge mt-1 {{ $memo->vote_required === 'required' ? 'bg-warning text-dark' : 'bg-info text-dark' }}"
                                  style="font-size:10px;">
                                Vote: {{ ucfirst($memo->vote_required) }}
                            </span>
                        @endif

                        {{-- Minutes note --}}
                        @if($memo->minutes_note)
                            <div class="mt-2 p-2 rounded-2 bg-light">
                                <small class="text-custom-paragraph fw-medium d-block mb-1">Minutes</small>
                                <p class="mb-0 small" style="white-space:pre-line;">{{ $memo->minutes_note }}</p>
                            </div>
                        @endif
                    </div>

                    {{-- Document count --}}
                    @if($memo->documents->isNotEmpty())
                        <span class="text-custom-paragraph small flex-shrink-0 mt-1">
                            📎 {{ $memo->documents->count() }}
                        </span>
                    @endif
                </div>
            @empty
                <div class="px-4 py-2 border-bottom">
                    <small class="text-custom-paragraph fst-italic ms-4">No memos in this section.</small>
                </div>
            @endforelse

        @empty
            <div class="px-4 py-4 text-center text-custom-paragraph fst-italic small">
                No agenda items yet.
                <a href="{{ route('meetings.agenda-builder', $meeting) }}">Build agenda →</a>
            </div>
        @endforelse
    </div>
</div>

            {{-- Attendees --}}
            <div class="pure-card rounded-custom card-bg shadow-custom mb-5">
                <div class="pure-card-header">
                    <h3 class="pure-card-title m-0">Attendees</h3>
                </div>
                <div class="pure-card-body p-0">
                    @forelse($meeting->attendees as $attendee)
                        <div class="d-flex align-items-center gap-3 px-4 py-3 border-bottom">
                            <div class="d-flex align-items-center justify-content-center rounded-circle bg-primary text-white fw-semibold flex-shrink-0"
                                style="width:34px;height:34px;font-size:12px;">
                                {{ strtoupper(substr($attendee->user?->name ?? 'U', 0, 1)) }}
                            </div>
                            <div class="flex-grow-1">
                                <p class="mb-0 fw-medium small">{{ $attendee->user?->name ?? '—' }}</p>
                                <small class="text-custom-paragraph">{{ $attendee->user?->role?->name ?? '' }}</small>
                            </div>
                            <span
                                class="badge {{ match ($attendee->status) {
                                    'attended' => 'badge-label-success',
                                    'confirmed' => 'badge-label-info',
                                    'declined' => 'badge-label-danger',
                                    'absent' => 'badge-label-secondary',
                                    default => 'badge-label-warning',
                                } }}">{{ ucfirst($attendee->status) }}</span>

                            {{-- RSVP for current user --}}
                            @if (auth()->id() === $attendee->user_id && in_array($attendee->status, ['invited']))
                                <form action="{{ route('meetings.rsvp', $meeting) }}" method="POST"
                                    class="d-flex gap-1">
                                    @csrf
                                    <button name="status" value="confirmed"
                                        class="btn btn-xs btn-outline-success btn-sm">Accept</button>
                                    <button name="status" value="declined"
                                        class="btn btn-xs btn-outline-danger btn-sm">Decline</button>
                                </form>
                            @endif
                        </div>
                    @empty
                        <div class="px-4 py-3 text-custom-paragraph small fst-italic">No attendees added.</div>
                    @endforelse
                </div>
            </div>

            {{-- Documents --}}
            @if ($meeting->documents->isNotEmpty())
                <div class="pure-card rounded-custom card-bg shadow-custom">
                    <div class="pure-card-header">
                        <h3 class="pure-card-title m-0">Documents</h3>
                    </div>
                    <div class="pure-card-body">
                        <div class="d-flex flex-column gap-2">
                            @foreach ($meeting->documents as $doc)
                                <div class="d-flex align-items-center gap-3 p-3 border rounded-2">
                                    <div class="flex-grow-1 min-w-0">
                                        <p class="mb-0 fw-medium small text-truncate">{{ $doc->original_name }}</p>
                                        <small class="text-custom-paragraph">
                                            {{ strtoupper($doc->file_type) }} &bull; {{ $doc->getFileSizeFormatted() }}
                                        </small>
                                    </div>
                                    <a href="{{ Storage::url($doc->file_path) }}" target="_blank"
                                        class="btn btn-sm btn-outline-secondary flex-shrink-0">Download</a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

        </div>

        {{-- ── Sidebar ─────────────────────────────────────────────────── --}}
        <div class="col-lg-4">
            <div class="pure-card rounded-custom card-bg shadow-custom">
                <div class="pure-card-header">
                    <h3 class="pure-card-title m-0">Info</h3>
                </div>
                <div class="pure-card-body">
                    <dl class="row g-0 mb-0">
                        <dt class="col-5 text-custom-paragraph small fw-medium pb-3">Created By</dt>
                        <dd class="col-7 small pb-3">{{ $meeting->creator?->name ?? '—' }}</dd>

                        <dt class="col-5 text-custom-paragraph small fw-medium pb-3">Chairperson</dt>
                        <dd class="col-7 small pb-3">{{ $meeting->chairperson?->name ?? '—' }}</dd>

                        <dt class="col-5 text-custom-paragraph small fw-medium pb-3">Secretary</dt>
                        <dd class="col-7 small pb-3">{{ $meeting->secretary?->name ?? '—' }}</dd>

                        <dt class="col-5 text-custom-paragraph small fw-medium pb-3">Agenda Items</dt>
                        <dd class="col-7 small pb-3">{{ $meeting->agendaItems->count() }}</dd>

                        <dt class="col-5 text-custom-paragraph small fw-medium pb-3">Attendees</dt>
                        <dd class="col-7 small pb-3">{{ $meeting->attendees->count() }}</dd>

                        @if ($meeting->previousMeeting)
                            <dt class="col-5 text-custom-paragraph small fw-medium pb-3">Previous</dt>
                            <dd class="col-7 small pb-3">
                                <a href="{{ route('meetings.show', $meeting->previousMeeting) }}">
                                    {{ $meeting->previousMeeting->meeting_number }}
                                </a>
                            </dd>
                        @endif

                        @if ($meeting->rescheduledFrom)
                            <dt class="col-5 text-custom-paragraph small fw-medium pb-3">Rescheduled From</dt>
                            <dd class="col-7 small pb-3">
                                <a href="{{ route('meetings.show', $meeting->rescheduledFrom) }}">
                                    {{ $meeting->rescheduledFrom->meeting_number }}
                                </a>
                            </dd>
                        @endif

                        <dt class="col-5 text-custom-paragraph small fw-medium pb-3">Created</dt>
                        <dd class="col-7 small pb-3">{{ $meeting->created_at->format('d M Y') }}</dd>

                        <dt class="col-5 text-custom-paragraph small fw-medium">Last Updated</dt>
                        <dd class="col-7 small">{{ $meeting->updated_at->diffForHumans() }}</dd>
                    </dl>
                </div>

                {{-- Notify attendees --}}
                @if ($meeting->isScheduled())
                    <div class="pure-card-footer">
                        <form action="{{ route('meetings.notify', $meeting) }}" method="POST"
                            onsubmit="return confirm('Send notifications to all attendees?')">
                            @csrf
                            <button type="submit"
                                class="btn btn-outline-primary w-100 d-flex align-items-center justify-content-center gap-2">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none">
                                    <path d="M22 2L11 13M22 2L15 22L11 13M22 2L2 9L11 13" stroke="currentColor"
                                        stroke-width="1.5" stroke-linecap="round" />
                                </svg>
                                Send Notifications
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>

    </div>

    {{-- ── Cancel Modal ────────────────────────────────────────────────── --}}
    @if ($meeting->canBeCancelled())
        <div class="modal fade" id="cancelModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title fw-semibold text-danger">Cancel Meeting</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('meetings.cancel', $meeting) }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <p class="text-custom-body">
                                Please provide a reason for cancelling
                                <strong>{{ $meeting->meeting_number }}</strong>.
                                All attendees will be notified.
                            </p>
                            <div class="mb-3">
                                <label class="form-label fw-medium">
                                    Reason <span class="text-danger">*</span>
                                </label>
                                <textarea name="cancellation_reason" class="form-control" rows="3"
                                    placeholder="Explain why this meeting is being cancelled..." required></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary"
                                data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-danger">Cancel Meeting</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- ── Reschedule Modal ────────────────────────────────────────────── --}}
    @if ($meeting->canBeRescheduled())
        <div class="modal fade" id="rescheduleModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title fw-semibold">Reschedule Meeting</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('meetings.reschedule', $meeting) }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-medium">New Date <span
                                            class="text-danger">*</span></label>
                                    <input type="date" name="date" class="form-control"
                                        min="{{ now()->format('Y-m-d') }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-medium">Start Time <span
                                            class="text-danger">*</span></label>
                                    <input type="time" name="start_time" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-medium">End Time</label>
                                    <input type="time" name="end_time" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-medium">Venue (optional)</label>
                                    <input type="text" name="venue" class="form-control"
                                        value="{{ $meeting->venue }}" placeholder="Leave blank to keep current">
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-medium">Reason</label>
                                    <textarea name="reason" class="form-control" rows="2" placeholder="Reason for rescheduling..."></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary"
                                data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Reschedule</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

@endsection
