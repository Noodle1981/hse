import { router, Head, Link, usePage } from '@inertiajs/react';
import AuthenticatedLayout from '../../../Layouts/AuthenticatedLayout';
import { useState, useEffect } from 'react';

const ICONS = {
    'shield-check': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />',
    'helmet': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />',
    'alert': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />',
    'fire': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z" />',
    'truck': '<path d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 100-4 2 2 0 000 4zm10 0a2 2 0 100-4 2 2 0 000 4z" />',
    'tool': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />',
    'doc': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />',
    'eye': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />',
    'environment': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />',
    'tag': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2zM9 5h6" />',
    'extintor': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5h6M12 5v2M9 9h6v11a2 2 0 01-2 2h-2a2 2 0 01-2-2V9zm3-4a3 3 0 013-3h1M7 9a2 2 0 012-2h1M18 10v4a1 1 0 01-1 1h-2" />',
    'mineria': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2 17h20M3 17l2-8h14l2 8M6 12h12" />',
    'fabrica': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V9l-5 3V9l-5 3V9l-5 3v9h15zM17 5v4" />',
    'construccion': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 20h16M7 20l4-14a1 1 0 012 0l4 14M9 12h6M10 8h4" />',
    'bodega': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m-8-14v14" />',
    'colectivo': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 18v2a1 1 0 001 1h2a1 1 0 001-1v-2h8v2a1 1 0 001 1h2a1 1 0 001-1v-2M4 6h16a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V8a2 2 0 012-2zm2 3h4v4H6V9zm8 0h4v4h-4V9zM6 15h12M6 15h.01M18 15h.01" />',
    'camioneta': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 14h18M6 17a2 2 0 11-4 0 2 2 0 014 0zm12 0a2 2 0 11-4 0 2 2 0 014 0zm-12-3v-3a1 1 0 011-1h5l3 3h5v4M7 10h4v3H7v-3z" />',
};

