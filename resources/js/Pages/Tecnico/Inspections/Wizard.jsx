import { router, Head, useForm, usePage, Link } from '@inertiajs/react';
import AuthenticatedLayout from '../../../Layouts/AuthenticatedLayout';
import { useState, useEffect, useRef } from 'react';
import SignaturePad from 'signature_pad';

/**
 * Safely coerce a value to an array.
 * Handles: real arrays, JSON-encoded strings (including double-encoded), null/undefined.
 */
function toArray(val) {
    if (Array.isArray(val)) return val;
    if (typeof val === 'string') {
        try { const parsed = JSON.parse(val); return Array.isArray(parsed) ? parsed : []; } catch { return []; }
    }
    return [];
}

export default function Wizard({ checklist, assignment = {}, inspection = {}, visitId = null, initialAnswers = {}, isEmulation = false }) {
    const { auth, flash } = usePage().props;
    const user = auth.user;
    console.log("DEBUG - User in Wizard props:", user);
    const [currentStep, setCurrentStep] = useState(0);
    const sections = checklist.sections || [];
    const totalSteps = sections.length + 2; // Intro + Secciones + Finalización

    // Detectar por URL si estamos en el contexto de técnico (más confiable que comparar strings de rol)
    const isTecnico = typeof window !== 'undefined' && window.location.pathname.startsWith('/tecnico');
    const saveRoute = isTecnico ? 'tecnico.checklists.wizard.save' : 'gestor.checklists.wizard.save';
    const completeRoute = isTecnico ? 'tecnico.checklists.wizard.complete' : 'gestor.checklists.wizard.complete';

    const form = useForm({
        answers: initialAnswers || {},
        notes: inspection.notes || '',
        supervisor_name: inspection.supervisor_name || '',
        supervisor_signature: '',
        technician_signature: '',
        visit_id: visitId,
    });

    // Manejo de Firmas
    const supervisorSigRef = useRef(null);
    const supervisorPad = useRef(null);

    useEffect(() => {
        if (currentStep === totalSteps - 1) {
            if (supervisorSigRef.current) {
                supervisorPad.current = new SignaturePad(supervisorSigRef.current, {
                    backgroundColor: 'rgba(255,255,255,0.05)',
                    penColor: 'rgb(255, 255, 255)'
                });
            }
        }
    }, [currentStep, totalSteps]);

    const handleAnswerChange = (questionId, value) => {
        form.setData('answers', {
            ...form.data.answers,
            [questionId]: value
        });
    };

    const nextStep = () => {
        if (currentStep < totalSteps - 1) {
            setCurrentStep(currentStep + 1);
            window.scrollTo(0, 0);
        }
    };

    const prevStep = () => {
        if (currentStep > 0) {
            setCurrentStep(currentStep - 1);
            window.scrollTo(0, 0);
        }
    };

    const submit = (e) => {
        e.preventDefault();
        
        if (isEmulation) {
            alert('Modo Emulación: El protocolo ha finalizado correctamente (No se guardaron datos).');
            const fallbackRoute = isTecnico ? 'tecnico.checklists.tasks.index' : 'gestor.checklists.index';
            router.get(route(fallbackRoute));
            return;
        }

        if (!inspection?.id) {
            alert('Error: no se pudo identificar la inspección. Recargá la página.');
            return;
        }

        if (supervisorPad.current) {
            if (supervisorPad.current.isEmpty()) {
                alert('La firma del operario/personal es obligatoria.');
                return;
            }
            if (!user.signature) {
                alert('No se encontró una firma registrada en su perfil. Es obligatorio tener una firma cargada para poder certificar.');
                return;
            }
            
            const sigData = {
                ...form.data,
                supervisor_signature: supervisorPad.current.toDataURL(),
                technician_signature: user.signature,
            };

            router.post(
                route(completeRoute, { id: checklist.id, inspection: inspection.id }),
                sigData,
                { preserveScroll: false }
            );
        }
    };

    return (
        <AuthenticatedLayout title="Asistente de Inspección Táctica">
            <Head title={`Inspección: ${checklist.title}`} />

            <div className="px-4 sm:px-6 lg:px-8 py-8 text-white selection:bg-orange-500/30">
                <div className="max-w-4xl mx-auto space-y-8 animate-fade-in-up">
                    
                    {/* HUD Title & Sync Badge */}
                    <div className="text-center space-y-3">
                        <h1 className="text-xl font-black italic uppercase tracking-tight text-white/90">{checklist.title}</h1>
                        <div className="inline-flex items-center gap-1.5 px-3 py-1.5 bg-orange-500/5 border border-orange-500/20 rounded-full text-[10px] font-black uppercase tracking-widest italic text-orange-500 shadow-[0_0_15px_rgba(246,146,57,0.1)]">
                            <span className="animate-pulse">⌚</span> Tiempo Real (Sync Servidor)
                        </div>
                    </div>

                    {/* Progress Loader */}
                    <div className="bg-[#16162a]/60 backdrop-blur-xl rounded-full border border-white/5 p-2 flex items-center gap-4 px-6 shadow-2xl">
                        <div className="flex-1 h-1.5 bg-white/5 rounded-full overflow-hidden">
                            <div className="h-full bg-orange-500 shadow-[0_0_15px_rgba(246,146,57,0.5)] transition-all duration-500" style={{ width: `${(currentStep / (totalSteps - 1)) * 100}%` }}></div>
                        </div>
                        <span className="text-[10px] font-black italic uppercase tracking-widest text-white/40">Fase {currentStep + 1} / {totalSteps}</span>
                    </div>

                    <div className="bg-[#16162a]/40 backdrop-blur-md rounded-[3rem] border border-white/5 shadow-3xl overflow-hidden relative">
                        <div className="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-transparent via-orange-500 to-transparent"></div>
                        
                        {/* PASO 0: INTRODUCCIÓN */}
                        {currentStep === 0 && (
                            <div className="p-12 text-center space-y-8 animate-fade-in">
                                <div className="w-24 h-24 bg-orange-500/10 border border-orange-500/20 rounded-3xl flex items-center justify-center mx-auto text-orange-500 shadow-2xl">
                                    <svg className="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                </div>
                                <div>
                                    <h2 className="text-3xl font-black italic uppercase tracking-tighter leading-none mb-4">{checklist.title}</h2>
                                    <p className="text-sm font-bold text-white/40 uppercase italic tracking-wide max-w-xl mx-auto">{checklist.description || 'Iniciando protocolo de verificación táctica para la sede asignada.'}</p>
                                </div>
                                <div className="pt-8 grid grid-cols-2 gap-4 max-w-sm mx-auto">
                                    <div className="bg-black/20 p-4 rounded-2xl border border-white/5 text-center">
                                        <div className="text-[8px] font-black text-white/20 uppercase tracking-widest mb-1">Secciones</div>
                                        <div className="text-xl font-black italic text-white">{sections.length}</div>
                                    </div>
                                    <div className="bg-black/20 p-4 rounded-2xl border border-white/5 text-center">
                                        <div className="text-[8px] font-black text-white/20 uppercase tracking-widest mb-1">Preguntas</div>
                                        <div className="text-xl font-black italic text-white">{checklist.questions?.length || 0}</div>
                                    </div>
                                </div>
                                <div className="pt-10">
                                    <button onClick={nextStep} className="px-16 py-6 bg-orange-600 text-white rounded-[2.5rem] text-xs font-black uppercase tracking-[0.2em] italic shadow-2xl shadow-orange-600/20 hover:brightness-110 active:scale-95 transition-all">Iniciar Protocolo</button>
                                </div>
                            </div>
                        )}

                        {/* PASOS DE SECCIONES */}
                        {currentStep > 0 && currentStep <= sections.length && (
                            <div className="p-12 space-y-10 animate-fade-in">
                                <div className="border-b border-white/5 pb-8">
                                    <h2 className="text-2xl font-black italic uppercase tracking-tighter flex items-center gap-4">
                                        <span className="w-1.5 h-10 bg-orange-500 rounded-full"></span>
                                        {sections[currentStep - 1].title}
                                    </h2>
                                    <p className="text-xs font-bold text-white/20 uppercase mt-2 italic tracking-widest leading-none">{sections[currentStep - 1].description}</p>
                                </div>

                                <div className="space-y-12">
                                    {sections[currentStep - 1].questions?.map((q, idx) => (
                                        <div key={q.id} className="space-y-6">
                                            <div className="flex items-start gap-4">
                                                <span className="text-xs font-black text-orange-500/40 italic mt-1">{idx + 1}.</span>
                                                <label className="text-lg font-black text-white italic uppercase tracking-tight flex-1">
                                                    {q.label}
                                                    {q.is_required && <span className="text-orange-500 ml-2 font-black">*</span>}
                                                </label>
                                            </div>
                                            
                                            {/* Renderizado de tipos de pregunta */}
                                            {q.type === 'text' && (
                                                <input id={`answer_\${q.id}`} name={`answer_\${q.id}`} type="text" value={form.data.answers[q.id] || ''} onChange={e => handleAnswerChange(q.id, e.target.value)} className="w-full bg-black/40 border-b-2 border-white/10 text-white focus:border-orange-500 outline-none py-3 text-lg font-black uppercase transition-all"/>
                                            )}

                                            {q.type === 'number' && (
                                                <div className="flex items-center gap-4">
                                                    <input id={`answer_\${q.id}`} name={`answer_\${q.id}`} type="number" value={form.data.answers[q.id] || ''} onChange={e => handleAnswerChange(q.id, e.target.value)} className="w-48 bg-black/40 border-b-2 border-white/10 text-white focus:border-orange-500 outline-none py-3 text-2xl font-black transition-all"/>
                                                    <span className="text-[10px] font-black text-white/20 uppercase italic">Unidades / Valor</span>
                                                </div>
                                            )}

                                            {(q.type === 'radio' || q.type === 'select') && (
                                                <div className="space-y-3">
                                                    <div className="grid grid-cols-1 md:grid-cols-2 gap-3">
                                                        {toArray(q.options).map((opt, oi) => {
                                                            const isSelected = form.data.answers[q.id] === opt;
                                                            const isCritical = isSelected 
                                                                && q.failure_logic === 'fail_if_selected' 
                                                                && toArray(q.failure_values).includes(opt);
                                                            return (
                                                                <button 
                                                                    key={oi}
                                                                    type="button" 
                                                                    onClick={() => handleAnswerChange(q.id, opt)}
                                                                    className={`p-4 rounded-2xl border text-xs font-black uppercase italic transition-all text-left flex items-center justify-between
                                                                        ${isCritical ? 'bg-red-500/20 border-red-500 text-red-400 shadow-lg shadow-red-900/20' :
                                                                          isSelected ? 'bg-orange-500/20 border-orange-500 text-orange-400' : 
                                                                          'bg-white/5 border-white/5 text-white/40 hover:bg-white/10'}`}
                                                                >
                                                                    {opt}
                                                                    {isSelected && <svg className="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fillRule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clipRule="evenodd"/></svg>}
                                                                </button>
                                                            );
                                                        })}
                                                    </div>
                                                    {/* Alerta crítica */}
                                                    {q.failure_logic === 'fail_if_selected' && toArray(q.failure_values).includes(form.data.answers[q.id]) && (
                                                        <div className="flex items-start gap-3 bg-red-500/10 border border-red-500/30 rounded-2xl p-4 animate-fade-in">
                                                            <svg className="w-5 h-5 text-red-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                                            <p className="text-[10px] font-black text-red-400 uppercase tracking-widest italic">⚠ Condición Crítica Detectada — Requiere Acción Correctiva</p>
                                                        </div>
                                                    )}
                                                </div>
                                            )}

                                            {q.type === 'date' && (
                                                <div className="flex items-center gap-4">
                                                    <input id={`answer_\${q.id}`} name={`answer_\${q.id}`} 
                                                        type="date" 
                                                        value={form.data.answers[q.id] || ''} 
                                                        onChange={e => handleAnswerChange(q.id, e.target.value)} 
                                                        className="bg-black/40 border-b-2 border-white/10 text-white focus:border-orange-500 outline-none py-3 px-4 text-lg font-black transition-all rounded-xl [color-scheme:dark]"
                                                    />
                                                </div>
                                            )}

                                            {q.type === 'textarea' && (
                                                <textarea id={`answer_\${q.id}`} name={`answer_\${q.id}`} 
                                                    value={form.data.answers[q.id] || ''} 
                                                    onChange={e => handleAnswerChange(q.id, e.target.value)} 
                                                    rows="3"
                                                    className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-white text-sm font-bold focus:outline-none focus:ring-1 focus:ring-orange-500 transition-all uppercase"
                                                    placeholder="Ingrese observaciones..."
                                                />
                                            )}

                                            {q.type === 'checkbox' && (
                                                <div className="space-y-3">
                                                    <div className="grid grid-cols-1 md:grid-cols-2 gap-3">
                                                        {toArray(q.options).map((opt, oi) => {
                                                            const selected = Array.isArray(form.data.answers[q.id]) 
                                                                ? form.data.answers[q.id] 
                                                                : (form.data.answers[q.id] ? [form.data.answers[q.id]] : []);
                                                            const isChecked = selected.includes(opt);
                                                            const isCriticalVal = toArray(q.failure_values).includes(opt);
                                                            // fail_if_selected: rojo si lo marcás (faltante)
                                                            // fail_if_missing: rojo si NO lo marcás (elemento obligatorio ausente)
                                                            const isCritical = isCriticalVal && (
                                                                (q.failure_logic === 'fail_if_selected' && isChecked) ||
                                                                (q.failure_logic === 'fail_if_missing' && !isChecked)
                                                            );
                                                            const toggle = () => {
                                                                const next = isChecked 
                                                                    ? selected.filter(v => v !== opt) 
                                                                    : [...selected, opt];
                                                                handleAnswerChange(q.id, next);
                                                            };
                                                            return (
                                                                <button 
                                                                    key={oi} 
                                                                    type="button"
                                                                    onClick={toggle}
                                                                    className={`p-4 rounded-2xl border text-xs font-black uppercase italic transition-all text-left flex items-center gap-3
                                                                        ${isCritical ? 'bg-red-500/20 border-red-500 text-red-400 shadow-lg shadow-red-900/20' :
                                                                          isChecked ? 'bg-emerald-500/20 border-emerald-500 text-emerald-400' : 
                                                                          'bg-white/5 border-white/5 text-white/40 hover:bg-white/10'}`}
                                                                >
                                                                    <span className={`w-4 h-4 rounded border flex-shrink-0 flex items-center justify-center transition-all ${isCritical ? 'bg-red-500 border-red-500' : isChecked ? 'bg-emerald-500 border-emerald-500' : 'border-white/20'}`}>
                                                                        {isChecked && <svg className="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20"><path fillRule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clipRule="evenodd"/></svg>}
                                                                    </span>
                                                                    {opt}
                                                                    {q.failure_logic === 'fail_if_missing' && isCriticalVal && !isChecked && (
                                                                        <span className="ml-auto text-[8px] font-black text-red-400 uppercase tracking-widest">FALTANTE</span>
                                                                    )}
                                                                </button>
                                                            );
                                                        })}
                                                    </div>
                                                    {/* Alerta elementos faltantes */}
                                                    {(() => {
                                                        const fv = toArray(q.failure_values);
                                                        if (fv.length === 0) return null;
                                                        const selected = Array.isArray(form.data.answers[q.id]) ? form.data.answers[q.id] : [];
                                                        let criticalCount = 0;
                                                        if (q.failure_logic === 'fail_if_selected') {
                                                            criticalCount = selected.filter(v => fv.includes(v)).length;
                                                        } else if (q.failure_logic === 'fail_if_missing') {
                                                            criticalCount = fv.filter(v => !selected.includes(v)).length;
                                                        }
                                                        if (criticalCount === 0) return null;
                                                        return (
                                                            <div className="flex items-start gap-3 bg-red-500/10 border border-red-500/30 rounded-2xl p-4 animate-fade-in">
                                                                <svg className="w-5 h-5 text-red-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                                                <p className="text-[10px] font-black text-red-400 uppercase tracking-widest italic">
                                                                    {q.failure_logic === 'fail_if_missing' 
                                                                        ? `⚠ ${criticalCount} elemento${criticalCount > 1 ? 's' : ''} faltante${criticalCount > 1 ? 's' : ''} — Condición Crítica de Seguridad`
                                                                        : `⚠ ${criticalCount} condición${criticalCount > 1 ? 'es' : ''} crítica${criticalCount > 1 ? 's' : ''} — Requiere Acción Correctiva`
                                                                    }
                                                                </p>
                                                            </div>
                                                        );
                                                    })()}
                                                </div>
                                            )}

                                            {q.help_text && <p className="text-[9px] font-black text-white/20 italic uppercase tracking-[0.2em] bg-white/5 px-4 py-2 rounded-lg inline-block">💡 {q.help_text}</p>}
                                        </div>
                                    ))}
                                </div>
                            </div>
                        )}

                        {/* PASO FINAL: FIRMAS */}
                        {currentStep === totalSteps - 1 && (
                            <form onSubmit={submit} className="p-12 space-y-12 animate-fade-in">
                                <div className="border-b border-white/5 pb-8">
                                    <h2 className="text-2xl font-black italic uppercase tracking-tighter">Certificación de Inspección</h2>
                                    <p className="text-xs font-bold text-white/80 uppercase mt-2 italic tracking-widest">Capture las firmas para concluir el protocolo.</p>
                                </div>

                                <div className="space-y-10">
                                    <div className="grid grid-cols-1 md:grid-cols-2 gap-12">
                                        <div className="space-y-6">
                                            <div className="group">
                                                <label htmlFor="supervisor_name" className="block text-[10px] font-black text-orange-400 uppercase tracking-[0.3em] italic mb-3 ml-2">Operario / Personal chequeado o revisado</label>
                                                <input id="supervisor_name" name="supervisor_name" type="text" value={form.data.supervisor_name} onChange={e => form.setData('supervisor_name', e.target.value)} required className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-orange-500 transition-all uppercase"/>
                                            </div>
                                            <div className="space-y-4">
                                                <div className="flex items-center justify-between ml-4 mr-2">
                                                    <span className="text-[10px] font-black text-white/80 uppercase tracking-[0.3em] italic">Firma Responsable</span>
                                                    <button type="button" onClick={() => supervisorPad.current?.clear()} className="flex items-center gap-1.5 text-[10px] font-black text-white/80 hover:text-red-400 uppercase tracking-widest italic transition-colors">
                                                        <svg className="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                        Limpiar
                                                    </button>
                                                </div>
                                                <div className="bg-white/5 border border-white/10 rounded-[2rem] h-48 relative overflow-hidden">
                                                    <canvas ref={supervisorSigRef} className="w-full h-full cursor-crosshair"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                        <div className="space-y-6">
                                            <div className="group">
                                                <label className="block text-[10px] font-black text-blue-400 uppercase tracking-[0.3em] italic mb-3 ml-2">Técnico Certificante</label>
                                                <div className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-black text-white uppercase">
                                                    {user.name || [user.nombres, user.apellidos].filter(Boolean).join(' ') || 'Sin nombre'}
                                                </div>
                                            </div>
                                            <div className="space-y-4">
                                                <div className="flex items-center justify-between ml-4 mr-2">
                                                    <span className="text-[10px] font-black text-emerald-400 uppercase tracking-[0.3em] italic">✔ Firma de Perfil Autorizada</span>
                                                </div>
                                                <div className="bg-emerald-500/5 border border-emerald-500/20 rounded-[2rem] h-48 flex items-center justify-center p-6 relative overflow-hidden shadow-[inset_0_0_20px_rgba(16,185,129,0.05)]">
                                                    {user.signature ? (
                                                        <img src={user.signature} alt="Firma Técnico Autorizada" className="max-h-full max-w-full object-contain filter invert opacity-80" />
                                                    ) : (
                                                        <span className="text-[10px] font-black text-red-400 uppercase tracking-[0.2em] italic">No se encontró una firma registrada en su perfil</span>
                                                    )}
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div className="group">
                                        <label htmlFor="notes" className="block text-[10px] font-black text-white/80 uppercase tracking-[0.3em] italic mb-3 ml-2">Notas Finales del Servicio</label>
                                        <textarea id="notes" name="notes" value={form.data.notes} onChange={e => form.setData('notes', e.target.value)} rows="3" className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-orange-500 transition-all uppercase"></textarea>
                                    </div>
                                </div>

                                <div className="pt-8">
                                    <button type="submit" disabled={form.processing} className="w-full py-6 bg-emerald-600 text-white rounded-[2.5rem] text-xs font-black uppercase tracking-[0.3em] italic shadow-2xl shadow-emerald-600/20 hover:brightness-110 active:scale-95 transition-all">Certificar Protocolo</button>
                                </div>
                            </form>
                        )}
                    </div>

                    {/* Controles de Navegación Inferiores */}
                    <div className="flex justify-between items-center px-6">
                        <button 
                            onClick={prevStep} 
                            disabled={currentStep === 0} 
                            className="px-10 py-5 bg-white/5 text-white/40 hover:text-white rounded-[2rem] text-[10px] font-black uppercase tracking-widest italic transition-all disabled:opacity-0"
                        >
                            Fase Anterior
                        </button>
                        {currentStep > 0 && currentStep < totalSteps - 1 && (
                            <button 
                                onClick={nextStep} 
                                className="px-12 py-5 bg-orange-600 text-white rounded-[2rem] text-[10px] font-black uppercase tracking-[0.2em] italic shadow-xl hover:brightness-110 active:scale-95 transition-all"
                            >
                                Siguiente Fase
                            </button>
                        )}
                    </div>
                </div>
            </div>

            <style>{`
                .animate-fade-in { animation: fade-in 0.5s ease-out forwards; }
                .animate-fade-in-up { animation: fade-in-up 0.6s ease-out forwards; }
                @keyframes fade-in { from { opacity: 0; } to { opacity: 1; } }
                @keyframes fade-in-up { from { opacity: 0; transform: translateY(40px); } to { opacity: 1; transform: translateY(0); } }
            `}</style>
        </AuthenticatedLayout>
    );
}
