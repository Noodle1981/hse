import React from 'react';

export default function UserFormModal({
    isOpen,
    onClose,
    editingId,
    form,
    onSubmit,
    onOpenResetPassword,
}) {
    if (!isOpen) return null;

    return (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-4 animate-fade-in overflow-y-auto">
            <div className="absolute inset-0 bg-black/80 backdrop-blur-sm" onClick={onClose}></div>
            <div className="bg-[#16162a] border border-white/10 rounded-[3rem] p-8 max-w-4xl w-full relative z-10 shadow-2xl overflow-hidden">
                <div className="absolute inset-0 bg-gradient-to-br from-[#F69239]/5 to-transparent"></div>
                <h3 className="text-xl font-black text-white italic mb-8 uppercase tracking-widest relative z-10 flex items-center gap-3">
                    <span className="w-2 h-8 bg-[#F69239] rounded-full"></span>
                    {editingId ? 'Actualizar Perfil' : 'Registro de Identidad'}
                </h3>

                <form onSubmit={onSubmit} className="relative z-10 space-y-8">
                    {form.data.foto && (
                        <div className="flex items-center gap-3 bg-white/5 border border-white/5 p-3 rounded-2xl max-w-sm animate-fade-in">
                            <div className="w-12 h-12 rounded-xl border border-[#F69239]/30 overflow-hidden shrink-0">
                                <img src={`/storage/${form.data.foto}`} alt="Foto de Perfil" className="w-full h-full object-cover" />
                            </div>
                            <div>
                                <span className="text-[8px] font-black text-[#F69239] uppercase tracking-widest block italic mb-0.5">Identidad Digital</span>
                                <span className="text-xs font-bold text-white uppercase italic">{form.data.nombres} {form.data.apellidos}</span>
                            </div>
                        </div>
                    )}

                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div className="space-y-2">
                            <label className="text-[9px] font-black text-white/40 uppercase tracking-[0.3em] ml-4">Nombres</label>
                            <input
                                id="nombres"
                                name="nombres"
                                type="text"
                                value={form.data.nombres}
                                onChange={e => form.setData('nombres', e.target.value)}
                                required
                                className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-[#F69239] transition-all"
                            />
                            {form.errors.nombres && <span className="text-red-500 text-[10px] font-black italic ml-4">{form.errors.nombres}</span>}
                        </div>
                        <div className="space-y-2">
                            <label className="text-[9px] font-black text-white/40 uppercase tracking-[0.3em] ml-4">Apellidos</label>
                            <input
                                id="apellidos"
                                name="apellidos"
                                type="text"
                                value={form.data.apellidos}
                                onChange={e => form.setData('apellidos', e.target.value)}
                                required
                                className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-[#F69239] transition-all"
                            />
                            {form.errors.apellidos && <span className="text-red-500 text-[10px] font-black italic ml-4">{form.errors.apellidos}</span>}
                        </div>
                        <div className="space-y-2">
                            <label className="text-[9px] font-black text-white/40 uppercase tracking-[0.3em] ml-4">Email Corporativo</label>
                            <input
                                id="email"
                                name="email"
                                type="email"
                                value={form.data.email}
                                onChange={e => form.setData('email', e.target.value.toLowerCase())}
                                required
                                className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-[#F69239] transition-all lowercase"
                            />
                            {form.errors.email && <span className="text-red-500 text-[10px] font-black italic ml-4">{form.errors.email}</span>}
                        </div>
                        {!editingId && (
                            <div className="space-y-2">
                                <label className="text-[9px] font-black text-white/40 uppercase tracking-[0.3em] ml-4">Contraseña</label>
                                <input
                                    id="password"
                                    name="password"
                                    type="password"
                                    value={form.data.password}
                                    onChange={e => form.setData('password', e.target.value)}
                                    required
                                    className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-[#F69239] transition-all"
                                />
                                {form.errors.password && <span className="text-red-500 text-[10px] font-black italic ml-4">{form.errors.password}</span>}
                            </div>
                        )}
                        <div className="space-y-2">
                            <label className="text-[9px] font-black text-white/40 uppercase tracking-[0.3em] ml-4">Rol Sistema</label>
                            <select
                                id="rol"
                                name="rol"
                                value={form.data.rol}
                                onChange={e => form.setData('rol', e.target.value)}
                                className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-[#F69239] transition-all [color-scheme:dark] italic uppercase"
                            >
                                <option value="tecnico">TÉCNICO OPERATIVO</option>
                                <option value="gestor">GESTOR ADMINISTRATIVO</option>
                            </select>
                        </div>
                        <div className="space-y-2">
                            <label className="text-[9px] font-black text-white/40 uppercase tracking-[0.3em] ml-4">Matrícula ID</label>
                            <input
                                id="matricula"
                                name="matricula"
                                type="text"
                                value={form.data.matricula}
                                onChange={e => form.setData('matricula', e.target.value.toUpperCase())}
                                required
                                className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-[#F69239] transition-all uppercase"
                            />
                            {form.errors.matricula && <span className="text-red-500 text-[10px] font-black italic ml-4">{form.errors.matricula}</span>}
                        </div>
                        <div className="space-y-2">
                            <label className="text-[9px] font-black text-white/40 uppercase tracking-[0.3em] ml-4">Teléfono Enlace</label>
                            <input
                                id="telefono"
                                name="telefono"
                                type="text"
                                value={form.data.telefono}
                                onChange={e => form.setData('telefono', e.target.value)}
                                required
                                className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-[#F69239] transition-all uppercase"
                            />
                            {form.errors.telefono && <span className="text-red-500 text-[10px] font-black italic ml-4">{form.errors.telefono}</span>}
                        </div>
                        <div className="space-y-2">
                            <label className="text-[9px] font-black text-white/40 uppercase tracking-[0.3em] ml-4">Nº Documento (Opcional)</label>
                            <input
                                id="n_doc"
                                name="n_doc"
                                type="text"
                                value={form.data.n_doc}
                                onChange={e => form.setData('n_doc', e.target.value.toUpperCase())}
                                className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-[#F69239] transition-all uppercase"
                            />
                            {form.errors.n_doc && <span className="text-red-500 text-[10px] font-black italic ml-4">{form.errors.n_doc}</span>}
                        </div>
                        <div className="space-y-2">
                            <label className="text-[9px] font-black text-white/40 uppercase tracking-[0.3em] ml-4">Estado</label>
                            <select
                                id="estado"
                                name="estado"
                                value={form.data.estado}
                                onChange={e => form.setData('estado', e.target.value)}
                                className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-[#F69239] transition-all [color-scheme:dark] italic uppercase"
                            >
                                <option value="activo">ACTIVO</option>
                                <option value="inactivo">INACTIVO</option>
                            </select>
                        </div>
                    </div>

                    <div className="flex justify-between items-center gap-4 pt-4">
                        {editingId ? (
                            <button
                                type="button"
                                onClick={onOpenResetPassword}
                                className="px-5 py-4 bg-amber-500/10 hover:bg-amber-500/20 text-amber-400 text-[9px] font-black uppercase italic tracking-widest rounded-2xl border border-amber-500/20 hover:border-amber-500/40 transition-all inline-flex items-center gap-2"
                            >
                                🔑 Resetear Clave
                            </button>
                        ) : <div />}

                        <div className="flex gap-4">
                            <button type="button" onClick={onClose} className="px-8 py-4 bg-white/5 text-white/40 hover:text-white rounded-2xl text-[10px] font-black uppercase tracking-widest italic transition-all">
                                Abortar Operación
                            </button>
                            <button type="submit" disabled={form.processing} className="px-10 py-4 bg-[#F69239] text-white rounded-2xl text-[10px] font-black uppercase tracking-widest italic transition-all shadow-lg shadow-[#F69239]/20 hover:brightness-110">
                                {form.processing ? 'Procesando...' : (editingId ? 'Confirmar Actualización' : 'Inyectar Usuario')}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    );
}
