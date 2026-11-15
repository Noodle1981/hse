import React from 'react';
import { ASSET_ICONS } from '../../Partials/AssetStatusHelpers';
import MetadataFieldEditor from './MetadataFieldEditor';

export default function AssetTypeModal({
    show,
    editingType,
    form,
    categories,
    availableSubcategories,
    filteredChecklists,
    checklists,
    filterSubcategoryId,
    setFilterSubcategoryId,
    importChecklistId,
    setImportChecklistId,
    importError,
    setImportError,
    importing,
    onImportFromChecklist,
    onAddField,
    onUpdateField,
    onRemoveField,
    onClose,
    onSubmit,
}) {
    if (!show) return null;

    const selectedCategory = form.data.category_id
        ? categories.find(c => String(c.id) === String(form.data.category_id))
        : null;

    return (
        <div className="fixed inset-0 bg-black/80 backdrop-blur-md flex items-start justify-center p-4 z-50 overflow-y-auto">
            <div className="bg-[#16162a] border border-white/10 rounded-[2.5rem] p-8 max-w-3xl w-full space-y-6 shadow-2xl my-8">
                <div className="flex items-center justify-between border-b border-white/5 pb-4">
                    <div>
                        <span className="text-[10px] font-black text-orange-400 uppercase tracking-widest italic">
                            {editingType ? 'Editar Tipo de Activo' : 'Nuevo tipo de Checklist Dinámico'}
                        </span>
                        <h2 className="text-xl font-black text-white uppercase italic">
                            {editingType ? editingType.name : 'Configurar Ficha Técnica'}
                        </h2>
                    </div>
                    <button onClick={onClose} className="text-white/40 hover:text-white font-bold text-lg">✕</button>
                </div>

                <form onSubmit={onSubmit} className="space-y-6">
                    <div className="bg-indigo-500/5 border border-indigo-500/20 rounded-2xl p-5 space-y-4">
                        <div className="flex items-start gap-3">
                            <div className="h-12 w-12 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center text-orange-500 shadow-inner rotate-3 hover:rotate-0 transition-all duration-500 flex-shrink-0">
                                <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="2">
                                    <path strokeLinecap="round" strokeLinejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <div>
                                <h4 className="text-xs font-black text-indigo-300 uppercase tracking-wider">Asociar Checklist y configurar Atributos</h4>
                                <p className="text-xs text-white mt-0.5">
                                    Seleccioná la categoría, subcategoría y el checklist. El nombre del tipo de activo se auto-asignará a partir del checklist seleccionado.
                                </p>
                            </div>
                        </div>

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
                                    {categories.map(c => <option key={c.id} value={c.id}>{c.name}</option>)}
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
                                    {availableSubcategories.map(s => <option key={s.id} value={s.id}>{s.name}</option>)}
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
                                        if (checklistSelect) form.setData(d => ({ ...d, name: checklistSelect.title }));
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
                                        <option key={c.id} value={c.id}>{c.title}{c.registry_code ? ` (${c.registry_code})` : ''}</option>
                                    ))}
                                </select>
                            </div>
                        </div>

                        <div className="flex justify-between items-center pt-2">
                            {selectedCategory ? (
                                <div className="flex items-center gap-1.5 p-2 bg-white/5 rounded-lg border border-white/5 w-fit animate-fade-in">
                                    <div className="w-5 h-5 rounded-md flex items-center justify-center border text-xs" style={{ backgroundColor: `${selectedCategory.color}15`, borderColor: `${selectedCategory.color}40`, color: selectedCategory.color }}>
                                        <svg className="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" dangerouslySetInnerHTML={{ __html: ASSET_ICONS[selectedCategory.icon] || ASSET_ICONS['tag'] }} />
                                    </div>
                                    <span className="text-[9px] font-black uppercase tracking-wider italic" style={{ color: selectedCategory.color }}>
                                        Categoría: {selectedCategory.name}
                                    </span>
                                </div>
                            ) : <div />}

                            <button
                                type="button"
                                onClick={onImportFromChecklist}
                                disabled={importing || !importChecklistId}
                                className="px-5 py-2.5 bg-indigo-600 text-white font-black text-[10px] uppercase tracking-wider rounded-xl hover:bg-indigo-500 disabled:opacity-40 disabled:cursor-not-allowed transition-all flex items-center gap-1.5 flex-shrink-0 shadow-lg"
                            >
                                {importing ? 'Cargando Campos...' : 'Cargar Atributos'}
                            </button>
                        </div>

                        {importError && (
                            <p className="text-[10px] text-amber-400 bg-amber-500/10 border border-amber-500/20 rounded-xl px-3 py-2">⚠️ {importError}</p>
                        )}
                    </div>

                    <div className="bg-orange-500/5 border border-orange-500/20 rounded-2xl p-5 space-y-4">
                        <div className="flex items-start gap-3">
                            <div className="h-10 w-10 rounded-xl bg-orange-500/10 border border-orange-500/20 flex items-center justify-center text-orange-400 shadow-inner flex-shrink-0">
                                <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="2.5">
                                    <path strokeLinecap="round" strokeLinejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                </svg>
                            </div>
                            <div className="flex-1">
                                <h4 className="text-xs font-black text-orange-400 uppercase tracking-wider">Etiquetas de Agrupación Jerárquica</h4>
                                <p className="text-[10px] text-white/50 mt-0.5">Personalizá cómo los gestores y técnicos clasificarán y verán los activos de este tipo.</p>
                            </div>
                        </div>

                        <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label className="block text-[9px] font-black text-white/40 uppercase tracking-widest mb-1 italic">Nivel 1: Grupo Principal *</label>
                                <input type="text" value={form.data.group_label} onChange={e => form.setData('group_label', e.target.value)} placeholder="Ej: Piso, Flota, Nave" className="w-full bg-black/40 border border-white/10 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-orange-500" required />
                            </div>
                            <div>
                                <label className="block text-[9px] font-black text-white/40 uppercase tracking-widest mb-1 italic">Nivel 2: Subgrupo / Sector</label>
                                <input type="text" value={form.data.subgroup_label} onChange={e => form.setData('subgroup_label', e.target.value)} placeholder="Ej: Sector, Sub-flota, Oficina" className="w-full bg-black/40 border border-white/10 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-orange-500" />
                            </div>
                        </div>
                    </div>

                    <div className="space-y-3">
                        <div className="flex items-center justify-between px-1">
                            <div>
                                <h4 className="text-xs font-black text-orange-400 uppercase tracking-wider italic">Preguntas / Atributos de Ficha Técnica</h4>
                                <p className="text-[10px] text-white/30 mt-0.5">Los campos importados se marcan con 🔗. Podés editarlos o agregar más manualmente.</p>
                            </div>
                            <button type="button" onClick={onAddField} className="h-9 px-5 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 hover:bg-emerald-500 hover:text-white rounded-xl text-[8px] font-black uppercase tracking-widest italic transition-all cursor-pointer flex-shrink-0">
                                Anclar Atributo
                            </button>
                        </div>

                        <div className="space-y-3 pb-2">
                            {form.data.metadata_schema.map((field, index) => (
                                <MetadataFieldEditor
                                    key={index}
                                    field={field}
                                    index={index}
                                    metadataSchema={form.data.metadata_schema}
                                    checklists={checklists}
                                    onUpdateSchema={(updated) => form.setData('metadata_schema', updated)}
                                    onUpdateField={onUpdateField}
                                    onRemoveField={onRemoveField}
                                />
                            ))}
                            {form.data.metadata_schema.length === 0 && (
                                <div className="text-center py-12 text-white/20 border border-dashed border-white/10 rounded-[2rem]">
                                    <p className="text-2xl mb-2">📋</p>
                                    <p className="text-xs italic">Sin atributos aún. Importá desde un checklist o anclá uno manualmente.</p>
                                </div>
                            )}
                        </div>
                    </div>

                    <div className="flex justify-end gap-3 pt-4 border-t border-white/5">
                        <button type="button" onClick={onClose} className="px-6 py-2.5 bg-white/5 border border-white/10 text-white/70 rounded-xl text-xs font-bold hover:bg-white/10 transition-all">Cancelar</button>
                        <button type="submit" disabled={form.processing} className="px-6 py-2.5 bg-gradient-to-r from-orange-500 to-amber-600 text-white font-black text-xs uppercase tracking-widest rounded-xl shadow-lg hover:brightness-110 transition-all">
                            {form.processing ? 'Guardando...' : 'Guardar Tipo de Activo'}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    );
}
