import { Head, Link, router } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { useState } from 'react';

export default function History({ visits, filters }) {
    const [search, setSearch] = useState(filters.search || '');

    const handleSearch = (e) => {
        e.preventDefault();
        router.get(route('tecnico.visits.index'), { search }, { preserveState: true });
    };

    return (
        <AuthenticatedLayout title="Historial de Misiones">
            <Head title="Mis Visitas" />

            <div className="px-4 py-8 lg:p-12 text-white selection:bg-blue-500/30">
                <div className="max-w-7xl mx-auto space-y-10 animate-fade-in">
                    
                    <div className="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                        <div>
                            <h1 className="text-4xl font-black uppercase italic tracking-tighter leading-none mb-2">Historial de<br/><span className="text-blue-500">Misiones</span></h1>
                            <p className="text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic">Registro Cronológico de Despliegue</p>
                        </div>
                        
                        <form onSubmit={handleSearch} className="w-full md:w-96">
                            <input 
                                type="text" 
                                placeholder="BUSCAR POR SEDE..."
                                value={search}
                                onChange={e => setSearch(e.target.value)}
                                className="w-full bg-[#16162a]/40 border border-white/5 rounded-2xl px-6 py-4 text-xs font-bold text-white placeholder-white/10 focus:ring-2 focus:ring-blue-500 transition-all uppercase italic"
                            />
                        </form>
                    </div>

                    <div className="bg-[#11111d]/60 backdrop-blur-xl border border-white/5 rounded-[3rem] overflow-hidden shadow-3xl">
                        <table className="w-full text-left border-collapse">
                            <thead>
                                <tr className="bg-white/5 text-[9px] font-black uppercase italic tracking-[0.3em] text-white/40">
                                    <th className="px-10 py-6">Fecha / Sede</th>
                                    <th className="px-8 py-6">Estado / Código</th>
                                    <th className="px-10 py-6 text-right">Acciones</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-white/5">
                                {visits.data.map(visit => (
                                    <tr key={visit.id} className="group hover:bg-white/[0.02] transition-colors">
                                        <td className="px-10 py-8">
                                            <div className="text-[10px] font-black text-white/40 uppercase italic mb-1">{new Date(visit.visit_date).toLocaleDateString()}</div>
                                            <div className="text-sm font-black text-white uppercase italic tracking-tighter">{visit.sede?.nombre_sede}</div>
                                            <div className="text-[8px] font-black text-white/20 uppercase italic tracking-widest">{visit.sede?.empresa?.nombre}</div>
                                        </td>
                                        <td className="px-8 py-8">
                                            <span className="px-3 py-1 rounded-full text-[8px] font-black uppercase tracking-widest italic border border-emerald-500/20 bg-emerald-500/10 text-emerald-500">
                                                {visit.status.toUpperCase()}
                                            </span>
                                            <div className="text-[8px] font-black text-white/20 uppercase italic mt-2 tracking-widest">REG: {visit.code || `#${visit.id}`}</div>
                                        </td>
                                        <td className="px-10 py-8 text-right">
                                            <Link 
                                                href={route('tecnico.visits.certificate.preview', visit.id)}
                                                className="inline-flex items-center gap-2 px-6 py-2.5 bg-white/5 border border-white/10 rounded-xl text-[9px] font-black uppercase italic tracking-widest hover:bg-white/10 transition-all"
                                            >
                                                Ver Constancia
                                            </Link>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                        
                        {visits.data.length === 0 && (
                            <div className="py-24 text-center opacity-10 italic font-black uppercase tracking-[0.4em]">
                                No se encontraron registros finalizados
                            </div>
                        )}
                    </div>
                </div>
            </div>

            <style>{`
                .animate-fade-in { animation: fade-in 0.8s ease-out; }
                @keyframes fade-in { from { opacity: 0; } to { opacity: 1; } }
            `}</style>
        </AuthenticatedLayout>
    );
}
