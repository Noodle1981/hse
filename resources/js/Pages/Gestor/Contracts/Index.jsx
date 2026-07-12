import React, { useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, router, Link } from '@inertiajs/react';
import AnnualPlanMatrix from './Partials/AnnualPlanMatrix';
import PlanningForm from './Partials/PlanningForm';

export default function Index({ 
    auth, 
    companies, 
    sedes, 
    activeSede, 
    plans, 
    dynamicMonths, 
    complianceData, 
    efficiency, 
    filters,
    checklistOptions 
}) {
    const [editMode, setEditMode] = useState(false);
    const [loading, setLoading] = useState(false);
    const isTech = auth.user.rol?.toLowerCase() === 'tecnico' || auth.user.rol?.toLowerCase() === 'técnico';

    const updateFilter = (newFilters) => {
        setLoading(true);
        router.get(route('annual-contract.index'), {
            ...filters,
            ...newFilters
        }, {
            preserveState: true,
            preserveScroll: true,
            onFinish: () => setLoading(false)
        });
    };

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={
                isTech ? (
                    <div className="flex flex-col">
                        <h1 className="text-xs font-black text-ep-primary uppercase tracking-widest italic leading-none mb-1">
                            Visor de <span className="text-white">Cronograma Operativo</span>
                        </h1>
                        <h2 className="text-sm font-bold text-indigo-100 uppercase tracking-tight leading-none italic">
                            Consulta de Actividades Programadas (PPD)
                        </h2>
                    </div>
                ) : (
                    <div className="flex flex-col">
                        <h1 className="text-xs font-black text-ep-primary uppercase tracking-widest italic leading-none mb-1">
                            Generador <span className="text-white">Plan Operativo</span>
                        </h1>
                        <h2 className="text-sm font-bold text-indigo-100 uppercase tracking-tight leading-none italic">
                            Control y Seguimiento PPD
                        </h2>
                    </div>
                )
            }
        >
            <Head title="Contrato Anual" />

            <div className={`py-6 bg-[#11111d] min-h-screen text-white selection:bg-ep-primary/30 ${loading ? 'opacity-70 pointer-events-none' : ''} transition-opacity`}>
                <div className="max-w-[98rem] mx-auto sm:px-6 lg:px-8 space-y-6">
                    
                    
                    {/* HUB DE CONTROLES */}
                    <div className="bg-ep-dark/40 backdrop-blur-xl p-6 rounded-[2rem] border border-white/5 shadow-2xl">
                        <div className="flex flex-col xl:flex-row gap-6 items-stretch xl:items-center">
                            
                            {/* Selectores Empresa / Sede */}
                            <div className="flex-1 grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div className="space-y-2">
                                    <label className="text-[10px] font-black text-indigo-100/40 uppercase tracking-[0.2em] italic ml-1 mb-1 block">Empresa Mandante</label>
                                    <select id="field_4" name="field_4" 
                                        value={filters.company_id}
                                        onChange={e => updateFilter({ company_id: e.target.value, sede_id: '' })}
                                        className="w-full bg-[#16162a] border border-white/10 rounded-2xl px-5 py-3 text-sm font-bold text-white focus:ring-ep-primary transition-all [color-scheme:dark]"
                                    >
                                        {companies.map(c => <option key={c.id} value={c.id} className="bg-[#16162a] text-white">{c.nombre}</option>)}
                                    </select>
                                </div>
                                <div className="space-y-2">
                                    <label className="text-[10px] font-black text-indigo-100/40 uppercase tracking-[0.2em] italic ml-1 mb-1 block">Activo / Sede Operativa</label>
                                    <select id="field_3" name="field_3" 
                                        value={filters.sede_id}
                                        onChange={e => updateFilter({ sede_id: e.target.value })}
                                        className="w-full bg-[#16162a] border border-white/10 rounded-2xl px-5 py-3 text-sm font-bold text-white focus:ring-ep-primary transition-all [color-scheme:dark]"
                                    >
                                        <option value="" className="bg-[#16162a] text-white">Seleccione Sede</option>
                                        {sedes.map(s => <option key={s.id} value={s.id} className="bg-[#16162a] text-white">{s.nombre_sede}</option>)}
                                    </select>
                                </div>
                            </div>

                            {/* Configuración Plan: Solo para Gestores */}
                            {!isTech && (
                                <div className="flex items-center gap-4 bg-indigo-600/10 p-3 rounded-2xl border border-indigo-500/20">
                                    <div>
                                        <label className="text-[9px] font-black text-indigo-100/40 uppercase tracking-widest italic block mb-1">Inicio</label>
                                        <input id="field_2" name="field_2" 
                                            type="month" 
                                            value={filters.contract_start}
                                            onChange={e => updateFilter({ contract_start: e.target.value })}
                                            className="bg-black/40 border-none rounded-xl px-4 py-2 text-xs font-bold text-white [color-scheme:dark]"
                                        />
                                    </div>
                                    <div className="w-px h-8 bg-indigo-500/20"></div>
                                    <div>
                                        <label className="text-[9px] font-black text-indigo-100/40 uppercase tracking-widest italic block mb-1">Duración</label>
                                        <select id="field_1" name="field_1" 
                                            value={filters.duration}
                                            onChange={e => updateFilter({ duration: e.target.value })}
                                            className="bg-[#16162a] border-none rounded-xl px-4 py-2 text-xs font-bold text-white [color-scheme:dark]"
                                        >
                                            <option value={3} className="bg-[#16162a] text-white">3 MESES</option>
                                            <option value={6} className="bg-[#16162a] text-white">6 MESES</option>
                                            <option value={12} className="bg-[#16162a] text-white">12 MESES</option>
                                            <option value={24} className="bg-[#16162a] text-white">24 MESES</option>
                                        </select>
                                    </div>
                                </div>
                            )}

                            {/* Acciones */}
                            <div className="flex gap-3">
                                {!isTech && (
                                    <button 
                                        onClick={() => setEditMode(!editMode)}
                                        className={`px-10 rounded-2xl font-black text-[11px] uppercase tracking-widest italic transition-all flex items-center justify-center gap-3 h-14 ${editMode ? 'bg-indigo-600 shadow-[0_0_30px_rgba(79,70,229,0.5)]' : 'bg-ep-primary shadow-2xl shadow-ep-primary/20 hover:scale-105'}`}
                                    >
                                        <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="3"><path d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                        {editMode ? 'GUARDAR PLAN' : 'PLANIFICAR'}
                                    </button>
                                )}
                                <a 
                                    href={route('annual-contract.pdf', { sede_id: filters.sede_id, year: filters.contract_start.substring(0,4) })}
                                    target="_blank"
                                    className="px-10 bg-white text-black rounded-2xl font-black text-[11px] uppercase tracking-widest italic hover:bg-black hover:text-white transition-all shadow-2xl flex items-center justify-center gap-3 h-14" rel="noreferrer"
                                >
                                    <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="3"><path d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                    PDF
                                </a>
                            </div>
                        </div>

                        {/* INFO TÁCTICA EXPANDIDA */}
                        {activeSede && (
                            <div className="mt-8 pt-6 border-t border-white/5 flex flex-wrap items-center justify-between gap-8">
                                <div className="flex flex-wrap items-center gap-4">
                                    <div className="flex items-center gap-4 bg-black/40 px-6 py-4 rounded-[1.5rem] border border-white/5 group hover:border-ep-primary/30 transition-all">
                                        <div className="w-10 h-10 bg-ep-primary/10 rounded-xl flex items-center justify-center text-ep-primary">
                                            <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" strokeWidth="2"/></svg>
                                        </div>
                                        <div>
                                            <span className="block text-[8px] font-black text-indigo-100/20 uppercase tracking-widest mb-1 italic">Ruta Operativa Actual</span>
                                            <div className="flex items-center gap-3">
                                                <span className="text-[11px] font-black text-white/90 uppercase italic tracking-tight">{activeSede.direccion || 'UBICACIÓN NO REGISTRADA'}</span>
                                                <a 
                                                    href={`https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(activeSede.direccion || activeSede.nombre_sede)}`}
                                                    target="_blank"
                                                    className="px-3 py-1 bg-indigo-500/10 border border-indigo-500/20 rounded-lg text-[8px] font-black text-indigo-400 uppercase tracking-widest hover:bg-indigo-500 hover:text-white transition-all italic" rel="noreferrer"
                                                >
                                                    MAPS
                                                </a>
                                            </div>
                                        </div>
                                    </div>

                                    <div className="flex items-center gap-4 bg-black/40 px-6 py-4 rounded-[1.5rem] border border-white/5 group hover:border-emerald-500/30 transition-all">
                                        <div className="w-10 h-10 bg-emerald-500/10 rounded-xl flex items-center justify-center text-emerald-500">
                                            <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" strokeWidth="1.5"/></svg>
                                        </div>
                                        <div>
                                            <span className="block text-[8px] font-black text-indigo-100/20 uppercase tracking-widest mb-1 italic">Fuerza Técnica Asignada</span>
                                            <div className="flex items-center gap-2">
                                                {activeSede.users?.map(u => (
                                                    <div key={u.id} className="flex items-center gap-2 pr-4 last:pr-0">
                                                        <span className="text-[10px] font-black text-white uppercase italic">{u.nombres} {u.apellidos}</span>
                                                        {u.telefono && (
                                                            <a 
                                                                href={`https://wa.me/${u.telefono.replace(/[^0-9]/g, '')}`}
                                                                target="_blank"
                                                                className="text-emerald-500 hover:scale-110 transition-all" rel="noreferrer"
                                                            >
                                                                <svg className="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                                                            </a>
                                                        )}
                                                    </div>
                                                ))}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {/* EFICIENCIA KPI */}
                                <div className="flex items-center gap-8">
                                    <div className="text-right">
                                        <span className="block text-[8px] font-black text-indigo-100/30 uppercase tracking-widest mb-1 italic">Cumplimiento Global</span>
                                        <div className="flex items-center gap-3">
                                            <span className="text-2xl font-black text-white italic">{efficiency.global}%</span>
                                            <div className="w-24 h-2 bg-white/5 rounded-full overflow-hidden">
                                                <div className="h-full bg-ep-primary transition-all duration-1000" style={{ width: `${efficiency.global}%` }}></div>
                                            </div>
                                        </div>
                                    </div>
                                    {efficiency.counts.overdue > 0 && (
                                        <div className="px-4 py-2 bg-red-500/10 border border-red-500/30 rounded-2xl animate-pulse">
                                            <span className="text-[10px] font-black text-red-500 uppercase italic tracking-widest">{efficiency.counts.overdue} ALERTAS</span>
                                        </div>
                                    )}
                                </div>
                            </div>
                        )}
                    </div>

                    {/* FORMULARIO DE PLANIFICACIÓN */}
                    {editMode && (
                        <PlanningForm 
                            sedeId={filters.sede_id} 
                            contractStart={filters.contract_start}
                            duration={filters.duration}
                            checklistOptions={checklistOptions}
                        />
                    )}

                    {/* MATRIZ DE CUMPLIMIENTO */}
                    <div className="bg-[#141421] rounded-[2.5rem] border border-white/5 shadow-2xl relative group">
                        <div className="px-8 py-6 border-b border-white/5 flex items-center justify-between bg-black/20">
                             <div className="flex items-center gap-3">
                                <div className="w-2 h-6 bg-ep-primary rounded-full"></div>
                                <h4 className="text-base font-black text-white italic uppercase tracking-widest tracking-tighter">Matriz de Cumplimiento Operativo</h4>
                            </div>
                            <div className="flex gap-6">
                                <div className="flex items-center gap-2"><div className="w-2 h-2 rounded-full bg-emerald-500"></div><span className="text-[9px] font-bold text-white/40 uppercase">REALIZADO</span></div>
                                <div className="flex items-center gap-2"><div className="w-2 h-2 rounded-full bg-indigo-500 shadow-[0_0_8px_rgba(99,102,241,0.5)]"></div><span className="text-[9px] font-bold text-white/40 uppercase">PROGRAMADO</span></div>
                                <div className="flex items-center gap-2"><div className="w-2 h-2 rounded-full bg-red-500 pulse"></div><span className="text-[9px] font-bold text-white/40 uppercase tracking-tighter">ALERTA / VENCIDO</span></div>
                            </div>
                        </div>

                        <AnnualPlanMatrix 
                            plans={plans} 
                            dynamicMonths={dynamicMonths} 
                            complianceData={complianceData} 
                            editMode={editMode}
                            checklistOptions={checklistOptions}
                        />
                    </div>

                </div>
            </div>
            
            <style dangerouslySetInnerHTML={{ __html: `
                .pulse { animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite; }
                @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: .5; } }
            `}} />
        </AuthenticatedLayout>
    );
}
