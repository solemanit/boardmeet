{{-- views/meetings/edit.blade.php --}}
@extends('layouts.app')
@section('page-title', 'Edit ' . $meeting->meeting_number)
@section('title', 'Edit Meeting')

@section('content')

<div class="page-header pb-7 d-flex justify-content-between align-items-center gap-6 flex-wrap">
    <div>
        <h2 class="fw-semibold fs-7">Edit Meeting</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('meetings.index') }}">Meetings</a></li>
                <li class="breadcrumb-item"><a href="{{ route('meetings.show', $meeting) }}">{{ $meeting->meeting_number }}</a></li>
                <li class="breadcrumb-item active">Edit</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('meetings.agenda-builder', $meeting) }}"
           class="btn btn-outline-secondary d-flex align-items-center gap-2">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none">
                <path d="M9 12H15M9 16H12M7 3H5C3.89543 3 3 3.89543 3 5V19C3 20.1046 3.89543 21 5 21H19C20.1046 21 21 20.1046 21 19V5C21 3.89543 20.1046 3 19 3H17M7 3C7 3 7 5 12 5C17 5 17 3 17 3"
                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
            </svg>
            Agenda Builder
        </a>
        <a href="{{ route('meetings.minutes', $meeting) }}"
           class="btn btn-outline-primary d-flex align-items-center gap-2">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none">
                <path d="M11 4H4C3.44772 4 3 4.44772 3 5V20C3 20.5523 3.44772 21 4 21H19C19.5523 21 20 20.5523 20 19V12M18.5 2.5C19.3284 1.67157 20.6716 1.67157 21.5 2.5C22.3284 3.32843 22.3284 4.67157 21.5 5.5L12 15L8 16L9 12L18.5 2.5Z"
                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
            </svg>
            Minutes
        </a>
        <a href="{{ route('meetings.show', $meeting) }}" class="btn btn-outline-secondary">← Back</a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4">
        {{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show mb-4">
        {{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<form action="{{ route('meetings.update', $meeting) }}" method="POST" enctype="multipart/form-data">
@csrf @method('PUT')
<div class="row g-6">

    {{-- Main Column --}}
    <div class="col-lg-8">

        {{-- Meeting Details --}}
        <div class="pure-card rounded-custom card-bg shadow-custom mb-5">
            <div class="pure-card-header d-flex align-items-center justify-content-between">
                <h3 class="pure-card-title m-0">Meeting Details</h3>
                <div class="d-flex gap-2">
                    <span class="badge {{ $meeting->getStatusBadgeClass() }}">{{ ucfirst($meeting->status) }}</span>
                    <span class="badge {{ $meeting->getTypeBadgeClass() }}">{{ strtoupper($meeting->type) }}</span>
                </div>
            </div>
            <div class="pure-card-body">

                <div class="mb-4">
                    <label class="form-label fw-medium">Meeting Number</label>
                    <input type="text" class="form-control bg-light fw-semibold text-primary"
                        value="{{ $meeting->meeting_number }}" readonly>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-medium">Title <span class="text-danger">*</span></label>
                    <input type="text" name="title"
                        class="form-control @error('title') is-invalid @enderror"
                        value="{{ old('title', $meeting->title) }}"
                        placeholder="e.g. Q4 Board Meeting 2024">
                    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-medium">Meeting Type <span class="text-danger">*</span></label>
                        <select name="type" class="form-select @error('type') is-invalid @enderror">
                            @foreach(['board','committee','agm','egm','executive','other'] as $t)
                                <option value="{{ $t }}" @selected(old('type', $meeting->type) === $t)>{{ strtoupper($t) }}</option>
                            @endforeach
                        </select>
                        @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-medium">Venue Type <span class="text-danger">*</span></label>
                        <select name="venue_type" id="venueType"
                            class="form-select @error('venue_type') is-invalid @enderror">
                            @foreach(['physical','virtual','hybrid'] as $vt)
                                <option value="{{ $vt }}" @selected(old('venue_type', $meeting->venue_type) === $vt)>{{ ucfirst($vt) }}</option>
                            @endforeach
                        </select>
                        @error('venue_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="row g-4 mb-4">
                    <div class="col-md-4">
                        <label class="form-label fw-medium">Date <span class="text-danger">*</span></label>
                        <input type="date" name="date"
                            class="form-control @error('date') is-invalid @enderror"
                            value="{{ old('date', $meeting->date->format('Y-m-d')) }}">
                        @error('date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-medium">Start Time <span class="text-danger">*</span></label>
                        <input type="time" name="start_time"
                            class="form-control @error('start_time') is-invalid @enderror"
                            value="{{ old('start_time', $meeting->start_time) }}">
                        @error('start_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-medium">End Time</label>
                        <input type="time" name="end_time" class="form-control"
                            value="{{ old('end_time', $meeting->end_time) }}">
                    </div>
                </div>

                <div class="mb-4" id="venueField">
                    <label class="form-label fw-medium">Venue / Location</label>
                    <input type="text" name="venue" class="form-control"
                        value="{{ old('venue', $meeting->venue) }}"
                        placeholder="e.g. Boardroom A, 5th Floor">
                </div>

                <div class="mb-4" id="linkField">
                    <label class="form-label fw-medium">Meeting Link</label>
                    <input type="url" name="meeting_link" class="form-control"
                        value="{{ old('meeting_link', $meeting->meeting_link) }}"
                        placeholder="https://zoom.us/j/...">
                </div>

                <div class="mb-0">
                    <label class="form-label fw-medium">Description</label>
                    <textarea name="description" rows="3" class="form-control">{{ old('description', $meeting->description) }}</textarea>
                </div>

            </div>
        </div>

        {{-- People --}}
        <div class="pure-card rounded-custom card-bg shadow-custom mb-5">
            <div class="pure-card-header"><h3 class="pure-card-title m-0">People</h3></div>
            <div class="pure-card-body">

                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-medium">Chairperson</label>
                        <select name="chairperson_id" class="form-select">
                            <option value="">-- Select --</option>
                            @foreach($users as $u)
                                <option value="{{ $u->id }}"
                                    @selected(old('chairperson_id', $meeting->chairperson_id) == $u->id)>
                                    {{ $u->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-medium">Secretary</label>
                        <select name="secretary_id" class="form-select">
                            <option value="">-- Select --</option>
                            @foreach($users as $u)
                                <option value="{{ $u->id }}"
                                    @selected(old('secretary_id', $meeting->secretary_id) == $u->id)>
                                    {{ $u->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="form-label fw-medium">Attendees</label>
                    <small class="text-custom-paragraph d-block mb-2">Currently selected attendees are pre-checked.</small>
                    @php $currentAttendeeIds = $meeting->attendees->pluck('user_id')->toArray(); @endphp
                    <div class="border rounded-2 p-3" style="max-height:220px;overflow-y:auto;">
                        @foreach($users as $u)
                        <div class="form-check mb-1">
                            <input class="form-check-input" type="checkbox"
                                name="attendees[]" id="att_{{ $u->id }}" value="{{ $u->id }}"
                                @checked(in_array($u->id, old('attendees', $currentAttendeeIds)))>
                            <label class="form-check-label small" for="att_{{ $u->id }}">
                                {{ $u->name }}
                                @if($u->role)<span class="text-custom-paragraph">({{ $u->role->name }})</span>@endif
                            </label>
                        </div>
                        @endforeach
                    </div>
                </div>

            </div>
        </div>

        {{-- Documents --}}
        <div class="pure-card rounded-custom card-bg shadow-custom">
            <div class="pure-card-header"><h3 class="pure-card-title m-0">Documents</h3></div>
            <div class="pure-card-body">

                @if($meeting->documents->isNotEmpty())
                    <small class="text-custom-paragraph fw-medium d-block mb-2">CURRENT DOCUMENTS</small>
                    <div class="d-flex flex-column gap-2 mb-5">
                        @foreach($meeting->documents as $doc)
                        <div class="d-flex align-items-center gap-3 p-3 border rounded-2 bg-light">
                            <div class="flex-grow-1 min-w-0">
                                <p class="mb-0 fw-medium small text-truncate">{{ $doc->original_name }}</p>
                                <small class="text-custom-paragraph">
                                    {{ strtoupper($doc->file_type) }} &bull; {{ $doc->getFileSizeFormatted() }}
                                </small>
                            </div>
                            <form action="{{ route('meetings.documents.destroy', [$meeting, $doc]) }}"
                                method="POST" class="d-inline"
                                onsubmit="return confirm('Delete this document?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger flex-shrink-0">Remove</button>
                            </form>
                        </div>
                        @endforeach
                    </div>
                @endif

                <label class="form-label fw-medium">Add New Documents</label>
                <input type="file" name="documents[]" class="form-control" multiple
                    accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png,.gif,.bmp,.svg,.webp">
                <small class="text-custom-paragraph mt-1 d-block">Max 10MB each.</small>

            </div>
        </div>

    </div>

    {{-- Sidebar --}}
    <div class="col-lg-4">
        <div class="pure-card rounded-custom card-bg shadow-custom sticky-top" style="top:80px;">
            <div class="pure-card-header"><h3 class="pure-card-title m-0">Reference</h3></div>
            <div class="pure-card-body">
                <label class="form-label fw-medium">Previous Meeting</label>
                <select name="previous_meeting_id" class="form-select form-select-sm">
                    <option value="">-- None --</option>
                    @foreach($pastMeetings as $pm)
                        <option value="{{ $pm->id }}"
                            @selected(old('previous_meeting_id', $meeting->previous_meeting_id) == $pm->id)>
                            {{ $pm->meeting_number }} — {{ $pm->date->format('d M Y') }}
                        </option>
                    @endforeach
                </select>
                <small class="text-custom-paragraph mt-1 d-block">Link to previous minutes/reference.</small>

                @if($meeting->rescheduledFrom)
                    <div class="alert alert-info mt-3 small mb-0">
                        Rescheduled from
                        <a href="{{ route('meetings.show', $meeting->rescheduledFrom) }}">
                            {{ $meeting->rescheduledFrom->meeting_number }}
                        </a>
                        ({{ $meeting->rescheduledFrom->date->format('d M Y') }})
                    </div>
                @endif
            </div>
            <div class="pure-card-footer d-flex gap-3">
                <button type="submit" class="btn btn-primary flex-grow-1 d-flex align-items-center justify-content-center gap-2">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                        <path d="M20 6L9 17L4 12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                    Save Changes
                </button>
                <a href="{{ route('meetings.show', $meeting) }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </div>
    </div>

</div>
</form>

@push('scripts')
<script>
const venueType  = document.getElementById('venueType');
const venueField = document.getElementById('venueField');
const linkField  = document.getElementById('linkField');

function toggleVenueFields() {
    const v = venueType.value;
    venueField.style.display = (v === 'virtual')  ? 'none' : '';
    linkField.style.display  = (v === 'physical') ? 'none' : '';
}

venueType.addEventListener('change', toggleVenueFields);
toggleVenueFields();
</script>
@endpush

@endsection
