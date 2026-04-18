import { Head, Link, router, usePage } from '@inertiajs/react';
import AuthenticatedLayout from '../../../Layouts/AuthenticatedLayout';
import { useState, useEffect } from 'react';

export default function Index({ findings, filters, kpis = {}, sedes = [], tecnicos = [] }) {
    const { auth } = usePage().props;
    
    // Normalización de KPIs para evitar fallos de renderizado
    const safeKpis = {
        total: kpis?.total || 0,
        pendientes: kpis?.pendientes || 0,
        criticos: kpis?.criticos || 0,
        vencidos: kpis?.vencidos || 0,
        resueltos_mes: kpis?.resueltos_mes || 0,
    };
    // FILTROS REACTIVOS
    const [search, setSearch] = useState(filters.search || '');
    const [status, setStatus] = useState(filters.status || 'all');
    const [criticidad, setCriticidad] = useState(filters.criticidad || 'all');
    const [sedeId, setSedeId] = useState(filters.sede_id || 'all');
    const [userId, setUserId] = useState(filters.user_id || 'all');
    const [vencidos, setVencidos] = useState(filters.vencidos || 'all');

    const updateFilters = () => {
        const targetRoute = auth?.user?.rol?.toLowerCase() === 'tecnico' || auth?.user?.rol?.toLowerCase() === 'técnico' 
            ? 'tecnico.hallazgos.index' 
            : 'gestor.hallazgos.index';

        router.get(route(targetRoute), {
            search,
            status,
            criticidad,
            sede_id: sedeId,
            user_id: userId,
            vencidos
        }, {
            preserveState: true,
            replace: true,
        });
    };

    useEffect(() => {
        const timeout = setTimeout(() => {
            if (search !== (filters.search || '')) updateFilters();
        }, 500);
        return () => clearTimeout(timeout);
    }, [search]);

    useEffect(() => {
        if (
            status !== (filters.status || 'all') || 
            criticidad !== (filters.criticidad || 'all') ||
            sedeId !== (filters.sede_id || 'all') ||
            userId !== (filters.user_id || 'all') ||
            vencidos !== (filters.vencidos || 'all')
        ) {
            updateFilters();
        }
    }, [status, criticidad, sedeId, userId, vencidos]);

    const clearFilters = () => {
        setSearch('');
        setStatus('all');
        setCriticidad('all');
        setSedeId('all');
        setUserId('all');
        setVencidos('all');
    };

    return (
        <AuthenticatedLayout 
            header={
                <div className="flex flex-col">
                    <h1 className="text-xs font-black text-ep-primary uppercase tracking-widest italic leading-none mb-1">
                        Centro de <span className="text-white">Hallazgos</span>
                    </h1>
                    <h2 className="text-sm font-bold text-indigo-100 uppercase tracking-tight leading-none italic">
                        Auditoría Forense y Riesgos
                    </h2>
                </div>
            }
        >
            <Head title="Gestión de Hallazgos" />

            <div className="px-2 sm:px-6 lg:px-8 py-8 text-white selection:bg-ep-primary/30">
                <div className="max-w-[95rem] mx-auto space-y-10 animate-fade-in">
                    
                    {/* INTELIGENCIA DE RIESGO: KPIs REPLICA LEGACY */}
                    <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6 animate-fade-in-down">
                        <StatCard 
                            title="Total Sistema" 
                            value={safeKpis.total} 
                            color="white" 
                            icon={<svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>} 
                        />
                        <StatCard 
                            title="Pendientes" 
                            value={safeKpis.pendientes} 
                            color="amber" 
                            icon={<svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>} 
                        />
                        <StatCard 
                            title="Críticos Abiertos" 
                            value={safeKpis.criticos} 
                            color="red" 
                            pulse={true}
                            icon={<svg className="w-5 h-5 shadow-[0_0_10px_#ef4444]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>} 
                        />
                        <StatCard 
                            title="Vencidos Total" 
                            value={safeKpis.vencidos} 
                            color="indigo" 
                            icon={<svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>} 
                        />
                        <StatCard 
                            title="Cerrados (Mes)" 
                            value={safeKpis.resueltos_mes} 
                            color="emerald" 
                            icon={<svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>} 
                        />
                    </div>

                    {/* HUB DE FILTRADO TÁCTICO */}
                    <div className="bg-[#16162a]/60 backdrop-blur-md rounded-3xl sm:rounded-[2.5rem] border border-white/5 p-5 sm:p-8 shadow-2xl relative animate-fade-in-down overflow-hidden">
                        <div className="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-indigo-500/30 to-transparent"></div>
                        
                        <div className="space-y-8 relative z-10">
                            <div className="grid grid-cols-1 md:grid-cols-12 gap-8">
                                <div className="md:col-span-6 relative group">
                                    <span className="absolute -top-2.5 left-4 bg-[#11111d] px-2 text-[8px] font-black text-indigo-400 uppercase tracking-[0.3em] italic z-10 transition-colors group-focus-within:text-white">Buscador Forense</span>
                                    <input type="text" 
                                           value={search}
                                           onChange={e => setSearch(e.target.value)}
                                           placeholder="TITULO, DESCRIPCIÓN O UBICACIÓN..."
                                           className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-indigo-500/50 transition-all placeholder-white/5 italic uppercase" />
                                </div>

                                <div className="md:col-span-3 relative group">
                                    <span className="absolute -top-2.5 left-4 bg-[#11111d] px-2 text-[8px] font-black text-indigo-400 uppercase tracking-[0.3em] italic z-10">Estado Operativo</span>
                                    <select value={status} onChange={e => setStatus(e.target.value)} className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-indigo-500/50 transition-all [color-scheme:dark] appearance-none cursor-pointer italic uppercase">
                                        <option value="all">TODOS LOS ESTADOS</option>
                                        <option value="pendiente">PENDIENTE</option>
                                        <option value="en_proceso">EN PROCESO</option>
                                        <option value="resuelto">RESUELTO</option>
                                        <option value="verificado">VERIFICADO</option>
                                        <option value="cerrado">CERRADO</option>
                                    </select>
                                </div>

                                <div className="md:col-span-3 relative group">
                                    <span className="absolute -top-2.5 left-4 bg-[#11111d] px-2 text-[8px] font-black text-red-500 uppercase tracking-[0.3em] italic z-10">Filtro Criticidad</span>
                                    <select value={criticidad} onChange={e => setCriticidad(e.target.value)} className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-red-500/50 transition-all [color-scheme:dark] appearance-none cursor-pointer italic uppercase">
                                        <option value="all">TODAS LAS CRITICIDADES</option>
                                        <option value="bajo">BAJO RIESGO</option>
                                        <option value="medio">RIESGO MEDIO</option>
                                        <option value="alto">ALTO RIESGO</option>
                                        <option value="critico">CRITICO / EMERGENCIA</option>
                                    </select>
                                </div>
                            </div>

                            <div className="grid grid-cols-1 md:grid-cols-4 gap-8">
                                <div className="relative group">
                                    <span className="absolute -top-2.5 left-4 bg-[#11111d] px-2 text-[8px] font-black text-white/20 uppercase tracking-[0.3em] italic z-10">Sede Operativa</span>
                                    <select value={sedeId} onChange={e => setSedeId(e.target.value)} className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-3 text-[11px] font-black text-white/60 focus:ring-2 focus:ring-white/20 transition-all [color-scheme:dark] italic uppercase tracking-widest cursor-pointer">
                                        <option value="all">TODAS LAS SEDES</option>
                                        {sedes.map(sede => (
                                            <option key={sede.id} value={sede.id}>{sede.nombre_sede}</option>
                                        ))}
                                    </select>
                                </div>
                                <div className="relative group">
                                    <span className="absolute -top-2.5 left-4 bg-[#11111d] px-2 text-[8px] font-black text-white/20 uppercase tracking-[0.3em] italic z-10">Técnico Auditor</span>
                                    <select value={userId} onChange={e => setUserId(e.target.value)} className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-3 text-[11px] font-black text-white/60 focus:ring-2 focus:ring-white/20 transition-all [color-scheme:dark] italic uppercase tracking-widest cursor-pointer">
                                        <option value="all">TODOS LOS TÉCNICOS</option>
                                        {tecnicos.map(tec => (
                                            <option key={tec.id} value={tec.id}>{tec.nombres}</option>
                                        ))}
                                    </select>
                                </div>
                                <div className="relative group">
                                    <span className="absolute -top-2.5 left-4 bg-[#11111d] px-2 text-[8px] font-black text-white/20 uppercase tracking-[0.3em] italic z-10">Rango Temporal</span>
                                    <select value={vencidos} onChange={e => setVencidos(e.target.value)} className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-3 text-[11px] font-black text-white/60 focus:ring-2 focus:ring-white/20 transition-all [color-scheme:dark] italic uppercase tracking-widest cursor-pointer">
                                        <option value="all">VENCIMIENTO (TODOS)</option>
                                        <option value="si">SOLO VENCIDOS</option>
                                        <option value="no">DENTRO DE PLAZO</option>
                                    </select>
                                </div>
                                <div className="flex items-center justify-end">
                                    {(search || status !== 'all' || criticidad !== 'all' || sedeId !== 'all' || userId !== 'all' || vencidos !== 'all') && (
                                        <button onClick={clearFilters} className="px-6 py-3 bg-white/5 border border-white/10 text-white/40 hover:text-white rounded-xl text-[9px] font-black uppercase tracking-widest italic transition-all group/clear flex items-center gap-2">
                                            <svg className="w-3 h-3 group-hover/clear:rotate-90 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" strokeWidth="3"/></svg>
                                            Limpiar Inteligencia
                                        </button>
                                    )}
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* MASTER TABLE: MATRIZ FORENSE (REPLICA LEGACY) */}
                    <div className="bg-[#16162a]/60 backdrop-blur-md rounded-3xl sm:rounded-[3rem] border border-white/5 shadow-2xl relative overflow-hidden animate-fade-in-up">
                        <div className="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-indigo-500/20 to-transparent"></div>
                        
                        <div className="overflow-x-auto custom-scrollbar-horizontal">
                            <table className="w-full border-collapse min-w-[1100px]">
                                <thead>
                                    <tr className="bg-black/20 text-left">
                                        <th className="px-8 py-6 uppercase tracking-[0.3em] text-[10px] font-black text-white/20 italic">Hallazgo</th>
                                        <th className="px-8 py-6 uppercase tracking-[0.3em] text-[10px] font-black text-white/20 italic">Sede</th>
                                        <th className="px-8 py-6 uppercase tracking-[0.3em] text-[10px] font-black text-white/20 italic text-center">Riesgo</th>
                                        <th className="px-8 py-6 uppercase tracking-[0.3em] text-[10px] font-black text-white/20 italic text-center">Status</th>
                                        <th className="px-8 py-6 uppercase tracking-[0.3em] text-[10px] font-black text-white/20 italic text-center">Plazo</th>
                                        <th className="px-8 py-6 uppercase tracking-[0.3em] text-[10px] font-black text-white/20 italic text-center">Docs</th>
                                        <th className="px-8 py-6 uppercase tracking-[0.3em] text-[10px] font-black text-white/20 italic text-right">Comandos</th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-white/5">
                                    {findings.data.length === 0 && (
                                        <tr>
                                            <td colSpan="7" className="px-8 py-32 text-center">
                                                <div className="flex flex-col items-center gap-2">
                                                    <span className="text-sm font-black text-white/5 uppercase tracking-[0.4em] italic leading-relaxed">
                                                        No se detectaron hallazgos
                                                    </span>
                                                    <span className="text-[9px] tracking-widest text-white/3 italic uppercase font-black">
                                                        Ajuste los filtros para ampliar la búsqueda
                                                    </span>
                                                </div>
                                            </td>
                                        </tr>
                                    )}
                                    {findings.data.map(finding => (
                                        <tr key={finding.id} className={`group/row hover:bg-white/5 transition-all duration-500 relative ${finding.is_overdue ? 'bg-red-500/5' : ''}`}>
                                            <td className="px-8 py-8">
                                                <div className="text-sm font-black text-white italic uppercase tracking-tight leading-tight mb-1 group-hover/row:text-indigo-400 transition-colors">
                                                    {finding.titulo}
                                                </div>
                                                <div className="text-[10px] font-bold text-white/30 italic max-w-xs truncate">
                                                    {finding.descripcion}
                                                </div>
                                                {finding.ubicacion_especifica && (
                                                    <div className="text-[9px] font-black text-indigo-400/60 mt-2 uppercase italic tracking-widest leading-none">📍 {finding.ubicacion_especifica}</div>
                                                )}
                                            </td>
                                            <td className="px-8 py-8">
                                                <div className="text-[10px] font-black text-white/60 italic uppercase tracking-tight leading-none">
                                                    {finding.sede?.nombre_sede || 'GLOBAL'}
                                                </div>
                                                <div className="text-[8px] font-bold text-white/20 italic mt-1 uppercase">
                                                    {finding.sede?.empresa?.nombre || '-'}
                                                </div>
                                            </td>
                                            <td className="px-8 py-8 text-center">
                                                <CriticidadBadge criticidad={finding.criticidad} />
                                            </td>
                                            <td className="px-8 py-8 text-center">
                                                <StatusBadge status={finding.estado} />
                                            </td>
                                            <td className="px-8 py-8 text-center">
                                                <div className={`text-[10px] font-black italic ${finding.is_overdue ? 'text-red-500 animate-pulse' : 'text-white/60'}`}>
                                                    {finding.plazo_correccion ? new Date(finding.plazo_correccion).toLocaleDateString() : '-'}
                                                </div>
                                            </td>
                                            <td className="px-8 py-8 text-center">
                                                <div className="flex justify-center items-center gap-4">
                                                    {finding.fotos_count > 0 && (
                                                        <div className="flex items-center gap-1.5" title={`${finding.fotos_count} Fotos`}>
                                                            <svg className="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                                            <span className="text-[10px] font-black text-indigo-400 leading-none">{finding.fotos_count}</span>
                                                        </div>
                                                    )}
                                                    {finding.seguimientos_count > 0 && (
                                                        <div className="flex items-center gap-1.5" title={`${finding.seguimientos_count} Seguimientos`}>
                                                            <svg className="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                            <span className="text-[10px] font-black text-emerald-400 leading-none">{finding.seguimientos_count}</span>
                                                        </div>
                                                    )}
                                                </div>
                                            </td>
                                            <td className="px-8 py-8 text-right">
                                                <div className="flex justify-end gap-3 transition-all duration-500">
                                                    <Link href={route(auth?.user?.rol?.toLowerCase() === 'tecnico' || auth?.user?.rol?.toLowerCase() === 'técnico' ? 'tecnico.hallazgos.show' : 'gestor.hallazgos.show', finding.id)} className="h-10 w-10 bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 hover:bg-indigo-500 hover:text-white rounded-xl flex items-center justify-center transition-all shadow-lg active:scale-95" title="Auditar">
                                                        <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="2.5"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                    </Link>
                                                    <a href={route('gestor.hallazgos.pdf', finding.id)} target="_blank" className="h-10 w-10 bg-white/5 border border-white/10 text-white/40 hover:bg-white/20 hover:text-white rounded-xl flex items-center justify-center transition-all shadow-lg active:scale-95" title="Reporte PDF">
                                                        <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="2.5"><path d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>

                        {/* PAGINACIÓN TÁCTICA */}
                        <div className="px-10 py-8 border-t border-white/5 bg-black/20 flex flex-col sm:flex-row items-center justify-between gap-6 font-black italic">
                            <div className="text-[10px] text-white/20 uppercase tracking-[0.5em]">
                                INVESTIGATION OPS • FORENSIC v2.0
                            </div>
                            <div className="flex gap-2">
                                {findings.links.map((link, i) => (
                                    <Link 
                                        key={i}
                                        href={link.url || '#'}
                                        dangerouslySetInnerHTML={{ __html: link.label }}
                                        className={`px-4 py-2 rounded-xl border text-[9px] ${link.active ? 'bg-indigo-500 border-indigo-500 text-white shadow-[0_0_15px_rgba(99,102,241,0.3)]' : 'bg-white/5 border-white/5 text-white/20 hover:text-white'} ${!link.url && 'opacity-20 cursor-not-allowed'}`}
                                    />
                                ))}
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>

            <style>{`
                .animate-fade-in-down { animation: fade-in-down 0.6s ease-out forwards; }
                .animate-fade-in-up { animation: fade-in-up 0.5s ease-out forwards; }
                .animate-fade-in { animation: fade-in 0.8s ease-out forwards; }
                @keyframes fade-in-down { from { opacity: 0; transform: translateY(-30px); } to { opacity: 1; transform: translateY(0); } }
                @keyframes fade-in-up { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
                @keyframes fade-in { from { opacity: 0; } to { opacity: 1; } }
            `}</style>
        </AuthenticatedLayout>
    );
}

function StatCard({ title, value, color, icon, pulse = false }) {
    const colors = {
        white: 'border-white/5 text-white',
        amber: 'border-amber-500/10 text-amber-500',
        red: 'border-red-500/20 text-red-500 bg-red-500/5',
        indigo: 'border-indigo-500/10 text-indigo-400',
        emerald: 'border-emerald-500/20 text-emerald-500 bg-emerald-500/5',
    };

    return (
        <div className={`bg-[#16162a]/60 backdrop-blur-md rounded-3xl border p-6 flex items-center justify-between group/stat hover:bg-white/5 transition-all ${colors[color] || colors.white}`}>
            <div>
                <p className={`text-[8px] font-black uppercase tracking-[0.3em] italic mb-1 opacity-40`}>{title}</p>
                <p className={`text-2xl font-black italic leading-none ${pulse ? 'animate-pulse' : ''}`}>{value}</p>
            </div>
            <div className={`w-10 h-10 rounded-xl flex items-center justify-center bg-white/5 transition-transform group-hover/stat:scale-110`}>
                {icon}
            </div>
        </div>
    );
}

function StatusBadge({ status }) {
    const config = {
        cerrado: { text: 'CERRADO', style: 'text-emerald-400 border-emerald-500/20' },
        verificado: { text: 'VERIFICADO', style: 'text-blue-400 border-blue-500/20' },
        resuelto: { text: 'RESUELTO', style: 'text-cyan-400 border-cyan-500/20' },
        en_proceso: { text: 'EN PROCESO', style: 'text-amber-400 border-amber-500/20' },
        pendiente: { text: 'PENDIENTE', style: 'text-red-500 border-red-500/20' },
    };
    
    const { text, style } = config[status] || config.pendiente;
    
    return (
        <span className={`inline-flex px-3 py-1 rounded-xl text-[9px] font-black uppercase tracking-widest italic border ${style} bg-black/20`}>
            {text}
        </span>
    );
}

function CriticidadBadge({ criticidad }) {
    const config = {
        critico: { text: 'CRÍTICO', style: 'text-red-500 bg-red-500/10 border-red-500/20 shadow-[0_0_10px_rgba(239,68,68,0.3)] animate-pulse' },
        alto: { text: 'ALTO RIESGO', style: 'text-orange-500 bg-orange-500/10 border-orange-500/20' },
        medio: { text: 'RIESGO MEDIO', style: 'text-amber-400 bg-amber-500/10 border-amber-500/20' },
        bajo: { text: 'BAJO RIESGO', style: 'text-emerald-400 bg-emerald-500/10 border-emerald-500/20' },
    };
    
    const { text, style } = config[criticidad] || config.bajo;
    
    return (
        <span className={`inline-flex px-3 py-1 rounded-xl text-[9px] font-black uppercase tracking-widest italic border ${style}`}>
            {text}
        </span>
    );
}