export default function Index({ categories, filters }) {
    const { auth, flash } = usePage().props;
    
    const [search, setSearch] = useState(filters.search || '');
    const [showInactive, setShowInactive] = useState(filters.showInactive === 'true' || filters.showInactive === true);

    const updateFilters = () => {
        router.get(route('gestor.categories.index'), { search, showInactive }, {
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
        if (showInactive !== (filters.showInactive === 'true')) updateFilters();
    }, [showInactive]);

    const handleToggleStatus = (id) => {
        router.post(route('gestor.categories.toggle', ['category', id]), {}, {
            preserveScroll: true,
        });
    };

    return (
        <AuthenticatedLayout 
            title="Bóveda de Categorías"
            header={
                <div className="flex items-center gap-4">
                    <Link 
                        href={route('gestor.checklists.index')} 
                        className="flex items-center justify-center w-10 h-10 rounded-xl bg-white/5 border border-white/10 hover:bg-[#F69239]/10 hover:border-[#F69239]/30 text-white/60 hover:text-white transition-all group/back shadow-lg"
                        title="Volver a Plantillas de Checklist"
                    >
                        <svg className="w-5 h-5 transform group-hover/back:-translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={3} d="M15 19l-7-7 7-7" />
                        </svg>
                    </Link>
                    <div className="flex flex-col">
                        <h1 className="text-xs font-black text-ep-primary uppercase tracking-widest italic leading-none mb-1">
                            Plantillas de Checklist
                        </h1>
                        <h2 className="text-sm font-bold text-white uppercase tracking-tight leading-none italic">
                            Bóveda de Categorías
                        </h2>
                    </div>
                </div>
            }
        >
            <Head title="Gestión de Categorías" />

            <div className="px-4 sm:px-6 lg:px-8 py-8 text-white selection:bg-orange-500/30">
                <div className="max-w-[95rem] mx-auto space-y-8 animate-fade-in">
                    
                    {/* HUB DE FILTRADO */}
                    <div className="bg-[#16162a]/40 backdrop-blur-md rounded-[2.5rem] border border-white/5 p-6 shadow-2xl relative overflow-hidden animate-fade-in-down">
                        <div className="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-orange-500/30 to-transparent"></div>
                        
                        <div className="flex flex-col lg:flex-row gap-6 justify-between items-center relative z-10">
                            <div className="w-full lg:w-1/3 relative group">
                                <span className="absolute -top-2.5 left-4 bg-[#11111d] px-2 text-[8px] font-black text-orange-400 uppercase tracking-[0.3em] italic z-10">Filtro de Matriz</span>
                                <input id="search" name="search" 
                                    type="text" 
                                    value={search}
                                    onChange={e => setSearch(e.target.value)}
                                    placeholder="BUSCAR CATEGORÍA POR NOMBRE..."
                                    className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-orange-500/50 transition-all placeholder-white/5 italic uppercase"
                                />
                            </div>

                            <div className="flex items-center gap-6">
                                <label className="inline-flex items-center cursor-pointer group">
                                    <input id="showInactive" name="showInactive" type="checkbox" checked={showInactive} onChange={e => setShowInactive(e.target.checked)} className="sr-only peer" />
                                    <div className="relative w-11 h-6 bg-white/5 border border-white/10 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white/20 after:border-white/10 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-orange-500/50"></div>
                                    <span className="ms-3 text-[10px] font-black text-white/40 uppercase tracking-widest italic group-hover:text-orange-400 transition-colors">Ver Inactivas</span>
                                </label>

                                <Link href={route('gestor.categories.create')} 
                                      className="flex h-12 px-8 bg-[#F69239] text-white hover:brightness-110 active:scale-95 rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] italic transition-all items-center gap-3 shadow-[0_0_20px_rgba(246,146,57,0.3)]">
                                    <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="3">
                                        <path d="M12 4v16m8-8H4"/>
                                    </svg>
                                    Nueva Categoría
                                </Link>
                            </div>
                        </div>
                    </div>

                    {/* Tabla de Categorías */}
                    <div className="bg-[#16162a]/60 backdrop-blur-md rounded-[3rem] border border-white/5 shadow-2xl relative overflow-hidden animate-fade-in-up">
                        <div className="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-orange-500/20 to-transparent"></div>
                        
                        <div className="overflow-x-auto">
                            <table className="w-full border-collapse min-w-[1000px]">
                                <thead>
                                    <tr className="bg-black/20 text-left">
                                        <th className="px-6 py-6 text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic leading-none">Código / Identificación</th>
                                        <th className="px-6 py-6 text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic leading-none">Categoría Maestro</th>
                                        <th className="px-6 py-6 text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic leading-none text-center">Subcategorías</th>
                                        <th className="px-6 py-6 text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic leading-none text-center">Estado</th>
                                        <th className="px-6 py-6 text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic leading-none text-right">Acciones Tácticas</th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-white/5">
                                    {categories.data.map(cat => (
                                        <tr key={cat.id} className="group/row hover:bg-white/5 transition-all duration-500 relative">
                                            <td className="px-6 py-6">
                                                <div className="inline-flex px-4 py-2 bg-white/5 border border-white/5 rounded-xl text-[10px] font-black text-white/40 uppercase tracking-widest italic group-hover/row:border-orange-500/30 group-hover/row:text-orange-400 transition-all">
                                                    {cat.code}
                                                </div>
                                            </td>
                                            <td className="px-6 py-6">
                                                <div className="flex items-center gap-4">
                                                    <div className="w-12 h-12 rounded-2xl flex items-center justify-center border border-white/5 shadow-lg group-hover/row:scale-110 transition-transform" style={{ backgroundColor: `${cat.color}10`, color: cat.color, borderColor: `${cat.color}20` }}>
                                                        <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" dangerouslySetInnerHTML={{ __html: ICONS[cat.icon] || ICONS['tag'] }} />
                                                    </div>
                                                    <div>
                                                        <div className="text-sm font-black text-white italic uppercase tracking-tight leading-none group-hover/row:text-orange-400 transition-colors">
                                                            {cat.name}
                                                        </div>
                                                        <div className="text-[9px] font-bold text-white/20 uppercase tracking-widest mt-1.5 italic max-w-xs truncate">
                                                            {cat.description || 'Sin descripción detallada'}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td className="px-6 py-6 text-center">
                                                <Link href={route('gestor.categories.subcategories', cat.id)} className="inline-flex items-center gap-2 px-4 py-2 border border-white/5 bg-white/5 rounded-xl text-[10px] font-black text-white/40 hover:text-orange-400 hover:border-orange-500/30 transition-all italic uppercase">
                                                    <svg className="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" strokeWidth="2"/></svg>
                                                    {cat.subcategories_count} Subcategorías
                                                </Link>
                                            </td>
                                            <td className="px-6 py-6 text-center">
                                                <StatusBadge active={cat.is_active} text={cat.is_active ? 'OPERATIVA' : 'SUSPENDIDA'} />
                                            </td>
                                            <td className="px-6 py-6 text-right">
                                                <div className="flex justify-end gap-3 transition-all duration-500">
                                                    <Link href={route('gestor.categories.edit', cat.id)} className="h-10 w-10 bg-blue-500/10 border border-blue-500/20 text-blue-400 hover:bg-blue-500 hover:text-white rounded-xl flex items-center justify-center transition-all shadow-lg">
                                                        <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" strokeWidth="2"/></svg>
                                                    </Link>
                                                    <button onClick={() => handleToggleStatus(cat.id)} className={`h-10 w-10 border shadow-lg rounded-xl flex items-center justify-center transition-all ${cat.is_active ? 'bg-red-500/10 border-red-500/20 text-red-500 hover:bg-red-500 hover:text-white' : 'bg-emerald-500/10 border-emerald-500/20 text-emerald-500 hover:bg-emerald-500 hover:text-white'}`}>
                                                        {cat.is_active ? (
                                                            <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" strokeWidth="2"/></svg>
                                                        ) : (
                                                            <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" strokeWidth="2"/></svg>
                                                        )}
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>

                        {/* Pagination */}
                        <div className="px-10 py-6 border-t border-white/5 bg-black/20 flex items-center justify-between font-black italic text-[10px]">
                            <div className="text-white/20">Matrices de Clasificación: {categories.total}</div>
                            <div className="flex gap-2">
                                {categories.links.map((link, i) => (
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

function StatusBadge({ active, text }) {
    return (
        <span className={`inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[8px] font-black uppercase tracking-widest italic border ${active ? 'text-emerald-400 border-emerald-500/30 bg-emerald-500/10' : 'text-white/20 border-white/5 bg-white/5'}`}>
            <span className={`w-1 h-1 rounded-full ${active ? 'bg-emerald-500 animate-pulse' : 'bg-white/20'}`}></span>
            {text}
        </span>
    );
}
