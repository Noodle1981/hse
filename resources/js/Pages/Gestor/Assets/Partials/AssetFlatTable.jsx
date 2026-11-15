import React from 'react';
import { Link } from '@inertiajs/react';
import { ASSET_ICONS, AssetStatusBadge } from './AssetStatusHelpers';

export default function AssetFlatTable({ assets, filters, onDelete }) {
    return (
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
                                        <div className="font-semibold text-white italic">{asset.name}</div>
                                        <div className="text-[10px] text-orange-400 font-mono tracking-wider">QR/ID: {asset.identifier}</div>
                                        {asset.grupo && (
                                            <div className="text-[9px] text-white/50 mt-1 uppercase tracking-wider font-bold italic">
                                                📁 {asset.asset_type?.group_label || 'Grupo'}: {asset.grupo} {asset.subgrupo && `> ${asset.subgrupo}`}
                                            </div>
                                        )}
                                    </td>
                                    <td className="p-4">
                                        <div className="flex flex-col gap-2">
                                            <div className="flex items-center gap-2">
                                                {asset.asset_type?.category && (
                                                    <div
                                                        className="w-6 h-6 rounded-lg flex items-center justify-center border transition-all text-xs flex-shrink-0"
                                                        style={{
                                                            backgroundColor: `${asset.asset_type.category.color}15`,
                                                            borderColor: `${asset.asset_type.category.color}40`,
                                                            color: asset.asset_type.category.color,
                                                        }}
                                                        title={`Categoría: ${asset.asset_type.category.name}`}
                                                    >
                                                        <svg className="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" dangerouslySetInnerHTML={{ __html: ASSET_ICONS[asset.asset_type.category.icon] || ASSET_ICONS['tag'] }} />
                                                    </div>
                                                )}
                                                <span className="px-2 py-0.5 bg-white/5 border border-white/10 rounded-lg text-[9px] font-black text-indigo-200 uppercase tracking-wide">
                                                    {asset.asset_type?.name || 'Genérico'}
                                                </span>
                                            </div>

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
                                        <AssetStatusBadge asset={asset} />
                                    </td>
                                    <td className="p-4 pr-6 text-right space-x-2">
                                        <Link href={route('gestor.assets.show', asset.id)} className="px-3 py-1.5 bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 rounded-xl text-[10px] font-black uppercase hover:bg-indigo-500/20 transition-all">
                                            Ficha
                                        </Link>
                                        <Link href={route('gestor.assets.edit', asset.id)} className="px-3 py-1.5 bg-white/5 text-white/70 border border-white/10 rounded-xl text-[10px] font-black uppercase hover:bg-white/10 transition-all">
                                            Editar
                                        </Link>
                                        <button onClick={() => onDelete(asset)} className="px-3 py-1.5 bg-red-500/10 text-red-400 border border-red-500/20 rounded-xl text-[10px] font-black uppercase hover:bg-red-500/20 transition-all">
                                            Dar de Baja
                                        </button>
                                    </td>
                                </tr>
                            ))
                        )}
                        <tr className="hover:bg-white/[0.04] transition-colors border-t border-white/5">
                            <td colSpan="5" className="p-4 pl-6">
                                <Link
                                    href={route('gestor.assets.create', {
                                        empresa_id: filters.empresa_id || '',
                                        sede_id: filters.sede_id || '',
                                        asset_type_id: filters.asset_type_id || '',
                                    })}
                                    className="inline-flex items-center gap-1.5 text-xs text-orange-400 font-black uppercase tracking-wider hover:text-orange-300 transition-colors"
                                >
                                    <span className="w-5 h-5 rounded-lg bg-orange-500/10 border border-orange-500/20 flex items-center justify-center text-orange-400 font-bold">+</span>
                                    Registrar nuevo activo
                                </Link>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

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
    );
}
