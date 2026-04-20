{{-- views/agendas/edit.blade.php --}}
@extends('layouts.app')

@section('page-title', 'Edit ' . $agenda->agenda_number)
@section('title', 'Edit Agenda')

@section('content')
    <div class="page-header pb-7 d-flex justify-content-between align-items-center gap-6 flex-wrap">
        <div>
            <h2 class="fw-semibold fs-7">Edit Agenda</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('agendas.index') }}">Agendas</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('agendas.show', $agenda) }}">{{ $agenda->agenda_number }}</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('agendas.show', $agenda) }}" class="btn btn-outline-secondary d-flex align-items-center gap-2">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M19 12H5M12 19L5 12L12 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            Back
        </a>
    </div>

    @if($agenda->isRejected())
        <div class="alert alert-warning d-flex align-items-start gap-3 mb-5">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="flex-shrink-0 mt-1">
                <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                <line x1="12" y1="9" x2="12" y2="13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                <line x1="12" y1="17" x2="12.01" y2="17" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
            </svg>
            <div>
                <strong>Revision Required</strong>
                <p class="mb-0 mt-1 small">{{ $agenda->rejection_reason }}</p>
                <p class="mb-0 mt-2 small text-muted">Saving will increment the version and mark this as "Revised". You can then resubmit for approval.</p>
            </div>
        </div>
    @endif

    <form action="{{ route('agendas.update', $agenda) }}" method="POST" enctype="multipart/form-data">
        @csrf @method('PUT')
        <div class="row g-6">

            <div class="col-lg-8">
                <div class="pure-card rounded-custom card-bg shadow-custom mb-5">
                    <div class="pure-card-header d-flex align-items-center justify-content-between">
                        <h3 class="pure-card-title m-0">Agenda Details</h3>
                        <div class="d-flex gap-2">
                            <span class="badge {{ $agenda->getStatusBadgeClass() }}">{{ ucfirst(str_replace('_', ' ', $agenda->status)) }}</span>
                            <span class="badge badge-label-info">v{{ $agenda->version }}</span>
                        </div>
                    </div>
                    <div class="pure-card-body">

                        {{-- Agenda Number (read-only) --}}
                        <div class="mb-4">
                            <label class="form-label fw-medium">Agenda Number</label>
                            <input type="text" class="form-control bg-light fw-semibold text-primary"
                                value="{{ $agenda->agenda_number }}" readonly>
                        </div>

                        <div class="mb-4">
                            <label for="title" class="form-label fw-medium">Title <span class="text-danger">*</span></label>
                            <input type="text" id="title" name="title"
                                class="form-control @error('title') is-invalid @enderror"
                                value="{{ old('title', $agenda->title) }}">
                            @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-4">
                            <label for="description" class="form-label fw-medium">Description</label>
                            <textarea id="description" name="description" rows="4"
                                class="form-control @error('description') is-invalid @enderror">{{ old('description', $agenda->description) }}</textarea>
                            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-medium">Priority <span class="text-danger">*</span></label>
                                <select name="priority" class="form-select @error('priority') is-invalid @enderror">
                                    @foreach(['low', 'medium', 'high', 'urgent'] as $p)
                                        <option value="{{ $p }}" @selected(old('priority', $agenda->priority) === $p)>{{ ucfirst($p) }}</option>
                                    @endforeach
                                </select>
                                @error('priority')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-medium">Category</label>
                                <select name="category" class="form-select @error('category') is-invalid @enderror">
                                    <option value="">-- Select Category --</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat }}" @selected(old('category', $agenda->category) === $cat)>{{ ucfirst($cat) }}</option>
                                    @endforeach
                                </select>
                                @error('category')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-medium">Assign To</label>
                                <select name="assigned_to" class="form-select @error('assigned_to') is-invalid @enderror">
                                    <option value="">-- Select User --</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" @selected(old('assigned_to', $agenda->assigned_to) == $user->id)>
                                            {{ $user->name }} ({{ $user->role?->name }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('assigned_to')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-medium">Meeting Date</label>
                                <input type="date" name="meeting_date"
                                    class="form-control @error('meeting_date') is-invalid @enderror"
                                    value="{{ old('meeting_date', $agenda->meeting_date?->format('Y-m-d')) }}">
                                @error('meeting_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        @if($agenda->isRejected())
                            <div class="mb-0">
                                <label for="revision_notes" class="form-label fw-medium">Revision Notes <span class="text-danger">*</span></label>
                                <textarea id="revision_notes" name="revision_notes" rows="3"
                                    class="form-control @error('revision_notes') is-invalid @enderror"
                                    placeholder="Describe what was changed in this revision...">{{ old('revision_notes', $agenda->revision_notes) }}</textarea>
                                @error('revision_notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Documents --}}
                <div class="pure-card rounded-custom card-bg shadow-custom">
                    <div class="pure-card-header">
                        <h3 class="pure-card-title m-0">Documents</h3>
                    </div>
                    <div class="pure-card-body">

                        {{-- Existing Main Document --}}
                        @if($agenda->mainDocument)
                            <div class="mb-4">
                                <small class="text-custom-paragraph fw-medium d-block mb-2">CURRENT MAIN DOCUMENT</small>
                                <div class="d-flex align-items-center gap-3 p-3 border rounded-2 bg-light">
                                    <div class="flex-grow-1">
                                        <p class="mb-0 fw-medium small">{{ $agenda->mainDocument->original_name }}</p>
                                        <small class="text-custom-paragraph">{{ strtoupper($agenda->mainDocument->file_type) }} &bull; {{ $agenda->mainDocument->getFileSizeFormatted() }}</small>
                                    </div>
                                    <form action="{{ route('agendas.documents.destroy', [$agenda, $agenda->mainDocument]) }}"
                                        method="POST" class="d-inline"
                                        onsubmit="return confirm('Delete this document?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Remove</button>
                                    </form>
                                </div>
                            </div>
                        @endif

                        {{-- Upload New Main Document --}}
                        <div class="mb-5">
                            <label class="form-label fw-medium d-flex align-items-center gap-2">
                                <span class="badge badge-label-primary">Main</span>
                                {{ $agenda->mainDocument ? 'Replace' : 'Upload' }} Main Document
                            </label>
                            <input type="file" id="main_document" name="main_document"
                                class="form-control @error('main_document') is-invalid @enderror">
                            @error('main_document')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- Existing Supporting Documents --}}
                        @if($agenda->supportingDocuments->isNotEmpty())
                            <div class="mb-4">
                                <small class="text-custom-paragraph fw-medium d-block mb-2">CURRENT SUPPORTING DOCUMENTS</small>
                                <div class="d-flex flex-column gap-2">
                                    @foreach($agenda->supportingDocuments as $doc)
                                        <div class="d-flex align-items-center gap-3 p-3 border rounded-2 bg-light">
                                            <div class="flex-grow-1 min-w-0">
                                                <p class="mb-0 fw-medium small text-truncate">{{ $doc->original_name }}</p>
                                                <small class="text-custom-paragraph">{{ strtoupper($doc->file_type) }} &bull; {{ $doc->getFileSizeFormatted() }}</small>
                                            </div>
                                            <form action="{{ route('agendas.documents.destroy', [$agenda, $doc]) }}"
                                                method="POST" class="d-inline"
                                                onsubmit="return confirm('Delete this document?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger flex-shrink-0">Remove</button>
                                            </form>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- Upload New Supporting Docs --}}
                        <div>
                            <label class="form-label fw-medium d-flex align-items-center gap-2">
                                <span class="badge badge-label-info">Supporting</span>
                                Add Supporting Documents
                            </label>
                            <input type="file" name="supporting_docs[]"
                                class="form-control @error('supporting_docs.*') is-invalid @enderror"
                                multiple>
                            @error('supporting_docs.*')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="col-lg-4">
                <div class="pure-card rounded-custom card-bg shadow-custom sticky-top" style="top: 80px;">
                    <div class="pure-card-header">
                        <h3 class="pure-card-title m-0">Save Changes</h3>
                    </div>
                    <div class="pure-card-body">
                        @if($agenda->isRejected())
                            <div class="alert alert-info small mb-0">
                                Saving will create version <strong>v{{ $agenda->version + 1 }}</strong> and mark this as "Revised".
                            </div>
                        @else
                            <p class="text-custom-paragraph small mb-0">Changes will be saved to the current draft.</p>
                        @endif
                    </div>
                    <div class="pure-card-footer d-flex gap-3">
                        <button type="submit" class="btn btn-primary flex-grow-1 d-flex align-items-center justify-content-center gap-2">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M20 6L9 17L4 12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            Save Changes
                        </button>
                        <a href="{{ route('agendas.show', $agenda) }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </div>
            </div>

        </div>
    </form>
@endsection
