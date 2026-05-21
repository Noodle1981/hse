import { router, Head, Link, useForm, usePage } from '@inertiajs/react';
import AuthenticatedLayout from '../../../Layouts/AuthenticatedLayout';
import { useState } from 'react';

export default function Subcategories({ category, subcategories }) {
    const { auth, flash } = usePage().props;
    
    const [showForm, setShowForm] = useState(false);
    const [editingId, setEditingId] = useState(null);

    const form = useForm({
        id: null,
        code: '',
        name: '',
        description: '',
        is_active: true,
    });

    const openCreate = () => {
        form.reset();
        form.clearErrors();
        setEditingId(null);
        setShowForm(true);
    };

    const openEdit = (sub) => {
        form.clearErrors();
        setEditingId(sub.id);
        form.setData({
            id: sub.id,
            code: sub.code || '',
            name: sub.name || '',
            description: sub.description || '',
            is_active: sub.is_active === 1 || sub.is_active === true,
        });
        setShowForm(true);
    };

    const submit = (e) => {
        e.preventDefault();
        form.post(route('gestor.categories.subcategories.save', category.id), {
            onSuccess: () => setShowForm(false),
        });
    };

    const handleToggleStatus = (id) => {
        router.post(route('gestor.categories.toggle', ['subcategory', id]), {}, {
            preserveScroll: true,
        });
    };

    return (
        <AuthenticatedLayout title="Bóveda de Subcategorías">
            <Head title={`Subcategorías - ${category.name}`} />

            <div className="px-4 sm:px-6 lg:px-8 py-8 text-white selection:bg-orange-500/30">
                <div className="max-w-[95rem] mx-auto space-y-8 animate-fade-in">
                    
                    {/* Header Hub */}
                    <div className="bg-[#16162a]/40 backdrop-blur-md rounded-[2.5rem] border border-white/5 p-6 shadow-2xl relative overflow-hidden animate-fade-in-down">
                        <div className="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-orange-500/30 to-transparent"></div>
                        
                        <div className="flex flex-col lg:flex-row gap-6 justify-between items-center relative z-10">
                            <div className="flex items-center gap-6">
                                <Link href={route('gestor.categories.index')} className="w-12 h-12 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center text-white/40 hover:text-white hover:border-white/20 transition-all">
                                    <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="3" d="M15 19l-7-7 7-7"/></svg>
                                </Link>
                                <div>
                                    <h2 className="text-xl font-black text-white italic uppercase tracking-tight leading-none bg-gradient-to-r from-white to-white/40 bg-clip-text text-transparent">{category.name}</h2>
                                    <p className="text-[9px] font-black text-orange-400 uppercase tracking-widest italic mt-1.5">Matriz Principal: {category.code}</p>
                                </div>
                            </div>

                            <button onClick={openCreate} 
                                   className="flex h-12 px-8 bg-orange-600 text-white hover:brightness-110 active:scale-95 rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] italic transition-all items-center gap-3 shadow-[0_0_20px_rgba(246,146,57,0.3)]">
                                <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="3">
                                    <path d="M12 4v16m8-8H4"/>
                                </svg>
                                Nueva Subcategoría
                            </button>
                        </div>
                    </div>

                    {/* Subcategories Grid */}
                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 animate-fade-in-up">
                        {subcategories.map(sub => (
                            <div key={sub.id} className="group bg-[#16162a]/60 backdrop-blur-md rounded-[2.5rem] border border-white/5 p-8 shadow-2xl relative overflow-hidden hover:bg-white/5 transition-all duration-500">
                                <div className="absolute top-0 right-0 p-6 flex gap-2 translate-x-10 opacity-0 group-hover:translate-x-0 group-hover:opacity-100 transition-all duration-500">
                                    <button onClick={() => openEdit(sub)} className="w-9 h-9 bg-blue-500/10 border border-blue-500/20 text-blue-400 hover:bg-blue-500 hover:text-white rounded-xl flex items-center justify-center transition-all shadow-lg">
                                        <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" strokeWidth="1.5"/></svg>
                                    </button>
                                    <button onClick={() => handleToggleStatus(sub.id)} className={`w-9 h-9 border shadow-lg rounded-xl flex items-center justify-center transition-all ${sub.is_active ? 'bg-red-500/10 border-red-500/20 text-red-500 hover:bg-red-500 hover:text-white' : 'bg-emerald-500/10 border-emerald-500/20 text-emerald-500 hover:bg-emerald-500 hover:text-white'}`}>
                                        {sub.is_active ? (
                                            <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" strokeWidth="2"/></svg>
                                        ) : (
                                            <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" strokeWidth="2"/></svg>
                                        )}
                                    </button>
                                </div>

                                <div className="space-y-4">
                                    <div className="inline-flex px-3 py-1 bg-orange-500/10 border border-orange-500/20 rounded-lg text-[9px] font-black text-orange-400 uppercase tracking-widest italic">
                                        SUB: {sub.code}
                                    </div>
                                    <div>
                                        <h4 className={`text-lg font-black italic uppercase tracking-tight group-hover:text-orange-400 transition-colors ${!sub.is_active && 'opacity-30'}`}>{sub.name}</h4>
                                        <p className="text-[10px] font-bold text-white/20 italic mt-2 uppercase leading-relaxed group-hover:text-white/40 transition-colors">{sub.description || 'Sin descripción táctica registrada'}</p>
                                    </div>
                                    <div className="pt-4 flex items-center justify-between border-t border-white/5">
                                        <StatusBadge active={sub.is_active} text={sub.is_active ? 'ACTIVA' : 'BLOQUEADA'} />
                                        <div className="text-[8px] font-black text-white/10 uppercase tracking-widest italic">ID: {sub.id}</div>
                                    </div>
                                </div>
                            </div>
                        ))}
                    </div>
                </div>
            </div>

            {/* Form Modal */}
            {showForm && (
                <div className="fixed inset-0 z-50 flex items-center justify-center p-4 animate-fade-in overflow-y-auto">
                    <div className="absolute inset-0 bg-black/85 backdrop-blur-md" onClick={() => setShowForm(false)}></div>
                    <div className="bg-[#16162a] border border-white/10 rounded-[3rem] p-8 max-w-2xl w-full relative z-10 shadow-2xl overflow-hidden">
                        <div className="absolute inset-x-0 top-0 h-1 bg-orange-500/50"></div>
                        <h3 className="text-xl font-black text-white italic mb-8 uppercase tracking-widest flex items-center gap-3">
                            <span className="w-2 h-8 bg-orange-500 rounded-full"></span>
                            {editingId ? 'Parametrizar Subcategoría' : 'Nueva Inyección de Clasificación'}
                        </h3>
                        
                        <form onSubmit={submit} className="space-y-6">
                            <div className="grid grid-cols-2 gap-6">
                                <div className="space-y-2">
                                    <label className="text-[9px] font-black text-white/40 uppercase tracking-[0.3em] ml-4">Código</label>
                                    <input id="code" name="code" type="text" value={form.data.code} onChange={e => form.setData('code', e.target.value)} required 
                                        className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-orange-500 transition-all uppercase"/>
                                </div>
                                <div className="space-y-2">
                                    <label className="text-[9px] font-black text-white/40 uppercase tracking-[0.3em] ml-4">Nombre</label>
                                    <input id="name" name="name" type="text" value={form.data.name} onChange={e => form.setData('name', e.target.value)} required 
                                        className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-orange-500 transition-all uppercase"/>
                                </div>
                            </div>
                            
                            <div className="space-y-2">
                                <label className="text-[9px] font-black text-white/40 uppercase tracking-[0.3em] ml-4">Descripción / Función</label>
                                <textarea id="description" name="description" value={form.data.description} onChange={e => form.setData('description', e.target.value)} rows="4"
                                    className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-orange-500 transition-all uppercase"></textarea>
                            </div>

                            <div className="flex items-center gap-4 bg-black/20 px-6 py-4 rounded-2xl border border-white/5">
                                <label className="inline-flex items-center cursor-pointer group">
                                    <input id="is_active" name="is_active" type="checkbox" checked={form.data.is_active} onChange={e => form.setData('is_active', e.target.checked)} className="sr-only peer" />
                                    <div className="relative w-11 h-6 bg-white/5 border border-white/10 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white/20 after:border-white/10 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500/50"></div>
                                    <span className="ms-3 text-[10px] font-black text-white/40 uppercase tracking-widest italic group-hover:text-emerald-400 transition-colors">Estado Operativo</span>
                                </label>
                            </div>

                            <div className="flex justify-end gap-4 pt-4">
                                <button type="button" onClick={() => setShowForm(false)} className="px-8 py-4 bg-white/5 text-white/40 hover:text-white rounded-2xl text-[10px] font-black uppercase tracking-widest italic transition-all">Abortar</button>
                                <button type="submit" disabled={form.processing} className="px-10 py-4 bg-orange-600 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest italic transition-all shadow-lg hover:brightness-110">
                                    {form.processing ? 'Procesando...' : (editingId ? 'Confirmar Cambios' : 'Registrar')}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            )}

            <style>{`
                .animate-fade-in { animation: fade-in 0.4s ease-out forwards; }
                .animate-fade-in-down { animation: fade-in-down 0.6s ease-out forwards; }
                .animate-fade-in-up { animation: fade-in-up 0.5s ease-out forwards; }
                @keyframes fade-in { from { opacity: 0; } to { opacity: 1; } }
                @keyframes fade-in-down { from { opacity: 0; transform: translateY(-30px); } to { opacity: 1; transform: translateY(0); } }
                @keyframes fade-in-up { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
            `}</style>
        </AuthenticatedLayout>
    );
}

function StatusBadge({ active, text }) {
    return (
        <span className={`inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full text-[7px] font-black uppercase tracking-widest italic border ${active ? 'text-emerald-400 border-emerald-500/30 bg-emerald-500/10' : 'text-white/20 border-white/5 bg-white/5'}`}>
            <span className={`w-1 h-1 rounded-full ${active ? 'bg-emerald-500 animate-pulse' : 'bg-white/20'}`}></span>
            {text}
        </span>
    );
}
