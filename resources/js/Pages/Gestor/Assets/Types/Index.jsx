import { Head, Link, useForm, usePage, router } from '@inertiajs/react';
import AuthenticatedLayout from '../../../../Layouts/AuthenticatedLayout';
import { useState, useMemo } from 'react';

const ICONS = {
    'shield-check': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />',
    'helmet': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />',
    'alert': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />',
    'fire': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z" />',
    'truck': '<path d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 100-4 2 2 0 000 4zm10 0a2 2 0 100-4 2 2 0 000 4z" />',
    'tool': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />',
    'doc': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />',
    'eye': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />',
    'environment': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />',
    'tag': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2zM9 5h6" />',
    'extintor': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5h6M12 5v2M9 9h6v11a2 2 0 01-2 2h-2a2 2 0 01-2-2V9zm3-4a3 3 0 013-3h1M7 9a2 2 0 012-2h1M18 10v4a1 1 0 01-1 1h-2" />',
    'mineria': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 15L20 4M4 11a14 14 0 0113-7M4 11a14 14 0 0013 7" />',
    'fabrica': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V9l-5 3V9l-5 3V9l-5 3v9h15zM17 5v4" />',
    'construccion': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 20h16M7 20l4-14a1 1 0 012 0l4 14M9 12h6M10 8h4" />',
    'bodega': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m-8-14v14" />',
    'colectivo': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 18v2a1 1 0 001 1h2a1 1 0 001-1v-2h8v2a1 1 0 001 1h2a1 1 0 001-1v-2M4 6h16a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V8a2 2 0 012-2zm2 3h4v4H6V9zm8 0h4v4h-4V9zM6 15h12M6 15h.01M18 15h.01" />',
    'camioneta': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 14h18M6 17a2 2 0 11-4 0 2 2 0 014 0zm12 0a2 2 0 11-4 0 2 2 0 014 0zm-12-3v-3a1 1 0 011-1h5l3 3h5v4M7 10h4v3H7v-3z" />',
};

