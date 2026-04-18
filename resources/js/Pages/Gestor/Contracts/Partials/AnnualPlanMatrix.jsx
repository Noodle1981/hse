import React, { useState } from 'react';
import { router } from '@inertiajs/react';

export default function AnnualPlanMatrix({ plans, dynamicMonths, complianceData, editMode }) {
    const [editingCell, setEditingCell] = useState(null);
    const [editingDate, setEditingDate] = useState('');

    const toggleMonth = (planId, month) => {
        if (!editMode) return;
        router.post(route('annual-contract.toggle', planId), { month }, { 
            preserveScroll: true,
            preserveState: true 
        });
    };

    const deletePlan = (id) => {
        if (confirm('¿Eliminar actividad del plan?')) {
            router.delete(route('annual-contract.destroy', id), {
                preserveScroll: true
            });
        }
    };

    const openDatePicker = (planId, month, currentDate) => {
        if (!editMode) return;
        setEditingCell(`${planId}_${month}`);
        setEditingDate(currentDate || '');
    };

    const saveDate = (planId, month) => {
        router.post(route('annual-contract.date', planId), { 
            month, 
            date: editingDate 
        }, {
            preserveScroll: true,
            onSuccess: () => setEditingCell(null)
        });
    };

    return (
        <div className="overflow-x-auto relative custom-scrollbar">
            <table className="w-full border-collapse table-fixed min-w-[1200px]">
                <thead>
                    <tr className="bg-black/40 border-b border-white/5">
                        <th className="sticky left-0 z-20 bg-[#141421] px-8 py-8 text-left w-[300px] shadow-[10px_0_20px_rgba(0,0,0,0.4)]">
                            <span className="text-[10px] font-black text-ep-primary uppercase tracking-[0.3em] italic">Actividad / Punto de Control</span>
                        </th>
                        {Object.entries(dynamicMonths).map(([key, info]) => (
                            <th key={key} className="px-2 py-8 text-center border-r border-white/5 last:border-r-0">
                                <div className="flex flex-col">
                                    <span className="text-xs font-black text-white italic uppercase tracking-tighter leading-none">{info.short}</span>
                                    <span className="text-[8px] font-black text-indigo-100/20 uppercase tracking-widest mt-2">{info.year.substring(2)}</span>
                                </div>
                            </th>
                        ))}
                    </tr>
                </thead>
                <tbody className="divide-y divide-white/[0.02]">
                    {plans.map((plan) => (
                        <tr key={plan.id} className="group/row hover:bg-white/[0.02] transition-colors">
                            <td className="sticky left-0 z-10 bg-[#141421] px-8 py-6 border-r border-white/5 shadow-[10px_0_20px_rgba(0,0,0,0.4)]">
                                <div className="flex items-center gap-4">
                                    <div className={`w-1 h-6 rounded-full shrink-0 ${plan.category === 'Inspección' ? 'bg-orange-500' : 'bg-blue-500'}`}></div>
                                    <div className="min-w-0">
                                        <div className="text-xs font-black text-white uppercase italic truncate" title={plan.activity_name}>
                                            {plan.activity_name}
                                        </div>
                                        <div className="text-[8px] font-bold text-indigo-100/20 uppercase tracking-widest mt-1">
                                            {plan.activity_code || 'SIN CÓDIGO'} • {plan.category}
                                        </div>
                                    </div>
                                    {editMode && (
                                        <button onClick={() => deletePlan(plan.id)} className="ml-auto opacity-0 group-hover/row:opacity-100 text-red-500 hover:scale-110 transition-all">
                                            <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" strokeWidth="2"/></svg>
                                        </button>
                                    )}
                                </div>
                            </td>
                            {Object.entries(dynamicMonths).map(([monthValue, info]) => {
                                const data = complianceData[plan.id]?.[monthValue] || { status: 'none', count: 0 };
                                const status = data.status;
                                const cellKey = `${plan.id}_${monthValue}`;
                                const scheduledDates = plan.scheduled_dates || {};
                                const cellDate = scheduledDates[monthValue];

                                return (
                                    <td key={monthValue} className="p-0 border-r border-white/[0.03] relative group/cell">
                                        <div className="flex flex-col items-center justify-center h-full py-4 gap-1">
                                            <div 
                                                onClick={() => toggleMonth(plan.id, monthValue)}
                                                className={`w-8 h-8 rounded-xl border-2 flex items-center justify-center transition-all duration-300
                                                    ${status === 'completed' ? 'bg-emerald-500/10 border-emerald-500/30 text-emerald-400 shadow-[0_0_15px_rgba(16,185,129,0.1)]' : ''}
                                                    ${status === 'overdue' ? 'bg-red-500/20 border-red-500/40 text-red-500 animate-pulse' : ''}
                                                    ${status === 'pending' ? 'bg-yellow-400/10 border-yellow-500/30 text-yellow-500' : ''}
                                                    ${status === 'none' ? 'border-transparent opacity-5' : ''}
                                                    ${editMode ? 'cursor-pointer hover:scale-110 border-dashed' : 'border-transparent'}
                                                `}
                                            >
                                                {status === 'completed' && <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="3"><path d="M5 13l4 4L19 7"/></svg>}
                                                {status === 'pending' && <div className="w-1.5 h-1.5 bg-yellow-400 rounded-full shadow-[0_0_10px_rgba(250,204,21,0.5)]"></div>}
                                                {status === 'overdue' && <span className="text-[10px] font-black">!</span>}
                                            </div>

                                            {status !== 'none' && (
                                                <button 
                                                    onClick={() => openDatePicker(plan.id, monthValue, cellDate)}
                                                    className={`text-[8px] font-black uppercase italic px-1.5 py-0.5 rounded transition-all ${cellDate ? 'bg-indigo-500/20 text-indigo-300 border border-indigo-500/30' : (editMode ? 'text-white/20 hover:text-white/60' : 'text-transparent')}`}
                                                >
                                                    {cellDate ? new Date(cellDate).toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit' }) : (editMode ? '...' : '')}
                                                </button>
                                            )}

                                            {/* Tooltip Detalle (Hover) */}
                                            {status === 'completed' && !editMode && (
                                                <div className="absolute bottom-full mb-4 left-1/2 -translate-x-1/2 w-48 bg-ep-dark/95 backdrop-blur-2xl border border-white/10 p-5 rounded-3xl shadow-2xl opacity-0 invisible group-hover/cell:opacity-100 group-hover/cell:visible transition-all duration-300 z-50 pointer-events-none translate-y-2 group-hover/cell:translate-y-0 text-left">
                                                    <div className="flex justify-between items-center mb-4 border-b border-white/5 pb-2">
                                                        <span className="text-[8px] font-black text-ep-primary uppercase tracking-widest italic">{info.short} {info.year}</span>
                                                        <span className="px-2 py-0.5 bg-emerald-500/20 text-emerald-400 text-[6px] font-black rounded uppercase italic">OK</span>
                                                    </div>
                                                    <div className="space-y-3">
                                                        <div className="flex flex-col">
                                                            <span className="text-[6px] font-black text-indigo-100/20 uppercase tracking-widest mb-1">Ejecutado</span>
                                                            <span className="text-[10px] font-black text-white italic">{data.details?.execution_date}</span>
                                                        </div>
                                                        {data.details?.attendees > 0 && (
                                                            <div className="flex flex-col">
                                                                <span className="text-[6px] font-black text-indigo-100/20 uppercase tracking-widest mb-1">Asistencia</span>
                                                                <span className="text-[10px] font-black text-blue-400 italic">{data.details.attendees} PAX</span>
                                                            </div>
                                                        )}
                                                        {data.details?.findings > 0 && (
                                                            <div className="flex flex-col">
                                                                <span className="text-[6px] font-black text-indigo-100/20 uppercase tracking-widest mb-1">Hallazgos</span>
                                                                <span className="text-[10px] font-black text-red-400 italic">{data.details.findings} DESVÍOS</span>
                                                            </div>
                                                        )}
                                                    </div>
                                                    <div className="absolute top-full left-1/2 -translate-x-1/2 border-8 border-transparent border-t-ep-dark/95"></div>
                                                </div>
                                            )}

                                            {/* Popover DatePicker */}
                                            {editingCell === cellKey && (
                                                <div className="absolute bottom-full mb-2 left-1/2 -translate-x-1/2 w-48 bg-ep-dark border border-ep-primary/30 p-4 rounded-2xl shadow-2xl z-[100] animate-fade-in">
                                                    <label className="block text-[8px] font-black text-white/40 uppercase italic mb-2 tracking-widest">Programar Fecha</label>
                                                    <input 
                                                        type="date" 
                                                        value={editingDate}
                                                        onChange={e => setEditingDate(e.target.value)}
                                                        className="w-full bg-black/40 border-white/10 rounded-xl px-3 py-2 text-xs font-bold text-white focus:ring-ep-primary transition-all [color-scheme:dark]"
                                                    />
                                                    <div className="flex gap-2 mt-4">
                                                        <button onClick={() => saveDate(plan.id, monthValue)} className="flex-1 py-2 bg-ep-primary text-white text-[9px] font-black uppercase italic rounded-lg transition-all hover:brightness-110">OK</button>
                                                        <button onClick={() => setEditingCell(null)} className="px-3 py-2 bg-white/5 text-white/50 text-[9px] font-black rounded-lg">✕</button>
                                                    </div>
                                                </div>
                                            )}
                                        </div>
                                    </td>
                                );
                            })}
                        </tr>
                    ))}
                    {plans.length === 0 && (
                        <tr>
                            <td colSpan={Object.keys(dynamicMonths).length + 1} className="py-24 text-center">
                                <p className="text-xl font-black text-indigo-100/5 uppercase italic tracking-[0.2em]">Matriz Vacía — Use el modo Planificar</p>
                            </td>
                        </tr>
                    )}
                </tbody>
            </table>
            
            <style dangerouslySetInnerHTML={{ __html: `
                .custom-scrollbar::-webkit-scrollbar { height: 6px; }
                .custom-scrollbar::-webkit-scrollbar-track { background: rgba(0,0,0,0.1); }
                .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.05); border-radius: 10px; }
                .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.1); }
            `}} />
        </div>
    );
}
