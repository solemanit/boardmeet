{{-- views/meetings/index.blade.php --}}
@extends('layouts.app')
@section('page-title', 'Meetings')
@section('content')

    <div class="page-header pb-7 d-flex justify-content-between align-items-center gap-6 flex-wrap">
        <div>
            <h2 class="fw-semibold fs-7">Meeting Management</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Home</a></li>
                    <li class="breadcrumb-item active">Meetings</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('meetings.create') }}" class="btn btn-primary d-flex align-items-center gap-2">
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                <path d="M8 1V15M1 8H15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
            </svg>
            New Meeting
        </a>
    </div>

    @include('layouts.partials.alerts')

    {{-- Stats --}}
    <div class="row row-cols-1 row-cols-sm-2 row-cols-xl-5 g-3 mb-6">
        @foreach ([['label' => 'Total', 'key' => 'total', 'color' => 'primary'], ['label' => 'Scheduled', 'key' => 'scheduled', 'color' => 'info'], ['label' => 'Ongoing', 'key' => 'ongoing', 'color' => 'warning'], ['label' => 'Completed', 'key' => 'completed', 'color' => 'success'], ['label' => 'Cancelled', 'key' => 'cancelled', 'color' => 'danger']] as $s)
            <div class="col">
                <div class="card shadow-custom rounded-custom">
                    <div class="card-body p-6">
                        <span class="fz-13px fw-medium d-block mb-1">{{ $s['label'] }}</span>
                        <h3 class="fs-5 mb-0">{{ $stats[$s['key']] }}</h3>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Upcoming Meetings (mini calendar) --}}
    <div class="card shadow-custom rounded-custom mb-4">
        <div class="card-body p-4">
            <h6 class="fw-semibold mb-3">Upcoming Meetings</h6>
            <div class="d-flex flex-wrap gap-2">
                @forelse($calendarMeetings as $cm)
                    <a href="{{ route('meetings.show', $cm) }}"
                        class="d-flex align-items-center gap-2 px-3 py-2 border rounded-2 text-decoration-none text-body">
                        <span class="fw-semibold text-primary small">{{ $cm->date->format('d M') }}</span>
                        <span class="small text-truncate" style="max-width:160px;">{{ $cm->title }}</span>
                        <span class="badge {{ $cm->getStatusBadgeClass() }} ms-1">{{ ucfirst($cm->status) }}</span>
                    </a>
                @empty
                    <p class="text-custom-paragraph small mb-0">No upcoming meetings in next 3 months.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card shadow-custom rounded-custom mb-4">
        <div class="card-body p-4">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small fw-medium">Search</label>
                    <input type="text" name="search" class="form-control form-control-sm"
                        value="{{ request('search') }}" placeholder="Title, number...">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-medium">Status</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All Status</option>
                        @foreach (['scheduled', 'ongoing', 'completed', 'cancelled', 'postponed'] as $s)
                            <option value="{{ $s }}" @selected(request('status') === $s)>{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-medium">Type</label>
                    <select name="type" class="form-select form-select-sm">
                        <option value="">All Types</option>
                        @foreach (['board', 'committee', 'agm', 'egm', 'executive', 'other'] as $t)
                            <option value="{{ $t }}" @selected(request('type') === $t)>{{ strtoupper($t) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-medium">Date From</label>
                    <input type="date" name="date_from" class="form-control form-control-sm"
                        value="{{ request('date_from') }}">
                </div>
                <div class="col-md-1">
                    <label class="form-label small fw-medium">To</label>
                    <input type="date" name="date_to" class="form-control form-control-sm"
                        value="{{ request('date_to') }}">
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm flex-grow-1">Filter</button>
                    <a href="{{ route('meetings.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
                </div>
            </form>
        </div>
    </div>

    {{-- Table --}}
    <div class="card shadow-custom rounded-custom">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Meeting #</th>
                        <th>Title</th>
                        <th>Type</th>
                        <th>Date</th>
                        <th>Venue</th>
                        <th>Status</th>
                        <th>Attendees</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($meetings as $meeting)
                        <tr>
                            <td>
                                <code class="text-custom-body fw-medium">{{ $meeting->meeting_number }}</code>
                            </td>
                            <td>
                                <a href="{{ route('meetings.show', $meeting) }}"
                                    class="fw-medium text-body text-decoration-none">
                                    {{ Str::limit($meeting->title, 45) }}
                                </a>
                            </td>
                            <td>
                                <span
                                    class="badge {{ $meeting->getTypeBadgeClass() }}">{{ strtoupper($meeting->type) }}</span>
                            </td>
                            <td class="small">
                                {{ $meeting->date->format('d M Y') }}<br>
                                <span class="text-custom-paragraph">{{ $meeting->start_time }}</span>
                            </td>
                            <td class="small text-custom-body">
                                {{ $meeting->venue ?? ($meeting->meeting_link ? 'Virtual' : '—') }}
                            </td>
                            <td>
                                <span
                                    class="badge {{ $meeting->getStatusBadgeClass() }}">{{ ucfirst($meeting->status) }}</span>
                            </td>
                            <td class="small">
                                {{ $meeting->attendees_count ?? $meeting->attendees->count() }}
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    {{-- View --}}
                                    <a href="{{ route('meetings.show', $meeting) }}"
                                        class="btn btn-sm btn-icon btn-icon-secondary rounded-pill" title="View">
                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none">
                                            <path d="M2 12C2 12 5 5 12 5C19 5 22 12 22 12C22 12 19 19 12 19C5 19 2 12 2 12Z"
                                                stroke="currentColor" stroke-width="1.5" />
                                            <circle cx="12" cy="12" r="3" stroke="currentColor"
                                                stroke-width="1.5" />
                                        </svg>
                                    </a>

                                    {{-- Agenda PDF --}}
                                    <a href="{{ route('meetings.print.agenda', $meeting) }}" target="_blank"
                                        class="btn btn-sm btn-icon btn-icon-secondary rounded-pill"
                                        title="Agenda PDF/Print">
                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none">
                                            <path
                                                d="M9 12H15M9 16H12M7 3H5C3.89543 3 3 3.89543 3 5V19C3 20.1046 3.89543 21 5 21H19C20.1046 21 21 20.1046 21 19V5C21 3.89543 20.1046 3 19 3H17M7 3C7 3 7 5 12 5C17 5 17 3 17 3"
                                                stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                                        </svg>
                                    </a>

                                    {{-- Minutes PDF --}}
                                    <a href="{{ route('meetings.print.minutes', $meeting) }}" target="_blank"
                                        class="btn btn-sm btn-icon btn-icon-secondary rounded-pill"
                                        title="Minutes PDF/Print">
                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none">
                                            <path
                                                d="M7 17H17M7 13H17M7 9H11M5 3H19C19.5523 3 20 3.44772 20 4V20C20 20.5523 19.5523 21 19 21H5C4.44772 21 4 20.5523 4 20V4C4 3.44772 4.44772 3 5 3Z"
                                                stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                                        </svg>
                                    </a>

                                    {{-- iCal --}}
                                    <a href="{{ route('meetings.ical', $meeting) }}"
                                        class="btn btn-sm btn-icon btn-icon-secondary rounded-pill" title="Download iCal">
                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none">
                                            <rect x="3" y="4" width="18" height="18" rx="2"
                                                stroke="currentColor" stroke-width="1.5" />
                                            <path d="M16 2V6M8 2V6M3 10H21" stroke="currentColor" stroke-width="1.5"
                                                stroke-linecap="round" />
                                        </svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-custom-paragraph">No meetings found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">{{ $meetings->links() }}</div>
    </div>

@endsection
