import React from 'react';

export default function AssetItemFormCard({
    item,
    index,
    totalItems,
    selectedType,
    existingGroups,
    existingSubgroups,
    createFields,
    isDuplicate,
    errors,
    onUpdateItem,
    onUpdateMetadata,
    onNormalizeGrupo,
    onNormalizeSubgrupo,
    onRemove,
}) {
    return (
        <div className="bg-[#16162a] p-8 rounded-[2.5rem] border border-white/5 shadow-2xl space-y-6 relative group">
            <div className="flex items-center justify-between border-b border-white/5 pb-4">
                <div className="flex items-center gap-2">
                    <span className="px-3 py-1 bg-orange-500/10 border border-orange-500/30 text-orange-400 rounded-xl text-xs font-black uppercase italic">
                        Activo #{index + 1}
                    </span>
                    {item.identifier && <span className="text-xs font-mono text-orange-300">({item.identifier})</span>}
                </div>

                {totalItems > 1 && (
                    <button
                        type="button"
                        onClick={() => onRemove(index)}
                        className="px-3 py-1.5 bg-red-500/10 text-red-400 border border-red-500/20 rounded-xl text-xs font-bold hover:bg-red-500 hover:text-white transition-all flex items-center gap-1"
                    >
                        <svg className="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Quitar
                    </button>
                )}
            </div>

            <div className="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label className="block text-[10px] font-black text-white/40 uppercase tracking-widest mb-2 italic">Nombre del Activo *</label>
                    <input
                        type="text"
                        value={item.name}
                        onChange={e => onUpdateItem(index, 'name', e.target.value)}
                        placeholder="Ej: Matafuegos o Extintor Pasillo A"
                        className="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-orange-500"
                        required
                    />
                    {errors[`items.${index}.name`] && <p className="text-xs text-red-400 mt-1">{errors[`items.${index}.name`]}</p>}
                </div>

                <div>
                    <label className="block text-[10px] font-black text-white/40 uppercase tracking-widest mb-2 italic">Identificador / Código QR *</label>
                    <input
                        type="text"
                        value={item.identifier}
                        onChange={e => onUpdateItem(index, 'identifier', e.target.value)}
                        placeholder="Ej: MF001"
                        className={`w-full bg-black/40 border rounded-xl px-4 py-3 text-sm font-mono focus:outline-none transition-all ${
                            isDuplicate ? 'border-red-500 text-red-400 bg-red-500/10' : 'border-white/10 text-orange-400 focus:border-orange-500'
                        }`}
                        required
                    />
                    {isDuplicate && <p className="text-xs font-bold text-red-400 mt-1 flex items-center gap-1">⚠️ Identificador repetido en este lote.</p>}
                    {errors[`items.${index}.identifier`] && <p className="text-xs text-red-400 mt-1">{errors[`items.${index}.identifier`]}</p>}
                </div>
            </div>

            <div className="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label className="block text-[10px] font-black text-white/40 uppercase tracking-widest mb-2 italic">
                        {selectedType?.group_label || 'Grupo'}
                    </label>
                    <input
                        type="text"
                        list={`existing-groups-${index}`}
                        value={item.grupo || ''}
                        onChange={e => onUpdateItem(index, 'grupo', e.target.value)}
                        onBlur={e => onNormalizeGrupo(index, e.target.value)}
                        placeholder={`Ej: ${selectedType?.group_label || 'Piso 1, Flota A'}`}
                        className="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-orange-500"
                    />
                    <datalist id={`existing-groups-${index}`}>
                        {existingGroups.map(g => <option key={g} value={g} />)}
                    </datalist>
                    {item.grupo && item.grupo.trim() && (
                        <div className="mt-1 flex items-center text-[9px] font-bold italic select-none">
                            {existingGroups.includes(item.grupo.trim()) ? (
                                <span className="text-emerald-400">✓ {selectedType?.group_label || 'Grupo'} existente en BD</span>
                            ) : (
                                <span className="text-orange-400">✨ Nuevo {selectedType?.group_label || 'Grupo'} (se creará)</span>
                            )}
                        </div>
                    )}
                    {errors[`items.${index}.grupo`] && <p className="text-xs text-red-400 mt-1">{errors[`items.${index}.grupo`]}</p>}
                </div>

                <div>
                    <label className="block text-[10px] font-black text-white/40 uppercase tracking-widest mb-2 italic">
                        {selectedType?.subgroup_label || 'Subgrupo'}
                    </label>
                    <input
                        type="text"
                        list={`existing-subgroups-${index}`}
                        value={item.subgrupo || ''}
                        onChange={e => onUpdateItem(index, 'subgrupo', e.target.value)}
                        onBlur={e => onNormalizeSubgrupo(index, e.target.value)}
                        placeholder={`Ej: ${selectedType?.subgroup_label || 'Sector Norte, Oficina'}`}
                        className="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-orange-500"
                    />
                    <datalist id={`existing-subgroups-${index}`}>
                        {existingSubgroups.map(sg => <option key={sg} value={sg} />)}
                    </datalist>
                    {item.subgrupo && item.subgrupo.trim() && (
                        <div className="mt-1 flex items-center text-[9px] font-bold italic select-none">
                            {existingSubgroups.includes(item.subgrupo.trim()) ? (
                                <span className="text-emerald-400">✓ {selectedType?.subgroup_label || 'Subgrupo'} existente en BD</span>
                            ) : (
                                <span className="text-orange-400">✨ Nuevo {selectedType?.subgroup_label || 'Subgrupo'} (se creará)</span>
                            )}
                        </div>
                    )}
                    {errors[`items.${index}.subgrupo`] && <p className="text-xs text-red-400 mt-1">{errors[`items.${index}.subgrupo`]}</p>}
                </div>
            </div>

            <div>
                <label className="block text-[10px] font-black text-white/40 uppercase tracking-widest mb-2 italic">Estado Inicial *</label>
                <div className="flex gap-4">
                    {[
                        { val: 'operativo', label: 'Operativo', cls: 'text-emerald-400', inp: 'text-emerald-500' },
                        { val: 'alerta', label: 'En Alerta', cls: 'text-amber-400', inp: 'text-amber-500' },
                        { val: 'fuera_de_servicio', label: 'Fuera de Servicio', cls: 'text-red-400', inp: 'text-red-500' },
                    ].map(opt => (
                        <label key={opt.val} className="flex items-center gap-2 cursor-pointer">
                            <input
                                type="radio"
                                name={`status_${index}`}
                                value={opt.val}
                                checked={item.status === opt.val}
                                onChange={e => onUpdateItem(index, 'status', e.target.value)}
                                className={opt.inp}
                            />
                            <span className={`text-xs font-bold ${opt.cls}`}>{opt.label}</span>
                        </label>
                    ))}
                </div>
            </div>

            {createFields.length > 0 && (
                <div className="p-6 bg-black/30 border border-white/10 rounded-2xl space-y-4">
                    <div className="flex items-center justify-between border-b border-white/5 pb-2">
                        <h3 className="text-xs font-black text-orange-400 uppercase tracking-wider italic">
                            Datos de Identificación del Activo ({selectedType?.name})
                        </h3>
                        <span className="text-[10px] text-white/30">Campos de Alta</span>
                    </div>
                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        {createFields.map(field => (
                            <div key={field.name}>
                                <label className="block text-[10px] font-bold text-white/60 uppercase mb-1">
                                    {field.label || field.name}
                                    {field.alert_days && <span className="ml-2 text-[9px] text-amber-400">(Alerta ≤ {field.alert_days}d)</span>}
                                    {field.alert_threshold && <span className="ml-2 text-[9px] text-amber-400">(Alerta ≥ {field.alert_threshold})</span>}
                                </label>
                                <input
                                    type={field.type === 'number' ? 'number' : (field.type === 'date' ? 'date' : 'text')}
                                    step={field.type === 'number' ? 'any' : undefined}
                                    value={item.metadata[field.name] || ''}
                                    onChange={e => onUpdateMetadata(index, field.name, e.target.value)}
                                    className="w-full bg-black/50 border border-white/10 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-orange-500"
                                />
                            </div>
                        ))}
                    </div>
                </div>
            )}
        </div>
    );
}
