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
                        'ep-primary': '#4338ca',
                        'ep-primary-dark': '#3730a3',
                        'ep-dark': '#111827',
                        'ep-sidebar': '#111827',
                        'ep-text': '#9ca3af',
                        'ep-text-hover': '#FFFFFF',
                    }
                }
            }
        }
    </script>
    
    <style>
        .sidebar-transition { transition: width 0.3s ease-in-out; }
        .menu-item-active {
            background: rgba(67, 56, 202, 0.15);
            border-left: 3px solid #4338ca;
            color: white;
        }
        .menu-item:hover {
            background-color: rgba(255, 255, 255, 0.05);
        }
        .sidebar-scroll::-webkit-scrollbar { width: 6px; }
        .sidebar-scroll::-webkit-scrollbar-track { background: rgba(255,255,255,0.05); }
        .sidebar-scroll::-webkit-scrollbar-thumb { background: rgba(67,56,202,0.4); border-radius: 4px; }
    </style>

    {{-- Coloris color picker --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/mdbassit/Coloris@latest/dist/coloris.min.css">
    <script src="https://cdn.jsdelivr.net/gh/mdbassit/Coloris@latest/dist/coloris.min.js"></script>
    <style>
        .clr-field button { width: 100%; height: 44px; border-radius: 12px; border: 2px solid #e5e7eb; cursor: pointer; }
        .clr-field { width: 100%; }
        #clr-picker { border-radius: 16px; box-shadow: 0 20px 60px rgba(0,0,0,0.25); z-index: 9999; }
    </style>

    
    @livewireStyles
</head>
<body class="bg-gray-100 min-h-screen" x-data="{ sidebarOpen: window.innerWidth >= 1024, mobileMenuOpen: false }">
    <div class="flex min-h-screen">
        <!-- Admin Sidebar -->
        <aside class="fixed left-0 top-0 h-screen bg-ep-sidebar sidebar-transition z-40 flex flex-col lg:relative"
               :class="[
                   (sidebarOpen || mobileMenuOpen) ? 'w-64' : 'w-20',
                   mobileMenuOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'
               ]">
            
            <!-- Brand -->
            <div class="h-16 flex items-center justify-between px-4 border-b border-gray-800 flex-shrink-0">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-ep-primary rounded-lg flex items-center justify-center font-bold text-white text-lg flex-shrink-0">
                        SA
                    </div>
                    <span x-show="sidebarOpen || mobileMenuOpen" class="text-white font-bold text-lg">SuperAdmin</span>
                </div>
                <!-- Desktop collapse toggle -->
                <button @click="sidebarOpen = !sidebarOpen" class="hidden lg:block text-ep-primary hover:text-indigo-400 p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              :d="sidebarOpen ? 'M11 19l-7-7 7-7m8 14l-7-7 7-7' : 'M13 5l7 7-7 7M5 5l7 7-7 7'"/>
                    </svg>
                </button>
            </div>
            
            <!-- Nav Links -->
            <nav class="flex-1 overflow-y-auto sidebar-scroll py-4">
                <ul class="space-y-1 px-3">
                    <li>
                        <a href="{{ route('admin.dashboard') }}" 
                           class="menu-item flex items-center px-3 py-3 rounded-lg text-ep-text hover:text-ep-text-hover transition-colors
                                  {{ request()->routeIs('admin.dashboard') ? 'menu-item-active text-white' : '' }}">
                            <span class="flex-shrink-0 mr-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                            </span>
                            <span x-show="sidebarOpen || mobileMenuOpen">Dashboard Global</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.consultants') }}" 
                           class="menu-item flex items-center px-3 py-3 rounded-lg text-ep-text hover:text-ep-text-hover transition-colors
                                  {{ request()->routeIs('admin.consultants*') ? 'menu-item-active text-white' : '' }}">
                            <span class="flex-shrink-0 mr-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            </span>
                            <span x-show="sidebarOpen || mobileMenuOpen">Consultoras</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <!-- Logout -->
            <div class="p-4 border-t border-gray-800 flex-shrink-0">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            x-show="sidebarOpen || mobileMenuOpen"
                            class="w-full flex items-center justify-center px-3 py-2 rounded-lg text-gray-400 hover:bg-gray-800 hover:text-white transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        Cerrar Sesión
                    </button>
                    <button type="submit"
                            x-show="!(sidebarOpen || mobileMenuOpen)"
                            title="Cerrar Sesión"
                            class="w-full flex items-center justify-center px-3 py-2 rounded-lg text-gray-400 hover:bg-gray-800 hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    </button>
                </form>
                <div x-show="sidebarOpen || mobileMenuOpen" class="text-xs text-gray-600 text-center mt-3">
                    XamaSafe v1.0.0
                </div>
            </div>
        </aside>
        
        <!-- Mobile Overlay -->
        <div x-show="mobileMenuOpen"
             @click="mobileMenuOpen = false"
             class="fixed inset-0 bg-black/50 z-30 lg:hidden"></div>
        
        <!-- Main Content -->
        <div class="flex-1 flex flex-col h-screen overflow-hidden">
            <!-- Top Header -->
            <header class="h-16 bg-white shadow-sm flex items-center justify-between px-4 lg:px-8 flex-shrink-0 z-20">
                <!-- Mobile hamburger -->
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="lg:hidden p-2 text-gray-600 hover:text-gray-900 focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>

                <h1 class="text-xl font-semibold text-gray-800">{{ $title ?? 'Super Administración' }}</h1>

                <div class="flex items-center space-x-2">
                    <span class="hidden md:block text-sm font-medium text-gray-700">Administrador Global</span>
                    <div class="w-8 h-8 bg-indigo-600 rounded-full flex items-center justify-center text-white text-sm font-bold">A</div>
                </div>
            </header>
            
            <!-- Main Content Area -->
            <main class="flex-1 overflow-y-auto bg-gray-100">
                <div class="p-6">
                    {{ $slot }}
                </div>
                <footer class="bg-white border-t border-gray-200 py-4 px-8">
                    <div class="text-center text-sm text-gray-500">XamaSafe © {{ date('Y') }}</div>
                </footer>
            </main>
        </div>
    </div>

    @livewireScripts
    @stack('scripts')
</body>
</html>
