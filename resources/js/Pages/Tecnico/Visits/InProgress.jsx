import { router, Head, useForm, Link } from '@inertiajs/react';
import AuthenticatedLayout from '../../../Layouts/AuthenticatedLayout';
import { useState, useEffect, useRef, useMemo } from 'react';
import SignaturePad from 'signature_pad';
import ActionSelector from './ActionSelector';
import CompletedProtocolsCard from './Partials/CompletedProtocolsCard';
import FinalizeVisitModal from './Partials/FinalizeVisitModal';

export default function InProgress({ visit, user, suggestedPlanTasks = [], pendingAssignments = [], pendingTrainings = [], checklistTemplates = [] }) {
    const [minutes, setMinutes] = useState(0);
    const [showFinalizeModal, setShowFinalizeModal] = useState(false);
    const [dismissedAlert, setDismissedAlert] = useState(false);
    const [showActionSelector, setShowActionSelector] = useState(false);

    const completedTasks = useMemo(() => {
        const list = [];

        visit.checklist_completions?.forEach(completion => {
            list.push({
                id: `completion-${completion.id}`,
                title: completion.assignment?.checklist?.title || 'Checklist',
                type: 'inspection',
                date: completion.completed_at ? new Date(completion.completed_at) : (completion.created_at ? new Date(completion.created_at) : new Date()),
                failures: completion.failure_analysis || { failed: 0 },
                detail: completion
            });
        });

        visit.trainings?.forEach(vt => {
            if (vt.training?.status === 'completed' || vt.training?.status === 'finalizado' || vt.is_adhoc || vt.training_id === null) {
                list.push({
                    id: `training-${vt.id}`,
                    title: vt.title || vt.training?.title || 'Capacitación',
                    type: 'training',
                    date: vt.created_at ? new Date(vt.created_at) : new Date(),
                    detail: vt
                });
            }
        });

        return list.sort((a, b) => b.date - a.date);
    }, [visit]);

    useEffect(() => {
        const start = new Date(visit.start_time);
        const updateTimer = () => {
            const now = new Date();
            const diff = Math.floor((now - start) / 1000 / 60);
            setMinutes(diff);
        };

        updateTimer();
        const interval = setInterval(updateTimer, 60000);
        return () => clearInterval(interval);
    }, [visit.start_time]);

    const alertState = useMemo(() => {
        if (dismissedAlert) return null;

        if (!visit.sede?.hora_cierre || visit.sede.es_roster_24_7) {
            if (minutes >= 240) {
                return {
                    type: 'duration_exceeded',
                    title: 'Jornada Prolongada',
                    message: `La visita actual excede las 4 horas de duración (${Math.floor(minutes / 60)}H ${minutes % 60}MIN).`,
                    isRed: true
                };
            }
            return null;
        }

        const closingTimeStr = visit.sede.hora_cierre;
        const parts = closingTimeStr.split(':');
        if (parts.length < 2) return null;

        const now = new Date();
        const closingDate = new Date();
        closingDate.setHours(parseInt(parts[0], 10), parseInt(parts[1], 10), 0, 0);

        const diffMs = closingDate - now;
        const diffMinutes = Math.floor(diffMs / 1000 / 60);

        if (diffMinutes <= 0) {
            return {
                type: 'cierre_excedido',
                title: 'Jornada Excedida (Sede Cerrada)',
                message: `La sede cerró a las ${parts[0]}:${parts[1]} HS. Operación en curso fuera de horario.`,
                isRed: true
            };
        }

        if (diffMinutes <= 60) {
            return {
                type: 'cierre_proximo',
                title: 'Cierre de Sede Próximo',
                message: `La sede cierra en ${diffMinutes} minutos (a las ${parts[0]}:${parts[1]} HS).`,
                isRed: false
            };
        }

        return null;
    }, [visit.sede, minutes, dismissedAlert]);

    const finalizeForm = useForm({
        notes: '',
        responsible_name: visit.sede?.responsable_1_nombre || '',
        signature: '',
        signature_unavailable: false,
        signature_skip_reason: '',
        email_responsable: visit.sede?.responsable_1_email || '',
    });

    const signatureRef = useRef(null);
    const signaturePad = useRef(null);

    useEffect(() => {
        if (showFinalizeModal && signatureRef.current && !finalizeForm.data.signature_unavailable) {
            signaturePad.current = new SignaturePad(signatureRef.current, {
                backgroundColor: 'rgba(255, 255, 255, 0.05)',
                penColor: '#F69239',
                minWidth: 2,
                maxWidth: 4
            });
        }
    }, [showFinalizeModal, finalizeForm.data.signature_unavailable]);

    const clearSignature = () => {
        if (signaturePad.current) {
            signaturePad.current.clear();
        }
    };

    const handleFinalize = async (e) => {
        e.preventDefault();

        let checkout_lat = null;
        let checkout_lng = null;

        if (typeof window !== 'undefined' && navigator.geolocation) {
            try {
                const position = await new Promise((resolve, reject) => {
                    navigator.geolocation.getCurrentPosition(resolve, reject, {
                        enableHighAccuracy: true,
                        timeout: 4000,
                        maximumAge: 0,
                    });
                });
                checkout_lat = position.coords.latitude;
                checkout_lng = position.coords.longitude;
            } catch (err) {
                console.warn('Geolocation capture for checkout failed/bypassed:', err);
            }
        }

        finalizeForm.transform((data) => ({
            ...data,
            checkout_lat,
            checkout_lng,
            signature: !data.signature_unavailable && signaturePad.current
                ? signaturePad.current.toDataURL()
                : '',
        }));

        finalizeForm.post(route('tecnico.visits.finalize', visit.id), {
            onSuccess: () => setShowFinalizeModal(false),
            preserveScroll: true
        });
    };

    return (
        <AuthenticatedLayout title="Bóveda Operativa (MODO ACCIÓN)">
            <Head title={`VISITA: ${visit.sede?.nombre_sede}`} />

            <div className="min-h-screen bg-[#11111d] text-white selection:bg-orange-500/30">
                <div className="h-16 bg-[#16162a] border-b border-white/5 flex items-center px-4 sm:px-8 justify-between sticky top-0 z-30 shadow-2xl">
                    <div className="flex items-center gap-4">
                        <div className="flex flex-col">
                            <h1 className="text-[10px] font-black text-orange-500 uppercase tracking-[0.3em] italic leading-none mb-1">
                                Visita en <span className="text-white">Operación</span>
                            </h1>
                            <h2 className="text-sm font-bold text-indigo-100 uppercase tracking-tight truncate max-w-[200px] sm:max-w-md">
                                {visit.sede?.nombre_sede}
                            </h2>
                        </div>
                    </div>
                    <div className="flex items-center gap-4">
                        <div className="flex items-center gap-2 px-3 py-1.5 rounded-full border bg-black/30 backdrop-blur-md">
                            {visit.gps_verified === true ? (
                                <>
                                    <span className="h-2.5 w-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                    <span className="text-[9px] font-black text-emerald-400 tracking-wider uppercase italic">GPS Verificado</span>
                                </>
                            ) : visit.gps_verified === false ? (
                                <>
                                    <span className="h-2.5 w-2.5 rounded-full bg-red-500 animate-pulse"></span>
                                    <span className="text-[9px] font-black text-red-400 tracking-wider uppercase italic">GPS Fuera de Rango</span>
                                </>
                            ) : (
                                <>
                                    <span className="h-2.5 w-2.5 rounded-full bg-gray-500"></span>
                                    <span className="text-[9px] font-black text-gray-400 tracking-wider uppercase italic">GPS Pendiente</span>
                                </>
                            )}
                        </div>

                        <div className="hidden sm:flex flex-col items-end">
                            <span className="text-[9px] font-black text-white/20 uppercase tracking-widest italic leading-none mb-1">Efector Asignado</span>
                            <span className="text-[11px] font-black text-indigo-100 uppercase italic leading-none">{user.name}</span>
                        </div>
                    </div>
                </div>

                <div className="px-4 sm:px-6 lg:px-8 py-8">
                    <div className="max-w-[95rem] mx-auto space-y-10 animate-fade-in">
                        {alertState && (
                            <div className={`${
                                alertState.isRed
                                    ? 'bg-red-500/10 border-red-500/20 shadow-red-500/5'
                                    : 'bg-amber-500/10 border-amber-500/20 shadow-amber-500/5'
                            } border p-6 rounded-[2rem] shadow-2xl flex items-center justify-between group animate-fade-in-down`}>
                                <div className="flex items-center gap-6">
                                    <div className={`h-12 w-12 rounded-2xl border flex items-center justify-center animate-pulse ${
                                        alertState.isRed
                                            ? 'bg-red-500/20 border-red-500/30 text-red-500'
                                            : 'bg-amber-500/20 border-amber-500/30 text-amber-500'
                                    }`}>
                                        <svg className="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p className={`text-xs font-black uppercase tracking-[0.2em] italic mb-1 ${
                                            alertState.isRed ? 'text-red-500' : 'text-amber-500'
                                        }`}>
                                            {alertState.title}
                                        </p>
                                        <p className="text-indigo-100/60 text-[10px] font-black uppercase italic tracking-widest leading-relaxed">
                                            {alertState.message}
                                        </p>
                                    </div>
                                </div>
                                <button
                                    onClick={() => setDismissedAlert(true)}
                                    className="px-6 py-2 bg-white/5 hover:bg-white/10 text-white/40 hover:text-white rounded-xl text-[9px] font-black uppercase tracking-widest italic border border-white/5 transition-all"
                                >
                                    Ignorar
                                </button>
                            </div>
                        )}

                        <div className="grid grid-cols-1 lg:grid-cols-12 gap-10 items-start">
                            <div className="lg:col-span-7 space-y-10">
                                <div className="bg-[#16162a]/40 backdrop-blur-md rounded-[2.5rem] border border-white/5 overflow-hidden shadow-2xl relative">
                                    <div className="p-10 flex flex-col items-center justify-center text-center relative z-10 space-y-8">
                                        <div className="h-16 w-16 rounded-2xl bg-orange-500/10 border border-orange-500/20 text-orange-500 flex items-center justify-center rotate-3 shadow-inner hover:rotate-0 transition-all duration-500">
                                            <svg className="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="2">
                                                <path strokeLinecap="round" strokeLinejoin="round" d="M12 4v16m8-8H4" />
                                            </svg>
                                        </div>

                                        <div className="space-y-3">
                                            <span className="text-[10px] font-black text-indigo-100/40 uppercase tracking-[0.2em] italic">Consola de Operaciones</span>
                                            <h2 className="text-2xl font-black text-white uppercase italic tracking-tight leading-none">Nueva Tarea en Campo</h2>
                                            <p className="text-xs text-indigo-100/60 max-w-sm italic">Inicia una inspección de control, capacitación express o registra novedades en esta sede.</p>
                                        </div>

                                        <button
                                            onClick={() => setShowActionSelector(true)}
                                            className="px-10 py-4 bg-orange-500 text-white rounded-2xl text-xs font-black uppercase italic tracking-widest hover:brightness-110 active:scale-95 transition-all shadow-[0_0_25px_rgba(246,146,57,0.35)] border border-orange-400/20 relative overflow-hidden group"
                                        >
                                            <div className="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></div>
                                            NUEVA TAREA
                                        </button>
                                    </div>
                                </div>

                                <CompletedProtocolsCard completedTasks={completedTasks} />
                            </div>

                            <div className="lg:col-span-5 space-y-10">
                                <div className="bg-[#16162a]/40 backdrop-blur-md rounded-[2.5rem] border border-white/5 overflow-hidden shadow-2xl relative">
                                    <div className="px-8 py-6 bg-black/20 border-b border-white/5 flex items-center gap-4 relative z-10">
                                        <div className="h-10 w-10 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-500 flex items-center justify-center shadow-inner">
                                            <svg className="w-6 h-6 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" strokeWidth="2.5" /></svg>
                                        </div>
                                        <h2 className="font-black text-white uppercase tracking-[0.2em] text-[11px] italic">Control de <span className="text-emerald-500">Jornada</span></h2>
                                    </div>
                                    <div className="p-8 space-y-8 relative z-10 text-center">
                                        <div className="flex flex-col items-center">
                                            <span className="text-[9px] font-black text-white/20 uppercase tracking-[0.4em] mb-2 italic">Tiempo en Operación</span>
                                            <div className="flex items-center gap-4">
                                                <span className="text-5xl font-black text-white italic tracking-tighter tabular-nums drop-shadow-[0_0_15px_rgba(255,255,255,0.1)]">
                                                    {Math.floor(minutes / 60)}h {minutes % 60}m
                                                </span>
                                                <div className="h-3 w-3 rounded-full bg-emerald-500 animate-ping"></div>
                                            </div>
                                        </div>

                                        <div className="grid grid-cols-2 gap-4">
                                            <Link href={route('tecnico.visits.index')} className="flex flex-col items-center justify-center gap-3 p-6 bg-white/5 border border-white/5 rounded-3xl hover:bg-white/10 hover:border-white/20 transition-all group">
                                                <div className="h-10 w-10 bg-white/5 border border-white/10 rounded-xl flex items-center justify-center text-white/20 group-hover:text-orange-500 transition-colors">
                                                    <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M11 15l-3-3m0 0l3-3m-3 3h8M3 12a9 9 0 1118 0 9 9 0 01-18 0z" strokeWidth="2.5" /></svg>
                                                </div>
                                                <span className="text-[9px] font-black text-white/20 uppercase tracking-widest italic group-hover:text-white">Suspender</span>
                                            </Link>
                                            <button onClick={() => setShowFinalizeModal(true)} className="flex flex-col items-center justify-center gap-3 p-6 bg-emerald-500/10 border border-emerald-500/20 rounded-3xl hover:bg-emerald-500/20 hover:border-emerald-500/40 transition-all group">
                                                <div className="h-10 w-10 bg-emerald-500 rounded-xl flex items-center justify-center text-white shadow-lg group-hover:scale-110 transition-transform">
                                                    <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7" strokeWidth="3" /></svg>
                                                </div>
                                                <span className="text-[9px] font-black text-emerald-500 uppercase tracking-widest italic">Finalizar</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div className="grid grid-cols-2 gap-6 relative">
                                    <button onClick={() => router.get(route('tecnico.hallazgos.create'), { visit_id: visit.id })} className="group relative p-10 bg-[#16162a]/60 backdrop-blur-md rounded-[3rem] border border-white/5 hover:border-orange-500/40 transition-all duration-700 shadow-2xl flex flex-col items-center text-center">
                                        <div className="h-20 w-20 bg-orange-500/10 rounded-3xl border border-orange-500/20 text-orange-500 mb-6 flex items-center justify-center shadow-inner group-hover:scale-110 group-hover:rotate-6 transition-all duration-500">
                                            <svg className="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="1.5"><path strokeLinecap="round" strokeLinejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                                        </div>
                                        <span className="text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic mb-1 group-hover:text-white transition-colors uppercase">Registrar</span>
                                        <span className="text-xs font-black text-orange-500 uppercase tracking-[0.3em] italic">Hallazgo</span>
                                        {visit.hallazgos?.length > 0 && <span className="absolute -top-3 -right-3 h-[42px] w-[42px] flex items-center justify-center rounded-2xl bg-orange-500 text-white text-xl font-black shadow-[0_0_25px_rgba(246,146,57,0.5)] border-2 border-[#11111d] italic z-20 transition-all animate-bounce-subtle">{visit.hallazgos.length}</span>}
                                    </button>

                                    <button onClick={() => router.get(route('tecnico.incidentes.create'), { visit_id: visit.id })} className="group relative p-10 bg-[#16162a]/60 backdrop-blur-md rounded-[3rem] border border-white/5 hover:border-red-500/40 transition-all duration-700 shadow-2xl flex flex-col items-center text-center">
                                        <div className="h-20 w-20 bg-red-500/10 rounded-3xl border border-red-500/20 text-red-500 mb-6 flex items-center justify-center shadow-inner group-hover:scale-110 group-hover:-rotate-6 transition-all duration-500">
                                            <svg className="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="1.5"><path strokeLinecap="round" strokeLinejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                        </div>
                                        <span className="text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic mb-1 group-hover:text-white transition-colors uppercase">Reportar</span>
                                        <span className="text-xs font-black text-red-500 uppercase tracking-[0.3em] italic">Incidente</span>
                                        {visit.incidentes?.length > 0 && <span className="absolute -top-3 -right-3 h-[42px] w-[42px] flex items-center justify-center rounded-2xl bg-red-500 text-white text-xl font-black shadow-[0_0_25px_rgba(239,68,68,0.5)] border-2 border-[#11111d] italic z-20 transition-all animate-bounce-subtle">{visit.incidentes.length}</span>}
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div className="text-center py-12">
                            <p className="text-[10px] font-black text-white/5 uppercase tracking-[0.6em] italic leading-relaxed">XamaSafe Tactical Protocol • Access Level: Specialist</p>
                        </div>
                    </div>
                </div>

                <FinalizeVisitModal
                    isOpen={showFinalizeModal}
                    onClose={() => setShowFinalizeModal(false)}
                    finalizeForm={finalizeForm}
                    signatureRef={signatureRef}
                    onClearSignature={clearSignature}
                    onSubmit={handleFinalize}
                />

                <ActionSelector
                    isOpen={showActionSelector}
                    onClose={() => setShowActionSelector(false)}
                    visit={visit}
                    suggestedTasks={suggestedPlanTasks}
                    pendingAssignments={pendingAssignments}
                    pendingTrainings={pendingTrainings}
                    checklistTemplates={checklistTemplates}
                />
            </div>

            <style>{`
                .animate-fade-in { animation: fade-in 0.4s ease-out forwards; }
                .animate-fade-in-down { animation: fade-in-down 0.6s ease-out forwards; }
                .animate-pulse-slow { animation: pulse 4s cubic-bezier(0.4, 0, 0.6, 1) infinite; }
                .animate-bounce-subtle { animation: bounce-subtle 3s infinite; }

                @keyframes fade-in { from { opacity: 0; } to { opacity: 1; } }
                @keyframes fade-in-down { from { opacity: 0; transform: translateY(-30px); } to { opacity: 1; transform: translateY(0); } }
                @keyframes bounce-subtle { 0%, 100% { transform: translateY(-5%); } 50% { transform: translateY(0); } }

                [x-cloak] { display: none !important; }
            `}</style>
        </AuthenticatedLayout>
    );
}
