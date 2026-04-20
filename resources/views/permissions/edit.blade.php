{{-- views/permissions/edit.blade.php --}}
@extends('layouts.app')

@section('page-title', 'Edit Permission')
@section('title', 'Edit ' . $permission->name)

@section('content')
    <div class="page-header pb-7 d-flex justify-content-between align-items-center gap-6 flex-wrap">
        <div>
            <h2 class="fw-semibold fs-7">Edit Permission</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('permissions.index') }}">All Permissions</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('permissions.show', $permission) }}" class="btn btn-outline-secondary d-flex align-items-center gap-2">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M2 12C2 12 5 5 12 5C19 5 22 12 22 12C22 12 19 19 12 19C5 19 2 12 2 12Z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
                    <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.5"/>
                </svg>
                View
            </a>
            <a href="{{ route('permissions.index') }}" class="btn btn-outline-secondary d-flex align-items-center gap-2">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M19 12H5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M12 19L5 12L12 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Back
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="pure-card rounded-custom card-bg shadow-custom">
                <div class="pure-card-header">
                    <h3 class="pure-card-title d-flex align-items-center gap-2 m-0">
                        <span class="text-primary d-flex align-items-center">
                            <svg width="20" height="20" viewBox="0 0 17 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M9.9516 2.31982C10.4732 1.75466 10.734 1.47208 11.0112 1.30725C11.6799 0.909531 12.5034 0.897164 13.1833 1.27463C13.465 1.43106 13.7339 1.70568 14.2715 2.25493C14.8092 2.80418 15.078 3.07881 15.2312 3.36665C15.6007 4.06118 15.5886 4.90235 15.1992 5.58549C15.0379 5.86861 14.7613 6.13504 14.208 6.66791L7.62544 13.008C6.57701 14.0178 6.0528 14.5227 5.39764 14.7786C4.74248 15.0345 4.02224 15.0157 2.58176 14.978L2.38576 14.9729C1.94723 14.9614 1.72797 14.9557 1.60051 14.811C1.47305 14.6664 1.49045 14.443 1.52526 13.9963L1.54415 13.7538C1.64211 12.4965 1.69108 11.8678 1.9366 11.3028C2.18211 10.7377 2.6056 10.2788 3.4526 9.36115L9.9516 2.31982Z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
                                <path d="M9.19922 2.39996L14.0992 7.29996" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
                                <path d="M9.89941 15L15.4994 15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                        Edit Permission
                        <span class="badge badge-label-info ms-1">{{ $permission->slug }}</span>
                    </h3>
                </div>

                <form action="{{ route('permissions.update', $permission) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="pure-card-body">

                        <div class="mb-4">
                            <label for="name" class="form-label fw-medium">
                                Permission Name <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                class="form-control @error('name') is-invalid @enderror"
                                id="name" name="name"
                                value="{{ old('name', $permission->name) }}"
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
                                value="{{ old('slug', $permission->slug) }}"
                                placeholder="e.g., users.create"
                                required>
                            <small class="text-custom-paragraph mt-1 d-block">
                                Unique identifier — lowercase, use dots for hierarchy
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
                                    <option value="{{ $module }}" @selected(old('module', $permission->module) === $module)>
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
                                placeholder="What does this permission allow?">{{ old('description', $permission->description) }}</textarea>
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
                            Update Permission
                        </button>
                        <a href="{{ route('permissions.show', $permission) }}" class="btn btn-outline-secondary">
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
