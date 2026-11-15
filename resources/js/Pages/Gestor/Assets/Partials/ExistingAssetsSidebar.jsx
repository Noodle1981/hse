import React from 'react';

export default function ExistingAssetsSidebar({
    hasSelection,
    localSearch,
    setLocalSearch,
    loadingExisting,
    existingAssets,
    filteredExistingAssets,
    groupedSidebarAssets,
    selectedType,
    editingAssetId,
    setEditingAssetId,
    editNombre,
    setEditNombre,
    editIdentificador,
    setEditIdentificador,
    editGrupo,
    setEditGrupo,
    editSubgrupo,
    setEditSubgrupo,
    savingAssetId,
    onSaveInline,
}) {
    return (
        <div className="space-y-6">
            <div className="bg-[#16162a] p-6 rounded-[2rem] border border-white/5 shadow-2xl space-y-4">
                <div>
                    <span className="text-[9px] font-black text-orange-500 uppercase tracking-widest italic block">Base de Datos de Activos</span>
                    <h2 className="text-md font-black text-white uppercase italic">Equipos Registrados</h2>
                </div>

                {hasSelection ? (
                    <>
                        <div className="relative">
                            <input
                                type="text"
                                placeholder="Buscar por ID o Nombre..."
                                value={localSearch}
                                onChange={e => setLocalSearch(e.target.value)}
                                className="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-2.5 text-xs text-white placeholder-white/20 focus:outline-none focus:border-orange-500 pl-9"
                            />
                            <svg className="w-3.5 h-3.5 text-white/30 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>

                        {loadingExisting ? (
                            <div className="space-y-3 py-6">
                                <div className="h-10 bg-white/5 rounded-xl animate-pulse" />
                                <div className="h-10 bg-white/5 rounded-xl animate-pulse" />
                                <div className="h-10 bg-white/5 rounded-xl animate-pulse" />
                            </div>
                        ) : filteredExistingAssets.length === 0 ? (
                            <div className="p-8 text-center text-white/30 text-xs uppercase tracking-widest italic bg-black/20 rounded-2xl border border-white/5">
                                {existingAssets.length === 0 ? 'No hay activos registrados para esta selección.' : 'No se encontraron coincidencias.'}
                            </div>
                        ) : (
                            <div className="max-h-[500px] overflow-y-auto pr-1 space-y-4 custom-scrollbar">
                                {Object.entries(groupedSidebarAssets).map(([gName, subgroups]) => (
                                    <div key={gName} className="space-y-2">
                                        <div className="text-[10px] font-black text-orange-400 uppercase tracking-widest italic flex items-center gap-1.5 pl-1 select-none">
                                            📁 {selectedType?.group_label || 'Grupo'}: {gName}
                                        </div>

                                        <div className="bg-black/25 rounded-2xl border border-white/5 p-3 space-y-3">
                                            {Object.entries(subgroups).map(([sgName, subAssets]) => (
                                                <div key={sgName} className="space-y-1.5">
                                                    <div className="text-[9px] font-black text-indigo-300/80 uppercase tracking-wide italic flex items-center gap-1 pl-1 select-none">
                                                        📍 {selectedType?.subgroup_label || 'Subgrupo'}: {sgName}
                                                    </div>

                                                    <div className="space-y-1.5 pl-2 border-l border-white/5">
                                                        {subAssets.map(asset => {
                                                            const isEditing = editingAssetId === asset.id;
                                                            const isSaving = savingAssetId === asset.id;

                                                            return (
                                                                <div key={asset.id} className={`p-2.5 rounded-xl border transition-all ${isEditing ? 'bg-orange-500/5 border-orange-500/25 shadow-lg' : 'bg-black/20 hover:bg-black/30 border-white/5'}`}>
                                                                    {isEditing ? (
                                                                        <div className="space-y-3">
                                                                            <div className="text-[10px] font-bold text-orange-400 uppercase italic">Editar Activo</div>
                                                                            <div className="space-y-2">
                                                                                <div>
                                                                                    <label className="block text-[8px] font-black text-white/40 uppercase mb-1">Nombre del Activo *</label>
                                                                                    <input type="text" value={editNombre} onChange={e => setEditNombre(e.target.value)} className="w-full bg-black/60 border border-white/10 rounded-lg px-2 py-1 text-[11px] text-white focus:outline-none focus:border-orange-500" required />
                                                                                </div>
                                                                                <div>
                                                                                    <label className="block text-[8px] font-black text-white/40 uppercase mb-1">Identificador / Código QR *</label>
                                                                                    <input type="text" value={editIdentificador} onChange={e => setEditIdentificador(e.target.value)} className="w-full bg-black/60 border border-white/10 rounded-lg px-2 py-1 text-[11px] text-white focus:outline-none focus:border-orange-500" required />
                                                                                </div>
                                                                            </div>
                                                                            <div className="grid grid-cols-2 gap-2">
                                                                                <div>
                                                                                    <label className="block text-[8px] font-black text-white/40 uppercase mb-1">{selectedType?.group_label || 'Grupo'}</label>
                                                                                    <input type="text" value={editGrupo} onChange={e => setEditGrupo(e.target.value)} className="w-full bg-black/60 border border-white/10 rounded-lg px-2 py-1 text-[11px] text-white focus:outline-none focus:border-orange-500" />
                                                                                </div>
                                                                                <div>
                                                                                    <label className="block text-[8px] font-black text-white/40 uppercase mb-1">{selectedType?.subgroup_label || 'Subgrupo'}</label>
                                                                                    <input type="text" value={editSubgrupo} onChange={e => setEditSubgrupo(e.target.value)} className="w-full bg-black/60 border border-white/10 rounded-lg px-2 py-1 text-[11px] text-white focus:outline-none focus:border-orange-500" />
                                                                                </div>
                                                                            </div>
                                                                            <div className="flex justify-end gap-2 pt-2 border-t border-white/5">
                                                                                <button type="button" onClick={() => setEditingAssetId(null)} disabled={isSaving} className="px-2.5 py-1 bg-white/5 hover:bg-white/10 rounded-lg text-[9px] font-bold uppercase transition-colors">
                                                                                    Cancelar
                                                                                </button>
                                                                                <button type="button" onClick={() => onSaveInline(asset.id)} disabled={isSaving} className="px-3 py-1 bg-orange-500 hover:bg-orange-600 text-white rounded-lg text-[9px] font-black uppercase transition-colors">
                                                                                    {isSaving ? 'Guardando...' : '✓ Guardar'}
                                                                                </button>
                                                                            </div>
                                                                        </div>
                                                                    ) : (
                                                                        <div className="flex items-center justify-between gap-3">
                                                                            <div className="min-w-0">
                                                                                <div className="font-semibold text-white text-xs truncate italic">{asset.name}</div>
                                                                                <div className="text-[10px] text-orange-400 font-mono tracking-wider">ID: {asset.identifier}</div>
                                                                            </div>
                                                                            <div className="flex items-center gap-2 flex-shrink-0">
                                                                                <span className={`px-2 py-0.5 rounded-lg text-[8px] font-black uppercase tracking-wider ${
                                                                                    asset.status === 'operativo' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' :
                                                                                    asset.status === 'alerta' ? 'bg-amber-500/10 text-amber-400 border border-amber-500/20' :
                                                                                    'bg-red-500/10 text-red-400 border border-red-500/20'
                                                                                }`}>
                                                                                    {asset.status === 'operativo' ? 'OK' : asset.status === 'alerta' ? 'Alerta' : 'Baja'}
                                                                                </span>
                                                                                <button
                                                                                    type="button"
                                                                                    onClick={() => {
                                                                                        setEditingAssetId(asset.id);
                                                                                        setEditNombre(asset.name || '');
                                                                                        setEditIdentificador(asset.identifier || '');
                                                                                        setEditGrupo(asset.grupo || '');
                                                                                        setEditSubgrupo(asset.subgrupo || '');
                                                                                    }}
                                                                                    className="p-1 hover:bg-white/10 rounded-lg text-white/40 hover:text-white transition-colors"
                                                                                    title="Editar Activo"
                                                                                >
                                                                                    <svg className="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                                                    </svg>
                                                                                </button>
                                                                            </div>
                                                                        </div>
                                                                    )}
                                                                </div>
                                                            );
                                                        })}
                                                    </div>
                                                </div>
                                            ))}
                                        </div>
                                    </div>
                                ))}
                            </div>
                        )}
                        <div className="pt-2 text-[10px] text-white/40 font-mono text-right">
                            Total en Sede: <strong className="text-orange-400">{existingAssets.length}</strong>
                        </div>
                    </>
                ) : (
                    <div className="p-8 text-center text-white/30 text-xs uppercase tracking-widest leading-relaxed italic bg-black/20 rounded-2xl border border-white/5">
                        Selecciona Empresa, Sede y Tipo de Activo para ver la disponibilidad actual.
                    </div>
                )}
            </div>
        </div>
    );
}
