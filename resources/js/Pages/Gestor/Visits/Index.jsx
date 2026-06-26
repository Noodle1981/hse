import { router, Head, Link, usePage } from '@inertiajs/react';
import AuthenticatedLayout from '../../../Layouts/AuthenticatedLayout';
import { useState, useEffect } from 'react';

export default function Index({ visits, filters, activeVisit }) {
    const { auth, flash } = usePage().props;
    
    const [search, setSearch] = useState(filters.search || '');
    const [status, setStatus] = useState(filters.status || 'all');

    const updateFilters = () => {
        router.get(route('gestor.operations.visits.index'), { search, status }, {
            preserveState: true,
            replace: true,
        });
    };

    useEffect(() => {
        const timeout = setTimeout(() => {
            if (search !== (filters.search || '')) updateFilters();
        }, 500);
        return () => clearTimeout(timeout);
    }, [search]);

    useEffect(() => {
        if (status !== (filters.status || 'all')) updateFilters();
    }, [status]);

    return (
        <AuthenticatedLayout title="Centro de Comando Operativo">
            <Head title="Gestión de Visitas" />

            <div className="px-4 sm:px-6 lg:px-8 py-8 text-white selection:bg-orange-500/30">
                <div className="max-w-[95rem] mx-auto space-y-8 animate-fade-in">
                    
                    {/* HUB DE ESTADO ACTIVO */}
                    {activeVisit && (
                        <div className="bg-orange-500/10 border border-orange-500/30 rounded-[2.5rem] p-8 flex flex-col md:flex-row items-center justify-between gap-6 shadow-2xl relative overflow-hidden group">
                            <div className="absolute inset-0 bg-gradient-to-r from-orange-500/5 to-transparent"></div>
                            <div className="relative z-10 flex items-center gap-6">
                                <div className="w-16 h-16 rounded-2xl bg-orange-500 flex items-center justify-center shadow-[0_0_30px_rgba(246,146,57,0.5)] animate-pulse">
                                    <svg className="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                </div>
                                <div>
                                    <h2 className="text-xl font-black uppercase italic tracking-widest text-white">Visita en curso detectada</h2>
                                    <p className="text-[10px] font-black text-orange-400 uppercase tracking-widest italic mt-1">Sede: {activeVisit?.sede?.nombre_sede || 'Sede Desconocida'} — Iniciada: {activeVisit?.start_time ? new Date(activeVisit.start_time).toLocaleTimeString() : ''}</p>
                                </div>
                            </div>
                            <Link href={route('gestor.operations.visits.show', activeVisit.id)} className="relative z-10 px-10 py-5 bg-orange-600 text-white rounded-[2rem] text-[11px] font-black uppercase tracking-[0.2em] italic transition-all shadow-xl hover:brightness-110 active:scale-95">Reanudar Despliegue</Link>
                        </div>
                    )}

                    {/* FILTROS TÁCTICOS */}
                    <div className="bg-[#16162a]/40 backdrop-blur-md rounded-[2rem] border border-white/5 p-8 shadow-2xl relative overflow-hidden animate-fade-in-down">
                        <div className="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-orange-500/30 to-transparent"></div>
                        
                        <div className="flex flex-col lg:flex-row gap-6 justify-between items-center relative z-10">
                            <div className="flex-1 flex flex-col lg:flex-row gap-4 w-full text-white">
                                <div className="relative group flex-1">
                                    <span className="absolute -top-2.5 left-4 bg-[#11111d] px-2 text-[8px] font-black text-orange-400 uppercase tracking-[0.3em] italic z-10">Explorador de Sedes</span>
                                    <input id="search" name="search" 
                                        type="text" 
                                        value={search}
                                        onChange={e => setSearch(e.target.value)}
                                        placeholder="BUSCAR VISITA POR NOMBRE DE SEDE..."
                                        className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-orange-500/50 transition-all placeholder-white/5 italic uppercase"
                                    />
                                </div>
                                <div className="relative group lg:w-48">
                                    <span className="absolute -top-2.5 left-4 bg-[#11111d] px-2 text-[8px] font-black text-orange-400 uppercase tracking-[0.3em] italic z-10">Filtro de Estado</span>
                                    <select id="status" name="status" value={status} onChange={e => setStatus(e.target.value)} className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-orange-500/50 transition-all [color-scheme:dark] italic uppercase">
                                        <option value="all">Ver Todas</option>
                                        <option value="completed">Completadas</option>
                                        <option value="signed">Certificadas</option>
                                        <option value="in_progress">En Curso</option>
                                    </select>
                                </div>
                            </div>

                            <Link href={route('gestor.operations.visits.create')} 
                                  className="flex h-12 px-8 bg-[#F69239] text-white hover:brightness-110 active:scale-95 rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] italic transition-all items-center gap-3 shadow-[0_0_20px_rgba(246,146,57,0.3)]">
                                <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="3">
                                    <path d="M12 4v16m8-8H4"/>
                                </svg>
                                Nueva Visita
                            </Link>
                        </div>
                    </div>

                    {/* Tabla de Visitas */}
                    <div className="bg-[#16162a]/60 backdrop-blur-md rounded-[3rem] border border-white/5 shadow-2xl relative overflow-hidden animate-fade-in-up">
                        <div className="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-orange-500/20 to-transparent"></div>
                        
                        <div className="overflow-x-auto">
                            <table className="w-full border-collapse min-w-[1000px]">
                                <thead>
                                    <tr className="bg-black/20 text-left">
                                        <th className="px-10 py-6 text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic leading-none text-center">Fecha</th>
                                        <th className="px-6 py-6 text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic leading-none">Sede / Cliente</th>
                                        <th className="px-6 py-6 text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic leading-none">Técnico Asignado</th>
                                        <th className="px-6 py-6 text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic leading-none text-center">Permanencia</th>
                                        <th className="px-6 py-6 text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic leading-none text-center">Estado</th>
                                        <th className="px-10 py-6 text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic leading-none text-right">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-white/5">
                                    {visits.data.map(visit => (
                                        <tr key={visit.id} className="group hover:bg-white/5 transition-all duration-300">
                                            <td className="px-10 py-6 text-center">
                                                <div className="text-sm font-black text-white italic">{new Date(visit.visit_date).toLocaleDateString()}</div>
                                                <div className="text-[9px] font-black text-white/20 uppercase tracking-widest mt-1">ID: #{visit.id}</div>
                                            </td>
                                            <td className="px-6 py-6">
                                                <div className="flex items-center gap-4">
                                                    <div className="w-10 h-10 rounded-xl bg-white/5 border border-white/5 flex items-center justify-center text-orange-500 font-black italic shadow-lg group-hover:scale-110 transition-transform">
                                                        {visit.sede?.empresa?.nombre_fantasia?.substring(0, 1) || 'S'}
                                                    </div>
                                                    <div>
                                                        <div className="text-sm font-black text-white italic uppercase group-hover:text-orange-400 transition-colors">{visit.sede?.nombre_sede || 'Sede Desconocida'}</div>
                                                        <div className="text-[9px] font-black text-white/20 uppercase tracking-widest mt-1 italic">{visit.sede?.empresa?.nombre_fantasia}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td className="px-6 py-6">
                                                <div className="flex items-center gap-3">
                                                    <div className="w-8 h-8 rounded-full bg-blue-500/10 border border-blue-500/20 flex items-center justify-center text-[10px] font-black text-blue-400 uppercase italic">
                                                        {visit.user?.name?.substring(0, 2)}
                                                    </div>
                                                    <span className="text-[11px] font-black text-white/60 italic uppercase tracking-wider">{visit.user?.name}</span>
                                                </div>
                                            </td>
                                            <td className="px-6 py-6 text-center">
                                                <div className="text-xs font-black text-white italic">{visit.start_time ? new Date(visit.start_time).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) : '--:--'}</div>
                                                <div className="text-[9px] font-black text-white/20 uppercase tracking-widest mt-1">A {visit.end_time ? new Date(visit.end_time).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) : '...'}</div>
                                            </td>
                                            <td className="px-6 py-6 text-center">
                                                <StatusBadge status={visit.status} label={visit.status === 'signed' ? 'CERTIFICADA' : (visit.status === 'completed' ? 'FINALIZADA' : 'INICIADA')} />
                                            </td>
                                            <td className="px-10 py-6 text-right">
                                                <div className="flex items-center justify-end gap-3 opacity-0 group-hover:opacity-100 transition-all duration-300">
                                                    {visit.status === 'in_progress' ? (
                                                        <Link href={route('gestor.operations.visits.show', visit.id)} className="h-10 px-6 bg-orange-600 text-white rounded-xl text-[9px] font-black uppercase tracking-widest italic flex items-center gap-2 shadow-lg">
                                                            Ingresar
                                                        </Link>
                                                    ) : (
                                                        <div className="flex gap-2">
                                                            {/* Vista Previa */}
                                                            <a 
                                                                href={route('gestor.visits.certificate.preview', visit.id)} 
                                                                target="_blank"
                                                                className="h-10 w-10 bg-white/5 border border-white/10 text-white/40 hover:bg-white/10 hover:text-white rounded-xl flex items-center justify-center transition-all shadow-lg"
                                                                title="Vista Previa PDF"
                                                            >
                                                                <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="2"><path strokeLinecap="round" strokeLinejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path strokeLinecap="round" strokeLinejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                            </a>
                                                            {/* Descargar PDF */}
                                                            <a 
                                                                href={route('gestor.visits.certificate.download', visit.id)}
                                                                className="h-10 w-10 bg-white/5 border border-white/10 text-white/40 hover:bg-white/10 hover:text-white rounded-xl flex items-center justify-center transition-all shadow-lg"
                                                                title="Descargar PDF"
                                                            >
                                                                <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="2"><path strokeLinecap="round" strokeLinejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                                            </a>
                                                        </div>
                                                    )}
                                                </div>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>

                        {/* Pagination */}
                        <div className="px-10 py-6 border-t border-white/5 bg-black/20 flex items-center justify-between font-black italic text-[10px]">
                            <div className="text-white/20">Registros Operativos: {visits.total}</div>
                            <div className="flex gap-2">
                                {visits.links.map((link, i) => (
                                    <Link 
                                        key={i}
                                        href={link.url || '#'}
                                        dangerouslySetInnerHTML={{ __html: link.label }}
                                        className={`px-4 py-2 rounded-xl border ${link.active ? 'bg-orange-500/10 border-orange-500/30 text-orange-400' : 'bg-white/5 border-white/5 text-white/20 hover:text-white'} ${!link.url && 'opacity-20 cursor-not-allowed'}`}
                                    />
                                ))}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

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

function StatusBadge({ status, label }) {
    const styles = {
        signed: 'text-emerald-400 border-emerald-500/30 bg-emerald-500/10',
        completed: 'text-blue-400 border-blue-500/30 bg-blue-500/10',
        in_progress: 'text-orange-400 border-orange-500/30 bg-orange-500/10 animate-pulse',
    };

    return (
        <span className={`inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[8px] font-black uppercase tracking-widest italic border ${styles[status] || 'text-white/20 border-white/5 bg-white/5'}`}>
            <span className={`w-1 h-1 rounded-full ${status === 'in_progress' ? 'bg-orange-500' : (status === 'signed' ? 'bg-emerald-500' : 'bg-blue-500')}`}></span>
            {label}
        </span>
    );
}
