@extends('layouts.app')
@section('page-title', 'Add Member')
@section('title', 'Add New Member')

@section('content')
    <div class="page-header pb-7 d-flex justify-content-between align-items-center gap-6 flex-wrap">
        <div>
            <h2 class="fw-semibold fs-7">Add Member</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('members.index') }}">Members</a></li>
                    <li class="breadcrumb-item active">Add</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('members.index') }}" class="btn btn-outline-secondary d-flex align-items-center gap-2">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M19 12H5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><path d="M12 19L5 12L12 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
            Back
        </a>
    </div>

    <form action="{{ route('members.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @include('members._form')
        <div class="d-flex align-items-center gap-3">
            <button type="submit" class="btn btn-primary">Add Member</button>
            <a href="{{ route('members.index') }}" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </form>

    @push('scripts')
        <script>
            document.getElementById('photo').addEventListener('change', function () {
                const [file] = this.files;
                if (file) document.getElementById('photo-preview').src = URL.createObjectURL(file);
            });
        </script>
    @endpush
@endsection
