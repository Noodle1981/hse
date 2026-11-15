import React from 'react';

export function toArray(val) {
    if (Array.isArray(val)) return val;
    if (typeof val === 'string') {
        try {
            const parsed = JSON.parse(val);
            return Array.isArray(parsed) ? parsed : [];
        } catch {
            return [];
        }
    }
    return [];
}

export default function QuestionRenderer({ section, answers, onAnswerChange }) {
    return (
        <div className="p-12 space-y-10 animate-fade-in">
            <div className="border-b border-white/5 pb-8">
                <h2 className="text-2xl font-black italic uppercase tracking-tighter flex items-center gap-4">
                    <span className="w-1.5 h-10 bg-orange-500 rounded-full"></span>
                    {section.title}
                </h2>
                <p className="text-xs font-bold text-white/20 uppercase mt-2 italic tracking-widest leading-none">{section.description}</p>
            </div>

            <div className="space-y-12">
                {section.questions?.map((q, idx) => (
                    <div key={q.id} className="space-y-6">
                        <div className="flex items-start gap-4">
                            <span className="text-xs font-black text-orange-500/40 italic mt-1">{idx + 1}.</span>
                            <label className="text-lg font-black text-white italic uppercase tracking-tight flex-1">
                                {q.label}
                                {q.is_required && <span className="text-orange-500 ml-2 font-black">*</span>}
                            </label>
                        </div>

                        {q.type === 'text' && (
                            <input
                                id={`answer_${q.id}`}
                                name={`answer_${q.id}`}
                                type="text"
                                value={answers[q.id] || ''}
                                onChange={e => onAnswerChange(q.id, e.target.value)}
                                className="w-full bg-black/40 border-b-2 border-white/10 text-white focus:border-orange-500 outline-none py-3 text-lg font-black uppercase transition-all"
                            />
                        )}

                        {q.type === 'number' && (
                            <div className="flex items-center gap-4">
                                <input
                                    id={`answer_${q.id}`}
                                    name={`answer_${q.id}`}
                                    type="number"
                                    value={answers[q.id] || ''}
                                    onChange={e => onAnswerChange(q.id, e.target.value)}
                                    className="w-48 bg-black/40 border-b-2 border-white/10 text-white focus:border-orange-500 outline-none py-3 text-2xl font-black transition-all"
                                />
                                <span className="text-[10px] font-black text-white/20 uppercase italic">Unidades / Valor</span>
                            </div>
                        )}

                        {(q.type === 'radio' || q.type === 'select') && (
                            <div className="space-y-3">
                                <div className="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    {toArray(q.options).map((opt, oi) => {
                                        const isSelected = answers[q.id] === opt;
                                        const isCritical = isSelected && q.failure_logic === 'fail_if_selected' && toArray(q.failure_values).includes(opt);
                                        return (
                                            <button
                                                key={oi}
                                                type="button"
                                                onClick={() => onAnswerChange(q.id, opt)}
                                                className={`p-4 rounded-2xl border text-xs font-black uppercase italic transition-all text-left flex items-center justify-between ${
                                                    isCritical
                                                        ? 'bg-red-500/20 border-red-500 text-red-400 shadow-lg shadow-red-900/20'
                                                        : isSelected
                                                            ? 'bg-orange-500/20 border-orange-500 text-orange-400'
                                                            : 'bg-white/5 border-white/5 text-white/40 hover:bg-white/10'
                                                }`}
                                            >
                                                {opt}
                                                {isSelected && (
                                                    <svg className="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fillRule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clipRule="evenodd" />
                                                    </svg>
                                                )}
                                            </button>
                                        );
                                    })}
                                </div>
                                {q.failure_logic === 'fail_if_selected' && toArray(q.failure_values).includes(answers[q.id]) && (
                                    <div className="flex items-start gap-3 bg-red-500/10 border border-red-500/30 rounded-2xl p-4 animate-fade-in">
                                        <svg className="w-5 h-5 text-red-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                        </svg>
                                        <p className="text-[10px] font-black text-red-400 uppercase tracking-widest italic">⚠ Condición Crítica Detectada — Requiere Acción Correctiva</p>
                                    </div>
                                )}
                            </div>
                        )}

                        {q.type === 'date' && (
                            <div className="flex items-center gap-4">
                                <input
                                    id={`answer_${q.id}`}
                                    name={`answer_${q.id}`}
                                    type="date"
                                    value={answers[q.id] || ''}
                                    onChange={e => onAnswerChange(q.id, e.target.value)}
                                    className="bg-black/40 border border-white/10 text-white focus:border-orange-500 focus:ring-1 focus:ring-orange-500 outline-none py-4 px-6 text-lg font-black transition-all rounded-[1.5rem] [color-scheme:dark] w-full max-w-xs"
                                />
                            </div>
                        )}

                        {q.type === 'time' && (
                            <div className="flex items-center gap-4">
                                <input
                                    id={`answer_${q.id}`}
                                    name={`answer_${q.id}`}
                                    type="time"
                                    value={answers[q.id] || ''}
                                    onChange={e => onAnswerChange(q.id, e.target.value)}
                                    className="bg-black/40 border border-white/10 text-white focus:border-orange-500 focus:ring-1 focus:ring-orange-500 outline-none py-4 px-6 text-lg font-black transition-all rounded-[1.5rem] [color-scheme:dark] w-full max-w-xs"
                                />
                                <button
                                    type="button"
                                    onClick={() => onAnswerChange(q.id, new Date().toTimeString().split(' ')[0].substring(0, 5))}
                                    className="px-6 py-4 bg-orange-600/10 border border-orange-600/20 text-orange-500 rounded-[1.5rem] text-[10px] font-black uppercase tracking-wider italic hover:bg-orange-600 hover:text-white transition-all active:scale-95"
                                >
                                    🕒 Hora Actual
                                </button>
                            </div>
                        )}

                        {q.type === 'datetime' && (
                            <div className="flex items-center gap-4">
                                <input
                                    id={`answer_${q.id}`}
                                    name={`answer_${q.id}`}
                                    type="datetime-local"
                                    value={answers[q.id] || ''}
                                    onChange={e => onAnswerChange(q.id, e.target.value)}
                                    className="bg-black/40 border border-white/10 text-white focus:border-orange-500 focus:ring-1 focus:ring-orange-500 outline-none py-4 px-6 text-lg font-black transition-all rounded-[1.5rem] [color-scheme:dark] w-full max-w-xs"
                                />
                                <button
                                    type="button"
                                    onClick={() => {
                                        const now = new Date();
                                        const localISOTime = new Date(now.getTime() - now.getTimezoneOffset() * 60000).toISOString().slice(0, 16);
                                        onAnswerChange(q.id, localISOTime);
                                    }}
                                    className="px-6 py-4 bg-orange-600/10 border border-orange-600/20 text-orange-500 rounded-[1.5rem] text-[10px] font-black uppercase tracking-wider italic hover:bg-orange-600 hover:text-white transition-all active:scale-95"
                                >
                                    📅 Ahora
                                </button>
                            </div>
                        )}

                        {q.type === 'textarea' && (
                            <textarea
                                id={`answer_${q.id}`}
                                name={`answer_${q.id}`}
                                value={answers[q.id] || ''}
                                onChange={e => onAnswerChange(q.id, e.target.value)}
                                rows="3"
                                className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-white text-sm font-bold focus:outline-none focus:ring-1 focus:ring-orange-500 transition-all uppercase"
                                placeholder="Ingrese observaciones..."
                            />
                        )}

                        {q.type === 'checkbox' && (
                            <div className="space-y-3">
                                <div className="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    {toArray(q.options).map((opt, oi) => {
                                        const selected = Array.isArray(answers[q.id]) ? answers[q.id] : (answers[q.id] ? [answers[q.id]] : []);
                                        const isChecked = selected.includes(opt);
                                        const isCriticalVal = toArray(q.failure_values).includes(opt);
                                        const isCritical = isCriticalVal && ((q.failure_logic === 'fail_if_selected' && isChecked) || (q.failure_logic === 'fail_if_missing' && !isChecked));
                                        const toggle = () => {
                                            const next = isChecked ? selected.filter(v => v !== opt) : [...selected, opt];
                                            onAnswerChange(q.id, next);
                                        };
                                        return (
                                            <button
                                                key={oi}
                                                type="button"
                                                onClick={toggle}
                                                className={`p-4 rounded-2xl border text-xs font-black uppercase italic transition-all text-left flex items-center gap-3 ${
                                                    isCritical
                                                        ? 'bg-red-500/20 border-red-500 text-red-400 shadow-lg shadow-red-900/20'
                                                        : isChecked
                                                            ? 'bg-emerald-500/20 border-emerald-500 text-emerald-400'
                                                            : 'bg-white/5 border-white/5 text-white/40 hover:bg-white/10'
                                                }`}
                                            >
                                                <span className={`w-4 h-4 rounded border flex-shrink-0 flex items-center justify-center transition-all ${isCritical ? 'bg-red-500 border-red-500' : isChecked ? 'bg-emerald-500 border-emerald-500' : 'border-white/20'}`}>
                                                    {isChecked && (
                                                        <svg className="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fillRule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clipRule="evenodd" />
                                                        </svg>
                                                    )}
                                                </span>
                                                {opt}
                                                {q.failure_logic === 'fail_if_missing' && isCriticalVal && !isChecked && (
                                                    <span className="ml-auto text-[8px] font-black text-red-400 uppercase tracking-widest">FALTANTE</span>
                                                )}
                                            </button>
                                        );
                                    })}
                                </div>
                                {(() => {
                                    const fv = toArray(q.failure_values);
                                    if (fv.length === 0) return null;
                                    const selected = Array.isArray(answers[q.id]) ? answers[q.id] : [];
                                    let criticalCount = 0;
                                    if (q.failure_logic === 'fail_if_selected') criticalCount = selected.filter(v => fv.includes(v)).length;
                                    else if (q.failure_logic === 'fail_if_missing') criticalCount = fv.filter(v => !selected.includes(v)).length;
                                    if (criticalCount === 0) return null;
                                    return (
                                        <div className="flex items-start gap-3 bg-red-500/10 border border-red-500/30 rounded-2xl p-4 animate-fade-in">
                                            <svg className="w-5 h-5 text-red-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                            </svg>
                                            <p className="text-[10px] font-black text-red-400 uppercase tracking-widest italic">
                                                {q.failure_logic === 'fail_if_missing'
                                                    ? `⚠ ${criticalCount} elemento${criticalCount > 1 ? 's' : ''} faltante${criticalCount > 1 ? 's' : ''} — Condición Crítica de Seguridad`
                                                    : `⚠ ${criticalCount} condición${criticalCount > 1 ? 'es' : ''} crítica${criticalCount > 1 ? 's' : ''} — Requiere Acción Correctiva`}
                                            </p>
                                        </div>
                                    );
                                })()}
                            </div>
                        )}

                        {q.help_text && (
                            <p className="text-[9px] font-black text-white/20 italic uppercase tracking-[0.2em] bg-white/5 px-4 py-2 rounded-lg inline-block">💡 {q.help_text}</p>
                        )}
                    </div>
                ))}
            </div>
        </div>
    );
}
