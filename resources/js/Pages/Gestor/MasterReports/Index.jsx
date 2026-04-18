import React from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, useForm } from '@inertiajs/react';

export default function Index({ auth, reports, filters }) {
    const { data, setData, get, processing } = useForm({
        search: filters.search || '',
        status: filters.status || '',
    });

    const handleSearch = (e) => {
        e.preventDefault();
        get(route('master-reports.index'), { preserveState: true });
    };

    const getStatusColor = (status) => {
        switch (status) {
            case 'solicitado': return 'text-yellow-400 bg-yellow-400/10 border-yellow-400/20';
            case 'en_redaccion': return 'text-ep-primary bg-ep-primary/10 border-ep-primary/20 shadow-[0_0_15px_rgba(246,146,57,0.1)]';
            case 'finalizado': return 'text-emerald-400 bg-emerald-400/10 border-emerald-400/20 shadow-[0_0_15px_rgba(52,211,153,0.1)]';
            default: return 'text-gray-400 bg-gray-400/10 border-gray-400/20';
        }
    };

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={
                <div className="flex flex-col">
                    <h1 className="text-xs font-black text-ep-primary uppercase tracking-widest italic leading-none mb-1">
                        Archivo de <span className="text-white">Informes Maestros</span>
                    </h1>
                    <h2 className="text-sm font-bold text-indigo-100 uppercase tracking-tight leading-none italic">
                        Bóveda de Consolidados
                    </h2>
                </div>
            }
        >
            <Head title="Bóveda de Informes" />

            <div className="py-12 bg-[#11111d] min-h-screen">
                <div className="max-w-[98rem] mx-auto sm:px-6 lg:px-8 space-y-10">
                    
                    {/* HUD HEADER: ACCIONES */}
                    <div className="flex flex-col md:flex-row justify-end items-start md:items-center gap-8">
                        {/* Solo Gestores pueden solicitar informes fuera de contrato */}
                        {auth.user.rol?.toLowerCase() !== 'tecnico' && auth.user.rol?.toLowerCase() !== 'técnico' && (
                            <Link
                                href={route('master-reports.create')}
                                className="group relative h-16 px-10 bg-ep-primary text-white text-xs font-black uppercase tracking-[0.2em] italic rounded-2xl shadow-2xl shadow-ep-primary/20 flex items-center gap-3 transition-all hover:scale-105 active:scale-95 overflow-hidden"
                            >
                                <div className="absolute inset-0 bg-gradient-to-r from-white/0 via-white/20 to-white/0 -translate-x-full group-hover:translate-x-full transition-transform duration-700"></div>
                                SOLICITAR INFORME MAESTRO
                                <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="3"><path d="M12 4v16m8-8H4"/></svg>
                            </Link>
                        )}
                    </div>

                    {/* FILTRADO TÁCTICO */}
                    <div className="bg-ep-dark/40 backdrop-blur-2xl p-8 rounded-[2.5rem] border border-white/5 shadow-2xl">
                        <form onSubmit={handleSearch} className="flex flex-wrap items-center gap-6">
                            <div className="flex-1 min-w-[300px] relative group">
                                <div className="absolute left-6 top-1/2 -translate-y-1/2 text-white/20 group-focus-within:text-ep-primary transition-colors">
                                    <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" strokeWidth="3"/></svg>
                                </div>
                                <input
                                    type="text"
                                    placeholder="FILTRAR POR CLIENTE, TITULO O REFERENCIA..."
                                    className="w-full bg-black/40 border-white/10 rounded-2xl text-white text-[11px] font-black uppercase italic tracking-widest focus:ring-ep-primary focus:border-ep-primary transition-all px-16 py-5"
                                    value={data.search}
                                    onChange={e => setData('search', e.target.value)}
                                />
                            </div>
                            
                            <div className="flex items-center gap-4 bg-black/20 p-2 rounded-2xl border border-white/5">
                                <span className="text-[9px] font-black text-white/20 uppercase tracking-widest pl-4 pr-2">ESTADOS</span>
                                <select
                                    className="bg-ep-dark border-none rounded-xl text-white text-[10px] font-black uppercase italic tracking-widest focus:ring-0 px-6 py-3 cursor-pointer"
                                    value={data.status}
                                    onChange={e => {
                                        setData('status', e.target.value);
                                        get(route('master-reports.index', { ...filters, status: e.target.value }));
                                    }}
                                >
                                    <option value="">TODOS</option>
                                    <option value="solicitado">SOLICITADOS</option>
                                    <option value="en_redaccion">EN REDACCIÓN</option>
                                    <option value="finalizado">FINALIZADOS</option>
                                </select>
                            </div>
                        </form>
                    </div>

                    {/* GRILLA DE BÓVEDA */}
                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                        {reports.data.map((report) => (
                            <div key={report.id} className="group relative bg-[#141421]/60 backdrop-blur-md rounded-[2.5rem] border border-white/5 p-8 shadow-2xl hover:border-ep-primary/30 transition-all duration-500 overflow-hidden flex flex-col">
                                <div className="absolute top-0 right-0 p-6">
                                    <span className={`px-4 py-1.5 rounded-xl text-[9px] font-black uppercase tracking-widest border ${getStatusColor(report.estado)}`}>
                                        {report.estado.replace('_', ' ')}
                                    </span>
                                </div>

                                <div className="flex-1 space-y-6">
                                    <div className="pt-4">
                                        <div className="flex items-center gap-2 text-ep-primary font-black text-[10px] uppercase italic tracking-[0.2em] mb-2">
                                            <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" strokeWidth="2"/></svg>
                                            {report.sede.nombre_sede}
                                        </div>
                                        <h3 className="text-xl font-black text-white uppercase italic tracking-tight line-clamp-2 leading-tight min-h-[3rem] group-hover:text-ep-primary transition-colors">
                                            {report.titulo}
                                        </h3>
                                    </div>

                                    {/* ALERTA DE REVISIÓN PARA TÉCNICOS */}
                                    {(auth.user.rol?.toLowerCase() === 'tecnico' || auth.user.rol?.toLowerCase() === 'técnico') && report.estado === 'en_redaccion' && report.observacion_revision && (
                                        <div className="p-4 bg-red-500/10 border border-red-500/20 rounded-2xl flex items-start gap-4 animate-pulse mt-4">
                                            <div className="p-2 bg-red-500/20 rounded-lg text-red-500">
                                                <svg className="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="2.5"><path d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            </div>
                                            <div>
                                                <p className="text-[8px] font-black text-red-500 uppercase tracking-widest italic mb-1">Nota de Corrección</p>
                                                <p className="text-[10px] font-bold text-red-100/60 leading-tight italic">"{report.observacion_revision}"</p>
                                            </div>
                                        </div>
                                    )}

                                    <div className="grid grid-cols-2 gap-4">
                                        <div className="p-4 bg-white/5 rounded-2xl border border-white/5 group-hover:bg-white/10 transition-colors">
                                            <span className="block text-[8px] font-black text-indigo-100/20 uppercase tracking-widest mb-1.5">Periodo</span>
                                            <span className="text-xs font-black text-white italic uppercase">{report.periodo}</span>
                                        </div>
                                        <div className="p-4 bg-white/5 rounded-2xl border border-white/5 group-hover:bg-white/10 transition-colors">
                                            <span className="block text-[8px] font-black text-indigo-100/20 uppercase tracking-widest mb-1.5">Frecuencia</span>
                                            <span className="text-xs font-black text-white italic uppercase">{report.frecuencia || 'Mensual'}</span>
                                        </div>
                                    </div>

                                    <div className="flex items-center gap-4 pt-4 border-t border-white/5">
                                        <div className="w-10 h-10 rounded-xl bg-ep-primary flex items-center justify-center text-xs font-black text-white shadow-lg border border-white/20">
                                            {report.tecnico?.nombres ? report.tecnico.nombres[0] : '?'}
                                        </div>
                                        <div className="flex-grow min-w-0">
                                            <span className="block text-[8px] font-black text-indigo-100/20 uppercase tracking-widest mb-1">Especialista</span>
                                            <span className="text-[10px] font-black text-white uppercase truncate block italic tracking-tight">
                                                {report.tecnico?.nombres || 'SIN ASIGNAR'}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div className="grid grid-cols-2 gap-3 mt-8">
                                    {(auth.user.rol?.toLowerCase() === 'tecnico' || auth.user.rol?.toLowerCase() === 'técnico') ? (
                                        report.estado !== 'finalizado' ? (
                                            <Link
                                                href={route('master-reports.edit', report.id)}
                                                className="col-span-2 py-5 bg-ep-primary text-white text-[10px] font-black uppercase tracking-widest italic rounded-2xl transition-all text-center shadow-xl shadow-ep-primary/20 relative overflow-hidden group/btn"
                                            >
                                                <div className="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover/btn:translate-x-full transition-transform duration-1000"></div>
                                                REDACTAR REPORTE
                                            </Link>
                                        ) : (
                                            <a
                                                href={route('master-reports.pdf', report.id)}
                                                target="_blank"
                                                className="col-span-2 py-5 bg-white/5 hover:bg-white hover:text-black text-white text-[10px] font-black uppercase tracking-widest italic rounded-2xl border border-white/10 transition-all text-center flex items-center justify-center gap-2 shadow-lg"
                                            >
                                                <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" strokeWidth="3"/></svg>
                                                DESCARGAR PDF FINAL
                                            </a>
                                        )
                                    ) : (
                                        <>
                                            <Link
                                                href={route('master-reports.edit', report.id)}
                                                className="px-4 py-4 bg-white/5 hover:bg-ep-primary text-white text-[10px] font-black uppercase tracking-widest italic rounded-2xl border border-white/10 transition-all text-center shadow-lg"
                                            >
                                                {report.estado === 'finalizado' ? 'REVISAR' : 'EDITAR'}
                                            </Link>
                                            <a
                                                href={route('master-reports.pdf', report.id)}
                                                target="_blank"
                                                className="px-4 py-4 bg-white/5 hover:bg-white hover:text-black text-white text-[10px] font-black uppercase tracking-widest italic rounded-2xl border border-white/10 transition-all text-center flex items-center justify-center gap-2"
                                            >
                                                <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" strokeWidth="3"/></svg>
                                                PDF
                                            </a>
                                        </>
                                    )}
                                </div>
                            </div>
                        ))}
                    </div>

                    {/* EMPTY STATE: BÓVEDA VACÍA */}
                    {reports.data.length === 0 && (
                        <div className="py-40 text-center bg-[#141421]/20 rounded-[4rem] border-2 border-dashed border-white/5 flex flex-col items-center justify-center group hover:border-ep-primary/20 transition-all">
                            <div className="w-40 h-40 bg-white/5 rounded-[3rem] border border-white/5 flex items-center justify-center mb-8 relative">
                                <svg className="w-20 h-20 text-white/5 group-hover:text-ep-primary/20 transition-all duration-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" strokeWidth="1"/></svg>
                                <div className="absolute inset-0 bg-ep-primary/5 blur-3xl rounded-full opacity-0 group-hover:opacity-100 transition-opacity"></div>
                            </div>
                            <h2 className="text-3xl font-black text-white/10 uppercase italic tracking-[0.2em] group-hover:text-white/30 transition-all">
                                Bóveda Vacía
                            </h2>
                            <p className="text-[10px] font-black text-white/5 uppercase tracking-[0.4em] mt-4 italic">No hay consolidados registrados en este sector</p>
                        </div>
                    )}
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
