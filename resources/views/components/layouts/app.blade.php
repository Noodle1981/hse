<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Sistema XamaSafe' }}</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @php
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $consultora = $user?->consultora;
        
        $sede = $user?->sedes()->first();
        $empresa = $sede ? $sede->empresa : null;
        
        $primaryColor = $empresa->primary_color ?? '#F69239';
        $logoPath = $user?->isSuperAdmin() ? null : ($consultora?->logo_path ?? null);
        $brandingLogo = $logoPath ? asset('storage/' . $logoPath) : asset('images/logo.png');
        $brandingName = $user?->isSuperAdmin() ? 'Panel Súper Usuario' : ($consultora?->nombre ?? 'EP - EQUIPO PREVENTIVO');
    @endphp

    <style>
        :root {
            --ep-primary-color: {{ $primaryColor }};
        }
    </style>
    
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

        /* Global Orange Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #11111D;
        }
        ::-webkit-scrollbar-thumb {
            background: #F69239;
            border-radius: 20px;
            border: 2px solid #11111D;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #FF9F4D;
        }
        
        * {
            scrollbar-width: thin;
            scrollbar-color: #F69239 #11111D;
        }

        /* Tactical Horizontal Scrollbar */
        .custom-scrollbar-horizontal::-webkit-scrollbar {
            height: 6px;
        }
        .custom-scrollbar-horizontal::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.02);
            border-radius: 10px;
        }
        .custom-scrollbar-horizontal::-webkit-scrollbar-thumb {
            background: rgba(246, 146, 57, 0.2);
            border-radius: 10px;
        }
        .custom-scrollbar-horizontal::-webkit-scrollbar-thumb:hover {
            background: rgba(246, 146, 57, 0.5);
        }

    </style>
    
    @livewireStyles
