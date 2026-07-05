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
    'mineria': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 15L20 4M4 11a14 14 0 0113-7M4 11a14 14 0 0013 7" />',
    'fabrica': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V9l-5 3V9l-5 3V9l-5 3v9h15zM17 5v4" />',
    'construccion': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 20h16M7 20l4-14a1 1 0 012 0l4 14M9 12h6M10 8h4" />',
    'bodega': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m-8-14v14" />',
    'colectivo': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 18v2a1 1 0 001 1h2a1 1 0 001-1v-2h8v2a1 1 0 001 1h2a1 1 0 001-1v-2M4 6h16a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V8a2 2 0 012-2zm2 3h4v4H6V9zm8 0h4v4h-4V9zM6 15h12M6 15h.01M18 15h.01" />',
    'camioneta': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 14h18M6 17a2 2 0 11-4 0 2 2 0 014 0zm12 0a2 2 0 11-4 0 2 2 0 014 0zm-12-3v-3a1 1 0 011-1h5l3 3h5v4M7 10h4v3H7v-3z" />',
};

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
                                🔗 Vincular Checklists
                            </Link>
                            <Link
                                href={route('gestor.assets.create')}
                                className="inline-flex items-center justify-center gap-2 px-6 py-3 bg-gradient-to-r from-orange-500 to-amber-600 text-white font-black text-xs uppercase tracking-widest rounded-xl shadow-lg hover:brightness-110 transition-all active:scale-95"
                            >
                                <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2.5" d="M12 4v16m8-8H4"/></svg>
                                Registrar Activos
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
                                                    <div className="flex flex-col gap-2">
                                                        <div className="flex items-center gap-2">
                                                            {asset.asset_type?.category && (
                                                                <div className="w-6 h-6 rounded-lg flex items-center justify-center border transition-all text-xs flex-shrink-0" style={{ backgroundColor: `${asset.asset_type.category.color}15`, borderColor: `${asset.asset_type.category.color}40`, color: asset.asset_type.category.color }} title={`Categoría: ${asset.asset_type.category.name}`}>
                                                                    <svg className="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" dangerouslySetInnerHTML={{ __html: ICONS[asset.asset_type.category.icon] || ICONS['tag'] }} />
                                                                </div>
                                                            )}
                                                            <span className="px-2 py-0.5 bg-white/5 border border-white/10 rounded-lg text-[9px] font-black text-indigo-200 uppercase tracking-wide">
                                                                {asset.asset_type?.name || 'Genérico'}
                                                            </span>
                                                        </div>
                                                        
                                                        {/* Checklists asociados */}
                                                        {asset.asset_type?.category?.checklists && asset.asset_type.category.checklists.length > 0 ? (
                                                            <div className="flex flex-col gap-0.5 pl-1">
                                                                <span className="text-[7px] font-black text-white/20 uppercase tracking-widest italic">Checklists asociados</span>
                                                                <div className="flex flex-wrap gap-x-2 gap-y-1">
                                                                    {asset.asset_type.category.checklists.map(c => (
                                                                        <span key={c.id} className="inline-flex items-center gap-1 text-[9px] text-white/50 font-bold hover:text-orange-400 transition-colors uppercase italic">
                                                                            📋 {c.title}
                                                                        </span>
                                                                    ))}
                                                                </div>
                                                            </div>
                                                        ) : (
                                                            <span className="text-[8px] text-white/20 italic pl-1">Sin checklists asociados</span>
                                                        )}
                                                    </div>
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
