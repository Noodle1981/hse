import React from 'react';

export function CategoryModal({ isOpen, onClose, form, onSubmit }) {
    if (!isOpen) return null;

    return (
        <div className="fixed inset-0 z-[100] flex items-center justify-center bg-black/80 backdrop-blur-sm p-4">
            <div className="bg-[#16162a] border border-white/10 rounded-[2rem] w-full max-w-lg p-8 shadow-2xl relative">
                <div className="absolute top-4 right-4">
                    <button onClick={onClose} className="w-8 h-8 flex items-center justify-center text-white/40 hover:text-white bg-white/5 rounded-full hover:bg-white/10 transition-colors">
                        <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" strokeWidth="2" /></svg>
                    </button>
                </div>
                <h3 className="text-xl font-black italic uppercase text-white mb-6">Nueva Categoría</h3>
                <form onSubmit={onSubmit} className="space-y-6">
                    <div className="flex flex-col gap-1.5">
                        <label className="pl-3 text-[8px] font-black text-orange-400 uppercase tracking-[0.3em] italic">Código</label>
                        <input type="text" value={form.data.code} onChange={e => form.setData('code', e.target.value)} required className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm text-white focus:ring-2 focus:ring-orange-500/50 outline-none" placeholder="Ej. SEG-01" />
                    </div>
                    <div className="flex flex-col gap-1.5">
                        <label className="pl-3 text-[8px] font-black text-orange-400 uppercase tracking-[0.3em] italic">Nombre</label>
                        <input type="text" value={form.data.name} onChange={e => form.setData('name', e.target.value)} required className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm text-white focus:ring-2 focus:ring-orange-500/50 outline-none" placeholder="Ej. Seguridad" />
                    </div>
                    <div className="flex gap-4">
                        <div className="flex-1 flex flex-col gap-1.5">
                            <label className="pl-3 text-[8px] font-black text-orange-400 uppercase tracking-[0.3em] italic">Color Hex</label>
                            <input type="text" value={form.data.color} onChange={e => form.setData('color', e.target.value)} className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm text-white focus:ring-2 focus:ring-orange-500/50 outline-none" />
                        </div>
                    </div>
                    <button type="submit" disabled={form.processing} className="w-full py-4 bg-orange-600 text-white rounded-2xl font-black uppercase tracking-widest text-sm hover:brightness-110 transition-all shadow-[0_0_15px_rgba(246,146,57,0.3)]">
                        {form.processing ? 'Guardando...' : 'Crear Categoría'}
                    </button>
                </form>
            </div>
        </div>
    );
}

export function SubcategoryModal({ isOpen, onClose, form, onSubmit }) {
    if (!isOpen) return null;

    return (
        <div className="fixed inset-0 z-[100] flex items-center justify-center bg-black/80 backdrop-blur-sm p-4">
            <div className="bg-[#16162a] border border-white/10 rounded-[2rem] w-full max-w-lg p-8 shadow-2xl relative">
                <div className="absolute top-4 right-4">
                    <button onClick={onClose} className="w-8 h-8 flex items-center justify-center text-white/40 hover:text-white bg-white/5 rounded-full hover:bg-white/10 transition-colors">
                        <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" strokeWidth="2" /></svg>
                    </button>
                </div>
                <h3 className="text-xl font-black italic uppercase text-white mb-6">Nueva Sub-Clasificación</h3>
                <form onSubmit={onSubmit} className="space-y-6">
                    <div className="flex flex-col gap-1.5">
                        <label className="pl-3 text-[8px] font-black text-orange-400 uppercase tracking-[0.3em] italic">Código</label>
                        <input type="text" value={form.data.code} onChange={e => form.setData('code', e.target.value)} required className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm text-white focus:ring-2 focus:ring-orange-500/50 outline-none" placeholder="Ej. SUB-01" />
                    </div>
                    <div className="flex flex-col gap-1.5">
                        <label className="pl-3 text-[8px] font-black text-orange-400 uppercase tracking-[0.3em] italic">Nombre</label>
                        <input type="text" value={form.data.name} onChange={e => form.setData('name', e.target.value)} required className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm text-white focus:ring-2 focus:ring-orange-500/50 outline-none" placeholder="Ej. Maquinaria Pesada" />
                    </div>
                    <button type="submit" disabled={form.processing} className="w-full py-4 bg-orange-600 text-white rounded-2xl font-black uppercase tracking-widest text-sm hover:brightness-110 transition-all shadow-[0_0_15px_rgba(246,146,57,0.3)]">
                        {form.processing ? 'Guardando...' : 'Crear Sub-Clasificación'}
                    </button>
                </form>
            </div>
        </div>
    );
}
