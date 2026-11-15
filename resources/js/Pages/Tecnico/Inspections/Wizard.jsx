import { router, Head, useForm, usePage, Link } from '@inertiajs/react';
import AuthenticatedLayout from '../../../Layouts/AuthenticatedLayout';
import { useState, useEffect, useRef } from 'react';
import SignaturePad from 'signature_pad';
import WizardIntroStep from './Partials/WizardIntroStep';
import QuestionRenderer, { toArray } from './Partials/QuestionRenderer';
import SignatureStep from './Partials/SignatureStep';
import { FailureInterceptModal, ReinspectAssetModal } from './Partials/WizardModals';

function inferFieldRole(fieldSchema) {
    if (!fieldSchema) return null;
    if (fieldSchema.role) return fieldSchema.role;
    const label = fieldSchema.label || '';
    if (fieldSchema.show_in_create || /identif|nro|n°|interno|codigo|patente|matricula/i.test(label)) return 'identificador';
    if (fieldSchema.type === 'date' && /venc|vto|vigenc|proxim|caduc/i.test(label)) return 'dato_con_regla';
    if (/tipo|modelo|marca|fabricante|capacidad/i.test(label)) return 'dato_estatico';
    return 'verificacion';
}

function resolveMetadataValue(selectedAsset, metadata, fieldKey) {
    let val = metadata[fieldKey];
    if (val === undefined || val === null || val === '') {
        if (selectedAsset[fieldKey] !== undefined && selectedAsset[fieldKey] !== null) {
            val = selectedAsset[fieldKey];
        } else if (fieldKey === 'identifier' || /identif|nro|n°|interno|codigo|patente|matricula/i.test(fieldKey)) {
            val = selectedAsset.identifier;
        }
    }
    return val;
}

