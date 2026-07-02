import { Head, Link, usePage } from '@inertiajs/react';
import AuthenticatedLayout from '../../../Layouts/AuthenticatedLayout';

export default function Show({ asset }) {
    const { flash } = usePage().props;

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

    return (
        <AuthenticatedLayout title={`Ficha de Activo: ${asset.name}`}>
            <Head title={`Ficha: ${asset.name}`} />

            <div className="min-h-screen bg-[#11111d] text-white p-4 sm:p-8 space-y-8">
                <div className="max-w-6xl mx-auto space-y-8">

                    {/* FLASH MESSAGE */}
                    {flash?.message && (
                        <div className="p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl text-emerald-400 text-xs font-bold">
                            {flash.message}
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
                        <div className="flex gap-3">
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

                    {/* GRID PRINCIPAL */}
                    <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        
                        {/* INFORMACIÓN Y METADATA (2 cols) */}
                        <div className="lg:col-span-2 space-y-8">
                            
                            {/* DETALLES GENERALES */}
                            <div className="bg-[#16162a] p-6 rounded-[2rem] border border-white/5 shadow-xl space-y-4">
                                <h2 className="text-xs font-black text-white/40 uppercase tracking-widest border-b border-white/5 pb-2">Información de Ubicación y Clasificación</h2>
                                <div className="grid grid-cols-2 gap-4 text-xs">
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
                                        <span className="font-bold text-white/70">{new Date(asset.created_at).toLocaleDateString()}</span>
                                    </div>
                                </div>
                            </div>

                            {/* FICHA DE METADATA EN TIEMPO REAL */}
                            <div className="bg-[#16162a] p-6 rounded-[2rem] border border-white/5 shadow-xl space-y-4">
                                <div className="flex items-center justify-between border-b border-white/5 pb-2">
                                    <h2 className="text-xs font-black text-orange-400 uppercase tracking-widest italic">Métricas y Metadata en Vivo</h2>
                                    <span className="text-[10px] text-white/30">Sincronizado vía Inspecciones</span>
                                </div>

                                {asset.metadata && Object.keys(asset.metadata).length > 0 ? (
                                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
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

                        {/* TIMELINE DE INSPECCIONES (1 col) */}
                        <div className="bg-[#16162a] p-6 rounded-[2rem] border border-white/5 shadow-xl space-y-4">
                            <h2 className="text-xs font-black text-white/40 uppercase tracking-widest border-b border-white/5 pb-2">Últimas Inspecciones</h2>

                            {asset.dynamic_inspections && asset.dynamic_inspections.length > 0 ? (
                                <div className="space-y-3">
                                    {asset.dynamic_inspections.map(ins => (
                                        <div key={ins.id} className="p-3 bg-black/30 border border-white/5 rounded-xl text-xs space-y-1">
                                            <div className="flex items-center justify-between">
                                                <span className="font-bold text-white uppercase italic truncate max-w-[150px]">{ins.checklist?.title}</span>
                                                <span className={`px-2 py-0.5 rounded text-[9px] font-black uppercase ${ins.result === 'Pass' ? 'bg-emerald-500/10 text-emerald-400' : 'bg-red-500/10 text-red-400'}`}>
                                                    {ins.result}
                                                </span>
                                            </div>
                                            <div className="text-[10px] text-white/40 flex items-center justify-between">
                                                <span>{ins.user?.name}</span>
                                                <span>{ins.completed_at ? new Date(ins.completed_at).toLocaleDateString() : 'N/D'}</span>
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            ) : (
                                <p className="text-xs text-white/30 italic py-6 text-center">Este activo aún no cuenta con inspecciones vinculadas.</p>
                            )}
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
