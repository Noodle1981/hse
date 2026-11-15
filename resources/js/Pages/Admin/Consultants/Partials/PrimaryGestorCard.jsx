import React from 'react';

export default function PrimaryGestorCard({
    hasPrimaryGestor,
    primaryGestor,
    isEditingPrimary,
    setIsEditingPrimary,
    primaryForm,
    onSavePrimaryGestor,
    onResetPassword,
}) {
    return (
        <div className="bg-[#16162a]/40 backdrop-blur-xl border border-white/5 rounded-[2.5rem] p-8 sm:p-10 shadow-2xl space-y-6 mt-8">
            <div className="flex items-center justify-between border-b border-white/5 pb-4">
                <div>
                    <h3 className="text-sm font-black text-ep-primary uppercase tracking-widest italic">
                        Gestor Principal de Consultora
                    </h3>
                    <p className="text-[10px] font-bold text-white/40 uppercase italic tracking-wider mt-0.5">
                        {hasPrimaryGestor ? 'Usuario Administrador de la Consultora' : 'Asignar Administrador Principal'}
                    </p>
                </div>
                {hasPrimaryGestor && !isEditingPrimary && (
                    <div className="flex items-center gap-2">
                        <button
                            type="button"
                            onClick={() => setIsEditingPrimary(true)}
                            className="px-4 py-2 bg-white/5 border border-white/10 hover:bg-white/10 text-white text-[10px] font-black uppercase italic tracking-widest rounded-xl transition-all"
                        >
                            Editar Datos
                        </button>
                        <button
                            type="button"
                            onClick={onResetPassword}
                            className="px-4 py-2 bg-amber-500/10 border border-amber-500/30 hover:bg-amber-500/20 text-amber-400 text-[10px] font-black uppercase italic tracking-widest rounded-xl transition-all flex items-center gap-1.5"
                        >
                            <svg className="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="2.5">
                                <path strokeLinecap="round" strokeLinejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z" />
                            </svg>
                            Resetear Clave
                        </button>
                    </div>
                )}
            </div>

            {hasPrimaryGestor && !isEditingPrimary && primaryGestor && (
                <div className="bg-black/30 border border-white/5 rounded-2xl p-6 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                    <div className="flex items-center gap-4">
                        <div className="w-12 h-12 rounded-xl bg-ep-primary/20 border border-ep-primary/40 flex items-center justify-center text-ep-primary font-black text-lg">
                            {primaryGestor.nombres?.charAt(0)}{primaryGestor.apellidos?.charAt(0)}
                        </div>
                        <div>
                            <div className="flex items-center gap-2">
                                <h4 className="text-sm font-black text-white uppercase italic tracking-wider">
                                    {primaryGestor.nombres} {primaryGestor.apellidos}
                                </h4>
                                <span className="text-[9px] font-black uppercase tracking-widest px-2 py-0.5 rounded bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">
                                    Gestor Principal
                                </span>
                            </div>
                            <p className="text-xs text-white/60 font-mono mt-0.5">{primaryGestor.email}</p>
                        </div>
                    </div>

                    <div className="flex flex-wrap items-center gap-3">
                        {primaryGestor.must_change_password && (
                            <span className="text-[9px] font-black uppercase tracking-wider px-2.5 py-1 rounded-full bg-amber-500/10 text-amber-400 border border-amber-500/30 animate-pulse">
                                ⚠️ Pendiente cambio de contraseña
                            </span>
                        )}
                        <span className={`text-[9px] font-black uppercase tracking-wider px-2.5 py-1 rounded-full border ${
                            primaryGestor.estado === 'activo'
                                ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/30'
                                : 'bg-red-500/10 text-red-400 border-red-500/30'
                        }`}>
                            {primaryGestor.estado}
                        </span>
                    </div>
                </div>
            )}

            {(!hasPrimaryGestor || isEditingPrimary) && (
                <form onSubmit={onSavePrimaryGestor} className="space-y-4">
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label className="block text-[10px] font-black text-indigo-100/40 uppercase tracking-[0.2em] italic mb-1">Nombres *</label>
                            <input
                                type="text"
                                value={primaryForm.data.nombres}
                                onChange={e => primaryForm.setData('nombres', e.target.value)}
                                className="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-2.5 text-xs text-white focus:border-ep-primary focus:ring-0 transition-all font-black uppercase italic"
                                required
                            />
                            {primaryForm.errors.nombres && <p className="text-xs text-red-400 mt-1">{primaryForm.errors.nombres}</p>}
                        </div>
                        <div>
                            <label className="block text-[10px] font-black text-indigo-100/40 uppercase tracking-[0.2em] italic mb-1">Apellidos *</label>
                            <input
                                type="text"
                                value={primaryForm.data.apellidos}
                                onChange={e => primaryForm.setData('apellidos', e.target.value)}
                                className="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-2.5 text-xs text-white focus:border-ep-primary focus:ring-0 transition-all font-black uppercase italic"
                                required
                            />
                            {primaryForm.errors.apellidos && <p className="text-xs text-red-400 mt-1">{primaryForm.errors.apellidos}</p>}
                        </div>
                        <div>
                            <label className="block text-[10px] font-black text-indigo-100/40 uppercase tracking-[0.2em] italic mb-1">Email (Usuario) *</label>
                            <input
                                type="email"
                                value={primaryForm.data.email}
                                onChange={e => primaryForm.setData('email', e.target.value)}
                                className="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-2.5 text-xs text-white focus:border-ep-primary focus:ring-0 transition-all font-mono"
                                required
                            />
                            {primaryForm.errors.email && <p className="text-xs text-red-400 mt-1">{primaryForm.errors.email}</p>}
                        </div>
                        <div>
                            <label className="block text-[10px] font-black text-indigo-100/40 uppercase tracking-[0.2em] italic mb-1">Teléfono</label>
                            <input
                                type="text"
                                value={primaryForm.data.telefono}
                                onChange={e => primaryForm.setData('telefono', e.target.value)}
                                className="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-2.5 text-xs text-white focus:border-ep-primary focus:ring-0 transition-all font-mono"
                            />
                            {primaryForm.errors.telefono && <p className="text-xs text-red-400 mt-1">{primaryForm.errors.telefono}</p>}
                        </div>
                    </div>

                    <div className="flex justify-end gap-3 pt-2">
                        {isEditingPrimary && (
                            <button
                                type="button"
                                onClick={() => setIsEditingPrimary(false)}
                                className="px-5 py-2.5 bg-white/5 border border-white/10 text-white/60 hover:text-white text-[10px] font-black uppercase italic tracking-widest rounded-xl transition-all"
                            >
                                Cancelar
                            </button>
                        )}
                        <button
                            type="submit"
                            disabled={primaryForm.processing}
                            className="px-6 py-2.5 bg-ep-primary hover:brightness-110 text-white text-[10px] font-black uppercase italic tracking-widest rounded-xl shadow-lg shadow-ep-primary/20 transition-all flex items-center gap-2"
                        >
                            <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="2.5">
                                <path strokeLinecap="round" strokeLinejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                            </svg>
                            {primaryForm.processing ? 'Guardando...' : hasPrimaryGestor ? 'Actualizar Datos' : 'Crear Gestor Principal'}
                        </button>
                    </div>
                </form>
            )}
        </div>
    );
}
