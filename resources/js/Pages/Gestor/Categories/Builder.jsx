import { router, Head, useForm, usePage } from '@inertiajs/react';
import AuthenticatedLayout from '../../../Layouts/AuthenticatedLayout';
import { useState } from 'react';

const ICONS = {
    'shield-check': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />',
    'helmet': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />',
    'alert': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />',
    'fire': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z" />',
    'truck': '<path d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 100-4 2 2 0 000 4zm10 0a2 2 0 100-4 2 2 0 000 4z" />',
    'tool': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />',
    'doc': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />',
    'eye': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />',
    'environment': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />',
};

export default function Builder({ category }) {
    const { auth, flash } = usePage().props;
    
    const form = useForm({
        id: category?.id || null,
        code: category?.code || '',
        name: category?.name || '',
        description: category?.description || '',
        color: category?.color || '#F69239',
        icon: category?.icon || 'shield-check',
        is_active: category ? category.is_active : true,
        order: category?.order || 0,
    });

    const submit = (e) => {
        e.preventDefault();
        form.post(route('gestor.categories.save'));
    };

    return (
        <AuthenticatedLayout title="Constructor de Clasificación">
            <Head title={category ? 'Editar Categoría' : 'Nueva Categoría'} />

            <div className="px-4 sm:px-6 lg:px-8 py-8 text-white selection:bg-orange-500/30">
                <div className="max-w-4xl mx-auto space-y-8">
                    
                    <div className="bg-[#16162a]/40 backdrop-blur-md rounded-[3rem] border border-white/5 shadow-2xl relative overflow-hidden animate-fade-in-up">
                        <div className="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-transparent via-orange-500 to-transparent"></div>
                        
                        <div className="px-10 py-8 border-b border-white/5 bg-white/5 flex items-center justify-between">
                            <h3 className="text-xl font-black text-white italic uppercase tracking-widest flex items-center gap-4">
                                <span className="w-2 h-8 bg-orange-500 rounded-full"></span>
                                {category ? 'Actualización de Matriz' : 'Inyectar Nueva Categoría'}
                            </h3>
                            <button onClick={() => window.history.back()} className="text-[10px] font-black text-white/20 hover:text-white uppercase tracking-widest italic transition-all">Regresar</button>
                        </div>

                        <form onSubmit={submit} className="p-10 space-y-10">
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div className="space-y-6">
                                    <div className="relative group">
                                        <span className="absolute -top-2.5 left-4 bg-[#11111d] px-2 text-[8px] font-black text-orange-400 uppercase tracking-[0.3em] italic z-10">Código Táctico</span>
                                        <input id="code" name="code" type="text" value={form.data.code} onChange={e => form.setData('code', e.target.value)} required 
                                            className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-orange-500/50 transition-all uppercase"/>
                                    </div>

                                    <div className="relative group">
                                        <span className="absolute -top-2.5 left-4 bg-[#11111d] px-2 text-[8px] font-black text-orange-400 uppercase tracking-[0.3em] italic z-10">Nombre de Matriz</span>
                                        <input id="name" name="name" type="text" value={form.data.name} onChange={e => form.setData('name', e.target.value)} required 
                                            className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-orange-500/50 transition-all uppercase"/>
                                        {form.errors.name && <span className="text-red-500 text-[9px] font-black italic ml-4 mt-1 block">{form.errors.name}</span>}
                                    </div>

                                    <div className="relative group">
                                        <span className="absolute -top-2.5 left-4 bg-[#11111d] px-2 text-[8px] font-black text-orange-400 uppercase tracking-[0.3em] italic z-10">Identificador Visual (Icono)</span>
                                        <div className="grid grid-cols-5 gap-3 bg-black/40 border border-white/10 rounded-2xl p-4">
                                            {Object.entries(ICONS).map(([key, svg]) => (
                                                <button 
                                                    key={key} 
                                                    type="button" 
                                                    onClick={() => form.setData('icon', svg)}
                                                    className={`w-12 h-12 rounded-xl border flex items-center justify-center transition-all ${form.data.icon === svg ? 'bg-orange-500/20 border-orange-500 text-orange-400' : 'bg-white/5 border-white/5 text-white/20 hover:bg-white/10'}`}
                                                >
                                                    <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" dangerouslySetInnerHTML={{ __html: svg }} />
                                                </button>
                                            ))}
                                        </div>
                                    </div>
                                </div>

                                <div className="space-y-6">
                                    <div className="relative group">
                                        <span className="absolute -top-2.5 left-4 bg-[#11111d] px-2 text-[8px] font-black text-orange-400 uppercase tracking-[0.3em] italic z-10">Descripción General</span>
                                        <textarea id="description" name="description" value={form.data.description} onChange={e => form.setData('description', e.target.value)} rows="5"
                                            className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-orange-500/50 transition-all uppercase"></textarea>
                                    </div>

                                    <div className="grid grid-cols-2 gap-6">
                                        <div className="relative group">
                                            <span className="absolute -top-2.5 left-4 bg-[#11111d] px-2 text-[8px] font-black text-orange-400 uppercase tracking-[0.3em] italic z-10">Cromatismo</span>
                                            <input id="color" name="color" type="color" value={form.data.color} onChange={e => form.setData('color', e.target.value)} 
                                                className="w-full h-14 bg-black/40 border border-white/10 rounded-2xl p-2 cursor-pointer"/>
                                        </div>
                                        <div className="relative group">
                                            <span className="absolute -top-2.5 left-4 bg-[#11111d] px-2 text-[8px] font-black text-orange-400 uppercase tracking-[0.3em] italic z-10">Prioridad / Orden</span>
                                            <input id="order" name="order" type="number" value={form.data.order} onChange={e => form.setData('order', e.target.value)} 
                                                className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-orange-500/50 transition-all"/>
                                        </div>
                                    </div>

                                    <div className="flex items-center gap-4 bg-black/20 px-6 py-4 rounded-2xl border border-white/5">
                                        <label className="inline-flex items-center cursor-pointer group">
                                            <input id="is_active" name="is_active" type="checkbox" checked={form.data.is_active} onChange={e => form.setData('is_active', e.target.checked)} className="sr-only peer" />
                                            <div className="relative w-11 h-6 bg-white/5 border border-white/10 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white/20 after:border-white/10 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500/50"></div>
                                            <span className="ms-3 text-[10px] font-black text-white/40 uppercase tracking-widest italic group-hover:text-emerald-400 transition-colors">Estado Operativo</span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div className="pt-10 flex justify-end gap-5">
                                <button type="submit" disabled={form.processing} 
                                    className="px-14 py-5 bg-orange-600 text-white rounded-[2rem] text-[11px] font-black uppercase tracking-[0.1em] italic transition-all shadow-xl shadow-orange-600/20 hover:brightness-110 active:scale-95 disabled:opacity-30">
                                    {form.processing ? 'Sincronizando...' : (category ? 'Actualizar Clasificación' : 'Confirmar Inyección')}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <style>{`
                .animate-fade-in-up { animation: fade-in-up 0.5s ease-out forwards; }
                @keyframes fade-in-up { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
            `}</style>
        </AuthenticatedLayout>
    );
}
