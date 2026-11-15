import React from 'react';

export function PlanDeleteModal({ planToDelete, onClose, onConfirm }) {
    if (!planToDelete) return null;

    return (
        <div className="fixed inset-0 z-[100] flex items-center justify-center p-4">
            <div
                className="fixed inset-0 bg-[#0a0a0f]/80 backdrop-blur-md animate-fade-in"
                onClick={onClose}
            ></div>

            <div className="relative w-full max-w-lg bg-[#141421] border border-red-500/20 rounded-[2.5rem] shadow-[0_25px_70px_rgba(0,0,0,0.8)] overflow-hidden animate-scale-up">
                <div className="absolute top-0 inset-x-0 h-1 bg-gradient-to-r from-transparent via-red-500 to-transparent opacity-50"></div>

                <div className="p-10 text-center">
                    <div className="mx-auto w-24 h-24 bg-red-500/10 rounded-full flex items-center justify-center border border-red-500/20 mb-8 relative">
                        <div className="absolute inset-0 bg-red-500/20 rounded-full blur-2xl animate-pulse"></div>
                        <svg className="w-10 h-10 text-red-500 relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" strokeWidth="2" /></svg>
                    </div>

                    <h3 className="text-2xl font-black text-white uppercase italic tracking-tighter mb-4">
                        Autorización de <span className="text-red-500">Eliminación</span>
                    </h3>

                    <p className="text-xs font-medium text-white/40 uppercase tracking-widest leading-relaxed mb-10 max-w-sm mx-auto italic">
                        Esta acción purgará los registros de planificación de forma <span className="text-white">permanente</span>. ¿Desea proceder con la desestructuración?
                    </p>

                    <div className="flex flex-col sm:flex-row gap-4">
                        <button
                            onClick={onClose}
                            className="flex-1 h-14 bg-white/5 border border-white/10 rounded-2xl text-[10px] font-black text-white uppercase tracking-widest italic hover:bg-white/10 transition-all"
                        >
                            Abortar Misión
                        </button>
                        <button
                            onClick={onConfirm}
                            className="flex-1 h-14 bg-red-600 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest italic hover:bg-red-500 active:scale-95 transition-all shadow-[0_10px_30px_rgba(220,38,38,0.3)]"
                        >
                            Confirmar Purga
                        </button>
                    </div>
                </div>

                <div className="bg-black/40 px-10 py-4 border-t border-white/5">
                    <span className="text-[8px] font-black text-white/10 uppercase tracking-[0.4em] italic leading-none">
                        Tactical Unit Data Destroyer v4.0 • Secret Matrix
                    </span>
                </div>
            </div>
        </div>
    );
}

