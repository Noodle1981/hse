import { router, Head, Link, usePage } from '@inertiajs/react';
import AuthenticatedLayout from '../../../Layouts/AuthenticatedLayout';
import { useState, useEffect } from 'react';

export default function Index({ checklists, categories, filters }) {
    const { auth, flash } = usePage().props;
    
    const [search, setSearch] = useState(filters.search || '');
    const [categoryFilter, setCategoryFilter] = useState(filters.categoryFilter || '');
    const [statusFilter, setStatusFilter] = useState(filters.statusFilter || 'active');

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
        if (categoryFilter !== (filters.categoryFilter || '') || statusFilter !== (filters.statusFilter || 'active')) {
            updateFilters();
        }
    }, [categoryFilter, statusFilter]);

    const handleToggleStatus = (id) => {
        router.post(route('gestor.checklists.toggle', id), {}, {
            preserveScroll: true,
        });
    };

    const handleDuplicate = (id) => {
        if (confirm('¿Desea duplicar este checklist como borrador?')) {
            router.post(route('gestor.checklists.duplicate', id));
        }
    };

    return (
        <AuthenticatedLayout title="Matriz Maestro de Checklists">
            <Head title="Checklists" />

            <div className="px-4 sm:px-6 lg:px-8 py-8 text-white selection:bg-orange-500/30">
                <div className="max-w-[95rem] mx-auto space-y-8 animate-fade-in">
                    
                    {/* HUB DE FILTRADO */}
                    <div className="bg-[#16162a]/40 backdrop-blur-md rounded-[2.5rem] border border-white/5 p-6 shadow-2xl relative overflow-hidden animate-fade-in-down">
                        <div className="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-orange-500/30 to-transparent"></div>
                        
                        <div className="flex flex-col lg:flex-row gap-6 justify-between items-center relative z-10">
                            <div className="flex-1 flex flex-col lg:flex-row gap-4 w-full">
                                <div className="relative group flex-1">
                                    <span className="absolute -top-2.5 left-4 bg-[#11111d] px-2 text-[8px] font-black text-orange-400 uppercase tracking-[0.3em] italic z-10">Explorador de Matrices</span>
                                    <input 
                                        type="text" 
                                        value={search}
                                        onChange={e => setSearch(e.target.value)}
                                        placeholder="BUSCAR CHECKLIST POR TÍTULO..."
                                        className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-orange-500/50 transition-all placeholder-white/5 italic uppercase"
                                    />
                                </div>
                                <div className="relative group lg:w-64">
                                    <span className="absolute -top-2.5 left-4 bg-[#11111d] px-2 text-[8px] font-black text-orange-400 uppercase tracking-[0.3em] italic z-10">Categoría</span>
                                    <select value={categoryFilter} onChange={e => setCategoryFilter(e.target.value)} className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-orange-500/50 transition-all [color-scheme:dark] italic uppercase">
                                        <option value="">TODAS LAS CATEGORÍAS</option>
                                        {categories.map(cat => <option key={cat.id} value={cat.id}>{cat.name}</option>)}
                                    </select>
                                </div>
                                <div className="relative group lg:w-48">
                                    <span className="absolute -top-2.5 left-4 bg-[#11111d] px-2 text-[8px] font-black text-orange-400 uppercase tracking-[0.3em] italic z-10">Estado</span>
                                    <select value={statusFilter} onChange={e => setStatusFilter(e.target.value)} className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-orange-500/50 transition-all [color-scheme:dark] italic uppercase">
                                        <option value="active">Solo Activos</option>
                                        <option value="inactive">Solo Inactivos</option>
                                        <option value="all">Ver Todos</option>
                                    </select>
                                </div>
                            </div>

                            <Link href={route('gestor.checklists.create')} 
                                  className="flex h-12 px-8 bg-orange-600 text-white hover:brightness-110 active:scale-95 rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] italic transition-all items-center gap-3 shadow-[0_0_20px_rgba(246,146,57,0.3)]">
                                <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="3">
                                    <path d="M12 4v16m8-8H4"/>
                                </svg>
                                Nueva Matriz
                            </Link>
                        </div>
                    </div>

                    {/* Grid de Checklists */}
                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 animate-fade-in-up">
                        {checklists.data.map(checklist => (
                            <div key={checklist.id} className={`group bg-[#16162a]/60 backdrop-blur-md rounded-[2.5rem] border border-white/5 p-8 shadow-2xl relative overflow-hidden transition-all duration-500 hover:bg-white/5 hover:border-orange-500/20 ${checklist.is_draft ? 'opacity-70 border-dashed' : ''}`}>
                                
                                {/* Overlay de Acciones */}
                                <div className="absolute top-0 right-0 p-6 flex gap-2 translate-x-10 opacity-0 group-hover:translate-x-0 group-hover:opacity-100 transition-all duration-500">
                                    <button onClick={() => handleDuplicate(checklist.id)} className="w-9 h-9 bg-orange-500/10 border border-orange-500/20 text-orange-400 hover:bg-orange-600 hover:text-white rounded-xl flex items-center justify-center transition-all shadow-lg" title="Duplicar">
                                        <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2" strokeWidth="2"/></svg>
                                    </button>
                                    <Link href={route('gestor.checklists.edit', checklist.id)} className="w-9 h-9 bg-blue-500/10 border border-blue-500/20 text-blue-400 hover:bg-blue-500 hover:text-white rounded-xl flex items-center justify-center transition-all shadow-lg" title="Editar">
                                        <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" strokeWidth="2"/></svg>
                                    </Link>
                                    <button onClick={() => handleToggleStatus(checklist.id)} className={`w-9 h-9 border shadow-lg rounded-xl flex items-center justify-center transition-all ${checklist.is_active ? 'bg-red-500/10 border-red-500/20 text-red-500 hover:bg-red-500 hover:text-white' : 'bg-emerald-500/10 border-emerald-500/20 text-emerald-500 hover:bg-emerald-500 hover:text-white'}`}>
                                        {checklist.is_active ? (
                                            <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" strokeWidth="2"/></svg>
                                        ) : (
                                            <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" strokeWidth="2"/></svg>
                                        )}
                                    </button>
                                </div>

                                <div className="space-y-6">
                                    <div className="flex items-center gap-3">
                                        <div className="w-2 h-10 bg-orange-500 rounded-full"></div>
                                        <div className="flex-1">
                                            <div className="text-[10px] font-black text-orange-400 uppercase tracking-[0.3em] italic mb-1">
                                                {checklist.category_relation?.name || 'SIN CATEGORÍA'}
                                            </div>
                                            <h4 className="text-lg font-black italic uppercase tracking-tight text-white leading-none truncate group-hover:text-orange-400 transition-colors">
                                                {checklist.title}
                                            </h4>
                                        </div>
                                    </div>

                                    <div className="bg-black/40 rounded-2xl p-4 border border-white/5 space-y-3">
                                        <div className="flex items-center justify-between">
                                            <span className="text-[9px] font-black text-white/20 uppercase italic">Complejidad</span>
                                            <span className="text-[9px] font-black text-white/60 uppercase italic">{checklist.questions_count} Reactivos</span>
                                        </div>
                                        <div className="w-full h-1 bg-white/5 rounded-full overflow-hidden">
                                            <div className="h-full bg-orange-500/40" style={{ width: `${Math.min(checklist.questions_count * 5, 100)}%` }}></div>
                                        </div>
                                        <div className="flex items-center justify-between">
                                            <span className="text-[9px] font-black text-white/20 uppercase italic">Identificador</span>
                                            <span className="text-[9px] font-black text-white/40 uppercase italic">{checklist.registry_code || 'S/N'}</span>
                                        </div>
                                    </div>

                                    <p className="text-[10px] font-bold text-white/30 italic uppercase leading-relaxed line-clamp-2">
                                        {checklist.description || 'Sin descripción detallada registrada para esta matriz táctica.'}
                                    </p>

                                    <div className="pt-4 flex items-center justify-between border-t border-white/5">
                                        <div className="flex -space-x-2">
                                            <div className="w-7 h-7 rounded-lg bg-orange-500/20 border border-orange-500/20 flex items-center justify-center text-[10px] font-black text-orange-400 italic">M</div>
                                            <div className="w-7 h-7 rounded-lg bg-blue-500/20 border border-blue-500/20 flex items-center justify-center text-[10px] font-black text-blue-400 italic">T</div>
                                        </div>
                                        <StatusBadge active={checklist.is_active} text={checklist.is_active ? 'OPERATIVO' : 'EN DESUSO'} />
                                    </div>
                                </div>
                            </div>
                        ))}
                    </div>

                    {/* Pagination */}
                    <div className="px-10 py-6 bg-black/20 rounded-[2rem] border border-white/5 flex items-center justify-between font-black italic text-[10px]">
                        <div className="text-white/20">Checklists Registrados: {checklists.total}</div>
                        <div className="flex gap-2">
                            {checklists.links.map((link, i) => (
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

function StatusBadge({ active, text }) {
    return (
        <span className={`inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[8px] font-black uppercase tracking-widest italic border ${active ? 'text-emerald-400 border-emerald-500/30 bg-emerald-500/10' : 'text-white/20 border-white/5 bg-white/5'}`}>
            <span className={`w-1 h-1 rounded-full ${active ? 'bg-emerald-500 animate-pulse' : 'bg-white/20'}`}></span>
            {text}
        </span>
    );
}
