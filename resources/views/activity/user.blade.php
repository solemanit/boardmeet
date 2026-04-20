@extends('layouts.app')

@section('page-title', 'User Activity')
@section('title', $user->name . "'s Activity")

@section('content')
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4><i class="bi bi-file-earmark-text"></i> Activity Log for {{ $user->name }}</h4>
                    <p class="text-muted">Email: {{ $user->email }}</p>
                </div>
                <a href="{{ route('users.show', $user) }}" class="btn btn-secondary btn-sm">
                    <i class="bi bi-arrow-left"></i> Back to User
                </a>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-light">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <form method="GET" action="{{ route('activity.user', $user) }}" class="d-flex gap-2">
                        <input type="text" name="search" class="form-control form-control-sm"
                            placeholder="Search by action..." value="{{ request('search') }}">
                        <button type="submit" class="btn btn-sm btn-primary">
                            <i class="bi bi-search"></i> Search
                        </button>
                    </form>
                </div>
                <div class="col-md-6 text-end">
                    @if (request()->has('search'))
                        <a href="{{ route('activity.user', $user) }}" class="btn btn-sm btn-secondary">
                            <i class="bi bi-x-circle"></i> Clear Filter
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 20%;">Action</th>
                        <th style="width: 25%;">Description</th>
                        <th style="width: 15%;">IP Address</th>
                        <th style="width: 20%;">User Agent</th>
                        <th style="width: 20%;">Date & Time</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($activities as $activity)
                        <tr>
                            <td>
                                <span class="badge bg-info">{{ ucfirst(str_replace('_', ' ', $activity->action)) }}</span>
                            </td>
                            <td>
                                <small>
                                    @if ($activity->description)
                                        {{ $activity->description }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </small>
                            </td>
                            <td>
                                <code>{{ $activity->ip_address }}</code>
                            </td>
                            <td>
                                <small class="text-muted">{{ substr($activity->user_agent, 0, 40) }}...</small>
                            </td>
                            <td>
                                <small>
                                    {{ $activity->created_at->format('M d, Y H:i:s') }}
                                    <br>
                                    <span class="text-muted">{{ $activity->created_at->diffForHumans() }}</span>
                                </small>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4">
                                <i class="bi bi-inbox" style="font-size: 2rem; color: #ccc;"></i>
                                <p class="text-muted mt-2">No activity log found for this user</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if ($activities instanceof \Illuminate\Pagination\Paginator)
        <div class="mt-4">
            {{ $activities->links() }}
        </div>
    @endif

    <div class="row mt-4">
        <div class="col-md-12">
            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i>
                <strong>Total Activities:</strong> {{ $totalCount ?? 0 }}
            </div>
        </div>
    </div>
@endsection
