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
<body class="bg-gray-100 min-h-screen" x-data="{ sidebarOpen: true, mobileMenuOpen: false }">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside :class="sidebarOpen ? 'w-64' : 'w-20'" 
               class="fixed left-0 top-0 h-screen bg-ep-sidebar sidebar-transition z-40 flex flex-col
                      lg:relative lg:translate-x-0"
               :class="mobileMenuOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">
            
            <!-- Brand/Logo -->
            <div class="h-16 flex items-center justify-between px-4 border-b border-gray-700">
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-3">
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
                    <!-- Plantillas (Solo Admin) -->
                    @if(auth()->user()->rol === 'Administrador')
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
                    @endif
                    
                    <!-- Mis Tareas (Solo Técnico) -->
                    @if(auth()->user()->rol === 'Técnico')
                    <li>
                        <a href="{{ route('tasks.index') }}" 
                           class="menu-item flex items-center px-3 py-3 rounded-lg text-ep-text hover:text-ep-text-hover transition-colors
                                  {{ request()->routeIs('tasks.*') ? 'menu-item-active text-white' : '' }}">
                            <span class="menu-icon mr-3 flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                </svg>
                            </span>
                            <span x-show="sidebarOpen" class="menu-text">Mis Tareas</span>
                        </a>
                    </li>
                    @endif

                    <!-- Asignaciones (Solo Admin - Acceso rápido) -->
                    @if(auth()->user()->rol === 'Administrador')
                    <li>
                        <a href="{{ route('assignments.index') }}" 
                           class="menu-item flex items-center px-3 py-3 rounded-lg text-ep-text hover:text-ep-text-hover transition-colors
                                  {{ request()->routeIs('assignments.*') ? 'menu-item-active text-white' : '' }}">
                            <span class="menu-icon mr-3 flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                </svg>
                            </span>
                            <span x-show="sidebarOpen" class="menu-text">Asignaciones</span>
                        </a>
                    </li>
                    @endif

                    <!-- Capacitaciones (Solo Admin) -->
                    @if(auth()->user()->rol === 'Administrador')
                    <li>
                        <a href="{{ route('trainings.index') }}" 
                           class="menu-item flex items-center px-3 py-3 rounded-lg text-ep-text hover:text-ep-text-hover transition-colors
                                  {{ request()->routeIs('trainings.*') ? 'menu-item-active text-white' : '' }}">
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

                    <!-- Matrices de Riesgo (Admin y Técnico) -->
                    <li>
                        <a href="{{ route('risk-matrices.index') }}" 
                           class="menu-item flex items-center px-3 py-3 rounded-lg text-ep-text hover:text-ep-text-hover transition-colors
                                  {{ request()->routeIs('risk-matrices.index') || request()->routeIs('risk-matrices.editor') || request()->routeIs('risk-matrices.evaluator') ? 'menu-item-active text-white' : '' }}">
                            <span class="menu-icon mr-3 flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"/>
                                </svg>
                            </span>
                            <span x-show="sidebarOpen" class="menu-text">Matrices de Riesgo</span>
                        </a>
                    </li>
                    {{-- Mapa de Calor Global (Solo Admin) --}}`r`n                    @if(auth()->user()->rol === 'Administrador')
                    <li>
                        <a href="{{ route('risk-matrices.dashboard') }}" 
                           class="menu-item flex items-center px-3 py-3 rounded-lg text-ep-text hover:text-ep-text-hover transition-colors
                                  {{ request()->routeIs('risk-matrices.dashboard') ? 'menu-item-active text-white' : '' }}">
                            <span class="menu-icon mr-3 flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2-2v12a2 2 0 002 2z"/>
                                </svg>
                            </span>
                            <span x-show="sidebarOpen" class="menu-text">Mapa de Calor Global</span>
                        </a>
                    </li>
                    @endif

                    <li class="mt-6">
                        <span x-show="sidebarOpen" class="px-3 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Ejecución y Control Anual</span>
                    </li>

                    <!-- Contrato Anual -->
                    <li>
                        <a href="{{ route('annual-contract.index') }}" 
                           class="menu-item flex items-center px-3 py-3 rounded-lg text-ep-text hover:text-ep-text-hover transition-colors
                                  {{ request()->routeIs('annual-contract.*') ? 'menu-item-active text-white' : '' }}">
                            <span class="menu-icon mr-3 flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"/>
                                </svg>
                            </span>
                            <span x-show="sidebarOpen" class="menu-text font-bold">Resumen Contrato Anual</span>
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
                    
                    <!-- Constancias de Visitas (Admin) -->
                    @if(auth()->user()->rol === 'Administrador')
                    <li>
                        <a href="{{ route('visit-certificates.index') }}" 
                           class="menu-item flex items-center px-3 py-3 rounded-lg text-ep-text hover:text-ep-text-hover transition-colors
                                  {{ request()->routeIs('visit-certificates.index') ? 'menu-item-active text-white' : '' }}">
                            <span class="menu-icon mr-3 flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </span>
                            <span x-show="sidebarOpen" class="menu-text">Constancias de Visitas</span>
                        </a>
                    </li>
                    
                    <!-- Hallazgos (Solo Admin) -->
                    <li>
                        <a href="{{ route('hallazgos.index') }}" 
                           class="menu-item flex items-center px-3 py-3 rounded-lg text-ep-text hover:text-ep-text-hover transition-colors
                                  {{ request()->routeIs('hallazgos.*') ? 'menu-item-active text-white' : '' }}">
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
                        <a href="{{ route('incidentes.index') }}" 
                           class="menu-item flex items-center px-3 py-3 rounded-lg text-ep-text hover:text-ep-text-hover transition-colors
                                  {{ request()->routeIs('incidentes.*') ? 'menu-item-active text-white' : '' }}">
                            <span class="menu-icon mr-3 flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </span>
                            <span x-show="sidebarOpen" class="menu-text">Incidentes</span>
                        </a>
                    </li>
                    @endif
                    
                    <!-- Separador Admin -->
                    @if(auth()->user()->rol === 'Administrador')
                        <li class="pt-4 pb-2" x-show="sidebarOpen">
                            <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider px-3">Administración</span>
                        </li>
                        
                        <!-- Usuarios -->
                        <li>
                            <a href="{{ route('users.index') }}" 
                               class="menu-item flex items-center px-3 py-3 rounded-lg text-ep-text hover:text-ep-text-hover transition-colors
                                      {{ request()->routeIs('users.*') ? 'menu-item-active text-white' : '' }}">
                                <span class="menu-icon mr-3 flex-shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                    </svg>
                                </span>
                                <span x-show="sidebarOpen" class="menu-text">Usuarios</span>
                            </a>
                        </li>

                        <!-- Técnicos -->
                        <li>
                            <a href="{{ route('technicians.index') }}" 
                               class="menu-item flex items-center px-3 py-3 rounded-lg text-ep-text hover:text-ep-text-hover transition-colors
                                      {{ request()->routeIs('technicians.*') ? 'menu-item-active text-white' : '' }}">
                                <span class="menu-icon mr-3 flex-shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </span>
                                <span x-show="sidebarOpen" class="menu-text">Técnicos</span>
                            </a>
                        </li>

                        <!-- Empresas -->
                        <li>
                            <a href="{{ route('companies.index') }}" 
                               class="menu-item flex items-center px-3 py-3 rounded-lg text-ep-text hover:text-ep-text-hover transition-colors
                                      {{ request()->routeIs('companies.*') ? 'menu-item-active text-white' : '' }}">
                                <span class="menu-icon mr-3 flex-shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                </span>
                                <span x-show="sidebarOpen" class="menu-text">Empresas</span>
                            </a>
                        </li>



                        <!-- EPP y Personal (Solo Admin) -->
                        <li class="pt-4 pb-2" x-show="sidebarOpen">
                            <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider px-3">EPP y Personal</span>
                        </li>
                        <li>
                            <a href="{{ route('epp.catalog') }}" 
                               class="menu-item flex items-center px-3 py-3 rounded-lg text-ep-text hover:text-ep-text-hover transition-colors
                                      {{ request()->routeIs('epp.catalog') ? 'menu-item-active text-white' : '' }}">
                                <span class="menu-icon mr-3 flex-shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                    </svg>
                                </span>
                                <span x-show="sidebarOpen" class="menu-text">Catálogo EPP</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('epp.employees.import') }}" 
                               class="menu-item flex items-center px-3 py-3 rounded-lg text-ep-text hover:text-ep-text-hover transition-colors
                                      {{ request()->routeIs('epp.employees.import') ? 'menu-item-active text-white' : '' }}">
                                <span class="menu-icon mr-3 flex-shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </span>
                                <span x-show="sidebarOpen" class="menu-text">Importar Personal</span>
                            </a>
                        </li>
                    @endif
                    
                    <!-- Entrega de EPP (Técnico y Admin) -->
                    <li class="pt-4 pb-2" x-show="sidebarOpen">
                        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider px-3">Gestión de Campo</span>
                    </li>
                    @if(auth()->user()->hasRole('Técnico') && !auth()->user()->hasRole('Administrador'))
                    <li>
                        <a href="{{ route('epp.deliver') }}" 
                           class="menu-item flex items-center px-3 py-3 rounded-lg text-ep-text hover:text-ep-text-hover transition-colors
                                  {{ request()->routeIs('epp.deliver') ? 'menu-item-active text-white' : '' }}">
                            <span class="menu-icon mr-3 flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </span>
                            <span x-show="sidebarOpen" class="menu-text">Entrega de EPP</span>
                        </a>
                    </li>
                    @endif
                    <li>
                        <a href="{{ route('epp.employees.index') }}" 
                           class="menu-item flex items-center px-3 py-3 rounded-lg text-ep-text hover:text-ep-text-hover transition-colors
                                  {{ request()->routeIs('epp.employees.index') ? 'menu-item-active text-white' : '' }}">
                            <span class="menu-icon mr-3 flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                            </span>
                            <span x-show="sidebarOpen" class="menu-text">Personal</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('epp.history') }}" 
                           class="menu-item flex items-center px-3 py-3 rounded-lg text-ep-text hover:text-ep-text-hover transition-colors
                                  {{ request()->routeIs('epp.history') ? 'menu-item-active text-white' : '' }}">
                            <span class="menu-icon mr-3 flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </span>
                            <span x-show="sidebarOpen" class="menu-text">Historial EPP</span>
                        </a>
                    </li>
                    
                    <!-- Mis Hallazgos (Técnico y Admin) -->
                    <li>
                        <a href="{{ route('mis-hallazgos') }}" 
                           class="menu-item flex items-center px-3 py-3 rounded-lg text-ep-text hover:text-ep-text-hover transition-colors
                                  {{ request()->routeIs('mis-hallazgos') ? 'menu-item-active text-white' : '' }}">
                            <span class="menu-icon mr-3 flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                            </span>
                            <span x-show="sidebarOpen" class="menu-text">Mis Hallazgos</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('visit-certificates.history') }}" 
                           class="menu-item flex items-center px-3 py-3 rounded-lg text-ep-text hover:text-ep-text-hover transition-colors
                                  {{ request()->routeIs('visit-certificates.*') ? 'menu-item-active text-white' : '' }}">
                            <span class="menu-icon mr-3 flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </span>
                            <span x-show="sidebarOpen" class="menu-text">Constancias de Visitas</span>
                        </a>
                    </li>

                    <!-- Separador Reportes -->
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
                    EP v2.0.0
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
                    {{ $title ?? 'EP - Sistema de Checklists' }}
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
            
            <!-- Main Content Area -->
            <main class="flex-1 p-6">
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

