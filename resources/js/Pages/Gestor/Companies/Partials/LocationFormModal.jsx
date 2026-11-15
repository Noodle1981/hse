import React, { useState, useEffect, useRef } from 'react';

function TimeSelect({ value, onChange, options, placeholder }) {
    const [isOpen, setIsOpen] = useState(false);
    const containerRef = useRef(null);

    useEffect(() => {
        function handleClickOutside(event) {
            if (containerRef.current && !containerRef.current.contains(event.target)) setIsOpen(false);
        }
        document.addEventListener('mousedown', handleClickOutside);
        return () => document.removeEventListener('mousedown', handleClickOutside);
    }, []);

    useEffect(() => {
        if (isOpen && value) {
            const container = containerRef.current?.querySelector('.options-list');
            const activeItem = container?.querySelector('[data-selected="true"]');
            if (container && activeItem) {
                container.scrollTop = activeItem.offsetTop - container.clientHeight / 2 + activeItem.clientHeight / 2;
            }
        }
    }, [isOpen, value]);

    return (
        <div ref={containerRef} className="w-1/2 relative">
            <button
                type="button"
                onClick={() => setIsOpen(!isOpen)}
                className="w-full bg-black/40 border border-white/10 rounded-2xl px-4 py-3 text-sm font-bold text-white focus:ring-2 focus:ring-emerald-500 focus:outline-none transition-all italic text-center cursor-pointer flex items-center justify-between"
            >
                <span className="mx-auto pl-4">{value || placeholder}</span>
                <svg className={`w-4 h-4 text-white/40 transition-transform ${isOpen ? 'rotate-180' : ''}`} fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            {isOpen && (
                <div className="absolute left-0 right-0 mt-2 bg-[#16162a]/95 backdrop-blur-md border border-white/10 rounded-2xl shadow-2xl z-50 overflow-hidden">
                    <div className="options-list max-h-48 overflow-y-auto custom-scrollbar py-2">
                        <button
                            type="button"
                            onClick={() => { onChange(''); setIsOpen(false); }}
                            className={`w-full px-4 py-2 text-center text-xs font-black uppercase italic tracking-wider transition-colors ${!value ? 'bg-[#F69239]/20 text-[#F69239]' : 'text-white/40 hover:bg-white/5 hover:text-white'}`}
                        >
                            {placeholder}
                        </button>
                        {options.map(opt => (
                            <button
                                key={opt}
                                type="button"
                                data-selected={value === opt}
                                onClick={() => { onChange(opt); setIsOpen(false); }}
                                className={`w-full px-4 py-2 text-center text-sm font-bold transition-colors ${value === opt ? 'bg-[#F69239] text-white' : 'text-white hover:bg-[#F69239]/20 hover:text-[#F69239]'}`}
                            >
                                {opt}
                            </button>
                        ))}
                    </div>
                </div>
            )}
        </div>
    );
}

const DAYS = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];

