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

        <!-- Style -->
        <style>[x-cloak] { display: none !important; }</style>
    </head>
    <body class="font-sans antialiased min-h-screen flex flex-col">
        <header style="background-color: #b0e0e6;" class="p-4 sm:w-full">
            <div class="container mx-auto flex items-center justify-between">
                <a href="{{ route('goal.index') }}">
                    <h1 class="font-bold text-lg text-white">Go for Goals</h1>
                </a>
                <div class="flex items-center space-x-4">
                    @auth
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" class="text-white rounded-full hover:bg-cyan-400 focus:outline-none text-2xl">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                class="w-6 h-6 text-white0 cursor-pointer">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 6.75a.75.75 0 110-1.5.75.75 0 010 1.5zM12 12.75a.75.75 0 110-1.5.75.75 0 010 1.5zM12 18.75a.75.75 0 110-1.5.75.75 0 010 1.5z" />
                            </svg>
                        </button>
                        <div x-show="open" x-cloak @click.outside=" open = false" class="absolute right-0 mt-2 w-48 bg-white border border-gray-300 rounded shadow-lg z-50">
                            <a href="{{ route('goal.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">{{ __('messages.back_to_home') }}</a>
                            <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">{{ __('messages.profile_edit') }}</a>
                            <a href="{{ route('contact.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">{{ __('messages.contact') }}</a>
                            <form action="{{ route('logout') }}" method="POST" class="block ">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-500 font-bold hover:bg-gray-100">{{ __('messages.logout') }}</button>
                            </form>
                        </div>
                    </div>
                    @else
                    <a href="/login" class="text-white font-bold px-3 py-1 rounded transition duration-200 hover:bg-white hover:text-[#4682b4]">{{ __('messages.login') }}</a>
                    <a href="/register" class="text-white font-bold px-3 py-1 rounded transition duration-200 hover:bg-white hover:text-[#4682b4]">{{ __('messages.register') }}</a>
                    @endauth
                </div>
            </div>
        </header>
        <div class="content flex-grow">
            @yield('content')
        </div>
        <footer style="background-color: #b0e0e6;" class="h-6 flex items-center justify-center p-2 font-bold text-white">
            <p>© 2025 You are Goal</p>
        </footer>
    </body>
</html>
