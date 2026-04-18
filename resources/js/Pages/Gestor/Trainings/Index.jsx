import { router, Head, Link, usePage } from '@inertiajs/react';
import AuthenticatedLayout from '../../../Layouts/AuthenticatedLayout';
import { useState, useEffect } from 'react';

export default function Index({ templates, history, empresas = [], sedes = [], filters }) {
    const { auth, flash } = usePage().props;
    
    // ESTADOS TÁCTICOS DE FILTRADO
    const [search, setSearch] = useState(filters.search || '');
    const [empresaFilter, setEmpresaFilter] = useState(filters.empresaFilter || '');
    const [sedeFilter, setSedeFilter] = useState(filters.sedeFilter || '');

    const updateFilters = () => {
        router.get(route('gestor.trainings.index'), { search, empresaFilter, sedeFilter }, {
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
        if (empresaFilter !== (filters.empresaFilter || '') || sedeFilter !== (filters.sedeFilter || '')) {
            updateFilters();
        }
    }, [empresaFilter, sedeFilter]);

    return (
        <AuthenticatedLayout 
            title="Centro de Entrenamiento"
            header={
                <div className="flex flex-col">
                    <h1 className="text-xs font-black text-ep-primary uppercase tracking-widest italic leading-none mb-1">
                        Centro de <span className="text-white">Entrenamiento</span>
                    </h1>
                    <h2 className="text-sm font-bold text-indigo-100 uppercase tracking-tight leading-none italic">
                        Gestión de Capacitación y Asistencia
                    </h2>
                </div>
            }
        >
            <Head title="Gestión de Capacitaciones" />

            <div className="px-4 sm:px-6 lg:px-8 py-8 text-white selection:bg-orange-500/30">
                <div className="max-w-[100rem] mx-auto space-y-8 animate-fade-in">
                    
                    {/* HUB DE COMANDOS TÁCTICOS (Filtros Legacy exactos) */}
                    <div className="bg-[#16162a]/60 backdrop-blur-xl rounded-[2.5rem] border border-white/5 p-8 shadow-2xl relative overflow-hidden animate-fade-in-down">
                        <div className="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-transparent via-indigo-500/30 to-transparent"></div>
                        
                        <div className="grid grid-cols-1 md:grid-cols-12 gap-6 items-end relative z-10">
                            
                            {/* Terminal de Escaneo */}
                            <div className="md:col-span-5 relative group">
                                <span className="absolute -top-2.5 left-4 bg-[#11111d] px-2 text-[8px] font-black text-indigo-400 uppercase tracking-[0.3em] italic z-10">Terminal de Escaneo</span>
                                <div className="relative">
                                    <div className="absolute inset-y-0 left-5 flex items-center pointer-events-none text-white/20 group-focus-within:text-indigo-400">
                                        <svg className="w-5 h-5 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="3" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                    </div>
                                    <input 
                                        type="text" 
                                        value={search}
                                        onChange={e => setSearch(e.target.value)}
                                        className="w-full bg-black/40 border border-white/10 rounded-2xl pl-14 pr-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-indigo-500/50 transition-all placeholder-white/5 italic uppercase"
                                        placeholder="BUSCAR ENTRENAMIENTO O INSTRUCTOR..."
                                    />
                                </div>
                            </div>

                            {/* Filtros de Inteligencia */}
                            <div className="md:col-span-4 grid grid-cols-2 gap-4">
                                <div className="relative group">
                                    <span className="absolute -top-2.5 left-4 bg-[#11111d] px-2 text-[8px] font-black text-white/20 uppercase tracking-[0.3em] italic z-10">Empresa</span>
                                    <select 
                                        value={empresaFilter}
                                        onChange={e => { setEmpresaFilter(e.target.value); setSedeFilter(''); }}
                                        className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-[10px] font-black text-white focus:ring-2 focus:ring-indigo-500/50 transition-all [color-scheme:dark] appearance-none cursor-pointer italic uppercase"
                                    >
                                        <option value="">TODAS</option>
                                        {empresas.map(emp => <option key={emp.id} value={emp.id}>{emp.nombre}</option>)}
                                    </select>
                                </div>
                                <div className="relative group">
                                    <span className="absolute -top-2.5 left-4 bg-[#11111d] px-2 text-[8px] font-black text-white/20 uppercase tracking-[0.3em] italic z-10">Sede</span>
                                    <select 
                                        value={sedeFilter}
                                        onChange={e => setSedeFilter(e.target.value)}
                                        disabled={!empresaFilter}
                                        className={`w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-[10px] font-black text-white focus:ring-2 focus:ring-indigo-500/50 transition-all [color-scheme:dark] appearance-none cursor-pointer italic uppercase ${!empresaFilter ? 'opacity-20 cursor-not-allowed' : ''}`}
                                    >
                                        <option value="">TODAS</option>
                                        {sedes.map(s => <option key={s.id} value={s.id}>{s.nombre_sede}</option>)}
                                    </select>
                                </div>
                            </div>

                            {/* Botón Asignar (Legacy Orange) */}
                            <div className="md:col-span-3">
                                <Link 
                                    href={route('gestor.trainings.create')} 
                                    className="flex h-14 bg-[#f69239] text-white hover:brightness-110 active:scale-95 rounded-2xl text-[11px] font-black uppercase tracking-[0.2em] italic transition-all items-center justify-center gap-3 shadow-[0_0_25px_rgba(246,146,57,0.3)] group/btn relative overflow-hidden"
                                >
                                    <div className="absolute inset-0 bg-white/10 translate-y-14 group-hover/btn:translate-y-0 transition-transform duration-500"></div>
                                    <svg className="w-5 h-5 group-hover/btn:rotate-90 transition-transform duration-500 relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="3"><path d="M12 4v16m8-8H4"/></svg>
                                    <span className="relative z-10">+ Asignar Capacitación</span>
                                </Link>
                            </div>
                        </div>
                    </div>

                    {/* MATRIZ DE ENTRENAMIENTO (CABECERAS EXACTAS) */}
                    <div className="bg-[#16162a]/60 backdrop-blur-xl rounded-[3rem] border border-white/5 shadow-2xl relative overflow-hidden group/matrix mb-20 animate-fade-in-up">
                        <div className="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-indigo-500/20 to-transparent"></div>
                        
                        <div className="overflow-x-auto">
                            <table className="table-auto w-full border-collapse">
                                <thead>
                                    <tr className="bg-black/20 text-left">
                                        <th className="px-8 py-6"><span className="text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic leading-none">Capacitación</span></th>
                                        <th className="hidden xl:table-cell px-8 py-6"><span className="text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic leading-none">Responsable</span></th>
                                        <th className="hidden lg:table-cell px-8 py-6"><span className="text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic leading-none">Punto de Despliegue</span></th>
                                        <th className="px-8 py-6"><span className="text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic leading-none">Cronograma</span></th>
                                        <th className="hidden sm:table-cell px-8 py-6 text-center"><span className="text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic leading-none">Asistencia</span></th>
                                        <th className="px-8 py-6 text-center"><span className="text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic leading-none">HUD</span></th>
                                        <th className="px-8 py-6 text-right"><span className="text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic leading-none">Comandos</span></th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-white/5">
                                    {history.data.length === 0 && (
                                        <tr>
                                            <td colSpan="7" className="px-10 py-32 text-center">
                                                <div className="flex flex-col items-center gap-2">
                                                    <span className="text-sm font-black text-white/5 uppercase tracking-[0.4em] italic leading-relaxed">
                                                        Sin registros de entrenamiento
                                                    </span>
                                                    <span className="text-[9px] tracking-widest text-white/3 italic uppercase font-black">
                                                        Inyecte una nueva capacitación para iniciar el seguimiento
                                                    </span>
                                                </div>
                                            </td>
                                        </tr>
                                    )}
                                    {history.data.map(session => (
                                        <tr key={session.id} className="group/row hover:bg-white/5 transition-all duration-500 relative">
                                            <td className="px-8 py-6 relative">
                                                <div className="flex items-center gap-4">
                                                    <SourceIcon adhoc={session.is_adhoc || session.training_id === null} />
                                                    <div className="flex flex-col">
                                                        <div className="text-sm font-black text-white italic uppercase tracking-tight leading-tight group-hover/row:text-indigo-400 transition-colors">
                                                            {session.title}
                                                        </div>
                                                        <div className="text-[8px] font-black text-white/20 uppercase tracking-widest italic mt-1">
                                                            {session.is_adhoc ? 'Inyección Manual / Ad-Hoc' : 'Plan Operativo Maestro'}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>

                                            <td className="hidden xl:table-cell px-8 py-6">
                                                <div className="flex items-center gap-3">
                                                    <div className="w-8 h-8 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center text-[10px] font-black text-indigo-400">
                                                        {session.visit?.user?.nombres?.charAt(0) || session.training?.responsible_user?.nombres?.charAt(0) || '?'}
                                                    </div>
                                                    <div className="flex flex-col">
                                                        <div className="text-[11px] font-black text-white/60 italic uppercase leading-none">
                                                            {session.visit?.user?.nombres || session.training?.responsible_user?.nombres || 'No asignado'}
                                                        </div>
                                                        <div className="text-[9px] font-bold text-white/20 italic mt-0.5">{session.visit?.user?.email || 'System'}</div>
                                                    </div>
                                                </div>
                                            </td>

                                            <td className="hidden lg:table-cell px-8 py-6">
                                                <div className="flex flex-col">
                                                    <div className="text-[10px] font-black text-white/60 italic uppercase truncate max-w-[180px] group-hover/row:text-orange-500 transition-colors">
                                                        {session.visit?.sede?.empresa?.nombre || 'General'}
                                                    </div>
                                                    <div className="text-[8px] font-bold text-white/20 italic mt-0.5 uppercase tracking-widest">
                                                        {session.visit?.sede?.nombre_sede || 'Base'}
                                                    </div>
                                                </div>
                                            </td>

                                            <td className="px-8 py-6">
                                                <div className="flex flex-col gap-1">
                                                    <div className="flex items-center gap-2 text-[10px] font-black text-white/60 italic">
                                                        <svg className="w-3.5 h-3.5 text-indigo-500/50" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="3"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                        {session.created_at ? new Date(session.created_at).toLocaleDateString() : 'N/A'}
                                                    </div>
                                                    <span className="text-[8px] font-black text-indigo-400 uppercase italic tracking-widest whitespace-nowrap">⏳ {session.duration_minutes || 60} min</span>
                                                </div>
                                            </td>

                                            <td className="hidden sm:table-cell px-8 py-6 text-center">
                                                <span className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-[9px] font-black bg-indigo-500/10 border border-indigo-500/30 text-indigo-400 shadow-[0_0_15px_rgba(99,102,241,0.1)]">
                                                    {Array.isArray(session.attendees) ? session.attendees.length : 0}
                                                </span>
                                            </td>

                                            <td className="px-8 py-6 text-center">
                                                <StatusBadge status="completed" />
                                            </td>

                                            <td className="px-8 py-6 text-right">
                                                <div className="flex justify-end gap-3 transition-all duration-500">
                                                    {!(session.is_adhoc || session.training_id === null) ? (
                                                        <Link 
                                                            href={route('gestor.trainings.attendance', session.id)}
                                                            className="h-10 px-4 bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 hover:bg-indigo-500 hover:text-white rounded-xl flex items-center justify-center gap-2 transition-all shadow-lg text-[9px] font-black uppercase italic tracking-widest"
                                                        >
                                                            <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="2.5"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                                            <span className="hidden md:inline">Plan Maestro</span>
                                                        </Link>
                                                    ) : (
                                                        <>
                                                            <a 
                                                                href={route('gestor.trainings.pdf.preview', session.id)}
                                                                target="_blank"
                                                                className="h-10 w-10 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 hover:bg-emerald-600 hover:text-white rounded-xl flex items-center justify-center transition-all shadow-lg"
                                                                title="Reporte PDF"
                                                            >
                                                                <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="2.5"><path d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                                            </a>
                                                            <Link 
                                                                href={route('gestor.trainings.edit', session.id)}
                                                                className="h-10 w-10 bg-white/5 border border-white/10 text-white/40 hover:bg-white/20 hover:text-white rounded-xl flex items-center justify-center transition-all shadow-lg"
                                                            >
                                                                <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="2.5"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                            </Link>
                                                        </>
                                                    )}
                                                </div>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>

                        {/* PAGINACIÓN TÁCTICA */}
                        <div className="px-10 py-8 border-t border-white/5 bg-black/20 flex flex-col sm:flex-row items-center justify-between gap-6 font-black italic">
                            <div className="text-[10px] text-white/20 uppercase tracking-[0.5em]">
                                TRAINING OPS ENGINE v3.2 • XamaSafe Secure
                            </div>
                            <div className="flex gap-2">
                                {history.links.map((link, i) => (
                                    <Link 
                                        key={i}
                                        href={link.url || '#'}
                                        dangerouslySetInnerHTML={{ __html: link.label }}
                                        className={`px-4 py-2 rounded-xl border text-[9px] ${link.active ? 'bg-indigo-500/10 border-indigo-500/30 text-indigo-400' : 'bg-white/5 border-white/5 text-white/20 hover:text-white'} ${!link.url && 'opacity-20 cursor-not-allowed'}`}
                                    />
                                ))}
                            </div>
                        </div>
                    </div>
                    
                    {/* SECCIÓN DE PLANTILLAS */}
                    <div className="space-y-6">
                        <div className="flex items-center gap-4 px-2">
                            <h3 className="text-xs font-black text-orange-500 uppercase tracking-[0.4em] italic">Plantillas Maestro</h3>
                            <div className="flex-1 h-px bg-orange-500/20"></div>
                        </div>
                        <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                            {templates.map(template => (
                                <div key={template.id} className="bg-[#16162a]/40 border border-white/5 rounded-[2rem] p-8 group hover:border-orange-500/30 transition-all relative overflow-hidden">
                                    <div className="absolute top-0 right-0 p-6 opacity-5 group-hover:opacity-10 transition-opacity">
                                        <svg className="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.168.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5S19.832 5.477 21 6.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" strokeWidth="2"/></svg>
                                    </div>
                                    <div className="text-[10px] font-black text-orange-500 uppercase tracking-widest italic mb-2">{template.type}</div>
                                    <h4 className="text-lg font-black text-white italic uppercase tracking-tight mb-2 group-hover:text-orange-400 transition-colors leading-tight">{template.title}</h4>
                                    <p className="text-[10px] font-bold text-white/40 uppercase italic tracking-wide line-clamp-2 min-h-[3em]">{template.description}</p>
                                    <div className="mt-6 pt-6 border-t border-white/5 flex justify-between items-center">
                                        <div className="text-[9px] font-black text-white/20 uppercase italic tracking-widest">⏳ {template.duration || 60} MIN</div>
                                        <Link href={route('gestor.trainings.edit', template.id)} className="p-2.5 bg-white/5 rounded-xl hover:bg-orange-600/20 text-white/20 hover:text-orange-500 transition-all border border-white/5">
                                            <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" strokeWidth="2"/></svg>
                                        </Link>
                                    </div>
                                </div>
                            ))}
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

function StatusBadge({ status }) {
    const config = {
        scheduled: { text: 'PROGRAMADA', style: 'text-amber-400 border-amber-500/30 bg-amber-500/10 shadow-[0_0_10px_rgba(245,158,11,0.1)]' },
        completed: { text: 'FINALIZADA', style: 'text-emerald-400 border-emerald-500/30 bg-emerald-500/10 shadow-[0_0_10px_rgba(16,185,129,0.1)]' },
        cancelled: { text: 'CANCELADA', style: 'text-red-400 border-red-500/30 bg-red-500/10 shadow-[0_0_10px_rgba(239,68,68,0.1)]' },
    };
    
    const { text, style } = config[status] || config.scheduled;
    
    return (
        <span className={`inline-flex px-4 py-1.5 rounded-full text-[8px] font-black uppercase tracking-widest italic border ${style}`}>
            {text}
        </span>
    );
}

function SourceIcon({ adhoc }) {
    return (
        <div className={`w-10 h-10 sm:w-12 sm:h-12 rounded-2xl border flex items-center justify-center text-xs font-black transition-transform hover:scale-110 shadow-lg ${adhoc ? 'bg-[#f69239]/10 border-[#f69239]/20 text-[#f69239]' : 'bg-indigo-500/10 border-indigo-500/20 text-indigo-400'}`}>
             {adhoc ? (
                 <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a2 2 0 00-1.96 1.414l-.718 2.154a2 2 0 01-3.04 1.088l-1.74-1.16a2 2 0 01-.832-2.338l.718-2.154a2 2 0 00-1.022-2.338l-2.387-.477a2 2 0 00-1.96 1.414l-.718 2.154a2 2 0 01-3.04 1.088l-1.74-1.16a2 2 0 01-.832-2.338l.718-2.154a2 2 0 00-1.022-2.338l-2.387-.477a2 2 0 00-1.96 1.414l-.718 2.154a2 2 0 01-3.04 1.088l-1.74-1.16a2 2 0 01-.832-2.338l.718-2.154a2 2 0 00-1.022-2.338z" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/></svg>
             ) : (
                 <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 14l9-5-9-5-9 5 9 5z" strokeWidth="2"/><path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" strokeWidth="2"/></svg>
             )}
        </div>
    );
}
