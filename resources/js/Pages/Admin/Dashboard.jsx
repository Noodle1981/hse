import React from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, router } from '@inertiajs/react';

export default function Dashboard({ auth, kpis, recentActivity, rankings, filters }) {
    
    const updateDateFilter = (from, to) => {
        router.get(route('admin.dashboard'), { from, to }, { preserveScroll: true });
    };

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={<h2 className="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Dashboard Administrativo</h2>}
        >
            <Head title="Admin Dashboard" />

            <div className="py-12 bg-[#0f0f1a] min-h-screen text-white selection:bg-ep-primary/30">
                <div className="max-w-[98rem] mx-auto sm:px-6 lg:px-8 space-y-8">
                    
                    {/* CABECERA Y FILTROS */}
                    <div className="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                        <div>
                            <h1 className="text-3xl font-black text-white italic uppercase tracking-tighter">
                                Auditoría <span className="text-ep-primary">Global</span>
                            </h1>
                            <p className="text-xs font-bold text-white/20 uppercase tracking-widest mt-2">Centro de Control de Ecosistema XamaSafe</p>
                        </div>

                        <div className="flex items-center gap-4 bg-white/5 p-2 rounded-2xl border border-white/5 backdrop-blur-md">
                            <input 
                                type="date" 
                                value={filters.from}
                                onChange={e => updateDateFilter(e.target.value, filters.to)}
                                className="bg-transparent border-none text-xs font-black text-white italic focus:ring-0 [color-scheme:dark]"
                            />
                            <span className="text-white/10">|</span>
                            <input 
                                type="date" 
                                value={filters.to}
                                onChange={e => updateDateFilter(filters.from, e.target.value)}
                                className="bg-transparent border-none text-xs font-black text-white italic focus:ring-0 [color-scheme:dark]"
                            />
                        </div>
                    </div>

                    {/* KPI CARDS */}
                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <KpiCard 
                            title="Inspecciones" 
                            value={kpis.inspections} 
                            subtitle="Periodo Actual" 
                            color="ep-primary" 
                            icon={<svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" strokeWidth="2"/></svg>}
                        />
                        <KpiCard 
                            title="Hallazgos" 
                            value={kpis.findings} 
                            subtitle="Detectados" 
                            color="orange" 
                            icon={<svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" strokeWidth="2"/></svg>}
                        />
                        <KpiCard 
                            title="Siniestralidad" 
                            value={kpis.incidents} 
                            subtitle="Incidentes" 
                            color="red" 
                            icon={<svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M13 10V3L4 14h7v7l9-11h-7z" strokeWidth="2"/></svg>}
                        />
                        <KpiCard 
                            title="Capacitaciones" 
                            value={kpis.trainings} 
                            subtitle="Ejecutadas" 
                            color="blue" 
                            icon={<svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 14l9-5-9-5-9 5 9 5z" strokeWidth="2"/><path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" strokeWidth="2"/></svg>}
                        />
                    </div>

                    <div className="grid grid-cols-1 xl:grid-cols-3 gap-8">
                        {/* FEED DE ACTIVIDAD */}
                        <div className="xl:col-span-2 bg-ep-dark/40 backdrop-blur-xl rounded-[2.5rem] border border-white/5 p-8 shadow-2xl">
                            <div className="flex items-center justify-between mb-8 pb-6 border-b border-white/5">
                                <div className="flex items-center gap-4">
                                    <div className="w-1.5 h-6 bg-ep-primary rounded-full"></div>
                                    <h3 className="text-xl font-black text-white uppercase italic tracking-tighter">Pulso del Sistema</h3>
                                </div>
                                <span className="text-[10px] font-black text-indigo-400 uppercase tracking-widest italic">Actividad en tiempo real</span>
                            </div>

                            <div className="space-y-6">
                                {recentActivity.map((act, i) => (
                                    <ActivityItem key={i} act={act} />
                                ))}
                            </div>
                        </div>

                        {/* RANKINGS Y ALERTAS */}
                        <div className="space-y-8">
                            <div className="bg-red-500/10 border border-red-500/20 p-8 rounded-[2.5rem] relative overflow-hidden group">
                                <div className="absolute top-0 right-0 p-8 opacity-5">
                                    <svg className="w-24 h-24 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 9v2m0 4h.01" strokeWidth="3"/></svg>
                                </div>
                                <div className="relative z-10">
                                    <h4 className="text-sm font-black text-red-400 uppercase italic tracking-widest mb-4">Alertas de Siniestralidad</h4>
                                    <p className="text-xs font-bold text-red-300/60 leading-relaxed mb-6">
                                        Se han detectado <span className="text-white font-black">{kpis.critical_findings} hallazgos críticos</span> sin resolver y {kpis.pending_assignments} asignaciones vencidas.
                                    </p>
                                    <Link href={route('findings.index')} className="text-[10px] font-black bg-red-500 text-white px-6 py-3 rounded-xl uppercase italic tracking-widest hover:brightness-110 transition-all inline-block">Ver Desvíos</Link>
                                </div>
                            </div>

                            {/* Ranking Técnicos */}
                            <div className="bg-ep-dark/40 backdrop-blur-xl p-8 rounded-[2.5rem] border border-white/5 shadow-2xl">
                                <h4 className="text-sm font-black text-ep-primary uppercase italic tracking-widest mb-6 border-b border-white/5 pb-4">Top Auditores</h4>
                                <div className="space-y-4">
                                    {rankings.top_technicians.map((tech, i) => (
                                        <div key={tech.id} className="flex items-center justify-between bg-white/[0.02] p-4 rounded-2xl border border-white/5 hover:border-ep-primary/30 transition-all group">
                                            <div className="flex items-center gap-4">
                                                <div className="w-8 h-8 rounded-full bg-ep-primary/20 flex items-center justify-center text-ep-primary text-xs font-black italic">#{i+1}</div>
                                                <div className="flex flex-col">
                                                    <span className="text-xs font-black text-white uppercase italic">{tech.nombres}</span>
                                                    <span className="text-[8px] font-bold text-white/20 tracking-widest uppercase italic">Especialista HSE</span>
                                                </div>
                                            </div>
                                            <div className="text-right">
                                                <span className="text-sm font-black text-white italic">{tech.checklists_count}</span>
                                                <span className="block text-[6px] font-bold text-ep-primary uppercase tracking-widest leading-none mt-1">Inspecciones</span>
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </AuthenticatedLayout>
    );
}

function KpiCard({ title, value, subtitle, color, icon }) {
    const colors = {
        'ep-primary': 'text-[#FFD700] bg-[#FFD700]/10 border-[#FFD700]/10',
        'orange': 'text-orange-500 bg-orange-500/10 border-orange-500/10',
        'red': 'text-red-500 bg-red-500/10 border-red-500/10',
        'blue': 'text-blue-500 bg-blue-500/10 border-blue-500/10'
    };

    return (
        <div className={`p-8 rounded-[2.5rem] border border-white/5 bg-ep-dark/40 backdrop-blur-xl shadow-2xl relative overflow-hidden group hover:scale-[1.02] transition-all duration-300`}>
            <div className="absolute top-0 right-0 p-8 opacity-5 group-hover:scale-110 transition-transform">
                {icon}
            </div>
            <div className="relative z-10">
                <div className="flex items-center gap-3 mb-4">
                    <div className={`p-2 rounded-xl bg-white/5 border border-white/5 ${colors[color].split(' ')[0]}`}>
                        {icon}
                    </div>
                    <span className="text-[10px] font-black text-white/40 uppercase italic tracking-widest">{title}</span>
                </div>
                <div className="flex items-baseline gap-2">
                    <span className="text-4xl font-black text-white italic">{value}</span>
                </div>
                <span className="text-[8px] font-black text-ep-primary uppercase italic tracking-[0.2em] mt-2 block">{subtitle}</span>
            </div>
        </div>
    );
}

function ActivityItem({ act }) {
    const icons = {
        inspection: <svg className="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" strokeWidth="3"/></svg>,
        incident: <svg className="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" strokeWidth="3"/></svg>,
    };

    return (
        <div className="flex items-start gap-4 p-4 rounded-3xl bg-white/[0.02] border border-white/5 hover:border-white/10 transition-all hover:bg-white/[0.04]">
            <div className="mt-1">
                <div className="w-10 h-10 bg-black/40 rounded-2xl flex items-center justify-center border border-white/10">
                    {icons[act.type]}
                </div>
            </div>
            <div className="flex-1 min-w-0">
                <div className="flex items-center justify-between mb-1">
                    <span className="text-[9px] font-black text-indigo-400 uppercase italic tracking-widest">{act.title}</span>
                    <span className="text-[8px] font-bold text-white/20 uppercase italic tracking-tighter">{act.date}</span>
                </div>
                <div className="text-xs font-black text-white uppercase italic truncate">{act.description}</div>
                <div className="mt-2 flex items-center gap-3">
                    <div className="flex items-center gap-1.5">
                        <div className="w-1 h-1 bg-white/20 rounded-full"></div>
                        <span className="text-[8px] font-bold text-white/40 uppercase italic tracking-widest">{act.company}</span>
                    </div>
                    <div className="flex items-center gap-1.5">
                        <div className="w-1 h-1 bg-white/20 rounded-full"></div>
                        <span className="text-[8px] font-black text-ep-primary uppercase italic tracking-widest">{act.user}</span>
                    </div>
                </div>
            </div>
        </div>
    );
}
