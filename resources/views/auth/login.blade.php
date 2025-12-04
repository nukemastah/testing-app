<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - MASTER SENTRA BOGA</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background-color: #b4746f;
            font-family: 'Arial', sans-serif;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center">
    <!-- Laravel Logo (opsional, bisa dihapus jika tidak diperlukan) -->
    <div class="absolute top-10">
        <svg class="w-12 h-12 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
            <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
        </svg>
    </div>

    <!-- Login Form Container -->
    <div class="w-full max-w-md">
        <div class="bg-gray-200 rounded-lg shadow-xl p-8">
            <!-- Header -->
            <div class="text-center mb-8">
                <h1 class="text-xl font-bold text-gray-800 tracking-wide">MASTER SENTRA BOGA</h1>
            </div>

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Email Field -->
                <div class="mb-4">
                    <label for="email" class="block text-center text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input id="email" 
                           class="w-full px-4 py-3 bg-gray-800 text-gray-300 rounded-md border-0 focus:outline-none focus:ring-0 focus:bg-gray-700" 
                           type="email" 
                           name="email" 
                           value="{{ old('email') }}" 
                           required 
                           autofocus 
                           autocomplete="username" />
                    @error('email')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password Field -->
                <div class="mb-4">
                    <label for="password" class="block text-center text-sm font-medium text-gray-700 mb-2">Password</label>
                    <input id="password" 
                           class="w-full px-4 py-3 bg-gray-800 text-gray-300 rounded-md border-0 focus:outline-none focus:ring-0 focus:bg-gray-700"
                           type="password"
                           name="password"
                           required 
                           autocomplete="current-password" />
                    @error('password')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="mb-6 flex items-center">
                    <input id="remember_me" 
                           type="checkbox" 
                           class="w-4 h-4 text-gray-600 bg-white border-gray-400 rounded focus:ring-0" 
                           name="remember">
                    <label for="remember_me" class="ml-2 text-sm text-gray-700">remember me</label>
                </div>

                <!-- Login Button -->
                <div class="mb-4">
                    <button type="submit" 
                            class="w-full bg-green-500 hover:bg-green-600 text-white font-medium py-3 px-4 rounded-md transition duration-200 focus:outline-none focus:ring-0"
                            style="background-color: #FEBA4F;">
                        LOGIN
                            
                    </button>
                </div>

                <!-- Register Button -->
                <div class="mb-4">
                    <a href="{{ route('register') }}" 
                       class="w-full block text-center bg-blue-500 hover:bg-blue-600 text-white font-medium py-3 px-4 rounded-md transition duration-200 focus:outline-none"
                       style="background-color: #F96815;">
                        REGISTER
                    </a>
                </div>

                <!-- Forgot Password Link -->
                <div class="text-right">
                    @if (Route::has('password.request'))
                        <a class="text-sm text-gray-600 hover:text-gray-800 underline" 
                           href="{{ route('password.request') }}">
                            Forget your Password?
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>
</body>
</html>