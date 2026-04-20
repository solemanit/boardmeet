{{-- views/permissions/create.blade.php --}}
@extends('layouts.app')

@section('page-title', 'Create Permission')
@section('title', 'Create New Permission')

@section('content')
    <div class="page-header pb-7 d-flex justify-content-between align-items-center gap-6 flex-wrap">
        <div>
            <h2 class="fw-semibold fs-7">Create Permission</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('permissions.index') }}">All Permissions</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Create</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('permissions.index') }}" class="btn btn-outline-secondary d-flex align-items-center gap-2">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M19 12H5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M12 19L5 12L12 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Back to Permissions
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="pure-card rounded-custom card-bg shadow-custom">
                <div class="pure-card-header">
                    <h3 class="pure-card-title d-flex align-items-center gap-2 m-0">
                        <span class="text-primary d-flex align-items-center">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 2C9.23858 2 7 4.23858 7 7V8H5C3.89543 8 3 8.89543 3 10V20C3 21.1046 3.89543 22 5 22H19C20.1046 22 21 21.1046 21 20V10C21 8.89543 20.1046 8 19 8H17V7C17 4.23858 14.7614 2 12 2Z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
                                <path d="M9 8V7C9 5.34315 10.3431 4 12 4C13.6569 4 15 5.34315 15 7V8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                <circle cx="12" cy="15" r="2" stroke="currentColor" stroke-width="1.5"/>
                            </svg>
                        </span>
                        New Permission
                    </h3>
                </div>

                <form action="{{ route('permissions.store') }}" method="POST">
                    @csrf
                    <div class="pure-card-body">

                        <div class="mb-4">
                            <label for="name" class="form-label fw-medium">
                                Permission Name <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                class="form-control @error('name') is-invalid @enderror"
                                id="name" name="name"
                                value="{{ old('name') }}"
                                placeholder="e.g., Create Users"
                                required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="slug" class="form-label fw-medium">
                                Slug <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                class="form-control @error('slug') is-invalid @enderror"
                                id="slug" name="slug"
                                value="{{ old('slug') }}"
                                placeholder="e.g., users.create"
                                required>
                            <small class="text-custom-paragraph mt-1 d-block">
                                Unique identifier — lowercase, use dots for hierarchy (auto-generated from name)
                            </small>
                            @error('slug')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="module" class="form-label fw-medium">
                                Module <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('module') is-invalid @enderror"
                                id="module" name="module" required>
                                <option value="">-- Select Module --</option>
                                @foreach ($modules as $module)
                                    <option value="{{ $module }}" @selected(old('module') === $module)>
                                        {{ ucfirst($module) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('module')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="description" class="form-label fw-medium">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                id="description" name="description"
                                rows="3"
                                placeholder="What does this permission allow?">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="alert alert-info d-flex align-items-start gap-2 mb-0">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="flex-shrink-0 mt-1">
                                <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.5"/>
                                <path d="M12 8V12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                <circle cx="12" cy="16" r="0.5" fill="currentColor" stroke="currentColor" stroke-width="1"/>
                            </svg>
                            <span>
                                Use a consistent naming pattern like <code>module.action</code>
                                (e.g., <code>users.create</code>, <code>users.edit</code>, <code>users.delete</code>)
                            </span>
                        </div>

                    </div>

                    <div class="pure-card-footer d-flex align-items-center gap-3">
                        <button type="submit" class="btn btn-primary d-flex align-items-center gap-2">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M20 6L9 17L4 12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            Create Permission
                        </button>
                        <a href="{{ route('permissions.index') }}" class="btn btn-outline-secondary">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.getElementById('name').addEventListener('input', function () {
                const slug = this.value
                    .toLowerCase()
                    .trim()
                    .replace(/[^\w\s-]/g, '')
                    .replace(/[\s_]+/g, '.')
                    .replace(/^\.+|\.+$/g, '');
                document.getElementById('slug').value = slug;
            });
        </script>
    @endpush
@endsection
