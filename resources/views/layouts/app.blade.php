<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'EP - Sistema de Checklists' }}</title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'ep-primary': '#F69239',
                        'ep-primary-dark': '#E07D1E',
                        'ep-dark': '#1E1E2D',
                        'ep-dark-light': '#2D2D3F',
                        'ep-sidebar': '#1E1E2D',
                        'ep-text': '#B5B5C3',
                        'ep-text-hover': '#FFFFFF',
                    }
                }
            }
        }
    </script>
    
    <style>
        /* Sidebar animations */
        .sidebar-collapsed {
            width: 70px !important;
        }
        .sidebar-collapsed .menu-text,
        .sidebar-collapsed .brand-text,
        .sidebar-collapsed .submenu {
            display: none;
        }
        .sidebar-collapsed .menu-icon {
            margin-right: 0;
        }
        
        /* Smooth transitions */
        .sidebar-transition {
            transition: width 0.3s ease-in-out;
        }
        
        /* Active menu item */
        .menu-item-active {
            background: linear-gradient(135deg, rgba(246, 146, 57, 0.3) 0%, rgba(246, 146, 57, 0.1) 100%);
            border-left: 3px solid #F69239;
        }
        
        /* Menu hover */
        .menu-item:hover {
            background-color: rgba(255, 255, 255, 0.05);
        }
        
        /* Custom scrollbar */
        .sidebar-scroll::-webkit-scrollbar {
            width: 4px;
        }
        .sidebar-scroll::-webkit-scrollbar-track {
            background: transparent;
        }
        .sidebar-scroll::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.2);
            border-radius: 2px;
        }
    </style>
    
    @livewireStyles
