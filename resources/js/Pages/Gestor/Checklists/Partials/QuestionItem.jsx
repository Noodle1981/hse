import React, { useState } from 'react';

export default function QuestionItem({ index, question, update, remove, move }) {
    const [optionInput, setOptionInput] = useState('');

    const addOption = () => {
        if (optionInput.trim()) {
            update(index, 'options', [...question.options, optionInput.trim()]);
            setOptionInput('');
        }
    };

    const removeOption = (idx) => {
        const newOpts = [...question.options];
        newOpts.splice(idx, 1);
        update(index, 'options', newOpts);

        const val = question.options[idx];
        const newFailures = question.failure_values.filter(v => v !== val);
        update(index, 'failure_values', newFailures);
    };

    const toggleFailure = (val) => {
        const exists = question.failure_values.includes(val);
        if (exists) {
            update(index, 'failure_values', question.failure_values.filter(v => v !== val));
        } else {
            update(index, 'failure_values', [...question.failure_values, val]);
        }
    };

    return (
        <div className="bg-[#11111d]/60 border border-white/5 rounded-[2rem] p-8 space-y-8 relative group/card hover:border-orange-500/20 transition-all duration-300 shadow-xl">
            <div className="flex flex-col lg:flex-row gap-8">
                <div className="flex-1 space-y-6">
                    <div className="flex items-start gap-4">
                        <div className="w-8 h-8 rounded-lg bg-orange-500/10 border border-orange-500/20 flex items-center justify-center text-[10px] font-black text-orange-400 italic">#{index + 1}</div>
                        <div className="flex-1 flex flex-col gap-1.5">
                            <label htmlFor={`question_label_${index}`} className="pl-3 text-[7px] font-black text-orange-400/40 uppercase tracking-[0.3em] italic">Enunciado / Reactivo</label>
                            <input
                                id={`question_label_${index}`}
                                name={`question_label_${index}`}
                                type="text"
                                value={question.label}
                                onChange={e => update(index, 'label', e.target.value)}
                                placeholder="¿QUÉ SE DEBE VERIFICAR?"
                                className="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-sm font-bold text-white focus:ring-1 focus:ring-orange-500 transition-all uppercase placeholder-white/5"
                            />
                        </div>
                    </div>

                    <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div className="flex flex-col gap-1.5">
                            <label htmlFor={`question_type_${index}`} className="pl-3 text-[7px] font-black text-orange-400/40 uppercase tracking-[0.3em] italic">Tipo de Respuesta</label>
                            <select
                                id={`question_type_${index}`}
                                name={`question_type_${index}`}
                                value={question.type}
                                onChange={e => update(index, 'type', e.target.value)}
                                className="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-xs font-bold text-white focus:ring-1 focus:ring-orange-500 transition-all [color-scheme:dark] italic uppercase"
                            >
                                <option value="text">Texto Corto</option>
                                <option value="textarea">Texto Extendido</option>
                                <option value="select">Lista Desplegable</option>
                                <option value="radio">Opción Única (Radio)</option>
                                <option value="checkbox">Opción Múltiple (Check)</option>
                                <option value="number">Numérico</option>
                                <option value="date">Fecha / Calendario</option>
                                <option value="time">Hora</option>
                                <option value="datetime">Fecha y Hora</option>
                            </select>
                        </div>
                        <div className="flex flex-col gap-1.5">
                            <label htmlFor={`question_help_${index}`} className="pl-3 text-[7px] font-black text-orange-400/40 uppercase tracking-[0.3em] italic">Ayuda al Operador</label>
                            <input
                                id={`question_help_${index}`}
                                name={`question_help_${index}`}
                                type="text"
                                value={question.help_text || ''}
                                onChange={e => update(index, 'help_text', e.target.value)}
                                placeholder="GUÍA RÁPIDA..."
                                className="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-[10px] font-bold text-white/60 focus:ring-1 focus:ring-orange-500 transition-all uppercase placeholder-white/5"
                            />
                        </div>
                        <div className="flex items-center gap-4 bg-black/20 px-6 py-2 rounded-xl border border-white/5">
                            <label className="inline-flex items-center cursor-pointer group">
                                <input
                                    id={`question_required_${index}`}
                                    name={`question_required_${index}`}
                                    type="checkbox"
                                    checked={question.is_required}
                                    onChange={e => update(index, 'is_required', e.target.checked)}
                                    className="sr-only peer"
                                />
                                <div className="relative w-8 h-4 bg-white/5 border border-white/10 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[1px] after:start-[1px] after:bg-white/20 after:border-white/10 after:border after:rounded-full after:h-3.5 after:w-3.5 after:transition-all peer-checked:bg-orange-500/50"></div>
                                <span className="ms-3 text-[8px] font-black text-white/20 uppercase tracking-widest italic group-hover:text-orange-400 transition-colors">Obligatorio</span>
                            </label>
                        </div>
                    </div>
                </div>

                {(['select', 'radio', 'checkbox'].includes(question.type)) && (
                    <div className="lg:w-80 space-y-4 pt-2">
                        <div className="flex flex-col gap-1.5">
                            <label htmlFor={`question_optionInput_${index}`} className="pl-3 text-[7px] font-black text-emerald-400 uppercase tracking-[0.3em] italic">Inyectar Opción</label>
                            <div className="flex gap-2">
                                <input
                                    id={`question_optionInput_${index}`}
                                    name={`question_optionInput_${index}`}
                                    type="text"
                                    value={optionInput}
                                    onChange={e => setOptionInput(e.target.value)}
                                    onKeyDown={e => e.key === 'Enter' && (e.preventDefault(), addOption())}
                                    className="flex-1 bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-xs font-bold text-white focus:ring-1 focus:ring-emerald-500 transition-all uppercase outline-none"
                                />
                                <button type="button" onClick={addOption} className="w-10 h-10 bg-emerald-500/20 border border-emerald-500/30 text-emerald-400 rounded-xl flex items-center justify-center hover:bg-emerald-500 hover:text-white transition-all">
                                    <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4" strokeWidth="3" /></svg>
                                </button>
                            </div>
                        </div>
                        <div className="flex flex-wrap gap-2">
                            {question.options.map((opt, oi) => (
                                <div key={oi} className={`group/opt flex items-center gap-2 pl-3 pr-1 py-1 rounded-lg border transition-all ${question.failure_values.includes(opt) ? 'bg-red-500/10 border-red-500/30 text-red-500' : 'bg-white/5 border-white/10 text-white/40'}`}>
                                    <span onClick={() => toggleFailure(opt)} className="text-[9px] font-black uppercase cursor-pointer">{opt}</span>
                                    <button type="button" onClick={() => removeOption(oi)} className="w-5 h-5 flex items-center justify-center hover:text-red-500 transition-colors">
                                        <svg className="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" strokeWidth="3" /></svg>
                                    </button>
                                </div>
                            ))}
                            {question.options.length === 0 && <div className="text-[8px] font-black text-white/10 italic p-2">Sin opciones definidas</div>}
                        </div>
                    </div>
                )}

                {question.type === 'number' && (
                    <div className="lg:w-80 space-y-2 pt-2">
                        <div className="grid grid-cols-2 gap-4">
                            <div className="flex flex-col gap-1.5">
                                <label htmlFor={`question_min_${index}`} className="pl-3 text-[7px] font-black text-white/20 uppercase tracking-[0.3em] italic">Mínimo</label>
                                <input id={`question_min_${index}`} name={`question_min_${index}`} type="number" value={question.min_value || ''} onChange={e => update(index, 'min_value', e.target.value)} className="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-xs text-white outline-none" />
                            </div>
                            <div className="flex flex-col gap-1.5">
                                <label htmlFor={`question_max_${index}`} className="pl-3 text-[7px] font-black text-white/20 uppercase tracking-[0.3em] italic">Máximo</label>
                                <input id={`question_max_${index}`} name={`question_max_${index}`} type="number" value={question.max_value || ''} onChange={e => update(index, 'max_value', e.target.value)} className="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-xs text-white outline-none" />
                            </div>
                        </div>
                        <p className="text-[8px] text-indigo-300/60 font-medium pl-2 italic leading-relaxed">
                            💡 Dejá ambos campos vacíos para permitir cualquier número libre (ej: lecturas de kilometraje histórico, horómetro, etc.) sin generar fallas.
                        </p>
                    </div>
                )}
            </div>

            <div className="absolute top-6 right-6 opacity-0 group-hover/card:opacity-100 transition-all flex border border-white/5 rounded-xl bg-black/40 divide-x divide-white/5 overflow-hidden">
                <button type="button" onClick={() => move(index, 'up')} className="w-9 h-10 flex items-center justify-center hover:bg-white/5 text-white/20 hover:text-white transition-all"><svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 15l7-7 7 7" strokeWidth="2" /></svg></button>
                <button type="button" onClick={() => move(index, 'down')} className="w-9 h-10 flex items-center justify-center hover:bg-white/5 text-white/20 hover:text-white transition-all"><svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" strokeWidth="2" /></svg></button>
                <button type="button" onClick={() => remove(index)} className="w-9 h-10 flex items-center justify-center hover:bg-white/5 text-red-500/40 hover:text-red-500 transition-all"><svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" strokeWidth="2" /></svg></button>
            </div>
        </div>
    );
}
