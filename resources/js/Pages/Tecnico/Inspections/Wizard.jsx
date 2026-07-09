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

export default function Wizard({ checklist, assignment = {}, inspection = {}, visitId = null, initialAnswers = {}, assets = [], isEmulation = false, inspectedAssetIds = [] }) {
    const { auth, flash } = usePage().props;
    const user = auth.user;
    console.log("DEBUG - User in Wizard props:", user);
    const [currentStep, setCurrentStep] = useState(0);
    const [showFailureModal, setShowFailureModal] = useState(false);
    const [pendingNextStep, setPendingNextStep] = useState(null);
    const [selectedAssetId, setSelectedAssetId] = useState(inspection.asset_id || '');
    const [assetSearch, setAssetSearch] = useState('');
    const [showReinspectModal, setShowReinspectModal] = useState(false);
    const [pendingAssetId, setPendingAssetId] = useState(null);
    const [sreBlockError, setSreBlockError] = useState(null);

    const sections = checklist.sections || [];
    const totalQuestions = sections.reduce((acc, sec) => acc + (sec.questions?.length || 0), 0);
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
        asset_id: inspection.asset_id || '',
    });

    const filteredAssets = (assets || []).filter(a =>
        a.name.toLowerCase().includes(assetSearch.toLowerCase()) ||
        a.identifier.toLowerCase().includes(assetSearch.toLowerCase())
    );

    const handleSelectAsset = (assetId) => {
        const newValue = selectedAssetId === assetId ? '' : assetId;
        
        if (newValue) {
            const selectedAsset = (assets || []).find(a => a.id === assetId);
            if (selectedAsset && selectedAsset.status === 'fuera_de_servicio') {
                const schema = selectedAsset.asset_type?.metadata_schema || [];
                let isResolving = false;
                schema.forEach(field => {
                    const rules = field.maintenance_rules || [];
                    rules.forEach(rule => {
                        if (String(rule.checklist_resolucion_id) === String(checklist.id)) {
                            isResolving = true;
                        }
                    });
                });

                if (!isResolving) {
                    setSreBlockError("ACCESO DENEGADO: El equipo se encuentra Fuera de Servicio por Mantenimiento Vencido.");
                    return;
                }
            }
        }

        // Clear SRE block error on successful selection or deselect
        setSreBlockError(null);

        // Show reinspect warning if asset has already been audited in the current visit
        const isInspected = (inspectedAssetIds || []).includes(assetId);
        if (newValue && isInspected) {
            setPendingAssetId(newValue);
            setShowReinspectModal(true);
            return;
        }

        executeSelectAsset(newValue);
    };

    const executeSelectAsset = (newValue) => {
        setSelectedAssetId(newValue);
        
        const updatedData = {
            ...form.data,
            asset_id: newValue,
        };
        
        const newAnswers = { ...form.data.answers };
        let hasChanges = false;
        
        if (newValue) {
            const selectedAsset = (assets || []).find(a => a.id === newValue);
            const metadata = (selectedAsset && selectedAsset.metadata) ? selectedAsset.metadata : {};
            
            // Map metadata schema fields for lookup
            const schemaFields = selectedAsset?.asset_type?.metadata_schema || [];
            const schemaMap = {};
            schemaFields.forEach(f => {
                schemaMap[f.name] = f;
            });
            
            sections.forEach(sec => {
                (sec.questions || []).forEach(q => {
                    if (q.vincular_a_metadata) {
                        const fieldSchema = schemaMap[q.vincular_a_metadata];
                        
                        // Infer role if not explicitly saved (backward compatibility)
                        let role = fieldSchema?.role;
                        if (!role && fieldSchema) {
                            const label = fieldSchema.label || '';
                            const isExpirationDate = /venc|vto|vigenc|proxim|caduc/i.test(label);
                            const isIdentifier = fieldSchema.show_in_create || /identif|nro|n°|interno|codigo|patente|matricula/i.test(label);
                            const isStatic = /tipo|modelo|marca|fabricante|capacidad/i.test(label);
                            
                            if (isIdentifier) {
                                role = 'identificador';
                            } else if (fieldSchema.type === 'date' && isExpirationDate) {
                                role = 'dato_con_regla';
                            } else if (isStatic) {
                                role = 'dato_estatico';
                            } else {
                                role = 'verificacion';
                            }
                        }
                        
                        // Pre-fill only if role is identifier, static data, or SRE rule data
                        if (role && ['identificador', 'dato_estatico', 'dato_con_regla'].includes(role)) {
                            const metadataValue = metadata[q.vincular_a_metadata];
                            newAnswers[q.id] = (metadataValue !== undefined && metadataValue !== null) ? metadataValue : '';
                        } else {
                            // Verification fields should start empty
                            newAnswers[q.id] = '';
                        }
                        hasChanges = true;
                    }
                });
            });
        } else {
            // Clear all metadata-linked fields when deselecting
            sections.forEach(sec => {
                (sec.questions || []).forEach(q => {
                    if (q.vincular_a_metadata) {
                        newAnswers[q.id] = '';
                        hasChanges = true;
                    }
                });
            });
        }
        
        if (hasChanges) {
            updatedData.answers = newAnswers;
        }
        
        form.setData(updatedData);
    };

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

    /**
     * Evalúa si alguna pregunta de la sección actual ha fallado.
     */
    const sectionHasFailure = (sectionIndex) => {
        const section = sections[sectionIndex];
        if (!section) return false;
        return section.questions?.some(q => {
            const answer = form.data.answers[q.id];
            if (answer === undefined || answer === null || answer === '') return false;
            const fv = toArray(q.failure_values);
            if (fv.length === 0) return false;
            const logic = q.failure_logic ?? 'fail_if_selected';
            if (logic === 'fail_if_missing') {
                const selected = Array.isArray(answer) ? answer : (answer ? [answer] : []);
                return fv.some(req => !selected.includes(req));
            } else {
                const selected = Array.isArray(answer) ? answer : [answer];
                return selected.some(val => fv.includes(val));
            }
        }) ?? false;
    };

    const nextStep = () => {
        const nextStepIndex = currentStep + 1;
        if (nextStepIndex >= totalSteps) return;


        // Estamos saliendo de una sección de preguntas (pasos 1 a sections.length)
        const sectionIndex = currentStep - 1; // paso 1 = sección 0
        if (sectionIndex >= 0 && sectionIndex < sections.length) {
            if (sectionHasFailure(sectionIndex)) {
                setPendingNextStep(nextStepIndex);
                setShowFailureModal(true);
                return;
            }
        }

        // Guardar progreso automáticamente
        if (!isEmulation) {
            router.post(
                route(saveRoute, { id: checklist.id, inspection: inspection.id }),
                {
                    answers: form.data.answers,
                    notes: form.data.notes,
                    visit_id: visitId,
                    asset_id: selectedAssetId,
                },
                { preserveScroll: true, preserveState: true }
            );
        }

        setCurrentStep(nextStepIndex);
        window.scrollTo(0, 0);
    };

    /**
     * Auto-llena todas las preguntas restantes con leyenda de auditoría
     * y salta directo al paso final (firma).
     */
    const handleRejectAndFinish = () => {
        const AUDIT_VALUE = 'No evaluado por falla crítica';
        const currentSectionIndex = currentStep - 1;
        const newAnswers = { ...form.data.answers };

        // Completar preguntas de secciones POSTERIORES a la actual
        sections.forEach((sec, secIdx) => {
            if (secIdx > currentSectionIndex) {
                sec.questions?.forEach(q => {
                    if (newAnswers[q.id] === undefined || newAnswers[q.id] === null || newAnswers[q.id] === '') {
                        newAnswers[q.id] = AUDIT_VALUE;
                    }
                });
            }
        });

        form.setData('answers', newAnswers);
        setShowFailureModal(false);
        setPendingNextStep(null);
        // Saltar al último paso (firma)
        setCurrentStep(totalSteps - 1);
        window.scrollTo(0, 0);
    };

    const handleContinueInspection = () => {
        setShowFailureModal(false);
        if (pendingNextStep !== null) {
            setCurrentStep(pendingNextStep);
            setPendingNextStep(null);
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
                    <div className="flex items-center gap-4 relative">
                        <Link 
                            href={['gestor', 'super_admin', 'administrador', 'admin'].includes(user.rol) 
                                ? route('gestor.checklists.index') 
                                : route('tecnico.checklists.tasks.index')
                            } 
                            className="absolute left-0 w-10 h-10 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center text-white/40 hover:text-white transition-all z-10"
                        >
                            <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="3" d="M15 19l-7-7 7-7" />
                            </svg>
                        </Link>
                        <div className="text-center flex-1 space-y-3">
                            <h1 className="text-xl font-black italic uppercase tracking-tight text-white/90 pl-12 pr-12">{checklist.title}</h1>
                            <div className="flex flex-wrap items-center justify-center gap-2">
                                <div className="inline-flex items-center gap-1.5 px-3 py-1.5 bg-orange-500/5 border border-orange-500/20 rounded-full text-[10px] font-black uppercase tracking-widest italic text-orange-500 shadow-[0_0_15px_rgba(246,146,57,0.1)]">
                                    <span className="animate-pulse">⌚</span> Tiempo Real (Sync Servidor)
                                </div>
                                {(() => {
                                    const selectedAsset = (assets || []).find(a => a.id === selectedAssetId);
                                    if (selectedAsset) {
                                        return (
                                            <div className="inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-500/10 border border-indigo-500/20 rounded-full text-[10px] font-black uppercase tracking-widest italic text-indigo-400 shadow-[0_0_15px_rgba(99,102,241,0.15)] animate-fade-in">
                                                <span>🔗</span> Activo: {selectedAsset.name} ({selectedAsset.identifier})
                                            </div>
                                        );
                                    }
                                    if (assets && assets.length > 0) {
                                        return (
                                            <div className="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-500/5 border border-amber-500/20 rounded-full text-[10px] font-black uppercase tracking-widest italic text-amber-500 shadow-[0_0_15px_rgba(245,158,11,0.1)] animate-fade-in">
                                                <span>📦</span> Trazabilidad de Activos
                                            </div>
                                        );
                                    }
                                    return null;
                                })()}
                            </div>
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
                                        <div className="text-xl font-black italic text-white">{totalQuestions}</div>
                                    </div>
                                </div>

                                {assets && assets.length > 0 && (
                                    <div className="pt-4 max-w-lg mx-auto text-left space-y-3 bg-black/30 p-6 rounded-3xl border border-white/10">
                                        <div className="flex items-center justify-between">
                                            <label className="text-[10px] font-black text-orange-400 uppercase tracking-widest italic">
                                                Seleccionar Activo Objeto de Inspección *
                                            </label>
                                            <span className="text-[9px] font-bold text-white/30 italic">Paso 0 - Trazabilidad</span>
                                        </div>

                                        {(sreBlockError || form.errors.asset_id) && (
                                            <div className="flex items-start gap-3 bg-red-500/10 border border-red-500/30 rounded-2xl p-4 animate-fade-in">
                                                <svg className="w-5 h-5 text-red-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                                <p className="text-[10px] font-black text-red-400 uppercase tracking-widest italic">{sreBlockError || form.errors.asset_id}</p>
                                            </div>
                                        )}
                                        <input
                                            type="text"
                                            value={assetSearch}
                                            onChange={e => setAssetSearch(e.target.value)}
                                            placeholder="Buscar por nombre o QR / Patente..."
                                            className="w-full bg-black/50 border border-white/10 rounded-xl px-4 py-2.5 text-xs text-white placeholder-white/20 focus:outline-none focus:border-orange-500"
                                        />
                                        <div className="max-h-48 overflow-y-auto space-y-2 pr-1">
                                            {filteredAssets.length === 0 ? (
                                                <p className="text-xs text-white/30 italic py-2 text-center">No hay activos que coincidan con la búsqueda.</p>
                                            ) : (
                                                filteredAssets.map(ast => {
                                                    const isInspected = (inspectedAssetIds || []).includes(ast.id);
                                                    return (
                                                        <div
                                                            key={ast.id}
                                                            onClick={() => handleSelectAsset(ast.id)}
                                                            className={`p-3 rounded-xl border cursor-pointer flex items-center justify-between transition-all ${
                                                                selectedAssetId === ast.id
                                                                    ? 'bg-orange-500/10 border-orange-500 text-white shadow-lg'
                                                                    : 'bg-white/5 border-white/5 text-white/70 hover:bg-white/10'
                                                            }`}
                                                        >
                                                            <div>
                                                                <div className="font-bold text-xs uppercase italic flex items-center gap-2">
                                                                    {ast.name}
                                                                    {isInspected && (
                                                                        <span className="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded bg-emerald-500/20 border border-emerald-500/30 text-emerald-400 text-[8px] font-black uppercase tracking-wider">
                                                                            ✓ Auditado
                                                                        </span>
                                                                    )}
                                                                </div>
                                                                <div className="text-[10px] font-mono text-orange-400">QR: {ast.identifier}</div>
                                                            </div>
                                                            <span className={`px-2 py-0.5 rounded text-[9px] font-black uppercase ${
                                                                ast.status === 'operativo' ? 'bg-emerald-500/20 text-emerald-400' : (ast.status === 'alerta' ? 'bg-amber-500/20 text-amber-400' : 'bg-red-500/20 text-red-400')
                                                            }`}>
                                                                {ast.status}
                                                            </span>
                                                        </div>
                                                    );
                                                })
                                            )}
                                        </div>
                                    </div>
                                )}
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
                                                <input id={`answer_${q.id}`} name={`answer_${q.id}`} type="text" value={form.data.answers[q.id] || ''} onChange={e => handleAnswerChange(q.id, e.target.value)} className="w-full bg-black/40 border-b-2 border-white/10 text-white focus:border-orange-500 outline-none py-3 text-lg font-black uppercase transition-all"/>
                                            )}

                                            {q.type === 'number' && (
                                                <div className="flex items-center gap-4">
                                                    <input id={`answer_${q.id}`} name={`answer_${q.id}`} type="number" value={form.data.answers[q.id] || ''} onChange={e => handleAnswerChange(q.id, e.target.value)} className="w-48 bg-black/40 border-b-2 border-white/10 text-white focus:border-orange-500 outline-none py-3 text-2xl font-black transition-all"/>
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
                                                    <input id={`answer_${q.id}`} name={`answer_${q.id}`} 
                                                        type="date" 
                                                        value={form.data.answers[q.id] || ''} 
                                                        onChange={e => handleAnswerChange(q.id, e.target.value)} 
                                                        className="bg-black/40 border border-white/10 text-white focus:border-orange-500 focus:ring-1 focus:ring-orange-500 outline-none py-4 px-6 text-lg font-black transition-all rounded-[1.5rem] [color-scheme:dark] w-full max-w-xs"
                                                    />
                                                </div>
                                            )}

                                            {q.type === 'time' && (
                                                <div className="flex items-center gap-4">
                                                    <input id={`answer_${q.id}`} name={`answer_${q.id}`} 
                                                        type="time" 
                                                        value={form.data.answers[q.id] || ''} 
                                                        onChange={e => handleAnswerChange(q.id, e.target.value)} 
                                                        className="bg-black/40 border border-white/10 text-white focus:border-orange-500 focus:ring-1 focus:ring-orange-500 outline-none py-4 px-6 text-lg font-black transition-all rounded-[1.5rem] [color-scheme:dark] w-full max-w-xs"
                                                    />
                                                    <button 
                                                        type="button" 
                                                        onClick={() => {
                                                            const now = new Date();
                                                            const timeStr = now.toTimeString().split(' ')[0].substring(0, 5);
                                                            handleAnswerChange(q.id, timeStr);
                                                        }}
                                                        className="px-6 py-4 bg-orange-600/10 border border-orange-600/20 text-orange-500 rounded-[1.5rem] text-[10px] font-black uppercase tracking-wider italic hover:bg-orange-600 hover:text-white transition-all active:scale-95"
                                                    >
                                                        🕒 Hora Actual
                                                    </button>
                                                </div>
                                            )}

                                            {q.type === 'datetime' && (
                                                <div className="flex items-center gap-4">
                                                    <input id={`answer_${q.id}`} name={`answer_${q.id}`} 
                                                        type="datetime-local" 
                                                        value={form.data.answers[q.id] || ''} 
                                                        onChange={e => handleAnswerChange(q.id, e.target.value)} 
                                                        className="bg-black/40 border border-white/10 text-white focus:border-orange-500 focus:ring-1 focus:ring-orange-500 outline-none py-4 px-6 text-lg font-black transition-all rounded-[1.5rem] [color-scheme:dark] w-full max-w-xs"
                                                    />
                                                    <button 
                                                        type="button" 
                                                        onClick={() => {
                                                            const now = new Date();
                                                            const tzOffset = now.getTimezoneOffset() * 60000;
                                                            const localISOTime = (new Date(now.getTime() - tzOffset)).toISOString().slice(0, 16);
                                                            handleAnswerChange(q.id, localISOTime);
                                                        }}
                                                        className="px-6 py-4 bg-orange-600/10 border border-orange-600/20 text-orange-500 rounded-[1.5rem] text-[10px] font-black uppercase tracking-wider italic hover:bg-orange-600 hover:text-white transition-all active:scale-95"
                                                    >
                                                        📅 Ahora
                                                    </button>
                                                </div>
                                            )}

                                            {q.type === 'textarea' && (
                                                <textarea id={`answer_${q.id}`} name={`answer_${q.id}`} 
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

            {/* ══════════════════════════════════════════════════
                MODAL DE INTERCEPCIÓN POR FALLA CRÍTICA
                Spec: promtSre.md — Sección 2 & 3
                ══════════════════════════════════════════════════ */}
            {showFailureModal && (
                <div className="fixed inset-0 z-50 flex items-center justify-center p-6 animate-fade-in">
                    {/* Backdrop oscuro */}
                    <div className="absolute inset-0 bg-[#0a0a15]/80 backdrop-blur-xl" />

                    {/* Panel glassmorphism */}
                    <div className="relative w-full max-w-lg bg-[#1a1a2e]/80 backdrop-blur-xl border border-white/10 rounded-3xl shadow-2xl overflow-hidden">
                        {/* Línea roja superior */}
                        <div className="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-transparent via-red-500 to-transparent" />

                        <div className="p-10 space-y-8 text-center">
                            {/* Icono de alerta animado */}
                            <div className="flex justify-center">
                                <div className="relative h-20 w-20 flex items-center justify-center">
                                    <div className="absolute inset-0 rounded-full border-2 border-red-500/20 border-t-red-500 animate-spin" />
                                    <div className="h-14 w-14 rounded-2xl bg-red-500/10 border border-red-500/30 flex items-center justify-center text-red-400 shadow-inner rotate-3">
                                        <svg className="w-7 h-7 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="2">
                                            <path strokeLinecap="round" strokeLinejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                        </svg>
                                    </div>
                                    <div className="absolute -top-1 -right-1">
                                        <span className="flex h-3 w-3">
                                            <span className="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-500 opacity-75" />
                                            <span className="relative inline-flex rounded-full h-3 w-3 bg-red-500" />
                                        </span>
                                    </div>
                                </div>
                            </div>

                            {/* Título */}
                            <div className="space-y-3">
                                <h2 className="text-xs font-black text-red-400 uppercase tracking-[0.4em] italic"
                                    style={{ textShadow: '0 0 20px rgba(239,68,68,0.5)' }}>
                                    ⚠️ Desvío Detectado
                                </h2>
                                <p className="text-[10px] font-black text-indigo-100/40 uppercase tracking-[0.2em] italic leading-relaxed">
                                    Se ha registrado una falla crítica en esta sección.<br />
                                    ¿Desea suspender y rechazar el activo ahora,<br />o continuar evaluando el resto de los puntos?
                                </p>
                            </div>

                            {/* Barra de escaneo HUD */}
                            <div className="h-px w-full bg-red-500/20 rounded-full overflow-hidden relative">
                                <div className="absolute inset-0 bg-gradient-to-r from-transparent via-red-500/40 to-transparent w-1/2 animate-scan" />
                            </div>

                            {/* Botones de acción */}
                            <div className="flex flex-col gap-4 pt-2">
                                {/* Botón Rechazar — Tactical Pill rojo */}
                                <button
                                    type="button"
                                    onClick={handleRejectAndFinish}
                                    className="w-full px-6 py-4 bg-red-500/20 text-red-400 border border-red-500/50 rounded-2xl text-[10px] font-black uppercase tracking-widest italic transition-all active:scale-95 animate-pulse hover:animate-none hover:bg-red-500 hover:text-white relative overflow-hidden group"
                                    style={{ boxShadow: '0 0 15px rgba(239,68,68,0.3)' }}
                                >
                                    <div className="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000" />
                                    🚫 Rechazar y Finalizar
                                </button>

                                {/* Botón Continuar — neutro */}
                                <button
                                    type="button"
                                    onClick={handleContinueInspection}
                                    className="w-full px-6 py-4 bg-white/5 text-white hover:bg-white/10 rounded-2xl text-[10px] font-black uppercase tracking-widest italic transition-all active:scale-95"
                                >
                                    Continuar Inspección
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            )}

            {showReinspectModal && (
                <div className="fixed inset-0 z-50 flex items-center justify-center p-6 animate-fade-in">
                    {/* Backdrop oscuro */}
                    <div className="absolute inset-0 bg-[#0a0a15]/80 backdrop-blur-xl" />

                    {/* Panel glassmorphism */}
                    <div className="relative w-full max-w-lg bg-[#1a1a2e]/80 backdrop-blur-xl border border-white/10 rounded-3xl shadow-2xl overflow-hidden">
                        {/* Línea amarilla superior */}
                        <div className="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-transparent via-amber-500 to-transparent" />

                        <div className="p-10 space-y-8 text-center">
                            {/* Icono de advertencia animado */}
                            <div className="flex justify-center">
                                <div className="relative h-20 w-20 flex items-center justify-center">
                                    <div className="absolute inset-0 rounded-full border-2 border-amber-500/20 border-t-amber-500 animate-spin" />
                                    <div className="h-14 w-14 rounded-2xl bg-amber-500/10 border border-amber-500/30 flex items-center justify-center text-amber-400 shadow-inner rotate-3">
                                        <svg className="w-7 h-7 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="2">
                                            <path strokeLinecap="round" strokeLinejoin="round" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <div className="absolute -top-1 -right-1">
                                        <span className="flex h-3 w-3">
                                            <span className="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-500 opacity-75" />
                                            <span className="relative inline-flex rounded-full h-3 w-3 bg-amber-500" />
                                        </span>
                                    </div>
                                </div>
                            </div>

                            {/* Título */}
                            <div className="space-y-3">
                                <h2 className="text-xs font-black text-amber-400 uppercase tracking-[0.4em] italic"
                                    style={{ textShadow: '0 0 20px rgba(245,158,11,0.5)' }}>
                                    ⚠️ Activo ya Auditado
                                </h2>
                                <p className="text-[10px] font-black text-indigo-100/40 uppercase tracking-[0.2em] italic leading-relaxed">
                                    Este activo ya fue auditado en la visita actual.<br />
                                    Si continúas, generarás un nuevo reporte de inspección independiente para este equipo.<br /><br />
                                    ¿Deseas volver a auditarlo?
                                </p>
                            </div>

                            {/* Barra de escaneo HUD */}
                            <div className="h-px w-full bg-amber-500/20 rounded-full overflow-hidden relative">
                                <div className="absolute inset-0 bg-gradient-to-r from-transparent via-amber-500/40 to-transparent w-1/2 animate-scan" />
                            </div>

                            {/* Botones de acción */}
                            <div className="flex flex-col gap-4 pt-2">
                                {/* Botón Volver a auditar */}
                                <button
                                    type="button"
                                    onClick={() => {
                                        executeSelectAsset(pendingAssetId);
                                        setShowReinspectModal(false);
                                        setPendingAssetId(null);
                                    }}
                                    className="w-full px-6 py-4 bg-amber-500/20 text-amber-400 border border-amber-500/50 rounded-2xl text-[10px] font-black uppercase tracking-widest italic transition-all active:scale-95 hover:bg-amber-500 hover:text-white relative overflow-hidden group"
                                    style={{ boxShadow: '0 0 15px rgba(245,158,11,0.3)' }}
                                >
                                    <div className="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000" />
                                    ✓ Sí, volver a auditar
                                </button>

                                {/* Botón Cancelar */}
                                <button
                                    type="button"
                                    onClick={() => {
                                        setShowReinspectModal(false);
                                        setPendingAssetId(null);
                                    }}
                                    className="w-full px-6 py-4 bg-white/5 text-white hover:bg-white/10 rounded-2xl text-[10px] font-black uppercase tracking-widest italic transition-all active:scale-95"
                                >
                                    Cancelar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            )}
        </AuthenticatedLayout>
    );
}
