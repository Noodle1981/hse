import { Head, useForm, Link } from '@inertiajs/react';
import AuthenticatedLayout from '../../../Layouts/AuthenticatedLayout';
import { useMemo } from 'react';

export default function Create({ empresas = [], sedes = [], assetTypes = [] }) {
    const { data, setData, post, processing, errors } = useForm({
        empresa_id: '',
        sede_id: '',
        asset_type_id: '',
        items: [
            {
                name: '',
                identifier: '',
                status: 'operativo',
                metadata: {},
            },
        ],
    });

    const selectedType = useMemo(() => {
        return assetTypes.find(t => t.id.toString() === data.asset_type_id.toString());
    }, [data.asset_type_id, assetTypes]);

    const availableSedes = useMemo(() => {
        return data.empresa_id
            ? sedes.filter(s => s.empresa_id.toString() === data.empresa_id.toString())
            : sedes;
    }, [data.empresa_id, sedes]);

    const createFields = useMemo(() => {
        if (!selectedType || !selectedType.metadata_schema) return [];
        return selectedType.metadata_schema.filter(field => !!field.show_in_create);
    }, [selectedType]);

    // Detectar identificadores duplicados en tiempo real dentro del formulario
    const duplicateIdentifiers = useMemo(() => {
        const counts = {};
        data.items.forEach(item => {
            const trimmed = (item.identifier || '').trim().toLowerCase();
            if (trimmed) {
                counts[trimmed] = (counts[trimmed] || 0) + 1;
            }
        });
        return Object.keys(counts).filter(id => counts[id] > 1);
    }, [data.items]);

    const isDuplicateItem = (identifier) => {
        const trimmed = (identifier || '').trim().toLowerCase();
        return trimmed && duplicateIdentifiers.includes(trimmed);
    };

    // Actualizar campo directo de un activo en la lista
    const updateItem = (index, field, value) => {
        const updatedItems = [...data.items];
        updatedItems[index] = {
            ...updatedItems[index],
            [field]: value,
        };
        setData('items', updatedItems);
    };

    // Actualizar metadata de un activo en la lista
    const updateItemMetadata = (index, key, value) => {
        const updatedItems = [...data.items];
        const currentMeta = updatedItems[index].metadata || {};
        updatedItems[index] = {
            ...updatedItems[index],
            metadata: {
                ...currentMeta,
                [key]: value,
            },
        };
        setData('items', updatedItems);
    };

    // Incremento inteligente de código al presionar + Agregar activo (ej: MF001 -> MF002)
    const autoIncrementIdentifier = (lastId) => {
        if (!lastId) return '';
        const match = lastId.match(/^(.*?)(\d+)$/);
        if (match) {
            const prefix = match[1];
            const numStr = match[2];
            const nextNum = parseInt(numStr, 10) + 1;
            const paddedNum = nextNum.toString().padStart(numStr.length, '0');
            return `${prefix}${paddedNum}`;
        }
        return `${lastId}_2`;
    };

    const addItem = () => {
        const lastItem = data.items[data.items.length - 1];
        const nextId = autoIncrementIdentifier(lastItem?.identifier || '');
        const nextName = lastItem?.name || '';

        setData('items', [
            ...data.items,
            {
                name: nextName,
                identifier: nextId,
                status: lastItem?.status || 'operativo',
                metadata: { ...(lastItem?.metadata || {}) },
            },
        ]);
    };

    const removeItem = (index) => {
        if (data.items.length <= 1) return;
        const updatedItems = data.items.filter((_, i) => i !== index);
        setData('items', updatedItems);
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        post(route('gestor.assets.store'));
    };

    return (
        <AuthenticatedLayout title="Registrar Activos">
            <Head title="Registrar Activos" />

            <div className="min-h-screen bg-[#11111d] text-white p-4 sm:p-8">
                <div className="max-w-4xl mx-auto space-y-8">
                    
                    {/* HEADER */}
                    <div className="flex items-center justify-between bg-[#16162a] p-6 rounded-[2rem] border border-white/5 shadow-2xl">
                        <div>
                            <span className="text-[10px] font-black text-orange-500 uppercase tracking-[0.3em] italic">Alta de Equipamiento</span>
                            <h1 className="text-xl font-black text-white uppercase italic">Registrar Nuevos Activos</h1>
                        </div>
                        <Link
                            href={route('gestor.assets.index')}
                            className="px-4 py-2 bg-white/5 border border-white/10 rounded-xl text-xs font-bold text-white/70 hover:bg-white/10 transition-all"
                        >
                            Volver al Listado
                        </Link>
                    </div>

                    {/* FORMULARIO PRINCIPAL */}
                    <form onSubmit={handleSubmit} className="space-y-6">
                        
                        {/* SECCIÓN GENERAL: EMPRESA, SEDE Y TIPO */}
                        <div className="bg-[#16162a] p-8 rounded-[2.5rem] border border-white/5 shadow-2xl space-y-6">
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

                            {/* TIPO DE ACTIVO */}
                            <div>
                                <label className="block text-[10px] font-black text-white/40 uppercase tracking-widest mb-2 italic">Tipo de Activo *</label>
                                <select
                                    value={data.asset_type_id}
                                    onChange={e => setData('asset_type_id', e.target.value)}
                                    className="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-orange-500"
                                    required
                                >
                                    <option value="">Seleccione Tipo de Activo...</option>
                                    {assetTypes.map(type => (
                                        <option key={type.id} value={type.id}>{type.name}</option>
                                    ))}
                                </select>
                                {errors.asset_type_id && <p className="text-xs text-red-400 mt-1">{errors.asset_type_id}</p>}
                            </div>
                        </div>

                        {/* LISTADO DINÁMICO DE ACTIVOS (CADA UNO INDIVIDUAL PERO CARGADOS EN MISMO PASO) */}
                        <div className="space-y-6">
                            {data.items.map((item, index) => (
                                <div key={index} className="bg-[#16162a] p-8 rounded-[2.5rem] border border-white/5 shadow-2xl space-y-6 relative group">
                                    
                                    {/* CABECERA DEL ACTIVO N° X */}
                                    <div className="flex items-center justify-between border-b border-white/5 pb-4">
                                        <div className="flex items-center gap-2">
                                            <span className="px-3 py-1 bg-orange-500/10 border border-orange-500/30 text-orange-400 rounded-xl text-xs font-black uppercase italic">
                                                Activo #{index + 1}
                                            </span>
                                            {item.identifier && (
                                                <span className="text-xs font-mono text-orange-300">({item.identifier})</span>
                                            )}
                                        </div>

                                        {data.items.length > 1 && (
                                            <button
                                                type="button"
                                                onClick={() => removeItem(index)}
                                                className="px-3 py-1.5 bg-red-500/10 text-red-400 border border-red-500/20 rounded-xl text-xs font-bold hover:bg-red-500 hover:text-white transition-all flex items-center gap-1"
                                            >
                                                <svg className="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                Quitar
                                            </button>
                                        )}
                                    </div>

                                    {/* NOMBRE E IDENTIFICADOR / QR */}
                                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                        {/* NOMBRE */}
                                        <div>
                                            <label className="block text-[10px] font-black text-white/40 uppercase tracking-widest mb-2 italic">Nombre del Activo *</label>
                                            <input
                                                type="text"
                                                value={item.name}
                                                onChange={e => updateItem(index, 'name', e.target.value)}
                                                placeholder="Ej: Matafuegos o Extintor Pasillo A"
                                                className="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-orange-500"
                                                required
                                            />
                                            {errors[`items.${index}.name`] && (
                                                <p className="text-xs text-red-400 mt-1">{errors[`items.${index}.name`]}</p>
                                            )}
                                        </div>

                                        {/* IDENTIFICADOR / QR */}
                                        <div>
                                            <label className="block text-[10px] font-black text-white/40 uppercase tracking-widest mb-2 italic">Identificador / Código QR *</label>
                                            <input
                                                type="text"
                                                value={item.identifier}
                                                onChange={e => updateItem(index, 'identifier', e.target.value)}
                                                placeholder="Ej: MF001"
                                                className={`w-full bg-black/40 border rounded-xl px-4 py-3 text-sm font-mono focus:outline-none transition-all ${
                                                    isDuplicateItem(item.identifier)
                                                        ? 'border-red-500 text-red-400 bg-red-500/10'
                                                        : 'border-white/10 text-orange-400 focus:border-orange-500'
                                                }`}
                                                required
                                            />
                                            {isDuplicateItem(item.identifier) && (
                                                <p className="text-xs font-bold text-red-400 mt-1 flex items-center gap-1">
                                                    ⚠️ Identificador repetido en este lote.
                                                </p>
                                            )}
                                            {errors[`items.${index}.identifier`] && (
                                                <p className="text-xs text-red-400 mt-1">{errors[`items.${index}.identifier`]}</p>
                                            )}
                                        </div>
                                    </div>

                                    {/* ESTADO INICIAL */}
                                    <div>
                                        <label className="block text-[10px] font-black text-white/40 uppercase tracking-widest mb-2 italic">Estado Inicial *</label>
                                        <div className="flex gap-4">
                                            <label className="flex items-center gap-2 cursor-pointer">
                                                <input
                                                    type="radio"
                                                    name={`status_${index}`}
                                                    value="operativo"
                                                    checked={item.status === 'operativo'}
                                                    onChange={e => updateItem(index, 'status', e.target.value)}
                                                    className="text-emerald-500"
                                                />
                                                <span className="text-xs font-bold text-emerald-400">Operativo</span>
                                            </label>
                                            <label className="flex items-center gap-2 cursor-pointer">
                                                <input
                                                    type="radio"
                                                    name={`status_${index}`}
                                                    value="alerta"
                                                    checked={item.status === 'alerta'}
                                                    onChange={e => updateItem(index, 'status', e.target.value)}
                                                    className="text-amber-500"
                                                />
                                                <span className="text-xs font-bold text-amber-400">En Alerta</span>
                                            </label>
                                            <label className="flex items-center gap-2 cursor-pointer">
                                                <input
                                                    type="radio"
                                                    name={`status_${index}`}
                                                    value="fuera_de_servicio"
                                                    checked={item.status === 'fuera_de_servicio'}
                                                    onChange={e => updateItem(index, 'status', e.target.value)}
                                                    className="text-red-500"
                                                />
                                                <span className="text-xs font-bold text-red-400">Fuera de Servicio</span>
                                            </label>
                                        </div>
                                    </div>

                                    {/* METADATA DINÁMICA DE ALTA (SI EL TIPO TIENE CAMPOS MARCADOS PARA ALTA) */}
                                    {createFields.length > 0 && (
                                        <div className="p-6 bg-black/30 border border-white/10 rounded-2xl space-y-4">
                                            <div className="flex items-center justify-between border-b border-white/5 pb-2">
                                                <h3 className="text-xs font-black text-orange-400 uppercase tracking-wider italic">
                                                    Datos de Identificación del Activo ({selectedType?.name})
                                                </h3>
                                                <span className="text-[10px] text-white/30">Campos de Alta</span>
                                            </div>
                                            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                                {createFields.map((field) => (
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
                                                            onChange={e => updateItemMetadata(index, field.name, e.target.value)}
                                                            className="w-full bg-black/50 border border-white/10 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-orange-500"
                                                        />
                                                    </div>
                                                ))}
                                            </div>
                                        </div>
                                    )}
                                </div>
                            ))}
                        </div>

                        {/* BOTÓN AGREGAR OTRO ACTIVO & BOTÓN DE GUARDADO */}
                        <div className="flex flex-col sm:flex-row items-center justify-between gap-4 pt-4">
                            <button
                                type="button"
                                onClick={addItem}
                                className="w-full sm:w-auto px-6 py-3 bg-[#16162a] border border-orange-500/30 text-orange-400 hover:bg-orange-500 hover:text-white font-black text-xs uppercase tracking-widest rounded-xl shadow-lg transition-all active:scale-95 flex items-center justify-center gap-2"
                            >
                                <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2.5" d="M12 4v16m8-8H4"/></svg>
                                + Agregar Otro Activo
                            </button>

                            <div className="flex flex-col items-end gap-1 w-full sm:w-auto">
                                <button
                                    type="submit"
                                    disabled={processing || duplicateIdentifiers.length > 0}
                                    className="w-full sm:w-auto px-10 py-3.5 bg-gradient-to-r from-orange-500 to-amber-600 text-white font-black text-xs uppercase tracking-widest rounded-xl shadow-lg hover:brightness-110 transition-all active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed"
                                >
                                    {processing
                                        ? 'Guardando...'
                                        : data.items.length > 1
                                            ? `Guardar ${data.items.length} Activos`
                                            : 'Guardar Activo'}
                                </button>
                                {duplicateIdentifiers.length > 0 && (
                                    <span className="text-[10px] text-red-400 font-bold">
                                        ⚠️ Hay identificadores repetidos en el formulario.
                                    </span>
                                )}
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
