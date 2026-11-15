import React from 'react';
import { ASSET_ICONS } from './AssetStatusHelpers';

export default function ChecklistInfoModal({ infoChecklist, onClose }) {
    if (!infoChecklist) return null;

    return (
        <div className="fixed inset-0 bg-black/60 backdrop-blur-md flex items-center justify-center p-4 z-50 animate-fadeIn" onClick={onClose}>
            <div
                className="bg-[#16162d] border border-white/10 w-full max-w-lg rounded-[2.5rem] shadow-2xl relative overflow-hidden p-8"
                onClick={(e) => e.stopPropagation()}
            >
                <div className="flex items-center gap-4 mb-6">
                    <div
                        className="h-12 w-12 rounded-2xl bg-white/5 border flex items-center justify-center shadow-inner flex-shrink-0"
                        style={{
                            borderColor: infoChecklist.category ? `${infoChecklist.category.color}40` : 'rgba(255,255,255,0.1)',
                            color: infoChecklist.category ? infoChecklist.category.color : '#a5b4fc',
                        }}
                    >
                        <svg
                            className="w-6 h-6"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                            strokeWidth="2"
                            dangerouslySetInnerHTML={{
                                __html: infoChecklist.category ? (ASSET_ICONS[infoChecklist.category.icon] || ASSET_ICONS['tag']) : ASSET_ICONS['doc'],
                            }}
                        />
                    </div>
                    <div>
                        <span className="text-[9px] font-black text-orange-500 uppercase tracking-[0.3em] italic">Especificación Técnica</span>
                        <h2 className="text-lg font-black text-white uppercase tracking-tight italic">{infoChecklist.title}</h2>
                    </div>
                </div>

                <div className="space-y-4 mb-8">
                    {infoChecklist.registry_code && (
                        <div className="bg-black/30 p-3 rounded-xl border border-white/5 flex items-center justify-between">
                            <span className="text-[9px] font-black text-white/30 uppercase tracking-widest">Código Normativo:</span>
                            <span className="text-xs font-mono font-bold text-orange-400">{infoChecklist.registry_code}</span>
                        </div>
                    )}

                    <div className="space-y-1.5">
                        <span className="text-[9px] font-black text-white/30 uppercase tracking-widest block">Descripción del Protocolo:</span>
                        <div className="bg-black/20 p-4 rounded-2xl border border-white/5 text-xs text-indigo-200 font-medium leading-relaxed max-h-60 overflow-y-auto custom-scrollbar">
                            {infoChecklist.description || 'Este checklist no cuenta con una descripción detallada registrada.'}
                        </div>
                    </div>
                </div>

                <div className="flex justify-end">
                    <button
                        onClick={onClose}
                        className="px-6 py-3 bg-gradient-to-r from-orange-500 to-amber-600 text-white text-[10px] font-black uppercase italic tracking-widest rounded-xl hover:brightness-110 active:scale-95 transition-all shadow-lg cursor-pointer"
                    >
                        Entendido
                    </button>
                </div>
            </div>
        </div>
    );
}
