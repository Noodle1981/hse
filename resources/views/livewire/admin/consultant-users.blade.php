<div class="min-h-screen bg-[#11111d] p-4 lg:p-8">
    {{-- Tactical Header --}}
    <div class="mb-10 flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
            <div class="flex items-center space-x-2 text-[10px] font-black text-white/20 uppercase tracking-[0.3em] mb-3">
                <a href="{{ route('admin.consultants') }}" class="hover:text-ep-primary transition-colors">Sistema Global</a>
                <span>/</span>
                <span class="text-ep-primary">Gestión de Operadores</span>
            </div>
            <h2 class="text-4xl font-black text-white uppercase italic tracking-tighter">
                Usuarios: <span class="text-ep-primary shadow-[0_0_20px_#4338ca/30]">{{ $consultant->nombre }}</span>
            </h2>
            <p class="text-white/40 mt-2 font-medium tracking-wide">Administración de credenciales y permisos para el despliegue técnico del nodo.</p>
        </div>
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.consultants') }}" class="group flex items-center px-6 py-4 bg-white/5 backdrop-blur-md border border-white/10 text-white text-[10px] font-black uppercase tracking-[0.2em] rounded-2xl hover:bg-white/10 transition-all shadow-xl">
                <svg class="w-5 h-5 mr-2 text-white/40 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Volver
            </a>
            <button wire:click="create" class="group relative flex items-center px-8 py-4 bg-ep-primary text-white text-[10px] font-black uppercase italic tracking-[0.2em] rounded-2xl shadow-[0_0_30px_rgba(67,56,202,0.4)] hover:brightness-110 active:scale-95 transition-all overflow-hidden">
                <span class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></span>
                <svg class="w-5 h-5 mr-2 group-hover:rotate-90 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                Alta de Operador
            </button>
        </div>
    </div>

    {{-- Performance Metrics --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
        {{-- Stat: Total Users --}}
        <div class="bg-white/5 backdrop-blur-xl border border-white/10 p-6 rounded-[2rem] shadow-2xl relative overflow-hidden group">
            <div class="absolute -right-6 -top-6 w-24 h-24 bg-ep-primary/10 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-700"></div>
            <div class="relative z-10">
                <p class="text-[10px] font-black text-white/40 uppercase tracking-widest mb-1 italic text-right">Usuarios Registrados</p>
                <div class="flex items-end justify-between">
                    <div class="w-10 h-10 rounded-xl bg-ep-primary/20 flex items-center justify-center text-ep-primary shadow-lg border border-ep-primary/20">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                    <div class="text-4xl font-black text-white italic tracking-tighter">{{ $users->total() }}</div>
                </div>
            </div>
        </div>

        {{-- Stat: License Limit --}}
        <div class="bg-white/5 backdrop-blur-xl border border-white/10 p-6 rounded-[2rem] shadow-2xl relative overflow-hidden group">
            <div class="absolute -right-6 -top-6 w-24 h-24 bg-yellow-400/10 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-700"></div>
            <div class="relative z-10">
                <p class="text-[10px] font-black text-white/40 uppercase tracking-widest mb-1 italic text-right">Techo de Licencias</p>
                <div class="flex items-end justify-between">
                    <div class="w-10 h-10 rounded-xl bg-yellow-400/20 flex items-center justify-center text-yellow-500 shadow-lg border border-yellow-400/20">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <div class="text-4xl font-black text-white italic tracking-tighter">{{ $consultant->max_tecnicos ?? 'INF' }}</div>
                </div>
            </div>
        </div>

        {{-- Stat: Utilization Bar --}}
        <div class="lg:col-span-2 bg-white/5 backdrop-blur-xl border border-white/10 p-8 rounded-[2rem] shadow-2xl flex flex-col justify-center">
            <div class="flex justify-between items-center mb-3">
                <span class="text-[10px] font-black text-white/60 uppercase tracking-[0.2em] italic">Consumo de Infraestructura</span>
                @php 
                    $usagePercent = $consultant->max_tecnicos > 0 ? min(100, ($users->total() / $consultant->max_tecnicos) * 100) : 0;
                @endphp
                <span class="text-xs font-black text-ep-primary tracking-widest">{{ round($usagePercent) }}%</span>
            </div>
            <div class="h-3 bg-black/40 rounded-full overflow-hidden border border-white/5 shadow-inner">
                <div class="h-full bg-gradient-to-r from-ep-primary to-indigo-400 shadow-[0_0_15px_#4338ca] transition-all duration-1000 ease-out" 
                     style="width: <?php echo $usagePercent; ?>%"></div>
            </div>
        </div>
    </div>

    {{-- Tactical Table Card --}}
    <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-[2.5rem] shadow-2xl relative overflow-hidden group">
        <div class="absolute -bottom-20 -left-20 w-80 h-80 bg-ep-primary/5 rounded-full blur-[100px]"></div>
        
        <table class="min-w-full relative z-10">
            <thead>
                <tr class="border-b border-white/10">
                    <th class="px-8 py-6 text-left text-[10px] font-black text-white/20 uppercase tracking-[0.3em]">Operador / Identidad</th>
                    <th class="px-8 py-6 text-left text-[10px] font-black text-white/20 uppercase tracking-[0.3em]">Nivel de Acceso</th>
                    <th class="px-8 py-6 text-center text-[10px] font-black text-white/20 uppercase tracking-[0.3em]">Estado Nodo</th>
                    <th class="px-8 py-6 text-right text-[10px] font-black text-white/20 uppercase tracking-[0.3em]">Acciones de Comando</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @forelse($users as $user)
                <tr class="group/row hover:bg-white/[0.03] transition-all duration-300">
                    <td class="px-8 py-6">
                        <div class="flex items-center">
                            <div class="relative">
                                <div class="absolute -inset-1 bg-ep-primary/20 rounded-xl blur opacity-0 group-hover/row:opacity-100 transition-opacity"></div>
                                <div class="relative h-12 w-12 rounded-xl bg-black/40 border border-white/10 flex items-center justify-center text-white font-black italic text-lg shadow-lg">
                                    {{ substr($user->nombres, 0, 1) }}{{ substr($user->apellidos, 0, 1) }}
                                </div>
                            </div>
                            <div class="ml-5">
                                <div class="text-base font-black text-white tracking-tight group-hover/row:text-ep-primary transition-colors uppercase italic">{{ $user->nombres }} {{ $user->apellidos }}</div>
                                <div class="text-xs font-medium text-white/30 tracking-wide font-mono">{{ $user->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-8 py-6">
                        <span class="inline-flex items-center px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-[0.15em] italic {{ $user->isGestor() ? 'bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 shadow-[0_0_10px_#6366f1/10]' : 'bg-white/5 text-white/40 border border-white/10' }}">
                            <span class="w-1.5 h-1.5 rounded-full bg-current mr-2 animate-pulse"></span>
                            {{ $user->rol }}
                        </span>
                    </td>
                    <td class="px-8 py-6 text-center">
                        <div class="flex flex-col items-center">
                            @if($user->estado === 'activo')
                                <div class="flex items-center space-x-2">
                                    <div class="w-2 h-2 rounded-full bg-green-500 shadow-[0_0_8px_#22c55e]"></div>
                                    <span class="text-[10px] font-black text-green-500 uppercase tracking-widest italic">En Línea</span>
                                </div>
                            @else
                                <div class="flex items-center space-x-2 opacity-40">
                                    <div class="w-2 h-2 rounded-full bg-red-500"></div>
                                    <span class="text-[10px] font-black text-red-500 uppercase tracking-widest italic">Inactivo</span>
                                </div>
                            @endif
                        </div>
                    </td>
                    <td class="px-8 py-6 text-right whitespace-nowrap">
                        <div class="flex items-center justify-end space-x-4 transition-all">
                            <button wire:click="edit({{ $user->id }})" class="p-2.5 bg-white/5 border border-white/10 text-white rounded-xl hover:bg-ep-primary/20 hover:text-ep-primary transition-all shadow-lg" title="Editar Operador">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 00-2 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-8 py-20 text-center">
                        <div class="flex flex-col items-center">
                            <div class="w-20 h-20 rounded-3xl bg-white/5 border border-white/10 flex items-center justify-center text-white/10 mb-6 border-dashed">
                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                            </div>
                            <h3 class="text-xl font-black text-white uppercase italic tracking-tighter">Sin Personal Activo</h3>
                            <p class="text-white/40 mt-2 text-sm font-medium">El nodo no tiene operadores registrados en la base actual.</p>
                            <button wire:click="create" class="mt-8 px-6 py-3 bg-white border border-white/10 text-black text-xs font-black uppercase tracking-widest rounded-xl hover:bg-white/90 transition-all shadow-xl">
                                Iniciar Alta Rápidamente
                            </button>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        
        @if($users->hasPages())
        <div class="px-8 py-6 bg-black/20 border-t border-white/5">
            {{ $users->links() }}
        </div>
        @endif
    </div>

    {{-- Tactical Modal --}}
    @if($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
        {{-- Backdrop con Desenfoque Extremo --}}
        <div class="absolute inset-0 bg-black/60 backdrop-blur-xl transition-all duration-500" wire:click="$set('showModal', false)"></div>
        
        {{-- Modal Container --}}
        <div class="relative w-full max-w-xl bg-[#1a1b26] rounded-[2.5rem] border border-white/10 shadow-[0_0_100px_rgba(0,0,0,0.8)] overflow-hidden transform transition-all duration-300 animate-in fade-in zoom-in slide-in-from-bottom-10" 
             x-data x-on:keydown.escape.window="$wire.set('showModal', false)">
            
            <div class="absolute top-0 right-0 w-40 h-40 bg-ep-primary/5 rounded-full blur-[60px]"></div>
            
            <form wire:submit.prevent="save">
                <div class="p-8 border-b border-white/5 flex items-center justify-between">
                    <div>
                        <h3 class="text-2xl font-black text-white uppercase italic tracking-tighter">
                            {{ $editingId ? 'Editar Detalle del Operator' : 'Registro de Nuevo Operativo' }}
                        </h3>
                        <p class="text-white/30 text-[9px] font-bold uppercase tracking-[.2em] mt-1">Configuración de Credenciales de Acceso</p>
                    </div>
                    <button type="button" wire:click="$set('showModal', false)" class="p-2 text-white/20 hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="p-8 space-y-6">
                    <div class="grid grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="block text-[9px] font-black text-white/40 uppercase tracking-widest italic ml-1">Nombres del Operativo</label>
                            <input type="text" wire:model="nombres" 
                                   class="w-full bg-black/40 border-white/10 rounded-2xl text-white placeholder-white/10 focus:ring-ep-primary/30 py-4 px-5 text-sm font-bold shadow-inner">
                            @error('nombres') <span class="text-red-400 text-[9px] font-bold uppercase italic">{{ $message }}</span> @enderror
                        </div>
                        <div class="space-y-2">
                            <label class="block text-[9px] font-black text-white/40 uppercase tracking-widest italic ml-1">Apellidos Completos</label>
                            <input type="text" wire:model="apellidos" 
                                   class="w-full bg-black/40 border-white/10 rounded-2xl text-white placeholder-white/10 focus:ring-ep-primary/30 py-4 px-5 text-sm font-bold shadow-inner">
                            @error('apellidos') <span class="text-red-400 text-[9px] font-bold uppercase italic">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-[9px] font-black text-white/40 uppercase tracking-widest italic ml-1">Identificador Único (Email)</label>
                        <input type="email" wire:model="email" 
                               class="w-full bg-black/40 border-white/10 rounded-2xl text-white placeholder-white/10 focus:ring-ep-primary/30 py-4 px-5 text-sm font-mono shadow-inner">
                        @error('email') <span class="text-red-400 text-[9px] font-bold uppercase italic">{{ $message }}</span> @enderror
                    </div>

                    <div class="space-y-2">
                        <label class="block text-[9px] font-black text-white/40 uppercase tracking-widest italic ml-1">Clave de Encriptación (Password)</label>
                        <div class="relative">
                            <input type="password" wire:model="password" 
                                   class="w-full bg-black/40 border-white/10 rounded-2xl text-white placeholder-white/10 focus:ring-ep-primary/30 py-4 px-5 text-sm shadow-inner"
                                   placeholder="{{ $editingId ? 'Mantener actual...' : 'Defina una clave segura' }}">
                        </div>
                        @if($editingId) 
                            <p class="text-white/20 text-[8px] font-medium uppercase tracking-wider italic">Deje vacío el campo para no alterar la clave actual del nodo.</p>
                        @endif
                        @error('password') <span class="text-red-400 text-[9px] font-bold uppercase italic">{{ $message }}</span> @enderror
                    </div>

                    <div class="space-y-4 pt-4 border-t border-white/5">
                        <label class="block text-[9px] font-black text-white/40 uppercase tracking-widest italic ml-1">Rango / Nivel de Comando</label>
                        <div class="grid grid-cols-2 gap-4">
                            <label class="relative cursor-pointer group/radio">
                                <input type="radio" wire:model="rol" value="gestor" class="peer absolute opacity-0 invisible">
                                <div class="w-full p-4 rounded-2xl bg-black/40 border border-white/5 text-center peer-checked:border-ep-primary peer-checked:bg-ep-primary/5 transition-all shadow-inner">
                                    <div class="text-[10px] font-black text-white/40 peer-checked:text-ep-primary uppercase tracking-widest group-hover/radio:text-white/60 transition-colors">Gestor Macro</div>
                                </div>
                            </label>
                            <label class="relative cursor-pointer group/radio">
                                <input type="radio" wire:model="rol" value="tecnico" class="peer absolute opacity-0 invisible">
                                <div class="w-full p-4 rounded-2xl bg-black/40 border border-white/5 text-center peer-checked:border-ep-primary peer-checked:bg-ep-primary/5 transition-all shadow-inner">
                                    <div class="text-[10px] font-black text-white/40 peer-checked:text-ep-primary uppercase tracking-widest group-hover/radio:text-white/60 transition-colors">Técnico Nivel 1</div>
                                </div>
                            </label>
                        </div>
                        @error('rol') <span class="text-red-400 text-[9px] font-bold uppercase italic">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="p-8 bg-black/20 flex items-center justify-between">
                    <button type="button" wire:click="$set('showModal', false)" class="text-[10px] font-black text-white/20 hover:text-white uppercase tracking-widest italic transition-colors">Cancelar Misión</button>
                    <button type="submit" 
                            class="group relative inline-flex items-center px-10 py-4 bg-ep-primary text-white text-[11px] font-black uppercase italic tracking-[0.2em] rounded-xl shadow-[0_0_30px_rgba(67,56,202,0.5)] active:scale-95 transition-all overflow-hidden hover:brightness-110">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Sincronizar Datos
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
