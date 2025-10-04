<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans text-gray-900 antialiased">
    <div class="min-h-screen flex flex-col items-center justify-center px-3 bg-gray-100 dark:bg-gray-900 relative">

        <!-- Tombol Dark Mode di pojok kanan atas -->
        <button id="darkToggle"
            class="absolute top-4 right-4
                   flex items-center gap-2 px-3 py-1 rounded-md border border-blue-500 text-blue-500
                   hover:bg-blue-500 hover:text-white
                   dark:border-blue-400 dark:text-blue-400
                   dark:hover:bg-blue-400 dark:hover:text-white
                   transition-colors duration-200">
            <!-- Icon -->
            <x-heroicon-o-moon x-show="!darkMode" class="h-5 w-5" id="iconMoon" />
            <x-heroicon-o-sun x-show="darkMode" class="h-5 w-5" id="iconSun" />

            <span id="labelDark">Dark</span>
            <span id="labelLight" class="hidden">Light</span>
        </button>

        <!-- Konten Login -->
        <div class="text-center mb-6">
            <!-- Ikon Box -->
            <div class="flex justify-center mb-3">
                <img src="{{ asset('images/box.png') }}" alt="Logo Box" class="w-20 h-20">
            </div>

            <!-- Judul -->
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">
                Selamat datang di Sistem Tracking Surat Jalan
            </h1>

            <!-- Subtitle -->
            <p class="text-gray-600 dark:text-gray-400 mt-2">
                Login untuk mengakses
            </p>
        </div>

        <div class="w-full sm:max-w-md px-6 py-4 bg-white dark:bg-gray-800 shadow-md overflow-hidden sm:rounded-lg">
            {{ $slot }}
        </div>
    </div>

    <script>
        const html = document.documentElement;
        const btnToggle = document.getElementById('darkToggle');
        const iconMoon = document.getElementById('iconMoon');
        const iconSun = document.getElementById('iconSun');
        const labelDark = document.getElementById('labelDark');
        const labelLight = document.getElementById('labelLight');

        // Default = dark jika belum ada setting
        if (localStorage.getItem('theme') === null) {
            localStorage.setItem('theme', 'dark');
        }

        function applyTheme(theme) {
            if (theme === 'dark') {
                html.classList.add('dark');
                iconSun.classList.remove('hidden');
                iconMoon.classList.add('hidden');
                labelLight.classList.remove('hidden');
                labelDark.classList.add('hidden');
            } else {
                html.classList.remove('dark');
                iconMoon.classList.remove('hidden');
                iconSun.classList.add('hidden');
                labelDark.classList.remove('hidden');
                labelLight.classList.add('hidden');
            }
        }

        // Terapkan saat pertama load
        applyTheme(localStorage.getItem('theme'));

        // Event toggle
        btnToggle.addEventListener('click', () => {
            const newTheme = html.classList.contains('dark') ? 'light' : 'dark';
            localStorage.setItem('theme', newTheme);
            applyTheme(newTheme);
        });
    </script>


</body>

</html>
