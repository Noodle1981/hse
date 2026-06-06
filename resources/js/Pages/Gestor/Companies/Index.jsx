import { router, Head, Link, usePage } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { useState, useEffect } from 'react';

export default function Index({ companies, filters }) {
    const { auth, flash } = usePage().props;
    
    const [search, setSearch] = useState(filters.search || '');
    const [showDeleted, setShowDeleted] = useState(filters.showDeleted === 'true' || filters.showDeleted === true);
    const [showDeleteModal, setShowDeleteModal] = useState(false);
    const [companyToDelete, setCompanyToDelete] = useState(null);

    const updateFilters = () => {
        router.get(route('gestor.companies.index'), { search, showDeleted }, {
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
        if (showDeleted !== (filters.showDeleted === 'true')) updateFilters();
    }, [showDeleted]);

    const confirmDelete = (company) => {
        setCompanyToDelete(company);
        setShowDeleteModal(true);
    };

    const handleDelete = () => {
        router.delete(route('gestor.companies.delete', companyToDelete.id), {
            onSuccess: () => setShowDeleteModal(false),
        });
    };

    const handleRestore = (id) => {
        router.post(route('gestor.companies.restore', id));
    };

    return (
        <AuthenticatedLayout title="Corporativo">
            <Head title="Gestión de Empresas" />

            <div className="px-4 sm:px-6 lg:px-8 py-8 text-white selection:bg-blue-500/30">
                <div className="max-w-[95rem] mx-auto space-y-8 animate-fade-in">
                    
                    {/* HUB DE FILTRADO */}
                    <div className="bg-[#16162a]/40 backdrop-blur-md rounded-[2.5rem] border border-white/5 p-6 shadow-2xl relative overflow-hidden animate-fade-in-down">
                        <div className="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-blue-500/30 to-transparent"></div>
                        
                        <div className="flex flex-col lg:flex-row gap-6 justify-between items-center relative z-10">
                            <div className="w-full lg:w-1/3 relative group">
                                <span className="absolute -top-2.5 left-4 bg-[#11111d] px-2 text-[8px] font-black text-blue-400 uppercase tracking-[0.3em] italic z-10">Escaneo de Entidades</span>
                                <input id="search" name="search" 
                                    type="text" 
                                    value={search}
                                    onChange={e => setSearch(e.target.value)}
                                    placeholder="NOMBRE, RAZÓN SOCIAL O CUIT..."
                                    className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-blue-500/50 transition-all placeholder-white/5 italic uppercase"
                                />
                            </div>

                            <div className="flex items-center gap-6">
                                <label className="inline-flex items-center cursor-pointer group">
                                    <input id="showDeleted" name="showDeleted" type="checkbox" checked={showDeleted} onChange={e => setShowDeleted(e.target.checked)} className="sr-only peer" />
                                    <div className="relative w-11 h-6 bg-white/5 border border-white/10 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white/20 after:border-white/10 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-red-500/50"></div>
                                    <span className="ms-3 text-[10px] font-black text-white/40 uppercase tracking-widest italic group-hover:text-red-400 transition-colors">Archivo Histórico</span>
                                </label>

                                <Link href={route('gestor.companies.create')} 
                                      className="flex h-12 px-8 bg-blue-600 text-white hover:brightness-110 active:scale-95 rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] italic transition-all items-center gap-3 shadow-[0_0_20px_rgba(59,130,246,0.3)]">
                                    <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="3">
                                        <path d="M12 4v16m8-8H4"/>
                                    </svg>
                                    Nueva Empresa
                                </Link>
                            </div>
                        </div>
                    </div>

                    {/* Tabla de Empresas */}
                    <div className="bg-[#16162a]/60 backdrop-blur-md rounded-[3rem] border border-white/5 shadow-2xl relative overflow-hidden animate-fade-in-up">
                        <div className="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-blue-500/20 to-transparent"></div>
                        
                        <div className="overflow-x-auto">
                            <table className="w-full border-collapse min-w-[1000px]">
                                <thead>
                                    <tr className="bg-black/20 text-left">
                                        <th className="px-6 py-6 text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic leading-none">Corporativo / Identidad</th>
                                        <th className="px-6 py-6 text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic leading-none">CUIT / Fiscal</th>
                                        <th className="px-6 py-6 text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic leading-none text-center">Unidades (Sedes)</th>
                                        <th className="px-6 py-6 text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic leading-none text-right">Comandos</th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-white/5">
                                    {companies.data.map(company => (
                                        <tr key={company.id} className={`group/row hover:bg-white/5 transition-all duration-500 relative ${company.deleted_at ? 'bg-red-500/5' : ''}`}>
                                            <td className="px-6 py-6">
                                                <div className="flex items-center gap-4">
                                                    <div className="w-12 h-12 rounded-2xl bg-blue-500/10 border border-blue-500/20 flex items-center justify-center text-blue-400 text-xs font-black group-hover:scale-110 transition-transform">
                                                        {company.nombre.charAt(0)}
                                                    </div>
                                                    <div>
                                                        <div className={`text-sm font-black text-white italic uppercase tracking-tight leading-none group-hover/row:text-blue-400 transition-colors ${company.deleted_at ? 'line-through opacity-40' : ''}`}>
                                                            {company.nombre}
                                                        </div>
                                                        <div className="text-[9px] font-bold text-white/20 uppercase tracking-widest mt-1 italic">
                                                            {company.razon_social}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td className="px-6 py-6">
                                                <div className="text-[11px] font-bold text-white/40 italic mb-1 group-hover/row:text-white/60 transition-colors">{company.cuit}</div>
                                                <div className="text-[9px] font-black text-blue-400/40 italic leading-none">{company.email || 'SIN EMAIL'}</div>
                                            </td>
                                            <td className="px-6 py-6 text-center">
                                                <span className="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest italic border border-white/5 bg-white/5 text-white/40 group-hover/row:border-blue-500/30 group-hover/row:text-blue-400 transition-all">
                                                    <svg className="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" strokeWidth="2"/></svg>
                                                    {company.sedes_count} Sedes
                                                </span>
                                            </td>
                                            <td className="px-6 py-6 text-right">
                                                <div className="flex justify-end gap-3 transition-all duration-500">
                                                    {company.deleted_at ? (
                                                        <button onClick={() => handleRestore(company.id)} className="h-10 px-4 bg-emerald-500/10 border border-emerald-500/20 text-emerald-500 hover:bg-emerald-500 hover:text-white rounded-xl flex items-center gap-2 text-[9px] font-black uppercase tracking-widest italic transition-all">
                                                            Restaurar
                                                        </button>
                                                    ) : (
                                                        <>
                                                            <Link href={route('gestor.companies.locations', company.id)} className="h-10 px-4 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 hover:bg-emerald-500 hover:text-white rounded-xl flex items-center gap-2 text-[9px] font-black uppercase tracking-widest italic transition-all shadow-lg">
                                                                Sedes
                                                            </Link>
                                                            <Link href={route('gestor.companies.edit', company.id)} className="h-10 w-10 bg-blue-500/10 border border-blue-500/20 text-blue-400 hover:bg-blue-500 hover:text-white rounded-xl flex items-center justify-center transition-all shadow-lg">
                                                                <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" strokeWidth="2"/></svg>
                                                            </Link>
                                                            <button onClick={() => confirmDelete(company)} className="h-10 w-10 bg-red-600/10 border border-red-500/20 text-red-500 hover:bg-red-600 hover:text-white rounded-xl flex items-center justify-center transition-all shadow-lg">
                                                                <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" strokeWidth="2"/></svg>
                                                            </button>
                                                        </>
                                                    )}
                                                </div>
                                            </td>
                                        </tr>
                                    ))}
                                    {companies.data.length === 0 && (
                                        <tr>
                                            <td colSpan="4" className="px-8 py-32 text-center text-white/5 uppercase font-black text-sm tracking-[0.4em] italic leading-relaxed">Sin registros corporativos</td>
                                        </tr>
                                    )}
                                </tbody>
                            </table>
                        </div>

                        {/* Pagination */}
                        <div className="px-10 py-6 border-t border-white/5 bg-black/20 flex items-center justify-between font-black italic text-[10px]">
                            <div className="text-white/20">Registros Totales: {companies.total}</div>
                            <div className="flex gap-2">
                                {companies.links.map((link, i) => (
                                    <Link 
                                        key={i}
                                        href={link.url || '#'}
                                        dangerouslySetInnerHTML={{ __html: link.label }}
                                        className={`px-4 py-2 rounded-xl border ${link.active ? 'bg-blue-500/10 border-blue-500/30 text-blue-400' : 'bg-white/5 border-white/5 text-white/20 hover:text-white'} ${!link.url && 'opacity-20 cursor-not-allowed'}`}
                                    />
                                ))}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {/* Delete Modal */}
            {showDeleteModal && (
                <div className="fixed inset-0 z-50 flex items-center justify-center p-4 animate-fade-in">
                    <div className="absolute inset-0 bg-black/80 backdrop-blur-sm" onClick={() => setShowDeleteModal(false)}></div>
                    <div className="bg-[#16162a] border border-white/10 rounded-[3rem] p-10 max-w-lg w-full relative z-10 shadow-2xl text-center overflow-hidden">
                        <div className="absolute inset-x-0 top-0 h-1 bg-red-500/50"></div>
                        <div className="w-20 h-20 bg-red-500/20 rounded-3xl flex items-center justify-center mx-auto mb-6 text-red-500 border border-red-500/20">
                            <svg className="w-10 h-10 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={3} d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        </div>
                        <h3 className="text-xl font-black text-white uppercase italic tracking-widest mb-4">Eliminar Entidad</h3>
                        <p className="text-sm text-white/40 mb-10 font-bold leading-relaxed italic uppercase">
                            ¿CONFIRMA LA BAJA DE <span className="text-red-400">{companyToDelete?.nombre}</span>?<br/><br/>
                            ESTO AFECTARÁ LA VISIBILIDAD DE SUS SEDES Y ASIGNACIONES ASOCIADAS.
                        </p>
                        <div className="flex justify-center gap-4">
                            <button onClick={() => setShowDeleteModal(false)} className="px-8 py-4 bg-white/5 text-white/40 hover:text-white rounded-2xl text-[10px] font-black uppercase tracking-widest italic transition-all">Cancelar</button>
                            <button onClick={handleDelete} className="px-10 py-4 bg-red-600 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest italic transition-all shadow-lg animate-pulse">Confirmar Baja</button>
                        </div>
                    </div>
                </div>
            )}

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
