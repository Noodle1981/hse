import React from 'react';
import { router } from '@inertiajs/react';

export function ResetPasswordModal({
    isOpen,
    userToReset,
    resetPassword,
    setResetPassword,
    copied,
    setCopied,
    onClose,
}) {
    if (!isOpen || !userToReset) return null;

    return (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-md animate-fade-in">
            <div className="w-full max-w-md bg-[#16162a] border border-white/10 rounded-[2.5rem] p-8 shadow-2xl relative">
                <button onClick={onClose} className="absolute top-6 right-6 text-white/40 hover:text-white transition-colors">✕</button>

                {!resetPassword ? (
                    <>
                        <div className="mb-6">
                            <h3 className="text-xs font-black text-amber-400 uppercase tracking-[0.4em] italic mb-1">Resetear Contraseña</h3>
                            <h4 className="text-lg font-black text-white uppercase italic tracking-tighter leading-none">
                                {userToReset.apellidos}, {userToReset.nombres}
                            </h4>
                        </div>
                        <p className="text-xs text-indigo-100/60 font-semibold leading-relaxed mb-8">
                            Se generará una contraseña temporal de 16 caracteres. El usuario deberá cambiarla en su próximo ingreso.
                        </p>
                        <div className="flex justify-end gap-3">
                            <button onClick={onClose} className="px-6 py-3 bg-white/5 text-white/40 hover:text-white rounded-2xl text-[10px] font-black uppercase tracking-widest italic transition-all">
                                Cancelar
                            </button>
                            <button
                                onClick={() => {
                                    router.post(route('gestor.users.reset-password', userToReset.id), {}, {
                                        onSuccess: (page) => {
                                            const pwd = page.props.flash?.generated_password;
                                            if (pwd) setResetPassword(pwd);
                                        }
                                    });
                                }}
                                className="px-6 py-3 bg-amber-500 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest italic transition-all hover:brightness-110"
                            >
                                Confirmar Reset
                            </button>
                        </div>
                    </>
                ) : (
                    <>
                        <div className="mb-6">
                            <h3 className="text-xs font-black text-emerald-400 uppercase tracking-[0.4em] italic mb-1">✓ Contraseña Generada</h3>
                            <h4 className="text-base font-black text-white uppercase italic tracking-tighter">{userToReset.apellidos}, {userToReset.nombres}</h4>
                        </div>
                        <p className="text-[10px] text-white/40 uppercase tracking-widest font-black italic mb-3">Contraseña Temporal (copia antes de cerrar)</p>
                        <div className="flex items-center gap-3 bg-black/40 border border-emerald-500/30 rounded-2xl px-5 py-4 mb-6">
                            <code className="flex-1 text-emerald-300 font-black text-sm tracking-wider">{resetPassword}</code>
                            <button
                                onClick={() => { navigator.clipboard.writeText(resetPassword); setCopied(true); setTimeout(() => setCopied(false), 2000); }}
                                className="px-3 py-1.5 bg-emerald-500/20 hover:bg-emerald-500 text-emerald-400 hover:text-white rounded-xl text-[9px] font-black uppercase transition-all"
                            >
                                {copied ? '✓ Copiado' : 'Copiar'}
                            </button>
                        </div>
                        <button onClick={onClose} className="w-full px-6 py-3 bg-white/5 hover:bg-white/10 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest italic transition-all">
                            Cerrar
                        </button>
                    </>
                )}
            </div>
        </div>
    );
}

export function DeleteUserModal({ isOpen, userToDelete, onClose, onConfirm }) {
    if (!isOpen) return null;

    return (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-4 animate-fade-in">
            <div className="absolute inset-0 bg-black/80 backdrop-blur-sm" onClick={onClose}></div>
            <div className="bg-[#16162a] border border-white/10 rounded-[3rem] p-10 max-w-lg w-full relative z-10 shadow-2xl text-center overflow-hidden">
                <div className="absolute inset-x-0 top-0 h-1 bg-red-500/50"></div>
                <div className="w-20 h-20 bg-red-500/20 rounded-3xl flex items-center justify-center mx-auto mb-6 text-red-500 border border-red-500/20">
                    <svg className="w-10 h-10 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={3} d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                </div>
                <h3 className="text-xl font-black text-white uppercase italic tracking-widest mb-4">Revocar Acceso</h3>
                <p className="text-sm text-white/40 mb-10 font-bold leading-relaxed italic uppercase">
                    ESTÁ A PUNTO DE DAR DE BAJA A <span className="text-red-400">{userToDelete?.nombres} {userToDelete?.apellidos}</span>.<br /><br />
                    EL REGISTRO NO SE ELIMINARÁ PERMANENTEMENTE PARA PRESERVAR EL HISTORIAL FORENSE.
                </p>
                <div className="flex justify-center gap-4">
                    <button onClick={onClose} className="px-8 py-4 bg-white/5 text-white/40 hover:text-white rounded-2xl text-[10px] font-black uppercase tracking-widest italic transition-all">
                        Cancelar
                    </button>
                    <button onClick={onConfirm} className="px-10 py-4 bg-red-600 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest italic transition-all shadow-lg animate-pulse">
                        Confirmar Baja
                    </button>
                </div>
            </div>
        </div>
    );
}

export function SignaturePreviewModal({ isOpen, previewUser, onClose }) {
    if (!isOpen || !previewUser) return null;

    return (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-4 animate-fade-in">
            <div className="absolute inset-0 bg-black/80 backdrop-blur-sm" onClick={onClose}></div>
            <div className="bg-[#16162a] border border-white/10 rounded-[3rem] p-10 max-w-lg w-full relative z-10 shadow-2xl text-center overflow-hidden">
                <div className="absolute inset-x-0 top-0 h-1 bg-amber-500/50"></div>
                <h3 className="text-xl font-black text-white uppercase italic tracking-widest mb-2">Visor de Firma</h3>
                <p className="text-[10px] text-white/40 uppercase tracking-widest mb-6 font-black italic">
                    Identidad: {previewUser.nombres} {previewUser.apellidos}
                </p>

                <div className="bg-white/5 border border-white/10 rounded-3xl p-6 mb-8 flex flex-col items-center justify-center relative">
                    <div className="absolute inset-0 opacity-5 pointer-events-none bg-[linear-gradient(to_right,#808080_1px,transparent_1px),linear-gradient(to_bottom,#808080_1px,transparent_1px)] bg-[size:14px_24px]"></div>

                    <div className="w-full h-32 bg-black/40 border border-white/5 rounded-2xl flex items-center justify-center p-4 relative z-10">
                        <img
                            src={`/storage/${previewUser.firma}`}
                            alt="Firma Aprobada"
                            className="max-h-full brightness-0 invert opacity-90"
                        />
                    </div>
                </div>

                <div className="flex flex-col gap-4 items-center mb-8">
                    <span className="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full text-[9px] font-black uppercase tracking-widest italic border text-emerald-400 border-emerald-500/30 bg-emerald-500/10 shadow-[0_0_15px_rgba(16,185,129,0.2)]">
                        <span className="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        Firma Oficial Aprobada
                    </span>
                    <span className="text-[9px] font-black text-white/30 uppercase tracking-widest italic">
                        Matrícula: {previewUser.matricula || 'N/A'}
                    </span>
                </div>

                <div className="flex justify-center">
                    <button onClick={onClose} className="px-10 py-4 bg-white/5 text-white/60 hover:text-white rounded-2xl text-[10px] font-black uppercase tracking-widest italic transition-all border border-white/5 hover:border-white/10">
                        Cerrar Visor
                    </button>
                </div>
            </div>
        </div>
    );
}
