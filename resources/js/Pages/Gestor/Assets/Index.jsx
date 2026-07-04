import { router, Head, Link, usePage } from '@inertiajs/react';
import AuthenticatedLayout from '../../../Layouts/AuthenticatedLayout';
import { useState, useEffect } from 'react';

export default function Index({ assets, kpis, filters, empresas = [], sedes = [], assetTypes = [] }) {
    const { flash } = usePage().props;

    const [search, setSearch] = useState(filters.search || '');
    const [empresaId, setEmpresaId] = useState(filters.empresa_id || '');
    const [sedeId, setSedeId] = useState(filters.sede_id || '');
    const [assetTypeId, setAssetTypeId] = useState(filters.asset_type_id || '');
    const [status, setStatus] = useState(filters.status || 'all');

    const updateFilters = () => {
        router.get(route('gestor.assets.index'), {
            search,
            empresa_id: empresaId,
            sede_id: sedeId,
            asset_type_id: assetTypeId,
            status,
        }, {
            preserveState: true,
            replace: true,
        });
    };

    useEffect(() => {
        const timeout = setTimeout(() => {
            if (
                search !== (filters.search || '') ||
                empresaId !== (filters.empresa_id || '') ||
                sedeId !== (filters.sede_id || '') ||
                assetTypeId !== (filters.asset_type_id || '') ||
                status !== (filters.status || 'all')
            ) {
                updateFilters();
            }
        }, 400);
        return () => clearTimeout(timeout);
    }, [search, empresaId, sedeId, assetTypeId, status]);

    const handleDelete = (asset) => {
        if (confirm(`¿Dar de baja el activo ${asset.name} (${asset.identifier})?`)) {
            router.delete(route('gestor.assets.destroy', asset.id));
        }
    };

    const availableSedes = empresaId
        ? sedes.filter(s => s.empresa_id.toString() === empresaId.toString())
        : sedes;

    const getStatusBadge = (asset) => {
        const st = asset.status;
        switch (st) {
            case 'operativo':
                return (
                    <span className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                        <span className="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        Operativo
                    </span>
                );
            case 'alerta':
                return (
                    <div className="relative inline-block group/tooltip">
                        <span className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-amber-500/10 text-amber-400 border border-amber-500/20 cursor-help">
                            <span className="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                            Alerta
                        </span>
                        
                        {asset.hallazgos && asset.hallazgos.length > 0 && (
                            <div className="absolute left-1/2 top-full mt-2 -translate-x-1/2 w-64 bg-[#141426] border border-amber-500/30 p-4 rounded-2xl shadow-2xl text-[10px] text-indigo-100 hidden group-hover/tooltip:block z-50 pointer-events-none transition-all">
                                <div className="absolute top-[-6px] left-1/2 -translate-x-1/2 w-3 h-3 bg-[#141426] border-l border-t border-amber-500/30 rotate-45" />
                                <span className="text-[10px] font-black text-amber-400 uppercase tracking-widest block mb-2 border-b border-white/5 pb-1">⚠️ DETALLES DE ALERTA</span>
                                <div className="space-y-2">
                                    {asset.hallazgos.map(h => {
                                        let desc = h.descripcion || '';
                                        let label = h.titulo.split(' - ')[1] || 'Alerta';
                                        if (label === 'fecha_de_proxima_carga_vencimiento') {
                                            label = 'Vencimiento Carga';
                                        } else if (label === 'vencimiento_prueba_hidraulica') {
                                            label = 'Prueba Hidráulica';
                                        } else {
                                            label = label.replace(/_/g, ' ').toUpperCase();
                                        }

                                        let valor = '';
                                        let dias = '';
                                        const valMatch = desc.match(/Valor:\s*(\S+)/i);
                                        const daysMatch = desc.match(/vence en\s*(\d+\s*días)/i);
                                        
                                        if (valMatch) valor = valMatch[1];
                                        if (daysMatch) dias = daysMatch[1];

                                        return (
                                            <div key={h.id} className="space-y-0.5 border-b border-white/5 pb-1.5 last:border-0 last:pb-0 text-left">
                                                <span className="text-[9px] font-black text-white/60 block uppercase tracking-wide">{label}</span>
                                                <div className="flex justify-between font-mono text-[9px] text-amber-300">
                                                    <span>Vence: {valor ? valor : 'N/D'}</span>
                                                    <span className="font-bold text-red-400">({dias ? dias : 'Vencido'})</span>
                                                </div>
                                            </div>
                                        );
                                    })}
                                </div>
                            </div>
                        )}
                    </div>
                );
            case 'fuera_de_servicio':
                return (
                    <span className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-red-500/10 text-red-400 border border-red-500/20">
                        <span className="w-2 h-2 rounded-full bg-red-500 animate-pulse"></span>
                        Fuera de Servicio
                    </span>
                );
            default:
                return (
                    <span className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-gray-500/10 text-gray-400 border border-gray-500/20">
                        {st}
                    </span>
                );
        }
    };

    return (
        <AuthenticatedLayout title="Bóveda Universal de Activos">
            <Head title="Gestión de Activos" />

            <div className="min-h-screen bg-[#11111d] text-white p-4 sm:p-8">
                <div className="max-w-7xl mx-auto space-y-8">
                    
                    {/* HEADER */}
                    <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-[#16162a] p-6 rounded-[2rem] border border-white/5 shadow-2xl">
                        <div>
                            <span className="text-[10px] font-black text-orange-500 uppercase tracking-[0.3em] italic">Módulo de Trazabilidad</span>
                            <h1 className="text-2xl font-black text-white uppercase tracking-tight italic">Activos y Equipamiento</h1>
                        </div>
                        <div className="flex flex-wrap gap-3">
                            <Link
                                href={route('gestor.asset-types.index')}
                                className="inline-flex items-center justify-center gap-2 px-5 py-3 bg-white/5 border border-white/10 text-white font-bold text-xs uppercase tracking-wider rounded-xl hover:bg-white/10 transition-all active:scale-95"
                            >
                                ⚙️ Tipos de Activos
                            </Link>
                            <Link
                                href={route('gestor.assets.create')}
                                className="inline-flex items-center justify-center gap-2 px-6 py-3 bg-gradient-to-r from-orange-500 to-amber-600 text-white font-black text-xs uppercase tracking-widest rounded-xl shadow-lg hover:brightness-110 transition-all active:scale-95"
                            >
                                <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2.5" d="M12 4v16m8-8H4"/></svg>
                                Nuevo Activo
                            </Link>
                        </div>
                    </div>

                    {/* FLASH MESSAGE */}
                    {flash?.message && (
                        <div className="p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl text-emerald-400 text-xs font-bold flex items-center justify-between">
                            <span>{flash.message}</span>
                        </div>
                    )}

                    {/* KPIS SUMMARY */}
                    <div className="grid grid-cols-2 sm:grid-cols-4 gap-4">
                        <div className="bg-[#16162a] p-5 rounded-2xl border border-white/5 shadow-xl">
                            <span className="text-[9px] font-black text-white/40 uppercase tracking-widest block mb-1">Total Activos</span>
                            <span className="text-2xl font-black text-white italic">{kpis.total}</span>
                        </div>
                        <div className="bg-[#16162a] p-5 rounded-2xl border border-emerald-500/10 shadow-xl">
                            <span className="text-[9px] font-black text-emerald-400 uppercase tracking-widest block mb-1">Operativos</span>
                            <span className="text-2xl font-black text-emerald-400 italic">{kpis.operativos}</span>
                        </div>
                        <div className="bg-[#16162a] p-5 rounded-2xl border border-amber-500/10 shadow-xl">
                            <span className="text-[9px] font-black text-amber-400 uppercase tracking-widest block mb-1">En Alerta</span>
                            <span className="text-2xl font-black text-amber-400 italic">{kpis.alerta}</span>
                        </div>
                        <div className="bg-[#16162a] p-5 rounded-2xl border border-red-500/10 shadow-xl">
                            <span className="text-[9px] font-black text-red-400 uppercase tracking-widest block mb-1">Fuera de Servicio</span>
                            <span className="text-2xl font-black text-red-400 italic">{kpis.fuera_de_servicio}</span>
                        </div>
                    </div>

                    {/* FILTROS */}
                    <div className="bg-[#16162a] p-6 rounded-[2rem] border border-white/5 space-y-4 shadow-xl">
                        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                            <div>
                                <label className="block text-[9px] font-black text-white/30 uppercase tracking-widest mb-1">Búsqueda</label>
                                <input
                                    type="text"
                                    value={search}
                                    onChange={e => setSearch(e.target.value)}
                                    placeholder="Nombre o Identificador/QR..."
                                    className="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-2.5 text-xs text-white placeholder-white/20 focus:outline-none focus:border-orange-500"
                                />
                            </div>
                            <div>
                                <label className="block text-[9px] font-black text-white/30 uppercase tracking-widest mb-1">Empresa</label>
                                <select
                                    value={empresaId}
                                    onChange={e => { setEmpresaId(e.target.value); setSedeId(''); }}
                                    className="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-orange-500"
                                >
                                    <option value="">Todas las Empresas</option>
                                    {empresas.map(e => (
                                        <option key={e.id} value={e.id}>{e.nombre}</option>
                                    ))}
                                </select>
                            </div>
                            <div>
                                <label className="block text-[9px] font-black text-white/30 uppercase tracking-widest mb-1">Sede</label>
                                <select
                                    value={sedeId}
                                    onChange={e => setSedeId(e.target.value)}
                                    className="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-orange-500"
                                >
                                    <option value="">Todas las Sedes</option>
                                    {availableSedes.map(s => (
                                        <option key={s.id} value={s.id}>{s.nombre_sede}</option>
                                    ))}
                                </select>
                            </div>
                            <div>
                                <label className="block text-[9px] font-black text-white/30 uppercase tracking-widest mb-1">Tipo de Activo</label>
                                <select
                                    value={assetTypeId}
                                    onChange={e => setAssetTypeId(e.target.value)}
                                    className="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-orange-500"
                                >
                                    <option value="">Todos los Tipos</option>
                                    {assetTypes.map(t => (
                                        <option key={t.id} value={t.id}>{t.name}</option>
                                    ))}
                                </select>
                            </div>
                            <div>
                                <label className="block text-[9px] font-black text-white/30 uppercase tracking-widest mb-1">Estado</label>
                                <select
                                    value={status}
                                    onChange={e => setStatus(e.target.value)}
                                    className="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-orange-500"
                                >
                                    <option value="all">Todos los Estados</option>
                                    <option value="operativo">Operativo</option>
                                    <option value="alerta">En Alerta</option>
                                    <option value="fuera_de_servicio">Fuera de Servicio</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    {/* TABLA DE ACTIVOS */}
                    <div className="bg-[#16162a] rounded-[2rem] border border-white/5 overflow-hidden shadow-2xl">
                        <div className="overflow-x-auto">
                            <table className="w-full text-left border-collapse">
                                <thead>
                                    <tr className="bg-black/20 border-b border-white/5 text-[9px] font-black text-white/30 uppercase tracking-widest">
                                        <th className="p-4 pl-6">Activo / Identificador</th>
                                        <th className="p-4">Tipo</th>
                                        <th className="p-4">Sede / Empresa</th>
                                        <th className="p-4">Estado</th>
                                        <th className="p-4 pr-6 text-right">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-white/5 text-xs">
                                    {assets.data.length === 0 ? (
                                        <tr>
                                            <td colSpan="5" className="p-12 text-center text-white/30 text-xs uppercase tracking-widest italic">
                                                No se encontraron activos registrados con los filtros aplicados.
                                            </td>
                                        </tr>
                                    ) : (
                                        assets.data.map(asset => (
                                            <tr key={asset.id} className="hover:bg-white/[0.02] transition-colors">
                                                <td className="p-4 pl-6">
                                                    <div className="font-bold text-white uppercase italic">{asset.name}</div>
                                                    <div className="text-[10px] text-orange-400 font-mono tracking-wider">QR/ID: {asset.identifier}</div>
                                                </td>
                                                <td className="p-4">
                                                    <span className="px-2.5 py-1 bg-white/5 border border-white/10 rounded-lg text-[10px] font-bold text-indigo-200">
                                                        {asset.asset_type?.name || 'Genérico'}
                                                    </span>
                                                </td>
                                                <td className="p-4">
                                                    <div className="text-white font-bold">{asset.sede?.nombre_sede}</div>
                                                    <div className="text-[10px] text-white/40">{asset.sede?.empresa?.nombre}</div>
                                                </td>
                                                <td className="p-4 relative hover:z-30">
                                                    {getStatusBadge(asset)}
                                                </td>
                                                <td className="p-4 pr-6 text-right space-x-2">
                                                    <Link
                                                        href={route('gestor.assets.show', asset.id)}
                                                        className="px-3 py-1.5 bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 rounded-xl text-[10px] font-black uppercase hover:bg-indigo-500/20 transition-all"
                                                    >
                                                        Ficha
                                                    </Link>
                                                    <Link
                                                        href={route('gestor.assets.edit', asset.id)}
                                                        className="px-3 py-1.5 bg-white/5 text-white/70 border border-white/10 rounded-xl text-[10px] font-black uppercase hover:bg-white/10 transition-all"
                                                    >
                                                        Editar
                                                    </Link>
                                                    <button
                                                        onClick={() => handleDelete(asset)}
                                                        className="px-3 py-1.5 bg-red-500/10 text-red-400 border border-red-500/20 rounded-xl text-[10px] font-black uppercase hover:bg-red-500/20 transition-all"
                                                    >
                                                        Dar de Baja
                                                    </button>
                                                </td>
                                            </tr>
                                        ))
                                    )}
                                </tbody>
                            </table>
                        </div>

                        {/* PAGINACIÓN */}
                        {assets.links && assets.links.length > 3 && (
                            <div className="p-4 bg-black/20 border-t border-white/5 flex items-center justify-between">
                                <div className="text-[10px] text-white/40 uppercase font-bold">
                                    Mostrando {assets.from} a {assets.to} de {assets.total} activos
                                </div>
                                <div className="flex gap-1">
                                    {assets.links.map((link, i) => (
                                        <Link
                                            key={i}
                                            href={link.url || '#'}
                                            dangerouslySetInnerHTML={{ __html: link.label }}
                                            className={`px-3 py-1 rounded-lg text-xs font-bold ${link.active ? 'bg-orange-500 text-white' : 'bg-white/5 text-white/40 hover:bg-white/10'}`}
                                        />
                                    ))}
                                </div>
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
