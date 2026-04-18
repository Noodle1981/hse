import { router, Head, useForm, usePage, Link } from '@inertiajs/react';
import AuthenticatedLayout from '../../../Layouts/AuthenticatedLayout';
import { useState, useEffect, useRef } from 'react';
import SignaturePad from 'signature_pad';

export default function InProgress({ visit, user, templates = [] }) {
    const { auth, flash } = usePage().props;
    
    // Estados de UI
    const [duration, setDuration] = useState(0);
    const [showFinalizeModal, setShowFinalizeModal] = useState(false);
    const [showAutoAssignModal, setShowAutoAssignModal] = useState(false);
    const [showStartTrainingModal, setShowStartTrainingModal] = useState(false);
    
    // Formulario de Inicio de Capacitación
    const trainingForm = useForm({
        training_id: '',
        title: '',
        description: '',
        duration: 30,
    });
    useEffect(() => {
        const start = new Date(visit.start_time);
        const interval = setInterval(() => {
            const now = new Date();
            const diff = Math.floor((now - start) / 1000 / 60); // minutos
            setDuration(diff);
        }, 60000); // Actualizar cada minuto

        // Carga inicial
        const initialDiff = Math.floor((new Date() - start) / 1000 / 60);
        setDuration(initialDiff);

        return () => clearInterval(interval);
    }, [visit.start_time]);

    // Formulario de Finalización
    const finalizeForm = useForm({
        notes: '',
        responsible_name: visit.sede?.responsable_1_nombre || '',
        signature: '',
        signature_unavailable: false,
        signature_skip_reason: '',
        email_responsable: visit.sede?.responsable_1_email || '',
    });

    // Manejo de Firma
    const signatureRef = useRef(null);
    const signaturePad = useRef(null);

    useEffect(() => {
        if (showFinalizeModal && signatureRef.current && !finalizeForm.data.signature_unavailable) {
            signaturePad.current = new SignaturePad(signatureRef.current, {
                backgroundColor: 'rgba(0,0,0,0.1)',
                penColor: 'rgb(255, 255, 255)'
            });
        }
    }, [showFinalizeModal, finalizeForm.data.signature_unavailable]);

    const clearSignature = () => {
        if (signaturePad.current) signaturePad.current.clear();
    };

    const handleFinalize = (e) => {
        e.preventDefault();
        
        if (!finalizeForm.data.signature_unavailable && signaturePad.current) {
            if (signaturePad.current.isEmpty()) {
                alert('Por favor, capture la firma del responsable.');
                return;
            }
            finalizeForm.setData('signature', signaturePad.current.toDataURL());
        }

        finalizeForm.post(route('gestor.operations.visits.finalize', visit.id));
    };

    return (
        <AuthenticatedLayout title="Consola de Despliegue Táctico">
            <Head title="Visita en Curso" />

            <div className="px-4 sm:px-6 lg:px-8 py-8 text-white selection:bg-orange-500/30">
                <div className="max-w-[100rem] mx-auto space-y-8">
                    
                    {/* BARRA DE ESTADO SUPERIOR */}
                    <div className="bg-[#16162a]/60 backdrop-blur-xl rounded-[3rem] border border-white/5 p-8 shadow-2xl flex flex-col lg:flex-row items-center justify-between gap-8 relative overflow-hidden animate-fade-in-down">
                        <div className="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-transparent via-orange-500 to-transparent"></div>
                        
                        <div className="flex items-center gap-6">
                            <div className="w-16 h-16 rounded-2xl bg-orange-500/10 border border-orange-500/20 flex items-center justify-center text-orange-500">
                                <svg className="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m4 0h1m-5 4h1m4 0h1m-5 4h1m4 0h1"/></svg>
                            </div>
                            <div>
                                <h1 className="text-2xl font-black uppercase italic tracking-tighter leading-none">{visit.sede?.nombre_sede}</h1>
                                <p className="text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic mt-2">Visita Táctica en Progreso — ID: #{visit.id}</p>
                            </div>
                        </div>

                        <div className="flex items-center gap-8">
                            <div className="text-right">
                                <div className="text-[10px] font-black text-orange-400 uppercase tracking-widest italic mb-1">Permanencia Actual</div>
                                <div className="text-3xl font-black text-white italic tracking-tighter">{duration} <span className="text-xs uppercase">MIN</span></div>
                            </div>
                            <div className="h-12 w-px bg-white/5"></div>
                            <button 
                                onClick={() => setShowFinalizeModal(true)}
                                className="px-12 py-5 bg-emerald-600 text-white rounded-[2rem] text-[11px] font-black uppercase tracking-[0.2em] italic transition-all shadow-xl shadow-emerald-600/20 hover:brightness-110 active:scale-95"
                            >
                                Finalizar Misión
                            </button>
                        </div>
                    </div>

                    <div className="grid grid-cols-1 lg:grid-cols-12 gap-8">
                        
                        {/* COLUMNA IZQUIERDA: CHECKLISTS Y TAREAS */}
                        <div className="lg:col-span-8 space-y-8">
                            
                            {/* SECCIÓN DE CHECKLISTS */}
                            <div className="bg-[#16162a]/40 backdrop-blur-md rounded-[3rem] border border-white/5 overflow-hidden shadow-2xl animate-fade-in-up">
                                <div className="px-10 py-8 border-b border-white/5 bg-white/5 flex items-center justify-between">
                                    <h3 className="text-sm font-black text-white italic uppercase tracking-[0.3em] flex items-center gap-3">
                                        <span className="w-1.5 h-6 bg-orange-500 rounded-full"></span>
                                        Matrices de Inspección
                                    </h3>
                                    <button onClick={() => setShowAutoAssignModal(true)} className="px-6 py-2 bg-white/5 border border-white/10 rounded-xl text-[9px] font-black text-white/40 uppercase tracking-widest italic hover:text-white hover:border-white/20 transition-all">
                                        Anclar Matriz Ad-Hoc
                                    </button>
                                </div>
                                <div className="p-8 grid grid-cols-1 md:grid-cols-2 gap-6">
                                    {/* ASIGNACIONES COMPLETADAS EN ESTA VISITA */}
                                    {visit.checklist_completions?.map(completion => (
                                        <div key={completion.id} className="bg-emerald-500/5 border border-emerald-500/30 rounded-[2rem] p-6 relative group overflow-hidden">
                                            <div className="absolute top-0 right-0 p-4">
                                                <svg className="w-6 h-6 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="3" d="M5 13l4 4L19 7"/></svg>
                                            </div>
                                            <div className="text-[8px] font-black text-emerald-400 uppercase tracking-widest italic mb-2">Completada — {new Date(completion.created_at).toLocaleTimeString()}</div>
                                            <h4 className="text-sm font-black text-white italic uppercase tracking-tight">{completion.assignment?.checklist?.title}</h4>
                                            <div className="mt-4 pt-4 border-t border-emerald-500/10 flex justify-between items-center text-[9px] font-black text-emerald-400/40 uppercase italic">
                                                <span>Reactivos: {completion.responses?.length || 0}</span>
                                                <Link href={route('gestor.inspections.show', completion.id)} className="underline hover:text-emerald-400">Ver Resumen</Link>
                                            </div>
                                        </div>
                                    ))}

                                    {/* ASIGNACIONES PENDIENTES (O EN PROGRESO) */}
                                    {visit.sede?.assignments?.filter(a => a.status !== 'completed' && a.user_id === auth.user.id).map(assignment => (
                                        <div key={assignment.id} className="bg-[#11111d]/60 border border-white/5 rounded-[2rem] p-6 relative group hover:border-orange-500/20 transition-all shadow-xl">
                                            <div className="flex justify-between items-start mb-4">
                                                <div className="px-3 py-1 bg-white/5 rounded-lg text-[7.5px] font-black text-white/40 uppercase tracking-widest italic border border-white/5">
                                                    {assignment.status === 'in_progress' ? '📜 EN CURSO' : '📑 PENDIENTE'}
                                                </div>
                                                <Link 
                                                    href={route('gestor.checklists.fill', [assignment.dynamic_checklist_id, { assignment: assignment.id, visit_id: visit.id }])} 
                                                    className="px-6 py-2 bg-orange-600 text-white rounded-xl text-[9px] font-black uppercase tracking-widest italic shadow-lg hover:brightness-110"
                                                >
                                                    {assignment.status === 'in_progress' ? 'Reanudar' : 'Iniciar'}
                                                </Link>
                                            </div>
                                            <h4 className="text-sm font-black text-white italic uppercase tracking-tight line-clamp-2">{assignment.checklist?.title}</h4>
                                            <p className="text-[9px] font-bold text-white/20 uppercase tracking-widest mt-2">Vence: {new Date(assignment.due_date).toLocaleDateString()}</p>
                                        </div>
                                    ))}

                                    {/* Si no hay nada */}
                                    {visit.checklist_completions?.length === 0 && (!visit.sede?.assignments || visit.sede.assignments.length === 0) && (
                                        <div className="col-span-2 border-2 border-dashed border-white/5 rounded-[2rem] p-10 flex flex-col items-center justify-center gap-4 text-white/10 opacity-20 transition-all">
                                            <svg className="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            <span className="text-[10px] font-black uppercase tracking-[0.2em] italic">Sin tareas vinculadas</span>
                                        </div>
                                    )}
                                </div>
                            </div>

                            {/* SECCIÓN DE CAPACITACIONES */}
                            <div className="bg-[#16162a]/40 backdrop-blur-md rounded-[3rem] border border-white/5 overflow-hidden shadow-2xl animate-fade-in-up delay-100">
                                <div className="px-10 py-8 border-b border-white/5 bg-white/5 flex items-center justify-between">
                                    <h3 className="text-sm font-black text-white italic uppercase tracking-[0.3em] flex items-center gap-3">
                                        <span className="w-1.5 h-6 bg-blue-500 rounded-full shadow-[0_0_10px_rgba(59,130,246,0.5)]"></span>
                                        Formación & Capacitación
                                    </h3>
                                    <button onClick={() => setShowStartTrainingModal(true)} className="px-6 py-2 bg-blue-600 text-white rounded-xl text-[9px] font-black uppercase tracking-widest italic hover:brightness-110 transition-all">
                                        + Nueva Sesión
                                    </button>
                                </div>
                                <div className="p-8 space-y-4">
                                    {visit.trainings?.map(vt => (
                                        <div key={vt.id} className="bg-blue-500/5 border border-blue-500/30 rounded-[2rem] p-6 flex items-center justify-between group">
                                            <div className="flex items-center gap-6">
                                                <div className="w-12 h-12 bg-blue-500/10 rounded-2xl flex items-center justify-center text-blue-400">
                                                    <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.168.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5S19.832 5.477 21 6.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" strokeWidth="2"/></svg>
                                                </div>
                                                <div>
                                                    <h4 className="text-sm font-black text-white italic uppercase tracking-tight">{vt.title}</h4>
                                                    <div className="text-[9px] font-black text-blue-400/40 uppercase tracking-widest mt-1 italic">
                                                        {vt.attendees?.length || 0} Alumnos Registrados
                                                    </div>
                                                </div>
                                            </div>
                                            <Link 
                                                href={route('gestor.operations.trainings.session', vt.id)} 
                                                className="px-6 py-3 bg-white/5 border border-white/10 rounded-xl text-[9px] font-black text-white italic uppercase tracking-widest hover:bg-white/10 transition-all"
                                            >
                                                Ingresar a la Sesión
                                            </Link>
                                        </div>
                                    ))}
                                    {visit.trainings?.length === 0 && (
                                        <div className="py-10 text-center opacity-10 italic">
                                            <p className="text-[9px] font-black uppercase tracking-[0.2em]">No hay capacitaciones registradas en este despliegue</p>
                                        </div>
                                    )}
                                </div>
                            </div>
                        </div>

                        {/* COLUMNA DERECHA: HALLAZGOS E INCIDENTES */}
                        <div className="lg:col-span-4 space-y-8">
                            
                            {/* HALLAZGOS REGISTRADOS */}
                            <div className="bg-[#16162a]/40 backdrop-blur-md rounded-[3rem] border border-white/5 overflow-hidden shadow-2xl animate-fade-in-up">
                                <div className="px-8 py-6 border-b border-white/5 bg-white/5 flex items-center justify-between">
                                    <h3 className="text-[10px] font-black text-white italic uppercase tracking-[0.2em] flex items-center gap-2">
                                        <span className="w-1.5 h-4 bg-red-600 rounded-full shadow-[0_0_8px_rgba(220,38,38,0.4)]"></span>
                                        Hallazgos Detectados
                                    </h3>
                                    <Link 
                                        href={route('gestor.operations.findings.create', { visit_id: visit.id })}
                                        className="px-4 py-2 bg-red-600/10 border border-red-500/20 text-red-500 rounded-xl flex items-center justify-center hover:bg-red-600 hover:text-white transition-all text-[9px] font-black uppercase italic tracking-widest"
                                    >
                                        + Nuevo
                                    </Link>
                                </div>
                                <div className="p-8 space-y-4">
                                    {visit.hallazgos?.map(h => (
                                        <Link 
                                            key={h.id} 
                                            href={route('gestor.operations.findings.show', h.id)}
                                            className="block bg-black/20 rounded-2xl p-5 border border-white/5 group hover:border-red-500/30 transition-all text-left"
                                        >
                                            <div className="flex justify-between items-start mb-2">
                                                <span className={`px-2 py-0.5 rounded text-[7px] font-black uppercase italic ${h.criticidad === 'critico' || h.criticidad === 'alto' ? 'bg-red-500/20 text-red-500' : 'bg-orange-500/20 text-orange-400'}`}>
                                                    {h.criticidad}
                                                </span>
                                                <span className="text-[7px] font-black text-white/20 uppercase italic">#{h.id}</span>
                                            </div>
                                            <h4 className="text-[10px] font-black text-white italic uppercase tracking-tight mb-1 group-hover:text-red-500 transition-colors">{h.titulo}</h4>
                                            <p className="text-[9px] font-bold text-white/40 uppercase italic leading-tight line-clamp-1">{h.descripcion}</p>
                                        </Link>
                                    ))}
                                    {visit.hallazgos?.length === 0 && (
                                        <div className="py-6 text-center opacity-10">
                                            <p className="text-[9px] font-black uppercase tracking-widest italic">Sin hallazgos reportados</p>
                                        </div>
                                    )}
                                </div>
                            </div>

                            {/* INCIDENTES REGISTRADOS */}
                            <div className="bg-[#16162a]/40 backdrop-blur-md rounded-[3rem] border border-white/5 overflow-hidden shadow-2xl animate-fade-in-up delay-200">
                                <div className="px-8 py-6 border-b border-white/5 bg-white/5 flex items-center justify-between">
                                    <h3 className="text-[10px] font-black text-red-600 italic uppercase tracking-[0.2em] flex items-center gap-2">
                                        <span className="w-1.5 h-4 bg-red-600 rounded-full animate-pulse shadow-[0_0_10px_rgba(220,38,38,0.6)]"></span>
                                        Incidentes / Accidentes
                                    </h3>
                                    <Link 
                                        href={route('tecnico.incidentes.create', { visit_id: visit.id })}
                                        className="px-4 py-2 bg-red-600 text-white rounded-xl flex items-center justify-center hover:brightness-110 transition-all text-[9px] font-black uppercase italic tracking-widest shadow-lg shadow-red-600/20"
                                    >
                                        + Reportar
                                    </Link>
                                </div>
                                <div className="p-8 space-y-4">
                                    {visit.incidentes?.map(i => (
                                        <Link 
                                            key={i.id} 
                                            href={route('tecnico.incidentes.show', i.id)}
                                            className="block bg-red-600/5 rounded-2xl p-5 border border-red-500/10 group hover:border-red-500/40 transition-all text-left"
                                        >
                                            <div className="flex justify-between items-start mb-2">
                                                <span className="px-2 py-0.5 rounded bg-red-600 text-white text-[7px] font-black uppercase italic shadow-sm">
                                                    {i.gravedad}
                                                </span>
                                                <span className="text-[7px] font-black text-white/20 uppercase italic">#{i.id}</span>
                                            </div>
                                            <h4 className="text-[10px] font-black text-white italic uppercase tracking-tight mb-1">{i.titulo}</h4>
                                            <p className="text-[9px] font-bold text-white/40 uppercase italic leading-tight line-clamp-1 truncate">{i.tipo.toUpperCase()}</p>
                                        </Link>
                                    ))}
                                    {visit.incidentes?.length === 0 && (
                                        <div className="py-6 text-center opacity-10">
                                            <p className="text-[9px] font-black uppercase tracking-widest italic">Sin incidentes críticos hoy</p>
                                        </div>
                                    )}
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            {/* MODAL DE FINALIZACIÓN */}
            {showFinalizeModal && (
                <div className="fixed inset-0 z-50 flex items-center justify-center p-4 animate-fade-in">
                    <div className="absolute inset-0 bg-black/90 backdrop-blur-xl" onClick={() => setShowFinalizeModal(false)}></div>
                    <div className="bg-[#16162a] border border-white/10 rounded-[3rem] p-10 max-w-2xl w-full relative z-10 shadow-3xl overflow-y-auto max-h-[90vh] custom-scrollbar">
                        <div className="absolute inset-x-0 top-0 h-1.5 bg-emerald-500"></div>
                        
                        <h2 className="text-2xl font-black text-white italic uppercase tracking-tighter mb-8 flex items-center gap-4">
                            <span className="w-2 h-10 bg-emerald-500 rounded-full"></span>
                            Finalizar y Certificar Visita
                        </h2>

                        <form onSubmit={handleFinalize} className="space-y-8">
                            <div className="space-y-2">
                                <label className="text-[9px] font-black text-white/30 uppercase tracking-[0.3em] ml-4 italic">Observaciones Generales de la Misión</label>
                                <textarea 
                                    value={finalizeForm.data.notes}
                                    onChange={e => finalizeForm.setData('notes', e.target.value)}
                                    rows="4"
                                    required
                                    className="w-full bg-black/40 border border-white/10 rounded-[2rem] px-8 py-6 text-sm font-bold text-white focus:ring-2 focus:ring-emerald-500 transition-all uppercase placeholder-white/5"
                                    placeholder="DETALLE LOS HITOS DE LA VISITA..."
                                ></textarea>
                            </div>

                            <div className="space-y-6 bg-black/20 p-8 rounded-[2rem] border border-white/5">
                                <div className="relative group">
                                    <span className="absolute -top-2.5 left-4 bg-[#11111d] px-2 text-[8px] font-black text-emerald-400 uppercase tracking-[0.3em] italic z-10">Nombre del Responsable en Sede</span>
                                    <input 
                                        type="text" 
                                        value={finalizeForm.data.responsible_name}
                                        onChange={e => finalizeForm.setData('responsible_name', e.target.value)}
                                        required
                                        className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-emerald-500 transition-all uppercase"
                                    />
                                </div>

                                {!finalizeForm.data.signature_unavailable ? (
                                    <div className="space-y-4">
                                        <div className="flex justify-between items-center ml-4">
                                            <span className="text-[8px] font-black text-white/30 uppercase tracking-[0.3em] italic text-left">Firma Digital del Responsable</span>
                                            <button type="button" onClick={clearSignature} className="text-[8px] font-black text-red-500 uppercase italic hover:underline">Limpiar Panel</button>
                                        </div>
                                        <div className="bg-white/5 border border-white/10 rounded-3xl overflow-hidden h-40 relative group">
                                            <canvas ref={signatureRef} className="w-full h-full cursor-crosshair"></canvas>
                                            <div className="absolute inset-0 pointer-events-none flex items-center justify-center opacity-10 group-hover:opacity-5 transition-opacity">
                                                <span className="text-[10px] font-black uppercase italic tracking-widest text-center">Capture Firma Aquí</span>
                                            </div>
                                        </div>
                                    </div>
                                ) : (
                                    <div className="space-y-4 animate-fade-in">
                                        <div className="relative group">
                                            <span className="absolute -top-2.5 left-4 bg-[#11111d] px-2 text-[8px] font-black text-red-400 uppercase tracking-[0.3em] italic z-10">Motivo de Firma Ausente</span>
                                            <input 
                                                type="text" 
                                                value={finalizeForm.data.signature_skip_reason}
                                                onChange={e => finalizeForm.setData('signature_skip_reason', e.target.value)}
                                                className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-xs font-bold text-white focus:ring-2 focus:ring-red-500 transition-all uppercase"
                                                placeholder="EJ: RESPONSABLE EN REUNIÓN / NO PRESENTE"
                                            />
                                        </div>
                                        <div className="relative group">
                                            <span className="absolute -top-2.5 left-4 bg-[#11111d] px-2 text-[8px] font-black text-blue-400 uppercase tracking-[0.3em] italic z-10">Email para Firma Remota</span>
                                            <input 
                                                type="email" 
                                                value={finalizeForm.data.email_responsable}
                                                onChange={e => finalizeForm.setData('email_responsable', e.target.value)}
                                                className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-xs font-bold text-white focus:ring-2 focus:ring-blue-500 transition-all lowercase"
                                                placeholder="RESPONSABLE@EMPRESA.COM"
                                            />
                                        </div>
                                    </div>
                                )}

                                <div className="flex items-center gap-4 pt-2">
                                    <label className="inline-flex items-center cursor-pointer group">
                                        <input 
                                            type="checkbox" 
                                            checked={finalizeForm.data.signature_unavailable} 
                                            onChange={e => finalizeForm.setData('signature_unavailable', e.target.checked)} 
                                            className="sr-only peer" 
                                        />
                                        <div className="relative w-10 h-5 bg-white/5 border border-white/10 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[1px] after:start-[1px] after:bg-white/20 after:border-white/10 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-orange-500/50"></div>
                                        <span className="ms-3 text-[9px] font-black text-white/20 uppercase tracking-widest italic group-hover:text-orange-400 transition-colors">Responsable no disponible para firma física</span>
                                    </label>
                                </div>
                            </div>

                            <div className="flex gap-4 pt-4">
                                <button type="button" onClick={() => setShowFinalizeModal(false)} className="flex-1 py-5 bg-white/5 text-white/40 hover:text-white rounded-[2rem] text-[11px] font-black uppercase tracking-widest italic transition-all">Cancelar</button>
                                <button 
                                    type="submit" 
                                    disabled={finalizeForm.processing}
                                    className="flex-[2] py-5 bg-emerald-600 text-white rounded-[2rem] text-[11px] font-black uppercase tracking-[0.2em] italic transition-all shadow-xl hover:brightness-110 active:scale-95 disabled:opacity-50"
                                >
                                    {finalizeForm.processing ? 'CERTIFICANDO...' : 'CONFIRMAR CIERRE'}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            )}

            {/* MODAL DE INICIO DE CAPACITACIÓN */}
            {showStartTrainingModal && (
                <div className="fixed inset-0 z-50 flex items-center justify-center p-4 animate-fade-in">
                    <div className="absolute inset-0 bg-black/95 backdrop-blur-2xl" onClick={() => setShowStartTrainingModal(false)}></div>
                    <div className="bg-[#16162a] border border-white/10 rounded-[3rem] p-10 max-w-xl w-full relative z-10 shadow-3xl">
                        <div className="absolute inset-x-0 top-0 h-1.5 bg-blue-500"></div>
                        <h2 className="text-2xl font-black text-white italic uppercase tracking-tighter mb-8 flex items-center gap-4">
                            <span className="w-2 h-10 bg-blue-500 rounded-full"></span>
                            Lanzar Protocolo de Formación
                        </h2>

                        <form onSubmit={(e) => {
                            e.preventDefault();
                            trainingForm.post(route('gestor.operations.trainings.start-in-visit', visit.id), {
                                onSuccess: () => setShowStartTrainingModal(false)
                            });
                        }} className="space-y-8">
                            <div className="relative group">
                                <span className="absolute -top-2.5 left-4 bg-[#11111d] px-2 text-[8px] font-black text-blue-400 uppercase tracking-[0.3em] italic z-10">Seleccionar Plantilla</span>
                                <select 
                                    value={trainingForm.data.training_id}
                                    onChange={e => trainingForm.setData('training_id', e.target.value)}
                                    className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-blue-500 transition-all [color-scheme:dark] italic uppercase"
                                >
                                    <option value="">Capacitación Ad-Hoc (Tema libre)</option>
                                    {templates.map(t => <option key={t.id} value={t.id}>{t.title}</option>)}
                                </select>
                            </div>

                            {!trainingForm.data.training_id && (
                                <div className="space-y-6 animate-fade-in">
                                    <div className="relative group">
                                        <span className="absolute -top-2.5 left-4 bg-[#11111d] px-2 text-[8px] font-black text-blue-400 uppercase tracking-[0.3em] italic z-10">Tema Personalizado</span>
                                        <input 
                                            type="text" 
                                            value={trainingForm.data.title}
                                            onChange={e => trainingForm.setData('title', e.target.value)}
                                            required={!trainingForm.data.training_id}
                                            className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-blue-500 transition-all uppercase italic"
                                            placeholder="EJ: INDUCCIÓN RIESGOS ESPECÍFICOS..."
                                        />
                                    </div>
                                    <div className="relative group">
                                        <span className="absolute -top-2.5 left-4 bg-[#11111d] px-2 text-[8px] font-black text-blue-400 uppercase tracking-[0.3em] italic z-10">Duración Estimada</span>
                                        <input type="number" value={trainingForm.data.duration} onChange={e => trainingForm.setData('duration', e.target.value)} className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-blue-500 transition-all italic"/>
                                    </div>
                                </div>
                            )}

                            <div className="flex gap-4 pt-4">
                                <button type="button" onClick={() => setShowStartTrainingModal(false)} className="flex-1 py-5 bg-white/5 text-white/40 hover:text-white rounded-[2rem] text-[11px] font-black uppercase tracking-widest italic transition-all">Cancelar</button>
                                <button 
                                    type="submit" 
                                    disabled={trainingForm.processing}
                                    className="flex-[2] py-5 bg-blue-600 text-white rounded-[2rem] text-[11px] font-black uppercase tracking-[0.2em] italic transition-all shadow-xl hover:brightness-110 active:scale-95 disabled:opacity-50"
                                >
                                    {trainingForm.processing ? 'Lanzando...' : 'Iniciar Sesión'}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            )}

            <style>{`
                .animate-fade-in { animation: fade-in 0.4s ease-out forwards; }
                .animate-fade-in-down { animation: fade-in-down 0.6s ease-out forwards; }
                .animate-fade-in-up { animation: fade-in-up 0.5s ease-out forwards; }
                .delay-100 { animation-delay: 0.1s; }
                @keyframes fade-in { from { opacity: 0; } to { opacity: 1; } }
                @keyframes fade-in-down { from { opacity: 0; transform: translateY(-30px); } to { opacity: 1; transform: translateY(0); } }
                @keyframes fade-in-up { from { opacity: 0; transform: translateY(40px); } to { opacity: 1; transform: translateY(0); } }
                .custom-scrollbar::-webkit-scrollbar { width: 6px; }
                .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 10px; }
            `}</style>
        </AuthenticatedLayout>
    );
}
