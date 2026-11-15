import React from 'react';

export default function GeneratedPasswordModal({ password, copied, onCopy, onDismiss }) {
    if (!password) return null;

    return (
        <div className="fixed inset-0 z-50 bg-black/80 backdrop-blur-md flex items-center justify-center p-4 animate-fade-in">
            <div className="bg-[#16162a] border border-amber-500/40 rounded-3xl p-8 max-w-md w-full shadow-2xl space-y-6">
                <div className="flex items-center gap-3 text-amber-400 border-b border-white/10 pb-4">
                    <div className="w-10 h-10 rounded-xl bg-amber-500/20 border border-amber-500/30 flex items-center justify-center">
                        <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="2">
                            <path strokeLinecap="round" strokeLinejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z" />
                        </svg>
                    </div>
                    <div>
                        <h3 className="text-base font-black uppercase italic tracking-tight text-white">Contraseña Temporal Generada</h3>
                        <p className="text-[10px] font-bold text-amber-400 uppercase tracking-widest italic">Un solo uso · Cambio obligatorio</p>
                    </div>
                </div>

                <p className="text-xs text-gray-300 leading-relaxed">
                    Se ha asignado la siguiente clave temporal al Gestor Principal. Proporciónasela para que pueda ingresar por primera vez:
                </p>

                <div className="bg-black/60 border border-amber-500/30 rounded-2xl p-4 flex items-center justify-between gap-3">
                    <span className="font-mono text-lg font-black text-amber-400 tracking-wider select-all">{password}</span>
                    <button
                        type="button"
                        onClick={() => onCopy(password)}
                        className="px-4 py-2 bg-amber-500 text-black hover:bg-amber-400 text-[10px] font-black uppercase italic tracking-widest rounded-xl transition-all flex items-center gap-1.5 shadow-md"
                    >
                        {copied ? '¡Copiada!' : 'Copiar'}
                    </button>
                </div>

                <div className="bg-red-500/10 border border-red-500/20 rounded-xl p-3 text-[10px] font-bold text-red-400 leading-normal flex items-start gap-2">
                    <span className="text-sm">⚠️</span>
                    <span>
                        <strong>IMPORTANTE:</strong> Esta contraseña NO se volverá a mostrar en pantalla por motivos de seguridad. Guárdala antes de cerrar.
                    </span>
                </div>

                <div className="pt-2">
                    <button
                        type="button"
                        onClick={onDismiss}
                        className="w-full py-3 bg-white/10 hover:bg-white/20 text-white text-[11px] font-black uppercase italic tracking-widest rounded-xl transition-all"
                    >
                        Entendido, ya la copié
                    </button>
                </div>
            </div>
        </div>
    );
}
