<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <head>
            <!-- Fonts -->
            <link rel="preconnect" href="https://fonts.bunny.net">
            <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
            <!-- Scripts -->
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        </head>
        <body class="font-sans antialiased">
            <div class="flex h-screen bg-gray-100 dark:bg-gray-900">
                <!-- Sidebar -->
                <div class="sidebar w-64 bg-gray-800 text-white flex flex-col min-h-screen">
                    <div class="p-5">Dashboard</div>
                    <ul>
                        <li><a href="/dashboard" class="block py-2 px-4 hover:bg-gray-700">Dashboard</a></li>
                        <li><a href="/classforms" class="block py-2 px-4 hover:bg-gray-700">Class Forms</a></li>
                        <li><a href="/exams" class="block py-2 px-4 hover:bg-gray-700">Exams</a></li>
                        <li><a href="/schoolsettings" class="block py-2 px-4 hover:bg-gray-700">School Settings</a></li>
                        <li><a href="/streams" class="block py-2 px-4 hover:bg-gray-700">Streams</a></li>
                        <li><a href="/studentactivities" class="block py-2 px-4 hover:bg-gray-700">Student Activities</a></li>
                        <li><a href="/studentdetails" class="block py-2 px-4 hover:bg-gray-700">Student Details</a></li>
                        <li><a href="/students" class="block py-2 px-4 hover:bg-gray-700">Students</a></li>
                        <li><a href="/subjects" class="block py-2 px-4 hover:bg-gray-700">Subjects</a></li>
                    </ul>
                </div>
    
                <!-- Main content -->
                <div class="flex-1 flex flex-col min-h-screen overflow-hidden">
                    <livewire:layout.navigation />
    
                    <!-- Page Heading -->
                    @if (isset($header))
                        <header class="bg-white dark:bg-gray-800 shadow">
                            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                                {{ $header }}
                            </div>
                        </header>
                    @endif
    
                    <!-- Page Content -->
                    <main class="flex-1 p-4 lg:p-8 overflow-y-auto">
                        {{ $slot }}
                    </main>
                </div>
            </div>
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
        </body>
    </html>