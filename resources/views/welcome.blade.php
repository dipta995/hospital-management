<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Diagnosis </title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased">
        <div class="relative sm:flex sm:justify-center sm:items-center min-h-screen bg-dots-darker bg-center bg-gray-100 dark:bg-dots-lighter dark:bg-gray-900 selection:bg-red-500 selection:text-white">
            <div class="max-w-7xl mx-auto p-6 lg:p-8">
                <div style="display: flex; justify-content: center; align-items: center; height: 100vh; flex-direction: column; text-align: center;">
                    @if(Auth::guard('admin')->check() == true)
                        <h4>Welcome to Diagnostic Management System</h4>
                        <a style="background-color: #0e4f50; margin-top: 20px;"
                           class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow-md transition ease-in-out duration-300"
                           href="{{ route('admin.login') }}">
                            Continue
                        </a>
                    @else
                        <a style="background-color: #0e4f50;"
                           class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow-md transition ease-in-out duration-300"
                           href="{{ route('admin.login') }}">
                            Login
                        </a>
                    @endif
                </div>

            </div>

    </body>
</html>

