@extends('layouts.app')

@section('page-title', 'Activity Logs')
@section('title', 'My Activity Log')

@section('content')
    <div class="page-header pb-7 d-flex justify-content-between align-items-center gap-6 flex-wrap">
        <div>
            <h2 class="fw-semibold fs-7">Activity Log</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Activity Log</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">

                {{-- Tab filter bar --}}
                {{-- @include('layouts.partials._datatable_tabs', [
                    'filterKey'  => 'activity-action',
                    'tabs'       => $actions ?? [],
                    'totalLabel' => 'Total: ' . ($totalCount ?? 0) . ' activit' . (($totalCount ?? 0) === 1 ? 'y' : 'ies'),
                ]) --}}

                <div class="card-datatable table-responsive pt-0">
                    <table class="table" id="activityTable">
                        <thead>
                            <tr>
                                <th></th>{{-- responsive control --}}
                                <th>Action</th>
                                <th>Description</th>
                                <th>IP Address</th>
                                <th>User Agent</th>
                                <th>Date & Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($activities as $activity)
                                <tr>
                                    <td></td>
                                    <td>
                                        <span class="badge badge-label-info">
                                            {{ ucfirst(str_replace('_', ' ', $activity->action)) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-custom-body">
                                            {{ $activity->description ?? '—' }}
                                        </span>
                                    </td>
                                    <td>
                                        <code class="text-custom-body">{{ $activity->ip_address }}</code>
                                    </td>
                                    <td>
                                        <span class="text-custom-body">
                                            {{ Str::limit($activity->user_agent, 40) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-custom-body d-block">
                                            {{ $activity->created_at->format('M d, Y H:i:s') }}
                                        </span>
                                        <small class="text-custom-paragraph">
                                            {{ $activity->created_at->diffForHumans() }}
                                        </small>
                                    </td>
                                </tr>
                            @empty
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        AppDataTable.init('#activityTable', {
            exportCols : [1, 2, 3, 4, 5],  // Action → Date & Time
            noSortCols : [],                // all columns sortable
            filterKey  : 'activity-action',
            filterCol  : 1,                 // Action column index
            order      : [[5, 'desc']],     // newest first
            pageLength : 10,
        });
    </script>
    @endpush
@endsection
