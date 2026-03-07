<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Sistema XamaSafe' }}</title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    @php
        $user = auth()->user();
        $empresa = null;
        if ($user) {
            $sede = $user->sedes()->first();
            $empresa = $sede ? $sede->empresa : null;
        }
        $primaryColor = $empresa->primary_color ?? '#F69239';
        $logoPath = $empresa->logo_path ?? null;
    @endphp

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'ep-primary': '{{ $primaryColor }}',
                        'ep-primary-dark': '{{ $primaryColor }}', // Simplificado
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
        
        /* Custom scrollbar - Mejorado para mejor visibilidad */
        .sidebar-scroll::-webkit-scrollbar {
            width: 8px;
        }
        .sidebar-scroll::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 4px;
        }
        .sidebar-scroll::-webkit-scrollbar-thumb {
            background: rgba(246, 146, 57, 0.4);
            border-radius: 4px;
            transition: background 0.2s;
        }
        .sidebar-scroll::-webkit-scrollbar-thumb:hover {
            background: rgba(246, 146, 57, 0.7);
        }
        
        /* Firefox scrollbar */
        .sidebar-scroll {
            scrollbar-width: thin;
            scrollbar-color: rgba(246, 146, 57, 0.4) rgba(255, 255, 255, 0.05);
        }
    </style>
    
    @livewireStyles
