import { Head, Link, router } from '@inertiajs/react';
import AuthenticatedLayout from '../../Layouts/AuthenticatedLayout';
import { useState } from 'react';

export default function MyAssignments({ inspections, trainings, reports, kpis, activeVisit, filters, empresas, sedes }) {
    const [activeTab, setActiveTab] = useState(filters.tab || 'inspections');
    const [search, setSearch] = useState(filters.search || '');
    const [selectedMonth, setSelectedMonth] = useState(filters.month || new Date().getMonth() + 1);
    const [empresaId, setEmpresaId] = useState(filters.empresa_id || '');
    const [sedeId, setSedeId] = useState(filters.sede_id || '');

    const months = [
        { id: 1, name: 'Enero' }, { id: 2, name: 'Febrero' }, { id: 3, name: 'Marzo' },
        { id: 4, name: 'Abril' }, { id: 5, name: 'Mayo' }, { id: 6, name: 'Junio' },
        { id: 7, name: 'Julio' }, { id: 8, name: 'Agosto' }, { id: 9, name: 'Septiembre' },
        { id: 10, name: 'Octubre' }, { id: 11, name: 'Noviembre' }, { id: 12, name: 'Diciembre' }
    ];

    const updateFilters = (newFilters) => {
        router.get(route('tecnico.checklists.tasks.index'), {
            tab: activeTab,
            search,
            month: selectedMonth,
            empresa_id: empresaId,
            sede_id: sedeId,
            ...newFilters
        }, { preserveState: true });
    };

    const getStatusBadge = (status) => {
        const styles = {
            'completed': 'bg-emerald-500/10 text-emerald-500 border-emerald-500/20',
            'finalizado': 'bg-emerald-500/10 text-emerald-500 border-emerald-500/20',
            'in_progress': 'bg-blue-500/10 text-blue-500 border-blue-500/20 animate-pulse',
            'scheduled': 'bg-white/5 text-white/40 border-white/10',
            'pending': 'bg-yellow-500/10 text-yellow-500 border-yellow-500/20',
        };
        return styles[status] || styles['scheduled'];
    };

    const renderList = () => {
        const data = activeTab === 'inspections' ? inspections : (activeTab === 'trainings' ? trainings : reports);
        
        if (data.length === 0) {
            return (
                <div className="py-24 text-center opacity-20 italic">
                    <p className="text-sm font-black uppercase tracking-[0.4em]">Sin misiones asignadas para este periodo</p>
                </div>
            );
        }

        return (
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 animate-fade-in-up">
                {data.map((item) => (
                    <div key={item.id} className="bg-[#16162a]/40 backdrop-blur-md rounded-[2.5rem] border border-white/5 p-8 relative group hover:border-white/20 transition-all shadow-2xl overflow-hidden">
                        <div className={`absolute top-0 right-0 w-1.5 h-full ${item.status === 'completed' ? 'bg-emerald-500' : (item.status === 'in_progress' ? 'bg-blue-500' : 'bg-white/5')}`}></div>
                        
                        <div className="flex justify-between items-start mb-6">
                            <span className={`px-4 py-1 rounded-full text-[8px] font-black uppercase tracking-widest italic border ${getStatusBadge(item.status)}`}>
                                {item.status.replace('_', ' ')}
                            </span>
                            <div className="text-[9px] font-black text-white/20 uppercase italic">
                                {new Date(item.due_date).toLocaleDateString()}
                            </div>
                        </div>

                        <h3 className="text-lg font-black text-white italic uppercase tracking-tighter leading-tight mb-4 group-hover:text-blue-400 transition-colors">
                            {item.title}
                        </h3>

                        <div className="space-y-2 mb-8">
                            <div className="flex items-center gap-2 text-[10px] font-bold text-white/40 uppercase italic">
                                <svg className="w-3 h-3 text-white/20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m4 0h1m-5 4h1m4 0h1m-5 4h1m4 0h1"/></svg>
                                {item.sede}
                            </div>
                            <div className="text-[8px] font-black text-white/20 uppercase tracking-widest italic ml-5">
                                {item.empresa}
                            </div>
                        </div>

                        <div className="flex items-center justify-between pt-6 border-t border-white/5">
                            {item.status !== 'completed' && item.status !== 'finalizado' ? (
                                <Link 
                                    href={activeTab === 'inspections' ? route('tecnico.checklists.fill', item.checklist_id) : '#'} 
                                    className="px-8 py-3 bg-blue-600 text-white rounded-xl text-[10px] font-black uppercase italic tracking-widest shadow-lg shadow-blue-600/20 hover:brightness-110 active:scale-95 transition-all"
                                >
                                    {item.status === 'in_progress' ? 'Continuar' : 'Iniciar Misión'}
                                </Link>
                            ) : (
                                <span className="text-[10px] font-black text-emerald-500 uppercase italic tracking-widest">
                                    Misión Cumplida
                                </span>
                            )}
                        </div>
                    </div>
                ))}
            </div>
        );
    };

    return (
        <AuthenticatedLayout title="Consola del Técnico">
            <Head title="Mis Asignaciones" />

            <div className="px-4 py-8 lg:p-12 text-white selection:bg-blue-500/30">
                <div className="max-w-7xl mx-auto space-y-10 animate-fade-in">
                    
                    {/* CABECERA Y TABLERO DE CONTROL */}
                    <div className="grid grid-cols-1 lg:grid-cols-4 gap-6">
                        <div className="lg:col-span-1 bg-[#16162a]/60 backdrop-blur-xl rounded-[2.5rem] border border-white/5 p-8 shadow-2xl flex flex-col justify-center">
                            <h1 className="text-3xl font-black uppercase italic tracking-tighter leading-none mb-2">Centro de<br/><span className="text-blue-500">Operaciones</span></h1>
                            <p className="text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic">Dashboard Técnico</p>
                        </div>

                        <div className="bg-[#16162a]/40 border border-white/5 p-8 rounded-[2.5rem] flex items-center gap-6 group hover:border-blue-500/30 transition-all">
                            <div className="w-14 h-14 rounded-2xl bg-blue-500/10 flex items-center justify-center text-blue-500 shadow-lg">
                                <svg className="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                            </div>
                            <div>
                                <div className="text-3xl font-black italic">{kpis.total_tasks}</div>
                                <div className="text-[9px] font-black text-white/30 uppercase tracking-widest italic leading-tight">Misiones<br/>Asignadas</div>
                            </div>
                        </div>

                        <div className="bg-[#16162a]/40 border border-white/5 p-8 rounded-[2.5rem] flex items-center gap-6 group hover:border-emerald-500/30 transition-all">
                            <div className="w-14 h-14 rounded-2xl bg-emerald-500/10 flex items-center justify-center text-emerald-500 shadow-lg">
                                <svg className="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2.5" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <div>
                                <div className="text-3xl font-black italic">{kpis.completed}</div>
                                <div className="text-[9px] font-black text-white/30 uppercase tracking-widest italic leading-tight">Misiones<br/>Finalizadas</div>
                            </div>
                        </div>

                        <div className="bg-[#16162a]/40 border border-white/5 p-8 rounded-[2.5rem] flex items-center gap-6 group hover:border-red-500/30 transition-all text-red-500">
                            <div className="w-14 h-14 rounded-2xl bg-red-500/10 flex items-center justify-center shadow-lg">
                                <svg className="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            </div>
                            <div>
                                <div className="text-3xl font-black italic text-white">{kpis.critical_findings}</div>
                                <div className="text-[9px] font-black text-white/30 uppercase tracking-widest italic leading-tight">Desviaciones<br/>Críticas</div>
                            </div>
                        </div>
                    </div>

                    {/* ESTADO DE VISITA ACTIVA */}
                    {activeVisit && (
                        <div className="bg-blue-600/80 backdrop-blur-xl border border-white/20 rounded-[2.5rem] p-8 flex flex-col md:flex-row items-center justify-between gap-8 shadow-2xl relative overflow-hidden group">
                           <div className="absolute top-0 right-0 p-8 opacity-10 group-hover:scale-110 transition-transform">
                                <svg className="w-32 h-32" fill="currentColor" viewBox="0 0 24 24"><path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m4 0h1m-5 4h1m4 0h1m-5 4h1m4 0h1"/></svg>
                           </div>
                           <div className="flex items-center gap-6">
                               <div className="w-16 h-16 rounded-2xl bg-white/20 flex items-center justify-center text-white border border-white/20 animate-pulse">
                                   <div className="w-3 h-3 bg-white rounded-full"></div>
                               </div>
                               <div>
                                   <div className="text-[10px] font-black text-white/60 uppercase tracking-widest italic mb-1">Visita en Progreso Detectada</div>
                                   <div className="text-2xl font-black italic uppercase tracking-tighter">{activeVisit.sede?.nombre_sede}</div>
                               </div>
                           </div>
                           <Link 
                                href={route('tecnico.visits.in-progress', activeVisit.id)}
                                className="px-12 py-5 bg-white text-blue-600 rounded-[2rem] text-xs font-black uppercase italic tracking-[0.2em] shadow-2xl hover:bg-blue-50 transition-all active:scale-95"
                           >
                               Volver a la Consola
                           </Link>
                        </div>
                    )}

                    {/* FILTROS Y TABS */}
                    <div className="space-y-6">
                        <div className="bg-[#16162a]/40 backdrop-blur-md rounded-[2.5rem] border border-white/5 p-4 flex flex-col md:flex-row gap-4 items-center">
                            <div className="flex bg-black/40 p-1.5 rounded-[1.8rem] w-full md:w-auto">
                                {[
                                    { id: 'inspections', name: 'Inspecciones' },
                                    { id: 'trainings', name: 'Capacitaciones' },
                                    { id: 'reports', name: 'Informes' }
                                ].map(tab => (
                                    <button
                                        key={tab.id}
                                        onClick={() => { setActiveTab(tab.id); updateFilters({ tab: tab.id }); }}
                                        className={`flex-1 md:flex-none px-8 py-3 rounded-[1.4rem] text-[10px] font-black uppercase italic transition-all ${activeTab === tab.id ? 'bg-blue-600 text-white shadow-lg' : 'text-white/30 hover:text-white'}`}
                                    >
                                        {tab.name}
                                    </button>
                                ))}
                            </div>

                            <div className="flex-1 w-full grid grid-cols-2 md:grid-cols-4 gap-4">
                                <select 
                                    value={selectedMonth}
                                    onChange={e => { setSelectedMonth(e.target.value); updateFilters({ month: e.target.value }); }}
                                    className="bg-black/40 border border-white/5 rounded-2xl px-6 py-4 text-xs font-bold text-white focus:ring-2 focus:ring-blue-500 transition-all italic uppercase [color-scheme:dark]"
                                >
                                    {months.map(m => <option key={m.id} value={m.id}>{m.name.toUpperCase()}</option>)}
                                </select>
                                <select 
                                    value={empresaId}
                                    onChange={e => { setEmpresaId(e.target.value); setSedeId(''); updateFilters({ empresa_id: e.target.value, sede_id: '' }); }}
                                    className="bg-black/40 border border-white/5 rounded-2xl px-6 py-4 text-xs font-bold text-white focus:ring-2 focus:ring-blue-500 transition-all italic uppercase [color-scheme:dark]"
                                >
                                    <option value="">TODAS LAS EMPRESAS</option>
                                    {empresas.map(e => <option key={e.id} value={e.id}>{e.nombre.toUpperCase()}</option>)}
                                </select>
                                {empresaId && (
                                    <select 
                                        value={sedeId}
                                        onChange={e => { setSedeId(e.target.value); updateFilters({ sede_id: e.target.value }); }}
                                        className="bg-black/40 border border-white/5 rounded-2xl px-6 py-4 text-xs font-bold text-white focus:ring-2 focus:ring-blue-500 transition-all italic uppercase [color-scheme:dark]"
                                    >
                                        <option value="">TODAS LAS SEDES</option>
                                        {sedes.filter(s => s.empresa_id == empresaId).map(s => <option key={s.id} value={s.id}>{s.nombre_sede.toUpperCase()}</option>)}
                                    </select>
                                )}
                                <div className="relative group md:col-span-1">
                                    <input 
                                        type="text" 
                                        placeholder="BUSCAR..."
                                        value={search}
                                        onChange={e => setSearch(e.target.value)}
                                        onKeyPress={e => e.key === 'Enter' && updateFilters({ search })}
                                        className="w-full bg-black/40 border border-white/5 rounded-2xl px-6 py-4 text-xs font-bold text-white placeholder-white/10 focus:ring-2 focus:ring-blue-500 transition-all uppercase italic"
                                    />
                                </div>
                            </div>
                        </div>

                        {/* LISTADO DE MISIONES */}
                        {renderList()}
                    </div>
                </div>
            </div>

            <style>{`
                .animate-fade-in { animation: fade-in 0.8s ease-out; }
                .animate-fade-in-up { animation: fade-in-up 0.5s ease-out; }
                @keyframes fade-in { from { opacity: 0; } to { opacity: 1; } }
                @keyframes fade-in-up { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
            `}</style>
        </AuthenticatedLayout>
    );
}
