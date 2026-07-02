import { Head, Link, useForm, usePage, router } from '@inertiajs/react';
import AuthenticatedLayout from '../../../../Layouts/AuthenticatedLayout';
import { useState, useMemo } from 'react';

export default function Index({ assetTypes = [], categories = [], empresas = [], checklists = [] }) {
    const { flash } = usePage().props;
    const [editingType, setEditingType] = useState(null);
    const [showModal, setShowModal] = useState(false);
    const [importing, setImporting] = useState(false);
    
    // Filtros de categoría y subcategoría para importar desde checklist
    const [filterCategoryId, setFilterCategoryId] = useState('');
    const [filterSubcategoryId, setFilterSubcategoryId] = useState('');
    const [importChecklistId, setImportChecklistId] = useState('');
    const [importError, setImportError] = useState('');

    const form = useForm({
        id: null,
        name: '',
        empresa_id: '',
        category_id: '',
        description: '',
        metadata_schema: [],
    });

    const openCreateModal = () => {
        setEditingType(null);
        setFilterCategoryId('');
        setFilterSubcategoryId('');
        setImportChecklistId('');
        setImportError('');
        form.setData({
            id: null,
            name: '',
            empresa_id: '',
            category_id: '',
            description: '',
            metadata_schema: [],
        });
        setShowModal(true);
    };

    const openEditModal = (type) => {
        setEditingType(type);
        setFilterCategoryId('');
        setFilterSubcategoryId('');
        setImportChecklistId('');
        setImportError('');
        form.setData({
            id: type.id,
            name: type.name || '',
            empresa_id: type.empresa_id || '',
            category_id: type.category_id || '',
            description: type.description || '',
            metadata_schema: type.metadata_schema || [],
        });
        setShowModal(true);
    };

    // Subcategorías disponibles según la categoría seleccionada en el filtro
    const availableSubcategories = useMemo(() => {
        if (!filterCategoryId) return [];
        const cat = categories.find(c => String(c.id) === String(filterCategoryId));
        return cat?.subcategories || [];
    }, [filterCategoryId, categories]);

    // Checklists filtrados en cascada por Categoría y Subcategoría
    const filteredChecklists = useMemo(() => {
        return checklists.filter(c => {
            if (filterCategoryId && String(c.category_id) !== String(filterCategoryId)) {
                return false;
            }
            if (filterSubcategoryId && String(c.subcategory_id) !== String(filterSubcategoryId)) {
                return false;
            }
            return true;
        });
    }, [checklists, filterCategoryId, filterSubcategoryId]);

    const addField = () => {
        form.setData('metadata_schema', [
            ...form.data.metadata_schema,
            { name: '', label: '', type: 'text', alert_days: '', alert_threshold: '' },
        ]);
    };

    const updateField = (index, key, value) => {
        const updated = [...form.data.metadata_schema];
        updated[index][key] = value;
        form.setData('metadata_schema', updated);
    };

    const removeField = (index) => {
        const updated = [...form.data.metadata_schema];
        updated.splice(index, 1);
        form.setData('metadata_schema', updated);
    };

    /**
     * Llama al endpoint de importación y fusiona los campos retornados con los existentes.
     */
    const handleImportFromChecklist = async () => {
        if (!importChecklistId) {
            setImportError('Seleccioná un checklist primero.');
            return;
        }
        setImportError('');
        setImporting(true);
        try {
            const url = route('gestor.asset-types.import-checklist', importChecklistId);
            const res = await fetch(url, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin',
            });
            if (!res.ok) throw new Error('Error al consultar el checklist.');
            const data = await res.json();

            if (!data.fields || data.fields.length === 0) {
                setImportError('Este checklist no tiene preguntas vinculadas a metadata. Abrí el Builder del checklist y completá el campo "Vincular a Metadata Activo" en cada pregunta relevante.');
                setImporting(false);
                return;
            }

            // Fusionar: solo agregar campos cuya clave no exista ya
            const existingKeys = form.data.metadata_schema.map(f => f.name);
            const newFields = data.fields.filter(f => !existingKeys.includes(f.name));
            const merged = [...form.data.metadata_schema, ...newFields];
            form.setData('metadata_schema', merged);

            if (newFields.length === 0) {
                setImportError(`Todos los campos del checklist ya estaban cargados (${data.fields.length} campos coincidentes).`);
            } else {
                setImportError('');
            }
        } catch (e) {
            setImportError('No se pudo conectar con el servidor. Verificá tu sesión.');
        }
        setImporting(false);
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        form.post(route('gestor.asset-types.store'), {
            onSuccess: () => {
                setShowModal(false);
                form.reset();
            },
        });
    };

    const handleDelete = (type) => {
        if (confirm(`¿Eliminar tipo de activo '${type.name}'?`)) {
            router.delete(route('gestor.asset-types.destroy', type.id));
        }
    };

    return (
        <AuthenticatedLayout title="Tipos de Activos y Atributos">
            <Head title="Tipos de Activos" />

            <div className="min-h-screen bg-[#11111d] text-white p-4 sm:p-8 space-y-8">
                <div className="max-w-7xl mx-auto space-y-8">

                    {/* HEADER */}
                    <div className="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-[#16162a] p-6 rounded-[2rem] border border-white/5 shadow-2xl">
                        <div>
                            <span className="text-[10px] font-black text-orange-500 uppercase tracking-[0.3em] italic">Configuración de Inventario</span>
                            <h1 className="text-2xl font-black text-white uppercase italic">Tipos de Activos y Atributos</h1>
                            <p className="text-xs text-white/40 mt-1">Definí la ficha técnica de cada categoría de activo. Podés importar los atributos desde un checklist ya configurado.</p>
                        </div>
                        <div className="flex gap-3">
                            <button
                                onClick={openCreateModal}
                                className="px-6 py-3 bg-gradient-to-r from-orange-500 to-amber-600 text-white font-black text-xs uppercase tracking-widest rounded-xl shadow-lg hover:brightness-110 transition-all active:scale-95 flex items-center gap-2"
                            >
                                <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2.5" d="M12 4v16m8-8H4"/></svg>
                                Nuevo Tipo de Activo
                            </button>
                            <Link
                                href={route('gestor.assets.index')}
                                className="px-4 py-3 bg-white/5 border border-white/10 text-white/70 font-bold text-xs uppercase tracking-wider rounded-xl hover:bg-white/10 transition-all"
                            >
                                ← Volver
                            </Link>
                        </div>
                    </div>

                    {/* FLASH MESSAGE */}
                    {flash?.message && (
                        <div className="p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl text-emerald-400 text-xs font-bold">
                            {flash.message}
                        </div>
                    )}

                    {/* GRID DE TIPOS */}
                    {assetTypes.length === 0 ? (
                        <div className="text-center py-20 text-white/30">
                            <svg className="w-16 h-16 mx-auto mb-4 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            <p className="text-sm font-bold">No hay tipos de activos configurados aún.</p>
                            <p className="text-xs mt-1">Creá uno y vinculalo a un checklist para importar sus atributos automáticamente.</p>
                        </div>
                    ) : (
                        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            {assetTypes.map(type => (
                                <div key={type.id} className="bg-[#16162a] p-6 rounded-[2rem] border border-white/5 shadow-xl space-y-4 flex flex-col justify-between hover:border-orange-500/20 transition-all">
                                    <div>
                                        <div className="flex items-center justify-between mb-2">
                                            <h3 className="text-base font-black text-white uppercase italic">{type.name}</h3>
                                            <span className="px-2.5 py-1 bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 rounded-lg text-[10px] font-bold">
                                                {type.category?.name || 'General'}
                                            </span>
                                        </div>
                                        {type.description && <p className="text-xs text-white/40 mb-4">{type.description}</p>}

                                        <div className="bg-black/30 p-4 rounded-xl border border-white/5 space-y-2">
                                            <span className="text-[9px] font-black text-orange-400 uppercase tracking-widest block">
                                                Atributos de Ficha Técnica ({type.metadata_schema?.length || 0})
                                            </span>
                                            {type.metadata_schema && type.metadata_schema.length > 0 ? (
                                                <div className="flex flex-wrap gap-1.5">
                                                    {type.metadata_schema.map((f, i) => (
                                                        <span key={i} title={`Clave: ${f.name}`} className="px-2.5 py-1 bg-white/5 border border-white/10 rounded-lg text-[10px] text-gray-300 font-mono cursor-help">
                                                            <strong className="text-white">{f.label || f.name}</strong>
                                                            <span className="ml-1 text-white/30">
                                                                {f.type === 'date'
                                                                    ? (f.alert_days ? '🔔 Vencimiento' : '🗓️ Fecha Info')
                                                                    : (f.type === 'number' ? '🔢' : '📝')}
                                                            </span>
                                                        </span>
                                                    ))}
                                                </div>
                                            ) : (
                                                <p className="text-[10px] text-white/30 italic">Sin atributos definidos.</p>
                                            )}
                                        </div>
                                    </div>

                                    <div className="flex justify-end gap-2 pt-4 border-t border-white/5">
                                        <button
                                            onClick={() => openEditModal(type)}
                                            className="px-3 py-1.5 bg-white/5 text-white/70 border border-white/10 rounded-xl text-[10px] font-black uppercase hover:bg-white/10 transition-all"
                                        >
                                            Editar Atributos
                                        </button>
                                        <button
                                            onClick={() => handleDelete(type)}
                                            className="px-3 py-1.5 bg-red-500/10 text-red-400 border border-red-500/20 rounded-xl text-[10px] font-black uppercase hover:bg-red-500/20 transition-all"
                                        >
                                            Eliminar
                                        </button>
                                    </div>
                                </div>
                            ))}
                        </div>
                    )}

                    {/* ══════════════════════════════════════════ */}
                    {/* MODAL CONFIGURADOR */}
                    {/* ══════════════════════════════════════════ */}
                    {showModal && (
                        <div className="fixed inset-0 bg-black/80 backdrop-blur-md flex items-center justify-center p-4 z-50 overflow-y-auto">
                            <div className="bg-[#16162a] border border-white/10 rounded-[2.5rem] p-8 max-w-3xl w-full space-y-6 shadow-2xl my-8">

                                <div className="flex items-center justify-between border-b border-white/5 pb-4">
                                    <div>
                                        <span className="text-[10px] font-black text-orange-400 uppercase tracking-widest italic">
                                            {editingType ? 'Editar Tipo de Activo' : 'Nuevo Tipo de Activo'}
                                        </span>
                                        <h2 className="text-xl font-black text-white uppercase italic">
                                            {editingType ? editingType.name : 'Configurar Ficha Técnica'}
                                        </h2>
                                    </div>
                                    <button onClick={() => setShowModal(false)} className="text-white/40 hover:text-white font-bold text-lg">✕</button>
                                </div>

                                <form onSubmit={handleSubmit} className="space-y-6">
                                    {/* INFO GENERAL */}
                                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div>
                                            <label className="block text-[10px] font-black text-white/40 uppercase mb-1">Nombre del Tipo *</label>
                                            <input
                                                type="text"
                                                value={form.data.name}
                                                onChange={e => form.setData('name', e.target.value)}
                                                placeholder="Ej: Matafuego, Autoelevador, Botiquín..."
                                                className="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-orange-500"
                                                required
                                            />
                                        </div>
                                        <div>
                                            <label className="block text-[10px] font-black text-white/40 uppercase mb-1">Categoría</label>
                                            <select
                                                value={form.data.category_id}
                                                onChange={e => form.setData('category_id', e.target.value)}
                                                className="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-orange-500"
                                            >
                                                <option value="">Sin categoría</option>
                                                {categories.map(c => <option key={c.id} value={c.id}>{c.name}</option>)}
                                            </select>
                                        </div>
                                    </div>

                                    {/* ─────────────────────────────────────────── */}
                                    {/* IMPORTAR DESDE CHECKLIST EN CASCADA */}
                                    {/* ─────────────────────────────────────────── */}
                                    <div className="bg-indigo-500/5 border border-indigo-500/20 rounded-2xl p-5 space-y-4">
                                        <div className="flex items-start gap-3">
                                            {/* Ícono Técnico según visual.md */}
                                            <div className="h-12 w-12 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center text-orange-500 shadow-inner rotate-3 hover:rotate-0 transition-all duration-500 flex-shrink-0">
                                                <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="2">
                                                    <path strokeLinecap="round" strokeLinejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <h4 className="text-xs font-black text-indigo-300 uppercase tracking-wider">Importar Atributos desde un Checklist</h4>
                                                <p className="text-[10px] text-white/40 mt-0.5">
                                                    Filtrá por categoría y subcategoría para encontrar rápidamente el checklist. Se importarán las preguntas con el campo
                                                    <strong className="text-orange-400"> "Vincular a Metadata Activo"</strong> completado.
                                                </p>
                                            </div>
                                        </div>

                                        {/* FILTROS EN CASCADA: CATEGORÍA -> SUBCATEGORÍA -> CHECKLIST */}
                                        <div className="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                                            <div>
                                                <label className="block text-[9px] font-black text-indigo-200/50 uppercase tracking-widest mb-1 italic">1. Filtrar por Categoría</label>
                                                <select
                                                    value={filterCategoryId}
                                                    onChange={e => {
                                                        setFilterCategoryId(e.target.value);
                                                        setFilterSubcategoryId('');
                                                        setImportChecklistId('');
                                                        setImportError('');
                                                    }}
                                                    className="w-full bg-black/40 border border-indigo-500/30 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-indigo-400"
                                                >
                                                    <option value="">— Todas las Categorías —</option>
                                                    {categories.map(c => (
                                                        <option key={c.id} value={c.id}>{c.name}</option>
                                                    ))}
                                                </select>
                                            </div>

                                            <div>
                                                <label className="block text-[9px] font-black text-indigo-200/50 uppercase tracking-widest mb-1 italic">2. Filtrar por Subcategoría</label>
                                                <select
                                                    value={filterSubcategoryId}
                                                    onChange={e => {
                                                        setFilterSubcategoryId(e.target.value);
                                                        setImportChecklistId('');
                                                        setImportError('');
                                                    }}
                                                    disabled={!filterCategoryId || availableSubcategories.length === 0}
                                                    className="w-full bg-black/40 border border-indigo-500/30 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-indigo-400 disabled:opacity-30 disabled:cursor-not-allowed"
                                                >
                                                    <option value="">
                                                        {!filterCategoryId
                                                            ? '— Seleccioná una categoría primero —'
                                                            : availableSubcategories.length === 0
                                                                ? '— Sin subcategorías disponibles —'
                                                                : '— Todas las Subcategorías —'}
                                                    </option>
                                                    {availableSubcategories.map(s => (
                                                        <option key={s.id} value={s.id}>{s.name}</option>
                                                    ))}
                                                </select>
                                            </div>
                                        </div>

                                        {/* SELECCIÓN DE CHECKLIST E IMPORTACIÓN */}
                                        <div className="flex gap-2 pt-1">
                                            <select
                                                value={importChecklistId}
                                                onChange={e => { setImportChecklistId(e.target.value); setImportError(''); }}
                                                className="flex-1 bg-black/40 border border-indigo-500/30 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-indigo-400"
                                            >
                                                <option value="">
                                                    {filteredChecklists.length === 0
                                                        ? '— No hay checklists disponibles con estos filtros —'
                                                        : `— Seleccionar Checklist (${filteredChecklists.length} disponibles) —`}
                                                </option>
                                                {filteredChecklists.map(c => (
                                                    <option key={c.id} value={c.id}>
                                                        {c.title}{c.registry_code ? ` (${c.registry_code})` : ''}
                                                    </option>
                                                ))}
                                            </select>

                                            <button
                                                type="button"
                                                onClick={handleImportFromChecklist}
                                                disabled={importing || !importChecklistId}
                                                className="px-5 py-2.5 bg-indigo-600 text-white font-black text-[10px] uppercase tracking-wider rounded-xl hover:bg-indigo-500 disabled:opacity-40 disabled:cursor-not-allowed transition-all flex items-center gap-1.5 flex-shrink-0 shadow-lg"
                                            >
                                                {importing ? (
                                                    <>
                                                        <svg className="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4"/><path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                                        Importando...
                                                    </>
                                                ) : (
                                                    <>
                                                        <svg className="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                                        Importar
                                                    </>
                                                )}
                                            </button>
                                        </div>

                                        {importError && (
                                            <p className="text-[10px] text-amber-400 bg-amber-500/10 border border-amber-500/20 rounded-xl px-3 py-2">
                                                ⚠️ {importError}
                                            </p>
                                        )}
                                    </div>

                                    {/* ─────────────────────────────────────────── */}
                                    {/* LISTA DE ATRIBUTOS (EDITABLE) */}
                                    {/* ─────────────────────────────────────────── */}
                                    <div className="bg-black/40 p-6 rounded-2xl border border-white/10 space-y-4">
                                        <div className="flex items-center justify-between">
                                            <div>
                                                <h4 className="text-xs font-black text-orange-400 uppercase tracking-wider italic">Atributos de Ficha Técnica</h4>
                                                <p className="text-[10px] text-white/40">Los campos importados se marcan con 🔗. Podés editarlos o agregar más manualmente.</p>
                                            </div>
                                            <button
                                                type="button"
                                                onClick={addField}
                                                className="px-3 py-1.5 bg-orange-500/20 text-orange-400 border border-orange-500/30 rounded-xl text-[10px] font-black uppercase hover:bg-orange-500 hover:text-white transition-all"
                                            >
                                                + Agregar Manual
                                            </button>
                                        </div>

                                        <div className="space-y-3">
                                            {form.data.metadata_schema.map((field, index) => (
                                                <div
                                                    key={index}
                                                    className={`p-4 rounded-xl grid grid-cols-1 sm:grid-cols-5 gap-3 items-center border transition-all ${
                                                        field._imported
                                                            ? 'bg-indigo-500/5 border-indigo-500/20'
                                                            : 'bg-white/5 border-white/10'
                                                    }`}
                                                >
                                                    <div>
                                                        <label className="block text-[8px] font-black text-white/30 uppercase">
                                                            {field._imported ? '🔗 Clave (de Checklist)' : 'Clave / ID'}
                                                        </label>
                                                        <input
                                                            type="text"
                                                            value={field.name}
                                                            onChange={e => updateField(index, 'name', e.target.value)}
                                                            placeholder="ej: presion"
                                                            className="w-full bg-black/50 border border-white/10 rounded-lg px-3 py-1.5 text-xs text-orange-300 font-mono focus:outline-none focus:border-orange-500"
                                                            required
                                                        />
                                                    </div>
                                                    <div>
                                                        <label className="block text-[8px] font-black text-white/30 uppercase">Etiqueta visible</label>
                                                        <input
                                                            type="text"
                                                            value={field.label}
                                                            onChange={e => updateField(index, 'label', e.target.value)}
                                                            placeholder="ej: Presión (PSI)"
                                                            className="w-full bg-black/50 border border-white/10 rounded-lg px-3 py-1.5 text-xs text-white focus:outline-none focus:border-orange-500"
                                                            required
                                                        />
                                                    </div>
                                                    <div>
                                                        <label className="block text-[8px] font-black text-white/30 uppercase">Tipo de Dato</label>
                                                        <select
                                                            value={
                                                                field.type === 'date'
                                                                    ? (field.alert_days ? 'date_alert' : 'date_info')
                                                                    : (field.type || 'text')
                                                            }
                                                            onChange={e => {
                                                                const val = e.target.value;
                                                                const updated = [...form.data.metadata_schema];
                                                                if (val === 'date_alert') {
                                                                    updated[index].type = 'date';
                                                                    if (!updated[index].alert_days) updated[index].alert_days = 30;
                                                                } else if (val === 'date_info') {
                                                                    updated[index].type = 'date';
                                                                    updated[index].alert_days = '';
                                                                } else {
                                                                    updated[index].type = val;
                                                                    updated[index].alert_days = '';
                                                                    updated[index].alert_threshold = '';
                                                                }
                                                                form.setData('metadata_schema', updated);
                                                            }}
                                                            className="w-full bg-black/50 border border-white/10 rounded-lg px-3 py-1.5 text-xs text-white focus:outline-none focus:border-orange-500"
                                                        >
                                                            <option value="text">Texto (Alfanumérico)</option>
                                                            <option value="number">Número (Métrica)</option>
                                                            <option value="date_alert">🔔 Fecha Vencimiento (Con Alerta)</option>
                                                            <option value="date_info">🗓️ Fecha Informativa (Sin Alerta)</option>
                                                        </select>
                                                    </div>
                                                    <div>
                                                        <label className="block text-[8px] font-black text-white/30 uppercase">
                                                            {field.type === 'date'
                                                                ? (field.alert_days ? 'Días Alerta Venc.' : 'Alerta Desactivada')
                                                                : (field.type === 'number' ? 'Umbral / Frecuencia (KM/Horas)' : 'N/A')}
                                                        </label>
                                                        {field.type === 'date' ? (
                                                            field.alert_days ? (
                                                                <input
                                                                    type="number"
                                                                    value={field.alert_days || ''}
                                                                    onChange={e => updateField(index, 'alert_days', e.target.value)}
                                                                    placeholder="ej: 30"
                                                                    className="w-full bg-black/50 border border-white/10 rounded-lg px-3 py-1.5 text-xs text-amber-300 focus:outline-none focus:border-orange-500"
                                                                />
                                                            ) : (
                                                                <input type="text" disabled placeholder="Sin alerta" className="w-full bg-black/20 border border-white/5 rounded-lg px-3 py-1.5 text-xs text-white/30 italic" />
                                                            )
                                                        ) : field.type === 'number' ? (
                                                            <div className="flex gap-1.5">
                                                                <input
                                                                    type="number"
                                                                    step="any"
                                                                    value={field.alert_threshold || ''}
                                                                    onChange={e => updateField(index, 'alert_threshold', e.target.value)}
                                                                    placeholder="Fijo (150k)"
                                                                    title="Umbral fijo absoluto"
                                                                    className="w-1/2 bg-black/50 border border-white/10 rounded-lg px-2 py-1.5 text-[11px] text-amber-300 focus:outline-none focus:border-orange-500"
                                                                />
                                                                <input
                                                                    type="number"
                                                                    step="any"
                                                                    value={field.alert_interval || ''}
                                                                    onChange={e => updateField(index, 'alert_interval', e.target.value)}
                                                                    placeholder="+Frec. (15k)"
                                                                    title="Intervalo recurrente (ej: cada 15.000 km)"
                                                                    className="w-1/2 bg-black/50 border border-indigo-500/30 rounded-lg px-2 py-1.5 text-[11px] text-indigo-300 focus:outline-none focus:border-indigo-400"
                                                                />
                                                            </div>
                                                        ) : (
                                                            <input type="text" disabled placeholder="N/A" className="w-full bg-black/20 border border-white/5 rounded-lg px-3 py-1.5 text-xs text-white/20" />
                                                        )}
                                                    </div>
                                                    <div className="text-right">
                                                        <button
                                                            type="button"
                                                            onClick={() => removeField(index)}
                                                            className="px-3 py-1.5 bg-red-500/10 text-red-400 border border-red-500/20 rounded-lg text-[10px] font-black uppercase hover:bg-red-500 hover:text-white transition-all"
                                                        >
                                                            Quitar
                                                        </button>
                                                    </div>

                                                    {/* MENSAJE DE ALERTA PERSONALIZADO (Si tiene alerta de fecha o umbral) */}
                                                    {(field.alert_days || field.type === 'number') && (
                                                        <div className="col-span-1 sm:col-span-5 pt-1">
                                                            <input
                                                                type="text"
                                                                value={field.alert_message || ''}
                                                                onChange={e => updateField(index, 'alert_message', e.target.value)}
                                                                placeholder="💬 Mensaje de Alerta Personalizado (ej: Este activo debe someterse a revisión periódica de aceite / prueba hidráulica)"
                                                                className="w-full bg-black/40 border border-amber-500/20 rounded-lg px-3 py-1.5 text-[11px] text-amber-200 placeholder-white/20 focus:outline-none focus:border-amber-500/50 italic"
                                                            />
                                                        </div>
                                                    )}
                                                </div>
                                            ))}
                                            {form.data.metadata_schema.length === 0 && (
                                                <div className="text-center py-8 text-white/30">
                                                    <p className="text-xs italic">Sin atributos aún. Importá desde un checklist o agregá uno manualmente.</p>
                                                </div>
                                            )}
                                        </div>
                                    </div>

                                    <div className="flex justify-end gap-3 pt-4 border-t border-white/5">
                                        <button
                                            type="button"
                                            onClick={() => setShowModal(false)}
                                            className="px-6 py-2.5 bg-white/5 border border-white/10 text-white/70 rounded-xl text-xs font-bold hover:bg-white/10 transition-all"
                                        >
                                            Cancelar
                                        </button>
                                        <button
                                            type="submit"
                                            disabled={form.processing}
                                            className="px-6 py-2.5 bg-gradient-to-r from-orange-500 to-amber-600 text-white font-black text-xs uppercase tracking-widest rounded-xl shadow-lg hover:brightness-110 transition-all"
                                        >
                                            {form.processing ? 'Guardando...' : 'Guardar Tipo de Activo'}
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    )}
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
