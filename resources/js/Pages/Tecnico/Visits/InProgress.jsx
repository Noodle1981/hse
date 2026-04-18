import { router, Head, useForm, usePage, Link } from '@inertiajs/react';
import AuthenticatedLayout from '../../../Layouts/AuthenticatedLayout';
import { useState, useEffect, useRef } from 'react';
import SignaturePad from 'signature_pad';

export default function InProgress({ visit, user, suggestedPlanTasks = [], pendingAssignments = [], pendingTrainings = [], templates = [] }) {
    const { auth, flash } = usePage().props;
    
    // ESTADOS TÁCTICOS
    const [minutes, setMinutes] = useState(0);
    const [showFinalizeModal, setShowFinalizeModal] = useState(false);
    const [showAutoAssignModal, setShowAutoAssignModal] = useState(false);
    const [isExceeded, setIsExceeded] = useState(false);
    const [dismissedAlert, setDismissedAlert] = useState(false);

    // CRONÓMETRO DE OPERACIÓN
    useEffect(() => {
        const start = new Date(visit.start_time);
        const updateTimer = () => {
            const now = new Date();
            const diff = Math.floor((now - start) / 1000 / 60); // minutos
            setMinutes(diff);
            // Alerta a los 30 minutos (Threshold legacy)
            if (diff >= 30 && !dismissedAlert) {
                setIsExceeded(true);
            }
        };

        updateTimer();
        const interval = setInterval(updateTimer, 30000); // Cada 30 segs como en legacy
        return () => clearInterval(interval);
    }, [visit.start_time, dismissedAlert]);

    // FORMULARIO DE FINALIZACIÓN (MODAL CIERRE)
    const finalizeForm = useForm({
        notes: '',
        responsible_name: visit.sede?.responsable_1_nombre || '',
        signature: '',
        signature_unavailable: false,
        signature_skip_reason: '',
        email_responsable: visit.sede?.responsable_1_email || '',
    });

    // MANEJO DE FIRMA DIGITAL
    const signatureRef = useRef(null);
    const signaturePad = useRef(null);

    useEffect(() => {
        if (showFinalizeModal && signatureRef.current && !finalizeForm.data.signature_unavailable) {
            // Inicializar SignaturePad (Legacy styles: White trace over Dark)
            signaturePad.current = new SignaturePad(signatureRef.current, {
                backgroundColor: 'rgba(255, 255, 255, 0.05)',
                penColor: '#F69239', // XamaSafe Orange
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

    const handleFinalize = (e) => {
        e.preventDefault();
        if (!finalizeForm.data.signature_unavailable && signaturePad.current) {
            if (signaturePad.current.isEmpty()) {
                alert('SE REQUIERE CERTIFICACIÓN: Por favor, capture la firma del responsable.');
                return;
            }
            finalizeForm.setData('signature', signaturePad.current.toDataURL());
        }
        finalizeForm.post(route('tecnico.visits.finalize', visit.id));
    };

    // ASIGNACIÓN DE TAREAS SUGERIDAS (CLON LEGACY)
    const assignSuggested = (planId) => {
        router.post(route('tecnico.visits.assign-suggested', visit.id), { plan_id: planId }, {
            preserveScroll: true,
            onSuccess: () => {
                // Notificación táctica? (opcional)
            }
        });
    };

    return (
        <AuthenticatedLayout title="Bóveda Operativa (MODO ACCIÓN)">
            <Head title={`VISITA: ${visit.sede?.nombre_sede}`} />

            <div className="min-h-screen bg-[#11111d] text-white selection:bg-orange-500/30">
                
                {/* HEADER TÁCTICO LEGACY */}
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
                    <div className="flex items-center gap-6">
                        <div className="hidden sm:flex flex-col items-end">
                            <span className="text-[9px] font-black text-white/20 uppercase tracking-widest italic leading-none mb-1">Efector Asignado</span>
                            <span className="text-[11px] font-black text-indigo-100 uppercase italic leading-none">{user.name}</span>
                        </div>
                    </div>
                </div>

                <div className="px-4 sm:px-6 lg:px-8 py-8">
                    <div className="max-w-[95rem] mx-auto space-y-10 animate-fade-in">

                        {/* ALERTA DE TIEMPO LÍMITE (PORTADO DE LEGACY) */}
                        {isExceeded && (
                            <div className="bg-red-500/10 border border-red-500/20 p-6 rounded-[2rem] shadow-2xl flex items-center justify-between group animate-fade-in-down">
                                <div className="flex items-center gap-6">
                                    <div className="h-12 w-12 rounded-2xl bg-red-500/20 border border-red-500/30 text-red-500 flex items-center justify-center animate-pulse">
                                        <svg className="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    </div>
                                    <div>
                                        <p className="text-white text-xs font-black uppercase tracking-[0.2em] italic mb-1">Alerta de <span className="text-red-500">Tiempo Límite</span></p>
                                        <p className="text-indigo-100/60 text-[10px] font-black uppercase italic tracking-widest">
                                            Jornada excedida: {Math.floor(minutes / 60)}H {minutes % 60}MIN
                                        </p>
                                    </div>
                                </div>
                                <button onClick={() => { setIsExceeded(false); setDismissedAlert(true); }} className="px-6 py-2 bg-white/5 hover:bg-white/10 text-white/40 hover:text-white rounded-xl text-[9px] font-black uppercase tracking-widest italic border border-white/5 transition-all">Ignorar</button>
                            </div>
                        )}

                        {/* GRID DE ACCIÓN DE 12 COLUMNAS (LEGACY STYLE) */}
                        <div className="grid grid-cols-1 lg:grid-cols-12 gap-10 items-start">

                            {/* COLUMNA IZQUIERDA: MISIONES Y TAREAS (7 COLS) */}
                            <div className="lg:col-span-7 space-y-10">
                                
                                {/* PLANIFICACIÓN MENSUAL (MISIONES SUGERIDAS) */}
                                {suggestedPlanTasks.length > 0 && (
                                    <div className="bg-indigo-500/5 border border-indigo-500/20 rounded-[2.5rem] p-8 relative overflow-hidden group shadow-2xl">
                                        <div className="absolute -right-20 -top-20 w-64 h-64 bg-indigo-500/10 rounded-full blur-3xl group-hover:scale-125 transition-transform duration-1000"></div>
                                        
                                        <div className="flex items-center gap-6 mb-8 relative z-10">
                                            <div className="h-12 w-12 rounded-2xl bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 flex items-center justify-center shadow-lg rotate-3 group-hover:rotate-0 transition-transform">
                                                <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                            </div>
                                            <div>
                                                <h3 className="text-xs font-black text-indigo-400 uppercase tracking-[0.3em] italic mb-1">Planificación <span className="text-white">Mensual</span></h3>
                                                <p className="text-[10px] font-bold text-indigo-100/40 uppercase italic tracking-widest">Tareas proyectadas por auditoría para esta sede</p>
                                            </div>
                                        </div>

                                        <div className="grid grid-cols-1 sm:grid-cols-2 gap-4 relative z-10">
                                            {suggestedPlanTasks.map(plan => (
                                                <div key={plan.id} className="bg-black/40 border border-white/5 rounded-3xl p-5 hover:border-indigo-500/30 transition-all flex justify-between items-center group/item">
                                                    <div className="flex-1 pr-4">
                                                        <h4 className="text-[11px] font-black text-white uppercase italic tracking-tight group-hover/item:text-indigo-400 transition-colors leading-tight mb-1">{plan.activity_name}</h4>
                                                        <span className="text-[8px] font-black text-indigo-100/20 uppercase tracking-widest italic">{plan.category} • {plan.activity_code || 'S/N'}</span>
                                                    </div>
                                                    <button onClick={() => assignSuggested(plan.id)} className="h-9 px-4 bg-indigo-500/10 text-indigo-400 rounded-xl text-[9px] font-black uppercase tracking-widest border border-indigo-500/20 hover:bg-indigo-500 hover:text-white transition-all italic">Vincular</button>
                                                </div>
                                            ))}
                                        </div>
                                    </div>
                                )}

                                {/* CHECKLISTS PENDIENTES */}
                                <div className="bg-[#16162a]/40 backdrop-blur-md rounded-[2.5rem] border border-white/5 overflow-hidden shadow-2xl relative">
                                    <div className="px-8 py-6 bg-black/20 border-b border-white/5 flex justify-between items-center relative z-10">
                                        <div className="flex items-center gap-4">
                                            <div className="h-10 w-10 rounded-xl bg-orange-500/10 border border-orange-500/20 text-orange-500 flex items-center justify-center rotate-3">
                                                <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                            </div>
                                            <h2 className="font-black text-white uppercase tracking-[0.2em] text-[11px] italic">Protocolos <span className="text-orange-500">Pendientes</span></h2>
                                        </div>
                                        <button onClick={() => setShowAutoAssignModal(true)} className="text-[9px] font-black uppercase tracking-[0.2em] text-white/20 hover:text-orange-500 flex items-center gap-2 transition-all italic">
                                            <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
                                            Auto-Afectar
                                        </button>
                                    </div>

                                    <div className="p-8 space-y-5 relative z-10">
                                        {visit.checklist_completions?.length === 0 && pendingAssignments.length === 0 && (
                                            <div className="text-center py-12 border-2 border-dashed border-white/5 rounded-[2rem]">
                                                <p className="text-[10px] font-black text-white/10 uppercase tracking-[0.4em] italic leading-relaxed">No hay checklists vinculados actualmente</p>
                                            </div>
                                        )}
                                        
                                        {/* Listar Asignaciones Pendientes */}
                                        {pendingAssignments.map(assignment => (
                                            <div key={assignment.id} className="bg-black/40 border border-white/5 rounded-3xl p-6 flex justify-between items-center group/task hover:border-orange-500/30 transition-all">
                                                <div className="flex items-center gap-5">
                                                    <div className="h-10 w-10 rounded-xl bg-orange-500/10 border border-orange-500/20 text-orange-500 flex items-center justify-center group-hover/task:scale-110 transition-transform">
                                                        <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                                    </div>
                                                    <div>
                                                        <h4 className="text-sm font-black text-white uppercase italic tracking-tight mb-1">{assignment.checklist?.title}</h4>
                                                        <div className="flex items-center gap-2">
                                                            <span className="text-[9px] font-black text-orange-500 uppercase tracking-widest italic">{assignment.status === 'in_progress' ? 'EN CURSO' : 'PENDIENTE'}</span>
                                                            <span className="h-1 w-1 rounded-full bg-white/20"></span>
                                                            <span className="text-[9px] font-black text-indigo-100/30 uppercase tracking-widest italic">REF: #{assignment.id}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <Link 
                                                    href={route('tecnico.checklists.fill', { id: assignment.dynamic_checklist_id, assignment: assignment.id, visit_id: visit.id })}
                                                    className="h-10 px-6 bg-orange-500 text-white rounded-xl text-[9px] font-black uppercase tracking-[0.2em] italic shadow-lg shadow-orange-900/20 hover:brightness-110 transition-all flex items-center"
                                                >
                                                    {assignment.status === 'in_progress' ? 'REANUDAR' : 'EMPEZAR'}
                                                </Link>
                                            </div>
                                        ))}

                                        {/* Checklist Completados en esta visita si los hubiera */}
                                        {visit.checklist_completions?.map(completion => (
                                            <div key={completion.id} className="bg-emerald-500/5 border border-emerald-500/20 rounded-3xl p-6 flex justify-between items-center group/done">
                                                <div className="flex items-center gap-5">
                                                    <div className="h-10 w-10 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-500 flex items-center justify-center">
                                                        <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="3" d="M5 13l4 4L19 7"/></svg>
                                                    </div>
                                                    <div>
                                                        <h4 className="text-sm font-black text-white/50 uppercase italic tracking-tight mb-1">{completion.assignment?.checklist?.title}</h4>
                                                        <span className="text-[9px] font-black text-emerald-500/60 uppercase tracking-widest italic leading-none">EJECUTADO</span>
                                                    </div>
                                                </div>
                                                <div className="text-[10px] font-black text-emerald-500/40 uppercase italic">CERTIFICADO</div>
                                            </div>
                                        ))}
                                    </div>
                                </div>
                            </div>

                            {/* COLUMNA DERECHA: CONTROL Y ACCIONES (5 COLS) */}
                            <div className="lg:col-span-5 space-y-10">
                                
                                {/* CONTROL DE JORNADA TÁCTICO */}
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

                                {/* HUB DE REGISTROS RÁPIDOS (TOUCH OPTIMIZED) */}
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

                {/* MODAL DE CIERRE Y CERTIFICACIÓN MASTER */}
                {showFinalizeModal && (
                    <div className="fixed inset-0 z-[100] overflow-y-auto flex items-center justify-center p-4 sm:p-6">
                        <div className="fixed inset-0 bg-black/95 backdrop-blur-2xl transition-opacity animate-fade-in" onClick={() => setShowFinalizeModal(false)}></div>
                        
                        <div className="relative bg-[#11111d] w-full max-w-2xl rounded-[3rem] border border-white/10 shadow-3xl overflow-hidden animate-fade-in-down">
                            <form onSubmit={handleFinalize} className="px-8 pt-10 pb-8 sm:px-10">
                                <div className="flex flex-col items-center mb-6">
                                    <div className="h-12 w-12 bg-emerald-500/10 rounded-2xl border border-emerald-500/20 text-emerald-500 mb-4 flex items-center justify-center shadow-inner">
                                        <svg className="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="2"><path strokeLinecap="round" strokeLinejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    </div>
                                    <h3 className="text-xl font-black text-white uppercase italic tracking-widest text-center leading-none mb-2">Sello <span className="text-emerald-500">Operativo</span></h3>
                                    <p className="text-[9px] font-black text-white/20 uppercase tracking-[0.4em] italic text-center">Protocolo de Cierre Tactico</p>
                                </div>

                                <div className="space-y-4">
                                    <div>
                                        <label className="block text-[10px] font-black text-white/20 uppercase tracking-[0.2em] mb-2 italic px-2">Comandos de Cierre (Observaciones)*</label>
                                        <textarea value={finalizeForm.data.notes} onChange={e => finalizeForm.setData('notes', e.target.value)} rows="3" className={`block w-full bg-black/40 border ${finalizeForm.errors.notes ? 'border-red-500' : 'border-white/10'} rounded-2xl px-6 py-4 text-white text-xs font-bold focus:outline-none focus:ring-2 focus:ring-emerald-500/50 transition-all placeholder-white/5 uppercase italic`} placeholder="Resumen técnico del despliegue..."></textarea>
                                        {finalizeForm.errors.notes && <p className="text-[9px] font-black text-red-500 mt-2 ml-2 uppercase italic">{finalizeForm.errors.notes}</p>}
                                    </div>

                                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div>
                                            <label className="block text-[10px] font-black text-white/20 uppercase tracking-[0.2em] mb-2 italic px-2">Responsable en Sede</label>
                                            <input type="text" value={finalizeForm.data.responsible_name} onChange={e => finalizeForm.setData('responsible_name', e.target.value)} className={`block w-full bg-black/40 border ${finalizeForm.errors.responsible_name ? 'border-red-500' : 'border-white/10'} rounded-xl px-4 py-3 text-white text-xs font-bold focus:outline-none focus:ring-2 focus:ring-emerald-500/50 transition-all uppercase italic`} />
                                            {finalizeForm.errors.responsible_name && <p className="text-[9px] font-black text-red-500 mt-2 ml-2 uppercase italic">{finalizeForm.errors.responsible_name}</p>}
                                        </div>
                                        <div>
                                            <label className="block text-[10px] font-black text-white/20 uppercase tracking-[0.2em] mb-2 italic px-2">Notificación Electrónica</label>
                                            <input type="email" value={finalizeForm.data.email_responsable} onChange={e => finalizeForm.setData('email_responsable', e.target.value)} placeholder="email@responsable.com" className={`block w-full bg-black/40 border ${finalizeForm.errors.email_responsable ? 'border-red-500' : 'border-white/10'} rounded-xl px-4 py-3 text-white text-xs font-bold focus:outline-none focus:ring-2 focus:ring-emerald-500/50 transition-all italic`} />
                                            {finalizeForm.errors.email_responsable && <p className="text-[9px] font-black text-red-500 mt-2 ml-2 uppercase italic">{finalizeForm.errors.email_responsable}</p>}
                                        </div>
                                    </div>

                                    {!finalizeForm.data.signature_unavailable && (
                                        <div className="space-y-2 animate-fade-in">
                                            <div className="flex items-center justify-between px-2">
                                                <label className="text-[10px] font-black text-emerald-500 uppercase tracking-[0.2em] italic">Panel de Certificación Digital*</label>
                                                <button type="button" onClick={clearSignature} className="text-[8px] font-black text-white/20 hover:text-red-500 uppercase tracking-widest italic transition-colors">Limpiar Sello</button>
                                            </div>
                                            <div className={`relative h-32 bg-black/60 border-2 border-dashed ${finalizeForm.errors.signature ? 'border-red-500' : 'border-white/10'} rounded-[2rem] overflow-hidden group shadow-inner`}>
                                                <canvas ref={signatureRef} className="w-full h-full cursor-crosshair"></canvas>
                                                <div className="absolute bottom-6 inset-x-0 text-center pointer-events-none opacity-10 group-hover:opacity-100 transition-opacity">
                                                    <p className="text-[9px] font-black text-white uppercase tracking-[0.3em] italic">Firma Requerida para Validación de Campo</p>
                                                </div>
                                            </div>
                                            {finalizeForm.errors.signature && <p className="text-[9px] font-black text-red-500 mt-1 text-center uppercase italic">{finalizeForm.errors.signature}</p>}
                                        </div>
                                        </div>
                                    )}

                                    <div className="flex items-center gap-4 px-2">
                                        <input type="checkbox" id="unavailable" checked={finalizeForm.data.signature_unavailable} onChange={e => finalizeForm.setData('signature_unavailable', e.target.checked)} className="w-5 h-5 rounded-lg border-white/10 bg-black/40 text-emerald-500 focus:ring-emerald-500/50" />
                                        <label htmlFor="unavailable" className="text-[10px] font-black text-white/40 uppercase tracking-widest italic cursor-pointer select-none">Firma Inaccesible / Omisión Justificada</label>
                                    </div>

                                    {finalizeForm.data.signature_unavailable && (
                                        <div className="animate-fade-in">
                                            <label className="block text-[11px] font-black text-white/20 uppercase tracking-[0.2em] mb-4 italic px-2">Razón de Omisión*</label>
                                            <textarea value={finalizeForm.data.signature_skip_reason} onChange={e => finalizeForm.setData('signature_skip_reason', e.target.value)} rows="2" className="block w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-white text-sm font-bold focus:outline-none focus:ring-1 focus:ring-red-500 transition-all uppercase italic" placeholder="Justifique la falta de firma..."></textarea>
                                        </div>
                                    )}
                                </div>

                                <div className="mt-8 flex flex-col sm:flex-row-reverse gap-3">
                                    <button type="submit" disabled={finalizeForm.processing} className={`w-full sm:w-auto h-12 px-10 ${finalizeForm.processing ? 'bg-emerald-900 cursor-not-allowed' : 'bg-emerald-600 hover:brightness-110 shadow-emerald-900/40'} text-white text-[9px] font-black uppercase tracking-widest italic rounded-xl shadow-2xl active:scale-95 transition-all flex items-center justify-center gap-2`}>
                                        <span>{finalizeForm.processing ? 'Sincronizando...' : 'Sellar Operación'}</span>
                                        <svg className={`w-4 h-4 ${finalizeForm.processing ? 'animate-spin' : ''}`} fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="3">
                                            {finalizeForm.processing ? (
                                                <path strokeLinecap="round" strokeLinejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                            ) : (
                                                <path d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                            )}
                                        </svg>
                                    </button>
                                    <button type="button" onClick={() => setShowFinalizeModal(false)} className="w-full sm:w-auto h-12 px-10 bg-white/5 text-white text-[9px] font-black uppercase tracking-widest italic rounded-xl border border-white/10 hover:bg-white/10 transition-all text-center">Abortar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                )}
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
