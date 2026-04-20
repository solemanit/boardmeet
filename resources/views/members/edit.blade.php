@extends('layouts.app')
@section('page-title', 'Edit Member')
@section('title', 'Edit ' . $member->name)

@section('content')
    <div class="page-header pb-7 d-flex justify-content-between align-items-center gap-6 flex-wrap">
        <div>
            <h2 class="fw-semibold fs-7">Edit Member</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('members.index') }}">Members</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('members.show', $member) }}" class="btn btn-outline-secondary d-flex align-items-center gap-2">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                <path d="M19 12H5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                <path d="M12 19L5 12L12 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
            </svg>
            Back
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-4">
            <strong>Please fix the following errors:</strong>
            <ul class="mb-0 mt-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- ✅ Main edit form --}}
    <form action="{{ route('members.update', $member) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('members._form')
        <div class="d-flex align-items-center gap-3">
            <button type="submit" class="btn btn-primary">Update Member</button>
            <a href="{{ route('members.show', $member) }}" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </form>

    {{-- ✅ Photo remove form is OUTSIDE the main form to avoid nesting --}}
    @if ($member->photo)
        <form id="remove-photo-form"
              action="{{ route('members.photo.remove', $member) }}"
              method="POST" style="display:none">
            @csrf
            @method('DELETE')
        </form>
    @endif

    @push('scripts')
        <script>
            // Photo preview on file select
            document.getElementById('photo').addEventListener('change', function () {
                const [file] = this.files;
                if (file) document.getElementById('photo-preview').src = URL.createObjectURL(file);
            });

            // Called by "Remove Photo" button in _form.blade.php
            function confirmRemovePhoto() {
                if (confirm('Are you sure you want to remove this photo?')) {
                    document.getElementById('remove-photo-form').submit();
                }
            }
        </script>
    @endpush
@endsection
