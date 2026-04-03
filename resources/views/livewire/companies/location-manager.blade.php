<div class="w-full mx-auto">
    @section('header_custom')
        <header class="h-16 relative overflow-hidden bg-ep-dark flex items-center z-20 shadow-lg">
            <div class="relative w-full px-4 sm:px-6 lg:px-8 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <!-- Mobile menu button -->
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="lg:hidden p-2 text-indigo-200 hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 6h16M4 12h16M4 18h16" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>

                    <div class="flex flex-col">
                        <h1 class="text-xs font-black text-ep-primary uppercase tracking-widest italic leading-none mb-1">
                            Control de <span class="text-white">Infraestructura</span>
                        </h1>
                        <h2 class="text-sm font-bold text-indigo-100 uppercase tracking-tight leading-none italic">
                            Gestión de Sedes - {{ $empresa->nombre }}
                        </h2>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <a href="{{ route('gestor.companies.index') }}" class="flex items-center gap-2 px-4 py-2 bg-white/5 border border-white/10 rounded-xl text-[10px] font-black text-white/40 hover:text-white hover:bg-white/10 transition-all uppercase italic tracking-widest group">
                        <svg class="w-4 h-4 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        Volver a Empresas
                    </a>
                </div>
            </div>
            <div class="absolute bottom-0 left-0 h-px w-full bg-gradient-to-r from-transparent via-ep-primary/30 to-transparent"></div>
        </header>
    @endsection

    <div class="px-2 sm:px-6 lg:px-8 py-8 text-white">
        <div class="max-w-[95rem] mx-auto space-y-8 animate-fade-in">

            @if (session()->has('message'))
                <div class="bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 px-6 py-4 rounded-2xl flex items-center gap-3 animate-fade-in-down shadow-[0_0_20px_rgba(16,185,129,0.1)]">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="2"/></svg>
                    <span class="text-xs font-black uppercase italic tracking-widest">{{ session('message') }}</span>
                </div>
            @endif
            
            {{-- HUB DE COMANDOS: FILTROS Y ACCIÓN MAESTRA --}}
            <div class="bg-ep-dark/40 backdrop-blur-md rounded-3xl sm:rounded-[2.5rem] border border-white/5 p-4 sm:p-6 lg:p-8 shadow-2xl relative overflow-hidden animate-fade-in-down">
                <div class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-ep-primary/40 to-transparent"></div>
                
                <div class="grid grid-cols-1 md:grid-cols-12 gap-4 sm:gap-6 lg:gap-8 items-end relative z-10">
                    {{-- BUSCADOR TÁCTICO --}}
                    <div class="md:col-span-5 relative group">
                        <span class="absolute -top-2.5 left-4 bg-[#11111d] px-2 text-[8px] font-black text-ep-primary uppercase tracking-[0.3em] italic z-10">Localizador de Sedes</span>
                        <div class="relative">
                            <input type="text" wire:model.live.debounce.300ms="search" 
                                   placeholder="BUSCAR SEDE, LOCALIDAD O DIRECCIÓN..."
                                   class="w-full bg-black/40 border border-white/10 rounded-2xl px-12 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-ep-primary/50 transition-all placeholder-white/5 italic uppercase">
                            <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-ep-primary/30 group-focus-within:text-ep-primary transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    {{-- FILTROS DE INTELIGENCIA --}}
                    <div class="md:col-span-4 flex items-center gap-6">
                        <div class="flex-1 px-6 py-4 bg-black/40 border border-white/10 rounded-2xl flex items-center justify-between group">
                            <div>
                                <p class="text-[8px] font-bold text-white/20 uppercase tracking-widest italic mb-0.5">Sincronización</p>
                                <p class="text-[10px] font-black text-ep-primary uppercase italic tracking-tighter">RED ACTIVA</p>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="text-[9px] font-black text-white/40 uppercase italic tracking-widest">Eliminados</span>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" wire:model.live="showDeleted" class="sr-only peer">
                                    <div class="w-9 h-5 bg-white/10 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white/20 after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-red-500/50 peer-checked:after:bg-white"></div>
                                </label>
                            </div>
                        </div>
                    </div>

                    {{-- ACCIÓN PRIMARIA --}}
                    <div class="md:col-span-3">
                        <button wire:click="toggleForm" 
                           class="w-full flex h-14 bg-ep-primary text-white hover:brightness-110 active:scale-95 rounded-2xl text-[11px] font-black uppercase tracking-[0.2em] italic transition-all items-center justify-center gap-3 shadow-[0_0_25px_rgba(246,146,57,0.3)] group/btn relative overflow-hidden">
                            <div class="absolute inset-0 bg-white/10 translate-y-14 group-hover/btn:translate-y-0 transition-transform duration-500"></div>
                            <svg class="w-5 h-5 group-hover/btn:-rotate-90 transition-transform duration-500 relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                                <path d="{{ $showForm ? 'M6 18L18 6M6 6l12 12' : 'M12 4v16m8-8H4' }}"/>
                            </svg>
                            <span class="relative z-10">{{ $showForm ? 'Cerrar Registro' : 'Nueva Sede' }}</span>
                        </button>
                    </div>
                </div>
            </div>

            {{-- FORMULARIO DE INYECCIÓN DE SEDE (HUD) --}}
            @if($showForm)
                <div class="bg-ep-dark/60 backdrop-blur-xl rounded-3xl sm:rounded-[3rem] border-2 border-ep-primary/30 p-5 sm:p-10 shadow-[0_0_50px_rgba(246,146,57,0.1)] relative animate-fade-in-down overflow-hidden">
                    <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-transparent via-ep-primary/50 to-transparent"></div>
                    
                    <div class="flex items-center justify-between mb-10">
                        <h3 class="text-xs font-black text-white italic uppercase tracking-[0.3em]">
                            Inyección de <span class="text-ep-primary">Infraestructura</span>
                        </h3>
                        <div class="text-[10px] font-black text-white/20 uppercase tracking-widest italic">Protocolo de Registro de Localización</div>
                    </div>

                    <form wire:submit.prevent="save" class="space-y-10">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                            <div class="relative group">
                                <span class="absolute -top-2.5 left-4 bg-ep-dark px-2 text-[8px] font-black text-ep-primary uppercase tracking-[0.3em] italic z-10">Nombre Identificador</span>
                                <input type="text" wire:model="newNombreSede" placeholder="EJ: SEDE CENTRAL / PLANTA ALPHA" class="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-ep-primary/50 transition-all placeholder-white/5 italic uppercase">
                                @error('newNombreSede') <span class="text-red-500 text-[8px] font-black uppercase mt-2 block ml-2">{{ $message }}</span> @enderror
                            </div>

                            <div class="relative group">
                                <span class="absolute -top-2.5 left-4 bg-ep-dark px-2 text-[8px] font-black text-white/40 uppercase tracking-[0.3em] italic z-10">Dirección Física</span>
                                <input type="text" wire:model="newDireccion" placeholder="CALLE, NÚMERO, BARRIO..." class="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-white/20 transition-all placeholder-white/5 italic">
                            </div>

                            <div class="relative group">
                                <span class="absolute -top-2.5 left-4 bg-ep-dark px-2 text-[8px] font-black text-white/40 uppercase tracking-[0.3em] italic z-10">Tipo de Sede</span>
                                <input type="text" wire:model="newTipoSede" list="tipos-list" placeholder="OFICINA / DEPÓSITO / PLANTA..." class="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-white/20 transition-all placeholder-white/5 italic uppercase">
                            </div>

                            <div class="relative group">
                                <span class="absolute -top-2.5 left-4 bg-ep-dark px-2 text-[8px] font-black text-white/40 uppercase tracking-[0.3em] italic z-10">Provincia</span>
                                <select wire:model="newProvinciaId" class="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-white/20 transition-all [color-scheme:dark] italic uppercase">
                                    <option value="">SELECCIONAR...</option>
                                    @foreach($existingProvincias as $provincia)
                                        <option value="{{ $provincia->id }}">{{ $provincia->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="relative group">
                                <span class="absolute -top-2.5 left-4 bg-ep-dark px-2 text-[8px] font-black text-white/40 uppercase tracking-[0.3em] italic z-10">Localidad</span>
                                <input type="text" wire:model="newLocalidad" list="localidades-list" placeholder="CIUDAD / MUNICIPIO..." class="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-white/20 transition-all placeholder-white/5 italic uppercase">
                            </div>

                            <div class="relative group">
                                <span class="absolute -top-2.5 left-4 bg-ep-dark px-2 text-[8px] font-black text-white/40 uppercase tracking-[0.3em] italic z-10">Coordenadas GPS (Lat / Lon)</span>
                                <div class="flex gap-2">
                                    <input type="text" wire:model="newLatitud" placeholder="LAT" class="w-1/2 bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-white/20 transition-all placeholder-white/5 italic">
                                    <input type="text" wire:model="newLongitud" placeholder="LON" class="w-1/2 bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-white/20 transition-all placeholder-white/5 italic">
                                </div>
                            </div>

                            <div class="relative group">
                                <span class="absolute -top-2.5 left-4 bg-ep-dark px-2 text-[8px] font-black text-white/40 uppercase tracking-[0.3em] italic z-10">Teléfono Directo</span>
                                <input type="text" wire:model="newTelefono" placeholder="EJ: +54 9..." class="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-white/20 transition-all placeholder-white/5 italic">
                            </div>

                            <div class="relative group">
                                <span class="absolute -top-2.5 left-4 bg-ep-dark px-2 text-[8px] font-black text-white/40 uppercase tracking-[0.3em] italic z-10">Email Institucional</span>
                                <input type="email" wire:model="newCorreo" placeholder="CONTACTO@EMPRESA.COM" class="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-white/20 transition-all placeholder-white/5 italic uppercase">
                                @error('newCorreo') <span class="text-red-500 text-[8px] font-black uppercase mt-2 block ml-2">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="flex justify-end gap-6 pt-10 border-t border-white/5">
                            <button type="button" wire:click="toggleForm" class="px-8 h-12 bg-white/5 border border-white/10 text-white/40 hover:text-white rounded-xl text-[10px] font-black uppercase tracking-widest italic transition-all">Descartar</button>
                            <button type="submit" wire:loading.attr="disabled" class="px-10 h-12 bg-ep-primary text-white rounded-xl text-[10px] font-black uppercase tracking-widest italic shadow-[0_0_20px_rgba(246,146,57,0.3)] hover:brightness-110 active:scale-95 transition-all">
                                <span wire:loading.remove>Confirmar Registro</span>
                                <span wire:loading class="flex items-center gap-2">
                                    <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    Cargando...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            @endif

            {{-- MATRIZ DE SEDES --}}
            <div class="bg-ep-dark/60 backdrop-blur-md rounded-3xl sm:rounded-[3rem] border border-white/5 shadow-2xl relative overflow-hidden animate-fade-in-up">
                <div class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-ep-primary/20 to-transparent"></div>
                
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse">
                        <thead>
                            <tr class="bg-black/20 text-left">
                                <th class="px-4 lg:px-8 py-6 text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic leading-none">Identificador / Sede</th>
                                <th class="hidden md:table-cell px-4 lg:px-8 py-6 text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic leading-none text-center">Tipo</th>
                                <th class="px-4 lg:px-8 py-6 text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic leading-none">Despliegue / Ubicación</th>
                                <th class="hidden lg:table-cell px-4 lg:px-8 py-6 text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic leading-none">Equipo Técnico</th>
                                <th class="px-4 lg:px-8 py-6 text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic leading-none text-right">Comandos</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @forelse($sedes as $sede)
                                <tr class="group/row hover:bg-white/5 transition-all duration-500 relative {{ $sede->trashed() ? 'opacity-50 grayscale' : '' }}">
                                    <td class="px-4 lg:px-8 py-6">
                                        <div class="flex items-center gap-3 sm:gap-4">
                                            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-2xl bg-ep-primary/10 border border-ep-primary/20 text-ep-primary flex items-center justify-center group-hover/row:scale-110 transition-transform">
                                                <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" stroke-width="2"/><path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" stroke-width="2"/></svg>
                                            </div>
                                            <div>
                                                <div class="text-xs sm:text-sm font-black text-white italic uppercase tracking-tight leading-none group-hover/row:text-ep-primary transition-colors flex items-center gap-2">
                                                    {{ $sede->nombre_sede }}
                                                    @if($sede->trashed()) <span class="bg-red-500 text-white text-[7px] px-1.5 py-0.5 rounded italic">DE BAJA</span> @endif
                                                </div>
                                                <div class="text-[8px] sm:text-[9px] font-black text-white/20 uppercase tracking-widest italic mt-1">{{ $sede->correo ?: 'SIN EMAIL' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    
                                    <td class="hidden md:table-cell px-4 lg:px-8 py-6 text-center">
                                        <span class="px-3 py-1.5 rounded-xl text-[9px] font-black bg-white/5 border border-white/10 text-white/40 uppercase tracking-[0.2em] italic">
                                            {{ $sede->tipo_sede ?: 'GENERAL' }}
                                        </span>
                                    </td>

                                    <td class="px-4 lg:px-8 py-6">
                                        <div class="text-[10px] font-black text-white/80 italic uppercase leading-none mb-1">{{ $sede->direccion ?: 'S/D' }}</div>
                                        <div class="text-[9px] font-bold text-white/20 italic uppercase tracking-widest">
                                            {{ $sede->localidad }}{{ $sede->provincia ? ' • ' . $sede->provincia->nombre : '' }}
                                        </div>
                                    </td>

                                    <td class="hidden lg:table-cell px-4 lg:px-8 py-6">
                                        @if($sede->users->count() > 0)
                                            <div class="space-y-1">
                                                @foreach($sede->users as $u)
                                                    <div class="flex items-center gap-2 group/u">
                                                        <div class="w-5 h-5 rounded-lg bg-indigo-500/20 border border-indigo-500/30 flex items-center justify-center text-[8px] font-black text-indigo-400">
                                                            {{ substr($u->nombres, 0, 1) }}
                                                        </div>
                                                        <span class="text-[10px] font-bold text-white/60 italic uppercase tracking-tight group-hover/u:text-white transition-colors">
                                                            {{ $u->nombres }} {{ $u->apellidos }}
                                                        </span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-[10px] font-black text-white/5 uppercase italic tracking-widest">Sin asignar</span>
                                        @endif
                                    </td>

                                    <td class="px-4 lg:px-8 py-6 text-right">
                                        <div class="flex justify-end gap-2 sm:gap-3 lg:translate-x-4 lg:opacity-0 lg:group-hover/row:translate-x-0 lg:group-hover/row:opacity-100 transition-all duration-500">
                                            @if($sede->trashed())
                                                <button wire:click="restore({{ $sede->id }})" class="h-10 px-4 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 hover:bg-emerald-500 hover:text-white rounded-xl text-[9px] font-black uppercase italic tracking-[0.2em] transition-all">Restaurar</button>
                                            @else
                                                <button wire:click="manageUsers({{ $sede->id }})" class="h-10 px-4 bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 hover:bg-indigo-500 hover:text-white rounded-xl text-[9px] font-black uppercase italic tracking-[0.2em] transition-all">Técnicos</button>
                                                <button wire:click="edit({{ $sede->id }})" class="h-10 w-10 flex items-center justify-center bg-white/5 border border-white/10 text-white/40 hover:bg-white/20 hover:text-white rounded-xl transition-all shadow-lg active:scale-90"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" stroke-width="2.5"/></svg></button>
                                                <button wire:click="confirmDelete({{ $sede->id }}, '{{ $sede->nombre_sede }}')" class="h-10 w-10 flex items-center justify-center bg-red-500/10 border border-red-500/20 text-red-500 hover:bg-red-600 hover:text-white rounded-xl transition-all shadow-lg active:scale-90"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2.5"/></svg></button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="px-8 py-32 text-center text-white/5 uppercase font-black text-sm tracking-[0.4em] italic leading-relaxed">No se detectaron localizaciones activas <br> <span class="text-[9px] tracking-widest text-white/3 italic">Inicie un nuevo registro de infraestructura desde la barra superior</span></td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="px-4 sm:px-10 py-6 border-t border-white/5 bg-black/20 flex flex-col sm:flex-row items-center justify-between gap-6">
                    <div class="hidden sm:block text-[9px] font-black text-white/10 uppercase tracking-[0.5em] italic">Infraestructura Monitorizada v4.0 • Consultora Autorizada</div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL GESTIÓN DE TÉCNICOS --}}
    @if($managingUsersForSede)
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm z-[100] flex items-center justify-center p-4">
            <div class="bg-ep-dark border border-white/10 rounded-[2.5rem] w-full max-w-2xl overflow-hidden shadow-2xl animate-fade-in-up">
                <div class="px-10 py-8 border-b border-white/5 bg-black/20 flex justify-between items-center">
                    <div>
                        <h3 class="text-sm font-black text-white italic uppercase tracking-[0.2em] leading-none mb-1">Escuadrón <span class="text-indigo-400">Técnico</span></h3>
                        <p class="text-[9px] font-bold text-white/20 uppercase italic tracking-widest">Asignación de Responsabilidades</p>
                    </div>
                    <button wire:click="closeUserManagement" class="w-10 h-10 rounded-full bg-white/5 text-white/40 hover:text-white transition-all flex items-center justify-center"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2.5"/></svg></button>
                </div>
                
                <div class="p-10 max-h-[60vh] overflow-y-auto space-y-4">
                    @forelse($availableUsers as $user)
                        @php $isSelected = in_array($user->id, $assignedUsers); @endphp
                        <label wire:click="toggleUser({{ $user->id }})" class="group flex items-center justify-between p-5 rounded-3xl border transition-all cursor-pointer {{ $isSelected ? 'bg-indigo-500/10 border-indigo-500/30' : 'bg-white/5 border-white/5 hover:border-white/20' }}">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-2xl flex items-center justify-center text-xs font-black {{ $isSelected ? 'bg-indigo-500 text-white' : 'bg-white/10 text-white/40 group-hover:text-white' }}">
                                    {{ substr($user->nombres, 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-xs font-black {{ $isSelected ? 'text-white' : 'text-white/60' }} italic uppercase tracking-tight">{{ $user->apellidos }}, {{ $user->nombres }}</p>
                                    <p class="text-[9px] font-bold text-white/20 italic">{{ $user->email }}</p>
                                </div>
                            </div>
                            <div class="w-10 h-10 rounded-full flex items-center justify-center transition-all">
                                @if($isSelected)
                                    <svg class="w-6 h-6 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4" stroke-width="3"/></svg>
                                @else
                                    <div class="w-6 h-6 rounded-full border-2 border-white/10 group-hover:border-white/30"></div>
                                @endif
                            </div>
                        </label>
                    @empty
                        <div class="py-10 text-center text-white/5 uppercase italic text-[10px] font-black tracking-widest leading-loose">No hay operarios detectados <br> Registre nuevos técnicos en el HUB maestro</div>
                    @endforelse
                </div>
                
                <div class="px-10 py-6 bg-black/20 border-t border-white/5 flex justify-end">
                    <button wire:click="closeUserManagement" class="px-8 h-12 bg-white/5 border border-white/10 text-white/40 hover:text-white rounded-xl text-[10px] font-black uppercase italic tracking-widest transition-all">Cerrar Protocolo</button>
                </div>
            </div>
        </div>
    @endif

    {{-- MODAL ELIMINACIÓN --}}
    @if($confirmingDeletion)
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm z-[100] flex items-center justify-center p-4">
            <div class="bg-ep-dark border border-red-500/30 rounded-[2.5rem] w-full max-w-lg overflow-hidden shadow-[0_0_50px_rgba(239,68,68,0.1)] animate-fade-in-up">
                <div class="p-10 text-center">
                    <div class="w-16 h-16 bg-red-500/10 border border-red-500/30 rounded-3xl mx-auto flex items-center justify-center text-red-500 mb-6 drop-shadow-[0_0_15px_rgba(239,68,68,0.4)]">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" stroke-width="2"/></svg>
                    </div>
                    <h3 class="text-sm font-black text-white italic uppercase tracking-[0.2em] mb-3">Protocolo de <span class="text-red-500">Baja Táctica</span></h3>
                    <p class="text-[10px] font-bold text-white/40 uppercase italic tracking-widest leading-relaxed mb-10">
                        ¿CONFIRMAR DESCONEXIÓN DE <span class="text-white">{{ $sedeToDeleteName }}</span>?<br>
                        EL HISTORIAL SE PRESERVARÁ PARA AUDITORÍAS FUTURAS.
                    </p>
                    <div class="flex gap-4">
                        <button wire:click="cancelDelete" class="flex-1 h-12 bg-white/5 border border-white/10 text-white/40 hover:text-white rounded-xl text-[10px] font-black uppercase italic tracking-widest transition-all">Abortar</button>
                        <button wire:click="delete" class="flex-1 h-12 bg-red-600 text-white rounded-xl text-[10px] font-black uppercase italic tracking-widest transition-all shadow-[0_0_20px_rgba(220,38,38,0.3)] hover:brightness-110 active:scale-95">Confirmar Baja</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <style>
        .animate-fade-in-down { animation: fade-in-down 0.6s ease-out forwards; }
        .animate-fade-in-up { animation: fade-in-up 0.5s ease-out forwards; }
        .animate-fade-in { animation: fade-in 0.8s ease-out forwards; }
        @keyframes fade-in-down { from { opacity: 0; transform: translateY(-30px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes fade-in-up { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes fade-in { from { opacity: 0; } to { opacity: 1; } }
    </style>
</div>
