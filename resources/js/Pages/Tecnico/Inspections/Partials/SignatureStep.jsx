import React from 'react';

export default function SignatureStep({
    form,
    user,
    supervisorSigRef,
    supervisorPad,
    onSubmit,
}) {
    return (
        <form onSubmit={onSubmit} className="p-12 space-y-12 animate-fade-in">
            <div className="border-b border-white/5 pb-8">
                <h2 className="text-2xl font-black italic uppercase tracking-tighter">Certificación de Inspección</h2>
                <p className="text-xs font-bold text-white/80 uppercase mt-2 italic tracking-widest">Capture las firmas para concluir el protocolo.</p>
            </div>

            <div className="space-y-10">
                <div className="grid grid-cols-1 md:grid-cols-2 gap-12">
                    <div className="space-y-6">
                        <div className="group">
                            <label htmlFor="supervisor_name" className="block text-[10px] font-black text-orange-400 uppercase tracking-[0.3em] italic mb-3 ml-2">
                                Operario / Personal chequeado o revisado
                            </label>
                            <input
                                id="supervisor_name"
                                name="supervisor_name"
                                type="text"
                                value={form.data.supervisor_name}
                                onChange={e => form.setData('supervisor_name', e.target.value)}
                                required
                                className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-orange-500 transition-all uppercase"
                            />
                        </div>
                        <div className="space-y-4">
                            <div className="flex items-center justify-between ml-4 mr-2">
                                <span className="text-[10px] font-black text-white/80 uppercase tracking-[0.3em] italic">Firma Responsable</span>
                                <button
                                    type="button"
                                    onClick={() => supervisorPad.current?.clear()}
                                    className="flex items-center gap-1.5 text-[10px] font-black text-white/80 hover:text-red-400 uppercase tracking-widest italic transition-colors"
                                >
                                    <svg className="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
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
                    <textarea
                        id="notes"
                        name="notes"
                        value={form.data.notes}
                        onChange={e => form.setData('notes', e.target.value)}
                        rows="3"
                        className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-orange-500 transition-all uppercase"
                    />
                </div>
            </div>

            <div className="pt-8">
                <button
                    type="submit"
                    disabled={form.processing}
                    className="w-full py-6 bg-emerald-600 text-white rounded-[2.5rem] text-xs font-black uppercase tracking-[0.3em] italic shadow-2xl shadow-emerald-600/20 hover:brightness-110 active:scale-95 transition-all"
                >
                    Certificar Protocolo
                </button>
            </div>
        </form>
    );
}
