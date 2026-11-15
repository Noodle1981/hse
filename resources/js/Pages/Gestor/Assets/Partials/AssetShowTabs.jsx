import React from 'react';
import { Link } from '@inertiajs/react';

export default function AssetShowTabs({
    asset,
    activeTab,
    setActiveTab,
    photoSlots,
    uploadingKey,
    onPhotoUpload,
    onPreviewImage,
    evidences = [],
    metadataLogs = [],
}) {
    return (
        <>
            <div className="flex border-b border-white/10 gap-2">
                <button
                    onClick={() => setActiveTab('photos')}
                    className={`px-6 py-3 font-black text-xs uppercase tracking-wider rounded-t-2xl border-t border-x transition-all ${
                        activeTab === 'photos'
                            ? 'bg-[#16162a] border-orange-500/30 text-orange-400'
                            : 'bg-black/20 border-transparent text-white/40 hover:text-white'
                    }`}
                >
                    📸 Fotos de Identificación ({Object.keys(asset.photos || {}).length}/{photoSlots.length})
                </button>
                <button
                    onClick={() => setActiveTab('evidences')}
                    className={`px-6 py-3 font-black text-xs uppercase tracking-wider rounded-t-2xl border-t border-x transition-all ${
                        activeTab === 'evidences'
                            ? 'bg-[#16162a] border-amber-500/30 text-amber-400'
                            : 'bg-black/20 border-transparent text-white/40 hover:text-white'
                    }`}
                >
                    ⚠️ Evidencias &amp; Siniestros ({evidences.length})
                </button>
                <button
                    onClick={() => setActiveTab('inspections')}
                    className={`px-6 py-3 font-black text-xs uppercase tracking-wider rounded-t-2xl border-t border-x transition-all ${
                        activeTab === 'inspections'
                            ? 'bg-[#16162a] border-indigo-500/30 text-indigo-400'
                            : 'bg-black/20 border-transparent text-white/40 hover:text-white'
                    }`}
                >
                    📋 Historial de Inspecciones ({asset.dynamic_inspections?.length || 0})
                </button>
                <button
                    onClick={() => setActiveTab('metadata_logs')}
                    className={`px-6 py-3 font-black text-xs uppercase tracking-wider rounded-t-2xl border-t border-x transition-all ${
                        activeTab === 'metadata_logs'
                            ? 'bg-[#16162a] border-emerald-500/30 text-emerald-400'
                            : 'bg-black/20 border-transparent text-white/40 hover:text-white'
                    }`}
                >
                    🕐 Historial de Metadata ({metadataLogs?.length || 0})
                </button>
            </div>

            {activeTab === 'photos' && (
                <div className="bg-[#16162a] p-8 rounded-b-[2.5rem] rounded-tr-[2.5rem] border border-white/5 shadow-2xl space-y-6">
                    <div className="flex items-center justify-between border-b border-white/5 pb-4">
                        <div>
                            <h3 className="text-sm font-black text-orange-400 uppercase italic">Expediente Fotográfico del Activo</h3>
                            <p className="text-xs text-white/40">Imágenes base de identificación configuradas para el tipo de activo <strong className="text-white">{asset.asset_type?.name}</strong>.</p>
                        </div>
                    </div>

                    <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                        {photoSlots.map((slot) => {
                            const photoUrl = asset.photos?.[slot.key];
                            const isUploading = uploadingKey === slot.key;

                            return (
                                <div key={slot.key} className="bg-black/40 border border-white/10 rounded-2xl p-4 space-y-3 flex flex-col justify-between group">
                                    <div>
                                        <div className="flex items-center justify-between mb-2">
                                            <span className="text-xs font-black text-white uppercase italic">{slot.label}</span>
                                            {slot.required && <span className="text-[9px] font-bold text-amber-400 uppercase">Requerida</span>}
                                        </div>

                                        <div className="relative aspect-video bg-black/60 rounded-xl overflow-hidden border border-white/5 flex items-center justify-center">
                                            {photoUrl ? (
                                                <img
                                                    src={photoUrl}
                                                    alt={slot.label}
                                                    onClick={() => onPreviewImage(photoUrl)}
                                                    className="w-full h-full object-cover cursor-pointer hover:scale-105 transition-all"
                                                />
                                            ) : (
                                                <div className="text-center p-4 text-white/20">
                                                    <svg className="w-10 h-10 mx-auto mb-1 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" /><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                                    <span className="text-[10px] font-bold block uppercase">Sin foto cargada</span>
                                                </div>
                                            )}
                                        </div>
                                    </div>

                                    <div className="pt-2">
                                        <label className="w-full py-2 bg-white/5 border border-white/10 hover:bg-white/10 rounded-xl text-[11px] font-bold text-white/70 flex items-center justify-center gap-2 cursor-pointer transition-all">
                                            {isUploading ? (
                                                <span>Subiendo...</span>
                                            ) : (
                                                <>
                                                    <svg className="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" /></svg>
                                                    {photoUrl ? 'Cambiar Foto' : 'Subir Foto'}
                                                </>
                                            )}
                                            <input
                                                type="file"
                                                accept="image/*"
                                                onChange={e => onPhotoUpload(slot.key, e.target.files[0])}
                                                className="hidden"
                                                disabled={isUploading}
                                            />
                                        </label>
                                    </div>
                                </div>
                            );
                        })}
                    </div>
                </div>
            )}

            {activeTab === 'evidences' && (
                <div className="bg-[#16162a] p-8 rounded-b-[2.5rem] rounded-tr-[2.5rem] border border-white/5 shadow-2xl space-y-6">
                    <div>
                        <h3 className="text-sm font-black text-amber-400 uppercase italic">Histórico de Evidencias Fotográficas y Siniestros</h3>
                        <p className="text-xs text-white/40">Registro dinámico capturado durante inspecciones, fallas e incidentes.</p>
                    </div>

                    {evidences.length === 0 ? (
                        <div className="text-center py-12 text-white/30 space-y-2">
                            <svg className="w-12 h-12 mx-auto opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                            <p className="text-xs font-bold">Sin fotos de hallazgos o incidentes para este activo aún.</p>
                        </div>
                    ) : (
                        <div className="space-y-4">
                            {evidences.map((ev) => (
                                <div key={ev.id} className="p-5 bg-black/30 border border-white/5 rounded-2xl space-y-3">
                                    <div className="flex items-center justify-between">
                                        <div className="flex items-center gap-2">
                                            <span className={`px-2.5 py-1 rounded-lg text-[10px] font-black uppercase ${
                                                ev.type === 'incident' ? 'bg-red-500/20 text-red-400 border border-red-500/30' : 'bg-amber-500/20 text-amber-400 border border-amber-500/30'
                                            }`}>
                                                {ev.type === 'incident' ? '🚨 Siniestro' : '⚠️ Inspección'}
                                            </span>
                                            <h4 className="text-xs font-bold text-white">{ev.title}</h4>
                                        </div>
                                        <span className="text-[10px] font-mono text-white/40">{ev.date}</span>
                                    </div>

                                    <p className="text-xs text-white/70">{ev.description}</p>
                                    <div className="text-[10px] text-white/40">Reportado por: <strong className="text-white/60">{ev.user}</strong></div>

                                    {ev.photos && ev.photos.length > 0 && (
                                        <div className="flex flex-wrap gap-3 pt-2">
                                            {ev.photos.map((p, pI) => (
                                                <img
                                                    key={pI}
                                                    src={p}
                                                    alt="Evidencia"
                                                    onClick={() => onPreviewImage(p)}
                                                    className="w-24 h-24 object-cover rounded-xl border border-white/10 cursor-pointer hover:scale-105 transition-all"
                                                />
                                            ))}
                                        </div>
                                    )}
                                </div>
                            ))}
                        </div>
                    )}
                </div>
            )}

            {activeTab === 'inspections' && (
                <div className="bg-[#16162a] p-8 rounded-b-[2.5rem] rounded-tr-[2.5rem] border border-white/5 shadow-2xl space-y-6">
                    <h3 className="text-sm font-black text-indigo-400 uppercase italic">Historial de Inspecciones Periódicas</h3>

                    {asset.dynamic_inspections && asset.dynamic_inspections.length > 0 ? (
                        <div className="space-y-3">
                            {asset.dynamic_inspections.map(ins => (
                                <div key={ins.id} className="p-4 bg-black/30 border border-white/5 rounded-2xl flex items-center justify-between">
                                    <div>
                                        <h4 className="text-xs font-bold text-white uppercase italic">{ins.checklist?.title}</h4>
                                        <p className="text-[10px] text-white/40 mt-0.5">Por: {ins.user?.name} | {ins.completed_at ? new Date(ins.completed_at).toLocaleString() : 'N/D'}</p>
                                    </div>
                                    <span className={`px-3 py-1 rounded-full text-xs font-black uppercase ${ins.result === 'Pass' ? 'bg-emerald-500/10 text-emerald-400' : 'bg-red-500/10 text-red-400'}`}>
                                        {ins.result}
                                    </span>
                                </div>
                            ))}
                        </div>
                    ) : (
                        <p className="text-xs text-white/30 italic py-6 text-center">Este activo aún no cuenta con inspecciones vinculadas.</p>
                    )}
                </div>
            )}

            {activeTab === 'metadata_logs' && (
                <div className="bg-[#16162a] p-8 rounded-b-[2.5rem] rounded-tr-[2.5rem] border border-white/5 shadow-2xl space-y-6">
                    <h3 className="text-sm font-black text-emerald-400 uppercase italic">Historial de Cambios de Metadata (Trazabilidad)</h3>
                    <p className="text-xs text-white/40">Registro auditor inmutable de cada campo de datos que fue actualizado a través de inspecciones.</p>

                    {metadataLogs && metadataLogs.length > 0 ? (
                        <div className="overflow-x-auto custom-scrollbar">
                            <table className="w-full text-left text-xs border-collapse">
                                <thead>
                                    <tr className="border-b border-white/10 text-white/40 font-bold uppercase tracking-wider">
                                        <th className="py-3 px-4">Fecha</th>
                                        <th className="py-3 px-4">Atributo</th>
                                        <th className="py-3 px-4">Valor Anterior</th>
                                        <th className="py-3 px-4">Nuevo Valor</th>
                                        <th className="py-3 px-4">Técnico</th>
                                        <th className="py-3 px-4">Inspección</th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-white/5 text-white/80">
                                    {metadataLogs.map(log => {
                                        const schemaField = asset.asset_type?.metadata_schema?.find(f => f.name === log.field_key);
                                        const fieldLabel = schemaField ? schemaField.label : log.field_key;

                                        return (
                                            <tr key={log.id} className="hover:bg-white/5 transition-all">
                                                <td className="py-3 px-4 font-mono">{log.created_at ? new Date(log.created_at).toLocaleString() : 'N/D'}</td>
                                                <td className="py-3 px-4 font-bold text-white">{fieldLabel}</td>
                                                <td className="py-3 px-4 font-mono text-white/40">{log.old_value !== null && log.old_value !== '' ? log.old_value : '—'}</td>
                                                <td className="py-3 px-4 font-mono text-emerald-400">{log.new_value !== null && log.new_value !== '' ? log.new_value : '—'}</td>
                                                <td className="py-3 px-4 font-bold">{log.changed_by_user?.name || log.changed_by?.name || 'Sistema'}</td>
                                                <td className="py-3 px-4">
                                                    {log.inspection ? (
                                                        <Link
                                                            href={route('tecnico.checklists.fill', [log.inspection.dynamic_checklist_id, log.inspection.checklist_assignment_id])}
                                                            className="text-indigo-400 hover:text-indigo-300 underline font-bold"
                                                        >
                                                            #{log.inspection.id} ({log.inspection.checklist?.title || 'Checklist'})
                                                        </Link>
                                                    ) : 'N/A'}
                                                </td>
                                            </tr>
                                        );
                                    })}
                                </tbody>
                            </table>
                        </div>
                    ) : (
                        <p className="text-xs text-white/30 italic py-6 text-center">No se registran cambios de metadata históricos para este activo.</p>
                    )}
                </div>
            )}
        </>
    );
}
