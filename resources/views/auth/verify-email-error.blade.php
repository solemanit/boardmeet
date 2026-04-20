<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification Failed - Board Meet</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="max-w-md w-full space-y-8">
            <div class="text-center">
                <h2 class="mt-6 text-3xl font-extrabold text-gray-900">
                    Verification Failed
                </h2>
            </div>

            <div class="bg-white py-8 px-4 shadow rounded-lg sm:px-10">
                <div class="rounded-md {{ isset($code) && $code === 'token_expired' ? 'bg-yellow-50' : 'bg-red-50' }} p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            @if(isset($code) && $code === 'token_expired')
                                <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            @else
                                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                            @endif
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium {{ isset($code) && $code === 'token_expired' ? 'text-yellow-800' : 'text-red-800' }}">
                                {{ isset($code) && $code === 'token_expired' ? 'Link Expired' : 'Verification Failed' }}
                            </h3>
                            <div class="mt-2 text-sm {{ isset($code) && $code === 'token_expired' ? 'text-yellow-700' : 'text-red-700' }}">
                                <p>{{ $message }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                @if(isset($code) && $code === 'token_expired' && isset($email))
                    <form method="POST" action="{{ route('resend-verification-email') }}" class="space-y-6">
                        @csrf
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">
                                Email Address
                            </label>
                            <input
                                type="email"
                                id="email"
                                name="email"
                                value="{{ $email }}"
                                required
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                            />
                        </div>

                        <button
                            type="submit"
                            class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700"
                        >
                            Resend Verification Email
                        </button>
                    </form>
                @endif

                <div class="mt-6 flex space-x-4">
                    <a href="{{ route('login') }}" class="flex-1 flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        Go to Login
                    </a>
                    <a href="/" class="flex-1 flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        Go Home
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
