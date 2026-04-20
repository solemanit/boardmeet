{{-- views/users/edit.blade.php --}}
@extends('layouts.app')

@section('page-title', 'Edit User')
@section('title', 'Edit User')

@section('content')
    <div class="page-header pb-7 d-flex justify-content-between align-items-center gap-6 flex-wrap">
        <div>
            <h2 class="fw-semibold fs-7">Edit User</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('users.index') }}">All Users</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit User</li>
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
                            <svg width="24" height="18" viewBox="0 0 24 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M21.2653 10.9126L20.8077 11.5068H20.8077L21.2653 10.9126ZM22.6544 13.8691L22.0721 13.3965H22.0721L22.6544 13.8691ZM20.774 14.25C20.3598 14.25 20.024 14.5858 20.024 15C20.024 15.4142 20.3598 15.75 20.774 15.75V14.25ZM19.0676 9.25305C18.655 9.21573 18.2904 9.5199 18.253 9.93242C18.2157 10.345 18.5199 10.7096 18.9324 10.7469L19.0676 9.25305ZM2.73465 10.9126L3.19224 11.5068H3.19224L2.73465 10.9126ZM1.34555 13.8691L1.92788 13.3965H1.92788L1.34555 13.8691ZM3.22596 15.75C3.64017 15.75 3.97596 15.4142 3.97596 15C3.97596 14.5858 3.64017 14.25 3.22596 14.25V15.75ZM5.06757 10.7469C5.4801 10.7096 5.78427 10.345 5.74695 9.93242C5.70963 9.5199 5.34495 9.21573 4.93242 9.25305L5.06757 10.7469ZM8.08377 12.1112L7.68933 11.4733L8.08377 12.1112ZM15.9162 12.1112L16.3106 11.4733L15.9162 12.1112ZM6.01467 15.6474L5.48718 16.1806V16.1806L6.01467 15.6474ZM17.9853 15.6474L17.4578 15.1143V15.1143L17.9853 15.6474ZM20.8077 11.5068C21.2273 11.8299 21.7171 12.1397 22.0394 12.5477C22.1874 12.7351 22.2403 12.8758 22.2487 12.9752C22.2551 13.051 22.2443 13.1844 22.0721 13.3965L23.2368 14.3418C23.6123 13.8791 23.7875 13.3714 23.7433 12.8489C23.7012 12.3499 23.4669 11.935 23.2164 11.6179C22.7418 11.0171 21.9894 10.5235 21.7229 10.3184L20.8077 11.5068ZM22.0721 13.3965C21.5767 14.0069 21.1735 14.25 20.774 14.25V15.75C21.8731 15.75 22.6619 15.0501 23.2368 14.3418L22.0721 13.3965ZM18.9324 10.7469C19.5727 10.8049 20.2174 11.0522 20.8077 11.5068L21.7229 10.3184C20.9186 9.69902 20.0061 9.33796 19.0676 9.25305L18.9324 10.7469ZM19.75 5.5C19.75 6.4665 18.9665 7.25 18 7.25V8.75C19.7949 8.75 21.25 7.29493 21.25 5.5H19.75ZM18 3.75C18.9665 3.75 19.75 4.5335 19.75 5.5H21.25C21.25 3.70507 19.7949 2.25 18 2.25V3.75ZM2.27705 10.3184C2.01061 10.5235 1.25814 11.0171 0.783541 11.6179C0.533058 11.935 0.298819 12.3499 0.256646 12.8489C0.212479 13.3714 0.387655 13.8791 0.763212 14.3418L1.92788 13.3965C1.75571 13.1844 1.74491 13.051 1.75132 12.9752C1.75972 12.8758 1.8126 12.7351 1.96058 12.5477C2.2829 12.1397 2.7727 11.8299 3.19224 11.5068L2.27705 10.3184ZM0.763212 14.3418C1.33807 15.0501 2.12686 15.75 3.22596 15.75V14.25C2.82645 14.25 2.42327 14.0069 1.92788 13.3965L0.763212 14.3418ZM4.93242 9.25305C3.9939 9.33796 3.08135 9.69902 2.27705 10.3184L3.19224 11.5068C3.7826 11.0522 4.42727 10.8049 5.06757 10.7469L4.93242 9.25305ZM2.25 5.5C2.25 7.29493 3.70508 8.75 5.5 8.75V7.25C4.5335 7.25 3.75 6.4665 3.75 5.5H2.25ZM5.5 2.25C3.70508 2.25 2.25 3.70507 2.25 5.5H3.75C3.75 4.5335 4.5335 3.75 5.5 3.75V2.25ZM8.47821 12.7491C10.6325 11.417 13.3674 11.417 15.5217 12.7491L16.3106 11.4733C13.6728 9.84224 10.3271 9.84224 7.68933 11.4733L8.47821 12.7491ZM8.81559 17.75H15.1843V16.25H8.81559V17.75ZM6.54215 15.1143C6.24786 14.8231 6.24425 14.6527 6.25178 14.5812C6.26373 14.4676 6.34381 14.2859 6.5888 14.0368C7.09833 13.5189 7.88621 13.1152 8.47821 12.7491L7.68933 11.4733C7.25955 11.739 6.19702 12.2962 5.51951 12.9849C5.17099 13.3391 4.82339 13.8221 4.76002 14.4242C4.69221 15.0683 4.96562 15.6646 5.48718 16.1806L6.54215 15.1143ZM15.5217 12.7491C16.1137 13.1152 16.9016 13.5189 17.4111 14.0368C17.6561 14.2859 17.7362 14.4676 17.7482 14.5812C17.7557 14.6527 17.7521 14.8231 17.4578 15.1143L18.5127 16.1806C19.0343 15.6646 19.3077 15.0683 19.2399 14.4242C19.1765 13.8221 18.8289 13.3391 18.4804 12.9849C17.8029 12.2962 16.7404 11.739 16.3106 11.4733L15.5217 12.7491ZM17.4578 15.1143C16.7252 15.839 16.0171 16.25 15.1843 16.25V17.75C16.5838 17.75 17.6511 17.033 18.5127 16.1806L17.4578 15.1143ZM5.48718 16.1806C6.3488 17.033 7.41613 17.75 8.81559 17.75V16.25C7.98284 16.25 7.27468 15.839 6.54215 15.1143L5.48718 16.1806ZM14.75 4.5C14.75 6.01878 13.5187 7.25 12 7.25V8.75C14.3472 8.75 16.25 6.84721 16.25 4.5H14.75ZM12 7.25C10.4812 7.25 9.24997 6.01878 9.24997 4.5H7.74997C7.74997 6.84721 9.65276 8.75 12 8.75V7.25ZM9.24997 4.5C9.24997 2.98122 10.4812 1.75 12 1.75V0.25C9.65276 0.25 7.74997 2.15279 7.74997 4.5H9.24997ZM12 1.75C13.5187 1.75 14.75 2.98122 14.75 4.5H16.25C16.25 2.15279 14.3472 0.25 12 0.25V1.75Z" fill="currentColor"/>
                            </svg>
                        </span>
                        Edit User
                    </h3>
                </div>
                <div class="pure-card-body">
                    <form action="{{ route('users.update', $user) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row gy-5">

                            {{-- Name --}}
                            <div class="col-md-6">
                                <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="name"
                                    class="form-control @error('name') is-invalid @enderror"
                                    placeholder="Enter full name"
                                    value="{{ old('name', $user->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Email --}}
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" id="email"
                                    class="form-control @error('email') is-invalid @enderror"
                                    placeholder="Enter email"
                                    value="{{ old('email', $user->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Phone --}}
                            <div class="col-md-6">
                                <label for="phone_number" class="form-label">Phone</label>
                                <input type="text" name="phone_number" id="phone_number"
                                    class="form-control @error('phone_number') is-invalid @enderror"
                                    placeholder="Enter phone number"
                                    value="{{ old('phone_number', $user->phone_number) }}">
                                @error('phone_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Role --}}
                            <div class="col-md-6">
                                <label for="role_id" class="form-label">Role <span class="text-danger">*</span></label>
                                <select name="role_id" id="role_id"
                                    class="form-select @error('role_id') is-invalid @enderror" required>
                                    <option value="">Select role</option>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->id }}"
                                            {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
                                            {{ $role->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('role_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Status --}}
                            <div class="col-md-6">
                                <label class="form-label">Status</label>
                                <div class="d-flex align-items-center gap-4 mt-1">
                                    @if ($user->is_active)
                                        <span class="badge badge-label-success me-2">Active</span>
                                        @if (auth()->user()->hasPermission('users.edit'))
                                            <form action="{{ route('users.deactivate', $user) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-label-secondary">
                                                    Deactivate
                                                </button>
                                            </form>
                                        @endif
                                    @else
                                        <span class="badge badge-label-secondary me-2">Inactive</span>
                                        @if (auth()->user()->hasPermission('users.edit'))
                                            <form action="{{ route('users.activate', $user) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-primary">
                                                    Activate
                                                </button>
                                            </form>
                                        @endif
                                    @endif
                                </div>
                            </div>

                            {{-- Last Login --}}
                            <div class="col-md-6">
                                <label class="form-label">Last Login</label>
                                <p class="form-control-plaintext text-custom-body">
                                    {{ $user->last_login_at?->diffForHumans() ?? 'Never' }}
                                </p>
                            </div>

                            {{-- Submit --}}
                            <div class="col-12 mt-7">
                                <button type="submit" class="btn btn-primary">Update User</button>
                                <a href="{{ route('users.show', $user) }}" class="btn btn-label-secondary ms-2">Cancel</a>
                            </div>

                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