</head>
<body class="bg-[#11111d] min-h-screen text-white" x-data="{ sidebarOpen: window.innerWidth >= 1024, mobileMenuOpen: false }">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside class="fixed left-0 top-0 h-screen bg-ep-sidebar sidebar-transition z-40 flex flex-col lg:relative lg:translate-x-0"
               :class="[
                   (sidebarOpen || mobileMenuOpen) ? 'w-64' : 'w-20',
                   mobileMenuOpen ? 'translate-x-0' : '-translate-x-full'
               ]">
            
            <!-- Brand/Logo -->
            <div class="h-16 flex items-center justify-between px-4 border-b border-gray-700 overflow-hidden">
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 group">
                    {{-- Logo dinámico con fallback --}}
                    <div class="w-10 h-10 bg-white p-1 rounded-lg flex items-center justify-center flex-shrink-0 shadow-lg border border-white/10 group-hover:scale-105 transition-transform duration-300">
                        <img src="{{ $brandingLogo }}?v={{ time() }}" alt="Logo" class="max-h-full w-auto object-contain">
                    </div>
                    
                    {{-- Marca Dual: Plataforma + Consultora --}}
                    <div x-show="sidebarOpen || mobileMenuOpen" class="flex flex-col transition-all duration-300">
                        <span class="text-white font-black uppercase italic tracking-tighter text-lg leading-none">XamaSafe</span>
                        <span class="text-yellow-400 font-bold uppercase italic tracking-[0.2em] text-[8px] mt-1 truncate max-w-[140px]" title="{{ $brandingName }}">
                            {{ $brandingName }}
                        </span>
                    </div>
                </a>
                <button @click="sidebarOpen = !sidebarOpen" class="hidden lg:block text-ep-primary hover:text-ep-primary-dark p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              :d="sidebarOpen ? 'M11 19l-7-7 7-7m8 14l-7-7 7-7' : 'M13 5l7 7-7 7M5 5l7 7-7 7'"/>
                    </svg>
                </button>
            </div>
            
            <!-- Menu -->
            <nav class="flex-1 overflow-y-auto sidebar-scroll py-2">
                <ul class="space-y-1 px-3">

                    <!-- Visitas (diferente para cada rol) -->
                    <!-- Mis Tareas (Solo Técnico) -->
                    @if(auth()->user()->isTechnician())
                    <li>
                        <a href="{{ route('tecnico.checklists.tasks.index') }}" 
                           class="menu-item flex items-center px-3 py-2 rounded-lg text-ep-text hover:text-ep-text-hover transition-colors
                                  {{ request()->routeIs('tecnico.checklists.tasks.*') ? 'menu-item-active text-white' : '' }}">
                            <span class="menu-icon mr-3 flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                </svg>
                            </span>
                            <span x-show="sidebarOpen || mobileMenuOpen" class="menu-text">Mis Tareas</span>
                        </a>
                    </li>
                    
                    <li>
                        <a href="{{ route('tecnico.visits.index') }}" 
                           class="menu-item flex items-center px-3 py-2 rounded-lg text-ep-text hover:text-ep-text-hover transition-colors
                                  {{ request()->routeIs('tecnico.visits.index') || request()->routeIs('tecnico.visits.in-progress') ? 'menu-item-active text-white' : '' }}">
                            <span class="menu-icon mr-3 flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </span>
                            <span x-show="sidebarOpen || mobileMenuOpen" class="menu-text">Mis Visitas</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('tecnico.hallazgos.index') }}" 
                           class="menu-item flex items-center px-3 py-2 rounded-lg text-ep-text hover:text-ep-text-hover transition-colors
                                  {{ request()->routeIs('tecnico.hallazgos.index') ? 'menu-item-active text-white' : '' }}">
                            <span class="menu-icon mr-3 flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </span>
                            <span x-show="sidebarOpen || mobileMenuOpen" class="menu-text">Mis Hallazgos</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('tecnico.incidentes.index') }}" 
                           class="menu-item flex items-center px-3 py-2 rounded-lg text-ep-text hover:text-ep-text-hover transition-colors
                                  {{ request()->routeIs('tecnico.incidentes.index') ? 'menu-item-active text-white' : '' }}">
                            <span class="menu-icon mr-3 flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </span>
                            <span x-show="sidebarOpen || mobileMenuOpen" class="menu-text">Mis Incidentes</span>
                        </a>
                    </li>
                    @endif
                    @if(auth()->user()->isAdmin())
                    <li>
                        <a href="{{ route('gestor.visits.history') }}" 
                           class="menu-item flex items-center px-3 py-2 rounded-lg text-ep-text hover:text-ep-text-hover transition-colors
                                  {{ request()->routeIs('gestor.visits.history') || request()->routeIs('gestor.visits.in-progress') ? 'menu-item-active text-white' : '' }}">
                            <span class="menu-icon mr-3 flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </span>
                            <span x-show="sidebarOpen || mobileMenuOpen" class="menu-text">Historial de Visitas</span>
                        </a>
                    </li>
                    @endif



                    <!-- Plantillas de Checklist (Solo Admin) -->
                    @if(auth()->user()->isAdmin())
                    <li>
                        <a href="{{ route('gestor.checklists.index') }}" 
                           class="menu-item flex items-center px-3 py-2 rounded-lg text-ep-text hover:text-ep-text-hover transition-colors
                                  {{ request()->routeIs('gestor.checklists.*') || request()->routeIs('gestor.categories.*') ? 'menu-item-active text-white' : '' }}">
                            <span class="menu-icon mr-3 flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </span>
                            <span x-show="sidebarOpen || mobileMenuOpen" class="menu-text">Plantillas de Checklist</span>
                        </a>
                    </li>
                    @endif

                    <!-- Capacitaciones - Solo Gestores -->
                    @if(auth()->user()->isAdmin())
                    <li>
                        <a href="{{ route('gestor.trainings.index') }}" 
                           class="menu-item flex items-center px-3 py-2 rounded-lg text-ep-text hover:text-ep-text-hover transition-colors
                                  {{ request()->routeIs('gestor.trainings.*') ? 'menu-item-active text-white' : '' }}">
                            <span class="menu-icon mr-3 flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                                </svg>
                            </span>
                            <span x-show="sidebarOpen || mobileMenuOpen" class="menu-text">Capacitaciones</span>
                        </a>
                    </li>
                    @endif


                {{-- Inspecciones (Asignaciones) - Solo Gestor --}}
                    @if(auth()->user()->isAdmin())
                    <li>
                        <a href="{{ route('gestor.assignments.index') }}" 
                           class="menu-item flex items-center px-3 py-2 rounded-lg text-ep-text hover:text-ep-text-hover transition-colors
                                  {{ request()->routeIs('gestor.assignments.*') || request()->routeIs('gestor.inspections.*') ? 'menu-item-active text-white' : '' }}">
                            <span class="menu-icon mr-3 flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                </svg>
                            </span>
                            <span x-show="sidebarOpen || mobileMenuOpen" class="menu-text">Inspecciones</span>
                        </a>
                    </li>
                    @endif

                    @if(auth()->user()->isAdmin())
                    <!-- Hallazgos (Solo Admin) -->
                    <li>
                        <a href="{{ route('gestor.hallazgos.index') }}" 
                           class="menu-item flex items-center px-3 py-2 rounded-lg text-ep-text hover:text-ep-text-hover transition-colors
                                  {{ request()->routeIs('gestor.hallazgos.*') ? 'menu-item-active text-white' : '' }}">
                            <span class="menu-icon mr-3 flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                            </span>
                            <span x-show="sidebarOpen || mobileMenuOpen" class="menu-text">Hallazgos</span>
                        </a>
                    </li>
                    
                    <!-- Incidentes (Solo Admin) -->
                    <li>
                        <a href="{{ route('gestor.incidentes.index') }}" 
                           class="menu-item flex items-center px-3 py-2 rounded-lg text-ep-text hover:text-ep-text-hover transition-colors
                                  {{ request()->routeIs('gestor.incidentes.*') ? 'menu-item-active text-white' : '' }}">
                            <span class="menu-icon mr-3 flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </span>
                            <span x-show="sidebarOpen || mobileMenuOpen" class="menu-text">Incidentes</span>
                        </a>
                    </li>
                    @endif

                    <li>
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('gestor.master-reports.index') }}" 
                               class="menu-item flex items-center px-3 py-2 rounded-lg text-ep-text hover:text-ep-text-hover transition-colors
                                      {{ request()->routeIs('gestor.master-reports.*') ? 'menu-item-active text-white' : '' }}">
                        @else
                            <a href="{{ route('tecnico.master-reports.index') }}" 
                               class="menu-item flex items-center px-3 py-2 rounded-lg text-ep-text hover:text-ep-text-hover transition-colors
                                      {{ request()->routeIs('tecnico.master-reports.*') ? 'menu-item-active text-white' : '' }}">
                        @endif
                            <span class="menu-icon mr-3 flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </span>
                            <span x-show="sidebarOpen || mobileMenuOpen" class="menu-text">Informe Maestro</span>
                        </a>
                    </li>





                    @if(auth()->user()->isAdmin())
                    <li>
                        <a href="{{ route('gestor.annual-contract.index') }}" 
                           class="menu-item flex items-center px-3 py-2 rounded-lg text-ep-text hover:text-ep-text-hover transition-colors
                                  {{ request()->routeIs('gestor.annual-contract.*') ? 'menu-item-active text-white' : '' }}">
                            <span class="menu-icon mr-3 flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                            </span>
                            <span x-show="sidebarOpen || mobileMenuOpen" class="menu-text">Contrato Anual</span>
                        </a>
                    </li>
                    @endif
                    @if(auth()->user()->isTechnician())
                    <li>
                        <a href="{{ route('tecnico.annual-contract.index') }}" 
                           class="menu-item flex items-center px-3 py-2 rounded-lg text-ep-text hover:text-ep-text-hover transition-colors
                                  {{ request()->routeIs('tecnico.annual-contract.*') ? 'menu-item-active text-white' : '' }}">
                            <span class="menu-icon mr-3 flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                            </span>
                            <span x-show="sidebarOpen || mobileMenuOpen" class="menu-text">Contrato Anual</span>
                        </a>
                    </li>
                    @endif

                    
                    @if(auth()->user()->isAdmin())
                    <!-- Administración Hub (Solo Admin) -->
                    <li>
                        <a href="{{ route('gestor.administration.hub') }}" 
                           class="menu-item flex items-center px-3 py-2 rounded-lg text-ep-text hover:text-ep-text-hover transition-colors
                                  {{ request()->routeIs('gestor.administration.hub') || request()->routeIs('gestor.users.*') || request()->routeIs('gestor.technicians.*') || request()->routeIs('gestor.companies.*') ? 'menu-item-active text-white' : '' }}">
                            <span class="menu-icon mr-3 flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </span>
                            <span x-show="sidebarOpen || mobileMenuOpen" class="menu-text">Administración</span>
                        </a>
                    </li>

                    @endif




                    




                    
                    <!-- Dashboard/Estadísticas (Solo para Gestores/Admin) -->
                    @if(!auth()->user()->isTechnician())
                    <li>
                        <a href="{{ route('dashboard') }}" 
                           class="menu-item flex items-center px-3 py-2 rounded-lg text-ep-text hover:text-ep-text-hover transition-colors
                                  {{ request()->routeIs('dashboard') ? 'menu-item-active text-white' : '' }}">
                            <span class="menu-icon mr-3 flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                            </span>
                            <span x-show="sidebarOpen || mobileMenuOpen" class="menu-text">Dashboard</span>
                        </a>
                    </li>
                    @endif

                    <!-- Mi Perfil -->
                    <li>
                        <a href="{{ route('profile') }}" 
                           class="menu-item flex items-center px-3 py-2 rounded-lg text-ep-text hover:text-ep-text-hover transition-colors
                                  {{ request()->routeIs('profile') ? 'menu-item-active text-white' : '' }}">
                            <span class="menu-icon mr-3 flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </span>
                            <span x-show="sidebarOpen || mobileMenuOpen" class="menu-text">Mi Perfil</span>
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
                            x-show="sidebarOpen || mobileMenuOpen"
                            class="w-full flex items-center justify-center px-3 py-2 rounded-lg text-gray-300 hover:bg-gray-700 hover:text-white transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        Cerrar Sesión
                    </button>
                    <button type="submit" 
                            x-show="!(sidebarOpen || mobileMenuOpen)"
                            title="Cerrar Sesión"
                            class="w-full flex items-center justify-center px-3 py-2 rounded-lg text-gray-300 hover:bg-gray-700 hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                    </button>
                </form>
                
                <div x-show="sidebarOpen || mobileMenuOpen" class="text-xs text-gray-500 text-center mt-3">
                    XamaSafe v1.0.0
                </div>
            </div>
        </aside>
        
        <!-- Overlay móvil -->
        <div x-show="mobileMenuOpen" 
             @click="mobileMenuOpen = false"
             class="fixed inset-0 bg-black/50 z-30 lg:hidden"></div>
        
        <!-- Main Content -->
        <div class="flex-1 flex flex-col h-screen overflow-hidden bg-ep-dark">
            <!-- Top Header (Customizable) -->
            @if(View::hasSection('header_custom'))
                @yield('header_custom')
            @else
                <header class="h-16 bg-ep-dark/80 backdrop-blur-md shadow-lg border-b border-white/5 flex items-center justify-between px-3 sm:px-6 lg:px-8 flex-shrink-0 z-20">
                    <!-- Mobile menu button -->
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="lg:hidden p-2 text-gray-600 hover:text-gray-900">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                    
                    <!-- Page Title -->
                    <h1 class="text-sm sm:text-xl font-bold text-white tracking-tight uppercase italic truncate max-w-[150px] sm:max-w-none">
                        {{ $title ?? 'XamaSafe' }}
                    </h1>
                    
                    <!-- Right side -->
                    <div class="flex items-center space-x-3 md:space-x-6">
                        <!-- Role Switcher -->
                        @if(auth()->user()->rol === \App\Models\User::ROLE_GESTOR || auth()->user()->rol === \App\Models\User::ROLE_SUPER_ADMIN)
                            <div class="flex items-center">
                                @if(session('active_role') === \App\Models\User::ROLE_TECNICO)
                                    <a href="{{ route('switch-role', \App\Models\User::ROLE_GESTOR) }}" 
                                       class="bg-ep-primary text-white px-3 py-1.5 rounded-full text-xs font-bold shadow-md hover:brightness-110 transition-all flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
                                        </svg>
                                        <span class="hidden xs:inline">Modo</span> Gestor
                                    </a>
                                @else
                                    <a href="{{ route('switch-role', \App\Models\User::ROLE_TECNICO) }}" 
                                       class="bg-gray-200 text-gray-700 px-3 py-1.5 rounded-full text-xs font-bold shadow-sm hover:bg-gray-300 transition-all flex items-center gap-1 border border-gray-300">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 5l7 7-7 7M5 5l7 7-7 7"/>
                                        </svg>
                                        <span class="hidden xs:inline">Modo</span> Técnico
                                    </a>
                                @endif
                            </div>
                        @endif

                        <!-- User info -->
                        <a href="{{ route('profile') }}" class="flex items-center space-x-2 hover:opacity-80 transition-opacity">
                            <div class="hidden sm:flex flex-col items-end mr-1">
                                <span class="text-xs font-black text-white uppercase italic tracking-wide">
                                    {{ auth()->user()->nombres }}
                                </span>
                                <span class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">
                                    {{ auth()->user()->rol === 'super_admin' ? 'Súper Admin' : (session('active_role') === 'tecnico' ? 'Modo Técnico' : 'Gestor') }}
                                </span>
                            </div>
                            <div class="w-8 h-8 sm:w-9 sm:h-9 bg-ep-primary rounded-full flex items-center justify-center text-white text-xs sm:text-sm font-black shadow-sm ring-2 ring-white/10">
                                {{ substr(auth()->user()->nombres ?? 'U', 0, 1) }}
                            </div>
                        </a>
                    </div>
                </header>
            @endif
            
            <!-- Main Content Area with Scroll -->
            <main class="flex-1 overflow-y-auto bg-[#11111d]">
                {{ $slot }}
            </main>
        </div>
    </div>

    @livewireScripts
    @stack('scripts')
</body>
</html>

