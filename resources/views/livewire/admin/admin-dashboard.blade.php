<div class="min-h-screen bg-gray-50/50 pb-12">
    @section('header_custom')
        <header class="h-16 relative overflow-hidden bg-ep-dark flex items-center z-20 shadow-lg">
    
            <div class="relative w-full px-4 sm:px-6 lg:px-8 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <!-- Mobile menu button -->
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="lg:hidden p-2 text-indigo-200 hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
    
                    <h1 class="text-lg md:text-xl font-medium text-white tracking-tight">
                        Dashboard <span class="text-ep-primary italic">Administrativo</span>
                    </h1>
                    @if($alerts['overdue_assignments'] > 0)
                        <div class="flex items-center gap-1.5 px-2 py-0.5 bg-red-500/20 border border-red-500/30 rounded-full animate-pulse">
                            <span class="w-1.5 h-1.5 bg-red-500 rounded-full"></span>
                            <span class="text-[10px] font-black text-red-200 uppercase">{{ $alerts['overdue_assignments'] }} Alertas</span>
                        </div>
                    @endif
                </div>
    
                <div class="flex items-center space-x-3">
                    @if(auth()->user()->rol === \App\Models\User::ROLE_GESTOR || auth()->user()->rol === \App\Models\User::ROLE_SUPER_ADMIN)
                        <a href="{{ route('switch-role', \App\Models\User::ROLE_TECNICO) }}" 
                           class="hidden xs:flex bg-white/10 backdrop-blur-md text-indigo-100 hover:bg-white/20 px-3 py-1 rounded-full text-[10px] font-bold border border-white/10 transition-all items-center gap-1">
                            Modo Técnico
                        </a>
                    @endif
                    
                    <a href="{{ route('profile') }}" class="flex items-center space-x-2 bg-white/5 backdrop-blur-sm p-1 pr-3 rounded-full border border-white/5 hover:bg-white/10 transition-all">
                        <div class="w-7 h-7 bg-ep-primary rounded-full flex items-center justify-center text-white text-[10px] font-black shadow-lg">
                            {{ substr(auth()->user()->nombres ?? 'U', 0, 1) }}
                        </div>
                        <div class="h-4 w-px bg-white/10 mx-1"></div>
                        <span class="text-[10px] font-bold text-indigo-100">{{ auth()->user()->nombres }}</span>
                    </a>
                </div>
            </div>
        </header>
    @endsection

    {{-- Main Content - No offset --}}
    {{-- Main Content - No offset --}}
    <div class="max-w-9xl mx-auto px-4 sm:px-6 lg:px-8 pt-6 relative z-10">
        
        {{-- Dashboard Toolbar --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <h2 class="text-2xl font-black text-gray-900 tracking-tight">{{ $greeting }}</h2>
                <p class="text-sm text-gray-500 font-medium italic">Aquí tienes el resumen operativo de la consultora.</p>
            </div>
            
            <div class="flex flex-wrap items-center gap-3 bg-white p-2 rounded-2xl shadow-sm border border-gray-100">
                <div class="flex items-center gap-2 px-3 border-r border-gray-100">
                    <span class="text-[10px] font-black uppercase text-gray-400">Periodo:</span>
                    <select wire:model.live="period" class="border-none bg-transparent text-xs font-bold text-ep-primary focus:ring-0 cursor-pointer">
                        <option value="this_month">Este Mes</option>
                        <option value="last_3_months">Últimos 3 Meses</option>
                        <option value="this_year">Este Año</option>
                        <option value="custom">Personalizado</option>
                    </select>
                </div>
                
                @if($period === 'custom')
                <div class="flex items-center gap-2 px-3 min-w-[300px] animate-fade-in">
                    <input type="date" wire:model.live="dateFrom" class="text-[10px] font-bold border-gray-200 rounded-lg py-1">
                    <span class="text-gray-300">al</span>
                    <input type="date" wire:model.live="dateTo" class="text-[10px] font-bold border-gray-200 rounded-lg py-1">
                </div>
                @else
                <div class="px-3 text-[10px] font-bold text-gray-500">
                    {{ \Carbon\Carbon::parse($dateFrom)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($dateTo)->format('d/m/Y') }}
                </div>
                @endif
                
                <button wire:click="$refresh" class="p-2 hover:bg-gray-50 rounded-xl transition-colors text-gray-400 hover:text-ep-primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                </button>
            </div>
        </div>

        {{-- Row 1: Core Operations --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Inspecciones -->
            <div class="group bg-white rounded-3xl shadow-xl shadow-gray-200/50 p-6 border-b-4 border-green-500 hover:-translate-y-1 transition-all duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-green-50 text-green-600 rounded-2xl group-hover:bg-green-500 group-hover:text-white transition-colors duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Inspecciones</span>
                </div>
                <h3 class="text-sm font-bold text-gray-500 mb-1 leading-none">Total Completadas</h3>
                <div class="flex items-baseline gap-2">
                    <span class="text-4xl font-black text-gray-900 tracking-tight">{{ $kpis['inspections']['total_period'] }}</span>
                    <span class="text-xs font-bold text-green-500">~{{ $kpis['inspections']['avg_per_day'] }}/día</span>
                </div>
            </div>

            <!-- Capacitaciones -->
            <div class="group bg-white rounded-3xl shadow-xl shadow-gray-200/50 p-6 border-b-4 border-indigo-500 hover:-translate-y-1 transition-all duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-indigo-50 text-indigo-600 rounded-2xl group-hover:bg-indigo-500 group-hover:text-white transition-colors duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path></svg>
                    </div>
                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Capacitación</span>
                </div>
                <h3 class="text-sm font-bold text-gray-500 mb-1 leading-none">Asistentes Totales</h3>
                <div class="flex items-baseline gap-2">
                    <span class="text-4xl font-black text-gray-900 tracking-tight">{{ $kpis['trainings']['attendees_period'] }}</span>
                    <span class="text-xs font-bold text-indigo-500">{{ $kpis['trainings']['total_scheduled'] }} sesiones</span>
                </div>
            </div>

            <!-- Constancias -->
            <div class="group bg-white rounded-3xl shadow-xl shadow-gray-200/50 p-6 border-b-4 border-ep-primary hover:-translate-y-1 transition-all duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-orange-50 text-ep-primary rounded-2xl group-hover:bg-ep-primary group-hover:text-white transition-colors duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Visitas</span>
                </div>
                <h3 class="text-sm font-bold text-gray-500 mb-1 leading-none">Constancias</h3>
                <div class="flex items-baseline gap-2">
                    <span class="text-4xl font-black text-gray-900 tracking-tight">{{ $kpis['visit_certificates']['total_period'] }}</span>
                    <span class="text-xs font-bold text-orange-500">{{ $kpis['visit_certificates']['pending_signature'] }} pendientes</span>
                </div>
            </div>

            <!-- Ecosistema -->
            <div class="group bg-white rounded-3xl shadow-xl shadow-gray-200/50 p-6 border-b-4 border-gray-800 hover:-translate-y-1 transition-all duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-gray-50 text-gray-800 rounded-2xl group-hover:bg-gray-800 group-hover:text-white transition-colors duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Ecosistema</span>
                </div>
                <h3 class="text-sm font-bold text-gray-500 mb-1 leading-none">Empresas Activas</h3>
                <div class="flex items-baseline gap-2">
                    <span class="text-4xl font-black text-gray-900 tracking-tight">{{ $kpis['ecosystem']['total_companies'] }}</span>
                    <span class="text-xs font-bold text-gray-500">{{ $kpis['ecosystem']['active_technicians'] }} técnicos</span>
                </div>
            </div>
        </div>

        {{-- Row 2: Risk Management (Findings & Incidents) --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
            <!-- Hallazgos Card -->
            <div class="col-span-1 md:col-span-2 bg-white rounded-3xl shadow-xl shadow-gray-200/50 overflow-hidden border border-gray-100 italic">
                <div class="px-6 py-4 bg-red-50/50 border-b border-red-100 flex items-center justify-between">
                    <h3 class="text-xs font-black text-red-900 uppercase tracking-widest flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        Gestión de Hallazgos
                    </h3>
                    @if($kpis['findings']['critical'] > 0)
                        <span class="px-2 py-1 bg-red-500 text-white text-[10px] font-black rounded-lg animate-pulse uppercase">¡{{ $kpis['findings']['critical'] }} Críticos!</span>
                    @endif
                </div>
                <div class="p-6 grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="text-center">
                        <span class="block text-2xl font-black text-gray-900">{{ $kpis['findings']['total'] }}</span>
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Detectados</span>
                    </div>
                    <div class="text-center">
                        <span class="block text-2xl font-black text-red-600">{{ $kpis['findings']['pending'] }}</span>
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Abiertos</span>
                    </div>
                    <div class="text-center">
                        <span class="block text-2xl font-black text-green-600">{{ $kpis['findings']['resolved'] }}</span>
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Resueltos</span>
                    </div>
                    <div class="text-center">
                        <span class="block text-2xl font-black text-ep-primary">
                            {{ $kpis['findings']['total'] > 0 ? round(($kpis['findings']['resolved'] / $kpis['findings']['total']) * 100) : 0 }}%
                        </span>
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Eficacia</span>
                    </div>
                </div>
            </div>

            <!-- Incidentes Card -->
            <div class="bg-indigo-900 rounded-3xl shadow-xl p-6 text-white relative overflow-hidden group">
                <div class="absolute -right-4 -top-4 w-32 h-32 bg-white/10 rounded-full blur-3xl group-hover:scale-125 transition-transform duration-700"></div>
                <div class="flex items-center justify-between mb-6 relative">
                    <div class="p-3 bg-white/10 backdrop-blur-md rounded-2xl">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <span class="text-[10px] font-black uppercase tracking-widest text-indigo-300">Seguridad</span>
                </div>
                <h3 class="text-sm font-bold text-indigo-100 mb-1 leading-none">Incidentes Reportados</h3>
                <div class="flex items-baseline gap-2 mb-4">
                    <span class="text-4xl font-black tracking-tight">{{ $kpis['incidents']['total'] }}</span>
                    @if($kpis['incidents']['serious'] > 0)
                        <span class="text-xs font-bold text-red-400 opacity-90 italic">({{ $kpis['incidents']['serious'] }} graves)</span>
                    @endif
                </div>
                <div class="pt-4 border-t border-white/10 flex items-center justify-between">
                    <span class="text-[10px] font-bold text-indigo-300 uppercase">Días Perdidos:</span>
                    <span class="text-lg font-black">{{ $kpis['incidents']['lost_days'] }}</span>
                </div>
            </div>
        </div>

        {{-- Row 3: Corporate Compliance & ecosystem --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10">
            <!-- Master Reports Card -->
            <div class="col-span-1 md:col-span-2 bg-white rounded-3xl shadow-xl shadow-gray-200/50 p-6 border border-gray-100 flex items-center gap-6">
                <div class="p-4 bg-orange-50 text-ep-primary rounded-2xl">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Informes Maestros</h3>
                    <div class="flex items-center gap-4">
                        <div>
                            <span class="block text-2xl font-black text-gray-900">{{ $kpis['reports']['total_master'] }}</span>
                            <span class="text-[10px] font-bold text-gray-500 uppercase">Solicitados</span>
                        </div>
                        <div class="h-8 w-px bg-gray-100"></div>
                        <div>
                            <span class="block text-2xl font-black text-green-600">{{ $kpis['reports']['completed'] }}</span>
                            <span class="text-[10px] font-bold text-gray-500 uppercase">Finalizados</span>
                        </div>
                    </div>
                </div>
                <div class="text-right">
                    <span class="inline-block px-3 py-1 bg-gray-100 rounded-full text-[10px] font-black text-gray-500 uppercase">
                        {{ $kpis['reports']['pending'] }} Pendientes
                    </span>
                </div>
            </div>

            <!-- Annual Compliance -->
            <div class="bg-white rounded-3xl shadow-xl shadow-gray-200/50 p-6 border-l-8 border-green-500 flex flex-col justify-center">
                <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Cumplimiento Anual</h3>
                <div class="flex items-center gap-3">
                    <span class="text-3xl font-black text-gray-900">
                        {{ $kpis['annual']['compliance_percentage'] }}%
                    </span>
                    <div class="flex-1 h-2 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full bg-green-500 rounded-full" style="width:{{ $kpis['annual']['compliance_percentage'] }}%"></div>
                    </div>
                </div>
            </div>

            <!-- Eco-System Link -->
            <div class="bg-gray-900 rounded-3xl shadow-xl p-6 text-white flex flex-col justify-center relative overflow-hidden group hover:bg-black transition-colors">
                <div class="absolute right-2 bottom-2 text-white/5 group-hover:text-white/10 transition-colors">
                    <svg class="w-16 h-16" fill="currentColor" viewBox="0 0 24 24"><path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
                <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Directorio</h3>
                <p class="text-xs font-bold text-indigo-100">Ver Gestión de Empresas y Técnicos</p>
                <div class="mt-2 flex items-center gap-1 text-[10px] font-black text-ep-primary uppercase italic">
                    Ir ahora
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </div>
                <a href="{{ route('gestor.companies.index') }}" class="absolute inset-0 z-10"></a>
            </div>
        </div>

        {{-- Layout Grid for Charts and Lists --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-10">
            {{-- Left Column: Charts and Stats (Col 8) --}}
            <div class="lg:col-span-2 space-y-8">
                <!-- Main Trend Chart -->
                <div class="bg-white rounded-[2rem] shadow-xl shadow-gray-200/50 p-8 border border-gray-100">
                    <div class="flex items-center justify-between mb-8">
                        <div>
                            <h3 class="text-xl font-black text-gray-900 tracking-tight italic">Evolución Operativa</h3>
                            <p class="text-sm text-gray-500 font-medium">Tendencia de actividad en el periodo seleccionado</p>
                        </div>
                        <div class="flex items-center gap-3">
                             <div class="flex items-center gap-2 px-3 py-1.5 bg-green-50 rounded-full">
                                <span class="h-2 w-2 rounded-full bg-green-500"></span>
                                <span class="text-[10px] font-black text-green-700 uppercase">Inspecciones</span>
                             </div>
                             <div class="flex items-center gap-2 px-3 py-1.5 bg-indigo-50 rounded-full">
                                <span class="h-2 w-2 rounded-full bg-indigo-500"></span>
                                <span class="text-[10px] font-black text-indigo-700 uppercase">Incidentes</span>
                             </div>
                        </div>
                    </div>
                    <div style="height: 350px;">
                        <canvas id="mainTrendChart"></canvas>
                    </div>
                </div>

                <!-- Secondary Charts Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="bg-white rounded-[2rem] shadow-xl shadow-gray-200/50 p-6 border border-gray-100">
                        <h3 class="text-xs font-black text-gray-900 uppercase tracking-widest mb-6 px-2 border-l-4 border-red-500 pl-4 italic">Estado de Hallazgos</h3>
                        <div style="height: 250px;">
                            <canvas id="findingsStatusChart"></canvas>
                        </div>
                    </div>
                    <div class="bg-white rounded-[2rem] shadow-xl shadow-gray-200/50 p-6 border border-gray-100">
                        <h3 class="text-xs font-black text-gray-900 uppercase tracking-widest mb-6 px-2 border-l-4 border-orange-500 pl-4 italic">Top Servicios</h3>
                        <div style="height: 250px;">
                            <canvas id="topChecklistsChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Rankings Section -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Top Technicians -->
                    <div class="bg-white rounded-[2rem] shadow-xl shadow-gray-200/50 overflow-hidden border border-gray-100">
                        <div class="px-6 py-4 bg-gray-50 border-b border-gray-100">
                            <h3 class="text-xs font-black text-gray-900 uppercase tracking-widest italic">Ranking de Técnicos</h3>
                        </div>
                        <div class="p-4">
                            @foreach($rankings['top_technicians'] as $tech)
                                <div class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-2xl transition-colors">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 bg-ep-dark rounded-full flex items-center justify-center text-white text-[10px] font-black">
                                            {{ substr($tech->nombres, 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-gray-800 leading-none">{{ $tech->nombres }}</p>
                                            <p class="text-[10px] text-gray-400 font-medium">Técnico Operativo</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-sm font-black text-ep-primary">{{ $tech->checklists_count }}</span>
                                        <span class="text-[10px] text-gray-400 font-bold uppercase block leading-none">Inspecciones</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Companies with Findings -->
                    <div class="bg-white rounded-[2rem] shadow-xl shadow-gray-200/50 overflow-hidden border border-gray-100">
                        <div class="px-6 py-4 bg-gray-50 border-b border-gray-100">
                            <h3 class="text-xs font-black text-gray-900 uppercase tracking-widest italic">Empresas con Hallazgos</h3>
                        </div>
                        <div class="p-4">
                            @foreach($rankings['top_companies_findings'] as $empresa)
                                <div class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-2xl transition-colors">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 bg-red-50 text-red-600 rounded-lg flex items-center justify-center">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                        </div>
                                        <div class="max-w-[150px]">
                                            <p class="text-sm font-bold text-gray-800 leading-none truncate">{{ $empresa->nombre }}</p>
                                            <p class="text-[10px] text-gray-400 font-medium truncate italic">Nivel de criticidad medio</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-sm font-black text-red-600">{{ $empresa->findings_count }}</span>
                                        <span class="text-[10px] text-gray-400 font-bold uppercase block leading-none">Pendientes</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Column: Timeline and Upcoming (Col 4) --}}
            <div class="lg:col-span-1 space-y-8">
                <!-- Activity Timeline -->
                <div class="bg-white rounded-[2rem] shadow-xl shadow-gray-200/50 flex flex-col h-full border border-gray-100 overflow-hidden">
                    <div class="px-8 py-6 bg-gray-50/50 border-b border-gray-100 flex items-center justify-between">
                        <h3 class="text-lg font-black text-gray-900 tracking-tight">Actividad <span class="text-ep-primary">Reciente</span></h3>
                        <span class="p-2 bg-white rounded-xl shadow-sm">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </span>
                    </div>
                    <div class="p-6 overflow-y-auto max-h-[1000px] scrollbar-thin scrollbar-thumb-gray-200 scrollbar-track-transparent">
                        <div class="relative ml-4 border-l-2 border-gray-100 space-y-8 pb-4">
                            @forelse($recentActivity as $activity)
                                <div class="relative pl-8">
                                    {{-- Time Dot --}}
                                    <div class="absolute -left-[9px] top-0 w-4 h-4 rounded-full border-4 border-white shadow-sm
                                        {{ $activity->type === 'visita' ? 'bg-blue-500' : 
                                           ($activity->type === 'inspeccion' ? 'bg-green-500' : 
                                           ($activity->type === 'capacitacion' ? 'bg-indigo-500' : 'bg-orange-500')) }}">
                                    </div>
                                    
                                    <div class="flex flex-col">
                                        <div class="flex items-center justify-between mb-1">
                                            <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">
                                                {{ $activity->type }}
                                            </p>
                                            <span class="text-[10px] font-bold text-gray-500 bg-gray-100 px-2 py-0.5 rounded-full">
                                                {{ $activity->date ? \Carbon\Carbon::parse($activity->date)->format('d/m') : '-' }}
                                            </span>
                                        </div>
                                        <p class="text-sm font-bold text-gray-800 leading-snug mb-1 group-hover:text-ep-primary transition-colors">
                                            {{ $activity->title }}
                                        </p>
                                        <div class="flex items-center gap-2">
                                            <span class="text-[10px] font-bold text-gray-500 bg-gray-50 px-2 py-0.5 rounded border border-gray-100 truncate max-w-[120px]">
                                                {{ $activity->empresa }}
                                            </span>
                                            <span class="text-[10px] font-medium text-gray-400 truncate">
                                                por {{ $activity->user_name }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-12">
                                    <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-300">
                                      <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    </div>
                                    <p class="text-sm text-gray-400 font-medium">Sin actividad reciente</p>
                                </div>
                            @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    document.addEventListener('livewire:navigated', initDashboardCharts);
    document.addEventListener('DOMContentLoaded', initDashboardCharts);
    
    // Re-init on Livewire updates
    document.addEventListener('livewire:load', function() {
        Livewire.on('updatedPeriod', () => {
            setTimeout(initDashboardCharts, 100);
        });
    });

    // For Livewire 3+
    document.addEventListener('livewire:init', () => {
        Livewire.on('periodUpdated', (event) => {
            initDashboardCharts();
        });
    });

    function initDashboardCharts() {
        const chartConfig = {
            font: "'Inter', sans-serif",
            colors: {
                primary: '#F69239',
                secondary: '#292938',
                success: '#10B981',
                danger: '#EF4444',
                info: '#4F46E5',
                warning: '#F59E0B'
            }
        };

        Chart.defaults.font.family = chartConfig.font;
        Chart.defaults.color = '#64748b';

        // 1. Main Trend Chart (Inspections vs Incidents)
        const trendCtx = document.getElementById('mainTrendChart');
        if (trendCtx) {
            new Chart(trendCtx, {
                type: 'line',
                data: {
                    labels: @json($charts['inspectionsPerMonth']['labels']),
                    datasets: [
                        {
                            label: 'Inspecciones',
                            data: @json($charts['inspectionsPerMonth']['data']),
                            borderColor: chartConfig.colors.success,
                            borderWidth: 4,
                            backgroundColor: 'rgba(16, 185, 129, 0.05)',
                            fill: true,
                            tension: 0.4,
                            pointRadius: 0
                        },
                        {
                            label: 'Incidentes',
                            data: @json($charts['incidentsPerMonth']['data']),
                            borderColor: chartConfig.colors.danger,
                            borderWidth: 2,
                            borderDash: [5, 5],
                            backgroundColor: 'transparent',
                            fill: false,
                            tension: 0.4,
                            pointRadius: 0
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { intersect: false, mode: 'index' },
                    scales: {
                        y: { grid: { borderDash: [5, 5], color: '#f1f5f9' }, border: { display: false } },
                        x: { grid: { display: false }, border: { display: false } }
                    },
                    plugins: { 
                        legend: { display: false },
                        tooltip: { backgroundColor: '#1e1e2d', cornerRadius: 12, padding: 12 }
                    }
                }
            });
        }

        // 2. Findings Status Chart (Doughnut)
        const findingsCtx = document.getElementById('findingsStatusChart');
        if (findingsCtx) {
            new Chart(findingsCtx, {
                type: 'doughnut',
                data: {
                    labels: @json($charts['findingsByStatus']['labels']),
                    datasets: [{
                        data: @json($charts['findingsByStatus']['data']),
                        backgroundColor: [chartConfig.colors.warning, chartConfig.colors.info, chartConfig.colors.success, chartConfig.colors.danger],
                        borderWidth: 0,
                        hoverOffset: 15
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '70%',
                    plugins: { 
                        legend: { position: 'bottom', labels: { usePointStyle: true, padding: 15, font: { size: 10, weight: 'bold' } } }
                    }
                }
            });
        }

        // 3. Top Checklists (Horizontal Bar)
        const topCtx = document.getElementById('topChecklistsChart');
        if (topCtx) {
            new Chart(topCtx, {
                type: 'bar',
                data: {
                    labels: @json($charts['topChecklists']['labels']),
                    datasets: [{
                        data: @json($charts['topChecklists']['data']),
                        backgroundColor: chartConfig.colors.primary,
                        borderRadius: 8,
                        barThickness: 15
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: { display: false },
                        y: { grid: { display: false }, border: { display: false }, ticks: { font: { size: 10, weight: 'bold' } } }
                    },
                    plugins: { legend: { display: false } }
                }
            });
        }
    }
</script>
@endpush
