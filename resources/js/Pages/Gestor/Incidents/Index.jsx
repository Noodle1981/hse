import { Head, Link, router } from '@inertiajs/react';
import AuthenticatedLayout from '../../../Layouts/AuthenticatedLayout';
import { useState } from 'react';

export default function Index({ incidents, filters, kpis }) {
    const [search, setSearch] = useState(filters.search || '');
    const [tipo, setTipo] = useState(filters.tipo || 'all');
    const [gravedad, setGravedad] = useState(filters.gravedad || 'all');

    const handleSearch = (e) => {
        e.preventDefault();
        router.get(route('gestor.incidentes.index'), { search, tipo, gravedad }, { preserveState: true });
    };

    const getGravityBadge = (sev) => {
        const colors = {
            'leve': 'bg-emerald-500/10 text-emerald-500 border-emerald-500/20',
            'moderado': 'bg-yellow-500/10 text-yellow-500 border-yellow-500/20',
            'grave': 'bg-orange-500/10 text-orange-500 border-orange-500/20 pulse',
            'fatal': 'bg-red-500/10 text-red-500 border-red-500/20 pulse-red',
        };
        return colors[sev] || 'bg-white/5 text-white/40 border-white/10';
    };

    const getTipoIcon = (t) => {
        if (t === 'accidente') return (
            <div className="w-8 h-8 rounded-lg bg-red-500/20 flex items-center justify-center text-red-500">
                <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
        );
        return (
            <div className="w-8 h-8 rounded-lg bg-blue-500/20 flex items-center justify-center text-blue-500">
                <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        );
    };

    return (
        <AuthenticatedLayout title="Monitor de Siniestralidad">
            <Head title="Incidentes & Accidentes" />

            <div className="px-4 py-8 lg:p-12 text-white selection:bg-red-500/30">
                <div className="max-w-7xl mx-auto space-y-10 animate-fade-in-up">
                    
                    {/* CABECERA Y KPIs */}
                    <div className="grid grid-cols-1 lg:grid-cols-4 gap-6">
                        <div className="lg:col-span-1 bg-[#16162a]/60 backdrop-blur-xl rounded-[2.5rem] border border-white/5 p-8 shadow-2xl flex flex-col justify-center">
                            <h1 className="text-3xl font-black uppercase italic tracking-tighter leading-none mb-2">Central de<br/><span className="text-red-600">Incidentes</span></h1>
                            <p className="text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic">Seguimiento Operativo</p>
                        </div>

                        <div className="bg-[#16162a]/40 border border-white/5 p-8 rounded-[2.5rem] flex items-center gap-6 group hover:border-red-500/30 transition-all">
                            <div className="w-14 h-14 rounded-2xl bg-red-500/10 flex items-center justify-center text-red-500 shadow-lg">
                                <svg className="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            </div>
                            <div>
                                <div className="text-3xl font-black italic">{kpis.accidentes}</div>
                                <div className="text-[9px] font-black text-white/30 uppercase tracking-widest italic leading-tight">Accidentes<br/>Reportados</div>
                            </div>
                        </div>

                        <div className="bg-[#16162a]/40 border border-white/5 p-8 rounded-[2.5rem] flex items-center gap-6 group hover:border-orange-500/30 transition-all">
                            <div className="w-14 h-14 rounded-2xl bg-orange-500/10 flex items-center justify-center text-orange-500 shadow-lg">
                                <svg className="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div>
                                <div className="text-3xl font-black italic">{kpis.dias_perdidos}</div>
                                <div className="text-[9px] font-black text-white/30 uppercase tracking-widest italic leading-tight">Días de<br/>Trabajo Perdidos</div>
                            </div>
                        </div>

                        <div className="bg-[#16162a]/40 border border-white/5 p-8 rounded-[2.5rem] flex items-center gap-6 group hover:border-blue-500/30 transition-all text-blue-500">
                            <div className="w-14 h-14 rounded-2xl bg-blue-500/10 flex items-center justify-center shadow-lg">
                                <svg className="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div>
                                <div className="text-3xl font-black italic text-white">{kpis.total - kpis.accidentes}</div>
                                <div className="text-[9px] font-black text-white/30 uppercase tracking-widest italic leading-tight">Cuasi-Accidentes<br/>& Incidentes</div>
                            </div>
                        </div>
                    </div>

                    {/* FILTROS */}
                    <div className="bg-[#16162a]/40 backdrop-blur-md rounded-[2.5rem] border border-white/5 p-6 flex flex-col md:flex-row gap-6 items-center">
                        <form onSubmit={handleSearch} className="flex-1 w-full grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div className="relative group md:col-span-2">
                                <input 
                                    type="text" 
                                    placeholder="BUSCAR POR DESCRIPCIÓN O SEDE..."
                                    value={search}
                                    onChange={e => setSearch(e.target.value)}
                                    className="w-full bg-black/40 border border-white/5 rounded-2xl px-6 py-4 text-xs font-bold text-white placeholder-white/20 focus:ring-2 focus:ring-red-500 transition-all uppercase italic"
                                />
                            </div>
                            <select 
                                value={tipo}
                                onChange={e => { setTipo(e.target.value); router.get(route('gestor.incidentes.index'), { search, tipo: e.target.value, gravedad }, { preserveState: true }); }}
                                className="bg-black/40 border border-white/5 rounded-2xl px-6 py-4 text-xs font-bold text-white focus:ring-2 focus:ring-red-500 transition-all italic uppercase [color-scheme:dark]"
                            >
                                <option value="all">TODOS LOS TIPOS</option>
                                <option value="accidente">ACCIDENTES</option>
                                <option value="incidente">INCIDENTES</option>
                                <option value="cuasi_accidente">CUASI ACCIDENTES</option>
                            </select>
                            <select 
                                value={gravedad}
                                onChange={e => { setGravedad(e.target.value); router.get(route('gestor.incidentes.index'), { search, tipo, gravedad: e.target.value }, { preserveState: true }); }}
                                className="bg-black/40 border border-white/5 rounded-2xl px-6 py-4 text-xs font-bold text-white focus:ring-2 focus:ring-red-500 transition-all italic uppercase [color-scheme:dark]"
                            >
                                <option value="all">TODAS LAS GRAVEDADES</option>
                                <option value="leve">LEVE</option>
                                <option value="moderado">MODERADO</option>
                                <option value="grave">GRAVE</option>
                                <option value="fatal">FATAL</option>
                            </select>
                        </form>
                    </div>

                    {/* LISTADO TÁCTICO */}
                    <div className="bg-[#11111d]/60 backdrop-blur-xl border border-white/5 rounded-[3rem] overflow-hidden shadow-3xl min-h-[500px]">
                        <table className="w-full text-left border-collapse">
                            <thead>
                                <tr className="bg-white/5 text-[9px] font-black uppercase italic tracking-[0.3em] text-white/40">
                                    <th className="px-10 py-6">Tipo / Ref</th>
                                    <th className="px-8 py-6">Sede / Ubicación</th>
                                    <th className="px-8 py-6">Fecha / Técnico</th>
                                    <th className="px-8 py-6 text-center">Gravedad</th>
                                    <th className="px-10 py-6 text-right">Acciones</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-white/5">
                                {incidents.data.map(inc => (
                                    <tr key={inc.id} className="group hover:bg-white/[0.02] transition-colors">
                                        <td className="px-10 py-8">
                                            <div className="flex items-center gap-4">
                                                {getTipoIcon(inc.tipo)}
                                                <div>
                                                    <div className="text-sm font-black text-white italic uppercase tracking-tighter truncate max-w-[200px]">{inc.titulo}</div>
                                                    <div className="text-[8px] font-black text-white/20 uppercase tracking-widest italic">ID: #{inc.id} • {inc.tipo.replace('_', ' ')}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td className="px-8 py-8">
                                            <div className="text-[11px] font-black text-white/80 uppercase italic">{inc.sede?.nombre_sede}</div>
                                            <div className="text-[8px] font-black text-white/20 uppercase italic tracking-widest">{inc.sede?.empresa?.nombre}</div>
                                        </td>
                                        <td className="px-8 py-8">
                                            <div className="text-[10px] font-black text-white/60 uppercase italic">{new Date(inc.fecha_ocurrencia).toLocaleDateString()}</div>
                                            <div className="text-[8px] font-black text-blue-400 uppercase italic bg-blue-500/10 px-2 py-0.5 rounded-lg border border-blue-500/20 inline-block mt-1">{inc.user?.name}</div>
                                        </td>
                                        <td className="px-8 py-8 text-center">
                                            <span className={`px-4 py-1.5 rounded-full text-[9px] font-black uppercase tracking-widest italic border ${getGravityBadge(inc.gravedad)}`}>
                                                {inc.gravedad}
                                            </span>
                                        </td>
                                        <td className="px-10 py-8 text-right">
                                            <div className="flex items-center justify-end gap-3 opacity-0 group-hover:opacity-100 transition-opacity">
                                                <Link 
                                                    href={route('gestor.incidentes.show', inc.id)}
                                                    className="w-10 h-10 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center text-white/40 hover:text-white hover:bg-white/10 transition-all"
                                                >
                                                    <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" strokeWidth="2"/><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" strokeWidth="2"/></svg>
                                                </Link>
                                                <Link 
                                                    href={route('gestor.incidentes.pdf', inc.id)}
                                                    className="w-10 h-10 rounded-xl bg-red-500/10 border border-red-500/20 flex items-center justify-center text-red-500/60 hover:text-red-500 transition-all"
                                                >
                                                    <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                </Link>
                                            </div>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                        
                        {incidents.data.length === 0 && (
                            <div className="py-24 text-center opacity-10 italic">
                                <p className="text-sm font-black uppercase tracking-[0.4em]">Sin reportes detectados con estos parámetros</p>
                            </div>
                        )}
                    </div>
                </div>
            </div>

            <style>{`
                .animate-fade-in-up { animation: fade-in-up 0.5s ease-out forwards; }
                @keyframes fade-in-up { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
                .pulse { animation: pulse_orange 2s infinite; }
                .pulse-red { animation: pulse_red 2s infinite; }
                @keyframes pulse_orange {
                    0% { box-shadow: 0 0 0 0 rgba(249, 115, 22, 0.4); }
                    70% { box-shadow: 0 0 0 10px rgba(249, 115, 22, 0); }
                    100% { box-shadow: 0 0 0 0 rgba(249, 115, 22, 0); }
                }
                @keyframes pulse_red {
                    0% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.4); }
                    70% { box-shadow: 0 0 0 10px rgba(239, 68, 68, 0); }
                    100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
                }
            `}</style>
        </AuthenticatedLayout>
    );
}
