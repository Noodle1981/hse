import { router, Head, useForm, usePage, Link } from '@inertiajs/react';
import AuthenticatedLayout from '../../../Layouts/AuthenticatedLayout';
import { useState, useEffect, useRef, useMemo } from 'react';
import SignaturePad from 'signature_pad';
import ActionSelector from './ActionSelector';

export default function InProgress({ visit, user, suggestedPlanTasks = [], pendingAssignments = [], pendingTrainings = [], templates = [], checklistTemplates = [] }) {
    const { auth, flash } = usePage().props;
    
    // ESTADOS TÁCTICOS
    const [minutes, setMinutes] = useState(0);
    const [showFinalizeModal, setShowFinalizeModal] = useState(false);
    const [showAutoAssignModal, setShowAutoAssignModal] = useState(false);
    const [isExceeded, setIsExceeded] = useState(false);
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
                    <div className="flex items-center gap-4">
                        {/* GPS STATUS BADGE */}
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
                                
                                {/* NUEVA TAREA TRIGGER CARD */}
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

                                {/* PROTOCOLOS HECHOS / HISTORIAL */}
                                <div className="bg-[#16162a]/40 backdrop-blur-md rounded-[2.5rem] border border-white/5 overflow-hidden shadow-2xl relative">
                                    <div className="px-8 py-6 bg-black/20 border-b border-white/5 flex justify-between items-center relative z-10">
                                        <div className="flex items-center gap-4">
                                            <div className="h-10 w-10 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-500 flex items-center justify-center rotate-3 shadow-inner">
                                                <svg className="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                            </div>
                                            <h2 className="font-black text-white uppercase tracking-[0.2em] text-[11px] italic">Protocolos <span className="text-emerald-500">Hechos</span></h2>
                                        </div>
                                        <span className="text-[9px] font-black uppercase tracking-[0.2em] text-white/40 italic">
                                            Historial: {completedTasks.length} {completedTasks.length === 1 ? 'tarea' : 'tareas'}
                                        </span>
                                    </div>

                                    <div className="p-8 space-y-5 relative z-10">
                                        {completedTasks.length === 0 ? (
                                            <div className="text-center py-16 border-2 border-dashed border-white/5 rounded-[2rem] bg-black/10">
                                                <p className="text-[10px] font-black text-white/20 uppercase tracking-[0.4em] italic leading-relaxed max-w-xs mx-auto">
                                                    Aún no se han registrado tareas en esta visita. Presiona &quot;Nueva Tarea&quot; arriba para iniciar.
                                                </p>
                                            </div>
                                        ) : (
                                            <div className="space-y-4">
                                                {completedTasks.map((task) => {
                                                    const timeStr = task.date ? task.date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: false }) : '';
                                                    return (
                                                        <div 
                                                            key={task.id} 
                                                            className={`relative border rounded-3xl p-6 flex flex-col sm:flex-row gap-4 justify-between sm:items-center group/done transition-all duration-300 ${
                                                                task.type === 'inspection' 
                                                                    ? (task.failures.failed > 0 
                                                                        ? 'bg-rose-500/[0.02] border-rose-500/10 hover:border-rose-500/30' 
                                                                        : 'bg-orange-500/[0.02] border-orange-500/10 hover:border-orange-500/30')
                                                                    : 'bg-indigo-500/[0.02] border-indigo-500/10 hover:border-indigo-500/30'
                                                            }`}
                                                        >
                                                            <div className="flex items-center gap-5">
                                                                {/* Icono de Estado Dinámico Conforme al Color */}
                                                                <div className={`h-12 w-12 rounded-2xl border flex items-center justify-center shrink-0 shadow-inner transition-transform duration-500 group-hover/done:scale-110 ${
                                                                    task.type === 'inspection' 
                                                                        ? (task.failures.failed > 0 
                                                                            ? 'bg-rose-500/10 border-rose-500/20 text-rose-400' 
                                                                            : 'bg-orange-500/10 border-orange-500/20 text-orange-400')
                                                                        : 'bg-indigo-500/10 border-indigo-500/20 text-indigo-400'
                                                                }`}>
                                                                    {task.type === 'inspection' ? (
                                                                        task.failures.failed > 0 ? (
                                                                            // X icon for failure (blink/pulse)
                                                                            <svg className="w-5.5 h-5.5 animate-pulse text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="3"><path strokeLinecap="round" strokeLinejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                                                        ) : (
                                                                            // Check/Tilde icon for pass
                                                                            <svg className="w-5.5 h-5.5 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="3"><path strokeLinecap="round" strokeLinejoin="round" d="M5 13l4 4L19 7"/></svg>
                                                                        )
                                                                    ) : (
                                                                        // Book icon for training
                                                                        <svg className="w-5.5 h-5.5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="2"><path strokeLinecap="round" strokeLinejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                                                    )}
                                                                </div>
                                                                
                                                                <div className="space-y-1">
                                                                    <div className="flex flex-wrap items-center gap-2">
                                                                        <h4 className="text-sm font-black text-white uppercase italic tracking-tight leading-none">{task.title}</h4>
                                                                        <span className="px-2 py-0.5 rounded-lg bg-white/5 border border-white/10 text-[9px] font-black text-indigo-100/40 uppercase tracking-widest italic">{timeStr} HS</span>
                                                                    </div>
                                                                    
                                                                    <div className="flex items-center gap-2">
                                                                        <span className={`text-[9px] font-black uppercase tracking-widest italic ${
                                                                            task.type === 'inspection' 
                                                                                ? (task.failures.failed > 0 ? 'text-rose-500/60' : 'text-orange-500/60') 
                                                                                : 'text-indigo-500/60'
                                                                        }`}>
                                                                            {task.type === 'inspection' ? 'INSPECCIÓN COMPLETADA' : 'CAPACITACIÓN COMPLETADA'}
                                                                        </span>
                                                                        
                                                                        {task.type === 'inspection' && task.failures && (
                                                                            <>
                                                                                <span className="h-1 w-1 rounded-full bg-white/20"></span>
                                                                                <span className={`px-2 py-0.5 rounded-md text-[8px] font-black uppercase tracking-widest italic ${
                                                                                    task.failures.failed > 0 
                                                                                        ? 'bg-rose-500/10 border border-rose-500/20 text-rose-500 shadow-[0_0_15px_rgba(244,63,94,0.1)]' 
                                                                                        : 'bg-orange-500/10 border border-orange-500/20 text-orange-400 shadow-[0_0_15px_rgba(249,115,22,0.1)]'
                                                                                }`}>
                                                                                    {task.failures.failed > 0 ? `${task.failures.failed} Fallas` : 'Sin Fallas'}
                                                                                </span>
                                                                            </>
                                                                        )}
                                                                    </div>

                                                                    {/* Nombre del Firmante / Participantes */}
                                                                    <div className="text-[10px] font-bold text-white/40 uppercase tracking-widest italic flex items-center gap-1.5 pt-0.5">
                                                                        <svg className="w-3.5 h-3.5 opacity-40 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                                                        {task.type === 'inspection' ? (
                                                                            <span>
                                                                                Firmado por: <span className="text-white font-black">{task.detail.assignment?.inspection?.supervisor_name || 'Responsable de Sede'}</span>
                                                                            </span>
                                                                        ) : (
                                                                            <span>
                                                                                Firmado por: <span className="text-white font-black">
                                                                                    {(() => {
                                                                                        const adhocAttendees = task.detail.attendees;
                                                                                        const plannedAttendances = task.detail.training?.attendances;
                                                                                        let namesList = [];
                                                                                        if (Array.isArray(adhocAttendees) && adhocAttendees.length > 0) {
                                                                                            namesList = adhocAttendees.map(a => a.name);
                                                                                        } else if (Array.isArray(plannedAttendances) && plannedAttendances.length > 0) {
                                                                                            namesList = plannedAttendances.map(a => a.employee_name);
                                                                                        }
                                                                                        if (namesList.length === 0) return 'Participantes';
                                                                                        if (namesList.length <= 2) return namesList.join(', ');
                                                                                        return `${namesList[0]}, ${namesList[1]} y ${namesList.length - 2} más`;
                                                                                    })()}
                                                                                </span>
                                                                            </span>
                                                                        )}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            
                                                            {task.type === 'inspection' && task.detail?.assignment?.inspection?.id && (
                                                                <Link 
                                                                    href={route('tecnico.inspections.show', task.detail.assignment.inspection.id)}
                                                                    className={`sm:self-center shrink-0 h-9 px-4 bg-white/5 hover:bg-white/10 text-white rounded-xl text-[9px] font-black uppercase tracking-widest italic border border-white/5 hover:border-white/10 transition-all flex items-center justify-center
                                                                        ${task.failures.failed > 0 ? 'hover:text-rose-400 hover:border-rose-500/20' : 'hover:text-orange-400 hover:border-orange-500/20'}`}
                                                                >
                                                                    Ver Detalle
                                                                </Link>
                                                            )}
                                                        </div>
                                                    );
                                                })}
                                            </div>
                                        )}
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

                                {Object.keys(finalizeForm.errors).length > 0 && (
                                    <div className="mb-6 p-5 bg-red-500/10 border border-red-500/20 rounded-[1.5rem] animate-fade-in">
                                        <p className="text-[10px] font-black text-red-500 uppercase tracking-[0.2em] italic mb-2 flex items-center gap-2">
                                            <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                            Campos Requeridos Faltantes
                                        </p>
                                        <ul className="list-disc pl-5 text-[9px] font-black text-indigo-200/50 uppercase tracking-widest italic space-y-1">
                                            {finalizeForm.errors.responsible_name && <li>Ingrese el nombre del Responsable en Sede</li>}
                                            {finalizeForm.errors.signature && <li>Firme digitalmente la constancia o justifique su omisión</li>}
                                            {finalizeForm.errors.signature_skip_reason && <li>Justifique por qué omitió la firma</li>}
                                            {finalizeForm.errors.notes && <li>{finalizeForm.errors.notes}</li>}
                                        </ul>
                                    </div>
                                )}

                                <div className="space-y-4">
                                    <div>
                                        <label className="block text-[10px] font-black text-white/20 uppercase tracking-[0.2em] mb-2 italic px-2">Comandos de Cierre (Observaciones)*</label>
                                        <textarea id="notes" name="notes" value={finalizeForm.data.notes} onChange={e => finalizeForm.setData('notes', e.target.value)} rows="3" className={`block w-full bg-black/40 border ${finalizeForm.errors.notes ? 'border-red-500' : 'border-white/10'} rounded-2xl px-6 py-4 text-white text-xs font-bold focus:outline-none focus:ring-2 focus:ring-emerald-500/50 transition-all placeholder-white/5 uppercase italic`} placeholder="Resumen técnico del despliegue..."></textarea>
                                        {finalizeForm.errors.notes && <p className="text-[9px] font-black text-red-500 mt-2 ml-2 uppercase italic">{finalizeForm.errors.notes}</p>}
                                    </div>

                                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div>
                                            <label className="block text-[10px] font-black text-white/20 uppercase tracking-[0.2em] mb-2 italic px-2">Responsable en Sede*</label>
                                            <input id="responsible_name" name="responsible_name" type="text" value={finalizeForm.data.responsible_name} onChange={e => finalizeForm.setData('responsible_name', e.target.value)} className={`block w-full bg-black/40 border ${finalizeForm.errors.responsible_name ? 'border-red-500' : 'border-white/10'} rounded-xl px-4 py-3 text-white text-xs font-bold focus:outline-none focus:ring-2 focus:ring-emerald-500/50 transition-all uppercase italic`} />
                                            {finalizeForm.errors.responsible_name && <p className="text-[9px] font-black text-red-500 mt-2 ml-2 uppercase italic">{finalizeForm.errors.responsible_name}</p>}
                                        </div>
                                        <div>
                                            <label className="block text-[10px] font-black text-white/20 uppercase tracking-[0.2em] mb-2 italic px-2">Notificación Electrónica</label>
                                            <input id="email_responsable" name="email_responsable" type="email" value={finalizeForm.data.email_responsable} onChange={e => finalizeForm.setData('email_responsable', e.target.value)} placeholder="email@responsable.com" className={`block w-full bg-black/40 border ${finalizeForm.errors.email_responsable ? 'border-red-500' : 'border-white/10'} rounded-xl px-4 py-3 text-white text-xs font-bold focus:outline-none focus:ring-2 focus:ring-emerald-500/50 transition-all italic`} />
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
                                    )}

                                    <div className="flex items-center gap-4 px-2">
                                        <input type="checkbox" id="unavailable" checked={finalizeForm.data.signature_unavailable} onChange={e => finalizeForm.setData('signature_unavailable', e.target.checked)} className="w-5 h-5 rounded-lg border-white/10 bg-black/40 text-emerald-500 focus:ring-emerald-500/50" />
                                        <label htmlFor="unavailable" className="text-[10px] font-black text-white/40 uppercase tracking-widest italic cursor-pointer select-none">Firma Inaccesible / Omisión Justificada</label>
                                    </div>

                                    {finalizeForm.data.signature_unavailable && (
                                        <div className="animate-fade-in">
                                            <label className="block text-[11px] font-black text-white/20 uppercase tracking-[0.2em] mb-4 italic px-2">Razón de Omisión*</label>
                                            <textarea id="signature_skip_reason" name="signature_skip_reason" value={finalizeForm.data.signature_skip_reason} onChange={e => finalizeForm.setData('signature_skip_reason', e.target.value)} rows="2" className="block w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-white text-sm font-bold focus:outline-none focus:ring-1 focus:ring-red-500 transition-all uppercase italic" placeholder="Justifique la falta de firma..."></textarea>
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
