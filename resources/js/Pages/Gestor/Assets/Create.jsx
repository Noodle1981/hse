import { Head, useForm, Link } from '@inertiajs/react';
import AuthenticatedLayout from '../../../Layouts/AuthenticatedLayout';
import { useMemo, useState, useEffect } from 'react';
import axios from 'axios';

export default function Create({ empresas = [], sedes = [], assetTypes = [], prefilled = {} }) {
    const { data, setData, post, processing, errors } = useForm({
        empresa_id: prefilled.empresa_id ? prefilled.empresa_id.toString() : '',
        sede_id: prefilled.sede_id ? prefilled.sede_id.toString() : '',
        asset_type_id: prefilled.asset_type_id ? prefilled.asset_type_id.toString() : '',
        items: [
            {
                name: '',
                identifier: '',
                status: 'operativo',
                grupo: '',
                subgrupo: '',
                metadata: {},
            },
        ],
    });

    const [existingAssets, setExistingAssets] = useState([]);
    const [loadingExisting, setLoadingExisting] = useState(false);
    const [localSearch, setLocalSearch] = useState('');

    const [editingAssetId, setEditingAssetId] = useState(null);
    const [editNombre, setEditNombre] = useState('');
    const [editIdentificador, setEditIdentificador] = useState('');
    const [editGrupo, setEditGrupo] = useState('');
    const [editSubgrupo, setEditSubgrupo] = useState('');
    const [savingAssetId, setSavingAssetId] = useState(null);

    const handleSaveInline = (assetId) => {
        const assetObj = existingAssets.find(a => a.id === assetId);
        if (!assetObj) return;

        if (!editNombre.trim()) {
            alert('El nombre del activo es obligatorio.');
            return;
        }
        if (!editIdentificador.trim()) {
            alert('El identificador/QR es obligatorio.');
            return;
        }

        setSavingAssetId(assetId);
        axios.put(route('gestor.assets.update', assetId), {
            empresa_id: assetObj.empresa_id,
            sede_id: assetObj.sede_id,
            asset_type_id: assetObj.asset_type_id,
            name: editNombre.trim(),
            identifier: editIdentificador.trim(),
            status: assetObj.status,
            grupo: editGrupo,
            subgrupo: editSubgrupo,
            metadata: assetObj.metadata || {},
        }, {
            headers: {
                'Accept': 'application/json'
            }
        })
        .then(res => {
            setExistingAssets(prev => prev.map(a => a.id === assetId ? { 
                ...a, 
                name: editNombre.trim(),
                identifier: editIdentificador.trim(),
                grupo: editGrupo, 
                subgrupo: editSubgrupo 
            } : a));
            setEditingAssetId(null);
        })
        .catch(err => {
            console.error(err);
            if (err.response && err.response.data && err.response.data.errors) {
                const msgs = Object.values(err.response.data.errors).flat().join('\n');
                alert(`Error al guardar:\n${msgs}`);
            } else if (err.response && err.response.data && err.response.data.message) {
                alert(`Error al guardar:\n${err.response.data.message}`);
            } else {
                alert('Error al guardar el grupo/subgrupo del activo.');
            }
        })
        .finally(() => {
            setSavingAssetId(null);
        });
    };

    useEffect(() => {
        if (data.empresa_id && data.sede_id && data.asset_type_id) {
            setLoadingExisting(true);
            axios.get(route('gestor.assets.index'), {
                params: {
                    empresa_id: data.empresa_id,
                    sede_id: data.sede_id,
                    asset_type_id: data.asset_type_id,
                    json: true
                }
            })
            .then(res => {
                setExistingAssets(res.data || []);
            })
            .catch(err => {
                console.error(err);
            })
            .finally(() => {
                setLoadingExisting(false);
            });
        } else {
            setExistingAssets([]);
        }
    }, [data.empresa_id, data.sede_id, data.asset_type_id]);

    const filteredExistingAssets = useMemo(() => {
        if (!localSearch) return existingAssets;
        const s = localSearch.toLowerCase();
        return existingAssets.filter(asset => 
            (asset.name && asset.name.toLowerCase().includes(s)) ||
            (asset.identifier && asset.identifier.toLowerCase().includes(s))
        );
    }, [existingAssets, localSearch]);

    const groupedSidebarAssets = useMemo(() => {
        const groups = {};
        filteredExistingAssets.forEach(asset => {
            const gName = asset.grupo || 'Sin Clasificar';
            if (!groups[gName]) {
                groups[gName] = {};
            }
            const sgName = asset.subgrupo || 'General';
            if (!groups[gName][sgName]) {
                groups[gName][sgName] = [];
            }
            groups[gName][sgName].push(asset);
        });
        return groups;
    }, [filteredExistingAssets]);

    const existingGroups = useMemo(() => {
        const set = new Set();
        existingAssets.forEach(a => {
            if (a.grupo) set.add(a.grupo);
        });
        return Array.from(set).sort();
    }, [existingAssets]);

    const existingSubgroups = useMemo(() => {
        const set = new Set();
        existingAssets.forEach(a => {
            if (a.subgrupo) set.add(a.subgrupo);
        });
        return Array.from(set).sort();
    }, [existingAssets]);

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
        return selectedType.metadata_schema.filter(field => {
            if (!field.show_in_create) return false;
            
            const nameLower = (field.name || '').toLowerCase();
            const labelLower = (field.label || '').toLowerCase();
            if (
                nameLower === 'nro_interno' || 
                nameLower === 'nro_interno_extintor' ||
                nameLower === 'identificacion' ||
                nameLower === 'identificador' ||
                nameLower === 'codigo' ||
                nameLower === 'nro_extintor' ||
                labelLower.includes('identificación') ||
                labelLower.includes('n° interno') ||
                labelLower.includes('numero interno') ||
                labelLower === 'código' ||
                labelLower === 'codigo'
            ) {
                return false;
            }
            return true;
        });
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

    const normalizeGrupoOnBlur = (index, value) => {
        const trimmed = (value || '').trim();
        const match = existingGroups.find(g => g.toLowerCase() === trimmed.toLowerCase());
        if (match) {
            updateItem(index, 'grupo', match);
        } else {
            updateItem(index, 'grupo', trimmed);
        }
    };

    const normalizeSubgrupoOnBlur = (index, value) => {
        const trimmed = (value || '').trim();
        const match = existingSubgroups.find(sg => sg.toLowerCase() === trimmed.toLowerCase());
        if (match) {
            updateItem(index, 'subgrupo', match);
        } else {
            updateItem(index, 'subgrupo', trimmed);
        }
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

        setData('items', [
            ...data.items,
            {
                name: '',
                identifier: nextId,
                status: lastItem?.status || 'operativo',
                grupo: lastItem?.grupo || '',
                subgrupo: lastItem?.subgrupo || '',
                metadata: {},
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
                <div className="max-w-7xl mx-auto space-y-8">
                    
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

                    {/* TWO-COLUMN GRID */}
                    <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        {/* FORMULARIO PRINCIPAL (Left - 2 Cols) */}
                        <div className="lg:col-span-2 space-y-6">
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

                                            {/* GRUPO Y SUBGRUPO DE AGRUPACIÓN (FLOTA/PISO) */}
                                            <div className="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                                {/* GRUPO */}
                                                <div>
                                                    <label className="block text-[10px] font-black text-white/40 uppercase tracking-widest mb-2 italic">
                                                        {selectedType?.group_label || 'Grupo'}
                                                    </label>
                                                    <input
                                                        type="text"
                                                        list={`existing-groups-${index}`}
                                                        value={item.grupo || ''}
                                                        onChange={e => updateItem(index, 'grupo', e.target.value)}
                                                        onBlur={e => normalizeGrupoOnBlur(index, e.target.value)}
                                                        placeholder={`Ej: ${selectedType?.group_label || 'Piso 1, Flota A'}`}
                                                        className="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-orange-500"
                                                    />
                                                    <datalist id={`existing-groups-${index}`}>
                                                        {existingGroups.map(g => (
                                                            <option key={g} value={g} />
                                                        ))}
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
                                                    {errors[`items.${index}.grupo`] && (
                                                        <p className="text-xs text-red-400 mt-1">{errors[`items.${index}.grupo`]}</p>
                                                    )}
                                                </div>

                                                {/* SUBGRUPO */}
                                                <div>
                                                    <label className="block text-[10px] font-black text-white/40 uppercase tracking-widest mb-2 italic">
                                                        {selectedType?.subgroup_label || 'Subgrupo'}
                                                    </label>
                                                    <input
                                                        type="text"
                                                        list={`existing-subgroups-${index}`}
                                                        value={item.subgrupo || ''}
                                                        onChange={e => updateItem(index, 'subgrupo', e.target.value)}
                                                        onBlur={e => normalizeSubgrupoOnBlur(index, e.target.value)}
                                                        placeholder={`Ej: ${selectedType?.subgroup_label || 'Sector Norte, Oficina'}`}
                                                        className="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-orange-500"
                                                    />
                                                    <datalist id={`existing-subgroups-${index}`}>
                                                        {existingSubgroups.map(sg => (
                                                            <option key={sg} value={sg} />
                                                        ))}
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
                                                    {errors[`items.${index}.subgrupo`] && (
                                                        <p className="text-xs text-red-400 mt-1">{errors[`items.${index}.subgrupo`]}</p>
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

                        {/* LISTADO DE ACTIVOS EXISTENTES (Right - 1 Col) */}
                        <div className="space-y-6">
                            <div className="bg-[#16162a] p-6 rounded-[2rem] border border-white/5 shadow-2xl space-y-4">
                                <div>
                                    <span className="text-[9px] font-black text-orange-500 uppercase tracking-widest italic block">Base de Datos de Activos</span>
                                    <h2 className="text-md font-black text-white uppercase italic">Equipos Registrados</h2>
                                </div>

                                {data.empresa_id && data.sede_id && data.asset_type_id ? (
                                    <>
                                        {/* Barra de búsqueda local */}
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
                                                {existingAssets.length === 0 
                                                    ? 'No hay activos registrados para esta selección.' 
                                                    : 'No se encontraron coincidencias.'}
                                            </div>
                                        ) : (
                                            <div className="max-h-[500px] overflow-y-auto pr-1 space-y-4 custom-scrollbar">
                                                {Object.entries(groupedSidebarAssets).map(([gName, subgroups]) => (
                                                    <div key={gName} className="space-y-2">
                                                        {/* Cabecera del Grupo L1 */}
                                                        <div className="text-[10px] font-black text-orange-400 uppercase tracking-widest italic flex items-center gap-1.5 pl-1 select-none">
                                                            📁 {selectedType?.group_label || 'Grupo'}: {gName}
                                                        </div>

                                                        {/* Contenedor del Grupo (Recuadro) */}
                                                        <div className="bg-black/25 rounded-2xl border border-white/5 p-3 space-y-3">
                                                            {Object.entries(subgroups).map(([sgName, subAssets]) => (
                                                                <div key={sgName} className="space-y-1.5">
                                                                    {/* Cabecera del Subgrupo L2 */}
                                                                    <div className="text-[9px] font-black text-indigo-300/80 uppercase tracking-wide italic flex items-center gap-1 pl-1 select-none">
                                                                        📍 {selectedType?.subgroup_label || 'Subgrupo'}: {sgName}
                                                                    </div>

                                                                    {/* Lista de Activos */}
                                                                    <div className="space-y-1.5 pl-2 border-l border-white/5">
                                                                        {subAssets.map(asset => {
                                                                            const isEditing = editingAssetId === asset.id;
                                                                            const isSaving = savingAssetId === asset.id;

                                                                            return (
                                                                                <div 
                                                                                    key={asset.id} 
                                                                                    className={`p-2.5 rounded-xl border transition-all ${
                                                                                        isEditing 
                                                                                            ? 'bg-orange-500/5 border-orange-500/25 shadow-lg' 
                                                                                            : 'bg-black/20 hover:bg-black/30 border-white/5'
                                                                                    }`}
                                                                                >
                                                                                    {isEditing ? (
                                                                                        <div className="space-y-3">
                                                                                            <div className="text-[10px] font-bold text-orange-400 uppercase italic">
                                                                                                Editar Activo
                                                                                            </div>
                                                                                            <div className="space-y-2">
                                                                                                <div>
                                                                                                    <label className="block text-[8px] font-black text-white/40 uppercase mb-1">
                                                                                                        Nombre del Activo *
                                                                                                    </label>
                                                                                                    <input
                                                                                                        type="text"
                                                                                                        value={editNombre}
                                                                                                        onChange={e => setEditNombre(e.target.value)}
                                                                                                        placeholder="Ej: Extintor 01"
                                                                                                        className="w-full bg-black/60 border border-white/10 rounded-lg px-2 py-1 text-[11px] text-white focus:outline-none focus:border-orange-500"
                                                                                                        required
                                                                                                    />
                                                                                                </div>
                                                                                                <div>
                                                                                                    <label className="block text-[8px] font-black text-white/40 uppercase mb-1">
                                                                                                        Identificador / Código QR *
                                                                                                    </label>
                                                                                                    <input
                                                                                                        type="text"
                                                                                                        value={editIdentificador}
                                                                                                        onChange={e => setEditIdentificador(e.target.value)}
                                                                                                        placeholder="Ej: EXT-001"
                                                                                                        className="w-full bg-black/60 border border-white/10 rounded-lg px-2 py-1 text-[11px] text-white focus:outline-none focus:border-orange-500"
                                                                                                        required
                                                                                                    />
                                                                                                </div>
                                                                                            </div>
                                                                                            <div className="grid grid-cols-2 gap-2">
                                                                                                <div>
                                                                                                    <label className="block text-[8px] font-black text-white/40 uppercase mb-1">
                                                                                                        {selectedType?.group_label || 'Grupo'}
                                                                                                    </label>
                                                                                                    <input
                                                                                                        type="text"
                                                                                                        value={editGrupo}
                                                                                                        onChange={e => setEditGrupo(e.target.value)}
                                                                                                        placeholder="Ej: Piso 1"
                                                                                                        className="w-full bg-black/60 border border-white/10 rounded-lg px-2 py-1 text-[11px] text-white focus:outline-none focus:border-orange-500"
                                                                                                    />
                                                                                                </div>
                                                                                                <div>
                                                                                                    <label className="block text-[8px] font-black text-white/40 uppercase mb-1">
                                                                                                        {selectedType?.subgroup_label || 'Subgrupo'}
                                                                                                    </label>
                                                                                                    <input
                                                                                                        type="text"
                                                                                                        value={editSubgrupo}
                                                                                                        onChange={e => setEditSubgrupo(e.target.value)}
                                                                                                        placeholder="Ej: Sector A"
                                                                                                        className="w-full bg-black/60 border border-white/10 rounded-lg px-2 py-1 text-[11px] text-white focus:outline-none focus:border-orange-500"
                                                                                                    />
                                                                                                </div>
                                                                                            </div>
                                                                                            <div className="flex justify-end gap-2 pt-2 border-t border-white/5">
                                                                                                <button
                                                                                                    type="button"
                                                                                                    onClick={() => setEditingAssetId(null)}
                                                                                                    disabled={isSaving}
                                                                                                    className="px-2.5 py-1 bg-white/5 hover:bg-white/10 rounded-lg text-[9px] font-bold uppercase transition-colors"
                                                                                                >
                                                                                                    Cancelar
                                                                                                </button>
                                                                                                <button
                                                                                                    type="button"
                                                                                                    onClick={() => handleSaveInline(asset.id)}
                                                                                                    disabled={isSaving}
                                                                                                    className="px-3 py-1 bg-orange-500 hover:bg-orange-600 text-white rounded-lg text-[9px] font-black uppercase transition-colors"
                                                                                                >
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
                                                                                                    asset.status === 'operativo' 
                                                                                                        ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' 
                                                                                                        : asset.status === 'alerta'
                                                                                                            ? 'bg-amber-500/10 text-amber-400 border border-amber-500/20'
                                                                                                            : 'bg-red-500/10 text-red-400 border border-red-500/20'
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
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
