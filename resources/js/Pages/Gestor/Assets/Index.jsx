import { router, Head, Link, usePage } from '@inertiajs/react';
import AuthenticatedLayout from '../../../Layouts/AuthenticatedLayout';
import React, { useState, useEffect, useMemo } from 'react';
import AssetGroupedList from './Partials/AssetGroupedList';
import AssetFlatTable from './Partials/AssetFlatTable';
import ChecklistInfoModal from './Partials/ChecklistInfoModal';

export default function Index({ assets, kpis, filters, empresas = [], sedes = [], assetTypes = [] }) {
    const { flash } = usePage().props;

    const findAssetTypeForGroup = (group) => {
        if (!group || !group.category) return null;
        return assetTypes.find(t => t.category_id === group.category.id);
    };

    const [search, setSearch] = useState(filters.search || '');
    const [empresaId, setEmpresaId] = useState(filters.empresa_id || '');
    const [sedeId, setSedeId] = useState(filters.sede_id || '');
    const [assetTypeId, setAssetTypeId] = useState(filters.asset_type_id || '');
    const [status, setStatus] = useState(filters.status || 'all');
    const [viewMode, setViewMode] = useState(filters.view_mode || 'grouped');

    const updateFilters = () => {
        router.get(route('gestor.assets.index'), {
            search,
            empresa_id: empresaId,
            sede_id: sedeId,
            asset_type_id: assetTypeId,
            status,
            view_mode: viewMode,
        }, { preserveState: true, replace: true });
    };

    useEffect(() => {
        const timeout = setTimeout(() => {
            if (
                search !== (filters.search || '') ||
                empresaId !== (filters.empresa_id || '') ||
                sedeId !== (filters.sede_id || '') ||
                assetTypeId !== (filters.asset_type_id || '') ||
                status !== (filters.status || 'all') ||
                viewMode !== (filters.view_mode || 'grouped')
            ) {
                updateFilters();
            }
        }, 400);
        return () => clearTimeout(timeout);
    }, [search, empresaId, sedeId, assetTypeId, status, viewMode]);

    const handleDelete = (asset) => {
        if (confirm(`¿Dar de baja el activo ${asset.name} (${asset.identifier})?`)) {
            router.delete(route('gestor.assets.destroy', asset.id));
        }
    };

    const availableSedes = empresaId ? sedes.filter(s => s.empresa_id.toString() === empresaId.toString()) : sedes;

    const [expandedGroups, setExpandedGroups] = useState({ 'first-run': true });
    const [infoChecklist, setInfoChecklist] = useState(null);

    const toggleGroup = (id) => {
        setExpandedGroups(prev => {
            const next = { ...prev };
            if (next['first-run']) delete next['first-run'];
            next[id] = !next[id];
            return next;
        });
    };

    const groupedAssets = useMemo(() => {
        if (viewMode !== 'grouped') return [];
        const groups = {};
        const noChecklist = [];

        assets.data.forEach(asset => {
            const checklists = asset.asset_type?.category?.checklists || [];
            if (checklists.length === 0) {
                noChecklist.push(asset);
            } else {
                checklists.forEach(c => {
                    if (!groups[c.id]) {
                        groups[c.id] = {
                            id: c.id,
                            title: c.title,
                            description: c.description,
                            registry_code: c.registry_code,
                            category: asset.asset_type.category,
                            assets: [],
                        };
                    }
                    groups[c.id].assets.push(asset);
                });
            }
        });

        const list = Object.values(groups);
        if (noChecklist.length > 0) {
            list.push({
                id: 'no-checklist',
                title: 'Activos sin Checklist Asociado',
                description: 'Estos activos no están vinculados a ninguna plantilla de inspección.',
                registry_code: null,
                category: null,
                assets: noChecklist,
            });
        }

        if (expandedGroups['first-run'] && list.length > 0) {
            setExpandedGroups({ [list[0].id]: true });
        }
        return list;
    }, [assets.data, viewMode]);

    const toggleAllGroups = (expand) => {
        const newExpanded = {};
        if (expand) groupedAssets.forEach(g => { newExpanded[g.id] = true; });
        setExpandedGroups(newExpanded);
    };

    const getGroupKpis = (groupAssets) => {
        const stats = { total: groupAssets.length, operativo: 0, alerta: 0, fuera_de_servicio: 0 };
        groupAssets.forEach(a => {
            if (a.status === 'operativo') stats.operativo++;
            else if (a.status === 'alerta') stats.alerta++;
            else if (a.status === 'fuera_de_servicio') stats.fuera_de_servicio++;
        });
        return stats;
    };

    return (
        <AuthenticatedLayout title="Bóveda Universal de Activos">
            <Head title="Gestión de Activos" />
            <div className="min-h-screen bg-[#11111d] text-white p-4 sm:p-8">
                <div className="max-w-7xl mx-auto space-y-8">
                    <div className="flex flex-col lg:flex-row lg:items-center justify-between gap-6 bg-[#16162a] p-6 rounded-[2rem] border border-white/5 shadow-2xl">
                        <div>
                            <span className="text-[10px] font-black text-orange-500 uppercase tracking-[0.3em] italic">Módulo de Trazabilidad</span>
                            <h1 className="text-2xl font-black text-white uppercase tracking-tight italic">Activos y Equipamiento</h1>
                        </div>

                        <div className="flex bg-black/40 border border-white/10 p-1 rounded-xl items-center self-start lg:self-center">
                            <button
                                onClick={() => setViewMode('list')}
                                className={`px-4 py-2.5 text-[10px] font-black uppercase tracking-wider rounded-lg transition-all cursor-pointer ${viewMode === 'list' ? 'bg-gradient-to-r from-orange-500 to-amber-600 text-white shadow-lg' : 'text-white/40 hover:text-white/70'}`}
                            >
                                📋 Lista Plana
                            </button>
                            <button
                                onClick={() => setViewMode('grouped')}
                                className={`px-4 py-2.5 text-[10px] font-black uppercase tracking-wider rounded-lg transition-all cursor-pointer ${viewMode === 'grouped' ? 'bg-gradient-to-r from-orange-500 to-amber-600 text-white shadow-lg' : 'text-white/40 hover:text-white/70'}`}
                            >
                                🗂️ Agrupado por Checklist
                            </button>
                        </div>

                        <div className="flex flex-wrap gap-3">
                            <Link href={route('gestor.asset-types.index')} className="inline-flex items-center justify-center gap-2 px-6 py-3 bg-gradient-to-r from-amber-500 to-yellow-500 text-white font-black text-xs uppercase tracking-widest rounded-xl shadow-lg shadow-amber-500/20 hover:brightness-110 transition-all active:scale-95">
                                🔗 Vincular Checklists
                            </Link>
                            <Link href={route('gestor.assets.create')} className="inline-flex items-center justify-center gap-2 px-6 py-3 bg-gradient-to-r from-orange-500 to-amber-600 text-white font-black text-xs uppercase tracking-widest rounded-xl shadow-lg hover:brightness-110 transition-all active:scale-95">
                                <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2.5" d="M12 4v16m8-8H4"/></svg>
                                Registrar Activos
                            </Link>
                        </div>
                    </div>

                    {flash?.message && (
                        <div className="p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl text-emerald-400 text-xs font-bold flex items-center justify-between">
                            <span>{flash.message}</span>
                        </div>
                    )}

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

                    <div className="bg-[#16162a] p-6 rounded-[2rem] border border-white/5 space-y-4 shadow-xl">
                        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                            <div>
                                <label className="block text-[9px] font-black text-white/30 uppercase tracking-widest mb-1">Búsqueda</label>
                                <input type="text" value={search} onChange={e => setSearch(e.target.value)} placeholder="Nombre o Identificador/QR..." className="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-2.5 text-xs text-white placeholder-white/20 focus:outline-none focus:border-orange-500" />
                            </div>
                            <div>
                                <label className="block text-[9px] font-black text-white/30 uppercase tracking-widest mb-1">Empresa</label>
                                <select value={empresaId} onChange={e => { setEmpresaId(e.target.value); setSedeId(''); }} className="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-orange-500">
                                    <option value="">Todas las Empresas</option>
                                    {empresas.map(e => <option key={e.id} value={e.id}>{e.nombre}</option>)}
                                </select>
                            </div>
                            <div>
                                <label className="block text-[9px] font-black text-white/30 uppercase tracking-widest mb-1">Sede</label>
                                <select value={sedeId} onChange={e => setSedeId(e.target.value)} className="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-orange-500">
                                    <option value="">Todas las Sedes</option>
                                    {availableSedes.map(s => <option key={s.id} value={s.id}>{s.nombre_sede}</option>)}
                                </select>
                            </div>
                            <div>
                                <label className="block text-[9px] font-black text-white/30 uppercase tracking-widest mb-1">Tipo de Activo</label>
                                <select value={assetTypeId} onChange={e => setAssetTypeId(e.target.value)} className="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-orange-500">
                                    <option value="">Todos los Tipos</option>
                                    {assetTypes.map(t => <option key={t.id} value={t.id}>{t.name}</option>)}
                                </select>
                            </div>
                            <div>
                                <label className="block text-[9px] font-black text-white/30 uppercase tracking-widest mb-1">Estado</label>
                                <select value={status} onChange={e => setStatus(e.target.value)} className="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-orange-500">
                                    <option value="all">Todos los Estados</option>
                                    <option value="operativo">Operativo</option>
                                    <option value="alerta">En Alerta</option>
                                    <option value="fuera_de_servicio">Fuera de Servicio</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    {viewMode === 'grouped' ? (
                        <AssetGroupedList
                            groupedAssets={groupedAssets}
                            expandedGroups={expandedGroups}
                            toggleGroup={toggleGroup}
                            toggleAllGroups={toggleAllGroups}
                            getGroupKpis={getGroupKpis}
                            findAssetTypeForGroup={findAssetTypeForGroup}
                            filters={filters}
                            onInfoChecklist={setInfoChecklist}
                            onDelete={handleDelete}
                        />
                    ) : (
                        <AssetFlatTable assets={assets} filters={filters} onDelete={handleDelete} />
                    )}

                    <ChecklistInfoModal infoChecklist={infoChecklist} onClose={() => setInfoChecklist(null)} />
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