</head>
<body class="bg-gray-100 min-h-screen" x-data="{ sidebarOpen: window.innerWidth >= 1024, mobileMenuOpen: false }">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside :class="sidebarOpen ? 'w-64' : 'w-20'" 
               class="fixed left-0 top-0 h-screen bg-ep-sidebar sidebar-transition z-40 flex flex-col
                      lg:relative lg:translate-x-0"
               :class="mobileMenuOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">
            
            <!-- Brand/Logo -->
            <div class="h-16 flex items-center justify-between px-4 border-b border-gray-700">
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-3">
                    @if($logoPath)
                        <img src="{{ asset('storage/' . $logoPath) }}" class="h-10 w-auto">
                    @else
                        <div class="w-10 h-10 bg-ep-primary rounded-lg flex items-center justify-center font-bold text-white text-lg flex-shrink-0">
                            EP
                        </div>
                    @endif
                    <span x-show="sidebarOpen" class="brand-text text-white font-semibold text-lg transition-opacity">
                        XamaSafe
                    </span>
                </a>
                <button @click="sidebarOpen = !sidebarOpen" class="hidden lg:block text-ep-primary hover:text-ep-primary-dark p-1">
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
                    <!-- Plantillas (Solo Admin) -->

                    

                    <!-- Visitas (diferente para cada rol) -->
                    <!-- Mis Tareas (Solo Técnico) -->
                    @if(auth()->user()->isTechnician())
                    <li>
                        <a href="{{ route('tecnico.checklists.tasks.index') }}" 
                           class="menu-item flex items-center px-3 py-3 rounded-lg text-ep-text hover:text-ep-text-hover transition-colors
                                  {{ request()->routeIs('tecnico.checklists.tasks.*') ? 'menu-item-active text-white' : '' }}">
                            <span class="menu-icon mr-3 flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                </svg>
                            </span>
                            <span x-show="sidebarOpen" class="menu-text">Mis Tareas</span>
                        </a>
                    </li>
                    
                    <li>
                        <a href="{{ route('tecnico.visits.index') }}" 
                           class="menu-item flex items-center px-3 py-3 rounded-lg text-ep-text hover:text-ep-text-hover transition-colors
                                  {{ request()->routeIs('tecnico.visits.index') || request()->routeIs('tecnico.visits.in-progress') ? 'menu-item-active text-white' : '' }}">
                            <span class="menu-icon mr-3 flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </span>
                            <span x-show="sidebarOpen" class="menu-text">Mis Visitas</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('tecnico.hallazgos.index') }}" 
                           class="menu-item flex items-center px-3 py-3 rounded-lg text-ep-text hover:text-ep-text-hover transition-colors
                                  {{ request()->routeIs('tecnico.hallazgos.index') ? 'menu-item-active text-white' : '' }}">
                            <span class="menu-icon mr-3 flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </span>
                            <span x-show="sidebarOpen" class="menu-text">Mis Hallazgos</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('tecnico.incidentes.index') }}" 
                           class="menu-item flex items-center px-3 py-3 rounded-lg text-ep-text hover:text-ep-text-hover transition-colors
                                  {{ request()->routeIs('tecnico.incidentes.index') ? 'menu-item-active text-white' : '' }}">
                            <span class="menu-icon mr-3 flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </span>
                            <span x-show="sidebarOpen" class="menu-text">Mis Incidentes</span>
                        </a>
                    </li>
                    @endif
                    @if(auth()->user()->isAdmin())
                    <li>
                        <a href="{{ route('gestor.visits.history') }}" 
                           class="menu-item flex items-center px-3 py-3 rounded-lg text-ep-text hover:text-ep-text-hover transition-colors
                                  {{ request()->routeIs('gestor.visits.history') || request()->routeIs('gestor.visits.in-progress') ? 'menu-item-active text-white' : '' }}">
                            <span class="menu-icon mr-3 flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </span>
                            <span x-show="sidebarOpen" class="menu-text">Historial de Visitas</span>
                        </a>
                    </li>
                    @endif



                    <!-- Plantillas de Checklist (Solo Admin) -->
                    @if(auth()->user()->isAdmin())
                    <li>
                        <a href="{{ route('gestor.checklists.index') }}" 
                           class="menu-item flex items-center px-3 py-3 rounded-lg text-ep-text hover:text-ep-text-hover transition-colors
                                  {{ request()->routeIs('gestor.checklists.*') || request()->routeIs('gestor.categories.*') ? 'menu-item-active text-white' : '' }}">
                            <span class="menu-icon mr-3 flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </span>
                            <span x-show="sidebarOpen" class="menu-text">Plantillas de Checklist</span>
                        </a>
                    </li>
                    @endif

                    <!-- Capacitaciones - Solo Gestores -->
                    @if(auth()->user()->isAdmin())
                    <li>
                        <a href="{{ route('gestor.trainings.index') }}" 
                           class="menu-item flex items-center px-3 py-3 rounded-lg text-ep-text hover:text-ep-text-hover transition-colors
                                  {{ request()->routeIs('gestor.trainings.*') ? 'menu-item-active text-white' : '' }}">
                            <span class="menu-icon mr-3 flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                                </svg>
                            </span>
                            <span x-show="sidebarOpen" class="menu-text">Capacitaciones</span>
                        </a>
                    </li>
                    @endif


                {{-- Inspecciones (Asignaciones) - Solo Gestor --}}
                    @if(auth()->user()->isAdmin())
                    <li>
                        <a href="{{ route('gestor.assignments.index') }}" 
                           class="menu-item flex items-center px-3 py-3 rounded-lg text-ep-text hover:text-ep-text-hover transition-colors
                                  {{ request()->routeIs('gestor.assignments.*') || request()->routeIs('gestor.inspections.*') ? 'menu-item-active text-white' : '' }}">
                            <span class="menu-icon mr-3 flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                </svg>
                            </span>
                            <span x-show="sidebarOpen" class="menu-text">Inspecciones</span>
                        </a>
                    </li>
                    @endif

                    @if(auth()->user()->isAdmin())
                    <!-- Hallazgos (Solo Admin) -->
                    <li>
                        <a href="{{ route('gestor.hallazgos.index') }}" 
                           class="menu-item flex items-center px-3 py-3 rounded-lg text-ep-text hover:text-ep-text-hover transition-colors
                                  {{ request()->routeIs('gestor.hallazgos.*') ? 'menu-item-active text-white' : '' }}">
                            <span class="menu-icon mr-3 flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                            </span>
                            <span x-show="sidebarOpen" class="menu-text">Hallazgos</span>
                        </a>
                    </li>
                    
                    <!-- Incidentes (Solo Admin) -->
                    <li>
                        <a href="{{ route('gestor.incidentes.index') }}" 
                           class="menu-item flex items-center px-3 py-3 rounded-lg text-ep-text hover:text-ep-text-hover transition-colors
                                  {{ request()->routeIs('gestor.incidentes.*') ? 'menu-item-active text-white' : '' }}">
                            <span class="menu-icon mr-3 flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </span>
                            <span x-show="sidebarOpen" class="menu-text">Incidentes</span>
                        </a>
                    </li>
                    @endif

                    <li>
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('gestor.master-reports.index') }}" 
                               class="menu-item flex items-center px-3 py-3 rounded-lg text-ep-text hover:text-ep-text-hover transition-colors
                                      {{ request()->routeIs('gestor.master-reports.*') ? 'menu-item-active text-white' : '' }}">
                        @else
                            <a href="{{ route('tecnico.master-reports.index') }}" 
                               class="menu-item flex items-center px-3 py-3 rounded-lg text-ep-text hover:text-ep-text-hover transition-colors
                                      {{ request()->routeIs('tecnico.master-reports.*') ? 'menu-item-active text-white' : '' }}">
                        @endif
                            <span class="menu-icon mr-3 flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </span>
                            <span x-show="sidebarOpen" class="menu-text">Informe Maestro</span>
                        </a>
                    </li>





                    @if(auth()->user()->isAdmin())
                    <li>
                        <a href="{{ route('gestor.annual-contract.index') }}" 
                           class="menu-item flex items-center px-3 py-3 rounded-lg text-ep-text hover:text-ep-text-hover transition-colors
                                  {{ request()->routeIs('gestor.annual-contract.*') ? 'menu-item-active text-white' : '' }}">
                            <span class="menu-icon mr-3 flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                            </span>
                            <span x-show="sidebarOpen" class="menu-text">Contrato Anual</span>
                        </a>
                    </li>
                    @endif
                    @if(auth()->user()->isTechnician())
                    <li>
                        <a href="{{ route('tecnico.annual-contract.index') }}" 
                           class="menu-item flex items-center px-3 py-3 rounded-lg text-ep-text hover:text-ep-text-hover transition-colors
                                  {{ request()->routeIs('tecnico.annual-contract.*') ? 'menu-item-active text-white' : '' }}">
                            <span class="menu-icon mr-3 flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                            </span>
                            <span x-show="sidebarOpen" class="menu-text">Contrato Anual</span>
                        </a>
                    </li>
                    @endif

                    
                    @if(auth()->user()->isAdmin())
                    <!-- Administración Hub (Solo Admin) -->
                    <li>
                        <a href="{{ route('gestor.administration.hub') }}" 
                           class="menu-item flex items-center px-3 py-3 rounded-lg text-ep-text hover:text-ep-text-hover transition-colors
                                  {{ request()->routeIs('gestor.administration.hub') || request()->routeIs('gestor.users.*') || request()->routeIs('gestor.technicians.*') || request()->routeIs('gestor.companies.*') ? 'menu-item-active text-white' : '' }}">
                            <span class="menu-icon mr-3 flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </span>
                            <span x-show="sidebarOpen" class="menu-text">Administración</span>
                        </a>
                    </li>

                    @endif




                    




                    
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
                    XamaSafe v1.0.0
                </div>
            </div>
        </aside>
        
        <!-- Overlay móvil -->
        <div x-show="mobileMenuOpen" 
             @click="mobileMenuOpen = false"
             class="fixed inset-0 bg-black/50 z-30 lg:hidden"></div>
        
        <!-- Main Content -->
        <div class="flex-1 flex flex-col h-screen overflow-hidden">
            <!-- Top Header -->
            <header class="h-16 bg-white shadow-sm flex items-center justify-between px-4 lg:px-8 flex-shrink-0 z-20">
                <!-- Mobile menu button -->
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="lg:hidden p-2 text-gray-600 hover:text-gray-900">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                
                <!-- Page Title -->
                <h1 class="text-xl font-semibold text-gray-800">
                    {{ $title ?? 'XamaSafe' }}
                </h1>
                
                <!-- Right side -->
                <div class="flex items-center space-x-4">
                    <!-- User info -->
                    <div class="flex items-center space-x-2">
                        <div class="w-8 h-8 bg-ep-primary rounded-full flex items-center justify-center text-white text-sm font-medium">
                            {{ substr(auth()->user()->nombres ?? 'U', 0, 1) }}
                        </div>
                        <span class="hidden md:block text-sm text-gray-700">
                            {{ auth()->user()->nombres ?? 'Usuario' }}
                        </span>
                    </div>
                </div>
            </header>
            
            <!-- Main Content Area with Scroll -->
            <main class="flex-1 overflow-y-auto bg-gray-100">
                <div class="p-6">
                    {{ $slot }}
                </div>
                
                <!-- Footer -->
                <footer class="bg-white border-t border-gray-200 py-4 px-8">
                    <div class="text-center text-sm text-gray-500">
                        XamaSafe © {{ date('Y') }}
                    </div>
                </footer>
            </main>
        </div>
    </div>

    @livewireScripts
    @stack('scripts')
</body>
</html>

