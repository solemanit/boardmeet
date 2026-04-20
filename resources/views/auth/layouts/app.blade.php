{{-- app.blade.php --}}
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'BoardMeet')</title>
    <link rel="shortcut icon" href="{{ asset('assets/img/logo/favicon.png') }}" type="image/x-icon">

    <link id="bootstrap-css" rel="stylesheet" type="text/css" href="{{ asset('assets/css/bootstrap.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/conca.css') }}">

    @stack('styles')
</head>

<body>
    <div class="auth-main">
        <div class="container-xxl">
            <div class="auth-wrapper auth-basic p-5 min-vh-100 d-flex align-items-center justify-content-center">
                @yield('content')
            </div>
        </div>
    </div>

    {{-- global js scripts for all pages --}}
    <script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap.js') }}"></script>

    {{-- app js  --}}


    <script src="{{ asset('assets/js/conca.js') }}"></script>

    @stack('scripts')
</body>

</html>
