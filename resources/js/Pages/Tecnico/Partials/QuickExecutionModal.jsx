import { Link } from '@inertiajs/react';

export default function QuickExecutionModal({
    modalOpen,
    setModalOpen,
    activeTab,
    activeVisit,
    sedes,
    selectedSedeId,
    setSelectedSedeId,
    modalSubTab,
    setModalSubTab,
    theme,
    activeSedeName,
    modalPlannedTasks,
    selectedMonth,
    safeRoute,
    handleExecute,
    handleExecuteAdhoc,
    modalSearchQuery,
    setModalSearchQuery,
    modalSelectedCategory,
    setModalSelectedCategory,
    modalSelectedSubcategory,
    setModalSelectedSubcategory,
    modalCategories,
    modalSubcategories,
    modalFilteredTemplates,
    selectedChecklistIdForConfig,
    setSelectedChecklistIdForConfig,
    trainingForm,
    setTrainingForm,
}) {
    if (!modalOpen) return null;

    const subTabActiveClass = activeTab === 'inspections'
        ? 'bg-orange-500 text-white shadow-lg shadow-orange-500/20'
        : activeTab === 'trainings'
            ? 'bg-[#4f46e5] text-white shadow-lg shadow-[#4f46e5]/20'
            : 'bg-emerald-600 text-white shadow-lg shadow-emerald-600/20';

    return (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div className="absolute inset-0 bg-[#07070c]/90 backdrop-blur-sm transition-all duration-300 animate-fade-in" onClick={() => setModalOpen(false)} />

            <div className="relative w-full max-w-4xl bg-[#141421] border border-white/10 rounded-[2.5rem] shadow-[0_25px_60px_rgba(0,0,0,0.8)] overflow-hidden flex flex-col max-h-[85vh] animate-scale-up z-10 text-white">
                <div className={`absolute top-0 left-0 right-0 h-1 bg-gradient-to-r ${
                    activeTab === 'inspections' ? 'from-orange-500 to-amber-500' :
                    activeTab === 'trainings' ? 'from-indigo-600 to-blue-500' :
                    'from-emerald-500 to-teal-500'
                }`} />

                <div className="p-8 border-b border-white/5 bg-black/20 flex items-center justify-between">
                    <div>
                        <h3 className="text-xl font-black uppercase italic tracking-tighter">
                            {activeTab === 'inspections' ? 'Consola de Inspección' : activeTab === 'trainings' ? 'Consola de Capacitación' : 'Consola de Informe'}
                        </h3>
                        <p className="text-[10px] text-white/40 uppercase tracking-widest mt-1">
                            {activeVisit ? `Ejecutando en Sede: ${activeVisit.sede?.nombre_sede}` : 'Establecer sede e iniciar ejecución'}
                        </p>
                    </div>
                    <button onClick={() => setModalOpen(false)} className="p-2 bg-white/5 hover:bg-white/10 rounded-full text-white/50 hover:text-white transition-colors">
                        <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" strokeWidth="2.5" strokeLinecap="round" strokeLinejoin="round"/></svg>
                    </button>
                </div>

                <div className="flex-1 overflow-y-auto p-8 space-y-6">
                    {!activeVisit && (
                        <div className="bg-white/[0.02] border border-white/5 rounded-2xl p-6 space-y-3">
                            <label className="text-[10px] font-black text-white/50 uppercase tracking-widest italic block">Seleccionar Sede Destino</label>
                            <select
                                value={selectedSedeId}
                                onChange={e => setSelectedSedeId(e.target.value)}
                                className="w-full bg-[#11111d] border border-white/10 rounded-xl px-5 py-4 text-xs font-black text-white focus:ring-ep-primary/50 transition-all italic uppercase [color-scheme:dark]"
                            >
                                <option value="">SELECCIONA UBICACIÓN...</option>
                                {sedes.map(s => <option key={s.id} value={s.id}>{s.nombre_sede.toUpperCase()}</option>)}
                            </select>
                        </div>
                    )}

                    {selectedSedeId ? (
                        <div className="space-y-6">
                            <div className="flex bg-[#11111d] p-1 rounded-2xl border border-white/5 w-full">
                                <button
                                    type="button"
                                    onClick={() => setModalSubTab('planned')}
                                    className={`flex-1 py-3 px-6 rounded-xl text-[10px] font-black uppercase tracking-[0.15em] italic transition-all flex items-center justify-center gap-2 ${modalSubTab === 'planned' ? subTabActiveClass : 'text-white/40 hover:text-white/80'}`}
                                >
                                    Según el Plan
                                </button>
                                <button
                                    type="button"
                                    onClick={() => setModalSubTab('adhoc')}
                                    className={`flex-1 py-3 px-6 rounded-xl text-[10px] font-black uppercase tracking-[0.15em] italic transition-all flex items-center justify-center gap-2 ${modalSubTab === 'adhoc' ? subTabActiveClass : 'text-white/40 hover:text-white/80'}`}
                                >
                                    Ejecución Libre / Ad-hoc
                                </button>
                            </div>

                            {modalSubTab === 'planned' && (
                                <div className="space-y-4 animate-fade-in">
                                    <h4 className={`text-xs font-black uppercase tracking-widest italic ${theme.primary}`}>
                                        Tareas Planificadas para {activeSedeName.toUpperCase() || 'ESTA SEDE'}
                                    </h4>
                                    {modalPlannedTasks.length === 0 ? (
                                        <p className="text-white/40 text-xs italic bg-white/[0.02] px-6 py-4 rounded-xl border border-white/5">
                                            No hay tareas planificadas pendientes de {theme.tabTitle.toLowerCase()} para esta sede en este mes.
                                        </p>
                                    ) : (
                                        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            {modalPlannedTasks.map(item => (
                                                <div key={`modal-planned-${item.id}`} className="bg-[#1b1b2d] border border-white/5 rounded-[1.5rem] p-5 flex flex-col justify-between hover:border-white/10 transition-all group/card relative overflow-hidden">
                                                    <div>
                                                        <div className="text-[8px] font-black text-white/30 uppercase tracking-widest italic mb-1">PROGRAMADO • ID: {item.registry_code}</div>
                                                        <h5 className="text-xs font-black text-white uppercase italic leading-tight">{item.title}</h5>
                                                    </div>
                                                    {activeTab === 'reports' ? (
                                                        <Link
                                                            href={safeRoute('master-reports.create', { sede_id: selectedSedeId, periodo: `${new Date().getFullYear()}-${String(selectedMonth).padStart(2, '0')}` })}
                                                            onClick={() => setModalOpen(false)}
                                                            className={`mt-4 w-full h-11 rounded-xl text-[9px] font-black uppercase tracking-widest italic transition-all flex items-center justify-center gap-2 border border-white/10 ${theme.bg} hover:brightness-110 shadow-lg`}
                                                        >
                                                            Iniciar
                                                        </Link>
                                                    ) : (
                                                        <button
                                                            onClick={() => handleExecute(item)}
                                                            className={`mt-4 w-full h-11 rounded-xl text-[9px] font-black uppercase tracking-widest italic transition-all flex items-center justify-center gap-2 border border-white/10 ${theme.bg} hover:brightness-110 shadow-lg`}
                                                        >
                                                            {item.status === 'in_progress' ? 'Reanudar' : 'Iniciar'}
                                                        </button>
                                                    )}
                                                </div>
                                            ))}
                                        </div>
                                    )}
                                </div>
                            )}

                            {modalSubTab === 'adhoc' && (
                                <div className="space-y-4 animate-fade-in">
                                    <h4 className="text-xs font-black text-indigo-100/50 uppercase tracking-widest italic">Ejecución Libre / Ad-hoc</h4>

                                    {(activeTab === 'inspections' || activeTab === 'trainings') && (
                                        <AdhocTemplateFilters
                                            modalSearchQuery={modalSearchQuery}
                                            setModalSearchQuery={setModalSearchQuery}
                                            modalSelectedCategory={modalSelectedCategory}
                                            setModalSelectedCategory={setModalSelectedCategory}
                                            modalSelectedSubcategory={modalSelectedSubcategory}
                                            setModalSelectedSubcategory={setModalSelectedSubcategory}
                                            modalCategories={modalCategories}
                                            modalSubcategories={modalSubcategories}
                                            filteredCount={modalFilteredTemplates.length}
                                            accent={activeTab === 'inspections' ? 'orange' : 'indigo'}
                                        />
                                    )}

                                    {activeTab === 'inspections' && (
                                        modalFilteredTemplates.length === 0 ? (
                                            <div className="py-12 text-center text-white/30 italic bg-white/[0.01] border border-white/5 rounded-2xl">
                                                No se encontraron checklists que coincidan con la búsqueda.
                                            </div>
                                        ) : (
                                            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                {modalFilteredTemplates.map(template => (
                                                    <div key={`adhoc-ins-${template.id}`} className="bg-black/20 border border-white/5 rounded-2xl p-5 flex items-center justify-between hover:bg-white/[0.01] hover:border-white/10 transition-all">
                                                        <div>
                                                            <div className="text-[7px] font-bold text-white/30 uppercase tracking-wider mb-0.5">CHECKLIST</div>
                                                            <div className="text-xs font-black text-white uppercase italic leading-none">{template.title}</div>
                                                            <div className="text-[8px] font-bold text-white/20 uppercase tracking-wider mt-1.5 italic">
                                                                {template.category_relation?.name || template.categoryRelation?.name || 'General'}
                                                                {(template.subcategory_relation?.name || template.subcategoryRelation?.name) && ` • ${template.subcategory_relation?.name || template.subcategoryRelation?.name}`}
                                                            </div>
                                                        </div>
                                                        <button
                                                            onClick={() => handleExecuteAdhoc('inspection', template.id)}
                                                            className="px-4 py-2 bg-ep-primary/10 hover:bg-ep-primary text-ep-primary hover:text-white border border-ep-primary/20 hover:border-transparent rounded-xl text-[8px] font-black uppercase italic tracking-widest transition-all"
                                                        >
                                                            Iniciar
                                                        </button>
                                                    </div>
                                                ))}
                                            </div>
                                        )
                                    )}

                                    {activeTab === 'trainings' && (
                                        modalFilteredTemplates.length === 0 ? (
                                            <div className="py-12 text-center text-white/30 italic bg-white/[0.01] border border-white/5 rounded-2xl">
                                                No se encontraron checklists para capacitación que coincidan con la búsqueda.
                                            </div>
                                        ) : (
                                            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                {modalFilteredTemplates.map(template => {
                                                    const isExpanded = selectedChecklistIdForConfig === template.id;
                                                    return (
                                                        <div key={`adhoc-train-${template.id}`} className={`bg-black/20 border rounded-2xl p-5 flex flex-col justify-between transition-all duration-300 ${isExpanded ? 'border-indigo-500/40 bg-[#16162a]/40 col-span-1 md:col-span-2' : 'border-white/5 hover:border-white/10'}`}>
                                                            <div className="flex items-center justify-between gap-4 w-full">
                                                                <div>
                                                                    <div className="text-[7px] font-bold text-indigo-400 uppercase tracking-wider mb-0.5">CAPACITACIÓN AD-HOC</div>
                                                                    <div className="text-xs font-black text-white uppercase italic leading-none">{template.title}</div>
                                                                </div>
                                                                <button
                                                                    onClick={() => {
                                                                        setSelectedChecklistIdForConfig(isExpanded ? null : template.id);
                                                                        if (!isExpanded) {
                                                                            setTrainingForm({ title: template.title, duration: '15', description: '', modality: 'presencial' });
                                                                        }
                                                                    }}
                                                                    className="px-4 py-2 border rounded-xl text-[8px] font-black uppercase italic tracking-widest transition-all bg-indigo-600/10 hover:bg-indigo-600 text-indigo-400 hover:text-white border-indigo-500/20"
                                                                >
                                                                    {isExpanded ? 'Cancelar' : 'Iniciar'}
                                                                </button>
                                                            </div>

                                                            {isExpanded && (
                                                                <div className="mt-5 pt-5 border-t border-indigo-500/10 space-y-4">
                                                                    <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                                        <div className="grid grid-cols-2 gap-2 bg-black/40 border border-white/10 rounded-xl p-1">
                                                                            {['presencial', 'virtual'].map(mod => (
                                                                                <button key={mod} type="button" onClick={() => setTrainingForm({ ...trainingForm, modality: mod })} className={`py-2 px-3 rounded-lg text-[9px] font-black uppercase italic ${trainingForm.modality === mod ? 'bg-indigo-600 text-white' : 'text-white/40'}`}>
                                                                                    {mod === 'presencial' ? 'In Situ (Presencial)' : 'Online (Virtual)'}
                                                                                </button>
                                                                            ))}
                                                                        </div>
                                                                        <select value={trainingForm.duration} onChange={e => setTrainingForm({ ...trainingForm, duration: e.target.value })} className="w-full bg-[#11111d] border border-white/10 rounded-xl px-4 py-3 text-xs text-white italic font-bold [color-scheme:dark]">
                                                                            {['5', '10', '15', '30', '45', '60'].map(d => <option key={d} value={d}>{d} MINUTOS</option>)}
                                                                        </select>
                                                                    </div>
                                                                    <textarea value={trainingForm.description} onChange={e => setTrainingForm({ ...trainingForm, description: e.target.value })} className="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-xs text-white italic font-bold uppercase" rows="2" placeholder="DEFINA LOS CONTENIDOS TÁCTICOS A TRATAR..." />
                                                                    <div className="flex justify-end">
                                                                        <button onClick={() => handleExecuteAdhoc('training', template.id, { title: template.title, modality: trainingForm.modality, duration: trainingForm.duration, description: trainingForm.description })} className="px-6 py-3 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-[9px] font-black uppercase tracking-widest italic">
                                                                            Confirmar & Iniciar
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            )}
                                                        </div>
                                                    );
                                                })}
                                            </div>
                                        )
                                    )}

                                    {activeTab === 'reports' && (
                                        <div className="bg-emerald-500/5 border border-emerald-500/10 rounded-[2rem] p-8 flex flex-col sm:flex-row items-center justify-between gap-6">
                                            <div>
                                                <h5 className="text-xs font-black text-emerald-400 uppercase tracking-widest italic">Informe Técnico Ad-hoc / Libre</h5>
                                                <p className="text-[9px] text-white/40 uppercase tracking-wider mt-0.5">Comienza a redactar un informe maestro mensual libre para esta sede</p>
                                            </div>
                                            <Link href={safeRoute('master-reports.create', { sede_id: selectedSedeId, periodo: `${new Date().getFullYear()}-${String(selectedMonth).padStart(2, '0')}` })} onClick={() => setModalOpen(false)} className="w-full sm:w-auto px-6 h-12 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-[9px] font-black uppercase tracking-widest italic shadow-lg flex items-center justify-center">
                                                Redactar Informe
                                            </Link>
                                        </div>
                                    )}
                                </div>
                            )}
                        </div>
                    ) : (
                        <div className="py-20 text-center text-white/30">
                            <p className="text-xs font-black uppercase italic tracking-widest">Por favor, selecciona una sede para continuar</p>
                        </div>
                    )}
                </div>
            </div>
        </div>
    );
}

