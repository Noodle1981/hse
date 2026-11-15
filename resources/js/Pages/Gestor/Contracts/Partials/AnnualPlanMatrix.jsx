import React, { useState } from 'react';
import { router, useForm } from '@inertiajs/react';
import { PlanDeleteModal, PlanEditModal } from './PlanActivityModals';
import PlanExecutionHistoryModal from './PlanExecutionHistoryModal';

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

    const selectedEditChecklist = checklistOptions.find(c => c.id == editForm.data.checklist_id);
    const editHasRealAssets = selectedEditChecklist && selectedEditChecklist.has_real_assets === true;

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
                            <th className="sticky left-0 z-20 bg-[#141421] px-4 md:px-8 py-8 text-left w-[140px] sm:w-[220px] md:w-[300px] shadow-[10px_0_20px_rgba(0,0,0,0.4)]">
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
                                <td className="sticky left-0 z-10 bg-[#141421] px-4 md:px-8 py-4 md:py-6 border-r border-white/5 shadow-[10px_0_20px_rgba(0,0,0,0.4)]">
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
                                                    <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" strokeWidth="2" /></svg>
                                                </button>
                                                <button onClick={() => setPlanToDelete(plan.id)} className="text-red-500 hover:scale-110 hover:text-red-400 transition-all">
                                                    <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" strokeWidth="2" /></svg>
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
                                                            <path d="M5 13l4 4L19 7" strokeLinecap="round" strokeLinejoin="round" />
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
                                                        <svg className="w-4 h-4 text-white/10 group-hover/status:text-ep-primary transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4" strokeWidth="3" /></svg>
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

                                                {status === 'completed' && !editMode && (
                                                    <div className="absolute bottom-full mb-3 left-1/2 -translate-x-1/2 whitespace-nowrap bg-[#141421]/95 backdrop-blur-md border border-emerald-500/30 px-3 py-1.5 rounded-xl shadow-[0_0_15px_rgba(16,185,129,0.2)] opacity-0 invisible group-hover/cell:opacity-100 group-hover/cell:visible transition-all duration-300 z-50 pointer-events-none translate-y-1 group-hover/cell:translate-y-0 text-center">
                                                        <span className="text-[10px] font-black text-emerald-400 uppercase tracking-wider italic flex items-center gap-1.5">
                                                            <span className="w-1.5 h-1.5 bg-emerald-400 rounded-full animate-ping"></span>
                                                            Haga click para auditar {data.count} {data.count === 1 ? 'ejecución' : 'ejecuciones'}
                                                        </span>
                                                    </div>
                                                )}

                                                {editingCell === cellKey && (
                                                    <div className="absolute bottom-full mb-2 left-1/2 -translate-x-1/2 w-48 bg-ep-dark border border-ep-primary/30 p-4 rounded-2xl shadow-2xl z-[100] animate-fade-in">
                                                        <label className="block text-[8px] font-black text-white/40 uppercase italic mb-2 tracking-widest">Programar Fecha</label>
                                                        <input
                                                            id="editingDate"
                                                            name="editingDate"
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

            <PlanDeleteModal
                planToDelete={planToDelete}
                onClose={() => setPlanToDelete(null)}
                onConfirm={confirmDelete}
            />

            <PlanEditModal
                planToEdit={planToEdit}
                editForm={editForm}
                checklistOptions={checklistOptions}
                selectedEditChecklist={selectedEditChecklist}
                editHasRealAssets={editHasRealAssets}
                onClose={closeEditModal}
                onSubmit={submitEdit}
                onCategoryChange={handleCategoryChange}
                onChecklistChange={handleChecklistChange}
            />

            <PlanExecutionHistoryModal
                selectedCellHistory={selectedCellHistory}
                onClose={() => setSelectedCellHistory(null)}
            />

            <style dangerouslySetInnerHTML={{ __html: `
                .custom-scrollbar::-webkit-scrollbar { height: 8px; width: 8px; }
                .custom-scrollbar::-webkit-scrollbar-track { background: rgba(255, 255, 255, 0.02); border-radius: 4px; }
                .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(246, 146, 57, 0.3); border-radius: 4px; border: 2px solid transparent; background-clip: padding-box; }
                .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(246, 146, 57, 0.6); border: 2px solid transparent; background-clip: padding-box; }
                .animate-fade-in { animation: fade-in 0.3s ease-out forwards; }
                .animate-scale-up { animation: scale-up 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
                @keyframes fade-in { from { opacity: 0; } to { opacity: 1; } }
                @keyframes scale-up { from { opacity: 0; transform: scale(0.9) translateY(20px); } to { opacity: 1; transform: scale(1) translateY(0); } }
            `}} />
        </>
    );
}
