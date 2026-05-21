import { router, Head, Link, usePage } from '@inertiajs/react';
import AuthenticatedLayout from '../../../Layouts/AuthenticatedLayout';
import { useState, useEffect } from 'react';

export default function History({ inspections, filters }) {
    const { auth } = usePage().props;
    
    const [search, setSearch] = useState(filters.search || '');

    const updateFilters = () => {
        router.get(route('tecnico.inspections.history'), { search }, {
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

    return (
        <AuthenticatedLayout title="Bóveda de Certificados">
            <Head title="Mis Inspecciones" />

            <div className="px-4 sm:px-6 lg:px-8 py-8 text-white selection:bg-orange-500/30">
                <div className="max-w-[100rem] mx-auto space-y-8 animate-fade-in">
                    
                    {/* HUB DE BÚSQUEDA */}
                    <div className="bg-[#16162a]/40 backdrop-blur-md rounded-[2.5rem] border border-white/5 p-6 shadow-2xl relative overflow-hidden animate-fade-in-down">
                        <div className="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-orange-500/30 to-transparent"></div>
                        
                        <div className="flex flex-col lg:flex-row gap-6 justify-between items-center relative z-10">
                            <div className="w-full lg:w-1/3 relative group">
                                <span className="absolute -top-2.5 left-4 bg-[#11111d] px-2 text-[8px] font-black text-orange-400 uppercase tracking-[0.3em] italic z-10">Filtrar Mis Protocolos</span>
                                <input id="search" name="search" 
                                    type="text" 
                                    value={search}
                                    onChange={e => setSearch(e.target.value)}
                                    placeholder="BUSCAR POR SEDE O CHECKLIST..."
                                    className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-orange-500/50 transition-all placeholder-white/5 italic uppercase"
                                />
                            </div>

                            <div className="flex items-center gap-4">
                                <span className="text-[10px] font-black text-white/20 uppercase tracking-widest italic">Protocolos Certificados: {inspections.total}</span>
                            </div>
                        </div>
                    </div>

                    {/* Tabla de Historial */}
                    <div className="bg-[#16162a]/60 backdrop-blur-md rounded-[3rem] border border-white/5 shadow-2xl relative overflow-hidden animate-fade-in-up">
                        <div className="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-orange-500/20 to-transparent"></div>
                        
                        <div className="overflow-x-auto">
                            <table className="w-full border-collapse min-w-[1000px]">
                                <thead>
                                    <tr className="bg-black/20 text-left">
                                        <th className="px-10 py-6 text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic leading-none">Fecha / Hora</th>
                                        <th className="px-6 py-6 text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic leading-none">Tipo de Matriz</th>
                                        <th className="px-6 py-6 text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic leading-none">Sede / Unidad</th>
                                        <th className="px-10 py-6 text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic leading-none text-right">Comandos</th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-white/5">
                                    {inspections.data.map(ins => (
                                        <tr key={ins.id} className="group hover:bg-white/5 transition-all duration-300">
                                            <td className="px-10 py-6">
                                                <div className="text-sm font-black text-white italic uppercase tracking-tighter">
                                                    {new Date(ins.completed_at).toLocaleDateString()}
                                                </div>
                                                <div className="text-[9px] font-black text-white/20 uppercase tracking-widest mt-1 italic">
                                                    {new Date(ins.completed_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})} HRS
                                                </div>
                                            </td>
                                            <td className="px-6 py-6">
                                                <div className="flex items-center gap-4">
                                                    <div className="w-10 h-10 rounded-xl bg-orange-500/10 border border-orange-500/20 flex items-center justify-center text-orange-500">
                                                        <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" strokeWidth="2"/></svg>
                                                    </div>
                                                    <div>
                                                        <div className="text-sm font-black text-white italic uppercase group-hover:text-orange-400 transition-colors">{ins.checklist?.title}</div>
                                                        <div className="text-[9px] font-black text-white/20 uppercase tracking-widest mt-1 italic">Protocolo: {ins.checklist?.registry_code || 'S/N'}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td className="px-6 py-6">
                                                <div className="text-sm font-black text-white/60 italic uppercase tracking-wider">{ins.sede?.nombre_sede}</div>
                                                <div className="text-[9px] font-black text-white/20 uppercase tracking-widest mt-1 italic">Región Operativa</div>
                                            </td>
                                            <td className="px-10 py-6 text-right">
                                                <div className="flex items-center justify-end gap-3 opacity-0 group-hover:opacity-100 transition-all duration-300">
                                                    <Link href={route('tecnico.inspections.show', ins.id)} className="h-10 px-6 bg-white/5 border border-white/10 text-white/40 hover:bg-white/10 hover:text-white rounded-xl text-[9px] font-black uppercase tracking-widest italic flex items-center gap-2 transition-all">
                                                        Detalle
                                                    </Link>
                                                    <a href={route('gestor.inspections.pdf.preview', ins.id)} target="_blank" className="h-10 w-10 bg-red-500/10 border border-red-500/20 text-red-500 hover:bg-red-500 hover:text-white rounded-xl flex items-center justify-center transition-all shadow-lg">
                                                        <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" strokeWidth="2"/></svg>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>

                        {/* Pagination */}
                        <div className="px-10 py-6 border-t border-white/5 bg-black/20 flex items-center justify-between font-black italic text-[10px]">
                            <div className="text-white/20">Matrices Cerradas: {inspections.total}</div>
                            <div className="flex gap-2">
                                {inspections.links.map((link, i) => (
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
