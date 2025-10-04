<!DOCTYPE html>
<html lang="en" 
      x-data="{ darkMode: localStorage.getItem('theme') === 'dark' }"
      x-init="$watch('darkMode', val => localStorage.setItem('theme', val ? 'dark' : 'light'))"
      x-bind:class="{ 'dark': darkMode }"
      class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Data Pegawai' }}</title>
    @vite('resources/css/app.css') {{-- Tailwind --}}
    <script defer src="https://unpkg.com/alpinejs"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet@1.9.3/dist/leaflet.css"/>
</head>

<body class="h-full text-gray-800 dark:text-gray-200 bg-gray-100 dark:bg-gray-900">
    <!-- Navbar -->
    <nav class="bg-white dark:bg-gray-800 shadow">
        <div class="px-4 sm:px-6 md:px-10 lg:px-16 py-4 flex items-center justify-between">
            <!-- Judul -->
            <h1 class="text-xl sm:text-2xl md:text-3xl font-bold flex items-center gap-2">
                <x-heroicon-o-document-text class="h-6 sm:h-7 md:h-8 w-6 sm:w-7 md:w-8 text-blue-600 dark:text-blue-400" />
                Sistem Tracking Surat Jalan
            </h1>

            <!-- Tombol Menu (mobile) -->
            <button @click="openMenu = !openMenu" class="sm:hidden p-2 rounded-md hover:bg-gray-200 dark:hover:bg-gray-700">
                <x-heroicon-o-bars-3 class="h-6 w-6 text-gray-700 dark:text-gray-200" x-show="!openMenu" />
                <x-heroicon-o-x-mark class="h-6 w-6 text-gray-700 dark:text-gray-200" x-show="openMenu" />
            </button>

            <!-- Menu Desktop -->
            <div class="hidden sm:flex items-center gap-3">
                <!-- Tombol Toggle Dark Mode -->
                <button @click="darkMode = !darkMode"
                    class="flex items-center gap-2 px-3 py-1 rounded-md border border-blue-500 text-blue-500
                       hover:bg-blue-500 hover:text-white
                       dark:border-blue-400 dark:text-blue-400
                       dark:hover:bg-blue-400 dark:hover:text-white
                       transition-colors duration-200">

                    <x-heroicon-o-moon x-show="!darkMode" class="h-5 w-5" />
                    <x-heroicon-o-sun x-show="darkMode" class="h-5 w-5" />

                    <span class="hidden md:inline" x-show="!darkMode">Dark</span>
                    <span class="hidden md:inline" x-show="darkMode">Light</span>
                </button>

                <!-- Tombol Logout -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="flex items-center gap-2 px-3 py-1 rounded-md border border-red-500 text-red-500
                           hover:bg-red-500 hover:text-white
                           dark:border-red-400 dark:text-red-400
                           dark:hover:bg-red-400 dark:hover:text-white
                           transition-colors duration-200">
                        <x-heroicon-o-arrow-right-end-on-rectangle class="h-5 w-5" />
                        <span class="hidden md:inline">Logout</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- Menu Mobile -->
        <div x-show="openMenu" x-transition class="sm:hidden px-4 pb-4 space-y-3 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700">
            <!-- Dark Mode -->
            <button @click="darkMode = !darkMode"
                class="w-full flex items-center justify-center gap-2 px-3 py-2 rounded-md border border-blue-500 text-blue-500
                   hover:bg-blue-500 hover:text-white
                   dark:border-blue-400 dark:text-blue-400
                   dark:hover:bg-blue-400 dark:hover:text-white
                   transition-colors duration-200">
                <x-heroicon-o-moon x-show="!darkMode" class="h-5 w-5" />
                <x-heroicon-o-sun x-show="darkMode" class="h-5 w-5" />
                <span x-show="!darkMode">Dark</span>
                <span x-show="darkMode">Light</span>
            </button>

            <!-- Logout -->
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="w-full flex items-center justify-center gap-2 px-3 py-2 rounded-md border border-red-500 text-red-500
                       hover:bg-red-500 hover:text-white
                       dark:border-red-400 dark:text-red-400
                       dark:hover:bg-red-400 dark:hover:text-white
                       transition-colors duration-200">
                    <x-heroicon-o-arrow-right-end-on-rectangle class="h-5 w-5" />
                    Logout
                </button>
            </form>
        </div>
    </nav>

    <!-- Content -->
    <main class="p-4 sm:p-6">
        @yield('content')
    </main>
</body>
</html>

