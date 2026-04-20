@extends('layouts.app')
@section('title', 'Reports')

@section('content')
<div class="row g-4 mb-5">
    <div class="col-12">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h4 class="fw-bold mb-1">Reports</h4>
                <p class="text-muted mb-0">Generate and export system reports</p>
            </div>
        </div>
    </div>
</div>

{{-- Summary Stats --}}
<div class="row g-4 mb-5">
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 bg-primary bg-opacity-10 p-3">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" class="text-primary">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <div>
                    <div class="fs-4 fw-bold">{{ $stats['total_meetings'] }}</div>
                    <div class="text-muted small">Total Meetings</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 bg-success bg-opacity-10 p-3">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" class="text-success">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <div>
                    <div class="fs-4 fw-bold">{{ $stats['total_members'] }}</div>
                    <div class="text-muted small">Total Members</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 bg-info bg-opacity-10 p-3">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" class="text-info">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <div>
                    <div class="fs-4 fw-bold">{{ $stats['total_agendas'] }}</div>
                    <div class="text-muted small">Total Agendas</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 bg-warning bg-opacity-10 p-3">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" class="text-warning">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <div class="fs-4 fw-bold">{{ $stats['pending_agendas'] }}</div>
                    <div class="text-muted small">Pending Review</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Report Cards --}}
<div class="row g-4">

    {{-- Meeting Report --}}
    <div class="col-md-6 col-xl-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-start justify-content-between mb-3">
                    <div class="rounded-3 bg-primary bg-opacity-10 p-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" class="text-primary">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <span class="badge bg-primary bg-opacity-10 text-primary">PDF</span>
                </div>
                <h6 class="fw-bold mb-1">Meeting Report</h6>
                <p class="text-muted small mb-4">Overview of all meetings with status, type, attendee counts, and timeline.</p>
                <a href="{{ route('reports.meetings') }}" class="btn btn-sm btn-outline-primary w-100">Generate Report</a>
            </div>
        </div>
    </div>

    {{-- Attendance Report --}}
    <div class="col-md-6 col-xl-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-start justify-content-between mb-3">
                    <div class="rounded-3 bg-success bg-opacity-10 p-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" class="text-success">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                    </div>
                    <span class="badge bg-primary bg-opacity-10 text-primary">PDF</span>
                </div>
                <h6 class="fw-bold mb-1">Attendance Report</h6>
                <p class="text-muted small mb-4">Per-meeting and per-user attendance rates, present/absent breakdown.</p>
                <a href="{{ route('reports.attendance') }}" class="btn btn-sm btn-outline-success w-100">Generate Report</a>
            </div>
        </div>
    </div>

    {{-- Minutes / Extract --}}
    <div class="col-md-6 col-xl-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-start justify-content-between mb-3">
                    <div class="rounded-3 bg-info bg-opacity-10 p-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" class="text-info">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </div>
                    <span class="badge bg-primary bg-opacity-10 text-primary">PDF</span>
                </div>
                <h6 class="fw-bold mb-1">Minutes & Extract</h6>
                <p class="text-muted small mb-4">Formal meeting minutes with agenda items, decisions, and action points.</p>
                <a href="{{ route('reports.minutes') }}" class="btn btn-sm btn-outline-info w-100">Generate Report</a>
            </div>
        </div>
    </div>

    {{-- Agenda / Implementation --}}
    <div class="col-md-6 col-xl-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-start justify-content-between mb-3">
                    <div class="rounded-3 bg-warning bg-opacity-10 p-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" class="text-warning">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <span class="badge bg-primary bg-opacity-10 text-primary">PDF</span>
                </div>
                <h6 class="fw-bold mb-1">Agenda & Implementation</h6>
                <p class="text-muted small mb-4">Agenda tracking by status, priority, approval workflow progress.</p>
                <a href="{{ route('reports.agendas') }}" class="btn btn-sm btn-outline-warning w-100">Generate Report</a>
            </div>
        </div>
    </div>

    {{-- Department-wise --}}
    <div class="col-md-6 col-xl-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-start justify-content-between mb-3">
                    <div class="rounded-3 bg-secondary bg-opacity-10 p-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" class="text-secondary">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <span class="badge bg-primary bg-opacity-10 text-primary">PDF</span>
                </div>
                <h6 class="fw-bold mb-1">Department-wise Report</h6>
                <p class="text-muted small mb-4">Member distribution, headcount, and status breakdown by department.</p>
                <a href="{{ route('reports.departments') }}" class="btn btn-sm btn-outline-secondary w-100">Generate Report</a>
            </div>
        </div>
    </div>

    {{-- Member Activity --}}
    <div class="col-md-6 col-xl-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-start justify-content-between mb-3">
                    <div class="rounded-3 bg-purple bg-opacity-10 p-3" style="background:rgba(111,66,193,.1)">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" style="color:#6f42c1">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <span class="badge bg-primary bg-opacity-10 text-primary">PDF</span>
                </div>
                <h6 class="fw-bold mb-1">Member Activity</h6>
                <p class="text-muted small mb-4">Member participation, meeting attendance rate, and activity summary.</p>
                <a href="{{ route('reports.member-activity') }}" class="btn btn-sm btn-outline-secondary w-100">Generate Report</a>
            </div>
        </div>
    </div>

    {{-- Audit Trail (admin only) --}}
    @if(auth()->user()->hasAnyRole(['admin', 'md']))
    <div class="col-md-6 col-xl-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-start justify-content-between mb-3">
                    <div class="rounded-3 bg-danger bg-opacity-10 p-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" class="text-danger">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <span class="badge bg-danger bg-opacity-10 text-danger">Admin</span>
                </div>
                <h6 class="fw-bold mb-1">Audit Trail</h6>
                <p class="text-muted small mb-4">Complete user activity log — actions, IP addresses, timestamps.</p>
                <a href="{{ route('reports.audit-trail') }}" class="btn btn-sm btn-outline-danger w-100">View Audit Trail</a>
            </div>
        </div>
    </div>
    @endif

</div>
@endsection
