import React from 'react';

export default function WizardIntroStep({
    checklist,
    sections,
    totalQuestions,
    assets,
    filteredAssets,
    selectedAssetId,
    assetSearch,
    setAssetSearch,
    inspectedAssetIds,
    sreBlockError,
    formErrors,
    onSelectAsset,
    onStart,
}) {
    return (
        <div className="p-12 text-center space-y-8 animate-fade-in">
            <div className="w-24 h-24 bg-orange-500/10 border border-orange-500/20 rounded-3xl flex items-center justify-center mx-auto text-orange-500 shadow-2xl">
                <svg className="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <div>
                <h2 className="text-3xl font-black italic uppercase tracking-tighter leading-none mb-4">{checklist.title}</h2>
                <p className="text-sm font-bold text-white/40 uppercase italic tracking-wide max-w-xl mx-auto">
                    {checklist.description || 'Iniciando protocolo de verificación táctica para la sede asignada.'}
                </p>
            </div>
            <div className="pt-8 grid grid-cols-2 gap-4 max-w-sm mx-auto">
                <div className="bg-black/20 p-4 rounded-2xl border border-white/5 text-center">
                    <div className="text-[8px] font-black text-white/20 uppercase tracking-widest mb-1">Secciones</div>
                    <div className="text-xl font-black italic text-white">{sections.length}</div>
                </div>
                <div className="bg-black/20 p-4 rounded-2xl border border-white/5 text-center">
                    <div className="text-[8px] font-black text-white/20 uppercase tracking-widest mb-1">Preguntas</div>
                    <div className="text-xl font-black italic text-white">{totalQuestions}</div>
                </div>
            </div>

            {assets && assets.length > 0 && (
                <div className="pt-4 max-w-lg mx-auto text-left space-y-3 bg-black/30 p-6 rounded-3xl border border-white/10">
                    <div className="flex items-center justify-between">
                        <label className="text-[10px] font-black text-orange-400 uppercase tracking-widest italic">
                            Seleccionar Activo Objeto de Inspección *
                        </label>
                        <span className="text-[9px] font-bold text-white/30 italic">Paso 0 - Trazabilidad</span>
                    </div>

                    {(sreBlockError || formErrors?.asset_id) && (
                        <div className="flex items-start gap-3 bg-red-500/10 border border-red-500/30 rounded-2xl p-4 animate-fade-in">
                            <svg className="w-5 h-5 text-red-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <p className="text-[10px] font-black text-red-400 uppercase tracking-widest italic">{sreBlockError || formErrors.asset_id}</p>
                        </div>
                    )}

                    <input
                        type="text"
                        value={assetSearch}
                        onChange={e => setAssetSearch(e.target.value)}
                        placeholder="Buscar por nombre o QR / Patente..."
                        className="w-full bg-black/50 border border-white/10 rounded-xl px-4 py-2.5 text-xs text-white placeholder-white/20 focus:outline-none focus:border-orange-500"
                    />
                    <div className="max-h-48 overflow-y-auto space-y-2 pr-1">
                        {filteredAssets.length === 0 ? (
                            <p className="text-xs text-white/30 italic py-2 text-center">No hay activos que coincidan con la búsqueda.</p>
                        ) : (
                            filteredAssets.map(ast => {
                                const isInspected = (inspectedAssetIds || []).includes(ast.id);
                                return (
                                    <div
                                        key={ast.id}
                                        onClick={() => onSelectAsset(ast.id)}
                                        className={`p-3 rounded-xl border cursor-pointer flex items-center justify-between transition-all ${
                                            selectedAssetId === ast.id
                                                ? 'bg-orange-500/10 border-orange-500 text-white shadow-lg'
                                                : 'bg-white/5 border-white/5 text-white/70 hover:bg-white/10'
                                        }`}
                                    >
                                        <div>
                                            <div className="font-bold text-xs uppercase italic flex items-center gap-2">
                                                {ast.name}
                                                {isInspected && (
                                                    <span className="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded bg-emerald-500/20 border border-emerald-500/30 text-emerald-400 text-[8px] font-black uppercase tracking-wider">
                                                        ✓ Auditado
                                                    </span>
                                                )}
                                            </div>
                                            <div className="text-[10px] font-mono text-orange-400">QR: {ast.identifier}</div>
                                        </div>
                                        <span className={`px-2 py-0.5 rounded text-[9px] font-black uppercase ${
                                            ast.status === 'operativo' ? 'bg-emerald-500/20 text-emerald-400' : (ast.status === 'alerta' ? 'bg-amber-500/20 text-amber-400' : 'bg-red-500/20 text-red-400')
                                        }`}>
                                            {ast.status}
                                        </span>
                                    </div>
                                );
                            })
                        )}
                    </div>
                </div>
            )}

            <div className="pt-10">
                <button
                    onClick={onStart}
                    className="px-16 py-6 bg-orange-600 text-white rounded-[2.5rem] text-xs font-black uppercase tracking-[0.2em] italic shadow-2xl shadow-orange-600/20 hover:brightness-110 active:scale-95 transition-all"
                >
                    Iniciar Protocolo
                </button>
            </div>
        </div>
    );
}
