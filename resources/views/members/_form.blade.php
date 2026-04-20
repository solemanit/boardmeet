@php
    $member ??= new \App\Models\Member();
    $isEdit = $member->exists;
@endphp

{{-- ── Personal Information ─────────────────────────────────────── --}}
<div class="pure-card rounded-custom card-bg shadow-custom mb-4">
    <div class="pure-card-header">
        <h3 class="pure-card-title m-0">Personal Information</h3>
    </div>
    <div class="pure-card-body">

        {{-- Photo Upload --}}
        <div class="mb-4 d-flex align-items-center gap-4">
            <div class="position-relative">
                <img id="photo-preview"
                    src="{{ $isEdit ? $member->photo_url : asset('assets/img/avatar/default-avatar.png') }}"
                    class="rounded-circle object-fit-cover border"
                    width="90" height="90" alt="Photo">
            </div>
            <div>
                <label class="form-label fw-medium mb-1">Profile Picture</label>
                <input type="file" name="photo" id="photo" accept="image/*"
                    class="form-control form-control-sm @error('photo') is-invalid @enderror"
                    style="max-width:260px">
                <small class="text-custom-paragraph d-block mt-1">JPG, PNG, WEBP — Maximum 2MB</small>
                @error('photo')<div class="invalid-feedback">{{ $message }}</div>@enderror

                {{-- ✅ No nested <form> here — button triggers JS instead --}}
                @if ($isEdit && $member->photo)
                    <button type="button"
                        class="btn btn-sm btn-outline-danger mt-1"
                        onclick="confirmRemovePhoto()">
                        Remove Photo
                    </button>
                @endif
            </div>
        </div>

        <div class="row g-4">
            <div class="col-md-6">
                <label class="form-label fw-medium">Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                    value="{{ old('name', $member->name ?? '') }}" placeholder="Full Name" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-6">
                <label class="form-label fw-medium">Email</label>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                    value="{{ old('email', $member->email ?? '') }}" placeholder="email@example.com">
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-6">
                <label class="form-label fw-medium">Phone Number</label>
                <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror"
                    value="{{ old('phone', $member->phone ?? '') }}" placeholder="01XXXXXXXXX">
                @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-3">
                <label class="form-label fw-medium">Date of Birth</label>
                <input type="date" name="date_of_birth" class="form-control @error('date_of_birth') is-invalid @enderror"
                    value="{{ old('date_of_birth', $member->date_of_birth?->format('Y-m-d') ?? '') }}">
                @error('date_of_birth')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-3">
                <label class="form-label fw-medium">Gender</label>
                <select name="gender" class="form-select @error('gender') is-invalid @enderror">
                    <option value="">-- Select --</option>
                    <option value="male"   @selected(old('gender', $member->gender ?? '') === 'male')>Male</option>
                    <option value="female" @selected(old('gender', $member->gender ?? '') === 'female')>Female</option>
                    <option value="other"  @selected(old('gender', $member->gender ?? '') === 'other')>Other</option>
                </select>
                @error('gender')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-3">
                <label class="form-label fw-medium">Blood Group</label>
                <select name="blood_group" class="form-select @error('blood_group') is-invalid @enderror">
                    <option value="">-- Select --</option>
                    @foreach (['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bg)
                        <option value="{{ $bg }}" @selected(old('blood_group', $member->blood_group ?? '') === $bg)>{{ $bg }}</option>
                    @endforeach
                </select>
                @error('blood_group')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-9">
                <label class="form-label fw-medium">Address</label>
                <textarea name="address" class="form-control @error('address') is-invalid @enderror"
                    rows="2" placeholder="Current Address">{{ old('address', $member->address ?? '') }}</textarea>
                @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
    </div>
</div>

{{-- ── Professional Information ─────────────────────────────────── --}}
<div class="pure-card rounded-custom card-bg shadow-custom mb-4">
    <div class="pure-card-header">
        <h3 class="pure-card-title m-0">Professional Information</h3>
    </div>
    <div class="pure-card-body">
        <div class="row g-4">
            <div class="col-md-4">
                <label class="form-label fw-medium">Employee ID</label>
                <input type="text" name="employee_id" class="form-control @error('employee_id') is-invalid @enderror"
                    value="{{ old('employee_id', $member->employee_id ?? '') }}" placeholder="EMP-0001">
                @error('employee_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-4">
                <label class="form-label fw-medium">Designation</label>
                <input type="text" name="designation" class="form-control @error('designation') is-invalid @enderror"
                    value="{{ old('designation', $member->designation ?? '') }}" placeholder="Manager">
                @error('designation')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-4">
                <label class="form-label fw-medium">Department</label>
                <input type="text" name="department" class="form-control @error('department') is-invalid @enderror"
                    value="{{ old('department', $member->department ?? '') }}" placeholder="Finance">
                @error('department')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-6">
                <label class="form-label fw-medium">Organization</label>
                <input type="text" name="organization" class="form-control @error('organization') is-invalid @enderror"
                    value="{{ old('organization', $member->organization ?? '') }}" placeholder="Company Ltd.">
                @error('organization')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-3">
                <label class="form-label fw-medium">Joining Date</label>
                <input type="date" name="joining_date" class="form-control @error('joining_date') is-invalid @enderror"
                    value="{{ old('joining_date', $member->joining_date?->format('Y-m-d') ?? '') }}">
                @error('joining_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-3">
                <label class="form-label fw-medium">Status <span class="text-danger">*</span></label>
                <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                    <option value="active"   @selected(old('status', $member->status ?? 'active') === 'active')>Active</option>
                    <option value="inactive" @selected(old('status', $member->status ?? '') === 'inactive')>Inactive</option>
                </select>
                @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
    </div>
</div>

{{-- ── Educational Information ──────────────────────────────────── --}}
<div class="pure-card rounded-custom card-bg shadow-custom mb-4">
    <div class="pure-card-header">
        <h3 class="pure-card-title m-0">Educational Information</h3>
    </div>
    <div class="pure-card-body">
        <div class="row g-4">
            <div class="col-md-4">
                <label class="form-label fw-medium">Highest Degree</label>
                <input type="text" name="degree" class="form-control @error('degree') is-invalid @enderror"
                    value="{{ old('degree', $member->degree ?? '') }}" placeholder="MBA, BSc, PhD…">
                @error('degree')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-5">
                <label class="form-label fw-medium">Institution</label>
                <input type="text" name="institution" class="form-control @error('institution') is-invalid @enderror"
                    value="{{ old('institution', $member->institution ?? '') }}" placeholder="University / College">
                @error('institution')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-3">
                <label class="form-label fw-medium">Passing Year</label>
                <input type="number" name="passing_year" class="form-control @error('passing_year') is-invalid @enderror"
                    value="{{ old('passing_year', $member->passing_year ?? '') }}"
                    placeholder="{{ date('Y') }}" min="1950" max="{{ date('Y') + 1 }}">
                @error('passing_year')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
    </div>
</div>
