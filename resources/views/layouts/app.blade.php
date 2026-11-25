<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Room Rental Management' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Kantumruy+Pro:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Kantumruy Pro', 'Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                        khmer: ['Kantumruy Pro', 'Inter', 'sans-serif'],
                    },
                },
            },
        };
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="bg-gray-100 font-sans text-gray-800 antialiased">
    <div class="flex h-screen overflow-hidden">

        <aside class="w-64 bg-slate-900 text-white flex flex-col flex-shrink-0">
            <div class="h-16 flex items-center px-6 bg-slate-950 font-bold text-lg tracking-wider border-b border-slate-800">
                <i class="fa-solid fa-building text-indigo-400 mr-3 text-xl"></i>
                <span>Room<span class="text-indigo-400">Rental</span></span>
            </div>

            <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
                <a href="/"
                   class="flex items-center px-4 py-3 rounded-lg text-sm font-medium transition {{ request()->is('/') ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <i class="fa-solid fa-chart-line w-6 text-base"></i>
                    <span>Dashboard</span>
                </a>

                <a href="{{ route('rooms.index') }}"
                   class="flex items-center px-4 py-3 rounded-lg text-sm font-medium transition {{ request()->routeIs('rooms.*') ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <i class="fa-solid fa-door-open w-6 text-base"></i>
                    <span>Rooms</span>
                </a>

                <a href="{{ route('contracts.index') }}"
                   class="flex items-center px-4 py-3 rounded-lg text-sm font-medium transition {{ request()->routeIs('contracts.*') ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <i class="fa-solid fa-file-contract w-6 text-base"></i>
                    <span>Tenants &amp; Contracts</span>
                </a>

                <a href="#" 
                   class="flex items-center px-4 py-3 rounded-lg text-sm font-medium text-slate-400 hover:bg-slate-800 hover:text-white transition">
                    <i class="fa-solid fa-bolt w-6 text-base"></i>
                    <span>Utility & Invoices</span>
                </a>
            </nav>

            <div class="p-4 border-t border-slate-800 flex items-center space-x-3 bg-slate-950">
                <div class="w-9 h-9 rounded-full bg-indigo-500 flex items-center justify-center font-bold text-sm text-white">
                    AD
                </div>
                <div>
                    <p class="text-sm font-semibold">Admin Panel</p>
                    <p class="text-xs text-slate-400">Room Management</p>
                </div>
            </div>
        </aside>

        <div class="flex-1 flex flex-col overflow-hidden">
            <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-8">
                <div class="text-sm text-gray-500 font-medium">
                    System Analysis & Design Prototype (v1.0)
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-xs bg-emerald-100 text-emerald-800 font-semibold px-2.5 py-1 rounded-full flex items-center">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 mr-1.5"></span> System Online
                    </span>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto p-8 bg-gray-50">
                @yield('content')
            </main>
        </div>

    </div>
</body>
</html>