<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $title ?? 'Attendance System' }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Inter:wght@400;500;600;700&family=Oswald:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <script src="https://cdn.tailwindcss.com"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-gray-100 min-h-screen">

    <header class="bg-blue-700 text-white">
        <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
            <h1 class="font-bold text-xl">Church Attendance</h1>
            <nav class="space-x-4 text-sm">
                <a href="{{ route('attendance.index') }}" class="hover:underline">Attendance</a>
                <a href="{{ route('attendance.records') }}" class="hover:underline">Records</a>
                <a href="{{ route('people.index') }}" class="hover:underline">People</a>
                <a href="{{ route('families.index') }}" class="hover:underline">Families</a>
            </nav>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 py-6">
        @yield('content')
    </main>

    @livewireScripts
</body>
</html>
