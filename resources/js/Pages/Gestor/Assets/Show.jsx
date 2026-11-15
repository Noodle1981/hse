import { Head, Link, usePage, router } from '@inertiajs/react';
import AuthenticatedLayout from '../../../Layouts/AuthenticatedLayout';
import { useState } from 'react';
import { parseAlertFinding, getAssetHallazgosWithVirtual, AssetStatusBadge } from './Partials/AssetStatusHelpers';
import AssetShowTabs from './Partials/AssetShowTabs';

export default function Show({ asset, evidences = [], metadataLogs = [] }) {
    const { flash } = usePage().props;
    const [activeTab, setActiveTab] = useState('photos');
    const [uploadingKey, setUploadingKey] = useState(null);
    const [previewImage, setPreviewImage] = useState(null);
    const hallazgos = getAssetHallazgosWithVirtual(asset);

    const photoSlots = asset.asset_type?.photo_schema && asset.asset_type.photo_schema.length > 0
        ? asset.asset_type.photo_schema
        : [
            { key: 'foto_principal', label: 'Foto Principal / Vista General', required: true },
            { key: 'foto_placa', label: 'Foto Placa / Identificador', required: false },
        ];

    const handlePhotoUpload = (key, file) => {
        if (!file) return;
        setUploadingKey(key);
        const formData = new FormData();
        formData.append('photo_key', key);
        formData.append('photo', file);

        router.post(route('gestor.assets.upload-photo', asset.id), formData, {
            forceFormData: true,
            onFinish: () => setUploadingKey(null),
        });
    };

    return (
        <AuthenticatedLayout title={`Ficha de Activo: ${asset.name}`}>
            <Head title={`Ficha: ${asset.name}`} />

            <div className="min-h-screen bg-[#11111d] text-white p-4 sm:p-8 space-y-8">
                <div className="max-w-6xl mx-auto space-y-8">
                    {flash?.message && (
                        <div className="p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl text-emerald-400 text-xs font-bold flex items-center justify-between">
                            <span>✅ {flash.message}</span>
                        </div>
                    )}

                    <div className="bg-[#16162a] p-8 rounded-[2.5rem] border border-white/5 shadow-2xl flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6">
                        <div>
                            <div className="flex items-center gap-3 mb-2">
                                <span className="text-[10px] font-black text-orange-500 uppercase tracking-[0.3em] italic">Ficha Técnica de Activo</span>
                                <AssetStatusBadge status={asset.status} />
                            </div>
                            <h1 className="text-3xl font-black text-white uppercase italic tracking-tight mb-1">{asset.name}</h1>
                            <p className="text-xs text-orange-400 font-mono">ID / Código QR: <strong className="text-white">{asset.identifier}</strong></p>
                        </div>
                        <div className="flex flex-wrap gap-3">
                            <a
                                href={route('gestor.assets.export-pdf', asset.id)}
                                target="_blank"
                                rel="noreferrer"
                                className="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-black text-xs uppercase tracking-widest rounded-xl shadow-lg transition-all flex items-center gap-2"
                            >
                                <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                📄 Exportar PDF
                            </a>
                            <Link
                                href={route('gestor.assets.edit', asset.id)}
                                className="px-5 py-2.5 bg-gradient-to-r from-orange-500 to-amber-600 text-white font-black text-xs uppercase tracking-widest rounded-xl shadow-lg hover:brightness-110 transition-all"
                            >
                                Editar Activo
                            </Link>
                            <Link
                                href={route('gestor.assets.index')}
                                className="px-5 py-2.5 bg-white/5 border border-white/10 rounded-xl text-xs font-bold text-white/70 hover:bg-white/10 transition-all"
                            >
                                Volver
                            </Link>
                        </div>
                    </div>

                    {hallazgos && hallazgos.length > 0 && (
                        <div className="bg-amber-500/10 border border-amber-500/20 p-6 rounded-[2rem] shadow-2xl space-y-4 animate-fade-in-down">
                            <div className="flex items-center gap-3">
                                <div className="h-10 w-10 rounded-2xl bg-amber-500/20 border border-amber-500/30 text-amber-400 flex items-center justify-center animate-pulse">
                                    <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 className="text-xs font-black text-amber-400 uppercase tracking-[0.2em] italic">Alertas y Vencimientos Detectados</h3>
                                    <p className="text-[10px] font-black text-indigo-100/40 uppercase tracking-[0.1em] italic">Este equipo requiere atención debido a las siguientes alertas del sistema:</p>
                                </div>
                            </div>
                            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                {hallazgos.map(h => {
                                    const alertInfo = parseAlertFinding(h);
                                    return (
                                        <div key={h.id} className="p-5 bg-black/40 border border-white/5 rounded-2xl space-y-3 relative overflow-hidden group">
                                            <div className="absolute inset-0 bg-gradient-to-r from-amber-500/0 via-amber-500/[0.02] to-amber-500/0 opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none" />

                                            <div className="flex items-center justify-between border-b border-white/5 pb-2">
                                                <div className="flex items-center gap-2">
                                                    <span className={`px-2 py-0.5 rounded-lg text-[8px] font-black uppercase tracking-wider ${
                                                        alertInfo.isSre
                                                            ? 'bg-indigo-500/20 text-indigo-300 border border-indigo-500/30'
                                                            : 'bg-amber-500/20 text-amber-300 border border-amber-500/30'
                                                    }`}>
                                                        {alertInfo.isSre ? 'Regla SRE' : 'Alerta'}
                                                    </span>
                                                    <span className="text-xs font-black text-white uppercase italic tracking-wide">
                                                        {alertInfo.ruleName}
                                                    </span>
                                                </div>
                                                <span className="text-[10px] text-amber-500 font-bold uppercase italic font-mono">
                                                    {h.criticidad}
                                                </span>
                                            </div>

                                            <p className="text-xs text-indigo-100/90 leading-relaxed font-medium">
                                                {alertInfo.reason}
                                            </p>

                                            <div className="flex justify-between items-center text-[9px] text-white/30 pt-2 border-t border-white/5 font-mono">
                                                <span>Detección: {new Date(h.fecha_deteccion).toLocaleDateString()} {new Date(h.fecha_deteccion).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}</span>
                                                <span>Origen: Sistema Automático</span>
                                            </div>
                                        </div>
                                    );
                                })}
                            </div>
                        </div>
                    )}

                    <div className="bg-[#16162a] p-6 rounded-[2rem] border border-white/5 shadow-xl space-y-4">
                        <h2 className="text-xs font-black text-white/40 uppercase tracking-widest border-b border-white/5 pb-2">Información de Ubicación y Clasificación</h2>
                        <div className="grid grid-cols-2 sm:grid-cols-6 gap-4 text-xs">
                            <div>
                                <span className="text-white/40 block text-[10px] uppercase font-bold">Empresa</span>
                                <span className="font-bold text-white text-sm">{asset.sede?.empresa?.nombre || 'N/A'}</span>
                            </div>
                            <div>
                                <span className="text-white/40 block text-[10px] uppercase font-bold">Sede / Planta</span>
                                <span className="font-bold text-white text-sm">{asset.sede?.nombre_sede || 'N/A'}</span>
                            </div>
                            <div>
                                <span className="text-white/40 block text-[10px] uppercase font-bold">{asset.asset_type?.group_label || 'Grupo'}</span>
                                <span className="font-bold text-orange-400 text-sm">{asset.grupo || 'Sin Clasificar'}</span>
                            </div>
                            <div>
                                <span className="text-white/40 block text-[10px] uppercase font-bold">{asset.asset_type?.subgroup_label || 'Subgrupo'}</span>
                                <span className="font-bold text-white text-sm">{asset.subgrupo || 'Sin Clasificar'}</span>
                            </div>
                            <div>
                                <span className="text-white/40 block text-[10px] uppercase font-bold">Tipo de Activo</span>
                                <span className="font-bold text-indigo-400 text-sm">{asset.asset_type?.name || 'Genérico'}</span>
                            </div>
                            <div>
                                <span className="text-white/40 block text-[10px] uppercase font-bold">Fecha de Registro</span>
                                <span className="font-bold text-white/70">{asset.created_at ? new Date(asset.created_at).toLocaleDateString() : 'N/D'}</span>
                            </div>
                        </div>
                    </div>

                    <AssetShowTabs
                        asset={asset}
                        activeTab={activeTab}
                        setActiveTab={setActiveTab}
                        photoSlots={photoSlots}
                        uploadingKey={uploadingKey}
                        onPhotoUpload={handlePhotoUpload}
                        onPreviewImage={setPreviewImage}
                        evidences={evidences}
                        metadataLogs={metadataLogs}
                    />

                    <div className="bg-[#16162a] p-6 rounded-[2rem] border border-white/5 shadow-xl space-y-4">
                        <div className="flex items-center justify-between border-b border-white/5 pb-2">
                            <h2 className="text-xs font-black text-orange-400 uppercase tracking-widest italic">Métricas y Metadata en Vivo</h2>
                            <span className="text-[10px] text-white/30">Sincronizado vía Inspecciones</span>
                        </div>

                        {asset.metadata && Object.keys(asset.metadata).length > 0 ? (
                            <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                {Object.entries(asset.metadata).map(([key, val]) => (
                                    <div key={key} className="p-4 bg-black/40 border border-white/10 rounded-2xl">
                                        <span className="text-[10px] font-black text-white/40 uppercase tracking-widest block mb-1">{key}</span>
                                        <span className="text-base font-black text-white italic">{String(val)}</span>
                                    </div>
                                ))}
                            </div>
                        ) : (
                            <p className="text-xs text-white/30 italic py-4 text-center">No hay metadata registrada actualmente para este activo.</p>
                        )}
                    </div>
                </div>
            </div>

            {previewImage && (
                <div className="fixed inset-0 bg-black/90 backdrop-blur-md flex items-center justify-center p-4 z-50" onClick={() => setPreviewImage(null)}>
                    <div className="relative max-w-4xl max-h-[90vh]">
                        <img src={previewImage} alt="Preview" className="max-w-full max-h-[85vh] rounded-2xl shadow-2xl border border-white/20" />
                        <button onClick={() => setPreviewImage(null)} className="absolute top-4 right-4 bg-black/60 text-white p-2 rounded-full font-bold">✕</button>
                    </div>
                </div>
            )}
        </AuthenticatedLayout>
    );
}
