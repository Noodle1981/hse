import React, { useState } from 'react';
import { router, useForm } from '@inertiajs/react';

const parseSafeDate = (dateStr, includeTime = false) => {
    if (!dateStr) return 'N/A';
    const d = new Date(dateStr);
    if (isNaN(d.getTime())) return 'N/A';
    if (includeTime) {
        // If it includes 'T' or a space/time, format with time, otherwise just date
        const hasTime = dateStr.includes('T') || dateStr.includes(' ');
        if (hasTime) {
            return d.toLocaleString('es-ES', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        }
    }
    return d.toLocaleDateString('es-ES', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });
};

const parseSafeDateShort = (dateStr) => {
    if (!dateStr) return '';
    const d = new Date(dateStr);
    if (isNaN(d.getTime())) return '';
    return d.toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit' });
};

export default function AnnualPlanMatrix({ plans, dynamicMonths, complianceData, editMode, checklistOptions = [] }) {
    const [editingCell, setEditingCell] = useState(null);
    const [selectedCellHistory, setSelectedCellHistory] = useState(null);
    const [editingDate, setEditingDate] = useState('');
    const [planToDelete, setPlanToDelete] = useState(null);
    const [planToEdit, setPlanToEdit] = useState(null);

    const editForm = useForm({
        category: '',
        activity_name: '',
        activity_code: '',
        checklist_id: '',
        frequency: 'none',
        duration_minutes: 15,
        modality: 'presencial',
        multi_trainer: false
    });

    const openEditModal = (plan) => {
        setPlanToEdit(plan);
        editForm.setData({
            category: plan.category,
            activity_name: plan.activity_name,
            activity_code: plan.activity_code || '',
            checklist_id: plan.checklist_id || '',
            frequency: plan.frequency || 'none',
            duration_minutes: plan.duration_minutes || 15,
            modality: plan.modality || 'presencial',
            multi_trainer: !!plan.multi_trainer
        });
    };

    const closeEditModal = () => {
        setPlanToEdit(null);
        editForm.reset();
        editForm.clearErrors();
    };

    const submitEdit = (e) => {
        e.preventDefault();
        if (!planToEdit) return;
        editForm.put(route('annual-contract.update', planToEdit.id), {
            preserveScroll: true,
            onSuccess: () => closeEditModal()
        });
    };

    const handleCategoryChange = (val) => {
        editForm.setData(d => ({
            ...d,
            category: val,
            activity_name: val === 'Informe' ? 'Informe HSE' : '',
            activity_code: val === 'Informe' ? 'EP-INF-01' : '',
            checklist_id: '',
            duration_minutes: val === 'Capacitación' ? 15 : null,
            modality: val === 'Capacitación' ? 'presencial' : null,
            multi_trainer: val === 'Capacitación' ? false : null
        }));
    };

    const handleChecklistChange = (val) => {
        const checklist = checklistOptions.find(c => c.id == val);
        editForm.setData(d => ({
            ...d,
            checklist_id: val,
            activity_name: checklist ? checklist.title : d.activity_name,
            activity_code: checklist ? (checklist.registry_code || '') : d.activity_code
        }));
    };

    const toggleMonth = (planId, month) => {
        if (!editMode) return;
        router.post(route('annual-contract.toggle', planId), { month }, { 
            preserveScroll: true,
            preserveState: true 
        });
    };

    const deletePlan = (id) => {
        setPlanToDelete(id);
    };

    const confirmDelete = () => {
        if (!planToDelete) return;
        router.delete(route('annual-contract.destroy', planToDelete), {
            preserveScroll: true,
            onSuccess: () => setPlanToDelete(null)
        });
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
        <>
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
                                    <div className={`w-1 h-6 rounded-full shrink-0 
                                        ${plan.category === 'Inspección' ? 'bg-orange-500' : 
                                          plan.category === 'Informe' ? 'bg-emerald-500' : 'bg-blue-500'}`}>
                                    </div>
                                    <div className="min-w-0">
                                        <div className="text-xs font-black text-white uppercase italic truncate" title={plan.activity_name}>
                                            {plan.activity_name}
                                        </div>
                                        <div className="text-[8px] font-bold text-indigo-100/20 uppercase tracking-widest mt-1">
                                            {plan.activity_code || 'SIN CÓDIGO'} • {plan.category}
                                        </div>
                                    </div>
                                    {editMode && (
                                        <div className="ml-auto opacity-0 group-hover/row:opacity-100 flex items-center gap-3 pr-2">
                                            <button onClick={() => openEditModal(plan)} className="text-indigo-400 hover:scale-110 hover:text-indigo-300 transition-all">
                                                <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" strokeWidth="2"/></svg>
                                            </button>
                                            <button onClick={() => deletePlan(plan.id)} className="text-red-500 hover:scale-110 hover:text-red-400 transition-all">
                                                <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" strokeWidth="2"/></svg>
                                            </button>
                                        </div>
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
                                                onClick={() => {
                                                    if (editMode) {
                                                        toggleMonth(plan.id, monthValue);
                                                    } else if (status === 'completed') {
                                                        setSelectedCellHistory({ plan, month: info.name, data });
                                                    }
                                                }}
                                                className={`w-10 h-10 rounded-2xl border-2 flex items-center justify-center transition-all duration-300 relative group/status
                                                    ${status === 'completed' ? 'bg-emerald-500/10 border-emerald-500/30 text-emerald-400 shadow-[0_0_20px_rgba(16,185,129,0.1)]' : ''}
                                                    ${status === 'overdue' ? 'bg-red-500/20 border-red-500/40 text-red-500 shadow-[0_0_25px_rgba(239,68,68,0.2)]' : ''}
                                                    ${status === 'pending' ? 'bg-indigo-500/10 border-indigo-500/30 text-indigo-400' : ''}
                                                    ${status === 'none' ? 'border-dashed border-white/5 opacity-40 hover:opacity-100 hover:border-ep-primary/50' : ''}
                                                    ${editMode ? 'cursor-pointer hover:scale-110' : (status === 'completed' ? 'cursor-pointer hover:scale-110' : 'cursor-default transition-none')}
                                                `}
                                            >
                                                {status === 'completed' && (
                                                    <svg className="w-5 h-5 drop-shadow-[0_0_10px_rgba(52,211,153,0.5)]" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="3.5">
                                                        <path d="M5 13l4 4L19 7" strokeLinecap="round" strokeLinejoin="round"/>
                                                    </svg>
                                                )}
                                                {status === 'pending' && <div className="w-2 h-2 bg-indigo-400 rounded-full shadow-[0_0_12px_rgba(129,140,248,0.8)]"></div>}
                                                {status === 'overdue' && (
                                                    <div className="flex flex-col items-center animate-pulse">
                                                        <span className="text-[12px] font-black leading-none">!</span>
                                                        <div className="w-1 h-3 bg-red-500 rounded-full mt-0.5 shadow-[0_0_10px_rgba(239,68,68,0.8)]"></div>
                                                    </div>
                                                )}
                                                {status === 'none' && editMode && (
                                                    <svg className="w-4 h-4 text-white/10 group-hover/status:text-ep-primary transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4" strokeWidth="3"/></svg>
                                                )}
                                            </div>

                                            {status !== 'none' && (
                                                <button 
                                                    onClick={() => openDatePicker(plan.id, monthValue, cellDate)}
                                                    className={`text-[8px] font-black uppercase italic px-1.5 py-0.5 rounded transition-all ${cellDate ? 'bg-indigo-500/20 text-indigo-300 border border-indigo-500/30' : (editMode ? 'text-white/20 hover:text-white/60' : 'text-transparent')}`}
                                                >
                                                    {cellDate ? parseSafeDateShort(cellDate) : (editMode ? '...' : '')}
                                                </button>
                                            )}

                                            {/* Tooltip Detalle Táctico (Hover) */}
                                            {status === 'completed' && !editMode && (
                                                <div className="absolute bottom-full mb-3 left-1/2 -translate-x-1/2 whitespace-nowrap bg-[#141421]/95 backdrop-blur-md border border-emerald-500/30 px-3 py-1.5 rounded-xl shadow-[0_0_15px_rgba(16,185,129,0.2)] opacity-0 invisible group-hover/cell:opacity-100 group-hover/cell:visible transition-all duration-300 z-50 pointer-events-none translate-y-1 group-hover/cell:translate-y-0 text-center">
                                                    <span className="text-[10px] font-black text-emerald-400 uppercase tracking-wider italic flex items-center gap-1.5">
                                                        <span className="w-1.5 h-1.5 bg-emerald-400 rounded-full animate-ping"></span>
                                                        Haga click para auditar {data.count} {data.count === 1 ? 'ejecución' : 'ejecuciones'}
                                                    </span>
                                                </div>
                                            )}

                                            {/* Popover DatePicker */}
                                            {editingCell === cellKey && (
                                                <div className="absolute bottom-full mb-2 left-1/2 -translate-x-1/2 w-48 bg-ep-dark border border-ep-primary/30 p-4 rounded-2xl shadow-2xl z-[100] animate-fade-in">
                                                    <label className="block text-[8px] font-black text-white/40 uppercase italic mb-2 tracking-widest">Programar Fecha</label>
                                                    <input id="editingDate" name="editingDate" 
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
        </div>

        {/* MODAL TÁCTICO DE CONFIRMACIÓN DE ELIMINACIÓN */}
        {planToDelete && (
            <div className="fixed inset-0 z-[100] flex items-center justify-center p-4">
                {/* Backdrop */}
                <div 
                    className="fixed inset-0 bg-[#0a0a0f]/80 backdrop-blur-md animate-fade-in"
                    onClick={() => setPlanToDelete(null)}
                ></div>
                
                {/* Card Modal */}
                <div className="relative w-full max-w-lg bg-[#141421] border border-red-500/20 rounded-[2.5rem] shadow-[0_25px_70px_rgba(0,0,0,0.8)] overflow-hidden animate-scale-up">
                    <div className="absolute top-0 inset-x-0 h-1 bg-gradient-to-r from-transparent via-red-500 to-transparent opacity-50"></div>
                    
                    <div className="p-10 text-center">
                        {/* Icono de Alerta */}
                        <div className="mx-auto w-24 h-24 bg-red-500/10 rounded-full flex items-center justify-center border border-red-500/20 mb-8 relative">
                            <div className="absolute inset-0 bg-red-500/20 rounded-full blur-2xl animate-pulse"></div>
                            <svg className="w-10 h-10 text-red-500 relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" strokeWidth="2"/></svg>
                        </div>

                        <h3 className="text-2xl font-black text-white uppercase italic tracking-tighter mb-4">
                            Autorización de <span className="text-red-500">Eliminación</span>
                        </h3>

                        <p className="text-xs font-medium text-white/40 uppercase tracking-widest leading-relaxed mb-10 max-w-sm mx-auto italic">
                            Esta acción purgará los registros de planificación de forma <span className="text-white">permanente</span>. ¿Desea proceder con la desestructuración?
                        </p>

                        <div className="flex flex-col sm:flex-row gap-4">
                            <button 
                                onClick={() => setPlanToDelete(null)}
                                className="flex-1 h-14 bg-white/5 border border-white/10 rounded-2xl text-[10px] font-black text-white uppercase tracking-widest italic hover:bg-white/10 transition-all"
                            >
                                Abortar Misión
                            </button>
                            <button 
                                onClick={confirmDelete}
                                className="flex-1 h-14 bg-red-600 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest italic hover:bg-red-500 active:scale-95 transition-all shadow-[0_10px_30px_rgba(220,38,38,0.3)]"
                            >
                                Confirmar Purga
                            </button>
                        </div>
                    </div>

                    {/* Footer / Info */}
                    <div className="bg-black/40 px-10 py-4 border-t border-white/5">
                        <span className="text-[8px] font-black text-white/10 uppercase tracking-[0.4em] italic leading-none">
                            Tactical Unit Data Destroyer v4.0 • Secret Matrix
                        </span>
                    </div>
                </div>
            </div>
        )}

        {/* MODAL DE EDICIÓN */}
        {planToEdit && (
            <div className="fixed inset-0 z-[100] flex items-center justify-center p-4">
                <div className="fixed inset-0 bg-[#0a0a0f]/80 backdrop-blur-md animate-fade-in" onClick={closeEditModal}></div>
                <div className="relative w-full max-w-2xl bg-[#141421] border border-ep-primary/20 rounded-[2.5rem] shadow-[0_25px_70px_rgba(0,0,0,0.8)] overflow-hidden animate-scale-up">
                    <div className="p-8">
                        <h3 className="text-xl font-black text-white uppercase italic tracking-tighter mb-6 flex items-center gap-3">
                            <div className="w-10 h-10 bg-ep-primary rounded-xl flex items-center justify-center text-white">
                                <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" strokeWidth="2"/></svg>
                            </div>
                            Editar Actividad
                        </h3>
                        <form onSubmit={submitEdit} className="space-y-6 relative z-10">
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div className="space-y-2">
                                    <label className="text-[10px] font-black text-indigo-100/30 uppercase tracking-widest italic ml-1 block">Pilar Operativo</label>
                                    <select 
                                        value={editForm.data.category}
                                        onChange={e => handleCategoryChange(e.target.value)}
                                        className="w-full bg-black/40 border-white/10 rounded-2xl px-5 py-4 text-xs font-black text-white focus:ring-ep-primary/50 transition-all italic"
                                    >
                                        <option value="Capacitación">CAPACITACIÓN</option>
                                        <option value="Inspección">INSPECCIÓN</option>
                                        <option value="Informe">INFORME TÉCNICO</option>
                                    </select>
                                </div>
                                <div className="space-y-2">
                                    <label className="text-[10px] font-black text-indigo-100/30 uppercase tracking-widest italic ml-1 block">Referencia</label>
                                    <select 
                                        value={editForm.data.checklist_id}
                                        onChange={e => handleChecklistChange(e.target.value)}
                                        disabled={!['Inspección', 'Capacitación'].includes(editForm.data.category)}
                                        className="w-full bg-black/40 border-white/10 rounded-2xl px-5 py-4 text-xs font-black text-white italic disabled:opacity-20 transition-all"
                                    >
                                        <option value="">MANUAL / NINGUNO</option>
                                        {checklistOptions.map(c => (
                                            <option key={c.id} value={c.id}>{c.title}</option>
                                        ))}
                                    </select>
                                </div>
                                <div className="space-y-2 md:col-span-2">
                                    <label className="text-[10px] font-black text-indigo-100/30 uppercase tracking-widest italic ml-1 block">Descripción de la Actividad</label>
                                    <input 
                                        type="text"
                                        value={editForm.data.activity_name}
                                        onChange={e => editForm.setData('activity_name', e.target.value)}
                                        className="w-full bg-black/40 border-white/10 rounded-2xl px-6 py-4 text-xs font-black text-white italic placeholder-white/5 focus:ring-ep-primary transition-all"
                                    />
                                    {editForm.errors.activity_name && <span className="text-red-500 text-[8px] font-black uppercase italic tracking-widest">{editForm.errors.activity_name}</span>}
                                </div>
                                <div className="space-y-2 md:col-span-1">
                                    <label className="text-[10px] font-black text-indigo-100/30 uppercase tracking-widest italic ml-1 block">Código Registro</label>
                                    <input 
                                        type="text"
                                        value={editForm.data.activity_code}
                                        onChange={e => editForm.setData('activity_code', e.target.value)}
                                        className="w-full bg-black/40 border-white/10 rounded-2xl px-6 py-4 text-xs font-black text-white italic focus:ring-ep-primary transition-all uppercase"
                                    />
                                </div>
                                <div className="space-y-2 md:col-span-1">
                                    <label className="text-[10px] font-black text-indigo-100/30 uppercase tracking-widest italic ml-1 block">Frecuencia Automática</label>
                                    <select 
                                        value={editForm.data.frequency}
                                        onChange={e => editForm.setData('frequency', e.target.value)}
                                        className="w-full bg-black/40 border-white/10 rounded-2xl px-5 py-4 text-xs font-black text-white italic focus:ring-ep-primary/50 transition-all"
                                    >
                                        <option value="none">SIN AUTO-PLAN</option>
                                        <option value="mensual">MENSUAL</option>
                                        <option value="bimestral">BIMESTRAL</option>
                                        <option value="trimestral">TRIMESTRAL</option>
                                    </select>
                                </div>

                                {/* Opciones de Capacitación Dinámicas en Edición */}
                                {editForm.data.category === 'Capacitación' && (
                                    <div className="grid grid-cols-1 md:grid-cols-3 gap-6 md:col-span-2 p-6 bg-white/5 rounded-3xl border border-white/5 animate-fade-in w-full">
                                        {/* Duración */}
                                        <div className="space-y-2">
                                            <label className="text-[10px] font-black text-indigo-100/30 uppercase tracking-widest italic ml-1 block">Duración Planificada</label>
                                            <select
                                                value={editForm.data.duration_minutes}
                                                onChange={e => editForm.setData('duration_minutes', parseInt(e.target.value))}
                                                className="w-full bg-[#11111d] border-white/10 rounded-2xl px-5 py-4 text-xs font-black text-white italic"
                                            >
                                                <option value={5}>5 MINUTOS</option>
                                                <option value={10}>10 MINUTOS</option>
                                                <option value={15}>15 MINUTOS</option>
                                                <option value={30}>30 MINUTOS</option>
                                                <option value={45}>45 MINUTOS</option>
                                                <option value={60}>1 HORA</option>
                                                <option value={90}>1:30 HORAS</option>
                                                <option value={120}>2 HORAS</option>
                                                <option value={180}>3 HORAS</option>
                                            </select>
                                        </div>
                                        {/* Modalidad */}
                                        <div className="space-y-2">
                                            <label className="text-[10px] font-black text-indigo-100/30 uppercase tracking-widest italic ml-1 block">Modalidad</label>
                                            <select
                                                value={editForm.data.modality}
                                                onChange={e => editForm.setData('modality', e.target.value)}
                                                className="w-full bg-[#11111d] border-white/10 rounded-2xl px-5 py-4 text-xs font-black text-white italic"
                                            >
                                                <option value="presencial">PRESENCIAL</option>
                                                <option value="virtual">VIRTUAL</option>
                                                <option value="hibrido">HÍBRIDO</option>
                                            </select>
                                        </div>
                                        {/* Multi Instructor Switch */}
                                        <div className="flex items-center justify-between p-4 bg-black/20 rounded-2xl border border-white/5">
                                            <div>
                                                <span className="block text-[10px] font-black text-white uppercase italic">Múltiples Instructores</span>
                                                <span className="block text-[8px] font-black text-white/30 uppercase italic mt-0.5">Asignación para co-instructores</span>
                                            </div>
                                            <button
                                                type="button"
                                                onClick={() => editForm.setData('multi_trainer', !editForm.data.multi_trainer)}
                                                className={`relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out ${
                                                    editForm.data.multi_trainer ? 'bg-indigo-600 shadow-[0_0_15px_rgba(99,102,241,0.3)]' : 'bg-white/10'
                                                }`}
                                            >
                                                <span className={`pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow transition duration-200 ease-in-out ${
                                                    editForm.data.multi_trainer ? 'translate-x-5' : 'translate-x-0'
                                                }`} />
                                            </button>
                                        </div>
                                    </div>
                                )}
                            </div>
                            <div className="flex justify-end gap-4 pt-4 border-t border-white/5 mt-8">
                                <button type="button" onClick={closeEditModal} className="px-6 py-3 bg-white/5 text-white/50 hover:text-white rounded-xl text-[10px] font-black uppercase tracking-widest italic transition-all">Cancelar</button>
                                <button type="submit" disabled={editForm.processing} className="px-8 py-3 bg-ep-primary text-white rounded-xl text-[10px] font-black uppercase tracking-widest italic hover:brightness-110 shadow-[0_10px_30px_rgba(246,146,57,0.3)] transition-all">
                                    {editForm.processing ? 'Guardando...' : 'Guardar Cambios'}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        )}

        {/* MODAL HISTORIAL DE EJECUCIONES COMPILADAS */}
        {selectedCellHistory && (
            <div className="fixed inset-0 z-[100] flex items-center justify-center p-4">
                {/* Backdrop */}
                <div 
                    className="fixed inset-0 bg-[#0a0a0f]/80 backdrop-blur-md animate-fade-in"
                    onClick={() => setSelectedCellHistory(null)}
                ></div>
                
                {/* Card Modal */}
                <div className="relative w-full max-w-xl bg-[#141421]/90 backdrop-blur-2xl border border-emerald-500/20 rounded-[2.5rem] shadow-[0_25px_70px_rgba(0,0,0,0.8)] overflow-hidden animate-scale-up">
                    <div className="absolute top-0 inset-x-0 h-1 bg-gradient-to-r from-transparent via-emerald-500 to-transparent opacity-50"></div>
                    
                    <div className="p-8">
                        <div className="flex justify-between items-start mb-6 border-b border-white/5 pb-4">
                            <div>
                                <span className="block text-[10px] font-black text-emerald-400 uppercase tracking-[0.2em] italic leading-none">Matriz de Cumplimiento</span>
                                <h3 className="text-xl font-black text-white uppercase italic tracking-tighter mt-1">
                                    Historial de Ejecución - {selectedCellHistory.month}
                                </h3>
                                <p className="text-[10px] font-bold text-indigo-100/30 uppercase tracking-widest mt-1">
                                    {selectedCellHistory.plan?.activity_name} • {selectedCellHistory.plan?.activity_code || 'SIN CÓDIGO'}
                                </p>
                            </div>
                            <button 
                                onClick={() => setSelectedCellHistory(null)} 
                                className="w-8 h-8 rounded-full bg-white/5 border border-white/10 flex items-center justify-center text-white/50 hover:text-white hover:bg-white/10 transition-all text-xs font-bold"
                            >
                                ✕
                            </button>
                        </div>

                        {/* List of executions */}
                        <div className="max-h-[350px] overflow-y-auto custom-scrollbar pr-2 space-y-4 mb-8">
                            {(!selectedCellHistory.data?.details?.history || selectedCellHistory.data.details.history.length === 0) ? (
                                <div className="py-8 text-center bg-white/5 rounded-2xl border border-white/5">
                                    <p className="text-xs font-black text-indigo-100/25 uppercase italic tracking-widest">Sin registros históricos detallados</p>
                                </div>
                            ) : (
                                selectedCellHistory.data.details.history.map((item, idx) => {
                                    const category = selectedCellHistory.plan?.category;

                                    if (category === 'Inspección') {
                                        return (
                                            <div key={idx} className="p-4 bg-white/5 rounded-2xl border border-white/5 hover:border-white/10 transition-colors">
                                                <div className="flex flex-col gap-3">
                                                    <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-white/5 pb-2">
                                                        <span className="text-[10px] font-black text-indigo-300 uppercase tracking-widest flex items-center gap-1">
                                                            📅 {parseSafeDate(item.date, true)}
                                                        </span>
                                                        <div className="text-xs font-bold text-white flex items-center gap-1">
                                                            👷 {item.technician_name || 'Técnico no especificado'}
                                                        </div>
                                                    </div>
                                                    
                                                    <div className="grid grid-cols-1 sm:grid-cols-3 gap-2 mt-1">
                                                        <div className="flex items-center justify-between px-3 py-2 rounded-xl text-indigo-400 bg-indigo-500/10 border border-indigo-500/20 text-[10px] font-bold">
                                                            <span className="opacity-80">Checklists Hechos:</span>
                                                            <span className="font-black text-xs">{item.total_checklists ?? 0}</span>
                                                        </div>
                                                        <div className="flex items-center justify-between px-3 py-2 rounded-xl text-emerald-400 bg-emerald-500/10 border border-emerald-500/30 text-[10px] font-bold shadow-[0_0_10px_rgba(16,185,129,0.05)]">
                                                            <span className="opacity-80">Exitosos:</span>
                                                            <span className="font-black text-xs">{item.passed ?? 0}</span>
                                                        </div>
                                                        {(item.failed ?? 0) > 0 ? (
                                                            <div className="flex items-center justify-between px-3 py-2 rounded-xl text-red-400 bg-red-500/10 border border-red-500/30 text-[10px] font-bold shadow-[0_0_10px_rgba(239,68,68,0.05)]">
                                                                <span className="opacity-80">Fallidos:</span>
                                                                <span className="font-black text-xs">{item.failed}</span>
                                                            </div>
                                                        ) : (
                                                            <div className="flex items-center justify-between px-3 py-2 rounded-xl text-white/20 bg-white/5 border border-white/5 text-[10px] font-bold">
                                                                <span className="opacity-60">Fallidos:</span>
                                                                <span className="font-black text-xs">{item.failed ?? 0}</span>
                                                            </div>
                                                        )}
                                                    </div>
                                                </div>
                                            </div>
                                        );
                                    }

                                    if (category === 'Capacitación') {
                                        const attendeesCount = item.asistentes ?? item.attendees_count ?? 0;
                                        return (
                                            <div key={idx} className="p-4 bg-white/5 rounded-2xl border border-white/5 hover:border-white/10 transition-colors">
                                                <div className="flex flex-col gap-3">
                                                    <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-white/5 pb-2">
                                                        <span className="text-[10px] font-black text-indigo-300 uppercase tracking-widest flex items-center gap-1">
                                                            📅 {parseSafeDate(item.date, true)}
                                                        </span>
                                                        <div className="text-xs font-bold text-white flex items-center gap-1">
                                                            👷 {item.technician_name || 'Instructor no especificado'}
                                                        </div>
                                                    </div>
                                                    <div className="flex flex-wrap items-center gap-2 mt-1">
                                                        <span className="px-3 py-1.5 rounded-xl text-emerald-400 bg-emerald-500/10 border border-emerald-500/30 text-[10px] font-black uppercase tracking-wider italic flex items-center gap-1.5 shadow-[0_0_10px_rgba(16,185,129,0.05)]">
                                                            👥 Asistentes: {attendeesCount} capacitados
                                                        </span>
                                                        {(item.observaciones || item.observations) && (
                                                            <span className="px-3 py-1.5 rounded-xl text-indigo-300 bg-indigo-500/10 border border-indigo-500/20 text-[10px] font-semibold italic flex items-center gap-1">
                                                                💬 {`"${item.observaciones ?? item.observations}"`}
                                                            </span>
                                                        )}
                                                    </div>
                                                </div>
                                            </div>
                                        );
                                    }

                                    // Default/Informe category
                                    const observations = item.observaciones ?? item.observations ?? 'Sin observaciones registradas';
                                    return (
                                        <div key={idx} className="p-4 bg-white/5 rounded-2xl border border-white/5 hover:border-white/10 transition-colors">
                                            <div className="flex flex-col gap-3">
                                                <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-white/5 pb-2">
                                                    <span className="text-[10px] font-black text-indigo-300 uppercase tracking-widest flex items-center gap-1">
                                                        📅 {parseSafeDate(item.date, true)}
                                                    </span>
                                                    <div className="text-xs font-bold text-white flex items-center gap-1">
                                                        👷 {item.technician_name || 'Autor no especificado'}
                                                    </div>
                                                </div>
                                                <div className="mt-1 p-3 bg-white/5 rounded-xl border border-white/5">
                                                    <span className="block text-[8px] font-black text-white/30 uppercase tracking-wider mb-1">Observaciones / Detalles:</span>
                                                    <p className="text-[11px] font-semibold text-indigo-200 italic leading-relaxed">
                                                        {`"${observations}"`}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    );
                                })
                            )}
                        </div>

                        <div className="flex justify-end pt-4 border-t border-white/5">
                            <button 
                                onClick={() => setSelectedCellHistory(null)}
                                className="w-full sm:w-auto h-12 px-8 bg-white/5 border border-white/10 rounded-2xl text-[10px] font-black text-white uppercase tracking-widest italic hover:bg-white/10 active:scale-95 transition-all"
                            >
                                Cerrar Panel
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        )}
        <style dangerouslySetInnerHTML={{ __html: `
            .custom-scrollbar::-webkit-scrollbar { height: 6px; }
            .custom-scrollbar::-webkit-scrollbar-track { background: rgba(0,0,0,0.1); }
            .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.05); border-radius: 10px; }
            .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.1); }
            .animate-fade-in { animation: fade-in 0.3s ease-out forwards; }
            .animate-scale-up { animation: scale-up 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
            @keyframes fade-in { from { opacity: 0; } to { opacity: 1; } }
            @keyframes scale-up { from { opacity: 0; transform: scale(0.9) translateY(20px); } to { opacity: 1; transform: scale(1) translateY(0); } }
        `}} />
    </>
);
}
