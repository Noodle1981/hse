import { Head, useForm, Link } from '@inertiajs/react';
import AuthenticatedLayout from '../../../Layouts/AuthenticatedLayout';
import { useMemo } from 'react';

export default function Edit({ asset, empresas = [], sedes = [], assetTypes = [] }) {
    const { data, setData, put, processing, errors } = useForm({
        empresa_id: asset.empresa_id || '',
        sede_id: asset.sede_id || '',
        asset_type_id: asset.asset_type_id || '',
        name: asset.name || '',
        identifier: asset.identifier || '',
        status: asset.status || 'operativo',
        metadata: asset.metadata || {},
    });

    const selectedType = useMemo(() => {
        return assetTypes.find(t => t.id.toString() === data.asset_type_id.toString());
    }, [data.asset_type_id, assetTypes]);

    const availableSedes = useMemo(() => {
        return data.empresa_id
            ? sedes.filter(s => s.empresa_id.toString() === data.empresa_id.toString())
            : sedes;
    }, [data.empresa_id, sedes]);

    const handleMetadataChange = (key, value) => {
        setData('metadata', {
            ...data.metadata,
            [key]: value,
        });
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        put(route('gestor.assets.update', asset.id));
    };

    return (
        <AuthenticatedLayout title={`Editar Activo: ${asset.name}`}>
            <Head title={`Editar Activo: ${asset.name}`} />

            <div className="min-h-screen bg-[#11111d] text-white p-4 sm:p-8">
                <div className="max-w-4xl mx-auto space-y-8">
                    
                    {/* HEADER */}
                    <div className="flex items-center justify-between bg-[#16162a] p-6 rounded-[2rem] border border-white/5 shadow-2xl">
                        <div>
                            <span className="text-[10px] font-black text-orange-500 uppercase tracking-[0.3em] italic">Edición de Equipamiento</span>
                            <h1 className="text-xl font-black text-white uppercase italic">Editar: {asset.name}</h1>
                        </div>
                        <div className="flex gap-2">
                            <Link
                                href={route('gestor.assets.show', asset.id)}
                                className="px-4 py-2 bg-indigo-500/10 border border-indigo-500/20 rounded-xl text-xs font-bold text-indigo-400 hover:bg-indigo-500/20 transition-all"
                            >
                                Ver Ficha
                            </Link>
                            <Link
                                href={route('gestor.assets.index')}
                                className="px-4 py-2 bg-white/5 border border-white/10 rounded-xl text-xs font-bold text-white/70 hover:bg-white/10 transition-all"
                            >
                                Listado
                            </Link>
                        </div>
                    </div>

                    {/* FORMULARIO */}
                    <form onSubmit={handleSubmit} className="bg-[#16162a] p-8 rounded-[2.5rem] border border-white/5 shadow-2xl space-y-6">
                        
                        <div className="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            {/* EMPRESA */}
                            <div>
                                <label className="block text-[10px] font-black text-white/40 uppercase tracking-widest mb-2 italic">Empresa *</label>
                                <select
                                    value={data.empresa_id}
                                    onChange={e => {
                                        setData(d => ({ ...d, empresa_id: e.target.value, sede_id: '' }));
                                    }}
                                    className="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-orange-500"
                                    required
                                >
                                    <option value="">Seleccione Empresa...</option>
                                    {empresas.map(emp => (
                                        <option key={emp.id} value={emp.id}>{emp.nombre}</option>
                                    ))}
                                </select>
                                {errors.empresa_id && <p className="text-xs text-red-400 mt-1">{errors.empresa_id}</p>}
                            </div>

                            {/* SEDE */}
                            <div>
                                <label className="block text-[10px] font-black text-white/40 uppercase tracking-widest mb-2 italic">Sede / Planta *</label>
                                <select
                                    value={data.sede_id}
                                    onChange={e => setData('sede_id', e.target.value)}
                                    className="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-orange-500"
                                    required
                                    disabled={!data.empresa_id}
                                >
                                    <option value="">Seleccione Sede...</option>
                                    {availableSedes.map(s => (
                                        <option key={s.id} value={s.id}>{s.nombre_sede}</option>
                                    ))}
                                </select>
                                {errors.sede_id && <p className="text-xs text-red-400 mt-1">{errors.sede_id}</p>}
                            </div>
                        </div>

                        <div className="grid grid-cols-1 sm:grid-cols-3 gap-6">
                            {/* TIPO DE ACTIVO */}
                            <div>
                                <label className="block text-[10px] font-black text-white/40 uppercase tracking-widest mb-2 italic">Tipo de Activo *</label>
                                <select
                                    value={data.asset_type_id}
                                    onChange={e => setData('asset_type_id', e.target.value)}
                                    className="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-orange-500"
                                    required
                                >
                                    <option value="">Seleccione Tipo...</option>
                                    {assetTypes.map(type => (
                                        <option key={type.id} value={type.id}>{type.name}</option>
                                    ))}
                                </select>
                                {errors.asset_type_id && <p className="text-xs text-red-400 mt-1">{errors.asset_type_id}</p>}
                            </div>

                            {/* NOMBRE */}
                            <div>
                                <label className="block text-[10px] font-black text-white/40 uppercase tracking-widest mb-2 italic">Nombre del Activo *</label>
                                <input
                                    type="text"
                                    value={data.name}
                                    onChange={e => setData('name', e.target.value)}
                                    className="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-orange-500"
                                    required
                                />
                                {errors.name && <p className="text-xs text-red-400 mt-1">{errors.name}</p>}
                            </div>

                            {/* IDENTIFICADOR / QR */}
                            <div>
                                <label className="block text-[10px] font-black text-white/40 uppercase tracking-widest mb-2 italic">Identificador / Código QR *</label>
                                <input
                                    type="text"
                                    value={data.identifier}
                                    onChange={e => setData('identifier', e.target.value)}
                                    className="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-sm font-mono text-orange-400 focus:outline-none focus:border-orange-500"
                                    required
                                />
                                {errors.identifier && <p className="text-xs text-red-400 mt-1">{errors.identifier}</p>}
                            </div>
                        </div>

                        {/* ESTADO */}
                        <div>
                            <label className="block text-[10px] font-black text-white/40 uppercase tracking-widest mb-2 italic">Estado *</label>
                            <div className="flex gap-4">
                                <label className="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="status" value="operativo" checked={data.status === 'operativo'} onChange={e => setData('status', e.target.value)} className="text-emerald-500" />
                                    <span className="text-xs font-bold text-emerald-400">Operativo</span>
                                </label>
                                <label className="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="status" value="alerta" checked={data.status === 'alerta'} onChange={e => setData('status', e.target.value)} className="text-amber-500" />
                                    <span className="text-xs font-bold text-amber-400">En Alerta</span>
                                </label>
                                <label className="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="status" value="fuera_de_servicio" checked={data.status === 'fuera_de_servicio'} onChange={e => setData('status', e.target.value)} className="text-red-500" />
                                    <span className="text-xs font-bold text-red-400">Fuera de Servicio</span>
                                </label>
                            </div>
                        </div>

                        {/* METADATA DINÁMICA */}
                        {selectedType && selectedType.metadata_schema && selectedType.metadata_schema.length > 0 && (
                            <div className="p-6 bg-black/30 border border-white/10 rounded-2xl space-y-4">
                                <div className="flex items-center justify-between border-b border-white/5 pb-2">
                                    <h3 className="text-xs font-black text-orange-400 uppercase tracking-wider italic">
                                        Campos de Metadata Dinámica ({selectedType.name})
                                    </h3>
                                </div>
                                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    {selectedType.metadata_schema.map((field) => (
                                        <div key={field.name}>
                                            <label className="block text-[10px] font-bold text-white/60 uppercase mb-1">
                                                {field.label || field.name}
                                                {field.alert_days && <span className="ml-2 text-[9px] text-amber-400">(Alerta ≤ {field.alert_days}d)</span>}
                                                {field.alert_threshold && <span className="ml-2 text-[9px] text-amber-400">(Alerta ≥ {field.alert_threshold})</span>}
                                            </label>
                                            <input
                                                type={field.type === 'number' ? 'number' : (field.type === 'date' ? 'date' : 'text')}
                                                step={field.type === 'number' ? 'any' : undefined}
                                                value={data.metadata[field.name] || ''}
                                                onChange={e => handleMetadataChange(field.name, e.target.value)}
                                                className="w-full bg-black/50 border border-white/10 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-orange-500"
                                            />
                                        </div>
                                    ))}
                                </div>
                            </div>
                        )}

                        {/* BOTÓN SUBMIT */}
                        <div className="flex justify-end pt-4">
                            <button
                                type="submit"
                                disabled={processing}
                                className="px-8 py-3.5 bg-gradient-to-r from-orange-500 to-amber-600 text-white font-black text-xs uppercase tracking-widest rounded-xl shadow-lg hover:brightness-110 transition-all active:scale-95 disabled:opacity-50"
                            >
                                {processing ? 'Actualizando...' : 'Guardar Cambios'}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
