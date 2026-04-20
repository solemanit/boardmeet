@extends('layouts.app')

@section('title', $circular->title)
@section('page-title', 'Circular Detail')

@section('content')
    <div class="page-header pb-7 d-flex justify-content-between align-items-center gap-6 flex-wrap">
        <div>
            <h2 class="fw-semibold fs-7">{{ $circular->title }}</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('circulars.index') }}">Circulars</a></li>
                    <li class="breadcrumb-item active">View</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            @if (auth()->user()->hasRole('admin'))
                {{-- Publish / Unpublish toggle --}}
                @if ($circular->isPublished())
                    <form action="{{ route('circulars.unpublish', $circular) }}" method="POST">
                        @csrf
                        <button class="btn btn-outline-warning d-flex align-items-center gap-2">
                            Unpublish
                        </button>
                    </form>
                @else
                    <form action="{{ route('circulars.publish', $circular) }}" method="POST">
                        @csrf
                        <button class="btn btn-success d-flex align-items-center gap-2">
                            Publish
                        </button>
                    </form>
                @endif

                {{-- Send Reminder --}}
                @if ($circular->isPublished())
                    <form action="{{ route('circulars.send-reminder', $circular) }}" method="POST"
                        onsubmit="return confirm('Send reminder email to all active members?')">
                        @csrf
                        <button class="btn btn-primary d-flex align-items-center gap-2">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M22 2L11 13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><path d="M22 2L15 22L11 13L2 9L22 2Z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/></svg>
                            Send Reminder
                        </button>
                    </form>
                @endif

                <a href="{{ route('circulars.edit', $circular) }}" class="btn btn-outline-secondary d-flex align-items-center gap-2">
                    <svg width="16" height="16" viewBox="0 0 17 16" fill="none"><path d="M9.9516 2.31982C10.4732 1.75466 10.734 1.47208 11.0112 1.30725C11.6799 0.909531 12.5034 0.897164 13.1833 1.27463C13.465 1.43106 13.7339 1.70568 14.2715 2.25493C14.8092 2.80418 15.078 3.07881 15.2312 3.36665C15.6007 4.06118 15.5886 4.90235 15.1992 5.58549C15.0379 5.86861 14.7613 6.13504 14.208 6.66791L7.62544 13.008C6.57701 14.0178 6.0528 14.5227 5.39764 14.7786C4.74248 15.0345 4.02224 15.0157 2.58176 14.978L2.38576 14.9729C1.94723 14.9614 1.72797 14.9557 1.60051 14.811C1.47305 14.6664 1.49045 14.443 1.52526 13.9963L1.54415 13.7538C1.64211 12.4965 1.69108 11.8678 1.9366 11.3028C2.18211 10.7377 2.6056 10.2788 3.4526 9.36115L9.9516 2.31982Z" stroke="currentColor" stroke-width="1.5"/></svg>
                    Edit
                </a>
            @endif
            <a href="{{ route('circulars.index') }}" class="btn btn-outline-secondary">Back</a>
        </div>
    </div>

    @include('layouts.partials.alerts')

    <div class="row g-4">
        {{-- Main Content --}}
        <div class="col-lg-8">
            <div class="pure-card rounded-custom card-bg shadow-custom">
                <div class="pure-card-header d-flex align-items-center gap-3">
                    <span class="badge badge-label-{{ $circular->type_badge_color }} fs-7">
                        {{ ucfirst($circular->type) }}
                    </span>
                    @if ($circular->isPublished())
                        <span class="badge badge-label-success">Published</span>
                    @else
                        <span class="badge badge-label-warning">Draft</span>
                    @endif
                </div>
                <div class="pure-card-body">
                    <h4 class="fw-semibold mb-1">{{ $circular->title }}</h4>
                    @if ($circular->reference_number)
                        <p class="text-custom-paragraph small mb-4">Ref: <code>{{ $circular->reference_number }}</code></p>
                    @endif

                    <div class="p-4 rounded" style="background:#f8fafc; white-space: pre-wrap; line-height:1.8; font-size:.95rem; color:#334155;">{{ $circular->body }}</div>
                </div>
            </div>

            {{-- Attachments --}}
            @if ($circular->attachments->isNotEmpty())
                <div class="pure-card rounded-custom card-bg shadow-custom mt-4">
                    <div class="pure-card-header">
                        <h3 class="pure-card-title m-0">
                            Attachments
                            <span class="badge badge-label-info ms-2">{{ $circular->attachments->count() }}</span>
                        </h3>
                    </div>
                    <div class="pure-card-body">
                        <div class="d-flex flex-column gap-3">
                            @foreach ($circular->attachments as $attachment)
                                <div class="d-flex align-items-center justify-content-between p-3 rounded border">
                                    <div class="d-flex align-items-center gap-3">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                            class="{{ $attachment->icon_class }}">
                                            <path d="M14 2H6C4.89543 2 4 2.89543 4 4V20C4 21.1046 4.89543 22 6 22H18C19.1046 22 20 21.1046 20 20V8L14 2Z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
                                            <path d="M14 2V8H20" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                        </svg>
                                        <div>
                                            <p class="mb-0 fw-medium small">{{ $attachment->original_name }}</p>
                                            <p class="mb-0 text-custom-paragraph" style="font-size:.75rem;">{{ $attachment->formatted_size }}</p>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="{{ $attachment->url }}" target="_blank"
                                            class="btn btn-sm btn-outline-primary">Download</a>
                                        @if (auth()->user()->hasRole('admin'))
                                            <form action="{{ route('circulars.attachments.destroy', [$circular, $attachment]) }}"
                                                method="POST"
                                                onsubmit="return confirm('Remove this attachment?')">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger">Remove</button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- Sidebar Meta --}}
        <div class="col-lg-4">
            <div class="pure-card rounded-custom card-bg shadow-custom">
                <div class="pure-card-header">
                    <h3 class="pure-card-title m-0">Details</h3>
                </div>
                <div class="pure-card-body">
                    <dl class="row row-cols-1 g-3 mb-0">
                        <div>
                            <dt class="text-custom-paragraph small">Issued Date</dt>
                            <dd class="fw-medium mb-0">{{ $circular->issued_date->format('d M, Y') }}</dd>
                        </div>
                        @if ($circular->published_at)
                            <div>
                                <dt class="text-custom-paragraph small">Published At</dt>
                                <dd class="fw-medium mb-0">{{ $circular->published_at->format('d M, Y H:i') }}</dd>
                            </div>
                        @endif
                        <div>
                            <dt class="text-custom-paragraph small">Created By</dt>
                            <dd class="fw-medium mb-0">{{ $circular->createdBy?->name ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-custom-paragraph small">Last Updated</dt>
                            <dd class="fw-medium mb-0">{{ $circular->updated_at->format('d M, Y H:i') }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            {{-- Reminder History --}}
            @if (auth()->user()->hasRole('admin') && $circular->reminderLogs->isNotEmpty())
                <div class="pure-card rounded-custom card-bg shadow-custom mt-4">
                    <div class="pure-card-header">
                        <h3 class="pure-card-title m-0">Reminder History</h3>
                    </div>
                    <div class="pure-card-body p-0">
                        <ul class="list-group list-group-flush">
                            @foreach ($circular->reminderLogs->sortByDesc('sent_at')->take(5) as $log)
                                <li class="list-group-item bg-transparent">
                                    <p class="mb-0 small fw-medium">{{ $log->sentBy?->name }}</p>
                                    <p class="mb-0 text-custom-paragraph" style="font-size:.75rem;">
                                        {{ $log->sent_at->format('d M, Y H:i') }} · {{ $log->recipient_count }} recipient(s)
                                    </p>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
