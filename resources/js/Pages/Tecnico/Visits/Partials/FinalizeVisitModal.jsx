import React from 'react';

export default function FinalizeVisitModal({
    isOpen,
    onClose,
    finalizeForm,
    signatureRef,
    onClearSignature,
    onSubmit,
}) {
    if (!isOpen) return null;

    return (
        <div className="fixed inset-0 z-[100] overflow-y-auto flex items-center justify-center p-4 sm:p-6">
            <div className="fixed inset-0 bg-black/95 backdrop-blur-2xl transition-opacity animate-fade-in" onClick={onClose}></div>

            <div className="relative bg-[#11111d] w-full max-w-2xl rounded-[3rem] border border-white/10 shadow-3xl overflow-hidden animate-fade-in-down">
                <form onSubmit={onSubmit} className="px-8 pt-10 pb-8 sm:px-10">
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
                                <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
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
                                    <button type="button" onClick={onClearSignature} className="text-[8px] font-black text-white/20 hover:text-red-500 uppercase tracking-widest italic transition-colors">Limpiar Sello</button>
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
                        <button type="button" onClick={onClose} className="w-full sm:w-auto h-12 px-10 bg-white/5 text-white text-[9px] font-black uppercase tracking-widest italic rounded-xl border border-white/10 hover:bg-white/10 transition-all text-center">Abortar</button>
                    </div>
                </form>
            </div>
        </div>
    );
}