export function PlanEditModal({
    planToEdit,
    editForm,
    checklistOptions,
    selectedEditChecklist,
    editHasRealAssets,
    onClose,
    onSubmit,
    onCategoryChange,
    onChecklistChange,
}) {
    if (!planToEdit) return null;

    return (
        <div className="fixed inset-0 z-[100] flex items-center justify-center p-4">
            <div className="fixed inset-0 bg-[#0a0a0f]/80 backdrop-blur-md animate-fade-in" onClick={onClose}></div>
            <div className="relative w-full max-w-2xl bg-[#141421] border border-ep-primary/20 rounded-[2.5rem] shadow-[0_25px_70px_rgba(0,0,0,0.8)] overflow-hidden animate-scale-up">
                <div className="p-8">
                    <h3 className="text-xl font-black text-white uppercase italic tracking-tighter mb-6 flex items-center gap-3">
                        <div className="w-10 h-10 bg-ep-primary rounded-xl flex items-center justify-center text-white">
                            <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" strokeWidth="2" /></svg>
                        </div>
                        Editar Actividad
                    </h3>
                    <form onSubmit={onSubmit} className="space-y-6 relative z-10">
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div className="space-y-2">
                                <label className="text-[10px] font-black text-indigo-100/30 uppercase tracking-widest italic ml-1 block">Pilar Operativo</label>
                                <select
                                    value={editForm.data.category}
                                    onChange={e => onCategoryChange(e.target.value)}
                                    className="w-full bg-[#16162a] border-white/10 rounded-2xl px-5 py-4 text-xs font-black text-white focus:ring-ep-primary/50 transition-all italic [color-scheme:dark]"
                                >
                                    <option value="Capacitación" className="bg-[#16162a] text-white">CAPACITACIÓN</option>
                                    <option value="Inspección" className="bg-[#16162a] text-white">INSPECCIÓN</option>
                                    <option value="Informe" className="bg-[#16162a] text-white">INFORME TÉCNICO</option>
                                </select>
                            </div>
                            <div className="space-y-2">
                                <label className="text-[10px] font-black text-indigo-100/30 uppercase tracking-widest italic ml-1 block">Referencia</label>
                                <select
                                    value={editForm.data.checklist_id}
                                    onChange={e => onChecklistChange(e.target.value)}
                                    disabled={!['Inspección', 'Capacitación'].includes(editForm.data.category)}
                                    className="w-full bg-[#16162a] border-white/10 rounded-2xl px-5 py-4 text-xs font-black text-white italic disabled:opacity-20 transition-all [color-scheme:dark]"
                                >
                                    <option value="" className="bg-[#16162a] text-white">MANUAL / NINGUNO</option>
                                    {checklistOptions.map(c => (
                                        <option key={c.id} value={c.id} className="bg-[#16162a] text-white">{c.title}</option>
                                    ))}
                                </select>
                                {editForm.data.category === 'Inspección' && editHasRealAssets && (
                                    <span className="text-[9px] font-black text-emerald-400 uppercase tracking-widest italic flex items-center gap-1 mt-1 animate-pulse">
                                        🛡️ Checklist con activos incluidos
                                    </span>
                                )}
                                {editForm.data.category === 'Inspección' && selectedEditChecklist && !editHasRealAssets && editForm.data.checklist_id && (
                                    <span className="text-[9px] font-black text-indigo-100/40 uppercase tracking-widest italic flex items-center gap-1 mt-1">
                                        📋 Checklist sin activos vinculados
                                    </span>
                                )}
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
                                    className="w-full bg-[#16162a] border-white/10 rounded-2xl px-5 py-4 text-xs font-black text-white italic focus:ring-ep-primary/50 transition-all [color-scheme:dark]"
                                >
                                    <option value="none" className="bg-[#16162a] text-white">SIN AUTO-PLAN</option>
                                    <option value="mensual" className="bg-[#16162a] text-white">MENSUAL</option>
                                    <option value="bimestral" className="bg-[#16162a] text-white">BIMESTRAL</option>
                                    <option value="trimestral" className="bg-[#16162a] text-white">TRIMESTRAL</option>
                                </select>
                            </div>

                            {editForm.data.category === 'Capacitación' && (
                                <div className="grid grid-cols-1 md:grid-cols-3 gap-6 md:col-span-2 p-6 bg-white/5 rounded-3xl border border-white/5 animate-fade-in w-full">
                                    <div className="space-y-2">
                                        <label className="text-[10px] font-black text-indigo-100/30 uppercase tracking-widest italic ml-1 block">Duración Planificada</label>
                                        <select
                                            value={editForm.data.duration_minutes}
                                            onChange={e => editForm.setData('duration_minutes', parseInt(e.target.value))}
                                            className="w-full bg-[#16162a] border-white/10 rounded-2xl px-5 py-4 text-xs font-black text-white italic [color-scheme:dark]"
                                        >
                                            <option value={5} className="bg-[#16162a] text-white">5 MINUTOS</option>
                                            <option value={10} className="bg-[#16162a] text-white">10 MINUTOS</option>
                                            <option value={15} className="bg-[#16162a] text-white">15 MINUTOS</option>
                                            <option value={30} className="bg-[#16162a] text-white">30 MINUTOS</option>
                                            <option value={45} className="bg-[#16162a] text-white">45 MINUTOS</option>
                                            <option value={60} className="bg-[#16162a] text-white">1 HORA</option>
                                            <option value={90} className="bg-[#16162a] text-white">1:30 HORAS</option>
                                            <option value={120} className="bg-[#16162a] text-white">2 HORAS</option>
                                            <option value={180} className="bg-[#16162a] text-white">3 HORAS</option>
                                        </select>
                                    </div>
                                    <div className="space-y-2">
                                        <label className="text-[10px] font-black text-indigo-100/30 uppercase tracking-widest italic ml-1 block">Modalidad</label>
                                        <select
                                            value={editForm.data.modality}
                                            onChange={e => editForm.setData('modality', e.target.value)}
                                            className="w-full bg-[#16162a] border-white/10 rounded-2xl px-5 py-4 text-xs font-black text-white italic [color-scheme:dark]"
                                        >
                                            <option value="presencial" className="bg-[#16162a] text-white">PRESENCIAL</option>
                                            <option value="virtual" className="bg-[#16162a] text-white">VIRTUAL</option>
                                            <option value="hibrido" className="bg-[#16162a] text-white">HÍBRIDO</option>
                                        </select>
                                    </div>
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
                            <button type="button" onClick={onClose} className="px-6 py-3 bg-white/5 text-white/50 hover:text-white rounded-xl text-[10px] font-black uppercase tracking-widest italic transition-all">Cancelar</button>
                            <button type="submit" disabled={editForm.processing} className="px-8 py-3 bg-ep-primary text-white rounded-xl text-[10px] font-black uppercase tracking-widest italic hover:brightness-110 shadow-[0_10px_30px_rgba(246,146,57,0.3)] transition-all">
                                {editForm.processing ? 'Guardando...' : 'Guardar Cambios'}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    );
}
