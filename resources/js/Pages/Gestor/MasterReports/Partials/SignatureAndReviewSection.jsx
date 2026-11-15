import React from 'react';
import { Link } from '@inertiajs/react';

export default function SignatureAndReviewSection({
    auth,
    report,
    data,
    setData,
    errors,
    processing,
    onSubmit,
    onApprove,
    onObserve,
}) {
    const tecnicoFirma = report.tecnico?.firma || (auth.user.is_technician ? auth.user.firma : null);
    const tecnicoNombre = report.tecnico
        ? `${report.tecnico.nombres} ${report.tecnico.apellidos || ''}`
        : `${auth.user.nombres} ${auth.user.apellidos || ''}`;

    return (
        <>
            <div className="bg-ep-dark/40 backdrop-blur-md p-10 sm:p-14 rounded-[4rem] border border-white/5 shadow-2xl relative overflow-hidden group">
                <div className="flex items-center mb-12 pb-8 border-b border-white/10">
                    <div className="w-16 h-16 bg-white/5 rounded-2xl flex items-center justify-center text-white font-black text-3xl border border-white/10 shadow-inner group-hover:bg-ep-primary/20 transition-all italic">5</div>
                    <div className="ml-8">
                        <h3 className="text-3xl font-black text-white uppercase italic tracking-tight">Validación Táctica</h3>
                        <p className="text-[10px] font-black text-white/20 uppercase tracking-[0.4em] mt-1">Sello Digital del Especialista</p>
                    </div>
                </div>

                {tecnicoFirma ? (
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-16 items-center">
                        <div>
                            <div className="inline-flex items-center gap-3 px-4 py-1.5 bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-[9px] font-black uppercase tracking-widest rounded-xl mb-6">
                                <div className="w-2 h-2 bg-emerald-400 rounded-full animate-ping"></div>
                                Firma Autorizada Vinculada
                            </div>
                            <h4 className="text-xl font-bold text-white mb-3">Certificación de Perfil Activa</h4>
                            <p className="text-[11px] text-indigo-100/40 italic font-medium leading-relaxed uppercase tracking-[0.2em] mb-4">
                                El informe maestro será validado utilizando la rúbrica registrada en el perfil del técnico certificante.
                            </p>
                            <div className="text-xs font-black text-white uppercase italic tracking-tighter">
                                TÉCNICO: <span className="text-ep-primary">{tecnicoNombre}</span>
                            </div>
                        </div>

                        <div className="bg-black/60 shadow-[inset_0_0_60px_rgba(0,0,0,0.9)] border-2 border-white/5 rounded-[4rem] p-10 flex flex-col justify-center items-center relative overflow-hidden">
                            <span className="block text-[8px] font-black text-indigo-100/20 uppercase tracking-[0.5em] mb-6 italic">RÚBRICA DIGITAL EN PERFIL</span>
                            <div className="p-6 bg-white/5 border border-white/10 rounded-3xl w-full max-w-[280px]">
                                <img src={`/storage/${tecnicoFirma}`} alt="Firma del técnico" className="h-28 object-contain brightness-0 invert opacity-90 mx-auto" />
                            </div>
                            <div className="mt-6 text-[8px] text-white/30 font-black uppercase tracking-[0.6em] italic animate-pulse">Sello Verificado SRE</div>
                        </div>
                    </div>
                ) : (
                    <div className="bg-red-500/5 border border-red-500/20 rounded-[2.5rem] p-10 sm:p-14">
                        <div className="flex flex-col md:flex-row gap-8 items-center">
                            <div className="w-16 h-16 rounded-2xl bg-red-500/10 border border-red-500/30 flex items-center justify-center text-red-500 text-3xl font-black italic">!</div>
                            <div className="flex-grow">
                                <h4 className="text-xl font-bold text-white mb-2 uppercase italic tracking-tight">Firma Digital Requerida</h4>
                                <p className="text-sm text-indigo-100/50 italic leading-relaxed mb-4">
                                    El técnico certificante <strong className="text-white font-black">{tecnicoNombre}</strong> no cuenta con una firma digital registrada en su perfil.
                                </p>
                                <div className="flex flex-wrap items-center gap-6 mt-6">
                                    <span className="px-3 py-1 bg-red-500/10 border border-red-500/20 text-red-400 text-[9px] font-black uppercase tracking-widest rounded-lg">ESTADO: BLOQUEADO</span>
                                    {auth.user.is_technician && (
                                        <Link href="/profile" className="px-6 py-2.5 bg-red-600/20 border border-red-500/30 hover:bg-red-600 text-white text-[10px] font-black uppercase tracking-widest italic rounded-xl transition-all">
                                            Ir a mi Perfil para Cargar Firma
                                        </Link>
                                    )}
                                </div>
                            </div>
                        </div>
                    </div>
                )}
            </div>

            {!auth.user.is_technician && report.estado === 'finalizado' && (
                <div className="bg-[#141421] p-10 sm:p-14 rounded-[4rem] shadow-[0_0_80px_rgba(0,0,0,0.6)] border-t-8 border-indigo-500 relative overflow-hidden">
                    <div className="flex items-center mb-12 pb-8 border-b border-white/10">
                        <div className="w-16 h-16 bg-indigo-600 rounded-2xl flex items-center justify-center text-white font-black text-3xl shadow-[0_0_30px_rgba(99,102,241,0.4)] rotate-3 italic">6</div>
                        <div className="ml-8">
                            <h3 className="text-3xl font-black text-white uppercase italic tracking-tight">Evaluación del Gestor</h3>
                            <p className="text-[10px] font-black text-indigo-400 uppercase tracking-[0.4em] mt-2 italic">Control de Calidad y Retroalimentación Operativa</p>
                        </div>
                    </div>
                    <div className="space-y-6">
                        <p className="text-[11px] text-indigo-100/40 uppercase tracking-[0.2em] font-black italic px-4">
                            Si decide observar el informe, detalle los puntos a corregir. Si aprueba el informe, este comentario se limpiará automáticamente.
                        </p>
                        <textarea
                            id="field_gestor_obs"
                            name="field_gestor_obs"
                            value={data.observacion_revision}
                            onChange={e => setData('observacion_revision', e.target.value)}
                            rows={6}
                            className="w-full bg-black/60 border border-white/10 rounded-[2.5rem] text-white placeholder-white/10 shadow-inner focus:ring-2 focus:ring-indigo-500 transition-all text-sm font-semibold p-8 italic leading-relaxed outline-none"
                            placeholder="Redacte aquí las observaciones técnicas..."
                        />
                        {errors.observacion_revision && (
                            <div className="mt-4 px-6 text-red-500 text-xs font-black uppercase italic tracking-[0.2em] animate-pulse">{errors.observacion_revision}</div>
                        )}
                    </div>
                </div>
            )}

            <div className="fixed bottom-10 left-0 right-0 z-50 px-8 pointer-events-none">
                <div className="max-w-7xl mx-auto flex justify-center pointer-events-auto">
                    <div className="bg-ep-dark/80 backdrop-blur-3xl p-5 rounded-[2.8rem] border border-white/10 shadow-[0_-30px_60px_rgba(0,0,0,0.7)] flex items-center gap-8">
                        {report.estado === 'aprobado' ? (
                            <div className="flex items-center gap-4 px-10 py-5 bg-emerald-500/10 border border-emerald-500/30 rounded-2xl">
                                <div className="w-3 h-3 bg-emerald-400 rounded-full animate-ping"></div>
                                <span className="text-[11px] font-black text-emerald-400 uppercase tracking-[0.3em] italic">INFORME APROBADO Y REGISTRADO</span>
                            </div>
                        ) : !auth.user.is_technician ? (
                            report.estado === 'finalizado' ? (
                                <div className="flex items-center gap-6">
                                    <button onClick={onObserve} disabled={processing} className="px-10 py-5 bg-red-600/10 hover:bg-red-600/20 text-red-400 text-[11px] font-black uppercase tracking-[0.2em] italic rounded-2xl border border-red-500/30 transition-all shadow-xl">
                                        OBSERVAR & PEDIR REVISIÓN
                                    </button>
                                    <button onClick={onApprove} disabled={processing} className="h-20 px-16 bg-emerald-500 text-white text-sm font-black uppercase tracking-[0.3em] italic rounded-3xl shadow-[0_0_40px_rgba(16,185,129,0.3)] hover:scale-105 transition-all flex items-center gap-5 disabled:opacity-50">
                                        APROBAR INFORME MAESTRO
                                    </button>
                                </div>
                            ) : (
                                <div className="flex items-center gap-4 px-10 py-5 bg-indigo-500/10 border border-indigo-500/30 rounded-2xl">
                                    <span className="text-[11px] font-black text-indigo-400 uppercase tracking-[0.3em] italic">MODO LECTURA ACTIVO: EN REDACCIÓN</span>
                                </div>
                            )
                        ) : ['en_redaccion', 'solicitado'].includes(report.estado) ? (
                            <div className="flex items-center gap-6">
                                <button onClick={() => onSubmit(false)} disabled={processing} className="px-12 py-5 bg-white/5 hover:bg-white/10 text-white text-[11px] font-black uppercase tracking-[0.2em] italic rounded-2xl border border-white/10 transition-all shadow-xl">
                                    {processing ? 'GUARDANDO...' : 'GUARDAR BORRADOR'}
                                </button>
                                <button onClick={() => onSubmit(true)} disabled={processing} className="h-20 px-16 bg-ep-primary text-white text-sm font-black uppercase tracking-[0.3em] italic rounded-3xl shadow-[0_0_40px_rgba(246,146,57,0.3)] hover:scale-105 transition-all flex items-center gap-5 disabled:opacity-50">
                                    {processing ? 'GENERANDO...' : 'GENERAR INFORME FINAL'}
                                </button>
                            </div>
                        ) : (
                            <div className="flex items-center gap-4 px-10 py-5 bg-blue-500/10 border border-blue-500/30 rounded-2xl">
                                <span className="text-[11px] font-black text-blue-400 uppercase tracking-[0.3em] italic">MODO LECTURA ACTIVO: EN REVISIÓN</span>
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </>
    );
}
