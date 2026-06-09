import { router, Head, Link, usePage } from '@inertiajs/react';
import AuthenticatedLayout from '../../../Layouts/AuthenticatedLayout';
import { useState, useEffect } from 'react';

export default function Index({ checklists, categories, filters }) {
    const { auth, flash } = usePage().props;
    
    // ESTADOS TÁCTICOS DE FILTRADO
    const [search, setSearch] = useState(filters.search || '');
    const [categoryFilter, setCategoryFilter] = useState(filters.categoryFilter || '');
    const [statusFilter, setStatusFilter] = useState(filters.statusFilter || 'all');

    const updateFilters = () => {
        router.get(route('gestor.checklists.index'), { search, categoryFilter, statusFilter }, {
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
        if (categoryFilter !== (filters.categoryFilter || '') || statusFilter !== (filters.statusFilter || 'all')) {
            updateFilters();
        }
    }, [categoryFilter, statusFilter]);

    // OPERACIONES MAESTRAS
    const handleToggleStatus = (id) => {
        router.post(route('gestor.checklists.toggle', id), {}, {
            preserveScroll: true,
        });
    };

    const handleDuplicate = (id) => {
        if (confirm('SISTEMA: ¿Desea duplicar este protocolo como borrador?')) {
            router.post(route('gestor.checklists.duplicate', id));
        }
    };

    return (
        <AuthenticatedLayout 
            title="Gestión de Checklists"
            header={
                <div className="flex flex-col">
                    <h1 className="text-xs font-black text-ep-primary uppercase tracking-widest italic leading-none mb-1">
                        Gestión de <span className="text-white">Checklists</span>
                    </h1>
                    <h2 className="text-sm font-bold text-indigo-100 uppercase tracking-tight leading-none italic">
                        Diseño y Gestión de Guías de Inspección
                    </h2>
                </div>
            }
        >
            <Head title="Listado de Checklists" />

            <div className="px-4 sm:px-6 lg:px-8 py-8 text-white selection:bg-orange-500/30">
                <div className="max-w-[100rem] mx-auto space-y-8 animate-fade-in">
                    
                    {/* BUSCADOR Y FILTROS TÁCTICOS (Estilo Legacy) */}
                    <div className="bg-[#16162a]/60 backdrop-blur-xl rounded-[2.5rem] border border-white/5 p-8 shadow-2xl relative overflow-hidden animate-fade-in-down group/filters">
                        <div className="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-transparent via-orange-500 to-transparent opacity-50"></div>
                        
                        <div className="grid grid-cols-1 md:grid-cols-12 gap-6 items-end relative z-10">
                            
                            {/* Buscador */}
                            <div className="md:col-span-4 relative group">
                                <span className="absolute -top-2.5 left-4 bg-[#11111d] px-2 text-[8px] font-black text-orange-500 uppercase tracking-[0.3em] italic z-10">Buscador de Checklists</span>
                                <div className="relative">
                                    <div className="absolute inset-y-0 left-5 flex items-center pointer-events-none text-white/20 group-focus-within:text-orange-500">
                                        <svg className="w-5 h-5 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                    </div>
                                    <input id="search" name="search" 
                                        type="text" 
                                        value={search}
                                        onChange={e => setSearch(e.target.value)}
                                        className="w-full bg-black/40 border border-white/10 rounded-2xl pl-14 pr-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-orange-500/50 transition-all placeholder-white/5 italic"
                                        placeholder="Filtrar por Título o Código..."
                                    />
                                </div>
                            </div>

                            {/* Categoría */}
                            <div className="md:col-span-3 relative group">
                                <span className="absolute -top-2.5 left-4 bg-[#11111d] px-2 text-[8px] font-black text-indigo-400 uppercase tracking-[0.3em] italic z-10">Categoría</span>
                                <select id="categoryFilter" name="categoryFilter" 
                                    value={categoryFilter}
                                    onChange={e => setCategoryFilter(e.target.value)}
                                    className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-indigo-500/50 transition-all [color-scheme:dark] appearance-none cursor-pointer italic uppercase"
                                    title="Seleccione una categoría"
                                >
                                    <option value="">TODAS LAS CATEGORÍAS</option>
                                    {categories.map(cat => <option key={cat.id} value={cat.id}>{cat.name}</option>)}
                                </select>
                            </div>

                            {/* Estado Operativo */}
                            <div className="md:col-span-2 relative group">
                                <span className="absolute -top-2.5 left-4 bg-[#11111d] px-2 text-[8px] font-black text-emerald-400 uppercase tracking-[0.3em] italic z-10">Estado</span>
                                <select id="statusFilter" name="statusFilter" 
                                    value={statusFilter}
                                    onChange={e => setStatusFilter(e.target.value)}
                                    className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-emerald-500/50 transition-all [color-scheme:dark] appearance-none cursor-pointer italic uppercase"
                                    title="Seleccione un estado"
                                >
                                    <option value="all">TODOS</option>
                                    <option value="active">ACTIVOS</option>
                                    <option value="inactive">INACTIVOS</option>
                                </select>
                            </div>

                            {/* Botones de Acción */}
                            <div className="md:col-span-3 flex gap-3">
                                <Link 
                                    href={route('gestor.categories.index')} 
                                    className="flex-1 flex h-14 bg-white/5 border border-white/10 text-white hover:bg-white/10 hover:border-white/20 active:scale-95 rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] italic transition-all items-center justify-center gap-2 shadow-lg"
                                    title="Administrar Categorías y Subcategorías"
                                >
                                    <svg className="w-5 h-5 text-[#F69239]" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="2"><path d="M7 7h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2zM9 5h6"/></svg>
                                    <span>Categorías</span>
                                </Link>
                                <Link 
                                    href={route('gestor.checklists.create')} 
                                    className="flex-1 flex h-14 bg-orange-600 text-white hover:brightness-110 active:scale-95 rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] italic transition-all items-center justify-center gap-2 shadow-[0_0_25px_rgba(246,146,57,0.3)] group/btn"
                                >
                                    <svg className="w-5 h-5 group-hover:rotate-90 transition-transform duration-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="3"><path d="M12 4v16m8-8H4"/></svg>
                                    <span>Nuevo</span>
                                </Link>
                            </div>
                        </div>
                    </div>

                    {/* MATRIZ MAESTRA (TABLA TÉCNICA CLONE) */}
                    <div className="bg-[#16162a]/60 backdrop-blur-xl rounded-[3rem] border border-white/5 shadow-2xl relative overflow-hidden group/matrix mb-20 animate-fade-in-up">
                        <div className="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-indigo-500/20 to-transparent"></div>
                        
                        <div className="overflow-x-auto">
                            <table className="table-auto w-full border-collapse">
                                <thead>
                                    <tr className="bg-black/40 text-left">
                                        <th className="px-10 py-8"><span className="text-[11px] font-black text-white/40 uppercase tracking-[0.4em] italic leading-none">Diseño de Checklist</span></th>
                                        <th className="hidden md:table-cell px-10 py-8"><span className="text-[11px] font-black text-white/40 uppercase tracking-[0.4em] italic leading-none">Categorización</span></th>
                                        <th className="hidden lg:table-cell px-10 py-8 text-center"><span className="text-[11px] font-black text-white/40 uppercase tracking-[0.4em] italic leading-none">Variables</span></th>
                                        <th className="px-10 py-8 text-center"><span className="text-[11px] font-black text-white/40 uppercase tracking-[0.4em] italic leading-none">Estado</span></th>
                                        <th className="px-10 py-8 text-right"><span className="text-[11px] font-black text-white/40 uppercase tracking-[0.4em] italic leading-none">Comandos</span></th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-white/5">
                                    {checklists.data.map(checklist => (
                                        <tr key={checklist.id} className="group/row hover:bg-white/5 transition-all duration-500 relative">
                                            {/* Indicador lateral legacy */}
                                            <td className="px-10 py-8 relative">
                                                <div className={`absolute left-0 top-1/2 -translate-y-1/2 w-1.5 h-12 ${checklist.is_active ? 'bg-orange-500 shadow-[0_0_15px_#f69239]' : 'bg-white/10'} rounded-r-full transition-all opacity-0 group-hover/row:opacity-100`}></div>
                                                
                                                <div className="flex flex-col">
                                                    <div className="text-base font-black text-white italic uppercase tracking-tight leading-tight mb-2 group-hover/row:text-orange-500 transition-colors">
                                                        {checklist.title}
                                                    </div>
                                                    {checklist.registry_code && (
                                                        <div className="inline-flex items-center px-3 py-1 bg-indigo-500/10 border border-indigo-500/20 rounded-lg text-[8px] font-black text-indigo-400 italic w-fit uppercase tracking-widest leading-none">
                                                            CODE: {checklist.registry_code}
                                                        </div>
                                                    )}
                                                </div>
                                            </td>

                                            <td className="hidden md:table-cell px-10 py-8">
                                                <div className="flex flex-col gap-2">
                                                    {checklist.category_relation && (
                                                        <div className="inline-flex items-center gap-3">
                                                            <div className="w-2.5 h-2.5 rounded-full shadow-lg" style={{ backgroundColor: checklist.category_relation.color, boxShadow: `0 0 10px ${checklist.category_relation.color}` }}></div>
                                                            <span className="text-[10px] font-black text-white/60 uppercase tracking-widest italic">{checklist.category_relation.name}</span>
                                                        </div>
                                                    )}
                                                    {checklist.description && (
                                                        <span className="text-[9px] text-white/20 font-bold uppercase tracking-wider truncate max-w-[200px] italic">
                                                            {checklist.description}
                                                        </span>
                                                    )}
                                                </div>
                                            </td>

                                            <td className="hidden lg:table-cell px-10 py-8 text-center">
                                                <div className="inline-flex flex-col items-center">
                                                    <div className="text-2xl font-black text-white italic leading-none mb-1">{checklist.questions_count}</div>
                                                    <div className="text-[8px] font-black text-white/20 uppercase tracking-[0.2em] italic">Reactivos</div>
                                                </div>
                                            </td>

                                            <td className="px-10 py-8 text-center">
                                                <button 
                                                    onClick={() => handleToggleStatus(checklist.id)}
                                                    className={`px-4 py-2.5 rounded-2xl text-[9px] font-black uppercase tracking-[0.2em] italic transition-all border flex items-center gap-3 mx-auto active:scale-95 ${checklist.is_active ? 'bg-emerald-500/10 border-emerald-500/40 text-emerald-400 shadow-[0_0_15px_rgba(16,185,129,0.1)]' : 'bg-red-500/10 border-red-500/20 text-red-500'}`}
                                                >
                                                    <div className={`w-2 h-2 rounded-full ${checklist.is_active ? 'bg-emerald-400 shadow-[0_0_8px_#10b981] animate-pulse' : 'bg-red-500'}`}></div>
                                                    <span>{checklist.is_active ? 'ONLINE' : 'OFFLINE'}</span>
                                                </button>
                                            </td>

                                            <td className="px-10 py-8 text-right">
                                                <div className="flex justify-end gap-3 transition-all duration-500">
                                                    {/* BOTÓN EMULADOR (MOBILE STYLE) */}
                                                    <Link 
                                                        href={route('tecnico.checklists.fill', { id: checklist.id })}
                                                        className="h-11 w-11 bg-white/5 border border-white/10 text-white hover:bg-white/20 rounded-xl flex items-center justify-center transition-all group/emu shadow-lg"
                                                        title="Emulación Técnica"
                                                    >
                                                        <svg className="w-5 h-5 group-hover/emu:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                                    </Link>

                                                    <button 
                                                        onClick={() => handleDuplicate(checklist.id)}
                                                        className="h-11 w-11 bg-white/5 border border-white/10 text-white hover:bg-orange-600 hover:text-white rounded-xl flex items-center justify-center transition-all shadow-lg"
                                                        title="Duplicar"
                                                    >
                                                        <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="2"><path d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"/></svg>
                                                    </button>

                                                    <Link 
                                                        href={route('gestor.checklists.edit', checklist.id)}
                                                        className="h-11 px-6 bg-white/5 border border-white/10 text-white hover:bg-orange-500 hover:text-white rounded-xl text-[9px] font-black uppercase tracking-widest italic flex items-center gap-3 transition-all shadow-lg"
                                                    >
                                                        <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="2"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                        <span>Editar</span>
                                                    </Link>
                                                </div>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>

                        {/* PAGINACIÓN TÁCTICA */}
                        <div className="px-10 py-8 border-t border-white/5 bg-black/20 flex flex-col sm:flex-row items-center justify-between gap-6 font-black italic">
                            <div className="text-[10px] text-white/10 uppercase tracking-[0.5em]">
                                Dynamic Checklists Engine v4.2 • Secure System
                            </div>
                            <div className="flex gap-2">
                                {checklists.links.map((link, i) => (
                                    <Link 
                                        key={i}
                                        href={link.url || '#'}
                                        dangerouslySetInnerHTML={{ __html: link.label }}
                                        className={`px-4 py-2 rounded-xl border text-[9px] ${link.active ? 'bg-orange-500/10 border-orange-500/30 text-orange-400' : 'bg-white/5 border-white/5 text-white/20 hover:text-white'} ${!link.url && 'opacity-20 cursor-not-allowed'}`}
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