export default function Index({ assetTypes = [], categories = [], empresas = [], checklists = [] }) {
    const { flash } = usePage().props;
    const [editingType, setEditingType] = useState(null);
    const [showModal, setShowModal] = useState(false);
    const [importing, setImporting] = useState(false);
    
    // Filtros de subcategoría para importar desde checklist
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
        photo_schema: [],
    });

    const openCreateModal = () => {
        setEditingType(null);
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
            photo_schema: [
                { key: 'foto_principal', label: 'Foto Principal / Vista General', required: true },
            ],
        });
        setShowModal(true);
    };

    const openEditModal = (type) => {
        setEditingType(type);
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
            photo_schema: type.photo_schema || [
                { key: 'foto_principal', label: 'Foto Principal / Vista General', required: true },
            ],
        });
        setShowModal(true);
    };

    // Subcategorías disponibles según la categoría seleccionada en Info General
    const availableSubcategories = useMemo(() => {
        const catId = form.data.category_id;
        if (!catId) return [];
        const cat = categories.find(c => String(c.id) === String(catId));
        return cat?.subcategories || [];
    }, [form.data.category_id, categories]);

    // Checklists filtrados en cascada por la Categoría principal y Subcategoría
    const filteredChecklists = useMemo(() => {
        const catId = form.data.category_id;
        return checklists.filter(c => {
            if (catId && String(c.category_id) !== String(catId)) {
                return false;
            }
            if (filterSubcategoryId && String(c.subcategory_id) !== String(filterSubcategoryId)) {
                return false;
            }
            return true;
        });
    }, [checklists, form.data.category_id, filterSubcategoryId]);

    const addField = () => {
        form.setData('metadata_schema', [
            ...form.data.metadata_schema,
            { name: '', label: '', type: 'text', show_in_create: false, alert_days: '', alert_threshold: '' },
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

    const addPhotoField = () => {
        form.setData('photo_schema', [
            ...(form.data.photo_schema || []),
            { key: '', label: '', required: false },
        ]);
    };

    const updatePhotoField = (index, key, value) => {
        const updated = [...(form.data.photo_schema || [])];
        updated[index][key] = value;
        form.setData('photo_schema', updated);
    };

    const removePhotoField = (index) => {
        const updated = [...(form.data.photo_schema || [])];
        updated.splice(index, 1);
        form.setData('photo_schema', updated);
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
                                            {type.category ? (
                                                <div className="flex items-center gap-1.5">
                                                    <div className="w-5 h-5 rounded-md flex items-center justify-center border transition-all text-xs" style={{ backgroundColor: `${type.category.color}15`, borderColor: `${type.category.color}40`, color: type.category.color }} title={`Categoría: ${type.category.name}`}>
                                                        <svg className="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" dangerouslySetInnerHTML={{ __html: ICONS[type.category.icon] || ICONS['tag'] }} />
                                                    </div>
                                                    <span className="px-1.5 py-0.5 rounded-lg text-[9px] font-black uppercase tracking-wider italic" style={{ color: type.category.color }}>
                                                        {type.category.name}
                                                    </span>
                                                </div>
                                            ) : (
                                                <span className="px-2.5 py-1 bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 rounded-lg text-[10px] font-bold">
                                                    General
                                                </span>
                                            )}
                                        </div>
                                        {type.description && <p className="text-xs text-white/40 mb-4">{type.description}</p>}

                                        <div className="bg-black/30 p-4 rounded-xl border border-white/5 space-y-2">
                                            <span className="text-[9px] font-black text-orange-400 uppercase tracking-widest block">
                                                Atributos de Ficha Técnica ({type.metadata_schema?.length || 0})
                                            </span>
                                            {type.metadata_schema && type.metadata_schema.length > 0 ? (
                                                <div className="flex flex-wrap gap-1.5">
                                                    {type.metadata_schema.map((f, i) => (
                                                        <span key={i} title={`Clave: ${f.name}`} className={`px-2.5 py-1 border rounded-lg text-[10px] font-mono cursor-help ${f.show_in_create ? 'bg-orange-500/10 border-orange-500/30 text-orange-300 font-bold' : 'bg-white/5 border-white/10 text-gray-300'}`}>
                                                            {f.show_in_create && <span className="mr-1 text-[9px]">📌 Alta |</span>}
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
                                    {/* ASOCIAR CHECKLIST Y CARGAR ATRIBUTOS */}
                                    <div className="bg-indigo-500/5 border border-indigo-500/20 rounded-2xl p-5 space-y-4">
                                        <div className="flex items-start gap-3">
                                            {/* Ícono Técnico según visual.md */}
                                            <div className="h-12 w-12 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center text-orange-500 shadow-inner rotate-3 hover:rotate-0 transition-all duration-500 flex-shrink-0">
                                                <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="2">
                                                    <path strokeLinecap="round" strokeLinejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <h4 className="text-xs font-black text-indigo-300 uppercase tracking-wider">Asociar Checklist y Cargar Atributos</h4>
                                                <p className="text-[10px] text-white/40 mt-0.5">
                                                    Seleccioná la categoría, subcategoría y el checklist. El nombre del tipo de activo se auto-asignará a partir del checklist seleccionado.
                                                </p>
                                            </div>
                                        </div>

                                        {/* FILTROS EN CASCADA: CATEGORÍA -> SUBCATEGORÍA -> CHECKLIST */}
                                        <div className="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-1">
                                            <div>
                                                <label className="block text-[9px] font-black text-indigo-200/50 uppercase tracking-widest mb-1 italic">1. Categoría *</label>
                                                <select
                                                    value={form.data.category_id}
                                                    onChange={e => {
                                                        form.setData('category_id', e.target.value);
                                                        setFilterSubcategoryId('');
                                                        setImportChecklistId('');
                                                        setImportError('');
                                                    }}
                                                    className="w-full bg-black/40 border border-indigo-500/30 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-indigo-400"
                                                    required
                                                >
                                                    <option value="">— Seleccionar Categoría —</option>
                                                    {categories.map(c => (
                                                        <option key={c.id} value={c.id}>{c.name}</option>
                                                    ))}
                                                </select>
                                            </div>

                                            <div>
                                                <label className="block text-[9px] font-black text-indigo-200/50 uppercase tracking-widest mb-1 italic">2. Subcategoría (Opcional)</label>
                                                <select
                                                    value={filterSubcategoryId}
                                                    onChange={e => {
                                                        setFilterSubcategoryId(e.target.value);
                                                        setImportChecklistId('');
                                                        setImportError('');
                                                    }}
                                                    disabled={!form.data.category_id || availableSubcategories.length === 0}
                                                    className="w-full bg-black/40 border border-indigo-500/30 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-indigo-400 disabled:opacity-30 disabled:cursor-not-allowed"
                                                >
                                                    <option value="">
                                                        {!form.data.category_id
                                                            ? '— Seleccioná categoría primero —'
                                                            : availableSubcategories.length === 0
                                                                ? '— Sin subcategorías disponibles —'
                                                                : '— Todas las Subcategorías —'}
                                                    </option>
                                                    {availableSubcategories.map(s => (
                                                        <option key={s.id} value={s.id}>{s.name}</option>
                                                    ))}
                                                </select>
                                            </div>

                                            <div>
                                                <label className="block text-[9px] font-black text-indigo-200/50 uppercase tracking-widest mb-1 italic">3. Checklist Asociado *</label>
                                                <select
                                                    value={importChecklistId}
                                                    onChange={e => {
                                                        const cid = e.target.value;
                                                        setImportChecklistId(cid);
                                                        setImportError('');
                                                        const checklistSelect = checklists.find(c => String(c.id) === String(cid));
                                                        if (checklistSelect) {
                                                            form.setData(d => ({
                                                                ...d,
                                                                name: checklistSelect.title
                                                            }));
                                                        }
                                                    }}
                                                    disabled={!form.data.category_id}
                                                    className="w-full bg-black/40 border border-indigo-500/30 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-indigo-400 disabled:opacity-30 disabled:cursor-not-allowed"
                                                    required={!editingType}
                                                >
                                                    <option value="">
                                                        {!form.data.category_id
                                                            ? '— Seleccioná categoría primero —'
                                                            : filteredChecklists.length === 0
                                                                ? '— No hay checklists disponibles —'
                                                                : `— Seleccionar Checklist (${filteredChecklists.length}) —`}
                                                    </option>
                                                    {filteredChecklists.map(c => (
                                                        <option key={c.id} value={c.id}>
                                                            {c.title}{c.registry_code ? ` (${c.registry_code})` : ''}
                                                        </option>
                                                    ))}
                                                </select>
                                            </div>
                                        </div>

                                        {/* SELECCIÓN DE CHECKLIST E IMPORTACIÓN ACCIÓN */}
                                        <div className="flex justify-between items-center pt-2">
                                            {/* Previsualización dinámica de Categoría e Icono */}
                                            {form.data.category_id ? (() => {
                                                const cat = categories.find(c => String(c.id) === String(form.data.category_id));
                                                if (!cat) return <div />;
                                                return (
                                                    <div className="flex items-center gap-1.5 p-2 bg-white/5 rounded-lg border border-white/5 w-fit animate-fade-in">
                                                        <div className="w-5 h-5 rounded-md flex items-center justify-center border text-xs" style={{ backgroundColor: `${cat.color}15`, borderColor: `${cat.color}40`, color: cat.color }}>
                                                            <svg className="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" dangerouslySetInnerHTML={{ __html: ICONS[cat.icon] || ICONS['tag'] }} />
                                                        </div>
                                                        <span className="text-[9px] font-black uppercase tracking-wider italic" style={{ color: cat.color }}>
                                                            Categoría: {cat.name}
                                                        </span>
                                                    </div>
                                                );
                                            })() : <div />}

                                            <button
                                                type="button"
                                                onClick={handleImportFromChecklist}
                                                disabled={importing || !importChecklistId}
                                                className="px-5 py-2.5 bg-indigo-600 text-white font-black text-[10px] uppercase tracking-wider rounded-xl hover:bg-indigo-500 disabled:opacity-40 disabled:cursor-not-allowed transition-all flex items-center gap-1.5 flex-shrink-0 shadow-lg"
                                            >
                                                {importing ? (
                                                    <>
                                                        <svg className="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4"/><path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                                        Cargando Campos...
                                                    </>
                                                ) : (
                                                    <>
                                                        <svg className="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                                        Cargar Atributos
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
                                                        <label className="flex items-center gap-1.5 cursor-pointer mt-1.5 select-none">
                                                            <input
                                                                type="checkbox"
                                                                checked={!!field.show_in_create}
                                                                onChange={e => updateField(index, 'show_in_create', e.target.checked)}
                                                                className="rounded border-white/20 bg-black/50 text-orange-500 focus:ring-orange-500 h-3 w-3"
                                                            />
                                                            <span className="text-[9px] font-bold text-orange-400">📌 Solicitar al crear activo</span>
                                                        </label>
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
