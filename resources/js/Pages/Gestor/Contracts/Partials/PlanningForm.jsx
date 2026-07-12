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
        duration: duration,
        duration_minutes: 15,
        modality: 'presencial',
        multi_trainer: false
    });

    const submit = (e) => {
        e.preventDefault();
        post(route('annual-contract.store'), {
            onSuccess: () => reset('activity_name', 'activity_code', 'checklist_id', 'frequency', 'duration_minutes', 'modality', 'multi_trainer'),
            preserveScroll: true
        });
    };

    const handleCategoryChange = (val) => {
        setData(d => ({
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
        setData(d => ({
            ...d,
            checklist_id: val,
            activity_name: checklist ? checklist.title : d.activity_name,
            activity_code: checklist ? (checklist.registry_code || '') : d.activity_code
        }));
    };

    if (!sedeId) return null;

    const selectedChecklist = checklistOptions.find(c => c.id == data.checklist_id);
    const hasAssetLink = selectedChecklist && selectedChecklist.has_asset_link > 0;

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
                        <select id="field_5" name="field_5" 
                            value={data.category}
                            onChange={e => handleCategoryChange(e.target.value)}
                            className="w-full bg-[#16162a] border-white/10 rounded-2xl px-5 py-4 text-xs font-black text-white focus:ring-ep-primary/50 transition-all italic [color-scheme:dark]"
                        >
                            <option value="Capacitación" className="bg-[#16162a] text-white">CAPACITACIÓN</option>
                            <option value="Inspección" className="bg-[#16162a] text-white">INSPECCIÓN</option>
                            <option value="Informe" className="bg-[#16162a] text-white">INFORME TÉCNICO</option>
                        </select>
                    </div>

                    {/* Vínculo (Solo si es Inspección) */}
                    <div className="space-y-2">
                        <label className="text-[10px] font-black text-indigo-100/30 uppercase tracking-widest italic ml-1 block">Referencia</label>
                        <select id="field_4" name="field_4" 
                            value={data.checklist_id}
                            onChange={e => handleChecklistChange(e.target.value)}
                            disabled={!['Inspección', 'Capacitación'].includes(data.category)}
                            className="w-full bg-[#16162a] border-white/10 rounded-2xl px-5 py-4 text-xs font-black text-white italic disabled:opacity-20 transition-all [color-scheme:dark]"
                        >
                            <option value="" className="bg-[#16162a] text-white">MANUAL / NINGUNO</option>
                            {checklistOptions.map(c => (
                                <option key={c.id} value={c.id} className="bg-[#16162a] text-white">{c.title}</option>
                            ))}
                        </select>
                        {data.category === 'Inspección' && hasAssetLink && (
                            <span className="text-[9px] font-black text-emerald-400 uppercase tracking-widest italic flex items-center gap-1 mt-1 animate-pulse">
                                🛡️ Checklist con activos incluidos
                            </span>
                        )}
                        {data.category === 'Inspección' && selectedChecklist && !hasAssetLink && data.checklist_id && (
                            <span className="text-[9px] font-black text-indigo-100/40 uppercase tracking-widest italic flex items-center gap-1 mt-1">
                                📋 Checklist sin activos vinculados
                            </span>
                        )}
                    </div>

                    {/* Actividad */}
                    <div className="space-y-2 lg:col-span-2">
                        <label className="text-[10px] font-black text-indigo-100/30 uppercase tracking-widest italic ml-1 block">Descripción de la Actividad</label>
                        <input id="field_3" name="field_3" 
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
                        <input id="field_2" name="field_2" 
                            type="text"
                            value={data.activity_code}
                            onChange={e => setData('activity_code', e.target.value)}
                            placeholder="EP-HSE-00"
                            className="w-full bg-black/40 border-white/10 rounded-2xl px-6 py-4 text-xs font-black text-white italic focus:ring-ep-primary transition-all uppercase"
                        />
                    </div>

                    <div className="space-y-2">
                        <label className="text-[10px] font-black text-indigo-100/30 uppercase tracking-widest italic ml-1 block">Frecuencia Automática</label>
                        <select id="field_1" name="field_1" 
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

                    {/* Opciones de Capacitación Dinámicas */}
                    {data.category === 'Capacitación' && (
                        <div className="grid grid-cols-1 md:grid-cols-3 gap-6 lg:col-span-6 p-6 bg-white/5 rounded-3xl border border-white/5 animate-fade-in w-full mt-4">
                            {/* Duración */}
                            <div className="space-y-2">
                                <label className="text-[10px] font-black text-indigo-100/30 uppercase tracking-widest italic ml-1 block">Duración Planificada</label>
                                <select
                                    value={data.duration_minutes}
                                    onChange={e => setData('duration_minutes', parseInt(e.target.value))}
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
                                    value={data.modality}
                                    onChange={e => setData('modality', e.target.value)}
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
                                    onClick={() => setData('multi_trainer', !data.multi_trainer)}
                                    className={`relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out ${
                                        data.multi_trainer ? 'bg-indigo-600 shadow-[0_0_15px_rgba(99,102,241,0.3)]' : 'bg-white/10'
                                    }`}
                                >
                                    <span className={`pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow transition duration-200 ease-in-out ${
                                        data.multi_trainer ? 'translate-x-5' : 'translate-x-0'
                                    }`} />
                                </button>
                            </div>
                        </div>
                    )}
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
