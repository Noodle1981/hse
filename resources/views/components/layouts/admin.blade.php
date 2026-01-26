<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Super Admin - XamaSafe' }}</title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'ep-primary': '#4338ca', // Indigo para diferenciarlo del naranja corporativo de las empresas
                        'ep-primary-dark': '#3730a3',
                        'ep-dark': '#111827',
                        'ep-sidebar': '#111827',
                        'ep-text': '#9ca3af',
                    }
                }
            }
        }
    </script>
    
    <style>
        .sidebar-transition { transition: width 0.3s ease-in-out; }
        .menu-item-active {
            background: rgba(67, 56, 202, 0.1);
            border-left: 3px solid #4338ca;
            color: white;
        }
    </style>
    
    @livewireStyles
</head>
<body class="bg-gray-50 min-h-screen" x-data="{ sidebarOpen: true, mobileMenuOpen: false }">
    <div class="flex min-h-screen">
        <!-- Admin Sidebar -->
        <aside :class="sidebarOpen ? 'w-64' : 'w-20'" 
               class="fixed left-0 top-0 h-screen bg-ep-sidebar sidebar-transition z-40 flex flex-col lg:relative lg:translate-x-0"
               :class="mobileMenuOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">
            
            <div class="h-16 flex items-center justify-between px-4 border-b border-gray-800">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-ep-primary rounded-lg flex items-center justify-center font-bold text-white text-lg">
                        SA
                    </div>
                    <span x-show="sidebarOpen" class="text-white font-bold text-lg">SuperAdmin</span>
                </div>
            </div>
            
            <nav class="flex-1 overflow-y-auto py-4">
                <ul class="space-y-1 px-3">
                    <li>
                        <a href="{{ route('admin.dashboard') }}" 
                           class="flex items-center px-3 py-3 rounded-lg text-ep-text hover:bg-gray-800 hover:text-white transition-all
                                  {{ request()->routeIs('admin.dashboard') ? 'menu-item-active' : '' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                            <span x-show="sidebarOpen">Dashboard Global</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.consultants') }}" 
                           class="flex items-center px-3 py-3 rounded-lg text-ep-text hover:bg-gray-800 hover:text-white transition-all
                                  {{ request()->routeIs('admin.consultants') ? 'menu-item-active' : '' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            <span x-show="sidebarOpen">Consultoras</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <div class="p-4 border-t border-gray-800">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center px-3 py-2 rounded-lg text-gray-400 hover:bg-gray-800 hover:text-white transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        <span x-show="sidebarOpen">Cerrar Sesión</span>
                    </button>
                </form>
            </div>
        </aside>
        
        <!-- Main Content -->
        <div class="flex-1 flex flex-col h-screen overflow-hidden">
            <header class="h-16 bg-white shadow-sm flex items-center justify-between px-8 flex-shrink-0 z-20">
                <button @click="sidebarOpen = !sidebarOpen" class="p-2 text-gray-600 hover:text-gray-900">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <h1 class="text-xl font-bold text-gray-800">{{ $title ?? 'Super Administración' }}</h1>
                <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 bg-indigo-600 rounded-full flex items-center justify-center text-white text-sm font-bold">A</div>
                    <span class="text-sm font-medium text-gray-700">Administrador Global</span>
                </div>
            </header>
            
            <main class="flex-1 overflow-y-auto p-6">
                {{ $slot }}
            </main>
        </div>
    </div>

    @livewireScripts
</body>
</html>