function AdhocTemplateFilters({
    modalSearchQuery, setModalSearchQuery,
    modalSelectedCategory, setModalSelectedCategory,
    modalSelectedSubcategory, setModalSelectedSubcategory,
    modalCategories, modalSubcategories, filteredCount, accent,
}) {
    return (
        <div className="bg-[#11111d]/50 border border-white/5 rounded-2xl p-5 space-y-4">
            <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                <input type="text" placeholder="Ej: EPP..." value={modalSearchQuery} onChange={e => setModalSearchQuery(e.target.value)} className="w-full bg-[#16162a] border border-white/10 rounded-xl px-4 py-3 text-xs text-white italic font-bold uppercase" />
                <select value={modalSelectedCategory} onChange={e => { setModalSelectedCategory(e.target.value); setModalSelectedSubcategory(''); }} className="w-full bg-[#16162a] border border-white/10 rounded-xl px-4 py-3 text-xs text-white italic font-bold uppercase [color-scheme:dark]">
                    <option value="">TODAS LAS CATEGORÍAS</option>
                    {modalCategories.map(cat => <option key={cat} value={cat}>{cat.toUpperCase()}</option>)}
                </select>
                <select value={modalSelectedSubcategory} onChange={e => setModalSelectedSubcategory(e.target.value)} disabled={!modalSelectedCategory} className="w-full bg-[#16162a] border border-white/10 rounded-xl px-4 py-3 text-xs text-white italic font-bold uppercase [color-scheme:dark] disabled:opacity-20">
                    <option value="">TODAS LAS SUBCATEGORÍAS</option>
                    {modalSubcategories.map(sub => <option key={sub} value={sub}>{sub.toUpperCase()}</option>)}
                </select>
            </div>
            {(modalSearchQuery || modalSelectedCategory || modalSelectedSubcategory) && (
                <div className="flex items-center justify-between text-[8px] font-bold text-white/20 uppercase tracking-widest">
                    <span>Resultados Encontrados: {filteredCount}</span>
                    <button onClick={() => { setModalSearchQuery(''); setModalSelectedCategory(''); setModalSelectedSubcategory(''); }} className={`text-${accent}-400 hover:text-white uppercase`}>
                        Limpiar Filtros
                    </button>
                </div>
            )}
        </div>
    );
}
