import React from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, useForm, Link } from '@inertiajs/react';

export default function Create({ auth, sedes, tecnicos, initial_sede_id, initial_periodo }) {
    const { data, setData, post, processing, errors } = useForm({
        sede_id: initial_sede_id || '',
        user_id: auth.user.rol === 'Técnico' ? auth.user.id : '',
        titulo: 'Informe Maestro de Servicio - ' + new Date().toLocaleDateString('es-ES', { month: 'long', year: 'numeric' }).toUpperCase(),
        periodo: initial_periodo || new Date().toISOString().substring(0, 7), // YYYY-MM
        frecuencia: 'mensual',
    });

    const submit = (e) => {
        e.preventDefault();
        post(route('master-reports.store'));
    };

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={<h2 className="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Solicitar Informe</h2>}
        >
            <Head title="Solicitar Informe Maestro" />

            <div className="py-12 bg-[#11111d] min-h-screen">
                <div className="max-w-4xl mx-auto sm:px-6 lg:px-8">
                    
                    <div className="mb-10 flex items-center gap-4">
                        <Link href={route('master-reports.index')} className="p-3 bg-white/5 rounded-xl border border-white/10 text-indigo-100 hover:text-ep-primary transition-colors">
                            <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M15 19l-7-7 7-7"/></svg>
                        </Link>
                        <div>
                            <h1 className="text-3xl font-black text-white uppercase italic tracking-tighter">Nueva <span className="text-ep-primary">Solicitud</span></h1>
                            <p className="text-indigo-100/40 text-[10px] font-black uppercase tracking-[0.2em] mt-1">Configuración inicial del informe maestro</p>
                        </div>
                    </div>

                    <div className="bg-ep-dark/60 backdrop-blur-xl p-8 sm:p-12 rounded-[2.5rem] border border-white/5 shadow-2xl relative overflow-hidden">
                        <div className="absolute top-0 right-0 p-8 opacity-10">
                            <svg className="w-32 h-32 text-ep-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="1" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </div>

                        <form onSubmit={submit} className="space-y-8 relative z-10">
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
                                {/* Sede */}
                                <div className="space-y-3">
                                    <label className="block text-[10px] font-black text-indigo-100/30 uppercase tracking-widest italic px-2">Sede / Cliente Operativo</label>
                                    <select id="field_5" name="field_5" 
                                        value={data.sede_id}
                                        onChange={e => setData('sede_id', e.target.value)}
                                        className="w-full bg-black/40 border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-ep-primary transition-all"
                                    >
                                        <option value="">Seleccione una sede</option>
                                        {sedes.map(sede => (
                                            <option key={sede.id} value={sede.id}>{sede.nombre_sede} ({sede.empresa?.nombre})</option>
                                        ))}
                                    </select>
                                    {errors.sede_id && <span className="text-red-500 text-[10px] font-black uppercase italic tracking-widest px-2">{errors.sede_id}</span>}
                                </div>

                                {/* Técnico */}
                                <div className="space-y-3">
                                    <label className="block text-[10px] font-black text-indigo-100/30 uppercase tracking-widest italic px-2">Especialista Asignado</label>
                                    <select id="field_4" name="field_4" 
                                        value={data.user_id}
                                        onChange={e => setData('user_id', e.target.value)}
                                        className="w-full bg-black/40 border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-ep-primary transition-all"
                                    >
                                        <option value="">Seleccione un técnico</option>
                                        {tecnicos.map(t => (
                                            <option key={t.id} value={t.id}>{t.nombres} {t.apellidos}</option>
                                        ))}
                                    </select>
                                    {errors.user_id && <span className="text-red-500 text-[10px] font-black uppercase italic tracking-widest px-2">{errors.user_id}</span>}
                                </div>

                                {/* Título */}
                                <div className="space-y-3 md:col-span-2">
                                    <label className="block text-[10px] font-black text-indigo-100/30 uppercase tracking-widest italic px-2">Título de Identificación</label>
                                    <input id="field_3" name="field_3" 
                                        type="text"
                                        value={data.titulo}
                                        onChange={e => setData('titulo', e.target.value)}
                                        placeholder="Ej: Reporte Mensual de Seguridad"
                                        className="w-full bg-black/40 border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white placeholder-white/5 focus:ring-2 focus:ring-ep-primary transition-all"
                                    />
                                    {errors.titulo && <span className="text-red-500 text-[10px] font-black uppercase italic tracking-widest px-2">{errors.titulo}</span>}
                                </div>

                                {/* Periodo */}
                                <div className="space-y-3">
                                    <label className="block text-[10px] font-black text-indigo-100/30 uppercase tracking-widest italic px-2">Periodo de Auditoría</label>
                                    <input id="field_2" name="field_2" 
                                        type="month"
                                        value={data.periodo}
                                        onChange={e => setData('periodo', e.target.value)}
                                        className="w-full bg-black/40 border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-ep-primary transition-all [color-scheme:dark]"
                                    />
                                    {errors.periodo && <span className="text-red-500 text-[10px] font-black uppercase italic tracking-widest px-2">{errors.periodo}</span>}
                                </div>

                                {/* Frecuencia */}
                                <div className="space-y-3">
                                    <label className="block text-[10px] font-black text-indigo-100/30 uppercase tracking-widest italic px-2">Frecuencia de Informe</label>
                                    <select id="field_1" name="field_1" 
                                        value={data.frecuencia}
                                        onChange={e => setData('frecuencia', e.target.value)}
                                        className="w-full bg-black/40 border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-ep-primary transition-all"
                                    >
                                        <option value="mensual">MENSUAL</option>
                                        <option value="trimestral">TRIMESTRAL</option>
                                        <option value="semestral">SEMESTRAL</option>
                                        <option value="anual">ANUAL</option>
                                    </select>
                                    {errors.frecuencia && <span className="text-red-500 text-[10px] font-black uppercase italic tracking-widest px-2">{errors.frecuencia}</span>}
                                </div>
                            </div>

                            <div className="flex justify-end pt-10">
                                <button 
                                    type="submit" 
                                    disabled={processing}
                                    className="h-16 px-12 bg-ep-primary text-white text-xs font-black uppercase tracking-[0.2em] italic rounded-2xl shadow-2xl shadow-ep-primary/30 hover:brightness-110 active:scale-95 transition-all flex items-center gap-3 disabled:opacity-50"
                                >
                                    {processing ? 'Procesando...' : (
                                        <>
                                            Solicitar Informe
                                            <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="3" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                                        </>
                                    )}
                                </button>
                            </div>
                        </form>
                    </div>

                    <div className="mt-8 p-6 bg-blue-500/5 rounded-2xl border border-blue-500/10 flex gap-4 items-start">
                        <svg className="w-5 h-5 text-blue-400 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <p className="text-[10px] text-indigo-100/40 uppercase font-bold leading-relaxed tracking-wider">
                            Al solicitar el informe, el sistema agrupará automáticamente todas las visitas, inspecciones y hallazgos registrados en el periodo seleccionado para que el técnico pueda redactar las conclusiones.
                        </p>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
