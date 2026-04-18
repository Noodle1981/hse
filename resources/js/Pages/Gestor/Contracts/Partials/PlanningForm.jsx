import React from 'react';
import { useForm } from '@inertiajs/react';

export default function PlanningForm({ sedeId, contractStart, duration, checklistOptions }) {
    const { data, setData, post, processing, reset, errors } = useForm({
        sede_id: sedeId,
        category: 'Capacitación',
        activity_name: '',
        activity_code: '',
        checklist_id: '',
        frequency: 'none',
        contract_start: contractStart,
        duration: duration
    });

    const submit = (e) => {
        e.preventDefault();
        post(route('annual-contract.store'), {
            onSuccess: () => reset('activity_name', 'activity_code', 'checklist_id', 'frequency'),
            preserveScroll: true
        });
    };

    const handleCategoryChange = (val) => {
        setData(d => ({
            ...d,
            category: val,
            activity_name: val === 'Informe' ? 'Informe HSE' : '',
            activity_code: val === 'Informe' ? 'EP-INF-01' : '',
            checklist_id: ''
        }));
    };

    const handleChecklistChange = (val) => {
        const checklist = checklistOptions.find(c => c.id == val);
        setData(d => ({
            ...d,
            checklist_id: val,
            activity_name: checklist ? checklist.title : d.activity_name,
            activity_code: checklist ? (checklist.registry_code || '') : d.activity_code
        }));
    };

    if (!sedeId) return null;

    return (
        <div className="bg-ep-primary/5 p-8 rounded-[2.5rem] border border-ep-primary/20 animate-fade-in relative overflow-hidden group">
            <div className="absolute top-0 right-0 p-8 opacity-5">
                <svg className="w-24 h-24 text-ep-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" strokeWidth="2"/></svg>
            </div>

            <form onSubmit={submit} className="relative z-10 space-y-6">
                <div className="flex items-center gap-4 mb-8">
                    <div className="w-10 h-10 bg-ep-primary rounded-xl flex items-center justify-center text-white shadow-lg">
                        <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4" strokeWidth="3"/></svg>
                    </div>
                    <h5 className="text-xl font-black text-white uppercase italic tracking-tighter">Planificar Nueva Actividad</h5>
                </div>

                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 xl:grid-cols-6 gap-6">
                    {/* Pilar */}
                    <div className="space-y-2">
                        <label className="text-[10px] font-black text-indigo-100/30 uppercase tracking-widest italic ml-1 block">Pilar Operativo</label>
                        <select 
                            value={data.category}
                            onChange={e => handleCategoryChange(e.target.value)}
                            className="w-full bg-black/40 border-white/10 rounded-2xl px-5 py-4 text-xs font-black text-white focus:ring-ep-primary/50 transition-all italic"
                        >
                            <option value="Capacitación">CAPACITACIÓN</option>
                            <option value="Inspección">INSPECCIÓN</option>
                            <option value="Informe">INFORME TÉCNICO</option>
                        </select>
                    </div>

                    {/* Vínculo (Solo si es Inspección) */}
                    <div className="space-y-2">
                        <label className="text-[10px] font-black text-indigo-100/30 uppercase tracking-widest italic ml-1 block">Referencia Matriz</label>
                        <select 
                            value={data.checklist_id}
                            onChange={e => handleChecklistChange(e.target.value)}
                            disabled={data.category !== 'Inspección'}
                            className="w-full bg-black/40 border-white/10 rounded-2xl px-5 py-4 text-xs font-black text-white italic disabled:opacity-20 transition-all"
                        >
                            <option value="">MANUAL / NINGUNO</option>
                            {checklistOptions.map(c => (
                                <option key={c.id} value={c.id}>{c.title}</option>
                            ))}
                        </select>
                    </div>

                    {/* Actividad */}
                    <div className="space-y-2 lg:col-span-2">
                        <label className="text-[10px] font-black text-indigo-100/30 uppercase tracking-widest italic ml-1 block">Descripción de la Actividad</label>
                        <input 
                            type="text"
                            value={data.activity_name}
                            onChange={e => setData('activity_name', e.target.value)}
                            placeholder="Ej: Registro de IPER"
                            className="w-full bg-black/40 border-white/10 rounded-2xl px-6 py-4 text-xs font-black text-white italic placeholder-white/5 focus:ring-ep-primary transition-all"
                        />
                         {errors.activity_name && <span className="text-red-500 text-[8px] font-black uppercase italic tracking-widest">{errors.activity_name}</span>}
                    </div>

                    {/* Código y Frecuencia */}
                    <div className="space-y-2">
                        <label className="text-[10px] font-black text-indigo-100/30 uppercase tracking-widest italic ml-1 block">Código Registro</label>
                        <input 
                            type="text"
                            value={data.activity_code}
                            onChange={e => setData('activity_code', e.target.value)}
                            placeholder="EP-HSE-00"
                            className="w-full bg-black/40 border-white/10 rounded-2xl px-6 py-4 text-xs font-black text-white italic focus:ring-ep-primary transition-all uppercase"
                        />
                    </div>

                    <div className="space-y-2">
                        <label className="text-[10px] font-black text-indigo-100/30 uppercase tracking-widest italic ml-1 block">Frecuencia Automática</label>
                        <select 
                            value={data.frequency}
                            onChange={e => setData('frequency', e.target.value)}
                            className="w-full bg-black/40 border-white/10 rounded-2xl px-5 py-4 text-xs font-black text-white italic focus:ring-ep-primary transition-all"
                        >
                            <option value="none">SIN AUTO-PLAN</option>
                            <option value="mensual">MENSUAL</option>
                            <option value="bimestral">BIMESTRAL</option>
                            <option value="trimestral">TRIMESTRAL</option>
                        </select>
                    </div>
                </div>

                <div className="flex justify-end pt-4">
                    <button 
                        type="submit" 
                        disabled={processing}
                        className="h-16 px-12 bg-white text-black text-xs font-black uppercase tracking-[0.2em] italic rounded-2xl hover:bg-ep-primary hover:text-white transition-all shadow-xl flex items-center gap-3 disabled:opacity-50"
                    >
                        {processing ? 'ESTABLECIENDO...' : (
                            <>
                                REGISTRAR ACTIVIDAD
                                <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="3"><path d="M12 4v16m8-8H4"/></svg>
                            </>
                        )}
                    </button>
                </div>
            </form>
        </div>
    );
}
