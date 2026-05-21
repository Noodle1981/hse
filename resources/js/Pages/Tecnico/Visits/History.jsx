import { Head, Link, router } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { useState, useMemo, useEffect } from 'react';
import { route } from 'ziggy-js';

export default function History({ auth = {}, visits = { data: [] }, filters = {} }) {
    const [search, setSearch] = useState(filters.search || '');
    const [status, setStatus] = useState(filters.status || 'all');

    // Helper para rutas seguras
    const safeRoute = (name, params = null) => {
        try {
            return route(name, params);
        } catch (e) {
            console.warn(`[Ziggy] Route "${name}" not found.`);
            return '#';
        }
    };

    const updateFilters = (newFilters) => {
        router.get(safeRoute('tecnico.visits.index'), {
            search,
            status,
            ...newFilters
        }, { preserveState: true, preserveScroll: true });
    };

    const calculateDuration = (start, end) => {
        if (!start || !end) return null;
        try {
            // Asumiendo formato HH:mm:ss o HH:mm
            const [sh, sm] = start.split(':').map(Number);
            const [eh, em] = end.split(':').map(Number);
            
            const startMins = sh * 60 + sm;
            const endMins = eh * 60 + em;
            
            let diff = endMins - startMins;
            if (diff < 0) diff += 1440; // Manejo de cambio de día si aplica

            const hrs = Math.floor(diff / 60);
            const mins = diff % 60;
            return { hrs, mins, totalMins: diff, isExcessive: hrs >= 8 };
        } catch (e) { return null; }
    };

    return (
        <AuthenticatedLayout 
            user={auth?.user} 
            header={
                <div className="flex flex-col">
                    <h1 className="text-xs font-black text-ep-primary uppercase tracking-widest italic leading-none mb-1">
                        Archivo de <span className="text-white">Visitas</span>
                    </h1>
                    <h2 className="text-sm font-bold text-indigo-100 uppercase tracking-tight leading-none italic">
                        Auditoría de Campo y Certificados
                    </h2>
                </div>
            }
        >
            <Head title="Historial de Visitas" />

            <div className="bg-[#11111d] min-h-screen selection:bg-ep-primary/30">
                <div className="p-6 lg:p-12">
                    <div className="max-w-[100rem] mx-auto space-y-10">
                        

                        {/* TOOLBAR DE FILTROS */}
                        <div className="bg-[#1a1a2e] rounded-[2.5rem] border border-white/5 p-8 flex flex-col xl:flex-row gap-8 items-end shadow-2xl animate-in fade-in slide-in-from-left-4 duration-700 delay-100">
                            <div className="flex-1 w-full space-y-3">
                                <label className="text-[9px] font-black text-ep-primary uppercase italic tracking-[0.4em] ml-1 block opacity-60">Buscador Inteligente</label>
                                <div className="relative group">
                                    <div className="absolute inset-y-0 left-5 flex items-center pointer-events-none text-white/10 group-focus-within:text-ep-primary transition-colors">
                                        <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" strokeWidth="2.5"/></svg>
                                    </div>
                                    <input id="search" name="search" 
                                        type="text" 
                                        placeholder="BUSCAR POR SEDE, EMPRESA O CÓDIGO..."
                                        value={search}
                                        onChange={e => setSearch(e.target.value)}
                                        onKeyPress={e => e.key === 'Enter' && updateFilters({ search })}
                                        className="w-full bg-black/40 border border-white/5 rounded-2xl pl-14 pr-6 py-4 text-xs font-bold text-white placeholder-white/5 italic focus:ring-ep-primary/50 transition-all uppercase"
                                    />
                                </div>
                            </div>

                            <div className="w-full xl:w-64 space-y-3">
                                <label className="text-[9px] font-black text-indigo-400 uppercase italic tracking-[0.4em] ml-1 block opacity-60">Estado Operativo</label>
                                <select id="status" name="status" 
                                    value={status}
                                    onChange={e => { setStatus(e.target.value); updateFilters({ status: e.target.value }); }}
                                    className="w-full bg-black/40 border border-white/5 rounded-2xl px-6 py-4 text-xs font-bold text-white italic focus:ring-ep-primary/50 transition-all uppercase [color-scheme:dark]"
                                >
                                    <option value="all">TODOS LOS ESTADOS</option>
                                    <option value="in_progress">EN PROGRESO / MASTER</option>
                                    <option value="completed">COMPLETADA (CIERRE)</option>
                                    <option value="signed">AUDITADA (FIRMADA)</option>
                                </select>
                            </div>

                            <button 
                                onClick={() => { setSearch(''); setStatus('all'); updateFilters({ search: '', status: 'all' }); }}
                                className="h-14 px-8 bg-white/5 border border-white/10 text-white/40 hover:text-white hover:bg-white/10 rounded-2xl font-black text-[9px] uppercase tracking-[0.2em] italic transition-all flex items-center justify-center gap-3 active:scale-95 group/clear whitespace-nowrap"
                            >
                                <svg className="w-4 h-4 group-hover:rotate-90 transition-transform duration-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 4l16 16m0-16L4 20" strokeWidth="2.5"/></svg>
                                Limpiar Auditoría
                            </button>
                        </div>

                        {/* MATRIZ DE HISTORIAL */}
                        <div className="bg-[#1a1a2e]/40 backdrop-blur-3xl rounded-[3rem] border border-white/5 shadow-3xl overflow-hidden animate-in fade-in slide-in-from-bottom-4 duration-700 delay-200">
                            <div className="overflow-x-auto">
                                <table className="w-full text-left">
                                    <thead>
                                        <tr className="bg-black/20 text-[10px] font-black text-white/20 uppercase italic tracking-[0.3em] border-b border-white/5">
                                            <th className="px-10 py-6">Cronología</th>
                                            <th className="px-8 py-6">Sede / Protocolo</th>
                                            <th className="px-8 py-6">Rendimiento Temporal</th>
                                            <th className="px-8 py-6">Estado Operativo</th>
                                            <th className="px-10 py-6 text-right">Acciones HUD</th>
                                        </tr>
                                    </thead>
                                    <tbody className="divide-y divide-white/[0.03]">
                                        {visits?.data?.length > 0 ? visits.data.map((visit) => {
                                            const duration = calculateDuration(visit.start_time, visit.end_time);
                                            return (
                                                <tr key={visit.id} className="group hover:bg-white/[0.02] transition-colors">
                                                    <td className="px-10 py-8">
                                                        <div className="flex flex-col">
                                                            <span className="text-sm font-black text-white italic leading-none mb-1 tracking-tight">{new Date(visit.visit_date).toLocaleDateString()}</span>
                                                            <span className="text-[9px] font-black text-ep-primary uppercase tracking-widest italic flex items-center gap-2">
                                                                <svg className="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 8v4l3 3" strokeWidth="2.5"/></svg>
                                                                {visit.start_time?.substring(0, 5) || '--:--'} • {visit.end_time?.substring(0, 5) || '--:--'}
                                                            </span>
                                                        </div>
                                                    </td>

                                                    <td className="px-8 py-8">
                                                        <div>
                                                            <div className="text-sm font-black text-white italic uppercase tracking-tight leading-none mb-1 group-hover:text-ep-primary transition-colors">{visit.sede?.nombre_sede || 'N/A'}</div>
                                                            <div className="text-[9px] text-white/30 font-black uppercase tracking-widest italic leading-none flex items-center gap-2">
                                                                <svg className="w-3 h-3 text-white/10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5" strokeWidth="2"/></svg>
                                                                {visit.sede?.empresa?.nombre || 'Empresa Local'}
                                                            </div>
                                                        </div>
                                                    </td>

                                                    <td className="px-8 py-8">
                                                        {duration ? (
                                                            duration.isExcessive ? (
                                                                <div className="inline-flex items-center px-4 py-2 bg-red-500/10 border border-red-500/20 rounded-xl text-red-500 animate-pulse group/alert relative">
                                                                    <svg className="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" strokeWidth="2.5"/></svg>
                                                                    <span className="text-xs font-black italic tracking-tighter uppercase">{duration.hrs}h {duration.mins}m</span>
                                                                    <div className="absolute -bottom-8 left-1/2 -translate-x-1/2 px-3 py-1 bg-red-600 text-[7px] font-black text-white uppercase rounded-lg opacity-0 group-hover/alert:opacity-100 transition-opacity whitespace-nowrap z-30">POSIBLE OLVIDO DE CIERRE</div>
                                                                </div>
                                                            ) : (
                                                                <div className="flex flex-col">
                                                                    <span className="text-sm font-black text-white/40 italic tracking-tight">{duration.hrs}h {duration.mins}m</span>
                                                                    <span className="text-[8px] font-bold text-white/10 uppercase tracking-widest italic">Tiempo de Nodo</span>
                                                                </div>
                                                            )
                                                        ) : (
                                                            <div className="inline-flex items-center px-3 py-1.5 bg-ep-primary/10 border border-ep-primary/20 rounded-xl text-ep-primary">
                                                                <div className="w-1.5 h-1.5 bg-ep-primary rounded-full animate-ping mr-2 shadow-[0_0_8px_currentColor]"></div>
                                                                <span className="text-[9px] font-black uppercase tracking-widest italic leading-none">Misión Viva</span>
                                                            </div>
                                                        )}
                                                    </td>

                                                    <td className="px-8 py-8">
                                                        <span className={`px-4 py-1.5 rounded-xl text-[9px] font-black uppercase italic tracking-widest border shadow-lg transition-all
                                                            ${visit.status === 'signed' ? 'text-indigo-400 bg-indigo-500/5 border-indigo-500/20 shadow-indigo-500/10' : 
                                                              visit.status === 'completed' ? 'text-emerald-400 bg-emerald-500/5 border-emerald-500/20 shadow-emerald-500/10' : 
                                                              'text-ep-primary bg-ep-primary/5 border-ep-primary/20 shadow-ep-primary/10'}
                                                        `}>
                                                            {visit.status === 'signed' ? 'AUDITADA' : visit.status === 'completed' ? 'COMPLETADA' : 'EN PROGRESO'}
                                                        </span>
                                                    </td>

                                                    <td className="px-10 py-8 text-right">
                                                        <div className="flex items-center justify-end gap-3">
                                                            {visit.status === 'in_progress' ? (
                                                                <Link href={safeRoute('tecnico.visits.in-progress', visit.id)} className="px-8 py-3 bg-ep-primary text-white rounded-2xl text-[10px] font-black uppercase italic tracking-[0.2em] shadow-xl hover:scale-105 active:scale-95 transition-all">
                                                                    Monitorizar
                                                                </Link>
                                                            ) : (
                                                                <>
                                                                    <a 
                                                                        href={safeRoute('tecnico.visits.certificate.loading', visit.id)} 
                                                                        target="_blank"
                                                                        className="w-10 h-10 bg-white/5 border border-white/5 rounded-xl flex items-center justify-center text-white/40 hover:text-white hover:bg-white/10 transition-all group/btn"
                                                                        title="Previsualizar Auditoría"
                                                                    >
                                                                        <svg className="w-4 h-4 group-hover/btn:scale-125 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" strokeWidth="2.5"/></svg>
                                                                    </a>
                                                                    <a 
                                                                        href={safeRoute('tecnico.visits.certificate.download', visit.id)} 
                                                                        className="w-10 h-10 bg-white/5 border border-white/5 rounded-xl flex items-center justify-center text-white/40 hover:text-emerald-500 hover:bg-emerald-500/10 transition-all group/btn"
                                                                        title="Descargar Certificado"
                                                                    >
                                                                        <svg className="w-4 h-4 group-hover/btn:translate-y-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" strokeWidth="2.5"/></svg>
                                                                    </a>
                                                                </>
                                                            )}
                                                        </div>
                                                    </td>
                                                </tr>
                                            );
                                        }) : (
                                            <tr>
                                                <td colSpan="5" className="px-10 py-32 text-center">
                                                    <div className="flex flex-col items-center opacity-10">
                                                        <svg className="w-20 h-20 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" strokeWidth="1"/></svg>
                                                        <span className="text-sm font-black uppercase italic tracking-[0.4em]">Cero Registros Detectados en Auditoría</span>
                                                    </div>
                                                </td>
                                            </tr>
                                        )}
                                    </tbody>
                                </table>
                            </div>

                            {/* PAGINACIÓN TÁCTICA */}
                            {visits?.links && (
                                <div className="px-10 py-8 bg-black/20 border-t border-white/5 flex items-center justify-between">
                                    <div className="text-[10px] font-black text-white/10 uppercase tracking-widest italic">Página {visits.current_page} de {visits.last_page}</div>
                                    <div className="flex gap-2">
                                        {visits.links.map((link, i) => (
                                            <Link 
                                                key={i}
                                                href={link.url || '#'}
                                                dangerouslySetInnerHTML={{ __html: link.label }}
                                                className={`px-4 py-2 rounded-xl text-[10px] font-black transition-all ${link.active ? 'bg-ep-primary text-white shadow-lg' : 'bg-white/5 text-white/20 hover:bg-white/10'} ${!link.url && 'opacity-20 cursor-not-allowed'}`}
                                            />
                                        ))}
                                    </div>
                                </div>
                            )}
                        </div>
                    </div>
                </div>
            </div>

            <style dangerouslySetInnerHTML={{ __html: `
                .shadow-3xl { filter: drop-shadow(0 35px 60px rgba(0,0,0,0.6)); }
                select { background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%23ffffff20' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e"); background-position: right 1rem center; background-repeat: no-repeat; background-size: 1.5em 1.5em; padding-right: 2.5rem; -webkit-appearance: none; -moz-appearance: none; appearance: none; }
            `}} />
        </AuthenticatedLayout>
    );
}
