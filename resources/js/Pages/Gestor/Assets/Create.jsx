import { Head, useForm, Link } from '@inertiajs/react';
import AuthenticatedLayout from '../../../Layouts/AuthenticatedLayout';
import { useMemo, useState, useEffect } from 'react';
import axios from 'axios';
import AssetItemFormCard from './Partials/AssetItemFormCard';
import ExistingAssetsSidebar from './Partials/ExistingAssetsSidebar';

const IDENTIFIER_FIELD_NAMES = new Set([
    'nro_interno', 'nro_interno_extintor', 'identificacion', 'identificador', 'codigo', 'nro_extintor',
]);

export default function Create({ empresas = [], sedes = [], assetTypes = [], prefilled = {} }) {
    const { data, setData, post, processing, errors } = useForm({
        empresa_id: prefilled.empresa_id ? prefilled.empresa_id.toString() : '',
        sede_id: prefilled.sede_id ? prefilled.sede_id.toString() : '',
        asset_type_id: prefilled.asset_type_id ? prefilled.asset_type_id.toString() : '',
        items: [{ name: '', identifier: '', status: 'operativo', grupo: '', subgrupo: '', metadata: {} }],
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
        if (!editNombre.trim()) { alert('El nombre del activo es obligatorio.'); return; }
        if (!editIdentificador.trim()) { alert('El identificador/QR es obligatorio.'); return; }

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
        }, { headers: { Accept: 'application/json' } })
            .then(() => {
                setExistingAssets(prev => prev.map(a => a.id === assetId ? {
                    ...a, name: editNombre.trim(), identifier: editIdentificador.trim(), grupo: editGrupo, subgrupo: editSubgrupo,
                } : a));
                setEditingAssetId(null);
            })
            .catch(err => {
                const msg = err.response?.data?.errors
                    ? Object.values(err.response.data.errors).flat().join('\n')
                    : (err.response?.data?.message || 'Error al guardar el grupo/subgrupo del activo.');
                alert(`Error al guardar:\n${msg}`);
            })
            .finally(() => setSavingAssetId(null));
    };

    useEffect(() => {
        if (data.empresa_id && data.sede_id && data.asset_type_id) {
            setLoadingExisting(true);
            axios.get(route('gestor.assets.index'), {
                params: { empresa_id: data.empresa_id, sede_id: data.sede_id, asset_type_id: data.asset_type_id, json: true },
            })
                .then(res => setExistingAssets(res.data || []))
                .catch(console.error)
                .finally(() => setLoadingExisting(false));
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
            const sgName = asset.subgrupo || 'General';
            if (!groups[gName]) groups[gName] = {};
            if (!groups[gName][sgName]) groups[gName][sgName] = [];
            groups[gName][sgName].push(asset);
        });
        return groups;
    }, [filteredExistingAssets]);

    const existingGroups = useMemo(() => Array.from(new Set(existingAssets.map(a => a.grupo).filter(Boolean))).sort(), [existingAssets]);
    const existingSubgroups = useMemo(() => Array.from(new Set(existingAssets.map(a => a.subgrupo).filter(Boolean))).sort(), [existingAssets]);
    const selectedType = useMemo(() => assetTypes.find(t => t.id.toString() === data.asset_type_id.toString()), [data.asset_type_id, assetTypes]);
    const availableSedes = useMemo(() => (data.empresa_id ? sedes.filter(s => s.empresa_id.toString() === data.empresa_id.toString()) : sedes), [data.empresa_id, sedes]);

    const createFields = useMemo(() => {
        if (!selectedType?.metadata_schema) return [];
        return selectedType.metadata_schema.filter(field => {
            if (!field.show_in_create) return false;
            const nameLower = (field.name || '').toLowerCase();
            const labelLower = (field.label || '').toLowerCase();
            if (IDENTIFIER_FIELD_NAMES.has(nameLower)) return false;
            if (labelLower.includes('identificación') || labelLower.includes('n° interno') || labelLower.includes('numero interno') || labelLower === 'código' || labelLower === 'codigo') {
                return false;
            }
            return true;
        });
    }, [selectedType]);

    const duplicateIdentifiers = useMemo(() => {
        const counts = {};
        data.items.forEach(item => {
            const trimmed = (item.identifier || '').trim().toLowerCase();
            if (trimmed) counts[trimmed] = (counts[trimmed] || 0) + 1;
        });
        return Object.keys(counts).filter(id => counts[id] > 1);
    }, [data.items]);

    const isDuplicateItem = (identifier) => {
        const trimmed = (identifier || '').trim().toLowerCase();
        return trimmed && duplicateIdentifiers.includes(trimmed);
    };

    const updateItem = (index, field, value) => {
        const updatedItems = [...data.items];
        updatedItems[index] = { ...updatedItems[index], [field]: value };
        setData('items', updatedItems);
    };

    const updateItemMetadata = (index, key, value) => {
        const updatedItems = [...data.items];
        updatedItems[index] = { ...updatedItems[index], metadata: { ...(updatedItems[index].metadata || {}), [key]: value } };
        setData('items', updatedItems);
    };

    const normalizeValueOnBlur = (index, field, list, value) => {
        const trimmed = (value || '').trim();
        const match = list.find(g => g.toLowerCase() === trimmed.toLowerCase());
        updateItem(index, field, match || trimmed);
    };

    const autoIncrementIdentifier = (lastId) => {
        if (!lastId) return '';
        const match = lastId.match(/^(.*?)(\d+)$/);
        if (match) return `${match[1]}${(parseInt(match[2], 10) + 1).toString().padStart(match[2].length, '0')}`;
        return `${lastId}_2`;
    };

    const addItem = () => {
        const lastItem = data.items[data.items.length - 1];
        setData('items', [...data.items, {
            name: '',
            identifier: autoIncrementIdentifier(lastItem?.identifier || ''),
            status: lastItem?.status || 'operativo',
            grupo: lastItem?.grupo || '',
            subgrupo: lastItem?.subgrupo || '',
            metadata: {},
        }]);
    };

    const removeItem = (index) => {
        if (data.items.length <= 1) return;
        setData('items', data.items.filter((_, i) => i !== index));
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
                    <div className="flex items-center justify-between bg-[#16162a] p-6 rounded-[2rem] border border-white/5 shadow-2xl">
                        <div>
                            <span className="text-[10px] font-black text-orange-500 uppercase tracking-[0.3em] italic">Alta de Equipamiento</span>
                            <h1 className="text-xl font-black text-white uppercase italic">Registrar Nuevos Activos</h1>
                        </div>
                        <Link href={route('gestor.assets.index')} className="px-4 py-2 bg-white/5 border border-white/10 rounded-xl text-xs font-bold text-white/70 hover:bg-white/10 transition-all">
                            Volver al Listado
                        </Link>
                    </div>

                    <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        <div className="lg:col-span-2 space-y-6">
                            <form onSubmit={handleSubmit} className="space-y-6">
                                <div className="bg-[#16162a] p-8 rounded-[2.5rem] border border-white/5 shadow-2xl space-y-6">
                                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                        <div>
                                            <label className="block text-[10px] font-black text-white/40 uppercase tracking-widest mb-2 italic">Empresa *</label>
                                            <select
                                                value={data.empresa_id}
                                                onChange={e => setData(d => ({ ...d, empresa_id: e.target.value, sede_id: '' }))}
                                                className="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-orange-500"
                                                required
                                            >
                                                <option value="">Seleccione Empresa...</option>
                                                {empresas.map(emp => <option key={emp.id} value={emp.id}>{emp.nombre}</option>)}
                                            </select>
                                            {errors.empresa_id && <p className="text-xs text-red-400 mt-1">{errors.empresa_id}</p>}
                                        </div>

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
                                                {availableSedes.map(s => <option key={s.id} value={s.id}>{s.nombre_sede}</option>)}
                                            </select>
                                            {errors.sede_id && <p className="text-xs text-red-400 mt-1">{errors.sede_id}</p>}
                                        </div>
                                    </div>

                                    <div>
                                        <label className="block text-[10px] font-black text-white/40 uppercase tracking-widest mb-2 italic">Tipo de Activo *</label>
                                        <select
                                            value={data.asset_type_id}
                                            onChange={e => setData('asset_type_id', e.target.value)}
                                            className="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-orange-500"
                                            required
                                        >
                                            <option value="">Seleccione Tipo de Activo...</option>
                                            {assetTypes.map(type => <option key={type.id} value={type.id}>{type.name}</option>)}
                                        </select>
                                        {errors.asset_type_id && <p className="text-xs text-red-400 mt-1">{errors.asset_type_id}</p>}
                                    </div>
                                </div>

                                <div className="space-y-6">
                                    {data.items.map((item, index) => (
                                        <AssetItemFormCard
                                            key={index}
                                            item={item}
                                            index={index}
                                            totalItems={data.items.length}
                                            selectedType={selectedType}
                                            existingGroups={existingGroups}
                                            existingSubgroups={existingSubgroups}
                                            createFields={createFields}
                                            isDuplicate={isDuplicateItem(item.identifier)}
                                            errors={errors}
                                            onUpdateItem={updateItem}
                                            onUpdateMetadata={updateItemMetadata}
                                            onNormalizeGrupo={(i, v) => normalizeValueOnBlur(i, 'grupo', existingGroups, v)}
                                            onNormalizeSubgrupo={(i, v) => normalizeValueOnBlur(i, 'subgrupo', existingSubgroups, v)}
                                            onRemove={removeItem}
                                        />
                                    ))}
                                </div>

                                <div className="flex flex-col sm:flex-row items-center justify-between gap-4 pt-4">
                                    <button type="button" onClick={addItem} className="w-full sm:w-auto px-6 py-3 bg-[#16162a] border border-orange-500/30 text-orange-400 hover:bg-orange-500 hover:text-white font-black text-xs uppercase tracking-widest rounded-xl shadow-lg transition-all active:scale-95 flex items-center justify-center gap-2">
                                        + Agregar Otro Activo
                                    </button>
                                    <div className="flex flex-col items-end gap-1 w-full sm:w-auto">
                                        <button type="submit" disabled={processing || duplicateIdentifiers.length > 0} className="w-full sm:w-auto px-10 py-3.5 bg-gradient-to-r from-orange-500 to-amber-600 text-white font-black text-xs uppercase tracking-widest rounded-xl shadow-lg hover:brightness-110 transition-all active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed">
                                            {processing ? 'Guardando...' : data.items.length > 1 ? `Guardar ${data.items.length} Activos` : 'Guardar Activo'}
                                        </button>
                                        {duplicateIdentifiers.length > 0 && <span className="text-[10px] text-red-400 font-bold">⚠️ Hay identificadores repetidos en el formulario.</span>}
                                    </div>
                                </div>
                            </form>
                        </div>

                        <ExistingAssetsSidebar
                            hasSelection={Boolean(data.empresa_id && data.sede_id && data.asset_type_id)}
                            localSearch={localSearch}
                            setLocalSearch={setLocalSearch}
                            loadingExisting={loadingExisting}
                            existingAssets={existingAssets}
                            filteredExistingAssets={filteredExistingAssets}
                            groupedSidebarAssets={groupedSidebarAssets}
                            selectedType={selectedType}
                            editingAssetId={editingAssetId}
                            setEditingAssetId={setEditingAssetId}
                            editNombre={editNombre}
                            setEditNombre={setEditNombre}
                            editIdentificador={editIdentificador}
                            setEditIdentificador={setEditIdentificador}
                            editGrupo={editGrupo}
                            setEditGrupo={setEditGrupo}
                            editSubgrupo={editSubgrupo}
                            setEditSubgrupo={setEditSubgrupo}
                            savingAssetId={savingAssetId}
                            onSaveInline={handleSaveInline}
                        />
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
