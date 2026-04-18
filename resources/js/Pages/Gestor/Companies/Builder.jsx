import { Head, useForm, usePage } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { useState } from 'react';

export default function Builder({ empresa }) {
    const { auth, flash } = usePage().props;
    
    const form = useForm({
        id: empresa?.id || null,
        nombre: empresa?.nombre || '',
        razon_social: empresa?.razon_social || '',
        cuit: empresa?.cuit || '',
        direccion: empresa?.direccion || '',
        email: empresa?.email || '',
        primary_color: empresa?.primary_color || '#F69239',
        logo: null,
    });

    const submit = (e) => {
        e.preventDefault();
        // Usamos post con _method si fuera necesario, pero el controlador acepta POST para save
        form.post(route('gestor.companies.save'), {
            forceFormData: true,
        });
    };

    return (
        <AuthenticatedLayout title="Configurador Corporativo">
            <Head title={empresa ? 'Editar Empresa' : 'Nueva Empresa'} />

            <div className="px-4 sm:px-6 lg:px-8 py-8 text-white selection:bg-blue-500/30">
                <div className="max-w-5xl mx-auto">
                    <div className="bg-[#16162a]/40 backdrop-blur-md rounded-[3rem] border border-white/5 overflow-hidden shadow-2xl relative animate-fade-in-up">
                        <div className="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-transparent via-blue-500 to-transparent"></div>
                        
                        <div className="px-10 py-8 border-b border-white/5 bg-white/5 flex items-center justify-between">
                            <h3 className="text-xl font-black text-white italic uppercase tracking-widest flex items-center gap-4">
                                <span className="w-2 h-8 bg-blue-500 rounded-full"></span>
                                {empresa ? 'Actualización de Entidad' : 'Inyección de Nueva Empresa'}
                            </h3>
                            <div className="flex items-center gap-3">
                                <div className="h-2 w-2 rounded-full bg-blue-500 animate-pulse shadow-[0_0_10px_#3b82f6]"></div>
                                <span className="text-[10px] font-black text-blue-400 uppercase tracking-widest italic leading-none">Terminal Activa</span>
                            </div>
                        </div>

                        <div className="p-10">
                            <form onSubmit={submit} className="space-y-10">
                                <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
                                    {/* Datos Básicos */}
                                    <div className="space-y-6">
                                        <div className="relative group">
                                            <span className="absolute -top-2.5 left-4 bg-[#11111d] px-2 text-[8px] font-black text-blue-400 uppercase tracking-[0.3em] italic z-10">Nombre Comercial</span>
                                            <input type="text" value={form.data.nombre} onChange={e => form.setData('nombre', e.target.value)} required
                                                className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-blue-500/50 transition-all uppercase"/>
                                            {form.errors.nombre && <span className="text-red-500 text-[9px] font-black italic ml-4 mt-1 block">{form.errors.nombre}</span>}
                                        </div>

                                        <div className="relative group">
                                            <span className="absolute -top-2.5 left-4 bg-[#11111d] px-2 text-[8px] font-black text-blue-400 uppercase tracking-[0.3em] italic z-10">Razón Social</span>
                                            <input type="text" value={form.data.razon_social} onChange={e => form.setData('razon_social', e.target.value)} required
                                                className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-blue-500/50 transition-all uppercase"/>
                                            {form.errors.razon_social && <span className="text-red-500 text-[9px] font-black italic ml-4 mt-1 block">{form.errors.razon_social}</span>}
                                        </div>

                                        <div className="relative group">
                                            <span className="absolute -top-2.5 left-4 bg-[#11111d] px-2 text-[8px] font-black text-blue-400 uppercase tracking-[0.3em] italic z-10">CUIT Fiscal</span>
                                            <input type="text" value={form.data.cuit} onChange={e => form.setData('cuit', e.target.value)} required
                                                className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-blue-500/50 transition-all placeholder-white/5" placeholder="00-00000000-0"/>
                                            {form.errors.cuit && <span className="text-red-500 text-[9px] font-black italic ml-4 mt-1 block">{form.errors.cuit}</span>}
                                        </div>
                                    </div>

                                    {/* Contacto y Ubicación */}
                                    <div className="space-y-6">
                                        <div className="relative group">
                                            <span className="absolute -top-2.5 left-4 bg-[#11111d] px-2 text-[8px] font-black text-blue-400 uppercase tracking-[0.3em] italic z-10">Dirección Fiscal</span>
                                            <input type="text" value={form.data.direccion} onChange={e => form.setData('direccion', e.target.value)}
                                                className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-blue-500/50 transition-all uppercase"/>
                                        </div>

                                        <div className="relative group">
                                            <span className="absolute -top-2.5 left-4 bg-[#11111d] px-2 text-[8px] font-black text-blue-400 uppercase tracking-[0.3em] italic z-10">Email de Enlace Corporativo</span>
                                            <input type="email" value={form.data.email} onChange={e => form.setData('email', e.target.value)}
                                                className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-blue-500/50 transition-all uppercase"/>
                                            {form.errors.email && <span className="text-red-500 text-[9px] font-black italic ml-4 mt-1 block">{form.errors.email}</span>}
                                        </div>

                                        <div className="grid grid-cols-2 gap-4">
                                            <div className="relative group">
                                                <span className="absolute -top-2.5 left-4 bg-[#11111d] px-2 text-[8px] font-black text-blue-400 uppercase tracking-[0.3em] italic z-10">Color Primario</span>
                                                <div className="flex items-center gap-3 bg-black/40 border border-white/10 rounded-2xl px-4 py-3">
                                                    <input type="color" value={form.data.primary_color} onChange={e => form.setData('primary_color', e.target.value)}
                                                        className="w-10 h-10 bg-transparent border-none cursor-pointer p-0"/>
                                                    <span className="text-sm font-bold text-white/60 uppercase">{form.data.primary_color}</span>
                                                </div>
                                            </div>
                                            <div className="relative group">
                                                <span className="absolute -top-2.5 left-4 bg-[#11111d] px-2 text-[8px] font-black text-blue-400 uppercase tracking-[0.3em] italic z-10">Logo Corporativo</span>
                                                <input type="file" onChange={e => form.setData('logo', e.target.files[0])}
                                                    className="w-full h-full opacity-0 absolute inset-0 cursor-pointer z-20"/>
                                                <div className="bg-black/40 border border-white/10 rounded-2xl px-4 py-3 text-[10px] font-black text-white/20 uppercase tracking-widest italic flex items-center justify-center h-full">
                                                    {form.data.logo ? form.data.logo.name : 'Subir Archivo'}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div className="pt-10 flex justify-end gap-5">
                                    <button type="button" onClick={() => window.history.back()}
                                        className="px-10 py-5 bg-white/5 text-white/40 hover:text-white rounded-[2rem] text-[11px] font-black uppercase tracking-widest italic transition-all">
                                        Abortar
                                    </button>
                                    <button type="submit" disabled={form.processing}
                                        className="px-14 py-5 bg-blue-600 text-white rounded-[2rem] text-[11px] font-black uppercase tracking-[0.1em] italic transition-all shadow-xl shadow-blue-600/20 hover:brightness-110 active:scale-95 disabled:opacity-30">
                                        {form.processing ? 'Sincronizando...' : (empresa ? 'Actualizar Matriz de Datos' : 'Inyectar Entidad Corporativa')}
                                    </button>
                                </div>
                            </form>
                        </div>
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
