@extends('layouts.app')
@section('page-title', 'Member Details')
@section('title', $member->name)

@section('content')
    <div class="page-header pb-7 d-flex justify-content-between align-items-center gap-6 flex-wrap">
        <div>
            <h2 class="fw-semibold fs-7">{{ $member->name }}</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('members.index') }}">Members</a></li>
                    <li class="breadcrumb-item active">Details</li>
                </ol>
            </nav>
        </div>
        @if (auth()->user()->hasRole('admin'))
            <div class="d-flex gap-2">
                <a href="{{ route('members.edit', $member) }}" class="btn btn-primary d-flex align-items-center gap-2">Edit</a>
                <form action="{{ route('members.destroy', $member) }}" method="POST"
                    onsubmit="return confirm('Delete this member?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-outline-danger">Delete</button>
                </form>
            </div>
        @endif
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">
        {{-- Left: Photo & Quick Info --}}
        <div class="col-lg-4">
            <div class="pure-card rounded-custom card-bg shadow-custom text-center p-4">
                <img src="{{ $member->photo_url }}"
                    class="rounded-circle object-fit-cover border mb-3"
                    width="110" height="110" alt="{{ $member->name }}">
                <h5 class="fw-semibold mb-1">{{ $member->name }}</h5>
                <p class="text-custom-body small mb-2">{{ $member->designation ?? '—' }}</p>
                @if ($member->status === 'active')
                    <span class="badge badge-label-success mb-3">Active</span>
                @else
                    <span class="badge badge-label-danger mb-3">Inactive</span>
                @endif

                @if (auth()->user()->hasRole('admin'))
                    <div class="mt-2">
                        @if ($member->status === 'active')
                            <form action="{{ route('members.deactivate', $member) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-danger w-100">Deactivate</button>
                            </form>
                        @else
                            <form action="{{ route('members.activate', $member) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-success w-100">Activate</button>
                            </form>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        {{-- Right: Details --}}
        <div class="col-lg-8">
            {{-- Personal --}}
            <div class="pure-card rounded-custom card-bg shadow-custom mb-4">
                <div class="pure-card-header"><h3 class="pure-card-title m-0">Personal information</h3></div>
                <div class="pure-card-body">
                    <div class="row g-3">
                        @foreach ([
                            'Email'       => $member->email,
                            'Phone'         => $member->phone,
                            'Date of Birth'  => $member->date_of_birth?->format('d M Y'),
                            'Age'         => $member->age ? $member->age . ' years' : null,
                            'Gender'        => $member->gender ? ucfirst($member->gender) : null,
                            'Blood Group' => $member->blood_group,
                            'Address'       => $member->address,
                        ] as $label => $value)
                            <div class="col-md-6">
                                <small class="text-custom-paragraph d-block">{{ $label }}</small>
                                <span class="fw-medium">{{ $value ?? '—' }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Professional --}}
            <div class="pure-card rounded-custom card-bg shadow-custom mb-4">
                <div class="pure-card-header"><h3 class="pure-card-title m-0">Professional Information</h3></div>
                <div class="pure-card-body">
                    <div class="row g-3">
                        @foreach ([
                            'Employee ID'  => $member->employee_id,
                            'Designation'  => $member->designation,
                            'Department'   => $member->department,
                            'Organization' => $member->organization,
                            'Joining Date' => $member->joining_date?->format('d M Y'),
                        ] as $label => $value)
                            <div class="col-md-6">
                                <small class="text-custom-paragraph d-block">{{ $label }}</small>
                                <span class="fw-medium">{{ $value ?? '—' }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Educational --}}
            <div class="pure-card rounded-custom card-bg shadow-custom">
                <div class="pure-card-header"><h3 class="pure-card-title m-0">Educational Information</h3></div>
                <div class="pure-card-body">
                    <div class="row g-3">
                        @foreach ([
                            'Highest Degree'           => $member->degree,
                            'Institution' => $member->institution,
                            'Passing Year'        => $member->passing_year,
                        ] as $label => $value)
                            <div class="col-md-4">
                                <small class="text-custom-paragraph d-block">{{ $label }}</small>
                                <span class="fw-medium">{{ $value ?? '—' }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