</head>
<body class="bg-gray-100 min-h-screen" x-data="{ sidebarOpen: true, mobileMenuOpen: false }">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside :class="sidebarOpen ? 'w-64' : 'w-20'" 
               class="fixed left-0 top-0 h-screen bg-ep-sidebar sidebar-transition z-40 flex flex-col
                      lg:relative lg:translate-x-0"
               :class="mobileMenuOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">
            
            <!-- Brand/Logo -->
            <div class="h-16 flex items-center justify-between px-4 border-b border-gray-700">
                <a href="{{ route('checklists.index') }}" class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-ep-primary rounded-lg flex items-center justify-center font-bold text-white text-lg flex-shrink-0">
                        EP
                    </div>
                    <span x-show="sidebarOpen" class="brand-text text-white font-semibold text-lg transition-opacity">
                        Checklists
                    </span>
                </a>
                <button @click="sidebarOpen = !sidebarOpen" class="hidden lg:block text-gray-400 hover:text-white p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              :d="sidebarOpen ? 'M11 19l-7-7 7-7m8 14l-7-7 7-7' : 'M13 5l7 7-7 7M5 5l7 7-7 7'"/>
                    </svg>
                </button>
            </div>
            
            <!-- Menu -->
            <nav class="flex-1 overflow-y-auto sidebar-scroll py-4">
                <ul class="space-y-1 px-3">
                    <!-- Dashboard -->
                    <li>
                        <a href="{{ route('checklists.index') }}" 
                           class="menu-item flex items-center px-3 py-3 rounded-lg text-ep-text hover:text-ep-text-hover transition-colors
                                  {{ request()->routeIs('checklists.index') ? 'menu-item-active text-white' : '' }}">
                            <span class="menu-icon mr-3 flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                                </svg>
                            </span>
                            <span x-show="sidebarOpen" class="menu-text">Plantillas</span>
                        </a>
                    </li>
                    
                    <!-- Nuevo Checklist -->
                    <li>
                        <a href="{{ route('checklists.create') }}" 
                           class="menu-item flex items-center px-3 py-3 rounded-lg text-ep-text hover:text-ep-text-hover transition-colors
                                  {{ request()->routeIs('checklists.create') ? 'menu-item-active text-white' : '' }}">
                            <span class="menu-icon mr-3 flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                            </span>
                            <span x-show="sidebarOpen" class="menu-text">Nuevo Checklist</span>
                        </a>
                    </li>
                    
                    <!-- Historial -->
                    <li>
                        <a href="{{ route('inspections.index') }}" 
                           class="menu-item flex items-center px-3 py-3 rounded-lg text-ep-text hover:text-ep-text-hover transition-colors
                                  {{ request()->routeIs('inspections.*') ? 'menu-item-active text-white' : '' }}">
                            <span class="menu-icon mr-3 flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                </svg>
                            </span>
                            <span x-show="sidebarOpen" class="menu-text">Historial</span>
                        </a>
                    </li>
                    
                    <!-- Separador -->
                    <li class="pt-4 pb-2" x-show="sidebarOpen">
                        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider px-3">Reportes</span>
                    </li>
                    
                    <!-- Dashboard/Estadísticas -->
                    <li>
                        <a href="{{ route('dashboard') }}" 
                           class="menu-item flex items-center px-3 py-3 rounded-lg text-ep-text hover:text-ep-text-hover transition-colors
                                  {{ request()->routeIs('dashboard') ? 'menu-item-active text-white' : '' }}">
                            <span class="menu-icon mr-3 flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                            </span>
                            <span x-show="sidebarOpen" class="menu-text">Dashboard</span>
                        </a>
                    </li>
                </ul>
            </nav>
            
            <!-- Footer del Sidebar -->
            <div class="p-4 border-t border-gray-700">
                <!-- Logout Button -->
                <form method="POST" action="{{ route('logout') }}" x-data>
                    @csrf
                    <button type="submit" 
                            x-show="sidebarOpen"
                            class="w-full flex items-center justify-center px-3 py-2 rounded-lg text-gray-300 hover:bg-gray-700 hover:text-white transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        Cerrar Sesión
                    </button>
                    <button type="submit" 
                            x-show="!sidebarOpen"
                            title="Cerrar Sesión"
                            class="w-full flex items-center justify-center px-3 py-2 rounded-lg text-gray-300 hover:bg-gray-700 hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                    </button>
                </form>
                
                <div x-show="sidebarOpen" class="text-xs text-gray-500 text-center mt-3">
                    EP v1.2.0.0
                </div>
            </div>
        </aside>
        
        <!-- Overlay móvil -->
        <div x-show="mobileMenuOpen" 
             @click="mobileMenuOpen = false"
             class="fixed inset-0 bg-black/50 z-30 lg:hidden"></div>
        
        <!-- Main Content -->
        <div class="flex-1 flex flex-col min-h-screen" :class="sidebarOpen ? 'lg:ml-0' : 'lg:ml-0'">
            <!-- Top Header -->
            <header class="h-16 bg-white shadow-sm flex items-center justify-between px-4 lg:px-8 sticky top-0 z-20">
                <!-- Mobile menu button -->
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="lg:hidden p-2 text-gray-600 hover:text-gray-900">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                
                <!-- Page Title -->
                <h1 class="text-xl font-semibold text-gray-800">
                    @yield('page-title', 'Checklists Dinámicos')
                </h1>
                
                <!-- Right side -->
                <div class="flex items-center space-x-4">
                    <!-- User info placeholder -->
                    <div class="flex items-center space-x-2">
                        <div class="w-8 h-8 bg-ep-primary rounded-full flex items-center justify-center text-white text-sm font-medium">
                            U
                        </div>
                    </div>
                </div>
            </header>
            
            <!-- Main Content Area -->
            <main class="flex-1 p-4 lg:p-8">
                <!-- Flash Messages -->
                @if (session()->has('message'))
                    <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg flex items-center justify-between"
                         x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)">
                        <span>{{ session('message') }}</span>
                        <button @click="show = false" class="text-green-700 hover:text-green-900">&times;</button>
                    </div>
                @endif

                {{ $slot }}
            </main>
            
            <!-- Footer -->
            <footer class="bg-white border-t border-gray-200 py-4 px-8">
                <div class="text-center text-sm text-gray-500">
                    EP - Sistema de Checklists Dinámicos © {{ date('Y') }}
                </div>
            </footer>
        </div>
    </div>

    @livewireScripts
    @stack('scripts')
</body>
</html>
