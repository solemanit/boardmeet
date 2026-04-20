{{-- ═══════════════════════════════════════════════════════════════
     views/roles/edit.blade.php
════════════════════════════════════════════════════════════════ --}}
@extends('layouts.app')
@section('page-title', 'Edit Role')
@section('title', 'Edit ' . $role->name)

@section('content')
    <div class="page-header pb-7 d-flex justify-content-between align-items-center gap-6 flex-wrap">
        <div>
            <h2 class="fw-semibold fs-7">Edit Role</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('roles.index') }}">Roles</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $role->name }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="pure-card rounded-custom card-bg shadow-custom">
                <div class="pure-card-header">
                    <h3 class="pure-card-title d-flex align-items-center gap-2 m-0">
                        <span class="text-primary d-flex align-items-center">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path opacity="0.4" d="M12 2L3 7V12C3 16.55 6.84 20.74 12 22C17.16 20.74 21 16.55 21 12V7L12 2Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M9 12L11 14L15 10" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                        Edit Role
                    </h3>
                </div>
                <div class="pure-card-body">
                    <form action="{{ route('roles.update', $role) }}" method="POST">
                        @csrf
                        @method('PUT')

                        {{-- Basic Fields --}}
                        <div class="row gy-5 mb-7">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Role Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="name"
                                    class="form-control @error('name') is-invalid @enderror"
                                    placeholder="Enter role name"
                                    value="{{ old('name', $role->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="slug" class="form-label">Slug <span class="text-danger">*</span></label>
                                <input type="text" name="slug" id="slug"
                                    class="form-control @error('slug') is-invalid @enderror"
                                    value="{{ old('slug', $role->slug) }}" required
                                    @if (in_array($role->slug, ['admin'])) disabled @endif>
                                <small class="text-muted">
                                    @if (in_array($role->slug, ['admin']))
                                        Slug cannot be changed for system roles.
                                    @else
                                        Auto-generated from name. Unique identifier.
                                    @endif
                                </small>
                                @error('slug')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="priority" class="form-label">Priority <span class="text-danger">*</span></label>
                                <input type="number" name="priority" id="priority"
                                    class="form-control @error('priority') is-invalid @enderror"
                                    value="{{ old('priority', $role->priority) }}" min="1" required>
                                <small class="text-muted">Higher number = higher priority (Admin: 100, MD: 80).</small>
                                @error('priority')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="description" class="form-label">Description</label>
                                <textarea name="description" id="description"
                                    class="form-control @error('description') is-invalid @enderror"
                                    placeholder="Brief description of this role"
                                    rows="1">{{ old('description', $role->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Permissions --}}
                        <hr>
                        <h6 class="fw-semibold mb-5 mt-6">Assign Permissions</h6>

                        @error('permissions')
                            <div class="alert alert-danger mb-4">{{ $message }}</div>
                        @enderror

                        @forelse($permissionsByModule as $module => $permissions)
                            @php
                                $permIds = collect($permissions)->pluck('id');
                                $allChecked = $role->permissions->whereIn('id', $permIds)->count() === $permIds->count();
                            @endphp
                            <div class="pure-card rounded-custom card-bg shadow-custom mb-4">
                                <div class="pure-card-header py-4">
                                    <div class="form-check mb-0">
                                        <input class="form-check-input module-checkbox" type="checkbox"
                                            id="module{{ $loop->index }}"
                                            data-module="{{ $module }}"
                                            onchange="toggleModule(this)"
                                            @if ($allChecked) checked @endif>
                                        <label class="form-check-label fw-semibold" for="module{{ $loop->index }}">
                                            {{ ucfirst($module) }}
                                        </label>
                                    </div>
                                </div>
                                <div class="pure-card-body">
                                    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 gy-3">
                                        @foreach ($permissions as $permission)
                                            <div class="col">
                                                <div class="form-check">
                                                    <input class="form-check-input permission-checkbox module-{{ $module }}"
                                                        type="checkbox"
                                                        id="perm{{ $permission->id }}"
                                                        name="permissions[]"
                                                        value="{{ $permission->id }}"
                                                        @if ($role->permissions->contains($permission)) checked @endif>
                                                    <label class="form-check-label" for="perm{{ $permission->id }}">
                                                        {{ $permission->name }}
                                                        <small class="text-muted d-block">{{ $permission->slug }}</small>
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-custom-body">No permissions available.</p>
                        @endforelse

                        <div class="col-12 mt-7">
                            <button type="submit" class="btn btn-primary">Update Role</button>
                            <a href="{{ route('roles.show', $role) }}" class="btn btn-label-secondary ms-2">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function toggleModule(checkbox) {
        const module = checkbox.getAttribute('data-module');
        document.querySelectorAll(`.module-${module}`).forEach(cb => cb.checked = checkbox.checked);
    }

    @if (!in_array($role->slug, ['admin']))
        document.getElementById('name').addEventListener('input', function () {
            document.getElementById('slug').value = this.value
                .toLowerCase().trim()
                .replace(/[^\w\s-]/g, '')
                .replace(/[\s_]+/g, '-')
                .replace(/^-+|-+$/g, '');
        });
    @endif
</script>
@endpush
