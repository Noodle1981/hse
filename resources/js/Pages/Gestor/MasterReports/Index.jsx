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
            case 'en_redaccion': return 'text-ep-primary bg-ep-primary/10 border-ep-primary/20';
            case 'finalizado': return 'text-emerald-400 bg-emerald-400/10 border-emerald-400/20';
            default: return 'text-gray-400 bg-gray-400/10 border-gray-400/20';
        }
    };

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={<h2 className="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Master Reports</h2>}
        >
            <Head title="Informes Maestros" />

            <div className="py-12 bg-[#11111d] min-h-screen">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
                    
                    {/* Header & Stats */}
                    <div className="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                        <div>
                            <h1 className="text-4xl font-black text-white uppercase italic tracking-tighter">
                                Monitor de <span className="text-ep-primary">Informes</span>
                            </h1>
                            <p className="text-indigo-100/40 text-xs font-bold uppercase tracking-widest mt-2">
                                Gestión y consolidación de reportes técnicos
                            </p>
                        </div>
                        <Link
                            href={route('master-reports.create')}
                            className="px-8 py-4 bg-ep-primary text-white text-xs font-black uppercase tracking-widest italic rounded-2xl shadow-xl shadow-ep-primary/20 hover:scale-105 active:scale-95 transition-all"
                        >
                            Solicitar Nuevo Informe
                        </Link>
                    </div>

                    {/* Filters */}
                    <div className="bg-ep-dark/60 backdrop-blur-xl p-6 rounded-[2rem] border border-white/5 shadow-2xl">
                        <form onSubmit={handleSearch} className="flex flex-wrap gap-4">
                            <div className="flex-grow min-w-[300px]">
                                <input
                                    type="text"
                                    placeholder="Buscar por sede o título..."
                                    className="w-full bg-black/40 border-white/10 rounded-xl text-white text-sm focus:ring-ep-primary font-bold px-6 py-4"
                                    value={data.search}
                                    onChange={e => setData('search', e.target.value)}
                                />
                            </div>
                            <select
                                className="bg-black/40 border-white/10 rounded-xl text-white text-sm focus:ring-ep-primary font-bold px-6 py-4"
                                value={data.status}
                                onChange={e => {
                                    setData('status', e.target.value);
                                    get(route('master-reports.index', { ...filters, status: e.target.value }));
                                }}
                            >
                                <option value="">Todos los Estados</option>
                                <option value="solicitado">Solicitados</option>
                                <option value="en_redaccion">En Redacción</option>
                                <option value="finalizado">Finalizados</option>
                            </select>
                        </form>
                    </div>

                    {/* Reports Grid */}
                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        {reports.data.map((report) => (
                            <div key={report.id} className="group relative bg-ep-dark/40 backdrop-blur-md rounded-[2rem] border border-white/5 p-8 shadow-2xl hover:border-ep-primary/30 transition-all duration-500 overflow-hidden">
                                <div className="absolute top-0 right-0 p-4">
                                    <span className={`px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest border ${getStatusColor(report.estado)}`}>
                                        {report.estado.replace('_', ' ')}
                                    </span>
                                </div>

                                <div className="space-y-6">
                                    <div>
                                        <h3 className="text-xl font-black text-white uppercase italic tracking-tight line-clamp-2 leading-tight min-h-[3rem]">
                                            {report.titulo}
                                        </h3>
                                        <div className="flex items-center gap-2 mt-2 text-ep-primary font-bold text-xs uppercase italic">
                                            <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                            {report.sede.nombre_sede}
                                        </div>
                                    </div>

                                    <div className="grid grid-cols-2 gap-4">
                                        <div className="p-4 bg-white/5 rounded-2xl border border-white/5">
                                            <span className="block text-[8px] font-black text-indigo-100/30 uppercase tracking-widest mb-1">Periodo</span>
                                            <span className="text-xs font-black text-white italic">{report.periodo}</span>
                                        </div>
                                        <div className="p-4 bg-white/5 rounded-2xl border border-white/5">
                                            <span className="block text-[8px] font-black text-indigo-100/30 uppercase tracking-widest mb-1">Frecuencia</span>
                                            <span className="text-xs font-black text-white italic uppercase">{report.frecuencia || 'Mensual'}</span>
                                        </div>
                                    </div>

                                    <div className="flex items-center gap-4 pt-4 border-t border-white/5">
                                        <div className="flex -space-x-3 overflow-hidden">
                                            {[report.tecnico, report.gestor].filter(Boolean).map((user, i) => (
                                                <div key={i} title={user.nombres} className="inline-block h-10 w-10 rounded-xl ring-2 ring-ep-dark bg-ep-primary flex items-center justify-center text-xs font-black text-white border border-white/20">
                                                    {user.nombres[0]}
                                                </div>
                                            ))}
                                        </div>
                                        <div className="flex-grow">
                                            <span className="block text-[8px] font-black text-indigo-100/20 uppercase tracking-widest leading-none mb-1">Especialista</span>
                                            <span className="text-[10px] font-bold text-white uppercase truncate block tracking-tighter italic">
                                                {report.tecnico?.nombres || 'Sin asignar'}
                                            </span>
                                        </div>
                                    </div>

                                    <div className="grid grid-cols-2 gap-3 pt-2">
                                        <Link
                                            href={route('master-reports.edit', report.id)}
                                            className="px-4 py-3 bg-white/5 hover:bg-ep-primary text-white text-[10px] font-black uppercase tracking-widest italic rounded-xl border border-white/10 transition-all text-center"
                                        >
                                            {report.estado === 'finalizado' ? 'Ver Detalles' : 'Redactar'}
                                        </Link>
                                        <a
                                            href={route('master-reports.pdf', report.id)}
                                            target="_blank"
                                            className="px-4 py-3 bg-white/5 hover:bg-emerald-500 text-white text-[10px] font-black uppercase tracking-widest italic rounded-xl border border-white/10 transition-all text-center"
                                        >
                                            PDF
                                        </a>
                                    </div>
                                </div>
                            </div>
                        ))}
                    </div>

                    {/* Pagination Placeholder */}
                    {reports.data.length === 0 && (
                        <div className="py-20 text-center bg-ep-dark/20 rounded-[3rem] border-2 border-dashed border-white/5">
                            <p className="text-xl font-black text-indigo-100/10 uppercase italic tracking-widest">No se encontraron informes maestros</p>
                        </div>
                    )}
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
