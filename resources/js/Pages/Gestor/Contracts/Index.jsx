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
            header={<h2 className="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Plan Operativo Anual (PPD)</h2>}
        >
            <Head title="Contrato Anual" />

            <div className={`py-8 bg-[#11111d] min-h-screen text-white selection:bg-ep-primary/30 ${loading ? 'opacity-70 pointer-events-none' : ''} transition-opacity`}>
                <div className="max-w-[98rem] mx-auto sm:px-6 lg:px-8 space-y-8">
                    
                    {/* HUB DE CONTROLES */}
                    <div className="bg-ep-dark/40 backdrop-blur-xl p-6 rounded-[2rem] border border-white/5 shadow-2xl">
                        <div className="flex flex-col xl:flex-row gap-6 items-stretch xl:items-center">
                            
                            {/* Selectores Empresa / Sede */}
                            <div className="flex-1 grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div className="space-y-2">
                                    <label className="text-[10px] font-black text-indigo-100/40 uppercase tracking-[0.2em] italic ml-1 mb-1 block">Empresa Mandante</label>
                                    <select 
                                        value={filters.company_id}
                                        onChange={e => updateFilter({ company_id: e.target.value, sede_id: '' })}
                                        className="w-full bg-black/40 border border-white/10 rounded-2xl px-5 py-3 text-sm font-bold text-white focus:ring-ep-primary transition-all"
                                    >
                                        {companies.map(c => <option key={c.id} value={c.id}>{c.nombre}</option>)}
                                    </select>
                                </div>
                                <div className="space-y-2">
                                    <label className="text-[10px] font-black text-indigo-100/40 uppercase tracking-[0.2em] italic ml-1 mb-1 block">Activo / Sede Operativa</label>
                                    <select 
                                        value={filters.sede_id}
                                        onChange={e => updateFilter({ sede_id: e.target.value })}
                                        className="w-full bg-black/40 border border-white/10 rounded-2xl px-5 py-3 text-sm font-bold text-white focus:ring-ep-primary transition-all"
                                    >
                                        <option value="">Seleccione Sede</option>
                                        {sedes.map(s => <option key={s.id} value={s.id}>{s.nombre_sede}</option>)}
                                    </select>
                                </div>
                            </div>

                            {/* Configuración Plan */}
                            <div className="flex items-center gap-4 bg-indigo-600/10 p-3 rounded-2xl border border-indigo-500/20">
                                <div>
                                    <label className="text-[9px] font-black text-indigo-100/40 uppercase tracking-widest italic block mb-1">Inicio</label>
                                    <input 
                                        type="month" 
                                        value={filters.contract_start}
                                        onChange={e => updateFilter({ contract_start: e.target.value })}
                                        className="bg-black/40 border-none rounded-xl px-4 py-2 text-xs font-bold text-white [color-scheme:dark]"
                                    />
                                </div>
                                <div className="w-px h-8 bg-indigo-500/20"></div>
                                <div>
                                    <label className="text-[9px] font-black text-indigo-100/40 uppercase tracking-widest italic block mb-1">Duración</label>
                                    <select 
                                        value={filters.duration}
                                        onChange={e => updateFilter({ duration: e.target.value })}
                                        className="bg-black/40 border-none rounded-xl px-4 py-2 text-xs font-bold text-white"
                                    >
                                        <option value={3}>3 MESES</option>
                                        <option value={6}>6 MESES</option>
                                        <option value={12}>12 MESES</option>
                                        <option value={24}>24 MESES</option>
                                    </select>
                                </div>
                            </div>

                            {/* Acciones */}
                            <div className="flex gap-3">
                                {auth.user.rol !== 'técnico' && (
                                    <button 
                                        onClick={() => setEditMode(!editMode)}
                                        className={`px-8 rounded-2xl font-black text-[10px] uppercase tracking-widest italic transition-all flex items-center justify-center gap-2 h-12 ${editMode ? 'bg-indigo-600 shadow-[0_0_20px_rgba(79,70,229,0.4)]' : 'bg-white/5 border border-white/10 hover:bg-white/10'}`}
                                    >
                                        {editMode ? 'GUARDAR PLAN' : 'PLANIFICAR'}
                                    </button>
                                )}
                                <a 
                                    href={route('annual-contract.pdf', { sede_id: filters.sede_id, year: filters.contract_start.substring(0,4) })}
                                    target="_blank"
                                    className="px-8 bg-white text-black rounded-2xl font-black text-[10px] uppercase tracking-widest italic hover:bg-ep-primary hover:text-white transition-all shadow-xl flex items-center justify-center gap-2 h-12"
                                >
                                    PDF
                                </a>
                            </div>
                        </div>

                        {/* INFO TÁCTICA */}
                        {activeSede && (
                            <div className="mt-6 pt-6 border-t border-white/5 flex flex-wrap items-center justify-between gap-6">
                                <div className="flex items-center gap-4 bg-white/5 px-6 py-3 rounded-2xl border border-white/5">
                                    <div className="flex flex-col">
                                        <span className="text-[10px] font-black text-white/90 uppercase tracking-tight italic">{activeSede.direccion || 'DIRECCIÓN NO DISPONIBLE'}</span>
                                        <span className="text-[8px] font-bold text-white/20 uppercase tracking-widest mt-1">Sede Central - {activeSede.empresa?.nombre}</span>
                                    </div>
                                    <div className="flex items-center gap-3 border-l border-white/10 ml-4 pl-4">
                                        <span className="text-[8px] font-black text-indigo-400 uppercase italic tracking-widest">EQUIPO:</span>
                                        {activeSede.users?.map(u => (
                                            <span key={u.id} className="text-[9px] font-black text-white/60 uppercase italic bg-indigo-500/10 px-3 py-1 rounded-xl">{u.nombres}</span>
                                        ))}
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
                    <div className="bg-ep-dark/60 backdrop-blur-md rounded-[2.5rem] border border-white/5 shadow-2xl relative overflow-hidden group">
                        <div className="px-8 py-6 border-b border-white/5 flex items-center justify-between bg-black/20">
                             <div className="flex items-center gap-3">
                                <div className="w-2 h-6 bg-ep-primary rounded-full"></div>
                                <h4 className="text-base font-black text-white italic uppercase tracking-widest tracking-tighter">Matriz de Cumplimiento Operativo</h4>
                            </div>
                            <div className="flex gap-6">
                                <div className="flex items-center gap-2"><div className="w-2 h-2 rounded-full bg-emerald-500"></div><span className="text-[9px] font-bold text-white/40 uppercase">REALIZADO</span></div>
                                <div className="flex items-center gap-2"><div className="w-2 h-2 rounded-full bg-yellow-400"></div><span className="text-[9px] font-bold text-white/40 uppercase">PROGRAMADO</span></div>
                                <div className="flex items-center gap-2"><div className="w-2 h-2 rounded-full bg-red-500 pulse"></div><span className="text-[9px] font-bold text-white/40 uppercase tracking-tighter">ALERTA / VENCIDO</span></div>
                            </div>
                        </div>

                        <AnnualPlanMatrix 
                            plans={plans} 
                            dynamicMonths={dynamicMonths} 
                            complianceData={complianceData} 
                            editMode={editMode}
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
