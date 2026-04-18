import React from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, router } from '@inertiajs/react';

export default function Dashboard({ auth, kpis, efficiency, recentActivity, topTechnicians, filters }) {
    
    const updateDateFilter = (from, to) => {
        router.get(route('gestor.dashboard'), { from, to }, { preserveScroll: true });
    };

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={<h2 className="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Dashboard Operativo</h2>}
        >
            <Head title="Gestor Dashboard" />

            <div className="py-12 bg-[#0f0f1a] min-h-screen text-white selection:bg-ep-primary/30">
                <div className="max-w-[98rem] mx-auto sm:px-6 lg:px-8 space-y-8">
                    
                    {/* CABECERA Y FILTROS */}
                    <div className="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                        <div className="flex items-center gap-4">
                             <div className="w-12 h-12 bg-ep-primary/10 rounded-2xl flex items-center justify-center border border-ep-primary/20 shadow-[0_0_20px_rgba(255,215,0,0.1)]">
                                <svg className="w-6 h-6 text-ep-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M13 10V3L4 14h7v7l9-11h-7z" strokeWidth="2"/></svg>
                            </div>
                            <div>
                                <h1 className="text-3xl font-black text-white italic uppercase tracking-tighter">
                                    Control <span className="text-ep-primary">Operativo</span>
                                </h1>
                                <p className="text-[10px] font-bold text-white/20 uppercase tracking-widest mt-1">Gestión Táctica - {auth.user.consultora?.nombre || 'Consultora'}</p>
                            </div>
                        </div>

                        <div className="flex items-center gap-4 bg-white/5 p-2 rounded-2xl border border-white/5 backdrop-blur-md">
                            <input 
                                type="date" 
                                value={filters.from}
                                onChange={e => updateDateFilter(e.target.value, filters.to)}
                                className="bg-transparent border-none text-[10px] font-black text-white italic focus:ring-0 [color-scheme:dark]"
                            />
                            <span className="text-white/10">|</span>
                            <input 
                                type="date" 
                                value={filters.to}
                                onChange={e => updateDateFilter(filters.from, e.target.value)}
                                className="bg-transparent border-none text-[10px] font-black text-white italic focus:ring-0 [color-scheme:dark]"
                            />
                        </div>
                    </div>

                    {/* KPI CARDS */}
                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <KpiCard 
                            title="Inspecciones" 
                            value={kpis.inspections} 
                            subtitle="Mes Actual" 
                            color="ep-primary" 
                            icon={<svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" strokeWidth="2"/></svg>}
                        />
                        <KpiCard 
                            title="Desvíos" 
                            value={kpis.findings} 
                            subtitle="Detectados" 
                            color="orange" 
                            icon={<svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" strokeWidth="2"/></svg>}
                        />
                        <KpiCard 
                            title="Incidentes" 
                            value={kpis.incidents} 
                            subtitle="Siniestralidad" 
                            color="red" 
                            icon={<svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M13 10V3L4 14h7v7l9-11h-7z" strokeWidth="2"/></svg>}
                        />
                         <KpiCard 
                            title="Formación" 
                            value={kpis.trainings} 
                            subtitle="Capacitaciones" 
                            color="blue" 
                            icon={<svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 14l9-5-9-5-9 5 9 5z" strokeWidth="2"/><path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" strokeWidth="2"/></svg>}
                        />
                    </div>

                    <div className="grid grid-cols-1 xl:grid-cols-3 gap-8">
                        
                        {/* PANEL DE EFICIENCIA PPD */}
                        <div className="bg-ep-dark/40 backdrop-blur-xl rounded-[2.5rem] border border-white/5 p-10 shadow-2xl relative overflow-hidden group col-span-1 xl:col-span-1">
                             <div className="absolute top-0 right-0 p-10 opacity-[0.03] group-hover:scale-110 transition-transform duration-700">
                                <svg className="w-48 h-48" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" strokeWidth="1"/></svg>
                            </div>
                            
                            <h3 className="text-sm font-black text-ep-primary uppercase italic tracking-widest mb-10">Eficiencia Plan Operativo</h3>
                            
                            <div className="flex flex-col items-center justify-center space-y-8">
                                <div className="relative w-48 h-48 flex items-center justify-center">
                                    <svg className="w-full h-full transform -rotate-90">
                                        <circle cx="96" cy="96" r="88" stroke="currentColor" strokeWidth="12" fill="transparent" className="text-white/5" />
                                        <circle cx="96" cy="96" r="88" stroke="currentColor" strokeWidth="12" fill="transparent" 
                                            strokeDasharray={552.92}
                                            strokeDashoffset={552.92 - (552.92 * efficiency.percentage) / 100}
                                            className="text-ep-primary transition-all duration-1000 ease-out shadow-[0_0_10px_rgba(255,215,0,0.5)]" 
                                        />
                                    </svg>
                                    <div className="absolute inset-0 flex flex-col items-center justify-center">
                                        <span className="text-5xl font-black text-white italic">{efficiency.percentage}%</span>
                                        <span className="text-[8px] font-bold text-white/20 uppercase tracking-[0.3em] mt-1">CUMPLIMIENTO</span>
                                    </div>
                                </div>

                                <div className="grid grid-cols-2 gap-8 w-full">
                                    <div className="text-center bg-white/[0.02] p-4 rounded-2xl border border-white/5">
                                        <span className="block text-xl font-black text-white italic">{efficiency.done}</span>
                                        <span className="text-[8px] font-bold text-white/30 uppercase tracking-widest">Ejecutadas</span>
                                    </div>
                                    <div className="text-center bg-white/[0.02] p-4 rounded-2xl border border-white/5">
                                        <span className="block text-xl font-black text-white/60 italic">{efficiency.planned}</span>
                                        <span className="text-[8px] font-bold text-white/30 uppercase tracking-widest">Planificadas</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {/* FEED DE ACTIVIDAD - MISIONES DE CAMPO */}
                        <div className="xl:col-span-2 bg-ep-dark/40 backdrop-blur-xl rounded-[2.5rem] border border-white/5 p-10 shadow-2xl overflow-hidden">
                            <div className="flex items-center justify-between mb-10 pb-6 border-b border-white/5">
                                <div className="flex items-center gap-4">
                                    <div className="w-2 h-8 bg-ep-primary rounded-full shadow-[0_0_15px_rgba(255,215,0,0.3)]"></div>
                                    <h3 className="text-xl font-black text-white uppercase italic tracking-tighter">Misiones de Campo</h3>
                                </div>
                                <span className="text-[8px] font-black text-indigo-400 uppercase tracking-widest italic bg-indigo-500/10 px-4 py-1.5 rounded-full border border-indigo-500/20">Actividad Reciente</span>
                            </div>

                            <div className="space-y-4 max-h-[500px] overflow-y-auto pr-4 custom-scrollbar">
                                {recentActivity.map((act, i) => (
                                    <ActivityItem key={i} act={act} />
                                ))}
                                {recentActivity.length === 0 && (
                                    <div className="py-20 text-center opacity-20">
                                        <p className="text-xl font-black italic uppercase tracking-[0.2em]">Sin actividad reciente</p>
                                    </div>
                                )}
                            </div>
                        </div>
                    </div>

                    {/* RANKING TÉCNICOS */}
                    <div className="bg-ep-dark/40 backdrop-blur-xl p-10 rounded-[2.5rem] border border-white/5 shadow-2xl">
                         <div className="flex items-center gap-4 mb-10">
                            <svg className="w-6 h-6 text-ep-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" strokeWidth="2"/></svg>
                            <h4 className="text-lg font-black text-white uppercase italic tracking-tighter">Top Auditores HSE</h4>
                        </div>
                        <div className="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-6">
                            {topTechnicians.map((tech, i) => (
                                <div key={tech.id} className="relative bg-white/[0.02] p-6 rounded-[2rem] border border-white/5 hover:border-ep-primary/30 transition-all group pt-10">
                                    <div className="absolute top-0 left-0 p-6 opacity-5 group-hover:scale-110 transition-transform">
                                        <span className="text-6xl font-black italic">#{i+1}</span>
                                    </div>
                                    <div className="relative z-10 flex flex-col items-center text-center">
                                        <div className="w-16 h-16 rounded-2xl bg-gradient-to-br from-indigo-500/20 to-purple-500/20 flex items-center justify-center border border-white/10 mb-4 group-hover:shadow-[0_0_20px_rgba(79,70,229,0.2)] transition-all">
                                            <span className="text-xl font-black text-white italic">{tech.nombres.substring(0,1)}{tech.apellidos.substring(0,1)}</span>
                                        </div>
                                        <span className="text-xs font-black text-white uppercase italic">{tech.nombres}</span>
                                        <span className="text-[8px] font-bold text-white/20 tracking-widest uppercase italic mt-1">{tech.checklists_count} Inspecciones</span>
                                        <div className="mt-4 w-full h-1 bg-white/5 rounded-full overflow-hidden">
                                            <div className="h-full bg-ep-primary" style={{ width: `${Math.min(100, (tech.checklists_count / 10) * 100)}%` }}></div>
                                        </div>
                                    </div>
                                </div>
                            ))}
                        </div>
                    </div>

                </div>
            </div>

             <style dangerouslySetInnerHTML={{ __html: `
                .custom-scrollbar::-webkit-scrollbar { width: 4px; }
                .custom-scrollbar::-webkit-scrollbar-track { background: rgba(0,0,0,0.1); }
                .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.05); border-radius: 10px; }
                .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.1); }
            `}} />
        </AuthenticatedLayout>
    );
}

function KpiCard({ title, value, subtitle, color, icon }) {
    const colors = {
        'ep-primary': 'text-[#FFD700] bg-[#FFD700]/10 border-[#FFD700]/10',
        'orange': 'text-orange-500 bg-orange-500/10 border-orange-500/10',
        'red': 'text-red-500 bg-red-500/10 border-red-500/10',
        'blue': 'text-indigo-500 bg-indigo-500/10 border-indigo-500/10'
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
                    <span className="text-[9px] font-black text-white/40 uppercase italic tracking-widest">{title}</span>
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
    return (
        <div className="flex items-start gap-4 p-5 rounded-[2rem] bg-white/[0.02] border border-white/5 hover:border-white/10 transition-all hover:bg-white/[0.04] group/item">
            <div className="mt-1">
                <div className="w-12 h-12 bg-black/40 rounded-2xl flex items-center justify-center border border-white/10 group-hover/item:border-ep-primary/30 transition-all">
                    <svg className="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" strokeWidth="3"/></svg>
                </div>
            </div>
            <div className="flex-1 min-w-0">
                <div className="flex items-center justify-between mb-2">
                    <span className="text-[10px] font-black text-indigo-400 uppercase italic tracking-widest">{act.title}</span>
                    <span className="text-[8px] font-bold text-white/20 uppercase italic">{act.date}</span>
                </div>
                <div className="text-xs font-black text-white uppercase italic truncate group-hover/item:text-ep-primary transition-colors">{act.description}</div>
                <div className="mt-3 flex items-center gap-4">
                    <div className="flex items-center gap-2">
                        <div className="w-1 h-1 bg-white/20 rounded-full"></div>
                        <span className="text-[8px] font-bold text-white/40 uppercase italic tracking-widest">{act.company}</span>
                    </div>
                    <div className="flex items-center gap-2">
                        <div className="w-1 h-1 bg-white/20 rounded-full"></div>
                        <span className="text-[8px] font-black text-ep-primary uppercase italic tracking-widest">{act.user}</span>
                    </div>
                </div>
            </div>
        </div>
    );
}
