@extends('layouts.app')

@section('title', 'Edit Circular')
@section('page-title', 'Edit Circular')

@section('content')
    <div class="page-header pb-7 d-flex justify-content-between align-items-center gap-6 flex-wrap">
        <div>
            <h2 class="fw-semibold fs-7">Edit Circular</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('circulars.index') }}">Circulars</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('circulars.show', $circular) }}" class="btn btn-outline-secondary">View</a>
            <a href="{{ route('circulars.index') }}" class="btn btn-outline-secondary">Back</a>
        </div>
    </div>

    @include('layouts.partials.alerts')

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="pure-card rounded-custom card-bg shadow-custom">
                <div class="pure-card-header">
                    <h3 class="pure-card-title m-0">
                        Edit Circular
                        <span class="badge badge-label-{{ $circular->type_badge_color }} ms-2">{{ ucfirst($circular->type) }}</span>
                    </h3>
                </div>

                <form action="{{ route('circulars.update', $circular) }}" method="POST" enctype="multipart/form-data">
                    @csrf @method('PUT')
                    <div class="pure-card-body">
                        <div class="row g-4">
                            <div class="col-12">
                                <label class="form-label fw-medium">Title <span class="text-danger">*</span></label>
                                <input type="text" name="title"
                                    class="form-control @error('title') is-invalid @enderror"
                                    value="{{ old('title', $circular->title) }}">
                                @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-sm-6">
                                <label class="form-label fw-medium">Reference Number</label>
                                <input type="text" name="reference_number"
                                    class="form-control @error('reference_number') is-invalid @enderror"
                                    value="{{ old('reference_number', $circular->reference_number) }}">
                                @error('reference_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-sm-6">
                                <label class="form-label fw-medium">Type <span class="text-danger">*</span></label>
                                <select name="type" class="form-select @error('type') is-invalid @enderror">
                                    @foreach (['circular' => 'Circular', 'notice' => 'Notice', 'memo' => 'Memo'] as $val => $label)
                                        <option value="{{ $val }}" @selected(old('type', $circular->type) === $val)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-sm-6">
                                <label class="form-label fw-medium">Issued Date <span class="text-danger">*</span></label>
                                <input type="date" name="issued_date"
                                    class="form-control @error('issued_date') is-invalid @enderror"
                                    value="{{ old('issued_date', $circular->issued_date->toDateString()) }}">
                                @error('issued_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-sm-6">
                                <label class="form-label fw-medium">Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-select @error('status') is-invalid @enderror">
                                    <option value="draft"     @selected(old('status', $circular->status) === 'draft')>Draft</option>
                                    <option value="published" @selected(old('status', $circular->status) === 'published')>Published</option>
                                </select>
                                @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-medium">Content <span class="text-danger">*</span></label>
                                <textarea name="body" rows="8"
                                    class="form-control @error('body') is-invalid @enderror">{{ old('body', $circular->body) }}</textarea>
                                @error('body')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            {{-- Existing Attachments --}}
                            @if ($circular->attachments->isNotEmpty())
                                <div class="col-12">
                                    <label class="form-label fw-medium">Existing Attachments</label>
                                    <div class="d-flex flex-column gap-2">
                                        @foreach ($circular->attachments as $attachment)
                                            <div class="d-flex align-items-center justify-content-between p-2 border rounded">
                                                <span class="small">{{ $attachment->original_name }}
                                                    <span class="text-custom-paragraph">({{ $attachment->formatted_size }})</span>
                                                </span>
                                                <form action="{{ route('circulars.attachments.destroy', [$circular, $attachment]) }}"
                                                    method="POST"
                                                    onsubmit="return confirm('Remove this attachment?')">
                                                    @csrf @method('DELETE')
                                                    <button class="btn btn-sm btn-outline-danger">Remove</button>
                                                </form>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            {{-- New Attachments --}}
                            <div class="col-12">
                                <label class="form-label fw-medium">Add More Attachments</label>
                                <input type="file" name="attachments[]" multiple
                                    class="form-control @error('attachments') is-invalid @enderror"
                                    accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png">
                                <small class="text-custom-paragraph mt-1 d-block">Max 5 files · 10MB each</small>
                                @error('attachments')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                @error('attachments.*')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                    <div class="pure-card-footer d-flex align-items-center gap-3">
                        <button type="submit" class="btn btn-primary d-flex align-items-center gap-2">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M20 6L9 17L4 12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                            Update Circular
                        </button>
                        <a href="{{ route('circulars.show', $circular) }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
