import React from 'react';

export default function FinalizeTrainingModal({
    isOpen,
    onClose,
    session,
    elapsedSeconds,
    finalizeForm,
    responsibleCanvasRef,
    responsibleSigPad,
    onSubmit,
}) {
    if (!isOpen) return null;

    return (
        <div className="fixed inset-0 z-[100] overflow-y-auto flex items-center justify-center p-4 sm:p-6">
            <div className="fixed inset-0 bg-black/95 backdrop-blur-2xl transition-opacity animate-fade-in" onClick={onClose}></div>

            <div className="relative bg-[#11111d] w-full max-w-lg rounded-[3rem] border border-white/10 shadow-3xl overflow-hidden animate-fade-in-down z-10">
                <div className="absolute inset-x-0 top-0 h-1.5 bg-emerald-500"></div>

                <form onSubmit={onSubmit} className="p-8 sm:p-10 flex flex-col items-center text-center space-y-6">
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

                    <div className="w-full bg-white/5 border border-white/10 rounded-2xl p-4 text-left space-y-2 px-6 py-4">
                        <div className="flex justify-between items-center text-[10px] font-black uppercase tracking-widest italic text-white/40 leading-none">
                            <span>Tiempo Programado:</span>
                            <span className="text-indigo-400 font-black">{session.duration_minutes ? Math.round(session.duration_minutes) : 15} minutos</span>
                        </div>
                        <div className="flex justify-between items-center text-[10px] font-black uppercase tracking-widest italic text-white/40 leading-none">
                            <span>Tiempo Demorado (Cronómetro):</span>
                            <span className="text-blue-400 font-black">{Math.max(1, Math.round(elapsedSeconds / 60))} minutos</span>
                        </div>
                    </div>

                    <div className="w-full text-left space-y-6">
                        <div className="relative group">
                            <span className="absolute -top-2.5 left-4 bg-[#11111d] px-2 text-[8px] font-black text-emerald-400 uppercase tracking-[0.3em] italic z-10">Nombre de la Persona a Cargo</span>
                            <input
                                type="text"
                                value={finalizeForm.data.responsible_name}
                                onChange={e => finalizeForm.setData('responsible_name', e.target.value)}
                                required
                                className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-black text-white focus:ring-2 focus:ring-emerald-500/50 transition-all uppercase placeholder-white/5 italic"
                                placeholder="Escriba el nombre del responsable de sede..."
                            />
                        </div>

                        <div className="space-y-2">
                            <div className="flex justify-between items-center px-1">
                                <span className="text-[8px] font-black text-emerald-400 uppercase tracking-[0.3em] italic">Firma del Responsable / Autorizante</span>
                                <button type="button" onClick={() => responsibleSigPad.current?.clear()} className="text-[8px] font-black text-red-500 uppercase italic hover:underline">Limpiar Firma</button>
                            </div>
                            <div className="bg-white/5 border border-white/10 rounded-2xl overflow-hidden h-36 touch-none cursor-crosshair relative">
                                <canvas ref={responsibleCanvasRef} className="w-full h-full"></canvas>
                                <div className="absolute inset-x-0 bottom-4 text-center pointer-events-none opacity-[0.02]">
                                    <span className="text-lg font-black italic uppercase text-white tracking-[0.5em]">XAMASAFE FIRMA</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div className="grid grid-cols-2 gap-4 w-full pt-4">
                        <button
                            type="button"
                            onClick={onClose}
                            className="h-12 bg-white/5 hover:bg-white/10 text-white/60 hover:text-white rounded-xl text-[10px] font-black uppercase tracking-widest italic border border-white/5 transition-all"
                        >
                            Cancelar
                        </button>
                        <button
                            type="submit"
                            disabled={finalizeForm.processing}
                            className="h-12 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-[10px] font-black uppercase tracking-widest italic shadow-lg shadow-emerald-600/20 active:scale-95 transition-all flex items-center justify-center gap-1.5 disabled:opacity-50"
                        >
                            {finalizeForm.processing ? (
                                <svg className="w-4 h-4 animate-spin text-white" fill="none" viewBox="0 0 24 24"><circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4" /><path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" /></svg>
                            ) : (
                                <svg className="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="3"><path strokeLinecap="round" strokeLinejoin="round" d="M5 13l4 4L19 7" /></svg>
                            )}
                            <span>{finalizeForm.processing ? 'Guardando...' : 'Confirmar Cierre'}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    );
}
