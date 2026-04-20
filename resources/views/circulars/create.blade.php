@extends('layouts.app')

@section('title', 'Create Circular')
@section('page-title', 'Create Circular')

@section('content')
    <div class="page-header pb-7 d-flex justify-content-between align-items-center gap-6 flex-wrap">
        <div>
            <h2 class="fw-semibold fs-7">Create Circular</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('circulars.index') }}">Circulars</a></li>
                    <li class="breadcrumb-item active">Create</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('circulars.index') }}" class="btn btn-outline-secondary d-flex align-items-center gap-2">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M19 12H5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><path d="M12 19L5 12L12 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
            Back
        </a>
    </div>

    @include('layouts.partials.alerts')

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="pure-card rounded-custom card-bg shadow-custom">
                <div class="pure-card-header">
                    <h3 class="pure-card-title m-0">New Circular / Notice</h3>
                </div>

                <form action="{{ route('circulars.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="pure-card-body">

                        <div class="row g-4">
                            {{-- Title --}}
                            <div class="col-12">
                                <label class="form-label fw-medium">Title <span class="text-danger">*</span></label>
                                <input type="text" name="title"
                                    class="form-control @error('title') is-invalid @enderror"
                                    value="{{ old('title') }}"
                                    placeholder="e.g., Annual General Meeting Notice">
                                @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            {{-- Ref No + Type --}}
                            <div class="col-sm-6">
                                <label class="form-label fw-medium">Reference Number</label>
                                <input type="text" name="reference_number"
                                    class="form-control @error('reference_number') is-invalid @enderror"
                                    value="{{ old('reference_number') }}"
                                    placeholder="e.g., BM/2025/001">
                                @error('reference_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-sm-6">
                                <label class="form-label fw-medium">Type <span class="text-danger">*</span></label>
                                <select name="type" class="form-select @error('type') is-invalid @enderror">
                                    @foreach (['circular' => 'Circular', 'notice' => 'Notice', 'memo' => 'Memo'] as $val => $label)
                                        <option value="{{ $val }}" @selected(old('type', 'circular') === $val)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            {{-- Issued Date + Status --}}
                            <div class="col-sm-6">
                                <label class="form-label fw-medium">Issued Date <span class="text-danger">*</span></label>
                                <input type="date" name="issued_date"
                                    class="form-control @error('issued_date') is-invalid @enderror"
                                    value="{{ old('issued_date', today()->toDateString()) }}">
                                @error('issued_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-sm-6">
                                <label class="form-label fw-medium">Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-select @error('status') is-invalid @enderror">
                                    <option value="draft"     @selected(old('status', 'draft') === 'draft')>Draft</option>
                                    <option value="published" @selected(old('status') === 'published')>Published</option>
                                </select>
                                <small class="text-custom-paragraph d-block mt-1">Only published circulars are visible to all users.</small>
                                @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            {{-- Body --}}
                            <div class="col-12">
                                <label class="form-label fw-medium">Content <span class="text-danger">*</span></label>
                                <textarea name="body" rows="8"
                                    class="form-control @error('body') is-invalid @enderror"
                                    placeholder="Write the full content of this circular...">{{ old('body') }}</textarea>
                                @error('body')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            {{-- Attachments --}}
                            <div class="col-12">
                                <label class="form-label fw-medium">Attachments</label>
                                <input type="file" name="attachments[]" multiple
                                    class="form-control @error('attachments') is-invalid @enderror"
                                    accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png">
                                <small class="text-custom-paragraph mt-1 d-block">
                                    Max 5 files · 10MB each · PDF, Word, Excel, Images
                                </small>
                                @error('attachments')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                @error('attachments.*')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                        </div>

                    </div>
                    <div class="pure-card-footer d-flex align-items-center gap-3">
                        <button type="submit" class="btn btn-primary d-flex align-items-center gap-2">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M20 6L9 17L4 12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                            Create Circular
                        </button>
                        <a href="{{ route('circulars.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
