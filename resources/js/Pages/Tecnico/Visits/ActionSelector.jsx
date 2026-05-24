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
    const [expandedCategory, setExpandedCategory] = useState(null);
    const [showExpressTraining, setShowExpressTraining] = useState(false);
    const [selectedAdhocTemplate, setSelectedAdhocTemplate] = useState('');
    
    // Form for express training
    const [trainingForm, setTrainingForm] = useState({ title: '', duration: '', description: '' });

    // Group checklist templates by category
    const templatesByCategory = useMemo(() => {
        const groups = {};
        checklistTemplates.forEach(template => {
            const catName = template.category_relation?.name || 'General';
            if (!groups[catName]) groups[catName] = [];
            groups[catName].push(template);
        });
        return groups;
    }, [checklistTemplates]);

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

    const submitExpressTraining = (e) => {
        e.preventDefault();
        router.post(route('tecnico.trainings.start-in-visit', visit.id), trainingForm, {
            onSuccess: () => {
                setShowExpressTraining(false);
                onClose();
            }
        });
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
                                activeTab === 'adhoc' ? 'bg-indigo-500 text-white shadow-lg shadow-indigo-500/20' : 'bg-white/5 text-white/40 hover:bg-white/10'
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
                            {suggestedTasks.length === 0 && (
                                <p className="text-white/40 text-xs italic bg-white/5 px-6 py-4 rounded-2xl border border-white/5">
                                    No hay obligaciones pendientes para este mes.
                                </p>
                            )}
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                {suggestedTasks.map(task => {
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
                            {pendingAssignments.length === 0 && pendingTrainings.length === 0 && (
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
                            
                            {/* Capacitación Express */}
                            <div className="bg-blue-500/10 border border-blue-500/20 rounded-[2rem] p-6 relative overflow-hidden">
                                <div className="absolute top-0 right-0 w-32 h-32 bg-blue-500/5 rounded-full blur-3xl pointer-events-none"></div>
                                <div className="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4 mb-4">
                                    <div>
                                        <h3 className="text-sm font-black text-blue-400 uppercase tracking-widest italic">Capacitación Express</h3>
                                        <p className="text-[10px] font-medium text-white/50 uppercase tracking-wide mt-1 italic">Registra una charla de 5 minutos o entrenamiento rápido in-situ.</p>
                                    </div>
                                    <button 
                                        onClick={() => setShowExpressTraining(!showExpressTraining)}
                                        className="h-10 px-5 bg-blue-500 hover:bg-blue-400 text-white rounded-xl text-[9px] font-black uppercase tracking-widest italic transition-all"
                                    >
                                        {showExpressTraining ? 'Cancelar' : 'Iniciar Charla'}
                                    </button>
                                </div>
                                
                                {showExpressTraining && (
                                    <form onSubmit={submitExpressTraining} className="space-y-4 animate-fade-in pt-4 border-t border-blue-500/20">
                                        <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                            <div className="space-y-2">
                                                <label className="text-[9px] font-black text-blue-300 uppercase tracking-widest block ml-1">Tema / Título</label>
                                                <input type="text" required value={trainingForm.title} onChange={e => setTrainingForm({...trainingForm, title: e.target.value})} className="w-full bg-black/40 border border-white/10 rounded-2xl px-4 py-3 text-xs text-white italic font-bold focus:ring-blue-500 transition-all" placeholder="Ej: Uso Correcto de EPP" />
                                            </div>
                                            <div className="space-y-2">
                                                <label className="text-[9px] font-black text-blue-300 uppercase tracking-widest block ml-1">Duración Estimada</label>
                                                <input type="text" required value={trainingForm.duration} onChange={e => setTrainingForm({...trainingForm, duration: e.target.value})} className="w-full bg-black/40 border border-white/10 rounded-2xl px-4 py-3 text-xs text-white italic font-bold focus:ring-blue-500 transition-all" placeholder="Ej: 15 mins" />
                                            </div>
                                        </div>
                                        <div className="space-y-2">
                                            <label className="text-[9px] font-black text-blue-300 uppercase tracking-widest block ml-1">Descripción / Bitácora</label>
                                            <textarea required value={trainingForm.description} onChange={e => setTrainingForm({...trainingForm, description: e.target.value})} className="w-full bg-black/40 border border-white/10 rounded-2xl px-4 py-3 text-xs text-white italic font-bold focus:ring-blue-500 transition-all" rows="2" placeholder="Describa brevemente los puntos tratados..."></textarea>
                                        </div>
                                        <button type="submit" className="w-full h-12 bg-blue-600 hover:bg-blue-500 text-white rounded-xl text-[10px] font-black uppercase tracking-widest italic shadow-[0_10px_30px_rgba(59,130,246,0.3)] transition-all">
                                            Guardar y Tomar Firmas
                                        </button>
                                    </form>
                                )}
                            </div>

                            {/* Selector Directo de Checklists Libres */}
                            <div className="bg-black/20 rounded-[2rem] p-6 border border-white/5 shadow-xl relative overflow-hidden">
                                <div className="absolute top-0 right-0 w-32 h-32 bg-indigo-500/5 rounded-full blur-3xl pointer-events-none"></div>
                                <h3 className="text-sm font-black text-indigo-400 uppercase tracking-widest italic mb-2">Autocargar Checklist a la Visita</h3>
                                <p className="text-[9px] font-medium text-white/40 uppercase tracking-widest italic mb-6 leading-relaxed">
                                    Selecciona cualquier checklist activo de la empresa para asignártelo a tu visita en progreso de forma instantánea.
                                </p>
                                
                                <div className="flex flex-col sm:flex-row gap-4 items-stretch sm:items-center">
                                    <div className="flex-1">
                                        <select 
                                            id="adhoc_select"
                                            value={selectedAdhocTemplate}
                                            onChange={e => setSelectedAdhocTemplate(e.target.value)}
                                            className="w-full bg-black/40 border border-white/10 rounded-2xl px-5 py-4 text-xs font-black text-white italic focus:ring-indigo-500/50 transition-all uppercase tracking-tighter"
                                        >
                                            <option value="">SELECCIONE CUALQUIER CHECKLIST...</option>
                                            {checklistTemplates.map(t => (
                                                <option key={t.id} value={t.id} className="bg-[#141421] text-white">
                                                    {t.title} {t.registry_code ? `(${t.registry_code})` : ''}
                                                </option>
                                            ))}
                                        </select>
                                    </div>
                                    <button 
                                        type="button"
                                        disabled={!selectedAdhocTemplate}
                                        onClick={() => handleStartAdhocChecklist(selectedAdhocTemplate)}
                                        className="h-14 px-8 bg-indigo-500 hover:bg-indigo-400 disabled:opacity-20 text-white rounded-xl text-[10px] font-black uppercase tracking-widest italic transition-all shadow-[0_10px_25px_rgba(99,102,241,0.3)] whitespace-nowrap active:scale-95"
                                    >
                                        Asignar e Iniciar
                                    </button>
                                </div>
                            </div>

                            {/* Checklists Libres (Accordion por Categorías) */}
                            <div>
                                <h3 className="text-sm font-black text-white/60 uppercase tracking-widest italic mb-4">Explorar por Categorías</h3>
                                <div className="space-y-3">
                                    {Object.entries(templatesByCategory).map(([category, templates]) => (
                                        <div key={category} className="bg-black/20 border border-white/5 rounded-2xl overflow-hidden">
                                            <button 
                                                onClick={() => setExpandedCategory(expandedCategory === category ? null : category)}
                                                className="w-full flex items-center justify-between p-4 hover:bg-white/[0.02] transition-colors"
                                            >
                                                <span className="font-bold text-white uppercase text-xs tracking-wider">{category} <span className="text-white/30 ml-2">({templates.length})</span></span>
                                                <svg className={`w-5 h-5 text-white/50 transition-transform ${expandedCategory === category ? 'rotate-180' : ''}`} fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M19 9l-7 7-7-7" /></svg>
                                            </button>
                                            
                                            {expandedCategory === category && (
                                                <div className="p-4 pt-0 border-t border-white/5 bg-black/10 grid grid-cols-1 md:grid-cols-2 gap-3">
                                                    {templates.map(template => (
                                                        <div key={template.id} className="bg-white/5 rounded-xl p-3 flex items-center justify-between hover:bg-white/10 transition-colors">
                                                            <span className="text-xs text-white/80 line-clamp-1 pr-4 uppercase italic font-bold tracking-tight">{template.title}</span>
                                                            <button 
                                                                onClick={() => handleStartAdhocChecklist(template.id)}
                                                                className="shrink-0 px-3 py-1.5 bg-indigo-500/20 text-indigo-300 rounded-lg text-[9px] font-black uppercase hover:bg-indigo-500 hover:text-white transition-colors"
                                                            >
                                                                Iniciar
                                                            </button>
                                                        </div>
                                                    ))}
                                                </div>
                                            )}
                                        </div>
                                    ))}
                                </div>
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
