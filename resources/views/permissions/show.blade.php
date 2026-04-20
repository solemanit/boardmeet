@extends('layouts.app')

@section('page-title', 'Permission Details')
@section('title', $permission->name)

@section('content')
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-lock"></i> Permission Information</h5>
                    <div class="btn-group btn-group-sm" role="group">
                        @if (auth()->user()->hasPermission('permissions.edit'))
                            <a href="{{ route('permissions.edit', $permission) }}" class="btn btn-outline-primary">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
                        @endif
                        @if (auth()->user()->hasPermission('permissions.delete'))
                            <form action="{{ route('permissions.destroy', $permission) }}" method="POST"
                                style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger"
                                    onclick="return confirm('Are you sure?')">
                                    <i class="bi bi-trash"></i> Delete
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="text-muted d-block mb-1"><small>Permission Name</small></label>
                            <h5 class="mb-3">{{ $permission->name }}</h5>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted d-block mb-1"><small>Module</small></label>
                            <h5 class="mb-3">
                                <span class="badge bg-info">{{ ucfirst($permission->module) }}</span>
                            </h5>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="text-muted d-block mb-1"><small>Slug</small></label>
                        <p><code>{{ $permission->slug }}</code></p>
                    </div>

                    <div class="mb-4">
                        <label class="text-muted d-block mb-1"><small>Description</small></label>
                        <p>{{ $permission->description ?? 'No description provided' }}</p>
                    </div>

                    <hr>

                    <div class="mb-4">
                        <label class="text-muted d-block mb-2"><small>Created & Updated</small></label>
                        <div class="row">
                            <div class="col-md-6">
                                <small class="text-muted">Created On</small>
                                <p>{{ $permission->created_at->format('M d, Y H:i') }}</p>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted">Last Updated</small>
                                <p>{{ $permission->updated_at->format('M d, Y H:i') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-shield-check"></i> Roles</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="display-4 text-primary">{{ $permission->roles->count() }}</div>
                        <small class="text-muted">roles with this permission</small>
                    </div>

                    @if ($permission->roles->count() > 0)
                        <div class="list-group">
                            @foreach ($permission->roles as $role)
                                <a href="{{ route('roles.show', $role) }}" class="list-group-item list-group-item-action">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <span class="role-badge role-{{ $role->slug }}">{{ $role->name }}</span>
                                            <small class="d-block text-muted mt-1">{{ $role->users->count() }}
                                                users</small>
                                        </div>
                                        <small class="badge bg-info">P{{ $role->priority }}</small>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted">No roles have this permission yet</p>
                    @endif
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-info-circle"></i> Details</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted">Slug</small>
                        <p class="text-monospace">{{ $permission->slug }}</p>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted">Module</small>
                        <p>{{ ucfirst($permission->module) }}</p>
                    </div>
                    <div class="mb-0">
                        <small class="text-muted">Status</small>
                        <p>
                            <span class="badge bg-success">Active</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
