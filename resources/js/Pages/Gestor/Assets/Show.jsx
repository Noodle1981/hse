import { Head, Link, usePage, router } from '@inertiajs/react';
import AuthenticatedLayout from '../../../Layouts/AuthenticatedLayout';
import { useState } from 'react';

export default function Show({ asset, evidences = [] }) {
    const { flash } = usePage().props;
    const [activeTab, setActiveTab] = useState('photos'); // 'photos' | 'evidences' | 'inspections'
    const [uploadingKey, setUploadingKey] = useState(null);
    const [previewImage, setPreviewImage] = useState(null);

    const photoSlots = asset.asset_type?.photo_schema && asset.asset_type.photo_schema.length > 0
        ? asset.asset_type.photo_schema
        : [
            { key: 'foto_principal', label: 'Foto Principal / Vista General', required: true },
            { key: 'foto_placa', label: 'Foto Placa / Identificador', required: false },
        ];

    const getStatusBadge = (st) => {
        switch (st) {
            case 'operativo':
                return (
                    <span className="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full text-xs font-black uppercase tracking-wider bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                        <span className="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        Operativo
                    </span>
                );
            case 'alerta':
                return (
                    <span className="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full text-xs font-black uppercase tracking-wider bg-amber-500/10 text-amber-400 border border-amber-500/20">
                        <span className="w-2.5 h-2.5 rounded-full bg-amber-500 animate-pulse"></span>
                        En Alerta
                    </span>
                );
            case 'fuera_de_servicio':
                return (
                    <span className="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full text-xs font-black uppercase tracking-wider bg-red-500/10 text-red-400 border border-red-500/20">
                        <span className="w-2.5 h-2.5 rounded-full bg-red-500 animate-pulse"></span>
                        Fuera de Servicio
                    </span>
                );
            default:
                return <span className="text-gray-400">{st}</span>;
        }
    };

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

                    {/* FLASH MESSAGE */}
                    {flash?.message && (
                        <div className="p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl text-emerald-400 text-xs font-bold flex items-center justify-between">
                            <span>✅ {flash.message}</span>
                        </div>
                    )}

                    {/* HEADER */}
                    <div className="bg-[#16162a] p-8 rounded-[2.5rem] border border-white/5 shadow-2xl flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6">
                        <div>
                            <div className="flex items-center gap-3 mb-2">
                                <span className="text-[10px] font-black text-orange-500 uppercase tracking-[0.3em] italic">Ficha Técnica de Activo</span>
                                {getStatusBadge(asset.status)}
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
                                <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
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

                    {/* ALERTAS ACTIVAS DETALLADAS */}
                    {asset.hallazgos && asset.hallazgos.length > 0 && (
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
                                {asset.hallazgos.map(h => {
                                    let cleanDesc = h.descripcion || '';
                                    if (cleanDesc.includes('[Detalles del Activo]')) {
                                        cleanDesc = cleanDesc.split('[Detalles del Activo]')[0].trim();
                                    }
                                    return (
                                        <div key={h.id} className="p-4 bg-black/40 border border-white/5 rounded-2xl space-y-2">
                                            <span className="text-[10px] font-black text-white/50 uppercase tracking-wider block">
                                                🚨 {h.titulo.replace('Alerta: ', '')}
                                            </span>
                                            <p className="text-xs text-white font-medium leading-relaxed">
                                                {cleanDesc}
                                            </p>
                                            <div className="flex justify-between items-center text-[10px] text-white/30 pt-1 border-t border-white/5 font-mono">
                                                <span>Detección: {new Date(h.fecha_deteccion).toLocaleDateString()}</span>
                                                <span className="text-amber-500 font-bold uppercase italic">Criticidad: {h.criticidad}</span>
                                            </div>
                                        </div>
                                    );
                                })}
                            </div>
                        </div>
                    )}

                    {/* DETALLES GENERALES */}
                    <div className="bg-[#16162a] p-6 rounded-[2rem] border border-white/5 shadow-xl space-y-4">
                        <h2 className="text-xs font-black text-white/40 uppercase tracking-widest border-b border-white/5 pb-2">Información de Ubicación y Clasificación</h2>
                        <div className="grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs">
                            <div>
                                <span className="text-white/40 block text-[10px] uppercase font-bold">Empresa</span>
                                <span className="font-bold text-white text-sm">{asset.sede?.empresa?.nombre || 'N/A'}</span>
                            </div>
                            <div>
                                <span className="text-white/40 block text-[10px] uppercase font-bold">Sede / Planta</span>
                                <span className="font-bold text-white text-sm">{asset.sede?.nombre_sede || 'N/A'}</span>
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

                    {/* NAVEGACIÓN DE PESTAÑAS */}
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
                            ⚠️ Evidencias & Siniestros ({evidences.length})
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
                    </div>

                    {/* CONTENIDO PESTAÑA 1: FOTOS DE IDENTIFICACIÓN */}
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
                                                            onClick={() => setPreviewImage(photoUrl)}
                                                            className="w-full h-full object-cover cursor-pointer hover:scale-105 transition-all"
                                                        />
                                                    ) : (
                                                        <div className="text-center p-4 text-white/20">
                                                            <svg className="w-10 h-10 mx-auto mb-1 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
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
                                                            <svg className="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                                            {photoUrl ? 'Cambiar Foto' : 'Subir Foto'}
                                                        </>
                                                    )}
                                                    <input
                                                        type="file"
                                                        accept="image/*"
                                                        onChange={e => handlePhotoUpload(slot.key, e.target.files[0])}
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

                    {/* CONTENIDO PESTAÑA 2: EVIDENCIAS DE HALLAZGOS E INCIDENTES */}
                    {activeTab === 'evidences' && (
                        <div className="bg-[#16162a] p-8 rounded-b-[2.5rem] rounded-tr-[2.5rem] border border-white/5 shadow-2xl space-y-6">
                            <div>
                                <h3 className="text-sm font-black text-amber-400 uppercase italic">Histórico de Evidencias Fotográficas y Siniestros</h3>
                                <p className="text-xs text-white/40">Registro dinámico capturado durante inspecciones, fallas e incidentes.</p>
                            </div>

                            {evidences.length === 0 ? (
                                <div className="text-center py-12 text-white/30 space-y-2">
                                    <svg className="w-12 h-12 mx-auto opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
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
                                                            onClick={() => setPreviewImage(p)}
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

                    {/* CONTENIDO PESTAÑA 3: INSPECCIONES */}
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

                    {/* METADATA DINÁMICA EN TIEMPO REAL */}
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

            {/* LIGHTBOX MODAL PARA VER FOTOGRAFÍAS */}
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
