import React from 'react';
import { Link } from '@inertiajs/react';
import { ASSET_ICONS, AssetStatusBadge } from './AssetStatusHelpers';

export default function AssetGroupedList({
    groupedAssets,
    expandedGroups,
    toggleGroup,
    toggleAllGroups,
    getGroupKpis,
    findAssetTypeForGroup,
    filters,
    onInfoChecklist,
    onDelete,
}) {
    return (
        <div className="space-y-6">
            <div className="flex justify-end gap-2 text-[10px] font-black uppercase tracking-wider">
                <button
                    onClick={() => toggleAllGroups(true)}
                    className="px-3 py-1.5 bg-white/5 border border-white/10 rounded-lg hover:bg-white/10 transition-all text-white/60 hover:text-white cursor-pointer"
                >
                    ➕ Expandir Todos
                </button>
                <button
                    onClick={() => toggleAllGroups(false)}
                    className="px-3 py-1.5 bg-white/5 border border-white/10 rounded-lg hover:bg-white/10 transition-all text-white/60 hover:text-white cursor-pointer"
                >
                    ➖ Colapsar Todos
                </button>
            </div>

            {groupedAssets.length === 0 ? (
                <div className="bg-[#16162a] p-12 rounded-[2rem] border border-white/5 text-center text-white/30 text-xs uppercase tracking-widest italic">
                    No se encontraron activos registrados con los filtros aplicados.
                </div>
            ) : (
                groupedAssets.map(group => {
                    const isExpanded = !!expandedGroups[group.id];
                    const stats = getGroupKpis(group.assets);

                    return (
                        <div key={group.id} className="bg-[#16162d] rounded-[2rem] border border-white/5 overflow-hidden shadow-xl transition-all">
                            <div
                                onClick={() => toggleGroup(group.id)}
                                className="p-6 flex flex-col md:flex-row md:items-center justify-between gap-4 cursor-pointer hover:bg-white/[0.02] transition-colors select-none"
                            >
                                <div className="flex items-center gap-4">
                                    <div
                                        className="h-12 w-12 rounded-2xl bg-white/5 border flex items-center justify-center shadow-inner flex-shrink-0 transition-transform duration-300"
                                        style={{
                                            borderColor: group.category ? `${group.category.color}40` : 'rgba(255,255,255,0.1)',
                                            color: group.category ? group.category.color : '#a5b4fc',
                                        }}
                                    >
                                        <svg
                                            className="w-6 h-6"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                            strokeWidth="2"
                                            dangerouslySetInnerHTML={{
                                                __html: group.category ? (ASSET_ICONS[group.category.icon] || ASSET_ICONS['tag']) : ASSET_ICONS['doc'],
                                            }}
                                        />
                                    </div>

                                    <div>
                                        <div className="flex flex-wrap items-center gap-2">
                                            <h3 className="text-sm font-black text-white uppercase tracking-wider italic">{group.title}</h3>
                                            {group.registry_code && (
                                                <span className="px-2 py-0.5 bg-black/40 border border-white/10 rounded-md text-[8px] font-mono text-orange-400 uppercase tracking-widest">
                                                    {group.registry_code}
                                                </span>
                                            )}
                                            {group.id !== 'no-checklist' && (
                                                <button
                                                    onClick={(e) => {
                                                        e.stopPropagation();
                                                        onInfoChecklist(group);
                                                    }}
                                                    className="w-5 h-5 rounded-lg flex items-center justify-center bg-white/5 border border-white/10 text-indigo-300 hover:bg-white/10 hover:text-white transition-all cursor-pointer"
                                                    title="Ver especificación del checklist"
                                                >
                                                    <svg className="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="2.5">
                                                        <path strokeLinecap="round" strokeLinejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.603 10.603z" />
                                                    </svg>
                                                </button>
                                            )}
                                        </div>
                                        <p className="text-[10px] text-white/40 mt-1 uppercase tracking-wider font-medium line-clamp-1 max-w-[200px] sm:max-w-md lg:max-w-xl">
                                            {group.description || 'Sin descripción disponible.'}
                                        </p>
                                    </div>
                                </div>

                                <div className="flex flex-wrap items-center gap-4 md:self-center self-end">
                                    <div className="bg-black/30 px-3 py-1.5 rounded-xl border border-white/5 flex items-center gap-2">
                                        <span className="text-[9px] font-black text-white/30 uppercase tracking-widest">Activos:</span>
                                        <span className="text-xs font-black text-indigo-300 italic">{stats.total}</span>
                                    </div>

                                    <div className="flex items-center gap-1 bg-black/30 p-1 rounded-full border border-white/5">
                                        <div className="w-6 h-6 rounded-full bg-emerald-500 text-black font-black text-[10px] flex items-center justify-center shadow-[0_0_10px_rgba(16,185,129,0.4)]" title={`${stats.operativo} Operativos`}>
                                            {stats.operativo}
                                        </div>
                                        <div className="w-6 h-6 rounded-full bg-amber-500 text-black font-black text-[10px] flex items-center justify-center shadow-[0_0_10px_rgba(245,158,11,0.4)]" title={`${stats.alerta} en Alerta`}>
                                            {stats.alerta}
                                        </div>
                                        <div className="w-6 h-6 rounded-full bg-red-500 text-white font-black text-[10px] flex items-center justify-center shadow-[0_0_10px_rgba(239,68,68,0.4)]" title={`${stats.fuera_de_servicio} Fuera de Servicio`}>
                                            {stats.fuera_de_servicio}
                                        </div>
                                    </div>

                                    <div className="text-white/40 hover:text-white transition-colors ml-2">
                                        <svg className={`w-5 h-5 transition-transform duration-300 ${isExpanded ? 'rotate-180' : ''}`} fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="2.5">
                                            <path strokeLinecap="round" strokeLinejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            {isExpanded && (
                                <div className="border-t border-white/5 bg-black/10 overflow-x-auto custom-scrollbar">
                                    <table className="w-full text-left border-collapse">
                                        <thead>
                                            <tr className="bg-black/40 border-b border-white/5 text-[9px] font-black text-orange-500 uppercase tracking-widest italic">
                                                <th className="p-3 pl-6">Activo / Identificador</th>
                                                <th className="p-3">Tipo de Activo</th>
                                                <th className="p-3">Sede / Empresa</th>
                                                <th className="p-3">Estado</th>
                                                <th className="p-3 pr-6 text-right">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody className="divide-y divide-white/5 text-xs">
                                            {(() => {
                                                const groupsL1 = {};
                                                group.assets.forEach(asset => {
                                                    const gName = asset.grupo || 'Sin Clasificar';
                                                    if (!groupsL1[gName]) groupsL1[gName] = {};
                                                    const sgName = asset.subgrupo || 'Sin Subgrupo';
                                                    if (!groupsL1[gName][sgName]) groupsL1[gName][sgName] = [];
                                                    groupsL1[gName][sgName].push(asset);
                                                });

                                                const groupLabel = group.assets[0]?.asset_type?.group_label || 'Grupo';
                                                const subgroupLabel = group.assets[0]?.asset_type?.subgroup_label || 'Subgrupo';

                                                return Object.entries(groupsL1).map(([gName, subgroups]) => (
                                                    <React.Fragment key={gName}>
                                                        <tr className="bg-white/[0.03] border-y border-white/10 select-none">
                                                            <td colSpan="5" className="p-3 pl-6 text-[10px] font-black text-orange-400 uppercase tracking-widest italic bg-black/35">
                                                                📁 {groupLabel}: {gName} ({Object.values(subgroups).reduce((acc, curr) => acc + curr.length, 0)})
                                                            </td>
                                                        </tr>
                                                        {Object.entries(subgroups).map(([sgName, subAssets]) => (
                                                            <React.Fragment key={sgName}>
                                                                <tr className="bg-white/[0.01] border-b border-white/5 select-none">
                                                                    <td colSpan="5" className="p-2 pl-10 text-[9px] font-bold text-indigo-300/80 uppercase tracking-wider italic bg-black/15">
                                                                        📍 {subgroupLabel}: {sgName} ({subAssets.length})
                                                                    </td>
                                                                </tr>
                                                                {subAssets.map(asset => (
                                                                    <tr key={asset.id} className="hover:bg-white/[0.01] transition-colors">
                                                                        <td className="p-3 pl-12">
                                                                            <div className="font-semibold text-white italic">{asset.name}</div>
                                                                            <div className="text-[10px] text-orange-400 font-mono tracking-wider">QR/ID: {asset.identifier}</div>
                                                                        </td>
                                                                        <td className="p-3">
                                                                            <span className="px-2 py-0.5 bg-white/5 border border-white/10 rounded-lg text-[9px] font-black text-indigo-200 uppercase tracking-wide">
                                                                                {asset.asset_type?.name || 'Genérico'}
                                                                            </span>
                                                                        </td>
                                                                        <td className="p-3">
                                                                            <div className="text-white font-bold">{asset.sede?.nombre_sede}</div>
                                                                            <div className="text-[10px] text-white/40">{asset.sede?.empresa?.nombre}</div>
                                                                        </td>
                                                                        <td className="p-3 relative hover:z-30">
                                                                            <AssetStatusBadge asset={asset} />
                                                                        </td>
                                                                        <td className="p-3 pr-6 text-right space-x-2">
                                                                            <Link href={route('gestor.assets.show', asset.id)} className="px-3 py-1.5 bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 rounded-xl text-[10px] font-black uppercase hover:bg-indigo-500/20 transition-all inline-block">
                                                                                Ficha
                                                                            </Link>
                                                                            <Link href={route('gestor.assets.edit', asset.id)} className="px-3 py-1.5 bg-white/5 text-white/70 border border-white/10 rounded-xl text-[10px] font-black uppercase hover:bg-white/10 transition-all inline-block">
                                                                                Editar
                                                                            </Link>
                                                                            <button onClick={() => onDelete(asset)} className="px-3 py-1.5 bg-red-500/10 text-red-400 border border-red-500/20 rounded-xl text-[10px] font-black uppercase hover:bg-red-500/20 transition-all cursor-pointer inline-block">
                                                                                Dar de Baja
                                                                            </button>
                                                                        </td>
                                                                    </tr>
                                                                ))}
                                                            </React.Fragment>
                                                        ))}
                                                    </React.Fragment>
                                                ));
                                            })()}
                                            <tr className="hover:bg-white/[0.04] transition-colors border-t border-white/5">
                                                <td colSpan="5" className="p-3 pl-6">
                                                    <Link
                                                        href={route('gestor.assets.create', {
                                                            empresa_id: filters.empresa_id || group.assets[0]?.empresa_id || '',
                                                            sede_id: filters.sede_id || group.assets[0]?.sede_id || '',
                                                            asset_type_id: findAssetTypeForGroup(group)?.id || group.assets[0]?.asset_type_id || '',
                                                        })}
                                                        className="inline-flex items-center gap-1.5 text-xs text-orange-400 font-black uppercase tracking-wider hover:text-orange-300 transition-colors"
                                                    >
                                                        <span className="w-5 h-5 rounded-lg bg-orange-500/10 border border-orange-500/20 flex items-center justify-center text-orange-400 font-bold">+</span>
                                                        Registrar nuevo activo para este checklist
                                                    </Link>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            )}
                        </div>
                    );
                })
            )}
        </div>
    );
}
