<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Gudang Bahan Kue</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background-color: #b4746f;
            font-family: 'Arial', sans-serif;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center">
    <!-- Forgot Password Form Container -->
    <div class="w-full max-w-md">
        <div class="bg-gray-300 rounded-lg shadow-xl p-8">
            <!-- Header -->
            <div class="text-center mb-6">
                <h1 class="text-lg font-bold text-gray-800 tracking-wide">FORGET PASSWORD</h1>
            </div>

            <!-- Session Status -->
            @if (session('status'))
                <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <!-- Email Field -->
                <div class="mb-6">
                    <label for="email" class="block text-center text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input id="email" 
                           class="w-full px-4 py-3 bg-gray-800 text-gray-300 rounded-md border-0 focus:outline-none focus:ring-0 focus:bg-gray-700" 
                           type="email" 
                           name="email"     
                           value="{{ old('email') }}" 
                           required 
                           autofocus />
                    @error('email')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Send Mail Button -->
                <div class="text-center">
                    <button type="submit"                              
                            class="hover:bg-orange-600 text-black font-medium py-3 px-8 rounded-md transition duration-200 focus:outline-none focus:ring-0"
                            style="background-color: #FEBA4F;">                         
                            Send Mail                     
                    </button>
                </div>
            </form>
        </div>  
    </div>
</body>
</html>