<div class="w-full mx-auto">
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

                    <div class="flex flex-col">
                        <h1 class="text-[10px] font-black text-ep-primary uppercase tracking-widest italic leading-none mb-1">
                            Matriz <span class="text-white">Identidades</span>
                        </h1>
                        <h2 class="text-sm font-bold text-indigo-100 uppercase tracking-tight leading-none">
                            Gestión Centralizada de Usuarios
                        </h2>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <a href="{{ route('profile') }}" class="flex items-center space-x-2 bg-white/5 backdrop-blur-sm p-1 pr-3 rounded-full border border-white/5 hover:bg-white/10 transition-all group">
                        <div class="w-7 h-7 bg-ep-primary rounded-full flex items-center justify-center text-white text-[10px] font-black shadow-lg">
                            {{ substr(auth()->user()->nombres ?? 'U', 0, 1) }}
                        </div>
                        <div class="h-4 w-px bg-white/10 mx-1"></div>
                        <span class="text-[10px] font-bold text-indigo-100 italic">{{ auth()->user()->nombres }}</span>
                    </a>
                </div>
            </div>
            <div class="absolute bottom-0 left-0 h-px w-full bg-gradient-to-r from-transparent via-ep-primary/30 to-transparent"></div>
        </header>
    @endsection

    <div class="px-4 sm:px-6 lg:px-8 py-8 bg-[#11111d] min-h-screen text-white">
        <div class="max-w-[95rem] mx-auto space-y-8 animate-fade-in">
            
            {{-- HUB DE FILTRADO Y ACCIÓN MAESTRA --}}
            <div class="bg-ep-dark/40 backdrop-blur-md rounded-[2.5rem] border border-white/5 p-6 shadow-2xl relative overflow-hidden animate-fade-in-down">
                <div class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-ep-primary/30 to-transparent"></div>
                
                <div class="flex flex-col lg:flex-row gap-6 justify-between items-center relative z-10">
                    <div class="w-full lg:w-1/3 relative group">
                        <span class="absolute -top-2.5 left-4 bg-[#11111d] px-2 text-[8px] font-black text-ep-primary uppercase tracking-[0.3em] italic z-10">Terminal de Búsqueda</span>
                        <input type="text" wire:model.live="search" 
                               placeholder="NOMBRE, EMAIL O MATRÍCULA..."
                               class="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-ep-primary/50 transition-all placeholder-white/5 italic uppercase">
                    </div>

                    <div class="flex flex-wrap items-center gap-6 w-full lg:w-auto justify-end">
                        <div class="flex items-center gap-4 bg-black/20 px-6 py-3 rounded-2xl border border-white/5">
                            <label class="inline-flex items-center cursor-pointer group">
                                <input type="checkbox" wire:model.live="showDeleted" class="sr-only peer">
                                <div class="relative w-11 h-6 bg-white/5 border border-white/10 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white/20 after:border-white/10 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-red-500/50"></div>
                                <span class="ms-3 text-[10px] font-black text-white/40 uppercase tracking-widest italic group-hover:text-red-400 transition-colors">Bajas Sistema</span>
                            </label>
                        </div>

                        <select wire:model.live="roleFilter" class="bg-black/40 border border-white/10 rounded-2xl px-6 py-3 text-[10px] font-black text-white focus:ring-2 focus:ring-indigo-500/50 transition-all [color-scheme:dark] italic uppercase appearance-none cursor-pointer">
                            <option value="all">TODOS LOS ROLES</option>
                            <option value="gestor">GESTORES</option>
                            <option value="tecnico">TÉCNICOS</option>
                        </select>

                        <button wire:click="toggleForm" 
                                class="flex h-12 px-8 bg-ep-primary text-white hover:brightness-110 active:scale-95 rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] italic transition-all items-center gap-3 shadow-[0_0_20px_rgba(246,146,57,0.3)] group/btn relative overflow-hidden">
                            <div class="absolute inset-0 bg-white/10 translate-y-12 group-hover/btn:translate-y-0 transition-transform"></div>
                            <svg class="w-4 h-4 group-hover/btn:rotate-90 transition-transform duration-500 relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                                <path d="{{ $showForm ? 'M6 18L18 6M6 6l12 12' : 'M12 4v16m8-8H4' }}"/>
                            </svg>
                            <span class="relative z-10">{{ $showForm ? 'Cancelar' : 'Nuevo Usuario' }}</span>
                        </button>
                    </div>
                </div>
            </div>

            {{-- FORMULARIO DE INYECCIÓN DE DATOS --}}
            @if($showForm)
                <div class="bg-ep-dark/60 backdrop-blur-md rounded-[3rem] border border-ep-primary/20 p-10 shadow-2xl animate-fade-in-up relative overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-br from-ep-primary/5 to-transparent"></div>
                    <h3 class="text-xl font-black text-white italic mb-8 uppercase tracking-widest relative z-10 flex items-center gap-3">
                        <span class="w-2 h-8 bg-ep-primary rounded-full"></span>
                        {{ $editingId ? 'Actualizar Perfil' : 'Registro de Identidad' }}
                    </h3>
                    
                    <form wire:submit.prevent="save" class="relative z-10">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                            <div class="space-y-2">
                                <label class="text-[9px] font-black text-white/40 uppercase tracking-[0.3em] ml-4">Nombres</label>
                                <input type="text" wire:model="nombres" class="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-ep-primary transition-all">
                                @error('nombres') <span class="text-red-500 text-[10px] font-black italic ml-4">{{ $message }}</span> @enderror
                            </div>

                            <div class="space-y-2">
                                <label class="text-[9px] font-black text-white/40 uppercase tracking-[0.3em] ml-4">Apellidos</label>
                                <input type="text" wire:model="apellidos" class="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-ep-primary transition-all">
                                @error('apellidos') <span class="text-red-500 text-[10px] font-black italic ml-4">{{ $message }}</span> @enderror
                            </div>

                            <div class="space-y-2">
                                <label class="text-[9px] font-black text-white/40 uppercase tracking-[0.3em] ml-4">Email Corporativo</label>
                                <input type="email" wire:model="email" class="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-ep-primary transition-all uppercase">
                                @error('email') <span class="text-red-500 text-[10px] font-black italic ml-4">{{ $message }}</span> @enderror
                            </div>

                            <div class="space-y-2">
                                <label class="text-[9px] font-black text-white/40 uppercase tracking-[0.3em] ml-4">Contraseña {{ $editingId ? '(Opcional)' : '(Requerida)' }}</label>
                                <input type="password" wire:model="password" class="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-ep-primary transition-all">
                                @error('password') <span class="text-red-500 text-[10px] font-black italic ml-4">{{ $message }}</span> @enderror
                            </div>

                            <div class="space-y-2">
                                <label class="text-[9px] font-black text-white/40 uppercase tracking-[0.3em] ml-4">Rol Sistema</label>
                                <select wire:model="rol" class="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-ep-primary transition-all [color-scheme:dark] italic uppercase">
                                    <option value="tecnico">TÉCNICO OPERATIVO</option>
                                    <option value="gestor">GESTOR ADMINISTRATIVO</option>
                                </select>
                                @error('rol') <span class="text-red-500 text-[10px] font-black italic ml-4">{{ $message }}</span> @enderror
                            </div>

                            <div class="space-y-2">
                                <label class="text-[9px] font-black text-white/40 uppercase tracking-[0.3em] ml-4">Matrícula ID</label>
                                <input type="text" wire:model="matricula" class="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-ep-primary transition-all uppercase" placeholder="MAT-000">
                                @error('matricula') <span class="text-red-500 text-[10px] font-black italic ml-4">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="mt-10 flex justify-end gap-4">
                            <button type="button" wire:click="toggleForm" class="px-8 py-4 bg-white/5 text-white/40 hover:text-white rounded-2xl text-[10px] font-black uppercase tracking-widest italic transition-all">
                                Abortar Operación
                            </button>
                            <button type="submit" wire:loading.attr="disabled" class="px-10 py-4 bg-ep-primary text-white rounded-2xl text-[10px] font-black uppercase tracking-widest italic transition-all shadow-lg shadow-ep-primary/20 hover:brightness-110">
                                <span wire:loading.remove>{{ $editingId ? 'Confirmar Actualización' : 'Inyectar Usuario' }}</span>
                                <span wire:loading class="animate-pulse">Procesando Identidad...</span>
                            </button>
                        </div>
                    </form>
                </div>
            @endif

            {{-- MASTER TABLE: MATRIZ DE IDENTIDADES --}}
            <div class="bg-ep-dark/60 backdrop-blur-md rounded-[3rem] border border-white/5 shadow-2xl relative overflow-hidden animate-fade-in-up">
                <div class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-indigo-500/20 to-transparent"></div>
                
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse">
                        <thead>
                            <tr class="bg-black/20 text-left">
                                <th class="px-8 py-6 text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic leading-none">Operativo / Perfil</th>
                                <th class="px-8 py-6 text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic leading-none">Canal de Enlace</th>
                                <th class="px-8 py-6 text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic leading-none text-center">Rol HUD</th>
                                <th class="px-8 py-6 text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic leading-none text-center">Matrícula</th>
                                <th class="px-8 py-6 text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic leading-none text-center">Status</th>
                                <th class="px-8 py-6 text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic leading-none text-right">Comandos</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @forelse($users as $user)
                                <tr class="group/row hover:bg-white/5 transition-all duration-500 relative {{ $user->trashed() ? 'bg-red-500/5' : '' }}">
                                    <td class="px-8 py-6">
                                        <div class="flex items-center gap-4">
                                            <div class="w-10 h-10 rounded-xl bg-ep-primary/10 flex items-center justify-center text-ep-primary text-xs font-black border border-ep-primary/20 group-hover/row:scale-110 transition-transform">
                                                {{ substr($user->nombres, 0, 1) }}{{ substr($user->apellidos, 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="text-sm font-black text-white italic uppercase tracking-tight leading-none group-hover/row:text-ep-primary transition-colors {{ $user->trashed() ? 'line-through opacity-40' : '' }}">
                                                    {{ $user->apellidos }}, {{ $user->nombres }}
                                                </div>
                                                @if($user->trashed()) <span class="text-[8px] font-black text-red-500 uppercase tracking-widest mt-1 inline-block">BAJA DETECTADA</span> @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <div class="text-[11px] font-bold text-white/40 italic group-hover/row:text-white/60 transition-colors">{{ $user->email }}</div>
                                    </td>
                                    <td class="px-8 py-6 text-center">
                                        @php
                                            $roleColor = match(strtolower($user->rol)) {
                                                'gestor' => 'text-purple-400 border-purple-500/30 bg-purple-500/10 shadow-[0_0_10px_rgba(168,85,247,0.2)]',
                                                'admin' => 'text-amber-400 border-amber-500/30 bg-amber-500/10 shadow-[0_0_10px_rgba(245,158,11,0.2)]',
                                                default => 'text-indigo-400 border-indigo-500/30 bg-indigo-500/10 shadow-[0_0_10px_rgba(99,102,241,0.2)]'
                                            };
                                        @endphp
                                        <span class="inline-flex px-3 py-1 rounded-full text-[8px] font-black uppercase tracking-widest italic border {{ $roleColor }}">
                                            {{ strtoupper($user->rol) }}
                                        </span>
                                    </td>
                                    <td class="px-8 py-6 text-center text-[11px] font-black text-white/60 italic uppercase tracking-widest">
                                        {{ $user->matricula ?: '---' }}
                                    </td>
                                    <td class="px-8 py-6 text-center">
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[8px] font-black uppercase tracking-widest italic border {{ ($user->estado === 'activo' && !$user->trashed()) ? 'text-emerald-400 border-emerald-500/30 bg-emerald-500/10' : 'text-white/20 border-white/5 bg-white/5' }}">
                                            <span class="w-1 h-1 rounded-full {{ ($user->estado === 'activo' && !$user->trashed()) ? 'bg-emerald-500 animate-pulse' : 'bg-white/20' }}"></span>
                                            {{ $user->trashed() ? 'INACTIVO' : ucfirst($user->estado) }}
                                        </span>
                                    </td>
                                    <td class="px-8 py-6 text-right">
                                        <div class="flex justify-end gap-3 translate-x-4 opacity-0 group-hover/row:translate-x-0 group-hover/row:opacity-100 transition-all duration-500">
                                            @if($user->trashed())
                                                <button wire:click="restore({{ $user->id }})" class="h-10 px-4 bg-emerald-500/10 border border-emerald-500/20 text-emerald-500 hover:bg-emerald-500 hover:text-white rounded-xl flex items-center gap-2 text-[9px] font-black uppercase tracking-widest italic transition-all shadow-lg">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" stroke-width="3"/></svg>
                                                    Restaurar
                                                </button>
                                            @else
                                                <button wire:click="edit({{ $user->id }})" class="h-10 w-10 bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 hover:bg-indigo-500 hover:text-white rounded-xl flex items-center justify-center transition-all shadow-lg">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" stroke-width="2"/></svg>
                                                </button>
                                                @if($user->id !== auth()->id())
                                                    <button wire:click="confirmDelete({{ $user->id }}, '{{ $user->nombres }} {{ $user->apellidos }}')" class="h-10 w-10 bg-red-600/10 border border-red-500/20 text-red-500 hover:bg-red-600 hover:text-white rounded-xl flex items-center justify-center transition-all shadow-lg">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2"/></svg>
                                                    </button>
                                                @endif
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-8 py-32 text-center text-white/5 uppercase font-black text-sm tracking-[0.4em] italic leading-relaxed">Matriz de identidades vacía <br> <span class="text-[9px] tracking-widest text-white/3 italic">Inyecte un nuevo usuario para iniciar el registro</span></td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="px-10 py-6 border-t border-white/5 bg-black/20 pagination-tactical">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL DE BAJA RÁPIDA (SOFT DELETE) --}}
    @if($confirmingDeletion)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 animate-fade-in">
            <div class="absolute inset-0 bg-black/80 backdrop-blur-sm" wire:click="cancelDelete"></div>
            <div class="bg-ep-dark border border-white/10 rounded-[3rem] p-10 max-w-lg w-full relative z-10 shadow-2xl text-center overflow-hidden">
                <div class="absolute inset-x-0 top-0 h-1 bg-red-500/50"></div>
                <div class="w-20 h-20 bg-red-500/20 rounded-3xl flex items-center justify-center mx-auto mb-6 text-red-500 border border-red-500/20">
                    <svg class="w-10 h-10 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <h3 class="text-xl font-black text-white uppercase italic tracking-widest mb-4">Revocar Acceso</h3>
                <p class="text-sm text-white/40 mb-10 font-bold leading-relaxed italic uppercase">
                    ESTÁ A PUNTO DE DAR DE BAJA A <span class="text-red-400">{{ $userToDeleteName }}</span>.<br><br>
                    EL REGISTRO NO SE ELIMINARÁ PERMANENTEMENTE PARA PRESERVAR EL HISTORIAL FORENSE, PERO EL ACCESO SERÁ REVOCADO DE INMEDIATO.
                </p>
                <div class="flex justify-center gap-4">
                    <button wire:click="cancelDelete" class="px-8 py-4 bg-white/5 text-white/40 hover:text-white rounded-2xl text-[10px] font-black uppercase tracking-widest italic transition-all">
                        Cancelar
                    </button>
                    <button wire:click="delete" class="px-10 py-4 bg-red-600 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest italic transition-all shadow-lg shadow-red-600/30 animate-pulse">
                        Confirmar Baja
                    </button>
                </div>
            </div>
        </div>
    @endif

    <style>
        .animate-fade-in { animation: fade-in 0.4s ease-out forwards; }
        .animate-fade-in-down { animation: fade-in-down 0.6s ease-out forwards; }
        .animate-fade-in-up { animation: fade-in-up 0.5s ease-out forwards; }
        @keyframes fade-in { from { opacity: 0; } to { opacity: 1; } }
        @keyframes fade-in-down { from { opacity: 0; transform: translateY(-30px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes fade-in-up { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }

        .pagination-tactical nav div:first-child { display: none; }
        .pagination-tactical nav span, .pagination-tactical nav a {
            background-color: transparent !important;
            border: 1px solid rgba(255,255,255,0.05) !important;
            color: rgba(255,255,255,0.2) !important;
            border-radius: 12px !important; margin: 0 4px !important; padding: 8px 14px !important;
            font-weight: 900 !important; font-size: 10px !important; transition: all 0.3s !important;
        }
        .pagination-tactical nav span[aria-current="page"] {
            border-color: rgba(246,146,57,0.3) !important;
            color: #f69239 !important;
            background: rgba(246,146,57,0.1) !important;
        }
    </style>
</div>
