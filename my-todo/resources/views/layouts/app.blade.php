<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title')</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <header style="background-color: #b0e0e6;" class="p-4 sm:w-full">
            <div class="container mx-auto flex items-center justify-between">
                <a href="{{ route('todo.index') }}">
                    <h1 class="font-bold text-lg text-white">You are GOAL</h1>
                </a>
                <div class="flex items-center space-x-4">
                    @auth
                    <a href="#" class="text-white font-bold">ログアウト</a>
                    @else
                    <a href="#" class="text-white font-bold">ログイン</a>
                    <a href="#" class="text-white font-bold">新規登録</a>
                    @endauth
                </div>
            </div>
        </header>
        <div class="content">
            @yield('content')
        </div>
        <footer style="background-color: #b0e0e6;" class="h-6 flex items-center justify-center p-2 mt-4 font-bold text-white">
            <p>© 2025 You are Goal</p>
        </footer>
    </body>
</html>
