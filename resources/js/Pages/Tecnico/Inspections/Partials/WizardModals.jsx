import React from 'react';

export function FailureInterceptModal({ show, onRejectAndFinish, onContinue }) {
    if (!show) return null;

    return (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-6 animate-fade-in">
            <div className="absolute inset-0 bg-[#0a0a15]/80 backdrop-blur-xl" />
            <div className="relative w-full max-w-lg bg-[#1a1a2e]/80 backdrop-blur-xl border border-white/10 rounded-3xl shadow-2xl overflow-hidden">
                <div className="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-transparent via-red-500 to-transparent" />
                <div className="p-10 space-y-8 text-center">
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

                    <div className="space-y-3">
                        <h2 className="text-xs font-black text-red-400 uppercase tracking-[0.4em] italic" style={{ textShadow: '0 0 20px rgba(239,68,68,0.5)' }}>
                            ⚠️ Desvío Detectado
                        </h2>
                        <p className="text-[10px] font-black text-indigo-100/40 uppercase tracking-[0.2em] italic leading-relaxed">
                            Se ha registrado una falla crítica en esta sección.<br />
                            ¿Desea suspender y rechazar el activo ahora,<br />o continuar evaluando el resto de los puntos?
                        </p>
                    </div>

                    <div className="h-px w-full bg-red-500/20 rounded-full overflow-hidden relative">
                        <div className="absolute inset-0 bg-gradient-to-r from-transparent via-red-500/40 to-transparent w-1/2 animate-scan" />
                    </div>

                    <div className="flex flex-col gap-4 pt-2">
                        <button
                            type="button"
                            onClick={onRejectAndFinish}
                            className="w-full px-6 py-4 bg-red-500/20 text-red-400 border border-red-500/50 rounded-2xl text-[10px] font-black uppercase tracking-widest italic transition-all active:scale-95 animate-pulse hover:animate-none hover:bg-red-500 hover:text-white relative overflow-hidden group"
                            style={{ boxShadow: '0 0 15px rgba(239,68,68,0.3)' }}
                        >
                            <div className="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000" />
                            🚫 Rechazar y Finalizar
                        </button>
                        <button
                            type="button"
                            onClick={onContinue}
                            className="w-full px-6 py-4 bg-white/5 text-white hover:bg-white/10 rounded-2xl text-[10px] font-black uppercase tracking-widest italic transition-all active:scale-95"
                        >
                            Continuar Inspección
                        </button>
                    </div>
                </div>
            </div>
        </div>
    );
}

export function ReinspectAssetModal({ show, onConfirm, onCancel }) {
    if (!show) return null;

    return (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-6 animate-fade-in">
            <div className="absolute inset-0 bg-[#0a0a15]/80 backdrop-blur-xl" />
            <div className="relative w-full max-w-lg bg-[#1a1a2e]/80 backdrop-blur-xl border border-white/10 rounded-3xl shadow-2xl overflow-hidden">
                <div className="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-transparent via-amber-500 to-transparent" />
                <div className="p-10 space-y-8 text-center">
                    <div className="flex justify-center">
                        <div className="relative h-20 w-20 flex items-center justify-center">
                            <div className="absolute inset-0 rounded-full border-2 border-amber-500/20 border-t-amber-500 animate-spin" />
                            <div className="h-14 w-14 rounded-2xl bg-amber-500/10 border border-amber-500/30 flex items-center justify-center text-amber-400 shadow-inner rotate-3">
                                <svg className="w-7 h-7 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="2">
                                    <path strokeLinecap="round" strokeLinejoin="round" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div className="space-y-3">
                        <h2 className="text-xs font-black text-amber-400 uppercase tracking-[0.4em] italic" style={{ textShadow: '0 0 20px rgba(245,158,11,0.5)' }}>
                            ⚠️ Activo ya Auditado
                        </h2>
                        <p className="text-[10px] font-black text-indigo-100/40 uppercase tracking-[0.2em] italic leading-relaxed">
                            Este activo ya fue auditado en la visita actual.<br />
                            Si continúas, generarás un nuevo reporte de inspección independiente para este equipo.<br /><br />
                            ¿Deseas volver a auditarlo?
                        </p>
                    </div>

                    <div className="flex flex-col gap-4 pt-2">
                        <button
                            type="button"
                            onClick={onConfirm}
                            className="w-full px-6 py-4 bg-amber-500/20 text-amber-400 border border-amber-500/50 rounded-2xl text-[10px] font-black uppercase tracking-widest italic transition-all active:scale-95 hover:bg-amber-500 hover:text-white"
                            style={{ boxShadow: '0 0 15px rgba(245,158,11,0.3)' }}
                        >
                            ✓ Sí, volver a auditar
                        </button>
                        <button
                            type="button"
                            onClick={onCancel}
                            className="w-full px-6 py-4 bg-white/5 text-white hover:bg-white/10 rounded-2xl text-[10px] font-black uppercase tracking-widest italic transition-all active:scale-95"
                        >
                            Cancelar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    );
}