export default function Wizard({ checklist, assignment = {}, inspection = {}, visitId = null, initialAnswers = {}, assets = [], isEmulation = false, inspectedAssetIds = [] }) {
    const { auth } = usePage().props;
    const user = auth.user;
    const [currentStep, setCurrentStep] = useState(0);
    const [showFailureModal, setShowFailureModal] = useState(false);
    const [pendingNextStep, setPendingNextStep] = useState(null);
    const [selectedAssetId, setSelectedAssetId] = useState(inspection.asset_id || '');
    const [assetSearch, setAssetSearch] = useState('');
    const [showReinspectModal, setShowReinspectModal] = useState(false);
    const [pendingAssetId, setPendingAssetId] = useState(null);
    const [sreBlockError, setSreBlockError] = useState(null);
    const [draftRestored, setDraftRestored] = useState(false);

    const draftKey = !isEmulation && inspection?.id ? `inspection_draft_${inspection.id}` : null;
    const sections = checklist.sections || [];
    const totalQuestions = sections.reduce((acc, sec) => acc + (sec.questions?.length || 0), 0);
    const totalSteps = sections.length + 2;

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

    // Restaurar borrador offline desde localStorage si existe
    useEffect(() => {
        if (!draftKey || typeof window === 'undefined') return;
        try {
            const raw = window.localStorage.getItem(draftKey);
            if (!raw) return;
            const draft = JSON.parse(raw);
            if (draft && (Object.keys(draft.answers || {}).length > 0 || draft.notes)) {
                form.setData(d => ({
                    ...d,
                    answers: { ...d.answers, ...(draft.answers || {}) },
                    notes: draft.notes || d.notes,
                    asset_id: draft.asset_id || d.asset_id,
                }));
                if (draft.asset_id && !selectedAssetId) setSelectedAssetId(draft.asset_id);
                setDraftRestored(true);
            }
        } catch {
            // Ignorar borrador corrupto
        }
    }, [draftKey]);

    // Guardar respaldo offline automático ante cada cambio
    useEffect(() => {
        if (!draftKey || typeof window === 'undefined') return;
        try {
            window.localStorage.setItem(draftKey, JSON.stringify({
                answers: form.data.answers,
                notes: form.data.notes,
                asset_id: selectedAssetId,
                updatedAt: Date.now(),
            }));
        } catch {
            // Ignorar errores de cuota de almacenamiento
        }
    }, [draftKey, form.data.answers, form.data.notes, selectedAssetId]);

    // Auto-prellenar respuestas de metadata al cargar (para borradores activos)
    useEffect(() => {
        if (!selectedAssetId || !assets?.length) return;
        const selectedAsset = assets.find(a => a.id === selectedAssetId);
        if (!selectedAsset) return;

        const metadata = selectedAsset.metadata || {};
        const schemaMap = Object.fromEntries((selectedAsset.asset_type?.metadata_schema || []).map(f => [f.name, f]));
        let hasChanges = false;
        const newAnswers = { ...form.data.answers };

        sections.forEach(sec => {
            (sec.questions || []).forEach(q => {
                if (!q.vincular_a_metadata || (newAnswers[q.id] !== undefined && newAnswers[q.id] !== null && newAnswers[q.id] !== '')) return;
                const role = inferFieldRole(schemaMap[q.vincular_a_metadata]);
                if (role && ['identificador', 'dato_estatico', 'dato_con_regla'].includes(role)) {
                    const val = resolveMetadataValue(selectedAsset, metadata, q.vincular_a_metadata);
                    if (val !== undefined && val !== null && val !== '') {
                        newAnswers[q.id] = val;
                        hasChanges = true;
                    }
                }
            });
        });

        if (hasChanges) form.setData(d => ({ ...d, answers: newAnswers }));
    }, [selectedAssetId, assets]);

    const filteredAssets = (assets || []).filter(a =>
        a.name.toLowerCase().includes(assetSearch.toLowerCase()) ||
        a.identifier.toLowerCase().includes(assetSearch.toLowerCase())
    );

    const handleSelectAsset = (assetId) => {
        const newValue = selectedAssetId === assetId ? '' : assetId;
        if (newValue) {
            const selectedAsset = (assets || []).find(a => a.id === assetId);
            if (selectedAsset?.status === 'fuera_de_servicio') {
                const isResolving = (selectedAsset.asset_type?.metadata_schema || []).some(field =>
                    (field.maintenance_rules || []).some(rule => String(rule.checklist_resolucion_id) === String(checklist.id))
                );
                if (!isResolving) {
                    setSreBlockError('ACCESO DENEGADO: El equipo se encuentra Fuera de Servicio por Mantenimiento Vencido.');
                    return;
                }
            }
        }
        setSreBlockError(null);
        if (newValue && (inspectedAssetIds || []).includes(assetId)) {
            setPendingAssetId(newValue);
            setShowReinspectModal(true);
            return;
        }
        executeSelectAsset(newValue);
    };

    const executeSelectAsset = (newValue) => {
        setSelectedAssetId(newValue);
        const updatedData = { ...form.data, asset_id: newValue };
        const newAnswers = { ...form.data.answers };
        let hasChanges = false;

        if (newValue) {
            const selectedAsset = (assets || []).find(a => a.id === newValue);
            const metadata = selectedAsset?.metadata || {};
            const schemaMap = Object.fromEntries((selectedAsset?.asset_type?.metadata_schema || []).map(f => [f.name, f]));

            sections.forEach(sec => {
                (sec.questions || []).forEach(q => {
                    if (!q.vincular_a_metadata) return;
                    const role = inferFieldRole(schemaMap[q.vincular_a_metadata]);
                    if (role && ['identificador', 'dato_estatico', 'dato_con_regla'].includes(role)) {
                        const val = resolveMetadataValue(selectedAsset, metadata, q.vincular_a_metadata);
                        newAnswers[q.id] = (val !== undefined && val !== null) ? val : '';
                    } else {
                        newAnswers[q.id] = '';
                    }
                    hasChanges = true;
                });
            });
        } else {
            sections.forEach(sec => (sec.questions || []).forEach(q => {
                if (q.vincular_a_metadata) {
                    newAnswers[q.id] = '';
                    hasChanges = true;
                }
            }));
        }

        if (hasChanges) updatedData.answers = newAnswers;
        form.setData(updatedData);
    };

    const supervisorSigRef = useRef(null);
    const supervisorPad = useRef(null);

    useEffect(() => {
        if (currentStep === totalSteps - 1 && supervisorSigRef.current) {
            supervisorPad.current = new SignaturePad(supervisorSigRef.current, {
                backgroundColor: 'rgba(255,255,255,0.05)',
                penColor: 'rgb(255, 255, 255)',
            });
        }
    }, [currentStep, totalSteps]);

    const handleAnswerChange = (questionId, value) => {
        form.setData('answers', { ...form.data.answers, [questionId]: value });
    };

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
            }
            const selected = Array.isArray(answer) ? answer : [answer];
            return selected.some(val => fv.includes(val));
        }) ?? false;
    };

    const nextStep = () => {
        const nextStepIndex = currentStep + 1;
        if (nextStepIndex >= totalSteps) return;
        const sectionIndex = currentStep - 1;
        if (sectionIndex >= 0 && sectionIndex < sections.length && sectionHasFailure(sectionIndex)) {
            setPendingNextStep(nextStepIndex);
            setShowFailureModal(true);
            return;
        }
        if (!isEmulation) {
            router.post(route(saveRoute, { id: checklist.id, inspection: inspection.id }), {
                answers: form.data.answers,
                notes: form.data.notes,
                visit_id: visitId,
                asset_id: selectedAssetId,
            }, { preserveScroll: true, preserveState: true });
        }
        setCurrentStep(nextStepIndex);
        window.scrollTo(0, 0);
    };

    const handleRejectAndFinish = () => {
        const AUDIT_VALUE = 'No evaluado por falla crítica';
        const currentSectionIndex = currentStep - 1;
        const newAnswers = { ...form.data.answers };
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
            router.get(route(isTecnico ? 'tecnico.checklists.tasks.index' : 'gestor.checklists.index'));
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
            router.post(route(completeRoute, { id: checklist.id, inspection: inspection.id }), {
                ...form.data,
                supervisor_signature: supervisorPad.current.toDataURL(),
                technician_signature: user.signature,
            }, {
                preserveScroll: false,
                onSuccess: () => {
                    if (draftKey && typeof window !== 'undefined') {
                        window.localStorage.removeItem(draftKey);
                    }
                },
            });
        }
    };

    const selectedAsset = (assets || []).find(a => a.id === selectedAssetId);

    return (
        <AuthenticatedLayout title="Asistente de Inspección Táctica">
            <Head title={`Inspección: ${checklist.title}`} />
            <div className="px-4 sm:px-6 lg:px-8 py-8 text-white selection:bg-orange-500/30">
                <div className="max-w-4xl mx-auto space-y-8 animate-fade-in-up">
                    <div className="flex items-center gap-4 relative">
                        <Link
                            href={['gestor', 'super_admin', 'administrador', 'admin'].includes(user.rol) ? route('gestor.checklists.index') : route('tecnico.checklists.tasks.index')}
                            className="absolute left-0 w-10 h-10 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center text-white/40 hover:text-white transition-all z-10"
                        >
                            <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="3" d="M15 19l-7-7 7-7" /></svg>
                        </Link>
                        <div className="text-center flex-1 space-y-3">
                            <h1 className="text-xl font-black italic uppercase tracking-tight text-white/90 pl-12 pr-12">{checklist.title}</h1>
                            <div className="flex flex-wrap items-center justify-center gap-2">
                                <div className="inline-flex items-center gap-1.5 px-3 py-1.5 bg-orange-500/5 border border-orange-500/20 rounded-full text-[10px] font-black uppercase tracking-widest italic text-orange-500 shadow-[0_0_15px_rgba(246,146,57,0.1)]">
                                    <span className="animate-pulse">⌚</span> Tiempo Real (Sync + Offline)
                                </div>
                                {draftRestored && (
                                    <div className="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-500/10 border border-emerald-500/20 rounded-full text-[10px] font-black uppercase tracking-widest italic text-emerald-400">
                                        💾 Borrador Local Restaurado
                                    </div>
                                )}
                                {selectedAsset && (
                                    <div className="inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-500/10 border border-indigo-500/20 rounded-full text-[10px] font-black uppercase tracking-widest italic text-indigo-400">
                                        <span>🔗</span> Activo: {selectedAsset.name} ({selectedAsset.identifier})
                                    </div>
                                )}
                            </div>
                        </div>
                    </div>

                    <div className="bg-[#16162a]/60 backdrop-blur-xl rounded-full border border-white/5 p-2 flex items-center gap-4 px-6 shadow-2xl">
                        <div className="flex-1 h-1.5 bg-white/5 rounded-full overflow-hidden">
                            <div className="h-full bg-orange-500 shadow-[0_0_15px_rgba(246,146,57,0.5)] transition-all duration-500" style={{ width: `${(currentStep / (totalSteps - 1)) * 100}%` }}></div>
                        </div>
                        <span className="text-[10px] font-black italic uppercase tracking-widest text-white/40">Fase {currentStep + 1} / {totalSteps}</span>
                    </div>

                    <div className="bg-[#16162a]/40 backdrop-blur-md rounded-[3rem] border border-white/5 shadow-3xl overflow-hidden relative">
                        <div className="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-transparent via-orange-500 to-transparent"></div>

                        {currentStep === 0 && (
                            <WizardIntroStep
                                checklist={checklist}
                                sections={sections}
                                totalQuestions={totalQuestions}
                                assets={assets}
                                filteredAssets={filteredAssets}
                                selectedAssetId={selectedAssetId}
                                assetSearch={assetSearch}
                                setAssetSearch={setAssetSearch}
                                inspectedAssetIds={inspectedAssetIds}
                                sreBlockError={sreBlockError}
                                formErrors={form.errors}
                                onSelectAsset={handleSelectAsset}
                                onStart={nextStep}
                            />
                        )}

                        {currentStep > 0 && currentStep <= sections.length && (
                            <QuestionRenderer
                                section={sections[currentStep - 1]}
                                answers={form.data.answers}
                                onAnswerChange={handleAnswerChange}
                            />
                        )}

                        {currentStep === totalSteps - 1 && (
                            <SignatureStep
                                form={form}
                                user={user}
                                supervisorSigRef={supervisorSigRef}
                                supervisorPad={supervisorPad}
                                onSubmit={submit}
                            />
                        )}
                    </div>

                    <div className="flex justify-between items-center px-6">
                        <button onClick={prevStep} disabled={currentStep === 0} className="px-10 py-5 bg-white/5 text-white/40 hover:text-white rounded-[2rem] text-[10px] font-black uppercase tracking-widest italic transition-all disabled:opacity-0">
                            Fase Anterior
                        </button>
                        {currentStep > 0 && currentStep < totalSteps - 1 && (
                            <button onClick={nextStep} className="px-12 py-5 bg-orange-600 text-white rounded-[2rem] text-[10px] font-black uppercase tracking-[0.2em] italic shadow-xl hover:brightness-110 active:scale-95 transition-all">
                                Siguiente Fase
                            </button>
                        )}
                    </div>
                </div>
            </div>

            <FailureInterceptModal show={showFailureModal} onRejectAndFinish={handleRejectAndFinish} onContinue={handleContinueInspection} />
            <ReinspectAssetModal
                show={showReinspectModal}
                onConfirm={() => { executeSelectAsset(pendingAssetId); setShowReinspectModal(false); setPendingAssetId(null); }}
                onCancel={() => { setShowReinspectModal(false); setPendingAssetId(null); }}
            />
        </AuthenticatedLayout>
    );
}
