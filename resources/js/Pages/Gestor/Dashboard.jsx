import React from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, router } from '@inertiajs/react';

export default function Dashboard({ auth, kpis, efficiency, recentEvents, companies, hoursTracking, filters }) {
    
    const updateDateFilter = (from, to) => {
        router.get(route('gestor.dashboard'), { from, to }, { preserveScroll: true });
    };

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={
                <div className="flex flex-col">
                    <h1 className="text-xs font-black text-ep-primary uppercase tracking-widest italic leading-none mb-1">
                        Centro de <span className="text-white">Mando Táctico</span>
                    </h1>
                    <h2 className="text-sm font-bold text-indigo-100 uppercase tracking-tight leading-none italic">
                        Inteligencia Operativa
                    </h2>
                </div>
            }
        >
            <Head title="Sincronización Centro de Mando" />

            <div className="py-6 md:py-12 bg-[#11111d] min-h-screen text-white selection:bg-ep-primary/30 pb-24 md:pb-40">
                <div className="max-w-[98rem] mx-auto px-4 sm:px-6 lg:px-8 space-y-6 md:space-y-12">
                    
                    {/* CABECERA TÁCTICA OPCIONAL (CONTROLES) */}
                    <div className="flex justify-end items-center">
                        <div className="flex flex-col sm:flex-row items-center gap-4 sm:gap-6 bg-ep-dark/60 p-3 sm:p-4 rounded-2xl sm:rounded-3xl border border-white/10 backdrop-blur-2xl shadow-2xl w-full sm:w-auto">
                             <div className="flex items-center gap-3 pr-0 sm:pr-6 border-r-0 sm:border-r border-white/10 w-full sm:w-auto justify-center sm:justify-start">
                                <div className="w-3 h-3 rounded-full bg-emerald-500 animate-pulse shadow-[0_0_8px_#10b981]"></div>
                                <span className="text-[10px] font-black text-white/60 uppercase tracking-widest italic leading-none">Global Link Active</span>
                            </div>
                            <div className="flex items-center gap-2 sm:gap-4 w-full sm:w-auto justify-center">
                                <input id="field_2" name="field_2" 
                                    type="date" 
                                    value={filters.from}
                                    onChange={e => updateDateFilter(e.target.value, filters.to)}
                                    className="bg-black/40 border-none rounded-xl text-[10px] font-black text-white italic focus:ring-ep-primary/50 transition-all [color-scheme:dark] px-3 py-2 sm:px-4 w-full sm:w-auto"
                                />
                                <span className="text-white/10">/</span>
                                <input id="field_1" name="field_1" 
                                    type="date" 
                                    value={filters.to}
                                    onChange={e => updateDateFilter(filters.from, e.target.value)}
                                    className="bg-black/40 border-none rounded-xl text-[10px] font-black text-white italic focus:ring-ep-primary/50 transition-all [color-scheme:dark] px-3 py-2 sm:px-4 w-full sm:w-auto"
                                />
                            </div>
                        </div>
                    </div>

                    {/* KPI CARDS HUD */}
                    <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6 xl:gap-8">
                        <KpiCard 
                            title="Frentes Corporativos" 
                            value={kpis.total_companies} 
                            subtitle="Empresas Activas" 
                            color="indigo" 
                            icon={<svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" strokeWidth="2"/></svg>}
                        />
                        <KpiCard 
                            title="Misiones Completadas" 
                            value={kpis.total_inspections} 
                            subtitle="Inspecciones Totales" 
                            color="emerald" 
                            icon={<svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" strokeWidth="2"/></svg>}
                        />
                        <KpiCard 
                            title="Inteligencia de Desvíos" 
                            value={kpis.total_findings} 
                            subtitle="Hallazgos Detectados" 
                            color="yellow" 
                            icon={<svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" strokeWidth="2"/></svg>}
                        />
                        <KpiCard 
                            title="Bóveda de Informes" 
                            value={kpis.total_reports} 
                            subtitle="Documentos Finalizados" 
                            color="blue" 
                            icon={<svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" strokeWidth="2"/></svg>}
                        />
                    </div>

                    {/* AUDITORÍA DE TIEMPOS HUD */}
                    <div className="bg-ep-dark/40 backdrop-blur-2xl rounded-3xl lg:rounded-[3rem] border border-white/5 p-6 lg:p-8 xl:p-12 shadow-2xl relative overflow-hidden group animate-fade-in-up">
                        <div className="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-ep-primary/30 to-transparent"></div>
                        <div className="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6 mb-8 relative z-10">
                            <div>
                                <h3 className="text-lg lg:text-xl font-black text-white uppercase italic tracking-tighter flex items-center gap-3 leading-none">
                                    <span className="w-1.5 h-6 bg-ep-primary rounded-full shadow-[0_0_8px_rgba(246,146,57,0.5)] shrink-0"></span>
                                    Auditoría Horaria y Control de Tiempos
                                </h3>
                                <p className="text-[9px] font-black text-white/30 uppercase tracking-[0.3em] mt-2 italic ml-4 leading-none">
                                    Módulo de rendimiento mensual: Gabinete vs Campo
                                </p>
                            </div>

                            {/* Alerta de Sobrejornada (Dynamic Alert Badge) */}
                            {hoursTracking?.exceeded_warnings_count > 0 && (
                                <div className="flex items-center gap-3 bg-red-600/10 border border-red-500/20 px-6 py-2.5 rounded-2xl animate-pulse shadow-[0_0_15px_rgba(239,68,68,0.15)] leading-none shrink-0">
                                    <span className="flex h-2 w-2 relative">
                                        <span className="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                        <span className="relative inline-flex rounded-full h-2 w-2 bg-red-500"></span>
                                    </span>
                                    <span className="text-[9px] font-black text-red-400 tracking-widest uppercase italic">
                                        ⚠️ {hoursTracking.exceeded_warnings_count} desvíos de jornada detectados
                                    </span>
                                </div>
                            )}
                        </div>

                        <div className="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8 items-center relative z-10">
                            {/* Horas de Campo */}
                            <div className="bg-black/30 p-5 lg:p-6 rounded-xl lg:rounded-2xl border border-white/5 flex flex-col justify-center">
                                <span className="text-[9px] font-black text-ep-primary uppercase tracking-[0.2em] italic mb-2 leading-none">Horas Efectivas de Campo (Terreno)</span>
                                <div className="flex items-baseline gap-2 mt-1">
                                    <span className="text-3xl lg:text-4xl font-black text-white italic tracking-tighter">{hoursTracking?.field_hours ?? '0.00'}</span>
                                    <span className="text-[10px] font-black text-white/20 uppercase tracking-widest italic">HRS</span>
                                </div>
                                <span className="text-[8px] font-bold text-white/20 uppercase tracking-widest mt-3 italic leading-none">Auditoría física vía Proof of Presence GPS</span>
                            </div>

                            {/* Barra HUD de Proporción Dual (Horizontal Split) */}
                            <div className="bg-black/50 p-5 lg:p-6 rounded-2xl lg:rounded-[2rem] border border-white/5 flex flex-col justify-center gap-4">
                                <div className="flex justify-between text-[9px] font-black uppercase tracking-widest italic text-white/30 leading-none">
                                    <span className="text-ep-primary">CAMPO</span>
                                    <span className="text-blue-400">GABINETE</span>
                                </div>
                                
                                {/* Barra Dual */}
                                <div className="h-2 w-full bg-white/5 rounded-full overflow-hidden flex shadow-inner border border-white/5">
                                    {((hoursTracking?.field_hours || 0) + (hoursTracking?.office_hours || 0)) > 0 ? (
                                        <>
                                            <div 
                                                style={{ width: `${((hoursTracking?.field_hours || 0) / ((hoursTracking?.field_hours || 0) + (hoursTracking?.office_hours || 0))) * 100}%` }}
                                                className="bg-ep-primary h-full shadow-[0_0_10px_rgba(246,142,57,0.5)] transition-all duration-1000"
                                            ></div>
                                            <div 
                                                style={{ width: `${((hoursTracking?.office_hours || 0) / ((hoursTracking?.field_hours || 0) + (hoursTracking?.office_hours || 0))) * 100}%` }}
                                                className="bg-blue-500 h-full shadow-[0_0_10px_rgba(59,130,246,0.5)] transition-all duration-1000"
                                            ></div>
                                        </>
                                    ) : (
                                        <div className="bg-white/10 w-full h-full"></div>
                                    )}
                                </div>

                                <div className="flex justify-between text-[8px] font-black uppercase tracking-wider italic text-white/20 leading-none">
                                    <span>
                                        {((hoursTracking?.field_hours || 0) + (hoursTracking?.office_hours || 0)) > 0 
                                            ? `${Math.round(((hoursTracking?.field_hours || 0) / ((hoursTracking?.field_hours || 0) + (hoursTracking?.office_hours || 0))) * 100)}%`
                                            : '0%'
                                        }
                                    </span>
                                    <span>
                                        {((hoursTracking?.field_hours || 0) + (hoursTracking?.office_hours || 0)) > 0 
                                            ? `${Math.round(((hoursTracking?.office_hours || 0) / ((hoursTracking?.field_hours || 0) + (hoursTracking?.office_hours || 0))) * 100)}%`
                                            : '0%'
                                        }
                                    </span>
                                </div>
                            </div>

                            {/* Horas de Gabinete */}
                            <div className="bg-black/30 p-5 lg:p-6 rounded-xl lg:rounded-2xl border border-white/5 flex flex-col justify-center">
                                <span className="text-[9px] font-black text-blue-400 uppercase tracking-[0.2em] italic mb-2 leading-none">Horas Estimadas de Gabinete (Oficina)</span>
                                <div className="flex items-baseline gap-2 mt-1">
                                    <span className="text-3xl lg:text-4xl font-black text-white italic tracking-tighter">{hoursTracking?.office_hours ?? '0.00'}</span>
                                    <span className="text-[10px] font-black text-white/20 uppercase tracking-widest italic">HRS</span>
                                </div>
                                <span className="text-[8px] font-bold text-white/20 uppercase tracking-widest mt-3 italic leading-none">Estimaciones de oficina por informe técnico</span>
                            </div>
                        </div>
                    </div>
                              {/* MATRIZ DE INTELIGENCIA DE NODOS */}
                    <div className="bg-ep-dark/40 backdrop-blur-2xl rounded-3xl lg:rounded-[3rem] border border-white/5 shadow-2xl relative overflow-hidden group">
                        <div className="absolute top-0 right-0 p-12 opacity-[0.02] group-hover:opacity-[0.05] transition-opacity duration-1000">
                             <svg className="w-64 h-64" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2m0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" strokeWidth="1"/></svg>
                        </div>

                        <div className="px-6 py-6 lg:px-10 lg:py-10 border-b border-white/10 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-black/30">
                            <div>
                                <h3 className="text-xl lg:text-2xl font-black text-white uppercase italic tracking-tighter">Matriz de Inteligencia Corporativa</h3>
                                <p className="text-[10px] font-black text-white/30 uppercase tracking-[0.4em] mt-2 italic">Detección de patrones y cumplimiento por frente</p>
                            </div>
                            <div className="flex items-center gap-6 lg:gap-8">
                                <div className="flex items-center gap-3">
                                    <div className="w-2 h-2 rounded-full bg-ep-primary"></div>
                                    <span className="text-[9px] font-black text-white/20 uppercase tracking-widest italic">Gestión</span>
                                </div>
                                <div className="flex items-center gap-3">
                                    <div className="w-2 h-2 rounded-full bg-red-500"></div>
                                    <span className="text-[9px] font-black text-white/20 uppercase tracking-widest italic">Seguridad</span>
                                </div>
                            </div>
                        </div>

                        {/* VISTA DESKTOP / TABLET (TABLA) */}
                        <div className="hidden md:block overflow-x-auto">
                            <table className="w-full text-left">
                                <thead>
                                    <tr className="bg-black/20 text-white/20 text-[10px] font-black uppercase italic tracking-[0.3em] border-b border-white/5">
                                        <th className="px-6 py-4 xl:px-10 xl:py-6">Identidad Nodo</th>
                                        <th className="px-6 py-4 xl:px-8 xl:py-6 text-center">Gestión Técnica</th>
                                        <th className="px-6 py-4 xl:px-8 xl:py-6 text-center">Seguridad Crítica</th>
                                        <th className="px-6 py-4 xl:px-8 xl:py-6 text-center">Auditoría</th>
                                        <th className="px-6 py-4 xl:px-10 xl:py-6 text-right">Contratos</th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-white/[0.03]">
                                    {companies.map((company) => (
                                        <tr key={company.id} className="group/row hover:bg-white/[0.02] transition-colors">
                                            <td className="px-6 py-4 xl:px-10 xl:py-8">
                                                <div className="flex items-center gap-4 xl:gap-6">
                                                    <div className="w-12 h-12 xl:w-16 xl:h-16 rounded-xl xl:rounded-2xl bg-black/60 border border-white/10 flex items-center justify-center p-2 group-hover/row:border-ep-primary/40 transition-colors shadow-inner overflow-hidden shrink-0">
                                                        {company.logo_path ? (
                                                            <img src={`/storage/${company.logo_path}`} className="max-h-full w-auto object-contain" />
                                                        ) : (
                                                            <span className="text-base xl:text-xl font-black text-white/40 italic">{company.nombre.substring(0,2)}</span>
                                                        )}
                                                    </div>
                                                    <div>
                                                        <div className="text-base xl:text-lg font-black text-white uppercase italic tracking-tighter leading-tight group-hover/row:text-ep-primary transition-colors">{company.nombre}</div>
                                                        <div className="text-[9px] font-bold text-indigo-100/20 uppercase tracking-[0.2em] mt-1 italic">{company.cuit || 'S/D'}</div>
                                                    </div>
                                                </div>
                                            </td>

                                            <td className="px-6 py-4 xl:px-8 xl:py-8 text-center bg-white/[0.01]">
                                                <div className="flex flex-col items-center">
                                                    <div className="flex items-center gap-2 xl:gap-3">
                                                       <span className="text-lg xl:text-xl font-black text-white italic">{company.inspecciones_count}</span>
                                                       <span className="text-[8px] font-black text-white/20 uppercase">Inspecciones</span>
                                                    </div>
                                                    <div className="text-[10px] font-bold text-indigo-400/50 uppercase italic mt-1 tracking-widest">{company.active_assignments_count} Asignadas</div>
                                                </div>
                                            </td>

                                            <td className="px-6 py-4 xl:px-8 xl:py-8 text-center bg-red-500/[0.01]">
                                                <div className="flex flex-col items-center">
                                                    <div className="flex items-center gap-2 xl:gap-3">
                                                       <span className={`text-lg xl:text-xl font-black ${company.hallazgos_count > 0 ? 'text-yellow-500' : 'text-white/10'} italic`}>{company.hallazgos_count}</span>
                                                       <span className="text-[8px] font-black text-white/20 uppercase">Hallazgos</span>
                                                    </div>
                                                    <div className={`text-[10px] font-black italic tracking-widest uppercase mt-1 ${company.incidentes_count > 0 ? 'text-red-500 animate-pulse' : 'text-white/10'}`}>
                                                        {company.incidentes_count} Siniestros
                                                    </div>
                                                </div>
                                            </td>

                                            <td className="px-6 py-4 xl:px-8 xl:py-8 text-center bg-blue-500/[0.01]">
                                                <div className="inline-flex flex-col items-center p-3 xl:p-4 bg-white/5 rounded-xl xl:rounded-2xl border border-white/5 shadow-2xl">
                                                    <span className="text-xl xl:text-2xl font-black text-blue-400 italic leading-none">{company.informes_count}</span>
                                                    <span className="text-[8px] font-black text-blue-400/30 uppercase tracking-widest mt-2">Bóveda</span>
                                                </div>
                                            </td>

                                            <td className="px-6 py-4 xl:px-10 xl:py-8 text-right bg-ep-primary/[0.01]">
                                                <div className="flex flex-col items-end">
                                                    <span className="text-lg xl:text-xl font-black text-white italic">{company.sedes_con_plan_count}</span>
                                                    <div className="px-2.5 py-1 bg-ep-primary/10 border border-ep-primary/20 rounded-lg text-[8px] font-black text-ep-primary uppercase italic tracking-widest mt-2">
                                                        PLANES ACTIVOS
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>

                        {/* VISTA MOBILE (TARJETAS GLASSMORPHIC) */}
                        <div className="grid grid-cols-1 sm:grid-cols-2 gap-6 p-6 md:hidden">
                            {companies.map((company) => (
                                <div key={company.id} className="bg-white/5 border border-white/10 rounded-3xl p-6 backdrop-blur-xl relative overflow-hidden flex flex-col justify-between gap-6 shadow-2xl">
                                    <div className="flex items-center gap-4">
                                        <div className="w-12 h-12 rounded-xl bg-black/60 border border-white/10 flex items-center justify-center p-1.5 shadow-inner overflow-hidden shrink-0">
                                            {company.logo_path ? (
                                                <img src={`/storage/${company.logo_path}`} className="max-h-full w-auto object-contain" />
                                            ) : (
                                                <span className="text-sm font-black text-white/40 italic">{company.nombre.substring(0,2)}</span>
                                            )}
                                        </div>
                                        <div className="min-w-0">
                                            <h4 className="text-base font-black text-white uppercase italic tracking-tight truncate leading-none">{company.nombre}</h4>
                                            <p className="text-[8px] font-bold text-indigo-100/30 uppercase tracking-widest mt-1.5 italic">{company.cuit || 'S/D'}</p>
                                        </div>
                                    </div>

                                    <div className="grid grid-cols-2 gap-3">
                                        <div className="bg-black/35 p-3 rounded-2xl border border-white/5">
                                            <span className="text-[8px] font-black text-white/30 uppercase tracking-wider block mb-1">Gestión</span>
                                            <div className="flex items-baseline gap-1.5 leading-none">
                                                <span className="text-lg font-black text-white italic">{company.inspecciones_count}</span>
                                                <span className="text-[8px] font-bold text-indigo-400/50 uppercase italic">{company.active_assignments_count} Asig</span>
                                            </div>
                                        </div>

                                        <div className="bg-black/35 p-3 rounded-2xl border border-white/5">
                                            <span className="text-[8px] font-black text-white/30 uppercase tracking-wider block mb-1">Hallazgos</span>
                                            <span className={`text-lg font-black italic leading-none ${company.hallazgos_count > 0 ? 'text-yellow-500 animate-pulse' : 'text-white/20'}`}>{company.hallazgos_count}</span>
                                        </div>

                                        <div className="bg-black/35 p-3 rounded-2xl border border-white/5">
                                            <span className="text-[8px] font-black text-white/30 uppercase tracking-wider block mb-1">Siniestros</span>
                                            <span className={`text-lg font-black italic leading-none ${company.incidentes_count > 0 ? 'text-red-500 animate-pulse shadow-[0_0_10px_rgba(239,68,68,0.2)]' : 'text-white/20'}`}>{company.incidentes_count}</span>
                                        </div>

                                        <div className="bg-black/35 p-3 rounded-2xl border border-white/5">
                                            <span className="text-[8px] font-black text-white/30 uppercase tracking-wider block mb-1">Bóveda</span>
                                            <span className="text-lg font-black text-blue-400 italic leading-none">{company.informes_count}</span>
                                        </div>
                                    </div>

                                    <div className="bg-ep-primary/5 border border-ep-primary/10 rounded-2xl p-3 flex justify-between items-center">
                                        <span className="text-[8px] font-black text-ep-primary/60 uppercase tracking-widest italic">Planes Activos</span>
                                        <span className="text-sm font-black text-white italic">{company.sedes_con_plan_count}</span>
                                    </div>
                                </div>
                            ))}
                        </div>

                         <div className="px-6 py-4 lg:px-10 lg:py-6 bg-black/40 border-t border-white/5 flex flex-col sm:flex-row items-center justify-between gap-4">
                            <div className="flex items-center gap-6">
                                <div className="flex items-center gap-2">
                                    <div className="w-1.5 h-1.5 rounded-full bg-emerald-500 shadow-[0_0_5px_#10b981]"></div>
                                    <span className="text-[8px] font-black text-white/10 uppercase tracking-[0.2em]">Enlace Seguro</span>
                                </div>
                                <div className="flex items-center gap-2">
                                    <div className="w-1.5 h-1.5 rounded-full bg-indigo-500 shadow-[0_0_5px_#6366f1]"></div>
                                    <span className="text-[8px] font-black text-white/10 uppercase tracking-[0.2em]">Kernel v5.0.1</span>
                                </div>
                            </div>
                            <span className="text-[9px] font-bold text-white/5 uppercase italic tracking-[0.4em]">Industrias Xamanen Intelligence Terminal</span>
                        </div>
                    </div>
                       <div className="grid grid-cols-1 xl:grid-cols-3 gap-6 lg:gap-8 xl:gap-12">
                         {/* EFICIENCIA PLAN OPERATIVO */}
                         <div className="bg-ep-dark/40 backdrop-blur-2xl rounded-3xl lg:rounded-[3rem] border border-white/5 p-6 lg:p-8 xl:p-12 shadow-2xl relative overflow-hidden group">
                             <div className="absolute top-0 right-0 p-12 opacity-[0.03] group-hover:scale-110 transition-transform duration-1000 rotate-12">
                                <svg className="w-48 h-48" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2m0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" strokeWidth="1"/></svg>
                            </div>
                            
                            <h3 className="text-xl font-black text-white uppercase italic tracking-tighter mb-8 lg:mb-12">Eficiencia Operativa</h3>
                            
                            <div className="flex flex-col items-center">
                                <div className="relative w-40 h-40 lg:w-48 lg:h-48 xl:w-56 xl:h-56 flex items-center justify-center">
                                    <svg viewBox="0 0 224 224" className="w-full h-full transform -rotate-90">
                                        <circle cx="112" cy="112" r="96" stroke="currentColor" strokeWidth="14" fill="transparent" className="text-white/5" />
                                        <circle cx="112" cy="112" r="96" stroke="currentColor" strokeWidth="14" fill="transparent" 
                                            strokeDasharray={603.18}
                                            strokeDashoffset={603.18 - (603.18 * efficiency.percentage) / 100}
                                            className="text-ep-primary transition-all duration-1000 ease-out shadow-[0_0_25px_rgba(246,146,57,0.5)]" 
                                            strokeLinecap="round"
                                        />
                                    </svg>
                                    <div className="absolute inset-0 flex flex-col items-center justify-center">
                                        <span className="text-4xl lg:text-5xl xl:text-6xl font-black text-white italic tracking-tighter leading-none">{efficiency.percentage}%</span>
                                        <span className="text-[8px] lg:text-[10px] font-black text-ep-primary uppercase tracking-[0.4em] mt-2 lg:mt-4 italic">CUMPLIMIENTO</span>
                                    </div>
                                </div>

                                <div className="grid grid-cols-2 gap-4 lg:gap-8 w-full mt-8 lg:mt-16 px-6">
                                    <div className="text-center bg-black/40 p-4 lg:p-6 rounded-3xl border border-white/5 shadow-inner">
                                        <span className="block text-xl lg:text-2xl font-black text-white italic leading-none mb-1">{efficiency.done}</span>
                                        <span className="text-[9px] font-black text-white/20 uppercase tracking-widest">EJECUTADAS</span>
                                    </div>
                                    <div className="text-center bg-black/40 p-4 lg:p-6 rounded-3xl border border-white/5 shadow-inner opacity-40">
                                        <span className="block text-xl lg:text-2xl font-black text-white italic leading-none mb-1">{efficiency.planned}</span>
                                        <span className="text-[9px] font-black text-white/20 uppercase tracking-widest">OBJETIVO</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {/* TERMINAL DE AUDITORÍA TÁCTICA */}
                        <div className="xl:col-span-2 bg-black/60 backdrop-blur-3xl rounded-3xl lg:rounded-[3rem] border border-white/5 p-6 lg:p-8 xl:p-12 shadow-3xl overflow-hidden font-mono relative">
                            <div className="absolute top-6 right-6 lg:top-10 lg:right-10 flex gap-3 z-10">
                                <div className="w-3 h-3 rounded-full bg-white/5"></div>
                                <div className="w-3 h-3 rounded-full bg-white/5"></div>
                                <div className="w-3 h-3 rounded-full bg-ep-primary animate-pulse"></div>
                            </div>

                            <div className="flex items-center gap-6 mb-8 lg:mb-12 pb-6 lg:pb-8 border-b border-white/5 relative z-10">
                                <div className="w-2 h-8 bg-blue-500 rounded-full shadow-[0_0_15px_#3b82f6]"></div>
                                <div>
                                    <h3 className="text-xl lg:text-2xl font-black text-white uppercase italic tracking-tighter">Terminal de Auditoría</h3>
                                    <p className="text-[10px] font-black text-white/20 uppercase tracking-[0.3em] mt-2 italic">Registro de Mantenimiento Operativo Global</p>
                                </div>
                            </div>

                            <div className="space-y-6 max-h-[300px] xl:max-h-[480px] overflow-y-auto pr-4 lg:pr-6 custom-scrollbar scroll-smooth">
                                {recentEvents.map((event, i) => (
                                    <div key={i} className="flex items-start gap-6 group/item opacity-80 hover:opacity-100 transition-all border-b border-white/[0.02] pb-6 last:border-0 translate-x-0 hover:translate-x-2 duration-300">
                                        <span className="text-[10px] text-white/20 font-bold shrink-0 mt-1">[{new Date(event.time).toLocaleTimeString()}]</span>
                                        <div className={`px-4 py-1.5 rounded-xl border shrink-0 text-[10px] font-black italic tracking-widest
                                            ${event.color === 'emerald' ? 'bg-emerald-500/10 border-emerald-500/30 text-emerald-400' : ''}
                                            ${event.color === 'yellow' ? 'bg-yellow-500/10 border-yellow-500/30 text-yellow-500' : ''}
                                            ${event.color === 'red' ? 'bg-red-500/10 border-red-500/30 text-red-500 animate-pulse' : ''}
                                            ${event.color === 'blue' ? 'bg-blue-500/10 border-blue-500/30 text-blue-400' : ''}
                                        `}>
                                            {event.label}
                                        </div>
                                        <div className="flex-grow">
                                            <p className="text-sm text-indigo-100/70 leading-relaxed font-bold italic group-hover/item:text-white transition-colors">
                                                {event.description}
                                            </p>
                                            <div className="mt-3 text-[10px] text-white/10 uppercase font-black italic">Sincronizado: {event.diffText}</div>
                                        </div>
                                    </div>
                                ))}
                            </div>

                            <div className="mt-12 pt-6 border-t border-white/5 flex items-center justify-between opacity-30">
                                <span className="text-[9px] font-black uppercase tracking-widest">Protocolo: SSL-V4_ENCRYPTED</span>
                                <div className="flex items-center gap-3">
                                    <span className="w-2 h-2 rounded-full bg-emerald-500"></span>
                                    <span className="text-[9px] font-black uppercase tracking-widest animate-pulse">Waiting for events...</span>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <style dangerouslySetInnerHTML={{ __html: `
                .custom-scrollbar::-webkit-scrollbar { width: 4px; }
                .custom-scrollbar::-webkit-scrollbar-track { background: rgba(255,255,255,0.02); }
                .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.05); border-radius: 10px; }
                .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.1); }
                .shadow-3xl { box-shadow: 0 35px 60px -15px rgba(0, 0, 0, 0.7); }
            `}} />
        </AuthenticatedLayout>
    );
}

function KpiCard({ title, value, subtitle, color, icon }) {
    const colorMap = {
        indigo: 'text-indigo-400 bg-indigo-500/10 border-indigo-500/20 shadow-indigo-500/20',
        emerald: 'text-emerald-400 bg-emerald-500/10 border-emerald-500/20 shadow-emerald-500/20',
        yellow: 'text-yellow-500 bg-yellow-500/10 border-yellow-500/20 shadow-yellow-500/20',
        blue: 'text-blue-400 bg-blue-500/10 border-blue-500/20 shadow-blue-500/20',
        red: 'text-red-500 bg-red-500/10 border-red-500/20 shadow-red-500/20'
    };

    return (
        <div className={`p-6 lg:p-8 xl:p-10 rounded-2xl lg:rounded-[2.5rem] xl:rounded-[3.5rem] border border-white/10 bg-ep-dark/60 backdrop-blur-2xl shadow-3xl relative overflow-hidden group hover:scale-[1.03] transition-all duration-500 border-l-4 lg:border-l-6 xl:border-l-8 ${color === 'indigo' ? 'border-l-indigo-500' : color === 'emerald' ? 'border-l-emerald-500' : color === 'yellow' ? 'border-l-yellow-600' : 'border-l-blue-600'}`}>
            <div className={`absolute top-0 right-0 p-6 lg:p-8 xl:p-10 opacity-[0.05] group-hover:scale-125 transition-transform duration-700 ${colorMap[color].split(' ')[0]}`}>
                {icon}
            </div>
            
            <div className="relative z-10">
                <div className="flex items-center gap-3 lg:gap-4 mb-4 lg:mb-6 xl:mb-8">
                    <div className={`p-2 lg:p-3 rounded-xl lg:rounded-2xl border ${colorMap[color]}`}>
                        {icon}
                    </div>
                    <h4 className="text-[10px] font-black text-white/30 uppercase italic tracking-[0.3em]">{title}</h4>
                </div>
                
                <div className="flex items-baseline gap-2 lg:gap-4">
                    <span className="text-4xl lg:text-5xl xl:text-6xl font-black text-white italic tracking-tighter leading-none">{value}</span>
                    <span className="text-[10px] font-black text-white/10 uppercase tracking-widest italic leading-none">Global</span>
                </div>
                
                <p className={`text-[11px] font-black uppercase italic tracking-[0.2em] mt-4 lg:mt-6 xl:mt-8 ${colorMap[color].split(' ')[0]}`}>
                    {subtitle}
                </p>
            </div>
            <div className="absolute inset-x-0 bottom-0 h-[2px] bg-gradient-to-r from-transparent via-white/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
        </div>
    );
}