export default function LocationFormModal({
    show,
    editingId,
    form,
    provincias,
    hoursList,
    minutesList,
    parseTime,
    handleTimeChange,
    onClose,
    onSubmit,
}) {
    if (!show) return null;

    return (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-4 animate-fade-in overflow-y-auto">
            <div className="absolute inset-0 bg-black/85 backdrop-blur-md" onClick={onClose}></div>
            <div className="bg-[#16162a] border border-white/10 rounded-[3rem] p-8 max-w-5xl w-full relative z-10 shadow-2xl overflow-hidden">
                <div className="absolute inset-x-0 top-0 h-1 bg-emerald-500/50"></div>
                <h3 className="text-xl font-black text-white italic mb-8 uppercase tracking-widest flex items-center gap-3">
                    <span className="w-2 h-8 bg-emerald-500 rounded-full"></span>
                    {editingId ? 'Parametrización de Sede' : 'Registro de Punto Operativo'}
                </h3>

                <form onSubmit={onSubmit} className="relative z-10 space-y-8 max-h-[70vh] overflow-y-auto pr-4 custom-scrollbar">
                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div className="space-y-2">
                            <label className="text-[10px] font-black text-indigo-100/40 uppercase tracking-[0.2em] italic ml-4 block mb-2">Nombre de Sede</label>
                            <input id="nombre_sede" name="nombre_sede" type="text" value={form.data.nombre_sede} onChange={e => form.setData('nombre_sede', e.target.value)} required className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-emerald-500 transition-all uppercase" />
                        </div>
                        <div className="space-y-2">
                            <label className="text-[10px] font-black text-indigo-100/40 uppercase tracking-[0.2em] italic ml-4 block mb-2">Dirección</label>
                            <input id="direccion" name="direccion" type="text" value={form.data.direccion} onChange={e => form.setData('direccion', e.target.value)} className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-emerald-500 transition-all uppercase" />
                        </div>
                        <div className="space-y-2">
                            <label className="text-[10px] font-black text-indigo-100/40 uppercase tracking-[0.2em] italic ml-4 block mb-2">Localidad</label>
                            <input id="localidad" name="localidad" type="text" value={form.data.localidad} onChange={e => form.setData('localidad', e.target.value)} className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-emerald-500 transition-all uppercase" />
                        </div>
                        <div className="space-y-2">
                            <label className="text-[10px] font-black text-indigo-100/40 uppercase tracking-[0.2em] italic ml-4 block mb-2">Provincia</label>
                            <select id="provincia_id" name="provincia_id" value={form.data.provincia_id} onChange={e => form.setData('provincia_id', e.target.value)} className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-emerald-500 transition-all [color-scheme:dark] italic uppercase">
                                <option value="">SELECCIONE PROVINCIA...</option>
                                {provincias.map(p => <option key={p.id} value={p.id}>{p.nombre}</option>)}
                            </select>
                        </div>
                        <div className="space-y-2">
                            <label className="text-[10px] font-black text-indigo-100/40 uppercase tracking-[0.2em] italic ml-4 block mb-2">Teléfono Sede</label>
                            <input id="telefono" name="telefono" type="text" value={form.data.telefono} onChange={e => form.setData('telefono', e.target.value)} className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-emerald-500 transition-all uppercase" />
                        </div>
                        <div className="space-y-2">
                            <label className="text-[10px] font-black text-indigo-100/40 uppercase tracking-[0.2em] italic ml-4 block mb-2">Email Sede</label>
                            <input id="correo" name="correo" type="email" value={form.data.correo} onChange={e => form.setData('correo', e.target.value)} className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-emerald-500 transition-all" />
                        </div>
                    </div>

                    <div className="pt-6 border-t border-white/5">
                        <div className="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6 mb-6">
                            <div>
                                <h4 className="text-[10px] font-black text-emerald-400 uppercase tracking-[0.4em] italic ml-4">Auditoría de Jornada y GPS</h4>
                                <p className="text-[8px] font-bold text-white/20 uppercase tracking-[0.2em] ml-4 mt-1">Parametrización horaria y tolerancia de presencia</p>
                            </div>
                            <div className="flex items-center gap-4 bg-black/40 px-6 py-3 rounded-2xl border border-white/5">
                                <span className="text-[9px] font-black text-white/40 uppercase tracking-widest italic">Roster Minero 24/7</span>
                                <button
                                    type="button"
                                    onClick={() => {
                                        const newVal = !form.data.es_roster_24_7;
                                        form.setData(d => ({
                                            ...d,
                                            es_roster_24_7: newVal,
                                            dias_operativos: newVal ? [] : d.dias_operativos,
                                            hora_apertura: newVal ? '' : d.hora_apertura,
                                            hora_cierre: newVal ? '' : d.hora_cierre,
                                        }));
                                    }}
                                    className={`px-6 py-2.5 rounded-xl text-[9px] font-black uppercase italic tracking-widest transition-all duration-300 border ${
                                        form.data.es_roster_24_7
                                            ? 'bg-[#F69239]/20 border-[#F69239] text-[#F69239] shadow-lg shadow-[#F69239]/20 scale-105'
                                            : 'bg-white/5 border-white/10 text-white/40 hover:text-white hover:border-white/20'
                                    }`}
                                >
                                    {form.data.es_roster_24_7 ? 'ACTIVO 24/7' : 'DESACTIVADO'}
                                </button>
                            </div>
                        </div>

                        <div className="bg-white/5 p-6 rounded-[2.5rem] border border-white/5 space-y-6">
                            {form.data.es_roster_24_7 ? (
                                <div className="flex flex-col items-center justify-center py-6 border border-[#F69239]/10 rounded-2xl bg-[#F69239]/5 shadow-inner">
                                    <span className="text-xl font-black text-[#F69239] tracking-[0.4em] uppercase italic animate-pulse">★ OPERACIÓN CONTINUA 24/7 ★</span>
                                    <p className="text-[9px] font-bold text-white/30 uppercase tracking-widest italic mt-2">
                                        MODALIDAD ROSTER MINERO HABILITADA. AUDITORÍA DE JORNADA Y CONTROL DOMINICAL OMITIDOS.
                                    </p>
                                </div>
                            ) : (
                                <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                    <div className="space-y-3">
                                        <label className="text-[10px] font-black text-indigo-100/40 uppercase tracking-[0.2em] italic ml-2 block mb-2">Días de Operación Ordinaria</label>
                                        <div className="flex flex-wrap gap-2">
                                            {DAYS.map(day => {
                                                const isSelected = form.data.dias_operativos.includes(day);
                                                return (
                                                    <button
                                                        key={day}
                                                        type="button"
                                                        onClick={() => {
                                                            const currentDays = [...form.data.dias_operativos];
                                                            form.setData('dias_operativos', isSelected ? currentDays.filter(d => d !== day) : [...currentDays, day]);
                                                        }}
                                                        className={`h-11 px-4 rounded-xl text-[10px] font-black uppercase italic tracking-wider transition-all border duration-300 ${
                                                            isSelected ? 'bg-[#F69239] border-[#F69239] text-white shadow-md shadow-[#F69239]/20' : 'bg-black/40 border-white/10 text-white/40 hover:text-white'
                                                        }`}
                                                    >
                                                        {day.substring(0, 3)}
                                                    </button>
                                                );
                                            })}
                                        </div>
                                    </div>

                                    <div className="grid grid-cols-2 gap-4">
                                        <div className="space-y-2">
                                            <label className="text-[10px] font-black text-indigo-100/40 uppercase tracking-[0.2em] italic ml-2 block mb-2">Apertura</label>
                                            <div className="flex items-center gap-2">
                                                <TimeSelect value={parseTime(form.data.hora_apertura).hour} onChange={val => handleTimeChange('hora_apertura', 'hour', val)} options={hoursList} placeholder="HH" />
                                                <span className="text-white/40 font-bold">:</span>
                                                <TimeSelect value={parseTime(form.data.hora_apertura).minute} onChange={val => handleTimeChange('hora_apertura', 'minute', val)} options={minutesList} placeholder="MM" />
                                            </div>
                                        </div>
                                        <div className="space-y-2">
                                            <label className="text-[10px] font-black text-indigo-100/40 uppercase tracking-[0.2em] italic ml-2 block mb-2">Cierre</label>
                                            <div className="flex items-center gap-2">
                                                <TimeSelect value={parseTime(form.data.hora_cierre).hour} onChange={val => handleTimeChange('hora_cierre', 'hour', val)} options={hoursList} placeholder="HH" />
                                                <span className="text-white/40 font-bold">:</span>
                                                <TimeSelect value={parseTime(form.data.hora_cierre).minute} onChange={val => handleTimeChange('hora_cierre', 'minute', val)} options={minutesList} placeholder="MM" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            )}

                            <div className="grid grid-cols-1 md:grid-cols-3 gap-6 pt-4 border-t border-white/5">
                                <div className="space-y-2">
                                    <label className="text-[10px] font-black text-indigo-100/40 uppercase tracking-[0.2em] italic ml-2 block mb-2">Latitud GPS</label>
                                    <input type="number" step="any" value={form.data.latitud} onChange={e => form.setData('latitud', e.target.value)} placeholder="Ej: -31.372506" className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-3 text-sm font-bold text-white focus:ring-2 focus:ring-emerald-500 transition-all [color-scheme:dark]" />
                                    {form.errors.latitud && <span className="text-red-500 text-[9px] font-black italic ml-2 mt-1 block">{form.errors.latitud}</span>}
                                </div>
                                <div className="space-y-2">
                                    <label className="text-[10px] font-black text-indigo-100/40 uppercase tracking-[0.2em] italic ml-2 block mb-2">Longitud GPS</label>
                                    <input type="number" step="any" value={form.data.longitud} onChange={e => form.setData('longitud', e.target.value)} placeholder="Ej: -64.182416" className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-3 text-sm font-bold text-white focus:ring-2 focus:ring-emerald-500 transition-all [color-scheme:dark]" />
                                    {form.errors.longitud && <span className="text-red-500 text-[9px] font-black italic ml-2 mt-1 block">{form.errors.longitud}</span>}
                                </div>
                                <div className="space-y-2">
                                    <label className="text-[10px] font-black text-indigo-100/40 uppercase tracking-[0.2em] italic ml-2 block mb-2">Radio Tolerancia (Metros)</label>
                                    <input type="number" min="0" value={form.data.radio_tolerancia_metros} onChange={e => form.setData('radio_tolerancia_metros', parseInt(e.target.value) || 0)} className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-3 text-sm font-bold text-white focus:ring-2 focus:ring-emerald-500 transition-all uppercase" />
                                    {form.errors.radio_tolerancia_metros && <span className="text-red-500 text-[9px] font-black italic ml-2 mt-1 block">{form.errors.radio_tolerancia_metros}</span>}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div className="pt-6 border-t border-white/5">
                        <h4 className="text-[10px] font-black text-emerald-400 uppercase tracking-[0.4em] mb-6 italic ml-4">Matriz de Responsables</h4>
                        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 bg-white/5 p-6 rounded-[2.5rem] border border-white/5">
                            {[1, 2, 3].map(n => (
                                <div key={n} className="space-y-4">
                                    <label className="text-[8px] font-black text-white/20 uppercase tracking-[0.3em] ml-2 italic">
                                        {n === 1 ? 'Primer' : n === 2 ? 'Segundo' : 'Tercer'} Responsable
                                    </label>
                                    <input type="text" value={form.data[`responsable_${n}_nombre`]} onChange={e => form.setData(`responsable_${n}_nombre`, e.target.value)} placeholder="NOMBRE COMPLETO" className="w-full bg-black/40 border border-white/5 rounded-xl px-4 py-3 text-xs text-white" />
                                    <input type="text" value={form.data[`responsable_${n}_cargo`]} onChange={e => form.setData(`responsable_${n}_cargo`, e.target.value)} placeholder="CARGO / FUNCIÓN" className="w-full bg-black/40 border border-white/5 rounded-xl px-4 py-3 text-xs text-white" />
                                </div>
                            ))}
                        </div>
                    </div>

                    <div className="flex justify-end gap-4 pt-4">
                        <button type="button" onClick={onClose} className="px-8 py-4 bg-white/5 text-white/40 hover:text-white rounded-2xl text-[10px] font-black uppercase tracking-widest italic transition-all">Abortar</button>
                        <button type="submit" disabled={form.processing} className="px-10 py-4 bg-emerald-600 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest italic transition-all shadow-lg hover:brightness-110">
                            {form.processing ? 'Procesando...' : editingId ? 'Confirmar Sede' : 'Registrar Sede'}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    );
}
