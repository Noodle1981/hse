import { Head, Link, router, usePage } from '@inertiajs/react';
import AuthenticatedLayout from '../../../Layouts/AuthenticatedLayout';
import { useState, useEffect } from 'react';

export default function Index({ findings, filters, stats }) {
    const { auth } = usePage().props;
    const [searchTerm, setSearchTerm] = useState(filters.search || '');
    const [status, setStatus] = useState(filters.status || 'all');
    const [criticidad, setCriticidad] = useState(filters.criticidad || 'all');

    const routePrefix = auth.user.is_admin ? 'gestor.hallazgos' : 'tecnico.hallazgos';
    const canReport = auth.user.is_admin || auth.user.is_technician;

    const updateFilters = () => {
        router.get(route(`${routePrefix}.index`), {
            search: searchTerm,
            status,
            criticidad
        }, {
            preserveState: true,
            replace: true,
        });
    };

    useEffect(() => {
        const timeout = setTimeout(() => {
            if (searchTerm !== (filters.search || '')) updateFilters();
        }, 500);
        return () => clearTimeout(timeout);
    }, [searchTerm]);

    useEffect(() => {
        updateFilters();
    }, [status, criticidad]);

    const getStatusColor = (st) => {
        const colors = {
            'pendiente': 'border-red-500/30 text-red-500 bg-red-500/5',
            'en_proceso': 'border-orange-500/30 text-orange-400 bg-orange-500/5',
            'resuelto': 'border-emerald-500/30 text-emerald-400 bg-emerald-500/5',
            'cerrado': 'border-white/10 text-white/40 bg-white/5',
        };
        return colors[st] || 'border-white/5 text-white/20';
    };

    const getCriticidadColor = (crit) => {
        const colors = {
            'critico': 'bg-red-500 shadow-[0_0_10px_rgba(239,68,68,0.5)]',
            'alto': 'bg-orange-500 shadow-[0_0_10px_rgba(249,115,22,0.5)]',
            'medio': 'bg-yellow-500',
            'bajo': 'bg-emerald-500',
        };
        return colors[crit] || 'bg-white/20';
    };

    return (
        <AuthenticatedLayout title="Monitor de Desviaciones Tácticas">
            <Head title="Gestión de Hallazgos" />

            <div className="px-4 sm:px-6 lg:px-8 py-8 text-white selection:bg-red-500/30">
                <div className="max-w-[100rem] mx-auto space-y-10">
                    
                    {/* STATS DE NIVEL TÁCTICO */}
                    <div className="grid grid-cols-1 md:grid-cols-3 gap-6 animate-fade-in-down">
                        <div className="bg-[#16162a]/60 backdrop-blur-xl border border-white/5 rounded-[2.5rem] p-8 relative overflow-hidden group">
                            <div className="absolute top-0 right-0 p-8 opacity-5 group-hover:opacity-10 transition-opacity">
                                <svg className="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" strokeWidth="2"/></svg>
                            </div>
                            <div className="text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic mb-2">Desviaciones Totales</div>
                            <div className="text-4xl font-black text-white italic tracking-tighter leading-none">{stats.total}</div>
                            <div className="w-12 h-1.5 bg-white/10 rounded-full mt-4"></div>
                        </div>

                        <div className="bg-[#16162a]/60 backdrop-blur-xl border border-red-500/10 rounded-[2.5rem] p-8 relative overflow-hidden group">
                            <div className="absolute top-0 right-0 p-8 opacity-10">
                                <div className="w-3 h-3 bg-red-500 rounded-full animate-ping"></div>
                            </div>
                            <div className="text-[10px] font-black text-red-500/40 uppercase tracking-[0.3em] italic mb-2">Estado Crítico</div>
                            <div className="text-4xl font-black text-white italic tracking-tighter leading-none">{stats.criticos}</div>
                            <div className="w-12 h-1.5 bg-red-500 rounded-full mt-4"></div>
                        </div>

                        <div className="bg-[#16162a]/60 backdrop-blur-xl border border-orange-500/10 rounded-[2.5rem] p-8 relative overflow-hidden group">
                            <div className="text-[10px] font-black text-orange-500/40 uppercase tracking-[0.3em] italic mb-2">Pendientes de Acción</div>
                            <div className="text-4xl font-black text-white italic tracking-tighter leading-none">{stats.pendientes}</div>
                            <div className="w-12 h-1.5 bg-orange-500 rounded-full mt-4"></div>
                        </div>
                    </div>

                    {/* HUB DE BÚSQUEDA Y FILTROS */}
                    <div className="bg-[#16162a]/40 backdrop-blur-md rounded-[3rem] border border-white/5 p-8 shadow-2xl flex flex-col xl:flex-row gap-8 items-center justify-between animate-fade-in-up">
                        <div className="w-full xl:w-1/3 relative group">
                            <span className="absolute -top-2.5 left-4 bg-[#11111d] px-2 text-[8px] font-black text-white/20 uppercase tracking-[0.3em] italic z-10 transition-colors group-focus-within:text-red-500">Filtrado Inteligente</span>
                            <input 
                                type="text" 
                                value={searchTerm}
                                onChange={e => setSearchTerm(e.target.value)}
                                placeholder="BUSCAR POR TÍTULO O SEDE..."
                                className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-red-500/50 transition-all placeholder-white/5 italic uppercase"
                            />
                        </div>

                        <div className="flex flex-wrap items-center gap-6 w-full xl:w-auto">
                            <div className="flex bg-black/40 p-1.5 rounded-2xl border border-white/5">
                                {['all', 'pendiente', 'en_proceso', 'resuelto'].map(st => (
                                    <button 
                                        key={st}
                                        onClick={() => setStatus(st)}
                                        className={`px-6 py-2.5 rounded-xl text-[9px] font-black uppercase tracking-widest italic transition-all ${status === st ? 'bg-white/10 text-white shadow-lg' : 'text-white/20 hover:text-white/40'}`}
                                    >
                                        {st === 'all' ? 'Ver Todos' : st.replace('_', ' ')}
                                    </button>
                                ))}
                            </div>

                            <select 
                                value={criticidad}
                                onChange={e => setCriticidad(e.target.value)}
                                className="bg-black/40 border border-white/10 rounded-2xl px-6 py-3.5 text-[9px] font-black text-white/60 focus:ring-2 focus:ring-red-500 transition-all [color-scheme:dark] italic uppercase tracking-widest"
                            >
                                <option value="all">Todas las Criticidades</option>
                                <option value="critico">🔴 Crítico</option>
                                <option value="alto">🟠 Alto</option>
                                <option value="medio">🟡 Medio</option>
                                <option value="bajo">🟢 Bajo</option>
                            </select>

                            {canReport && (
                                <Link 
                                    href={route(`${routePrefix}.create`)}
                                    className="px-8 py-4 bg-white/5 border border-white/10 text-white text-[9px] font-black uppercase tracking-[0.3em] rounded-2xl italic hover:bg-white/10 transition-all ml-auto xl:ml-0"
                                >
                                    + Reportar Hallazgo
                                </Link>
                            )}
                        </div>
                    </div>

                    {/* LISTADO DE HALLAZGOS */}
                    <div className="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8 animate-fade-in-up delay-100">
                        {findings.data.map(finding => (
                            <div key={finding.id} className="bg-[#16162a]/60 backdrop-blur-xl border border-white/5 rounded-[3rem] overflow-hidden group hover:border-white/10 transition-all shadow-2xl relative">
                                {/* Ribbon de Criticidad */}
                                <div className={`absolute top-0 right-10 w-4 h-12 rounded-b-full ${getCriticidadColor(finding.criticidad)}`}></div>
                                
                                <div className="p-10 space-y-6">
                                    <div className="flex justify-between items-start">
                                        <div className={`px-4 py-1.5 border rounded-full text-[8px] font-black uppercase tracking-[0.2em] italic ${getStatusColor(finding.estado)}`}>
                                            {finding.estado.replace('_', ' ')}
                                        </div>
                                    </div>

                                    <div>
                                        <h3 className="text-xl font-black text-white italic uppercase tracking-tighter leading-tight group-hover:text-red-500 transition-colors line-clamp-2">
                                            {finding.titulo}
                                        </h3>
                                        <div className="text-[10px] font-black text-white/20 uppercase tracking-widest mt-2 italic flex items-center gap-2">
                                            <svg className="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m4 0h1m-5 4h1m4 0h1m-5 4h1m4 0h1" strokeWidth="2"/></svg>
                                            {finding.sede?.nombre_sede}
                                        </div>
                                    </div>

                                    <p className="text-[11px] font-bold text-white/40 uppercase italic leading-relaxed line-clamp-3">
                                        {finding.descripcion}
                                    </p>

                                    <div className="pt-6 border-t border-white/5 flex items-center justify-between">
                                        <div className="flex flex-col">
                                            <span className="text-[8px] font-black text-white/10 uppercase italic">Fecha de Detección</span>
                                            <span className="text-[10px] font-black text-white/60 italic uppercase tracking-tighter">{new Date(finding.fecha_deteccion).toLocaleDateString()}</span>
                                        </div>
                                        <Link 
                                            href={route(`${routePrefix}.show`, finding.id)}
                                            className="w-12 h-12 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center text-white/40 hover:text-white hover:bg-white/10 group-hover:bg-red-500/10 group-hover:text-red-500 transition-all shadow-lg"
                                        >
                                            <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M17 8l4 4m0 0l-4 4m4-4H3" strokeWidth="3"/></svg>
                                        </Link>
                                    </div>
                                </div>
                            </div>
                        ))}
                    </div>

                    {/* PAGINACIÓN */}
                    {findings.total > findings.per_page && (
                        <div className="flex justify-center pt-10 animate-fade-in">
                            <div className="flex gap-2">
                                {findings.links.map((link, i) => (
                                    <Link 
                                        key={i}
                                        href={link.url || '#'}
                                        dangerouslySetInnerHTML={{ __html: link.label }}
                                        className={`px-5 py-2.5 rounded-xl border text-[10px] font-black uppercase italic transition-all ${link.active ? 'bg-red-500 border-red-500 text-white' : 'bg-white/5 border-white/5 text-white/20 hover:text-white'} ${!link.url && 'opacity-20 cursor-not-allowed'}`}
                                    />
                                ))}
                            </div>
                        </div>
                    )}
                </div>
            </div>

            <style>{`
                .animate-fade-in-down { animation: fade-in-down 0.6s ease-out forwards; }
                .animate-fade-in-up { animation: fade-in-up 0.5s ease-out forwards; }
                @keyframes fade-in-down { from { opacity: 0; transform: translateY(-30px); } to { opacity: 1; transform: translateY(0); } }
                @keyframes fade-in-up { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
                .delay-100 { animation-delay: 0.1s; }
            `}</style>
        </AuthenticatedLayout>
    );
}
