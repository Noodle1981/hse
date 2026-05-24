import { Head, useForm, Link, router, usePage } from '@inertiajs/react';
import AuthenticatedLayout from '../../../Layouts/AuthenticatedLayout';
import { useState, useRef, useEffect } from 'react';
import SignaturePad from 'signature_pad';

export default function Session({ session }) {
    const { auth } = usePage().props;
    const [isAdding, setIsAdding] = useState(false);
    const [showFinalizeModal, setShowFinalizeModal] = useState(false);
    
    // Detect active role using shared backend prop
    const isTecnico = auth?.user?.is_technician;
    const prefix = isTecnico ? 'tecnico' : 'gestor.operations';

    const form = useForm({
        name: '',
        position: '',
        dni: '',
        signature: '',
    });

    const detailsForm = useForm({
        description: session.description || '',
        notes: session.notes || '',
    });

    const handleSaveDetails = (e) => {
        e.preventDefault();
        detailsForm.post(route(`${prefix}.trainings.update-details`, session.id), {
            preserveScroll: true
        });
    };

    const handleFinalizeSession = () => {
        setShowFinalizeModal(true);
    };

    const confirmFinalizeSession = () => {
        setShowFinalizeModal(false);
        router.post(route(`${prefix}.trainings.finalize`, session.id));
    };

    const canvasRef = useRef(null);
    const signaturePad = useRef(null);

    useEffect(() => {
        if (isAdding && canvasRef.current) {
            signaturePad.current = new SignaturePad(canvasRef.current, {
                backgroundColor: 'rgba(255, 255, 255, 0.05)',
                penColor: 'rgb(59, 130, 246)'
            });
            
            const resizeCanvas = () => {
                const ratio = Math.max(window.devicePixelRatio || 1, 1);
                canvasRef.current.width = canvasRef.current.offsetWidth * ratio;
                canvasRef.current.height = canvasRef.current.offsetHeight * ratio;
                canvasRef.current.getContext("2d").scale(ratio, ratio);
                signaturePad.current.clear();
            };

            window.addEventListener("resize", resizeCanvas);
            resizeCanvas();
            return () => window.removeEventListener("resize", resizeCanvas);
        }
    }, [isAdding]);

    const handleAddAttendee = (e) => {
        e.preventDefault();
        if (signaturePad.current.isEmpty()) {
            alert('Por favor, capture la firma del alumno.');
            return;
        }

        form.setData('signature', signaturePad.current.toDataURL());
        
        form.post(route(`${prefix}.trainings.add-attendee`, session.id), {
            onSuccess: () => {
                setIsAdding(false);
                form.reset();
            },
            preserveScroll: true
        });
    };

    const removeAttendee = (index) => {
        if (confirm('¿Eliminar registro de este asistente?')) {
            router.post(route(`${prefix}.trainings.remove-attendee`, [session.id, index]));
        }
    };

    return (
        <AuthenticatedLayout title="Registro de Asistencia — En Vivo">
            <Head title={`Sesión: ${session.title}`} />

            <div className="px-4 py-8 lg:p-12 text-white selection:bg-blue-500/30">
                <div className="max-w-5xl mx-auto space-y-12">
                    
                    {/* CABECERA TÁCTICA */}
                    <div className="flex flex-col md:flex-row justify-between items-start md:items-center gap-8 animate-fade-in-down">
                        <div className="flex items-center gap-6">
                            <Link href={isTecnico ? route('tecnico.visits.in-progress', session.visit_certificate_id) : route('gestor.operations.visits.show', session.visit_certificate_id)} className="w-12 h-12 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center text-white/40 hover:text-white transition-all">
                                <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="3" d="M15 19l-7-7 7-7"/></svg>
                            </Link>
                            <div>
                                <h1 className="text-2xl font-black uppercase italic tracking-tighter leading-none">{session.title}</h1>
                                <p className="text-[10px] font-black text-blue-400 uppercase tracking-[0.3em] italic mt-2">Capacitación en Sede: {session.visit?.sede?.nombre_sede}</p>
                            </div>
                        </div>
                        <div className="flex items-center gap-4">
                            <div className="px-6 py-4 bg-black/40 border border-white/5 rounded-2xl text-center min-w-[120px]">
                                <div className="text-[8px] font-black text-white/20 uppercase italic">Asistentes</div>
                                <div className="text-xl font-black text-blue-500 italic">{session.attendees?.length || 0}</div>
                            </div>
                            <button 
                                type="button"
                                onClick={handleFinalizeSession}
                                className="px-6 py-4 bg-emerald-600/10 hover:bg-emerald-600 border border-emerald-500/20 hover:border-transparent text-emerald-400 hover:text-white rounded-2xl text-[10px] font-black uppercase tracking-widest italic shadow-lg hover:shadow-emerald-600/20 active:scale-95 transition-all flex items-center gap-2"
                            >
                                <svg className="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="2.5"><path strokeLinecap="round" strokeLinejoin="round" d="M5 13l4 4L19 7"/></svg>
                                Finalizar Charla
                            </button>
                        </div>
                    </div>

                    {/* BITÁCORA Y OBSERVACIONES (PREMIUM UI DARK) */}
                    <div className="bg-[#16162a]/40 backdrop-blur-md border border-white/5 rounded-[2.5rem] p-8 shadow-2xl relative group overflow-hidden animate-fade-in-down">
                        <div className="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-blue-500 to-indigo-500 opacity-20 group-hover:opacity-100 transition-opacity duration-300"></div>
                        
                        <form onSubmit={handleSaveDetails} className="space-y-6 relative z-10">
                            <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                                <h3 className="text-[10px] font-black text-blue-400 uppercase tracking-[0.3em] italic flex items-center gap-2">
                                    <svg className="w-4 h-4 shrink-0 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    Bitácora & Detalles de la Charla
                                </h3>
                                <button 
                                    type="submit" 
                                    disabled={detailsForm.processing}
                                    className="w-full sm:w-auto px-6 py-3 bg-blue-600 hover:brightness-110 disabled:opacity-50 text-white rounded-xl text-[9px] font-black uppercase tracking-widest italic transition-all flex items-center justify-center gap-1.5 shadow-lg shadow-blue-600/10 active:scale-95"
                                >
                                    {detailsForm.processing ? 'Guardando...' : 'Guardar Detalles'}
                                </button>
                            </div>

                            <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                <div className="relative group">
                                    <span className="absolute -top-2.5 left-4 bg-[#11111d] px-2 text-[8px] font-black text-blue-400/60 uppercase tracking-[0.3em] italic z-10">Temario / Descripción de Charla</span>
                                    <textarea 
                                        value={detailsForm.data.description}
                                        onChange={e => detailsForm.setData('description', e.target.value)}
                                        rows="2"
                                        className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-xs font-bold text-white focus:ring-2 focus:ring-blue-500/50 transition-all uppercase placeholder-white/5 italic"
                                        placeholder="Escriba los temas explicados en la capacitación..."
                                    />
                                </div>
                                <div className="relative group">
                                    <span className="absolute -top-2.5 left-4 bg-[#11111d] px-2 text-[8px] font-black text-blue-400/60 uppercase tracking-[0.3em] italic z-10">Observaciones / Notas de Campo</span>
                                    <textarea 
                                        value={detailsForm.data.notes}
                                        onChange={e => detailsForm.setData('notes', e.target.value)}
                                        rows="2"
                                        className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-xs font-bold text-white focus:ring-2 focus:ring-blue-500/50 transition-all uppercase placeholder-white/5 italic"
                                        placeholder="Registrar novedades, comentarios o notas operativas de la capacitación..."
                                    />
                                </div>
                            </div>
                        </form>
                    </div>

                    {!isAdding ? (
                        <div className="space-y-8 animate-fade-in-up">
                            {/* BOTÓN DE ACCESORIO GIGANTE PARA TABLETS */}
                            <button 
                                onClick={() => setIsAdding(true)} 
                                className="w-full py-12 bg-blue-600/10 border-2 border-dashed border-blue-500/40 rounded-[3rem] flex flex-col items-center justify-center gap-6 hover:bg-blue-600/20 active:scale-95 transition-all group"
                            >
                                <div className="w-20 h-20 bg-blue-600 rounded-full flex items-center justify-center shadow-2xl shadow-blue-600/40 group-hover:scale-110 transition-transform">
                                    <svg className="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="4" d="M12 4v16m8-8H4"/></svg>
                                </div>
                                <div className="text-center">
                                    <span className="text-xl font-black uppercase italic tracking-widest block">Registrar Nuevo Alumno</span>
                                    <span className="text-[10px] font-bold text-white/20 uppercase tracking-[0.3em] mt-2 block italic">Click para abrir panel de firma</span>
                                </div>
                            </button>

                            {/* LISTADO DE ASISTENTES */}
                            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                {session.attendees?.map((a, i) => (
                                    <div key={i} className="bg-[#16162a]/40 border border-white/5 rounded-[2.5rem] p-6 group hover:border-white/20 transition-all relative">
                                        <button 
                                            onClick={() => removeAttendee(i)} 
                                            className="absolute top-4 right-4 text-white/10 hover:text-red-500 transition-colors"
                                        >
                                            <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" strokeWidth="2.5"/></svg>
                                        </button>
                                        <div className="flex flex-col items-center text-center gap-4">
                                            <div className="w-full h-24 bg-white/5 rounded-2xl flex items-center justify-center p-4">
                                                <img src={a.signature} alt="Firma Alumno" className="max-h-full brightness-0 invert opacity-60" />
                                            </div>
                                            <div>
                                                <div className="text-sm font-black italic uppercase text-white">{a.name}</div>
                                                <div className="text-[9px] font-black text-white/20 uppercase tracking-widest mt-1 italic">
                                                    {a.position || 'OPERARIO'} • DNI: {a.dni || '-'}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </div>
                    ) : (
                        /* PANEL DE FIRMA EN VIVO */
                        <div className="animate-fade-in-up">
                            <div className="bg-[#16162a] border border-white/10 rounded-[3rem] p-8 lg:p-12 shadow-3xl space-y-10 relative overflow-hidden">
                                <div className="absolute inset-x-0 top-0 h-1.5 bg-blue-500"></div>
                                
                                <div className="flex justify-between items-center">
                                    <h2 className="text-2xl font-black text-white italic uppercase tracking-tighter flex items-center gap-4">
                                        <span className="w-2 h-10 bg-blue-500 rounded-full"></span>
                                        Toma de Datos & Firma
                                    </h2>
                                    <button onClick={() => setIsAdding(false)} className="px-6 py-2 bg-white/5 text-white/40 rounded-xl text-[9px] font-black uppercase tracking-widest italic font-black">X CANCELAR</button>
                                </div>

                                <form onSubmit={handleAddAttendee} className="space-y-10">
                                    <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
                                        <div className="relative group">
                                            <span className="absolute -top-2.5 left-4 bg-[#16162a] px-2 text-[8px] font-black text-blue-400 uppercase tracking-[0.3em] italic z-10">Nombre Completo del Alumno</span>
                                            <input id="name" name="name" 
                                                type="text" 
                                                value={form.data.name}
                                                onChange={e => form.setData('name', e.target.value)}
                                                autoFocus
                                                required
                                                className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-5 text-lg font-black text-white focus:ring-2 focus:ring-blue-500/50 transition-all uppercase placeholder-white/5 italic"
                                                placeholder="..."
                                            />
                                        </div>
                                        <div className="relative group">
                                            <span className="absolute -top-2.5 left-4 bg-[#16162a] px-2 text-[8px] font-black text-blue-400 uppercase tracking-[0.3em] italic z-10">DNI / Identificación</span>
                                            <input id="dni" name="dni" 
                                                type="text" 
                                                value={form.data.dni}
                                                onChange={e => form.setData('dni', e.target.value)}
                                                className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-5 text-lg font-black text-white focus:ring-2 focus:ring-blue-500/50 transition-all uppercase placeholder-white/5 italic"
                                                placeholder="..."
                                            />
                                        </div>
                                        <div className="relative group">
                                            <span className="absolute -top-2.5 left-4 bg-[#16162a] px-2 text-[8px] font-black text-blue-400 uppercase tracking-[0.3em] italic z-10">Puesto / Sector</span>
                                            <input id="position" name="position" 
                                                type="text" 
                                                value={form.data.position}
                                                onChange={e => form.setData('position', e.target.value)}
                                                className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-5 text-lg font-black text-white focus:ring-2 focus:ring-blue-500/50 transition-all uppercase placeholder-white/5 italic"
                                                placeholder="..."
                                            />
                                        </div>
                                    </div>

                                    <div className="space-y-6">
                                        <div className="flex justify-between items-center ml-4">
                                            <span className="text-[10px] font-black text-blue-400/40 uppercase tracking-[0.3em] italic">Capture su firma en el recuadro inferior</span>
                                            <button type="button" onClick={() => signaturePad.current?.clear()} className="text-[9px] font-black text-red-500 uppercase italic hover:underline">Limpiar Panel</button>
                                        </div>
                                        <div className="bg-white/5 border border-white/10 rounded-[3rem] overflow-hidden h-72 touch-none cursor-crosshair relative">
                                            <canvas ref={canvasRef} className="w-full h-full"></canvas>
                                            <div className="absolute inset-x-0 bottom-8 text-center pointer-events-none opacity-5">
                                                <span className="text-4xl font-black italic uppercase text-white tracking-[1em]">XAMASAFE</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div className="flex flex-col gap-4">
                                        <button 
                                            type="submit" 
                                            disabled={form.processing}
                                            className="w-full py-8 bg-blue-600 text-white rounded-[2.5rem] text-xl font-black uppercase tracking-[0.2em] italic transition-all shadow-2xl shadow-blue-600/20 hover:brightness-110 active:scale-95 disabled:opacity-50"
                                        >
                                            {form.processing ? 'Registrando...' : 'Confirmar y Guardar Asistente'}
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    )}
                </div>
            </div>

            {showFinalizeModal && (
                <div className="fixed inset-0 z-[100] overflow-y-auto flex items-center justify-center p-4 sm:p-6">
                    <div className="fixed inset-0 bg-black/95 backdrop-blur-2xl transition-opacity animate-fade-in" onClick={() => setShowFinalizeModal(false)}></div>
                    
                    <div className="relative bg-[#11111d] w-full max-w-lg rounded-[3rem] border border-white/10 shadow-3xl overflow-hidden animate-fade-in-down z-10">
                        <div className="absolute inset-x-0 top-0 h-1.5 bg-emerald-500"></div>
                        
                        <div className="p-8 sm:p-10 flex flex-col items-center text-center space-y-6">
                            <div className="h-16 w-16 bg-emerald-500/10 rounded-2xl border border-emerald-500/20 text-emerald-500 flex items-center justify-center rotate-3 shadow-inner hover:rotate-0 transition-all duration-500">
                                <svg className="w-8 h-8 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="2.5"><path strokeLinecap="round" strokeLinejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            </div>
                            
                            <div className="space-y-2">
                                <span className="text-[9px] font-black text-emerald-500 uppercase tracking-[0.3em] italic">Confirmación de Cierre</span>
                                <h3 className="text-xl font-black text-white uppercase italic tracking-tighter leading-none">¿Finalizar Capacitación?</h3>
                                <p className="text-xs text-white/50 max-w-sm italic">
                                    Se guardará esta charla y su listado de asistencia de forma permanente en la bitácora de la visita actual.
                                </p>
                            </div>
                            
                            <div className="grid grid-cols-2 gap-4 w-full pt-4">
                                <button 
                                    type="button"
                                    onClick={() => setShowFinalizeModal(false)}
                                    className="h-12 bg-white/5 hover:bg-white/10 text-white/60 hover:text-white rounded-xl text-[10px] font-black uppercase tracking-widest italic border border-white/5 transition-all"
                                >
                                    Cancelar
                                </button>
                                <button 
                                    type="button"
                                    onClick={confirmFinalizeSession}
                                    className="h-12 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-[10px] font-black uppercase tracking-widest italic shadow-lg shadow-emerald-600/20 active:scale-95 transition-all flex items-center justify-center gap-1.5"
                                >
                                    <svg className="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="3"><path strokeLinecap="round" strokeLinejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    Confirmar Cierre
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            )}

            <style>{`
                .animate-fade-in-down { animation: fade-in-down 0.6s ease-out forwards; }
                .animate-fade-in-up { animation: fade-in-up 0.5s ease-out forwards; }
                @keyframes fade-in-down { from { opacity: 0; transform: translateY(-30px); } to { opacity: 1; transform: translateY(0); } }
                @keyframes fade-in-up { from { opacity: 0; transform: translateY(40px); } to { opacity: 1; transform: translateY(0); } }
                canvas { image-rendering: -moz-crisp-edges; image-rendering: -webkit-optimize-contrast; image-rendering: crisp-edges; }
            `}</style>
        </AuthenticatedLayout>
    );
}
