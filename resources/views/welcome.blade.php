<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Time Tracker</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Nunito', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 antialiased">

    <div class="min-h-screen flex flex-col items-center justify-center px-4">
        @if (Route::has('login'))
            <div class="absolute top-4 right-4 space-x-4 text-sm">
                @auth
                    <a href="{{ url('/home') }}" class="text-blue-600 hover:underline">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="text-blue-600 hover:underline">Log in</a>

                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="text-blue-600 hover:underline">Register</a>
                    @endif
                @endauth
            </div>
        @endif

        <h1 class="text-3xl font-bold mb-4">Welcome to Employee Management</h1>
        <p class="text-gray-600 mb-8 text-center max-w-xl">
       
        </p>
        <div class="w-full max-w-2xl bg-white rounded-lg shadow p-6">
        
            <div class="flex items-start mb-6">
                <div class="text-2xl mr-3">🕒</div>
                <div>
                    <h2 class="text-lg font-semibold"> Log Your Time</h2>
                    <p class="text-sm text-gray-600 mt-1">
                         Record daily logs with description, hours, and minutes. Never miss tracking your efforts.
                    </p>
                </div>
            </div>
        </div>

        <div class="w-full max-w-2xl bg-white rounded-lg shadow p-6 mt-6">
        
            <div class="flex items-start mb-6">
                <div class="text-2xl mr-3">🛌</div>
                <div>
                    <h2 class="text-lg font-semibold">Take Your Leave</h2>
                    <p class="text-sm text-gray-600 mt-1">
                        Request and manage leaves efficiently. View leave balances and history in one place.
                    </p>
                </div>
            </div>
        </div>

        <div class="mt-6 text-sm text-gray-500">
            
        </div>
    </div>

</body>
</html>