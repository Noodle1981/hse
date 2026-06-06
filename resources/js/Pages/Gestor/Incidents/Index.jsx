import { Head, Link, router } from '@inertiajs/react';
import AuthenticatedLayout from '../../../Layouts/AuthenticatedLayout';
import { useState, useEffect } from 'react';

export default function Index({ incidents, filters, kpis, sedes }) {
    const [search, setSearch] = useState(filters.search || '');
    const [tipo, setTipo] = useState(filters.tipo || 'all');
    const [gravedad, setGravedad] = useState(filters.gravedad || 'all');
    const [sedeId, setSedeId] = useState(filters.sede_id || 'all');

    const updateFilters = () => {
        const targetRoute = auth?.user?.rol?.toLowerCase() === 'tecnico' || auth?.user?.rol?.toLowerCase() === 'técnico' 
            ? 'tecnico.incidentes.index' 
            : 'gestor.incidentes.index';

        router.get(route(targetRoute), {
            search,
            tipo,
            gravedad,
            sede_id: sedeId
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
            tipo !== (filters.tipo || 'all') || 
            gravedad !== (filters.gravedad || 'all') ||
            sedeId !== (filters.sede_id || 'all')
        ) {
            updateFilters();
        }
    }, [tipo, gravedad, sedeId]);

    return (
        <AuthenticatedLayout 
            header={
                <div className="flex flex-col">
                    <h1 className="text-xs font-black text-[#f69239] uppercase tracking-widest italic leading-none mb-1">
                        Centro de <span className="text-white">Incidentes</span>
                    </h1>
                    <h2 className="text-sm font-bold text-indigo-100 uppercase tracking-tight leading-none italic">
                        Control de Emergencias y Seguridad
                    </h2>
                </div>
            }
        >
            <Head title="Control de Emergencias y Seguridad" />

            <div className="px-2 sm:px-6 lg:px-8 py-8 text-white selection:bg-red-500/30">
                <div className="max-w-[95rem] mx-auto space-y-10 animate-fade-in">
                    

                    {/* DASHBOARD DE SINIESTRALIDAD: 5 CARDS REPLICA LEGACY */}
                    <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6 animate-fade-in-down">
                        <StatCard 
                            title="Reportes" 
                            value={kpis.total} 
                            color="white" 
                            icon={<svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>} 
                        />
                        <StatCard 
                            title="Accidentes" 
                            value={kpis.accidentes} 
                            color="red" 
                            icon={<svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>} 
                        />
                        <StatCard 
                            title="Graves" 
                            value={kpis.graves} 
                            color="orange" 
                            pulse={kpis.graves > 0}
                            icon={<svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>} 
                        />
                        <StatCard 
                            title="Días Perdidos" 
                            value={kpis.dias_perdidos} 
                            color="yellow" 
                            icon={<svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>} 
                        />
                        <StatCard 
                            title="Afectados" 
                            value={kpis.afectados} 
                            color="blue" 
                            icon={<svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>} 
                        />
                    </div>

                    {/* HUB DE FILTRADO TÁCTICO */}
                    <div className="bg-[#16162a]/60 backdrop-blur-md rounded-3xl sm:rounded-[2.5rem] border border-white/5 p-5 sm:p-8 shadow-2xl relative animate-fade-in-down overflow-hidden">
                        <div className="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-red-500/30 to-transparent"></div>
                        
                        <div className="grid grid-cols-1 xl:grid-cols-12 gap-6 relative z-10">
                            {/* Buscador */}
                            <div className="xl:col-span-4 relative group">
                                <span className="absolute -top-2.5 left-4 bg-[#11111d] px-2 text-[8px] font-black text-white/20 uppercase tracking-[0.3em] italic z-10 group-focus-within:text-red-500 transition-colors">Buscar incidente...</span>
                                <div className="flex items-center bg-black/40 border border-white/10 rounded-2xl px-5 transition-all focus-within:ring-2 focus-within:ring-red-500/50">
                                    <svg className="w-4 h-4 text-white/20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="3" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                    <input id="search" name="search" 
                                        type="text" 
                                        value={search}
                                        onChange={e => setSearch(e.target.value)}
                                        placeholder="TÍTULO, DESCRIPCIÓN O UBICACIÓN..."
                                        className="w-full bg-transparent border-none focus:ring-0 text-sm font-bold text-white px-3 py-4 placeholder-white/5 italic uppercase"
                                    />
                                </div>
                            </div>

                            {/* Filtros Dropdowns */}
                            <div className="xl:col-span-8 grid grid-cols-1 md:grid-cols-3 gap-4">
                                <FilterSelect 
                                    label="Todos los Tipos" 
                                    value={tipo} 
                                    onChange={setTipo}
                                    options={[
                                        { value: 'all', label: 'TODOS LOS TIPOS' },
                                        { value: 'accidente', label: 'ACCIDENTES' },
                                        { value: 'incidente', label: 'INCIDENTES' },
                                        { value: 'cuasi_accidente', label: 'CUASI ACCIDENTES' }
                                    ]}
                                />
                                <FilterSelect 
                                    label="Todas las Gravedades" 
                                    value={gravedad} 
                                    onChange={setGravedad}
                                    options={[
                                        { value: 'all', label: 'TODAS LAS GRAVEDADES' },
                                        { value: 'leve', label: 'LEVE' },
                                        { value: 'moderado', label: 'MODERADO' },
                                        { value: 'grave', label: 'GRAVE' },
                                        { value: 'fatal', label: 'FATAL' }
                                    ]}
                                />
                                <FilterSelect 
                                    label="Todas las Sedes" 
                                    value={sedeId} 
                                    onChange={setSedeId}
                                    options={[
                                        { value: 'all', label: 'TODAS LAS SEDES' },
                                        ...sedes.map(s => ({ value: s.id, label: s.nombre_sede }))
                                    ]}
                                />
                            </div>
                        </div>
                    </div>

                    {/* MATRIZ DE SINIESTRALIDAD (TABLA) */}
                    <div className="bg-[#16162a]/60 backdrop-blur-md rounded-3xl sm:rounded-[3rem] border border-white/5 shadow-2xl relative overflow-hidden animate-fade-in-up">
                        <div className="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-red-500/20 to-transparent"></div>
                        
                        <div className="overflow-x-auto custom-scrollbar-horizontal">
                            <table className="w-full border-collapse min-w-[1000px]">
                                <thead>
                                    <tr className="bg-black/20 text-left">
                                        <th className="px-8 py-6 uppercase tracking-[0.3em] text-[10px] font-black text-white/20 italic">Incidente / Ref</th>
                                        <th className="px-8 py-6 uppercase tracking-[0.3em] text-[10px] font-black text-white/20 italic">Sede / Ubicación</th>
                                        <th className="px-8 py-6 uppercase tracking-[0.3em] text-[10px] font-black text-white/20 italic">Fecha / Auditor</th>
                                        <th className="px-8 py-6 uppercase tracking-[0.3em] text-[10px] font-black text-white/20 italic text-center">Impacto</th>
                                        <th className="px-8 py-6 uppercase tracking-[0.3em] text-[10px] font-black text-white/20 italic text-center">Gravedad</th>
                                        <th className="px-8 py-6 uppercase tracking-[0.3em] text-[10px] font-black text-white/20 italic text-right">Comandos</th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-white/5">
                                    {incidents.data.length === 0 && (
                                        <tr>
                                            <td colSpan="6" className="px-8 py-32 text-center relative overflow-hidden">
                                                <div className="flex flex-col items-center gap-6 relative z-10 animate-fade-in">
                                                    <div className="w-24 h-24 rounded-full bg-emerald-500/5 flex items-center justify-center border border-emerald-500/10 shadow-[0_0_50px_rgba(16,185,129,0.1)]">
                                                        <svg className="w-12 h-12 text-emerald-500/40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                                    </div>
                                                    <div className="flex flex-col gap-2">
                                                        <h3 className="text-sm font-black text-white/40 uppercase tracking-[0.4em] italic leading-relaxed">
                                                            Zona de Operaciones Segura
                                                        </h3>
                                                        <p className="text-[9px] tracking-widest text-white/10 italic uppercase font-black">
                                                            No se han registrado incidentes de seguridad
                                                        </p>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    )}
                                    {incidents.data.map(inc => (
                                        <tr key={inc.id} className="group/row hover:bg-white/5 transition-all duration-500 relative">
                                            <td className="px-8 py-8">
                                                <div className="flex items-center gap-4">
                                                    <TipoIcon tipo={inc.tipo} />
                                                    <div>
                                                        <div className="text-sm font-black text-white italic uppercase tracking-tight leading-tight mb-1 group-hover/row:text-red-500 transition-colors truncate max-w-[200px]">
                                                            {inc.titulo}
                                                        </div>
                                                        <div className="text-[8px] font-black text-white/20 uppercase tracking-widest italic leading-none">ID: #{inc.id} • {inc.tipo.replace('_', ' ')}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td className="px-8 py-8">
                                                <div className="text-[11px] font-black text-white/80 uppercase italic leading-none">{inc.sede?.nombre_sede || 'N/A'}</div>
                                                <div className="text-[8px] font-black text-white/20 uppercase italic tracking-widest mt-1.5">{inc.sede?.empresa?.nombre || '-'}</div>
                                            </td>
                                            <td className="px-8 py-8">
                                                <div className="text-[10px] font-black text-white/60 uppercase italic leading-none">{new Date(inc.fecha_ocurrencia).toLocaleDateString()}</div>
                                                <div className="text-[8px] font-black text-indigo-400 uppercase italic bg-indigo-500/10 px-2 py-0.5 rounded-lg border border-indigo-500/20 inline-block mt-2 tracking-widest">{inc.user?.nombres || 'SISTEMA'}</div>
                                            </td>
                                            <td className="px-8 py-8 text-center text-white/20">
                                                <div className="flex flex-col gap-1 items-center">
                                                    <span className="text-[10px] font-black text-white italic leading-none">
                                                        {inc.personas_afectadas || 0} Afect.
                                                    </span>
                                                    <span className="text-[8px] font-black uppercase tracking-widest italic">
                                                        {inc.dias_perdidos || 0} Días P.
                                                    </span>
                                                </div>
                                            </td>
                                            <td className="px-8 py-8 text-center">
                                                <GravityBadge gravity={inc.gravedad} />
                                            </td>
                                            <td className="px-8 py-8 text-right">
                                                <div className="flex justify-end gap-3 transition-all duration-500">
                                                    <Link href={route(auth?.user?.rol?.toLowerCase() === 'tecnico' || auth?.user?.rol?.toLowerCase() === 'técnico' ? 'tecnico.incidentes.show' : 'gestor.incidentes.show', inc.id)} className="h-10 w-10 bg-white/5 border border-white/10 text-white/40 hover:bg-white/20 hover:text-white rounded-xl flex items-center justify-center transition-all shadow-lg active:scale-95" title="Analizar">
                                                        <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="2.5"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                    </Link>
                                                    <a href={route(auth?.user?.rol?.toLowerCase() === 'tecnico' || auth?.user?.rol?.toLowerCase() === 'técnico' ? 'tecnico.incidentes.pdf' : 'gestor.incidentes.pdf', inc.id)} target="_blank" className="h-10 w-10 bg-red-600/10 border border-red-500/20 text-red-500/60 hover:bg-red-600 hover:text-white rounded-xl flex items-center justify-center transition-all shadow-lg active:scale-95" title="Exportar PDF" rel="noreferrer">
                                                        <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="2.5"><path strokeLinecap="round" strokeLinejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
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
                                EMERGENCY CONTROL • PROTOCOL v3.0
                            </div>
                            <div className="flex gap-2">
                                {incidents.links.map((link, i) => (
                                    <Link 
                                        key={i}
                                        href={link.url || '#'}
                                        dangerouslySetInnerHTML={{ __html: link.label }}
                                        className={`px-4 py-2 rounded-xl border text-[9px] ${link.active ? 'bg-red-600 border-red-600 text-white shadow-[0_0_15px_rgba(220,38,38,0.3)]' : 'bg-white/5 border-white/5 text-white/20 hover:text-white'} ${!link.url && 'opacity-20 cursor-not-allowed'}`}
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
        red: 'border-red-500/20 text-red-500 bg-red-500/5',
        orange: 'border-orange-500/10 text-orange-500',
        yellow: 'border-yellow-500/10 text-yellow-500 bg-yellow-500/5',
        blue: 'border-blue-500/20 text-blue-400 bg-blue-500/5',
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

function FilterSelect({ label, value, onChange, options }) {
    return (
        <div className="relative group">
            <span className="absolute -top-2.5 left-4 bg-[#11111d] px-2 text-[8px] font-black text-white/20 uppercase tracking-[0.3em] italic z-10 transition-colors group-hover:text-white">{label}</span>
            <select id="value" name="value" 
                value={value} 
                onChange={e => onChange(e.target.value)} 
                className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-xs font-bold text-white focus:ring-2 focus:ring-red-500 transition-all [color-scheme:dark] appearance-none cursor-pointer italic uppercase tracking-widest"
            >
                {options.map(opt => (
                    <option key={opt.value} value={opt.value}>{opt.label}</option>
                ))}
            </select>
        </div>
    );
}

function GravityBadge({ gravity }) {
    const config = {
        fatal: { text: 'FATAL / CRÍTICO', style: 'text-red-500 bg-red-500/10 border-red-500/20 shadow-[0_0_10px_rgba(239,68,68,0.3)] animate-pulse' },
        grave: { text: 'GRAVE', style: 'text-orange-500 bg-orange-500/10 border-orange-500/20' },
        moderado: { text: 'RIESGO MEDIO', style: 'text-yellow-400 bg-yellow-500/10 border-yellow-500/20' },
        leve: { text: 'BAJO RIESGO', style: 'text-emerald-400 bg-emerald-500/10 border-emerald-500/20' },
    };
    
    const { text, style } = config[gravity] || config.leve;
    
    return (
        <span className={`inline-flex px-3 py-1 rounded-xl text-[9px] font-black uppercase tracking-widest italic border ${style}`}>
            {text}
        </span>
    );
}

function TipoIcon({ tipo }) {
    const isAccident = tipo === 'accidente';
    return (
        <div className={`w-10 h-10 rounded-xl flex items-center justify-center transition-all shadow-lg ${isAccident ? 'bg-red-500/20 text-red-500' : 'bg-blue-500/20 text-blue-400'}`}>
            {isAccident ? (
                <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            ) : (
                <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            )}
        </div>
    );
}
