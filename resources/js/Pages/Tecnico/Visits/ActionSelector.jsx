import React, { useState, useMemo } from 'react';
import { router, Link } from '@inertiajs/react';
import { route } from 'ziggy-js';

export default function ActionSelector({ 
    isOpen, 
    onClose, 
    visit, 
    suggestedTasks = [], 
    pendingAssignments = [], 
    pendingTrainings = [],
    checklistTemplates = [],
    trainingTemplates = []
}) {
    const [activeTab, setActiveTab] = useState('plan'); // 'plan' | 'adhoc'
    const [searchQuery, setSearchQuery] = useState('');
    const [selectedCategory, setSelectedCategory] = useState('');
    const [selectedSubcategory, setSelectedSubcategory] = useState('');

    // All unique categories present in the active checklist templates
    const categories = useMemo(() => {
        const cats = new Set();
        checklistTemplates.forEach(t => {
            const catName = t.category_relation?.name || t.categoryRelation?.name;
            if (catName) cats.add(catName);
        });
        return Array.from(cats).sort();
    }, [checklistTemplates]);

    // Unique subcategories available within the selected category
    const subcategories = useMemo(() => {
        if (!selectedCategory) return [];
        const subcats = new Set();
        checklistTemplates.forEach(t => {
            const catName = t.category_relation?.name || t.categoryRelation?.name;
            if (catName === selectedCategory) {
                const subName = t.subcategory_relation?.name || t.subcategoryRelation?.name;
                if (subName) subcats.add(subName);
            }
        });
        return Array.from(subcats).sort();
    }, [checklistTemplates, selectedCategory]);

    // Filtered templates based on query, category, and subcategory
    const filteredTemplates = useMemo(() => {
        return checklistTemplates.filter(t => {
            const catName = t.category_relation?.name || t.categoryRelation?.name;
            const subName = t.subcategory_relation?.name || t.subcategoryRelation?.name;

            // Search filter
            if (searchQuery) {
                const q = searchQuery.toLowerCase();
                const titleMatch = t.title?.toLowerCase().includes(q);
                const codeMatch = t.registry_code?.toLowerCase().includes(q);
                if (!titleMatch && !codeMatch) return false;
            }
            // Category filter
            if (selectedCategory) {
                if (catName !== selectedCategory) return false;
            }
            // Subcategory filter
            if (selectedSubcategory) {
                if (subName !== selectedSubcategory) return false;
            }
            return true;
        });
    }, [checklistTemplates, searchQuery, selectedCategory, selectedSubcategory]);

    const handleCategoryChange = (e) => {
        setSelectedCategory(e.target.value);
        setSelectedSubcategory('');
    };

    // Parse the Spanish month from the visit date
    const activeMonthName = useMemo(() => {
        if (!visit?.fecha_visita) return 'ESTE MES';
        const dateStr = visit.fecha_visita; // e.g. "2026-05-24"
        const parts = dateStr.split('-');
        if (parts.length < 2) return 'ESTE MES';
        const monthNum = parseInt(parts[1], 10);
        const months = [
            'ENERO', 'FEBRERO', 'MARZO', 'ABRIL', 'MAYO', 'JUNIO',
            'JULIO', 'AGOSTO', 'SEPTIEMBRE', 'OCTUBRE', 'NOVIEMBRE', 'DICIEMBRE'
        ];
        return months[monthNum - 1] || 'ESTE MES';
    }, [visit]);

    // Filter out suggested tasks that are trainings
    const filteredSuggestedTasks = useMemo(() => {
        return suggestedTasks.filter(task => task.category !== 'Capacitación');
    }, [suggestedTasks]);

    const handleAssignSuggested = (planId) => {
        router.post(route('tecnico.visits.assign-suggested', visit.id), { plan_id: planId }, {
            onSuccess: () => onClose()
        });
    };

    const handleStartAdhocChecklist = (templateId) => {
        if (!templateId) return;
        router.get(route('tecnico.checklists.fill', { id: templateId }), {
            visit_id: visit.id
        });
        onClose();
    };

    if (!isOpen) return null;

    return (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div className="absolute inset-0 bg-[#0a0a0f]/80 backdrop-blur-md animate-fade-in" onClick={onClose}></div>
            
            <div className="relative w-full max-w-4xl bg-[#141421] border border-white/10 rounded-[2.5rem] shadow-[0_25px_70px_rgba(0,0,0,0.8)] overflow-hidden flex flex-col max-h-[90vh] animate-scale-up z-10">
                
                {/* Header */}
                <div className="p-8 border-b border-white/5 bg-black/20">
                    <div className="flex items-center justify-between">
                        <div>
                            <h2 className="text-xl font-black text-white uppercase italic tracking-tighter">Selector de Acciones</h2>
                            <p className="text-xs text-white/40 uppercase tracking-widest mt-1">Selecciona la siguiente tarea a ejecutar</p>
                        </div>
                        <button onClick={onClose} className="p-2 bg-white/5 hover:bg-white/10 rounded-full text-white/50 hover:text-white transition-colors">
                            <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/></svg>
                        </button>
                    </div>
                    
                    {/* Tabs */}
                    <div className="flex gap-4 mt-6">
                        <button 
                            onClick={() => setActiveTab('plan')}
                            className={`px-6 py-3 rounded-xl text-xs font-black uppercase italic tracking-widest transition-all ${
                                activeTab === 'plan' ? 'bg-ep-primary text-white shadow-lg shadow-ep-primary/20' : 'bg-white/5 text-white/40 hover:bg-white/10'
                            }`}
                        >
                            Tareas del Plan
                        </button>
                        <button 
                            onClick={() => setActiveTab('adhoc')}
                            className={`px-6 py-3 rounded-xl text-xs font-black uppercase italic tracking-widest transition-all ${
                                activeTab === 'adhoc' ? 'bg-orange-500 text-white shadow-lg shadow-orange-500/20' : 'bg-white/5 text-white/40 hover:bg-white/10'
                            }`}
                        >
                            Acciones Libres
                        </button>
                    </div>
                </div>

                {/* Content Body */}
                <div className="flex-1 overflow-y-auto p-8 space-y-6">
                    
                    {activeTab === 'plan' && (
                        <div className="space-y-6">
                            <h3 className="text-sm font-black text-ep-primary uppercase tracking-widest italic">
                                Obligaciones de {activeMonthName}
                            </h3>
                            {filteredSuggestedTasks.length === 0 && (
                                <p className="text-white/40 text-xs italic bg-white/5 px-6 py-4 rounded-2xl border border-white/5">
                                    No hay obligaciones pendientes para este mes.
                                </p>
                            )}
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                {filteredSuggestedTasks.map(task => {
                                    const isTraining = task.category === 'Capacitación';
                                    return (
                                        <div 
                                            key={task.id} 
                                            className={`bg-black/20 rounded-[2rem] p-6 flex flex-col justify-between border transition-all duration-300 relative group/card overflow-hidden
                                                ${isTraining 
                                                    ? 'border-blue-500/10 hover:border-blue-500/40 hover:shadow-[0_15px_40px_rgba(59,130,246,0.1)]' 
                                                    : 'border-orange-500/10 hover:border-orange-500/40 hover:shadow-[0_15px_40px_rgba(249,115,22,0.1)]'
                                                }`}
                                        >
                                            {/* Glowing indicator bar */}
                                            <div className={`absolute top-0 left-0 right-0 h-1 bg-gradient-to-r transition-opacity duration-300
                                                ${isTraining 
                                                    ? 'from-blue-600 to-indigo-500 opacity-20 group-hover/card:opacity-100' 
                                                    : 'from-orange-500 to-amber-500 opacity-20 group-hover/card:opacity-100'
                                                }`}
                                            ></div>

                                            <div>
                                                <span className={`text-[9px] font-black uppercase tracking-[0.2em] italic block mb-2
                                                    ${isTraining ? 'text-blue-400' : 'text-orange-400'}`}
                                                >
                                                    {task.category}
                                                </span>
                                                <h4 className="text-sm font-black text-white leading-tight uppercase italic tracking-tight">{task.activity_name}</h4>
                                                <p className="text-[9px] font-bold text-white/30 mt-2 uppercase tracking-widest">
                                                    Código: {task.activity_code || 'SIN CÓDIGO'}
                                                </p>
                                            </div>

                                            <button 
                                                onClick={() => handleAssignSuggested(task.id)}
                                                className={`mt-6 w-full h-11 rounded-xl text-[10px] font-black uppercase tracking-widest italic transition-all duration-300 border flex items-center justify-center gap-2
                                                    ${isTraining 
                                                        ? 'bg-blue-500/10 text-blue-400 border-blue-500/20 group-hover/card:bg-blue-600 group-hover/card:text-white group-hover/card:border-transparent group-hover/card:shadow-[0_10px_20px_rgba(59,130,246,0.3)]' 
                                                        : 'bg-orange-500/10 text-orange-400 border-orange-500/20 group-hover/card:bg-orange-500 group-hover/card:text-white group-hover/card:border-transparent group-hover/card:shadow-[0_10px_20px_rgba(249,115,22,0.3)]'
                                                    }`}
                                            >
                                                <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="2.5"><path d="M12 4v16m8-8H4" strokeLinecap="round" strokeLinejoin="round"/></svg>
                                                Asignar e Iniciar
                                            </button>
                                        </div>
                                    );
                                })}
                            </div>

                            <hr className="border-white/5 my-8" />
                            
                            <h3 className="text-sm font-black text-ep-primary uppercase tracking-widest italic">Asignaciones Pendientes</h3>
                            {pendingAssignments.length === 0 && (
                                <p className="text-white/40 text-xs italic bg-white/5 px-6 py-4 rounded-2xl border border-white/5">
                                    No hay asignaciones pendientes en este momento.
                                </p>
                            )}
                            <div className="space-y-3">
                                {pendingAssignments.map(assignment => (
                                    <div key={`assig-${assignment.id}`} className="bg-white/5 border border-white/10 rounded-2xl p-5 flex items-center justify-between hover:border-white/20 transition-all">
                                        <div>
                                            <div className="text-[8px] font-black text-orange-400 uppercase tracking-widest italic mb-1">Inspección Programada</div>
                                            <div className="text-xs font-black text-white uppercase italic">{assignment.checklist?.title || 'Checklist'}</div>
                                        </div>
                                        <Link href={route('tecnico.checklists.fill', { id: assignment.dynamic_checklist_id, assignment: assignment.id, visit_id: visit.id })} onClick={onClose} className="px-5 py-2.5 bg-orange-500 hover:brightness-110 text-white rounded-xl text-[9px] font-black uppercase tracking-widest italic transition-all shadow-[0_5px_15px_rgba(249,115,22,0.3)]">
                                            Ejecutar
                                        </Link>
                                    </div>
                                ))}
                            </div>
                        </div>
                    )}

                    {activeTab === 'adhoc' && (
                        <div className="space-y-8">
                            
                            {/* Filtros de Búsqueda y Clasificación */}
                            <div className="bg-black/20 rounded-[2rem] p-6 border border-white/5 shadow-xl relative overflow-hidden space-y-4">
                                <div className="absolute top-0 right-0 w-32 h-32 bg-orange-500/5 rounded-full blur-3xl pointer-events-none"></div>
                                
                                <div className="space-y-2">
                                    <h3 className="text-sm font-black text-orange-400 uppercase tracking-widest italic">Buscador y Explorador de Checklists</h3>
                                    <p className="text-[9px] font-medium text-white/40 uppercase tracking-widest italic leading-relaxed">
                                        Filtra por nombre, categoría o subcategoría para encontrar el checklist que necesitas ejecutar de forma instantánea.
                                    </p>
                                </div>

                                <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    {/* Buscador */}
                                    <div className="relative">
                                        <span className="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-white/30">
                                            <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                                        </span>
                                        <input 
                                            type="text" 
                                            value={searchQuery}
                                            onChange={e => setSearchQuery(e.target.value)}
                                            className="w-full bg-black/40 border border-white/10 rounded-2xl pl-10 pr-4 py-4 text-xs font-bold text-white placeholder-white/20 focus:border-orange-500/50 focus:ring-orange-500/50 transition-all uppercase tracking-tighter"
                                            placeholder="BUSCAR POR NOMBRE O CÓDIGO..."
                                        />
                                    </div>

                                    {/* Categoría */}
                                    <div>
                                        <select 
                                            value={selectedCategory}
                                            onChange={handleCategoryChange}
                                            className="w-full bg-black/40 border border-white/10 rounded-2xl px-5 py-4 text-xs font-black text-white italic focus:ring-orange-500/50 transition-all uppercase tracking-tighter"
                                        >
                                            <option value="" className="bg-[#141421] text-white">TODAS LAS CATEGORÍAS</option>
                                            {categories.map(cat => (
                                                <option key={cat} value={cat} className="bg-[#141421] text-white">{cat}</option>
                                            ))}
                                        </select>
                                    </div>

                                    {/* Subcategoría */}
                                    <div>
                                        <select 
                                            value={selectedSubcategory}
                                            onChange={e => setSelectedSubcategory(e.target.value)}
                                            disabled={!selectedCategory}
                                            className="w-full bg-black/40 border border-white/10 rounded-2xl px-5 py-4 text-xs font-black text-white italic focus:ring-orange-500/50 transition-all uppercase tracking-tighter disabled:opacity-30 disabled:cursor-not-allowed"
                                        >
                                            <option value="" className="bg-[#141421] text-white">
                                                {!selectedCategory ? 'SELECCIONE CATEGORÍA PRIMERO' : 'TODAS LAS SUBCATEGORÍAS'}
                                            </option>
                                            {subcategories.map(sub => (
                                                <option key={sub} value={sub} className="bg-[#141421] text-white">{sub}</option>
                                            ))}
                                        </select>
                                    </div>
                                </div>
                            </div>

                            {/* Resultados de Búsqueda */}
                            <div className="space-y-4">
                                <div className="flex justify-between items-center px-2">
                                    <h4 className="text-xs font-black text-white/50 uppercase tracking-widest italic">
                                        Checklists Disponibles <span className="text-orange-400">({filteredTemplates.length})</span>
                                    </h4>
                                    {(searchQuery || selectedCategory || selectedSubcategory) && (
                                        <button 
                                            onClick={() => { setSearchQuery(''); setSelectedCategory(''); setSelectedSubcategory(''); }}
                                            className="text-[9px] font-black text-orange-400 hover:text-orange-300 uppercase tracking-widest italic"
                                        >
                                            Limpiar Filtros
                                        </button>
                                    )}
                                </div>

                                {filteredTemplates.length === 0 ? (
                                    <div className="bg-black/10 border border-white/5 rounded-3xl p-12 text-center space-y-3">
                                        <svg className="w-12 h-12 text-white/10 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                        <p className="text-xs text-white/30 italic uppercase tracking-wider font-bold">No se encontraron checklists que coincidan con los filtros.</p>
                                    </div>
                                ) : (
                                    <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        {filteredTemplates.map(template => {
                                            const catName = template.category_relation?.name || template.categoryRelation?.name || 'General';
                                            const subName = template.subcategory_relation?.name || template.subcategoryRelation?.name;
                                            return (
                                                <div 
                                                    key={template.id} 
                                                    className="bg-white/5 border border-white/5 hover:border-orange-500/30 rounded-3xl p-5 flex flex-col justify-between hover:bg-white/[0.08] transition-all group/card relative overflow-hidden"
                                                >
                                                    <div className="space-y-3">
                                                        <div className="flex flex-wrap gap-1.5">
                                                            <span className="px-2 py-0.5 bg-orange-500/10 text-orange-400 rounded-md text-[8px] font-black uppercase tracking-widest italic">
                                                                {catName}
                                                            </span>
                                                            {subName && (
                                                                <span className="px-2 py-0.5 bg-indigo-500/10 text-indigo-300 rounded-md text-[8px] font-black uppercase tracking-widest italic">
                                                                    {subName}
                                                                </span>
                                                            )}
                                                        </div>
                                                        <div>
                                                            <h5 className="text-xs font-black text-white uppercase italic tracking-tight group-hover/card:text-orange-400 transition-colors leading-snug">
                                                                {template.title}
                                                            </h5>
                                                            {template.registry_code && (
                                                                <span className="text-[8px] font-bold text-white/30 tracking-widest block mt-1 uppercase">
                                                                    Código: {template.registry_code}
                                                                </span>
                                                            )}
                                                        </div>
                                                    </div>

                                                    <div className="mt-5 flex items-center justify-between pt-4 border-t border-white/5">
                                                        <span className="text-[8px] text-white/20 uppercase tracking-widest italic">Listo para iniciar</span>
                                                        <button 
                                                            onClick={() => handleStartAdhocChecklist(template.id)}
                                                            className="px-4 py-2 bg-orange-500/10 text-orange-400 border border-orange-500/20 group-hover/card:bg-orange-500 group-hover/card:text-white group-hover/card:border-transparent group-hover/card:shadow-[0_5px_15px_rgba(249,115,22,0.3)] rounded-xl text-[9px] font-black uppercase tracking-widest italic transition-all duration-300"
                                                        >
                                                            Iniciar
                                                        </button>
                                                    </div>
                                                </div>
                                            );
                                        })}
                                    </div>
                                )}
                            </div>

                            {/* Enlaces a incidentes / hallazgos */}
                            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <Link href={route('tecnico.incidentes.create', { visit_id: visit.id })} onClick={onClose} className="bg-red-500/10 border border-red-500/20 p-6 rounded-2xl flex flex-col items-center justify-center gap-2 hover:bg-red-500/20 transition-all group text-center">
                                    <svg className="w-8 h-8 text-red-500 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                                    <span className="text-xs font-black text-red-400 uppercase tracking-widest">Reportar Incidente</span>
                                </Link>
                                <Link href={route('tecnico.hallazgos.create', { visit_id: visit.id })} onClick={onClose} className="bg-amber-500/10 border border-amber-500/20 p-6 rounded-2xl flex flex-col items-center justify-center gap-2 hover:bg-amber-500/20 transition-all group text-center">
                                    <svg className="w-8 h-8 text-amber-500 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                                    <span className="text-xs font-black text-amber-400 uppercase tracking-widest">Registrar Hallazgo</span>
                                </Link>
                            </div>

                        </div>
                    )}

                </div>
            </div>
            
            <style dangerouslySetInnerHTML={{ __html: `
                .animate-fade-in { animation: fade-in 0.3s ease-out forwards; }
                .animate-scale-up { animation: scale-up 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
                @keyframes fade-in { from { opacity: 0; } to { opacity: 1; } }
                @keyframes scale-up { from { opacity: 0; transform: scale(0.9) translateY(20px); } to { opacity: 1; transform: scale(1) translateY(0); } }
            `}} />
        </div>
    );
}
