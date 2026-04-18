import { Head, Link, router, usePage } from '@inertiajs/react';
import AuthenticatedLayout from '../../../Layouts/AuthenticatedLayout';
import { useState } from 'react';

export default function Index({ templates, history, filters }) {
    const { auth } = usePage().props;
    const [searchTerm, setSearchTerm] = useState(filters.search || '');

    return (
        <AuthenticatedLayout title="Centro de Capacitación Táctica">
            <Head title="Gestión de Capacitaciones" />

            <div className="px-4 sm:px-6 lg:px-8 py-8 text-white selection:bg-orange-500/30">
                <div className="max-w-[95rem] mx-auto space-y-12">
                    
                    {/* HUB DE CABECERA */}
                    <div className="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 animate-fade-in-down">
                        <div>
                            <h1 className="text-3xl font-black uppercase italic tracking-tighter leading-none">Capacitaciones & Formación</h1>
                            <p className="text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic mt-2">Gestión de Plantillas y Registros de Asistencia</p>
                        </div>
                        <Link 
                            href={route('gestor.trainings.create')} 
                            className="px-10 py-5 bg-orange-600 text-white rounded-[2rem] text-[11px] font-black uppercase tracking-[0.2em] italic transition-all shadow-xl shadow-orange-600/20 hover:brightness-110 active:scale-95"
                        >
                            Nueva Plantilla
                        </Link>
                    </div>

                    <div className="grid grid-cols-1 lg:grid-cols-12 gap-10">
                        
                        {/* COLUMNA IZQUIERDA: PLANTILLAS (Templates) */}
                        <div className="lg:col-span-12 space-y-8 animate-fade-in-up">
                            <div className="bg-[#16162a]/40 backdrop-blur-md rounded-[3rem] border border-white/5 overflow-hidden shadow-2xl">
                                <div className="px-10 py-8 border-b border-white/5 bg-white/5 flex items-center justify-between">
                                    <h3 className="text-sm font-black text-white italic uppercase tracking-[0.3em] flex items-center gap-3">
                                        <span className="w-1.5 h-6 bg-orange-500 rounded-full shadow-[0_0_10px_rgba(246,146,57,0.5)]"></span>
                                        Plantillas Maestro
                                    </h3>
                                </div>
                                <div className="p-8 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                                    {templates.map(template => (
                                        <div key={template.id} className="bg-black/20 border border-white/5 rounded-[2rem] p-8 group hover:border-orange-500/30 transition-all relative overflow-hidden">
                                            <div className="absolute top-0 right-0 p-6 opacity-5 group-hover:opacity-10 transition-opacity">
                                                <svg className="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.168.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5S19.832 5.477 21 6.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" strokeWidth="2"/></svg>
                                            </div>
                                            <div className="text-[10px] font-black text-orange-500 uppercase tracking-widest italic mb-2">{template.type}</div>
                                            <h4 className="text-lg font-black text-white italic uppercase tracking-tight mb-2 group-hover:text-orange-400 transition-colors">{template.title}</h4>
                                            <p className="text-[10px] font-bold text-white/40 uppercase italic tracking-wide line-clamp-2 min-h-[3em]">{template.description}</p>
                                            <div className="mt-6 pt-6 border-t border-white/5 flex justify-between items-center">
                                                <div className="text-[9px] font-black text-white/20 uppercase italic tracking-widest">⏳ {template.duration || 30} MIN</div>
                                                <div className="flex gap-2">
                                                    <Link href={route('gestor.trainings.edit', template.id)} className="p-2 bg-white/5 rounded-lg hover:bg-white/10 text-white/40 hover:text-white transition-all border border-white/5">
                                                        <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" strokeWidth="2"/></svg>
                                                    </Link>
                                                </div>
                                            </div>
                                        </div>
                                    ))}
                                    {templates.length === 0 && (
                                        <div className="col-span-full py-20 text-center opacity-20">
                                            <p className="text-xs font-black uppercase tracking-widest italic">No hay plantillas registradas</p>
                                        </div>
                                    )}
                                </div>
                            </div>
                        </div>

                        {/* HISTORIAL OPERATIVO */}
                        <div className="lg:col-span-12 space-y-8 animate-fade-in-up delay-100">
                             <div className="bg-[#16162a]/40 backdrop-blur-md rounded-[3rem] border border-white/5 overflow-hidden shadow-2xl">
                                <div className="px-10 py-8 border-b border-white/5 bg-white/5">
                                    <h3 className="text-sm font-black text-white italic uppercase tracking-[0.3em] flex items-center gap-3">
                                        <span className="w-1.5 h-6 bg-blue-500 rounded-full shadow-[0_0_10px_rgba(59,130,246,0.5)]"></span>
                                        Registro Histórico de Sesiones
                                    </h3>
                                </div>
                                <div className="overflow-x-auto">
                                    <table className="w-full border-collapse">
                                        <thead>
                                            <tr className="bg-black/20 text-left">
                                                <th className="px-10 py-6 text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic">Fecha / Visit</th>
                                                <th className="px-6 py-6 text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic">Tema Capacitado</th>
                                                <th className="px-6 py-6 text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic">Sede / Ubicación</th>
                                                <th className="px-6 py-6 text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic text-center">Asistentes</th>
                                                <th className="px-10 py-6 text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic text-right">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody className="divide-y divide-white/5">
                                            {history.data.map(session => (
                                                <tr key={session.id} className="group hover:bg-white/5 transition-all">
                                                    <td className="px-10 py-6 text-[11px] font-black text-white italic uppercase tracking-tighter">
                                                        {new Date(session.created_at).toLocaleDateString()}
                                                        <div className="text-[8px] opacity-20 mt-1">Ref: #{session.visit_certificate_id}</div>
                                                    </td>
                                                    <td className="px-6 py-6">
                                                        <div className="text-sm font-black text-white italic uppercase group-hover:text-blue-400 transition-colors">{session.title}</div>
                                                        <div className="text-[9px] font-black text-white/20 uppercase italic tracking-widest mt-1">{session.is_adhoc ? 'In-Situ / Ad-Hoc' : 'Plantilla Maestro'}</div>
                                                    </td>
                                                    <td className="px-6 py-6">
                                                        <div className="text-[11px] font-black text-white/60 italic uppercase">{session.visit?.sede?.nombre_sede}</div>
                                                    </td>
                                                    <td className="px-6 py-6 text-center">
                                                        <span className="px-3 py-1 bg-blue-500/10 border border-blue-500/20 text-blue-400 rounded-lg text-[9px] font-black italic">
                                                            {Array.isArray(session.attendees) ? session.attendees.length : 0} ALUMNOS
                                                        </span>
                                                    </td>
                                                    <td className="px-10 py-6 text-right">
                                                        <div className="flex justify-end gap-3 opacity-0 group-hover:opacity-100 transition-all">
                                                            <Link href={route('gestor.trainings.attendance', session.id)} className="h-10 px-4 bg-white/5 border border-white/10 text-white/40 hover:text-white rounded-xl flex items-center gap-2 text-[9px] font-black uppercase tracking-widest italic">
                                                                Asistencia
                                                            </Link>
                                                            <a href={route('gestor.trainings.pdf.preview', session.id)} target="_blank" className="h-10 w-10 bg-red-600/10 border border-red-500/20 text-red-500 hover:bg-red-600 hover:text-white rounded-xl flex items-center justify-center transition-all">
                                                                <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" strokeWidth="2"/></svg>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            ))}
                                        </tbody>
                                    </table>
                                </div>
                             </div>
                        </div>

                    </div>
                </div>
            </div>

            <style>{`
                .animate-fade-in-down { animation: fade-in-down 0.6s ease-out forwards; }
                .animate-fade-in-up { animation: fade-in-up 0.5s ease-out forwards; }
                @keyframes fade-in-down { from { opacity: 0; transform: translateY(-20px); } to { opacity: 1; transform: translateY(0); } }
                @keyframes fade-in-up { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
                .delay-100 { animation-delay: 0.1s; }
            `}</style>
        </AuthenticatedLayout>
    );
}
