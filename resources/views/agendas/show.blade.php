{{-- views/agendas/show.blade.php --}}
@extends('layouts.app')

@section('page-title', $agenda->agenda_number)
@section('title', 'Agenda: ' . $agenda->title)

@section('content')
    <div class="page-header pb-7 d-flex justify-content-between align-items-center gap-6 flex-wrap">
        <div>
            <div class="d-flex align-items-center gap-3 mb-1">
                <h2 class="fw-semibold fs-7 mb-0">{{ $agenda->agenda_number }}</h2>
                <span class="badge {{ $agenda->getStatusBadgeClass() }} fs-6">
                    {{ ucfirst(str_replace('_', ' ', $agenda->status)) }}
                </span>
                @if($agenda->version > 1)
                    <span class="badge badge-label-info">v{{ $agenda->version }}</span>
                @endif
            </div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('agendas.index') }}">Agendas</a></li>
                    <li class="breadcrumb-item active">{{ $agenda->agenda_number }}</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            @if($agenda->canBeEdited() && (auth()->id() === $agenda->created_by || auth()->user()->hasRole('admin')))
                <a href="{{ route('agendas.edit', $agenda) }}" class="btn btn-outline-secondary d-flex align-items-center gap-2">
                    <svg width="16" height="16" viewBox="0 0 17 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M9.9516 2.31982C10.4732 1.75466 10.734 1.47208 11.0112 1.30725C11.6799 0.909531 12.5034 0.897164 13.1833 1.27463C13.465 1.43106 13.7339 1.70568 14.2715 2.25493C14.8092 2.80418 15.078 3.07881 15.2312 3.36665C15.6007 4.06118 15.5886 4.90235 15.1992 5.58549C15.0379 5.86861 14.7613 6.13504 14.208 6.66791L7.62544 13.008C6.57701 14.0178 6.0528 14.5227 5.39764 14.7786C4.74248 15.0345 4.02224 15.0157 2.58176 14.978L2.38576 14.9729C1.94723 14.9614 1.72797 14.9557 1.60051 14.811C1.47305 14.6664 1.49045 14.443 1.52526 13.9963L1.54415 13.7538C1.64211 12.4965 1.69108 11.8678 1.9366 11.3028C2.18211 10.7377 2.6056 10.2788 3.4526 9.36115L9.9516 2.31982Z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
                        <path d="M9.19922 2.39996L14.0992 7.29996" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
                    </svg>
                    Edit
                </a>
            @endif

            @if($agenda->canBeSubmitted() && auth()->id() === $agenda->created_by)
                <button class="btn btn-primary d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#submitModal">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M22 2L11 13M22 2L15 22L11 13M22 2L2 9L11 13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Submit for Review
                </button>
            @endif

            @if($agenda->isPendingReview() && auth()->user()->hasAnyRole(['admin', 'md']))
                <button class="btn btn-success d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#approveModal">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M20 6L9 17L4 12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Approve
                </button>
                <button class="btn btn-danger d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#rejectModal">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M18 6L6 18M6 6L18 18" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Reject
                </button>
            @endif

            <a href="{{ route('agendas.index') }}" class="btn btn-outline-secondary">Back</a>
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

    {{-- Rejection Alert --}}
    @if($agenda->isRejected() && $agenda->rejection_reason)
        <div class="alert alert-danger d-flex align-items-start gap-3 mb-5">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="flex-shrink-0 mt-1">
                <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.5"/>
                <path d="M12 8V12M12 16H12.01" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
            </svg>
            <div>
                <strong>Rejection Reason:</strong>
                <p class="mb-0 mt-1">{{ $agenda->rejection_reason }}</p>
            </div>
        </div>
    @endif

    <div class="row g-6">

        {{-- Main Info --}}
        <div class="col-lg-8">

            {{-- Details Card --}}
            <div class="pure-card rounded-custom card-bg shadow-custom mb-5">
                <div class="pure-card-header">
                    <h3 class="pure-card-title m-0">Agenda Details</h3>
                </div>
                <div class="pure-card-body">
                    <h4 class="fw-semibold mb-3">{{ $agenda->title }}</h4>

                    @if($agenda->description)
                        <p class="text-custom-body mb-4" style="white-space: pre-line;">{{ $agenda->description }}</p>
                    @else
                        <p class="text-custom-paragraph fst-italic mb-4">No description provided.</p>
                    @endif

                    <div class="row g-4">
                        <div class="col-sm-6 col-md-3">
                            <small class="text-custom-paragraph d-block mb-1">Priority</small>
                            <span class="badge {{ $agenda->getPriorityBadgeClass() }}">{{ ucfirst($agenda->priority) }}</span>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <small class="text-custom-paragraph d-block mb-1">Category</small>
                            <span class="fw-medium">{{ $agenda->category ? ucfirst($agenda->category) : '—' }}</span>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <small class="text-custom-paragraph d-block mb-1">Meeting Date</small>
                            <span class="fw-medium">{{ $agenda->meeting_date?->format('d M Y') ?? '—' }}</span>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <small class="text-custom-paragraph d-block mb-1">Version</small>
                            <span class="fw-medium">v{{ $agenda->version }}</span>
                        </div>
                    </div>

                    @if($agenda->revision_notes)
                        <div class="mt-4 p-3 rounded-2 bg-light">
                            <small class="text-custom-paragraph d-block mb-1 fw-medium">Revision Notes</small>
                            <p class="mb-0 small" style="white-space: pre-line;">{{ $agenda->revision_notes }}</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Documents --}}
            <div class="pure-card rounded-custom card-bg shadow-custom mb-5">
                <div class="pure-card-header">
                    <h3 class="pure-card-title m-0">Documents</h3>
                </div>
                <div class="pure-card-body">

                    {{-- Main Document --}}
                    @if($agenda->mainDocument)
                        <div class="mb-4">
                            <small class="text-custom-paragraph fw-medium d-block mb-2">MAIN DOCUMENT</small>
                            <div class="d-flex align-items-center gap-3 p-3 border rounded-2">
                                <div class="flex-shrink-0">
                                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="{{ $agenda->mainDocument->getFileIconClass() }}">
                                        <path d="M14 2H6C5.46957 2 4.96086 2.21071 4.58579 2.58579C4.21071 2.96086 4 3.46957 4 4V20C4 20.5304 4.21071 21.0391 4.58579 21.4142C4.96086 21.7893 5.46957 22 6 22H18C18.5304 22 19.0391 21.7893 19.4142 21.4142C19.7893 21.0391 20 20.5304 20 20V8L14 2Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M14 2V8H20" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                                <div class="flex-grow-1 min-w-0">
                                    <p class="mb-0 fw-medium text-truncate">{{ $agenda->mainDocument->original_name }}</p>
                                    <small class="text-custom-paragraph">
                                        {{ strtoupper($agenda->mainDocument->file_type) }} &bull;
                                        {{ $agenda->mainDocument->getFileSizeFormatted() }}
                                    </small>
                                </div>
                                <a href="{{ Storage::url($agenda->mainDocument->file_path) }}"
                                    target="_blank"
                                    class="btn btn-sm btn-outline-primary flex-shrink-0">
                                    Download
                                </a>
                            </div>
                        </div>
                    @endif

                    {{-- Supporting Documents --}}
                    @if($agenda->supportingDocuments->isNotEmpty())
                        <div>
                            <small class="text-custom-paragraph fw-medium d-block mb-2">SUPPORTING DOCUMENTS</small>
                            <div class="d-flex flex-column gap-2">
                                @foreach($agenda->supportingDocuments as $doc)
                                    <div class="d-flex align-items-center gap-3 p-3 border rounded-2">
                                        <div class="flex-shrink-0">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="{{ $doc->getFileIconClass() }}">
                                                <path d="M14 2H6C5.46957 2 4.96086 2.21071 4.58579 2.58579C4.21071 2.96086 4 3.46957 4 4V20C4 20.5304 4.21071 21.0391 4.58579 21.4142C4.96086 21.7893 5.46957 22 6 22H18C18.5304 22 19.0391 21.7893 19.4142 21.4142C19.7893 21.0391 20 20.5304 20 20V8L14 2Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        </div>
                                        <div class="flex-grow-1 min-w-0">
                                            <p class="mb-0 fw-medium small text-truncate">{{ $doc->original_name }}</p>
                                            <small class="text-custom-paragraph">{{ strtoupper($doc->file_type) }} &bull; {{ $doc->getFileSizeFormatted() }}</small>
                                        </div>
                                        <a href="{{ Storage::url($doc->file_path) }}" target="_blank"
                                            class="btn btn-sm btn-outline-secondary flex-shrink-0">
                                            Download
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if(!$agenda->mainDocument && $agenda->supportingDocuments->isEmpty())
                        <p class="text-custom-paragraph fst-italic mb-0">No documents attached.</p>
                    @endif
                </div>
            </div>

            {{-- Approval History --}}
            <div class="pure-card rounded-custom card-bg shadow-custom">
                <div class="pure-card-header">
                    <h3 class="pure-card-title m-0">Approval History</h3>
                </div>
                <div class="pure-card-body p-0">
                    @forelse($agenda->approvalHistory as $log)
                        <div class="d-flex gap-3 p-4 border-bottom">
                            <div class="flex-shrink-0">
                                <div class="d-flex align-items-center justify-content-center rounded-circle bg-primary text-white fw-semibold"
                                    style="width:36px;height:36px;font-size:13px;">
                                    {{ strtoupper(substr($log->user?->name ?? 'S', 0, 1)) }}
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                                    <strong class="small">{{ $log->user?->name ?? 'System' }}</strong>
                                    <span class="badge {{ $log->getActionBadgeClass() }} small">
                                        {{ $log->getActionLabel() }}
                                    </span>
                                    @if($log->version > 1)
                                        <span class="badge badge-label-secondary small">v{{ $log->version }}</span>
                                    @endif
                                </div>
                                @if($log->comment)
                                    <p class="mb-1 small text-custom-body">{{ $log->comment }}</p>
                                @endif
                                <small class="text-custom-paragraph">{{ $log->created_at->diffForHumans() }}</small>
                            </div>
                        </div>
                    @empty
                        <div class="p-4 text-center text-custom-paragraph fst-italic">No history yet.</div>
                    @endforelse
                </div>
            </div>

        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">
            <div class="pure-card rounded-custom card-bg shadow-custom">
                <div class="pure-card-header">
                    <h3 class="pure-card-title m-0">Info</h3>
                </div>
                <div class="pure-card-body">
                    <dl class="row g-0 mb-0">
                        <dt class="col-5 text-custom-paragraph small fw-medium pb-3">Created By</dt>
                        <dd class="col-7 small pb-3">{{ $agenda->creator?->name ?? '—' }}</dd>

                        <dt class="col-5 text-custom-paragraph small fw-medium pb-3">Assigned To</dt>
                        <dd class="col-7 small pb-3">{{ $agenda->assignee?->name ?? '—' }}</dd>

                        @if($agenda->isApproved())
                            <dt class="col-5 text-custom-paragraph small fw-medium pb-3">Approved By</dt>
                            <dd class="col-7 small pb-3">{{ $agenda->approver?->name ?? '—' }}</dd>

                            <dt class="col-5 text-custom-paragraph small fw-medium pb-3">Approved At</dt>
                            <dd class="col-7 small pb-3">{{ $agenda->approved_at?->format('d M Y H:i') ?? '—' }}</dd>
                        @endif

                        <dt class="col-5 text-custom-paragraph small fw-medium pb-3">Created</dt>
                        <dd class="col-7 small pb-3">{{ $agenda->created_at->format('d M Y') }}</dd>

                        <dt class="col-5 text-custom-paragraph small fw-medium">Last Updated</dt>
                        <dd class="col-7 small">{{ $agenda->updated_at->diffForHumans() }}</dd>
                    </dl>
                </div>
            </div>
        </div>

    </div>

    {{-- ── Modals ───────────────────────────────────────────────────── --}}

    {{-- Submit Modal --}}
    <div class="modal fade" id="submitModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Submit for Review</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('agendas.submit', $agenda) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <p class="text-custom-body">Are you sure you want to submit <strong>{{ $agenda->agenda_number }}</strong> for review?</p>
                        <div class="mb-3">
                            <label class="form-label fw-medium">Comment (optional)</label>
                            <textarea name="comment" class="form-control" rows="3"
                                placeholder="Any notes for the reviewer..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Approve Modal --}}
    <div class="modal fade" id="approveModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-semibold text-success">Approve Agenda</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('agendas.approve', $agenda) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <p class="text-custom-body">Approve <strong>{{ $agenda->agenda_number }}</strong>?</p>
                        <div class="mb-3">
                            <label class="form-label fw-medium">Comment (optional)</label>
                            <textarea name="comment" class="form-control" rows="3"
                                placeholder="Approval notes..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Approve</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Reject Modal --}}
    <div class="modal fade" id="rejectModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-semibold text-danger">Reject Agenda</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('agendas.reject', $agenda) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <p class="text-custom-body">Please provide a reason for rejecting <strong>{{ $agenda->agenda_number }}</strong>.</p>
                        <div class="mb-3">
                            <label class="form-label fw-medium">Rejection Reason <span class="text-danger">*</span></label>
                            <textarea name="rejection_reason" class="form-control" rows="4"
                                placeholder="Explain why this agenda is being rejected..." required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Reject</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
