import { Head, Link, useForm, usePage, router } from '@inertiajs/react';
import AuthenticatedLayout from '../../../../Layouts/AuthenticatedLayout';
import { useState, useMemo } from 'react';
import AssetTypeCard from './Partials/AssetTypeCard';
import AssetTypeModal from './Partials/AssetTypeModal';

export default function Index({ assetTypes = [], categories = [], checklists = [] }) {
    const { flash } = usePage().props;
    const [editingType, setEditingType] = useState(null);
    const [showModal, setShowModal] = useState(false);
    const [importing, setImporting] = useState(false);
    const [filterSubcategoryId, setFilterSubcategoryId] = useState('');
    const [importChecklistId, setImportChecklistId] = useState('');
    const [importError, setImportError] = useState('');
    const [expandedAttributes, setExpandedAttributes] = useState({});

    const toggleAttributes = (typeId) => {
        setExpandedAttributes(prev => ({ ...prev, [typeId]: !prev[typeId] }));
    };

    const form = useForm({
        id: null,
        name: '',
        empresa_id: '',
        category_id: '',
        description: '',
        group_label: 'Grupo',
        subgroup_label: 'Subgrupo',
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
            group_label: 'Grupo',
            subgroup_label: 'Subgrupo',
            metadata_schema: [],
            photo_schema: [{ key: 'foto_principal', label: 'Foto Principal / Vista General', required: true }],
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
            group_label: type.group_label || 'Grupo',
            subgroup_label: type.subgroup_label || 'Subgrupo',
            metadata_schema: type.metadata_schema || [],
            photo_schema: type.photo_schema || [{ key: 'foto_principal', label: 'Foto Principal / Vista General', required: true }],
        });
        setShowModal(true);
    };

    const availableSubcategories = useMemo(() => {
        if (!form.data.category_id) return [];
        const cat = categories.find(c => String(c.id) === String(form.data.category_id));
        return cat?.subcategories || [];
    }, [form.data.category_id, categories]);

    const filteredChecklists = useMemo(() => {
        const catId = form.data.category_id;
        return checklists.filter(c => {
            if (catId && String(c.category_id) !== String(catId)) return false;
            if (filterSubcategoryId && String(c.subcategory_id) !== String(filterSubcategoryId)) return false;
            return true;
        });
    }, [checklists, form.data.category_id, filterSubcategoryId]);

    const addField = () => {
        form.setData('metadata_schema', [
            ...form.data.metadata_schema,
            { name: '', label: '', type: 'text', show_in_create: false, role: 'verificacion', alert_days: '', alert_threshold: '' },
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
                headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin',
            });
            if (!res.ok) throw new Error('Error al consultar el checklist.');
            const data = await res.json();

            if (!data.fields || data.fields.length === 0) {
                setImportError('Este checklist no tiene preguntas vinculadas a metadata. Abrí el Builder del checklist y completá el campo "Vincular a Metadata Activo" en cada pregunta relevante.');
                setImporting(false);
                return;
            }

            const manualFields = form.data.metadata_schema.filter(f => !f._imported);
            form.setData('metadata_schema', [...manualFields, ...data.fields]);
            setImportError('');
        } catch {
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
                    <div className="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-[#16162a] p-6 rounded-[2rem] border border-white/5 shadow-2xl">
                        <div>
                            <span className="text-[10px] font-black text-orange-500 uppercase tracking-[0.3em] italic">Configuración de Inventario de Checklist Dinámicos</span>
                            <h1 className="text-2xl font-black text-white uppercase italic">Tipos de Checklist Dinámicos y Atributos</h1>
                            <p className="text-sm text-white mt-1.5">
                                Definí la ficha técnica de cada categoría de activo asociando a un Checklists previamente creado. Podés importar los atributos desde un checklist ya configurado y definir indices y reglas de atributos.
                            </p>
                        </div>
                        <div className="flex gap-3">
                            <button
                                onClick={openCreateModal}
                                className="px-6 py-3 bg-gradient-to-r from-orange-500 to-amber-600 text-white font-black text-xs uppercase tracking-widest rounded-xl shadow-lg hover:brightness-110 transition-all active:scale-95 flex items-center gap-2"
                            >
                                <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2.5" d="M12 4v16m8-8H4" /></svg>
                                Nuevo tipo de Checklist Dinámico
                            </button>
                            <Link href={route('gestor.assets.index')} className="px-4 py-3 bg-white/5 border border-white/10 text-white/70 font-bold text-xs uppercase tracking-wider rounded-xl hover:bg-white/10 transition-all">
                                ← Volver
                            </Link>
                        </div>
                    </div>

                    {flash?.message && (
                        <div className="p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl text-emerald-400 text-xs font-bold">
                            {flash.message}
                        </div>
                    )}

                    {assetTypes.length === 0 ? (
                        <div className="text-center py-20 text-white/30">
                            <svg className="w-16 h-16 mx-auto mb-4 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                            <p className="text-sm font-bold">No hay tipos de activos configurados aún.</p>
                            <p className="text-xs mt-1">Creá uno y vinculalo a un checklist para importar sus atributos automáticamente.</p>
                        </div>
                    ) : (
                        <div className="flex flex-col gap-4">
                            {assetTypes.map(type => (
                                <AssetTypeCard
                                    key={type.id}
                                    type={type}
                                    isExpanded={!!expandedAttributes[type.id]}
                                    onToggleAttributes={toggleAttributes}
                                    onEdit={openEditModal}
                                    onDelete={handleDelete}
                                />
                            ))}
                        </div>
                    )}

                    <AssetTypeModal
                        show={showModal}
                        editingType={editingType}
                        form={form}
                        categories={categories}
                        availableSubcategories={availableSubcategories}
                        filteredChecklists={filteredChecklists}
                        checklists={checklists}
                        filterSubcategoryId={filterSubcategoryId}
                        setFilterSubcategoryId={setFilterSubcategoryId}
                        importChecklistId={importChecklistId}
                        setImportChecklistId={setImportChecklistId}
                        importError={importError}
                        setImportError={setImportError}
                        importing={importing}
                        onImportFromChecklist={handleImportFromChecklist}
                        onAddField={addField}
                        onUpdateField={updateField}
                        onRemoveField={removeField}
                        onClose={() => setShowModal(false)}
                        onSubmit={handleSubmit}
                    />
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
