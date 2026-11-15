import React from 'react';
import { ASSET_ICONS } from '../../Partials/AssetStatusHelpers';

export default function AssetTypeCard({ type, isExpanded, onToggleAttributes, onEdit, onDelete }) {
    return (
        <div className="bg-[#16162a] p-6 rounded-[2rem] border border-white/5 shadow-xl hover:border-orange-500/20 transition-all flex flex-col gap-4">
            <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div className="flex items-center gap-3">
                    <h3 className="text-base font-black text-white uppercase italic">{type.name}</h3>
                    {type.category ? (
                        <div className="flex items-center gap-1.5">
                            <div
                                className="w-5 h-5 rounded-md flex items-center justify-center border transition-all text-xs"
                                style={{
                                    backgroundColor: `${type.category.color}15`,
                                    borderColor: `${type.category.color}40`,
                                    color: type.category.color,
                                }}
                                title={`Categoría: ${type.category.name}`}
                            >
                                <svg
                                    className="w-3 h-3"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                    dangerouslySetInnerHTML={{ __html: ASSET_ICONS[type.category.icon] || ASSET_ICONS['tag'] }}
                                />
                            </div>
                            <span className="px-1.5 py-0.5 rounded-lg text-[9px] font-black uppercase tracking-wider italic" style={{ color: type.category.color }}>
                                {type.category.name}
                            </span>
                        </div>
                    ) : (
                        <span className="px-2.5 py-1 bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 rounded-lg text-[10px] font-bold">
                            General
                        </span>
                    )}
                </div>

                <div className="flex items-center gap-2">
                    <button
                        onClick={() => onEdit(type)}
                        className="px-3 py-1.5 bg-white/5 text-white/70 border border-white/10 rounded-xl text-[10px] font-black uppercase hover:bg-white/10 transition-all cursor-pointer"
                    >
                        Editar Atributos
                    </button>
                    <button
                        onClick={() => onDelete(type)}
                        className="px-3 py-1.5 bg-red-500/10 text-red-400 border border-red-500/20 rounded-xl text-[10px] font-black uppercase hover:bg-red-500/20 transition-all cursor-pointer"
                    >
                        Eliminar
                    </button>
                </div>
            </div>

            {type.description && <p className="text-xs text-white/40">{type.description}</p>}

            <div className="bg-black/30 p-4 rounded-xl border border-white/5 space-y-2">
                <button
                    type="button"
                    onClick={() => onToggleAttributes(type.id)}
                    className="w-full flex items-center justify-between text-[9px] font-black text-orange-400 uppercase tracking-widest hover:text-orange-300 transition-colors focus:outline-none cursor-pointer"
                >
                    <span>Atributos de Ficha Técnica ({type.metadata_schema?.length || 0})</span>
                    <svg className={`w-3.5 h-3.5 transition-transform duration-200 ${isExpanded ? 'rotate-180' : ''}`} fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="2.5">
                        <path strokeLinecap="round" strokeLinejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                    </svg>
                </button>

                {isExpanded && (
                    <div className="pt-2 animate-fadeIn">
                        {type.metadata_schema && type.metadata_schema.length > 0 ? (
                            <div className="flex flex-wrap gap-1.5">
                                {type.metadata_schema.map((f, i) => {
                                    const isConditional = (f.maintenance_rules && f.maintenance_rules.length > 0) || f.alert_days || f.alert_threshold || f.alert_interval;
                                    const isCreate = f.show_in_create;
                                    const isSre = f.maintenance_rules && f.maintenance_rules.length > 0;

                                    let badgeClass = 'bg-white/5 border-white/10 text-gray-300 hover:border-white/20';
                                    if (isConditional) badgeClass = 'bg-amber-500/10 border-amber-500/30 text-amber-300 font-bold hover:bg-amber-500/15';
                                    else if (isCreate) badgeClass = 'bg-blue-500/10 border-blue-500/30 text-blue-300 font-bold hover:bg-blue-500/15';

                                    return (
                                        <span key={i} title={`Clave: ${f.name}`} className={`px-2.5 py-1 border rounded-lg text-[10px] font-mono cursor-help transition-all duration-200 ${badgeClass}`}>
                                            {isCreate && <span className="mr-1 text-[9px] text-blue-400 font-bold">📌 Alta</span>}
                                            {isCreate && isConditional && <span className="mx-1 text-white/20">|</span>}
                                            {isConditional && <span className="mr-1 text-[9px] text-amber-400 font-bold">{isSre ? '⚡ Regla SRE' : '🔔 Alerta'}</span>}
                                            {(isCreate || isConditional) && <span className="mx-1 text-white/20">|</span>}
                                            <strong className="text-white">{f.label || f.name}</strong>
                                            <span className="ml-1 text-white/30">
                                                {f.type === 'date' ? '🗓️' : f.type === 'number' ? '🔢' : '📝'}
                                            </span>
                                        </span>
                                    );
                                })}
                            </div>
                        ) : (
                            <p className="text-[10px] text-white/30 italic">Sin atributos definidos.</p>
                        )}
                    </div>
                )}
            </div>
        </div>
    );
}
