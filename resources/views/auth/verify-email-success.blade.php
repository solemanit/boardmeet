<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verified - Board Meet</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center">
        <div class="max-w-md w-full space-y-8">
            <div class="text-center">
                <h2 class="mt-6 text-3xl font-extrabold text-gray-900">
                    Email Verified ✓
                </h2>
            </div>

            <div class="bg-white py-8 px-4 shadow rounded-lg sm:px-10">
                <div class="rounded-md bg-green-50 p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-green-800">
                                Email Verified & Account Activated
                            </h3>
                            <div class="mt-2 text-sm text-green-700">
                                <p>Your email address has been verified and your account is now active. You can login immediately.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="space-y-4">
                    <div>
                        <p class="text-sm text-gray-700">
                            <strong>Email:</strong> {{ $user->email }}
                        </p>
                        <p class="text-sm text-gray-700 mt-2">
                            <strong>Name:</strong> {{ $user->name }}
                        </p>
                    </div>

                    <div class="flex space-x-4">
                        <a href="{{ route('login') }}" class="flex-1 flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                            Go to Login
                        </a>
                        <a href="{{ route('dashboard.index') }}" class="flex-1 flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            Go to Dashboard
                        </a>
                    </div>
                </div>

                <p class="mt-4 text-center text-xs text-gray-500">
                    This page will automatically redirect in 5 seconds...
                </p>
            </div>
        </div>
    </div>

    <script>
        setTimeout(function() {
            window.location.href = "{{ route('login') }}";
        }, 5000);
    </script>
</body>
</html>
