{{-- views/meetings/create.blade.php --}}
@extends('layouts.app')
@section('page-title', 'Create Meeting')
@section('title', 'Create Meeting')

@section('content')

    <div class="page-header pb-7 d-flex justify-content-between align-items-center gap-6 flex-wrap">
        <div>
            <h2 class="fw-semibold fs-7">Create Meeting</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('meetings.index') }}">Meetings</a></li>
                    <li class="breadcrumb-item active">Create</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('meetings.index') }}" class="btn btn-outline-secondary">← Back</a>
    </div>

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-4">
            {{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-4">
            <strong>Please fix the following errors:</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('meetings.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row g-6">

            {{-- Main Column --}}
            <div class="col-lg-8">

                {{-- Meeting Details --}}
                <div class="pure-card rounded-custom card-bg shadow-custom mb-5">
                    <div class="pure-card-header">
                        <h3 class="pure-card-title m-0">Meeting Details</h3>
                    </div>
                    <div class="pure-card-body">

                        <div class="mb-4">
                            <label class="form-label fw-medium">Meeting Number</label>
                            <input type="text" class="form-control bg-light fw-semibold text-primary"
                                value="{{ $meetingNumber }}" readonly>
                            <small class="text-custom-paragraph mt-1 d-block">Auto-generated</small>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-medium">Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                                value="{{ old('title') }}" placeholder="e.g. Q4 Board Meeting 2024">
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-medium">Meeting Type <span class="text-danger">*</span></label>
                                <select name="type" class="form-select @error('type') is-invalid @enderror">
                                    @foreach (['board', 'committee', 'agm', 'egm', 'executive', 'other'] as $t)
                                        <option value="{{ $t }}" @selected(old('type', 'board') === $t)>
                                            {{ strtoupper($t) }}</option>
                                    @endforeach
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-medium">Venue Type <span class="text-danger">*</span></label>
                                <select name="venue_type" id="venueType"
                                    class="form-select @error('venue_type') is-invalid @enderror">
                                    @foreach (['physical', 'virtual', 'hybrid'] as $vt)
                                        <option value="{{ $vt }}" @selected(old('venue_type', 'physical') === $vt)>
                                            {{ ucfirst($vt) }}</option>
                                    @endforeach
                                </select>
                                @error('venue_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row g-4 mb-4">
                            <div class="col-md-4">
                                <label class="form-label fw-medium">Date <span class="text-danger">*</span></label>
                                <input type="date" name="date"
                                    class="form-control @error('date') is-invalid @enderror" value="{{ old('date') }}"
                                    min="{{ now()->format('Y-m-d') }}">
                                @error('date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-medium">Start Time <span class="text-danger">*</span></label>
                                <input type="time" name="start_time"
                                    class="form-control @error('start_time') is-invalid @enderror"
                                    value="{{ old('start_time') }}">
                                @error('start_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-medium">End Time</label>
                                <input type="time" name="end_time"
                                    class="form-control @error('end_time') is-invalid @enderror"
                                    value="{{ old('end_time') }}">
                                @error('end_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-4" id="venueField">
                            <label class="form-label fw-medium">Venue / Location</label>
                            <input type="text" name="venue" class="form-control" value="{{ old('venue') }}"
                                placeholder="e.g. Boardroom A, 5th Floor">
                        </div>

                        {{-- replace id="linkField" block --}}
                        <div class="mb-4">
                            <label class="form-label fw-medium">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" class="me-1">
                                    <path
                                        d="M15 10l4.553-2.069A1 1 0 0121 8.82v6.36a1 1 0 01-1.447.894L15 14M3 8a2 2 0 012-2h10a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8z"
                                        stroke="currentColor" stroke-width="1.5" />
                                </svg>
                                Zoom / Meeting Link
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <small class="text-custom-paragraph">URL</small>
                                </span>
                                <input type="url" name="meeting_link" id="meetingLink"
                                    class="form-control @error('meeting_link') is-invalid @enderror"
                                    value="{{ old('meeting_link') }}" placeholder="https://zoom.us/j/123456789">
                                @error('meeting_link')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <small class="text-custom-paragraph mt-1 d-block">Required for virtual or hybrid
                                meetings.</small>
                        </div>

                        <div class="mb-0">
                            <label class="form-label fw-medium">Description</label>
                            <textarea name="description" rows="3" class="form-control @error('description') is-invalid @enderror"
                                placeholder="Brief description...">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>
                </div>

                {{-- People --}}
                <div class="pure-card rounded-custom card-bg shadow-custom mb-5">
                    <div class="pure-card-header">
                        <h3 class="pure-card-title m-0">People</h3>
                    </div>
                    <div class="pure-card-body">

                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-medium">Chairperson</label>
                                <select name="chairperson_id" class="form-select">
                                    <option value="">-- Select --</option>
                                    @foreach ($users as $u)
                                        <option value="{{ $u->id }}" @selected(old('chairperson_id') == $u->id)>
                                            {{ $u->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-medium">Secretary</label>
                                <select name="secretary_id" class="form-select">
                                    <option value="">-- Select --</option>
                                    @foreach ($users as $u)
                                        <option value="{{ $u->id }}" @selected(old('secretary_id') == $u->id)>
                                            {{ $u->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="mb-0">
                            <label class="form-label fw-medium">Attendees</label>
                            <small class="text-custom-paragraph d-block mb-2">Select all members who should attend.</small>
                            <div class="border rounded-2 p-3" style="max-height:220px;overflow-y:auto;">
                                @foreach ($users as $u)
                                    <div class="form-check mb-1">
                                        <input class="form-check-input" type="checkbox" name="attendees[]"
                                            id="att_{{ $u->id }}" value="{{ $u->id }}"
                                            @checked(in_array($u->id, old('attendees', [])))>
                                        <label class="form-check-label small" for="att_{{ $u->id }}">
                                            {{ $u->name }}
                                            @if ($u->role)
                                                <span class="text-custom-paragraph">({{ $u->role->name }})</span>
                                            @endif
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                    </div>
                </div>

                {{-- Documents --}}
                <div class="pure-card rounded-custom card-bg shadow-custom">
                    <div class="pure-card-header">
                        <h3 class="pure-card-title m-0">Documents</h3>
                    </div>
                    <div class="pure-card-body">
                        <label class="form-label fw-medium">Upload Meeting Documents</label>
                        <input type="file" name="documents[]"
                            class="form-control @error('documents.*') is-invalid @enderror" multiple accept="...">
                        @error('documents.*')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-custom-paragraph mt-1 d-block">Notice, minutes draft, supporting files. Max 10MB
                            each.</small>
                    </div>
                </div>

            </div>

            {{-- Sidebar --}}
            <div class="col-lg-4">
                <div class="pure-card rounded-custom card-bg shadow-custom sticky-top" style="top:80px;">
                    <div class="pure-card-header">
                        <h3 class="pure-card-title m-0">Reference</h3>
                    </div>
                    <div class="pure-card-body">
                        <label class="form-label fw-medium">Previous Meeting</label>
                        <select name="previous_meeting_id" class="form-select form-select-sm">
                            <option value="">-- None --</option>
                            @foreach ($pastMeetings as $pm)
                                <option value="{{ $pm->id }}" @selected(old('previous_meeting_id') == $pm->id)>
                                    {{ $pm->meeting_number }} — {{ $pm->date->format('d M Y') }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-custom-paragraph mt-1 d-block">Link to previous minutes/reference.</small>

                        <div class="alert alert-info small mt-4 mb-0">
                            <strong>Next step:</strong> After creating the meeting, you'll be redirected to the
                            <strong>Agenda Builder</strong> to add and arrange agenda items.
                        </div>
                    </div>
                    <div class="pure-card-footer d-flex gap-3">
                        <button type="submit"
                            class="btn btn-primary flex-grow-1 d-flex align-items-center justify-content-center gap-2">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none">
                                <path d="M20 6L9 17L4 12" stroke="currentColor" stroke-width="1.5"
                                    stroke-linecap="round" />
                            </svg>
                            Create Meeting
                        </button>
                        <a href="{{ route('meetings.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </div>
            </div>

        </div>
    </form>

    @push('scripts')
        <script>
            // replace the existing <script> block
            const venueType = document.getElementById('venueType');
            const venueField = document.getElementById('venueField');
            const meetingLink = document.getElementById('meetingLink');

            function toggleVenueFields() {
                const v = venueType.value;
                venueField.style.display = (v === 'virtual') ? 'none' : '';

                // Highlight zoom link as required for virtual/hybrid
                if (v === 'physical') {
                    meetingLink.removeAttribute('required');
                    meetingLink.closest('.mb-4').querySelector('label').classList.remove('text-primary');
                } else {
                    meetingLink.setAttribute('required', 'required');
                    meetingLink.closest('.mb-4').querySelector('label').classList.add('text-primary', 'fw-semibold');
                }
            }

            venueType.addEventListener('change', toggleVenueFields);
            toggleVenueFields();
        </script>
    @endpush

@endsection
