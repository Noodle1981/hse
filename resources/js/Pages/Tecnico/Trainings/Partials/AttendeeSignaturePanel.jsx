import React from 'react';

export default function AttendeeSignaturePanel({
    form,
    signatureError,
    canvasRef,
    signaturePad,
    onCancel,
    onSubmit,
}) {
    return (
        <div className="animate-fade-in-up">
            <div className="bg-[#16162a] border border-white/10 rounded-[3rem] p-8 lg:p-12 shadow-3xl space-y-10 relative overflow-hidden">
                <div className="absolute inset-x-0 top-0 h-1.5 bg-blue-500"></div>

                <div className="flex justify-between items-center">
                    <h2 className="text-2xl font-black text-white italic uppercase tracking-tighter flex items-center gap-4">
                        <span className="w-2 h-10 bg-blue-500 rounded-full"></span>
                        Toma de Datos &amp; Firma
                    </h2>
                    <button onClick={onCancel} className="px-6 py-2 bg-white/5 text-white/40 rounded-xl text-[9px] font-black uppercase tracking-widest italic font-black">X CANCELAR</button>
                </div>

                <form onSubmit={onSubmit} className="space-y-10">
                    <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <div className="relative group">
                            <span className="absolute -top-2.5 left-4 bg-[#16162a] px-2 text-[8px] font-black text-blue-400 uppercase tracking-[0.3em] italic z-10">Nombre Completo del Alumno</span>
                            <input
                                id="name"
                                name="name"
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
                            <input
                                id="dni"
                                name="dni"
                                type="text"
                                value={form.data.dni}
                                onChange={e => form.setData('dni', e.target.value)}
                                className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-5 text-lg font-black text-white focus:ring-2 focus:ring-blue-500/50 transition-all uppercase placeholder-white/5 italic"
                                placeholder="..."
                            />
                        </div>
                        <div className="relative group">
                            <span className="absolute -top-2.5 left-4 bg-[#16162a] px-2 text-[8px] font-black text-blue-400 uppercase tracking-[0.3em] italic z-10">Puesto / Sector</span>
                            <input
                                id="position"
                                name="position"
                                type="text"
                                value={form.data.position}
                                onChange={e => form.setData('position', e.target.value)}
                                className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-5 text-lg font-black text-white focus:ring-2 focus:ring-blue-500/50 transition-all uppercase placeholder-white/5 italic"
                                placeholder="..."
                            />
                        </div>
                    </div>

                    <div className="space-y-6">
                        {signatureError && (
                            <div className="p-4 bg-red-500/10 border border-red-500/20 rounded-2xl flex items-center gap-3 animate-fade-in mx-4">
                                <svg className="w-5 h-5 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="2.5"><path strokeLinecap="round" strokeLinejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                                <span className="text-[10px] font-black text-red-500 uppercase tracking-widest italic leading-none">{signatureError}</span>
                            </div>
                        )}
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
    );
}
