import { router, Head, Link, useForm, usePage } from '@inertiajs/react';
import AuthenticatedLayout from '../../../Layouts/AuthenticatedLayout';
import { useState, useEffect } from 'react';

export default function Builder({ checklist, categories }) {
    const { auth, flash } = usePage().props;
    
    // Estado local para subcategorías (depende de la categoría seleccionada)
    const [availableSubcategories, setAvailableSubcategories] = useState([]);
    
    // Formulario principal
    const form = useForm({
        id: checklist?.id || null,
        title: checklist?.title || '',
        description: checklist?.description || '',
        category_id: checklist?.category_id || '',
        subcategory_id: checklist?.subcategory_id || '',
        registry_code: checklist?.registry_code || '',
        is_active: checklist ? checklist.is_active : true,
        useSections: checklist?.sections?.length > 0,
        sections: checklist?.sections?.map(s => ({
            id: s.id,
            tempId: null,
            title: s.title,
            description: s.description || '',
        })) || [],
        questions: checklist?.questions?.map(q => ({
            id: q.id,
            label: q.label,
            type: q.type,
            options: q.options || [],
            min_value: q.min_value,
            max_value: q.max_value,
            failure_values: q.failure_values || [],
            failure_logic: q.failure_logic || 'fail_if_selected',
            is_required: q.is_required === 1 || q.is_required === true,
            help_text: q.help_text || '',
            section_id: q.section_id,
            section_tempId: null,
        })) || [],
    });

    // Sincronizar subcategorías cuando cambia la categoría
    useEffect(() => {
        if (form.data.category_id) {
            const cat = categories.find(c => c.id == form.data.category_id);
            setAvailableSubcategories(cat?.subcategories || []);
        } else {
            setAvailableSubcategories([]);
        }
    }, [form.data.category_id, categories]);

    // --- Manejo de Secciones ---
    const addSection = () => {
        const tempId = 'temp_' + Date.now();
        form.setData('sections', [...form.data.sections, {
            id: null,
            tempId: tempId,
            title: 'Nueva Sección',
            description: '',
        }]);
    };

    const removeSection = (index) => {
        const section = form.data.sections[index];
        const newSections = [...form.data.sections];
        newSections.splice(index, 1);
        
        // Desvincular preguntas
        const newQuestions = form.data.questions.map(q => {
            if (q.section_id === section.id || q.section_tempId === section.tempId) {
                return { ...q, section_id: null, section_tempId: null };
            }
            return q;
        });

        form.setData({
            ...form.data,
            sections: newSections,
            questions: newQuestions,
        });
    };

    const updateSection = (index, field, value) => {
        const newSections = [...form.data.sections];
        newSections[index][field] = value;
        form.setData('sections', newSections);
    };

    // --- Manejo de Preguntas ---
    const addQuestion = (sectionTempId = null, sectionId = null) => {
        form.setData('questions', [...form.data.questions, {
            id: null,
            label: '',
            type: 'text',
            options: [],
            failure_values: [],
            failure_logic: 'fail_if_selected',
            is_required: false,
            help_text: '',
            section_tempId: sectionTempId,
            section_id: sectionId,
        }]);
    };

    const removeQuestion = (index) => {
        const newQuestions = [...form.data.questions];
        newQuestions.splice(index, 1);
        form.setData('questions', newQuestions);
    };

    const updateQuestion = (index, field, value) => {
        const newQuestions = [...form.data.questions];
        newQuestions[index][field] = value;
        form.setData('questions', newQuestions);
    };

    const moveQuestion = (index, direction) => {
        if (direction === 'up' && index > 0) {
            const newQuestions = [...form.data.questions];
            [newQuestions[index-1], newQuestions[index]] = [newQuestions[index], newQuestions[index-1]];
            form.setData('questions', newQuestions);
        } else if (direction === 'down' && index < form.data.questions.length - 1) {
            const newQuestions = [...form.data.questions];
            [newQuestions[index+1], newQuestions[index]] = [newQuestions[index], newQuestions[index+1]];
            form.setData('questions', newQuestions);
        }
    };

    const submit = (e) => {
        e.preventDefault();
        form.post(route('gestor.checklists.save'));
    };

    return (
        <AuthenticatedLayout title="Ingeniería de Matrices">
            <Head title={checklist ? 'Editar Matrix' : 'Nueva Matrix'} />

            <div className="px-4 sm:px-6 lg:px-8 py-8 text-white selection:bg-orange-500/30">
                <form onSubmit={submit} className="max-w-[100rem] mx-auto space-y-8">
                    
                    {/* HUB DE CONFIGURACIÓN BASE */}
                    <div className="bg-[#16162a]/40 backdrop-blur-md rounded-[3rem] border border-white/5 p-10 shadow-2xl relative overflow-hidden animate-fade-in-down">
                        <div className="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-transparent via-orange-500 to-transparent"></div>
                        
                        <div className="flex flex-col lg:flex-row gap-10">
                            <div className="flex-1 space-y-8">
                                <div className="flex items-center gap-4 border-b border-white/5 pb-6">
                                    <Link href={route('gestor.checklists.index')} className="w-10 h-10 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center text-white/40 hover:text-white transition-all">
                                        <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="3" d="M15 19l-7-7 7-7"/></svg>
                                    </Link>
                                    <h3 className="text-xl font-black italic uppercase tracking-widest">{checklist ? 'Sincronización de Matrix' : 'Diseño de Nueva Matrix'}</h3>
                                </div>

                                <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div className="relative group">
                                        <span className="absolute -top-2.5 left-4 bg-[#11111d] px-2 text-[8px] font-black text-orange-400 uppercase tracking-[0.3em] italic z-10">Título de Matriz</span>
                                        <input type="text" value={form.data.title} onChange={e => form.setData('title', e.target.value)} required 
                                            className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-orange-500/50 transition-all uppercase"/>
                                    </div>
                                    <div className="relative group">
                                        <span className="absolute -top-2.5 left-4 bg-[#11111d] px-2 text-[8px] font-black text-orange-400 uppercase tracking-[0.3em] italic z-10">Código Registral</span>
                                        <input type="text" value={form.data.registry_code} onChange={e => form.setData('registry_code', e.target.value)} 
                                            className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-orange-500/50 transition-all uppercase"/>
                                    </div>
                                </div>

                                <div className="relative group">
                                    <span className="absolute -top-2.5 left-4 bg-[#11111d] px-2 text-[8px] font-black text-orange-400 uppercase tracking-[0.3em] italic z-10">Alcance / Descripción</span>
                                    <textarea value={form.data.description} onChange={e => form.setData('description', e.target.value)} rows="2"
                                        className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-orange-500/50 transition-all uppercase"></textarea>
                                </div>
                            </div>

                            <div className="lg:w-80 space-y-6">
                                <div className="bg-white/5 rounded-[2.5rem] p-8 border border-white/5 space-y-6">
                                    <div className="relative group">
                                        <span className="absolute -top-2.5 left-4 bg-[#16162a] px-2 text-[8px] font-black text-orange-400 uppercase tracking-[0.3em] italic z-10">Clasificación Principal</span>
                                        <select value={form.data.category_id} onChange={e => form.setData('category_id', e.target.value)} 
                                            className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-xs font-bold text-white focus:ring-2 focus:ring-orange-500/50 transition-all [color-scheme:dark] italic uppercase">
                                            <option value="">SELECCIONE CATEGORÍA...</option>
                                            {categories.map(cat => <option key={cat.id} value={cat.id}>{cat.name}</option>)}
                                        </select>
                                    </div>
                                    <div className="relative group">
                                        <span className="absolute -top-2.5 left-4 bg-[#16162a] px-2 text-[8px] font-black text-orange-400 uppercase tracking-[0.3em] italic z-10">Sub-Estrategia</span>
                                        <select value={form.data.subcategory_id} onChange={e => form.setData('subcategory_id', e.target.value)} 
                                            className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-xs font-bold text-white focus:ring-2 focus:ring-orange-500/50 transition-all [color-scheme:dark] italic uppercase">
                                            <option value="">SELECCIONE SUBCATEGORÍA...</option>
                                            {availableSubcategories.map(sub => <option key={sub.id} value={sub.id}>{sub.name}</option>)}
                                        </select>
                                    </div>

                                    <div className="flex items-center gap-4 bg-black/20 px-6 py-4 rounded-2xl border border-white/5">
                                        <label className="inline-flex items-center cursor-pointer group">
                                            <input type="checkbox" checked={form.data.is_active} onChange={e => form.setData('is_active', e.target.checked)} className="sr-only peer" />
                                            <div className="relative w-11 h-6 bg-white/5 border border-white/10 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white/20 after:border-white/10 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500/50"></div>
                                            <span className="ms-3 text-[10px] font-black text-white/40 uppercase tracking-widest italic group-hover:text-emerald-400 transition-colors">Estado Matrix</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* CONSTRUCTOR DE ESTRUCTURA */}
                    <div className="space-y-6">
                        <div className="flex items-center justify-between px-6">
                            <div className="flex items-center gap-4">
                                <h4 className="text-[12px] font-black text-white/40 uppercase tracking-[0.5em] italic">Esqueleto de Matrix</h4>
                                <div className="h-px w-32 bg-white/5"></div>
                            </div>
                            <div className="flex gap-4">
                                <button type="button" onClick={() => form.setData('useSections', !form.data.useSections)} className={`px-6 py-3 rounded-xl text-[9px] font-black uppercase tracking-widest italic border transition-all ${form.data.useSections ? 'bg-orange-500/20 border-orange-500 text-orange-400' : 'bg-white/5 border-white/5 text-white/10 hover:text-white'}`}>
                                    {form.data.useSections ? 'Deshabilitar Secciones' : 'Habilitar Secciones'}
                                </button>
                                <button type="button" onClick={() => addQuestion()} className="px-6 py-3 bg-white/5 border border-white/10 rounded-xl text-[9px] font-black uppercase tracking-widest italic text-white hover:bg-white/10 transition-all flex items-center gap-2">
                                    <svg className="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4" strokeWidth="3"/></svg>
                                    Pregunta Suelta
                                </button>
                                {form.data.useSections && (
                                    <button type="button" onClick={addSection} className="px-8 py-3 bg-orange-600 text-white rounded-xl text-[9px] font-black uppercase tracking-widest italic transition-all shadow-lg hover:brightness-110">
                                        Inyectar Sección
                                    </button>
                                )}
                            </div>
                        </div>

                        {/* LISTADO DE SECCIONES Y PREGUNTAS */}
                        <div className="space-y-12 pb-32">
                            {/* Preguntas sin sección */}
                            <div className="space-y-4">
                                {form.data.questions.filter(q => !q.section_id && !q.section_tempId).map((question, i) => {
                                    const realIndex = form.data.questions.indexOf(question);
                                    return <QuestionItem key={realIndex} index={realIndex} question={question} update={updateQuestion} remove={removeQuestion} move={moveQuestion} />;
                                })}
                            </div>

                            {/* Bloques de Secciones */}
                            {form.data.useSections && form.data.sections.map((section, sectionIndex) => (
                                <div key={section.tempId || section.id} className="bg-[#16162a]/60 backdrop-blur-md rounded-[2.5rem] border border-white/5 overflow-hidden animate-fade-in-up">
                                    <div className="bg-white/5 px-8 py-6 flex items-center justify-between">
                                        <div className="flex-1 flex gap-6 items-center">
                                            <div className="w-10 h-10 rounded-xl bg-orange-500/20 flex items-center justify-center text-[11px] font-black text-orange-400 italic border border-orange-500/20">{sectionIndex + 1}</div>
                                            <input 
                                                type="text" 
                                                value={section.title} 
                                                onChange={e => updateSection(sectionIndex, 'title', e.target.value)}
                                                className="bg-transparent border-none text-lg font-black text-white italic uppercase focus:ring-0 w-1/2 p-0 placeholder-white/10"
                                                placeholder="NOMBRE DE LA SECCIÓN..." 
                                            />
                                        </div>
                                        <div className="flex items-center gap-4">
                                            <button type="button" onClick={() => addQuestion(section.tempId, section.id)} className="h-9 px-6 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 hover:bg-emerald-500 hover:text-white rounded-xl text-[8px] font-black uppercase tracking-widest italic transition-all">Anclar Pregunta</button>
                                            <button type="button" onClick={() => removeSection(sectionIndex)} className="h-9 w-9 bg-red-500/10 border border-red-500/20 text-red-500 hover:bg-red-500 hover:text-white rounded-xl flex items-center justify-center transition-all">
                                                <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" strokeWidth="2"/></svg>
                                            </button>
                                        </div>
                                    </div>
                                    <div className="p-8 space-y-4 bg-black/20">
                                        {form.data.questions.filter(q => (section.id && q.section_id === section.id) || (section.tempId && q.section_tempId === section.tempId)).map((question, i) => {
                                            const realIndex = form.data.questions.indexOf(question);
                                            return <QuestionItem key={realIndex} index={realIndex} question={question} update={updateQuestion} remove={removeQuestion} move={moveQuestion} />;
                                        })}
                                        {form.data.questions.filter(q => (section.id && q.section_id === section.id) || (section.tempId && q.section_tempId === section.tempId)).length === 0 && (
                                            <div className="py-12 border-2 border-dashed border-white/5 rounded-[2rem] flex flex-col items-center gap-4 opacity-20">
                                                <svg className="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" strokeWidth="2"/></svg>
                                                <span className="text-[10px] font-black uppercase tracking-widest italic">Sección vacía - Inyectar preguntas</span>
                                            </div>
                                        )}
                                    </div>
                                </div>
                            ))}
                        </div>
                    </div>

                    {/* BOTONES DE ACCIÓN FLOTANTES */}
                    <div className="fixed bottom-10 left-1/2 -translate-x-1/2 z-40 flex gap-6 px-10 py-6 bg-[#16162a]/80 backdrop-blur-2xl rounded-[3rem] border border-white/10 shadow-[0_30px_60px_rgba(0,0,0,0.5)] animate-fade-in-up">
                        <Link href={route('gestor.checklists.index')} className="px-10 py-5 bg-white/5 text-white/40 hover:text-white rounded-[2rem] text-[11px] font-black uppercase tracking-widest italic transition-all">Abortar Misión</Link>
                        <button type="submit" disabled={form.processing} className="px-16 py-5 bg-emerald-600 text-white rounded-[2rem] text-[11px] font-black uppercase tracking-[0.2em] italic transition-all shadow-xl shadow-emerald-600/20 hover:brightness-110 active:scale-95 disabled:opacity-30">
                            {form.processing ? 'SINCRONIZANDO...' : 'CONFIRMAR MATRIZ'}
                        </button>
                    </div>

                </form>
            </div>

            <style>{`
                .animate-fade-in-down { animation: fade-in-down 0.6s ease-out forwards; }
                .animate-fade-in-up { animation: fade-in-up 0.5s ease-out forwards; }
                @keyframes fade-in-down { from { opacity: 0; transform: translateY(-30px); } to { opacity: 1; transform: translateY(0); } }
                @keyframes fade-in-up { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
            `}</style>
        </AuthenticatedLayout>
    );
}

function QuestionItem({ index, question, update, remove, move }) {
    const [optionInput, setOptionInput] = useState('');

    const addOption = () => {
        if (optionInput.trim()) {
            update(index, 'options', [...question.options, optionInput.trim()]);
            setOptionInput('');
        }
    };

    const removeOption = (idx) => {
        const newOpts = [...question.options];
        newOpts.splice(idx, 1);
        update(index, 'options', newOpts);
        
        // También remover de failure_values si aplica
        const val = question.options[idx];
        const newFailures = question.failure_values.filter(v => v !== val);
        update(index, 'failure_values', newFailures);
    };

    const toggleFailure = (val) => {
        const exists = question.failure_values.includes(val);
        if (exists) {
            update(index, 'failure_values', question.failure_values.filter(v => v !== val));
        } else {
            update(index, 'failure_values', [...question.failure_values, val]);
        }
    };

    return (
        <div className="bg-[#11111d]/60 border border-white/5 rounded-[2rem] p-8 space-y-8 relative group/card hover:border-orange-500/20 transition-all duration-300 shadow-xl">
            <div className="flex flex-col lg:flex-row gap-8">
                <div className="flex-1 space-y-6">
                    <div className="flex items-start gap-4">
                        <div className="w-8 h-8 rounded-lg bg-orange-500/10 border border-orange-500/20 flex items-center justify-center text-[10px] font-black text-orange-400 italic">#{index + 1}</div>
                        <div className="flex-1 relative group">
                            <span className="absolute -top-2.5 left-4 bg-[#11111d] px-2 text-[7px] font-black text-orange-400/40 uppercase tracking-[0.3em] italic z-10">Enunciado / Reactivo</span>
                            <input type="text" value={question.label} onChange={e => update(index, 'label', e.target.value)} placeholder="¿QUÉ SE DEBE VERIFICAR?" className="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-sm font-bold text-white focus:ring-1 focus:ring-orange-500 transition-all uppercase placeholder-white/5"/>
                        </div>
                    </div>
                    
                    <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div className="relative group">
                            <span className="absolute -top-2.5 left-4 bg-[#11111d] px-2 text-[7px] font-black text-orange-400/40 uppercase tracking-[0.3em] italic z-10">Tipo de Respuesta</span>
                            <select value={question.type} onChange={e => update(index, 'type', e.target.value)} className="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-xs font-bold text-white focus:ring-1 focus:ring-orange-500 transition-all [color-scheme:dark] italic uppercase">
                                <option value="text">Texto Corto</option>
                                <option value="textarea">Texto Extendido</option>
                                <option value="select">Lista Desplegable</option>
                                <option value="radio">Opción Única (Radio)</option>
                                <option value="checkbox">Opción Múltiple (Check)</option>
                                <option value="number">Numérico</option>
                                <option value="date">Fecha / Calendario</option>
                            </select>
                        </div>
                        <div className="relative group">
                            <span className="absolute -top-2.5 left-4 bg-[#11111d] px-2 text-[7px] font-black text-orange-400/40 uppercase tracking-[0.3em] italic z-10">Ayuda al Operador</span>
                            <input type="text" value={question.help_text} onChange={e => update(index, 'help_text', e.target.value)} placeholder="GUÍA RÁPIDA..." className="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-[10px] font-bold text-white/60 focus:ring-1 focus:ring-orange-500 transition-all uppercase placeholder-white/5"/>
                        </div>
                        <div className="flex items-center gap-4 bg-black/20 px-6 py-2 rounded-xl border border-white/5">
                            <label className="inline-flex items-center cursor-pointer group">
                                <input type="checkbox" checked={question.is_required} onChange={e => update(index, 'is_required', e.target.checked)} className="sr-only peer" />
                                <div className="relative w-8 h-4 bg-white/5 border border-white/10 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[1px] after:start-[1px] after:bg-white/20 after:border-white/10 after:border after:rounded-full after:h-3.5 after:w-3.5 after:transition-all peer-checked:bg-orange-500/50"></div>
                                <span className="ms-3 text-[8px] font-black text-white/20 uppercase tracking-widest italic group-hover:text-orange-400 transition-colors">Obligatorio</span>
                            </label>
                        </div>
                    </div>
                </div>

                {/* OPCIONES (Solo si es select/radio/checkbox) */}
                {(['select', 'radio', 'checkbox'].includes(question.type)) && (
                    <div className="lg:w-80 space-y-4 pt-2">
                        <div className="relative group">
                            <span className="absolute -top-2.5 left-4 bg-[#11111d] px-2 text-[7px] font-black text-emerald-400 uppercase tracking-[0.3em] italic z-10">Inyectar Opción</span>
                            <div className="flex gap-2">
                                <input type="text" value={optionInput} onChange={e => setOptionInput(e.target.value)} onKeyDown={e => e.key === 'Enter' && (e.preventDefault(), addOption())} className="flex-1 bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-xs font-bold text-white focus:ring-1 focus:ring-emerald-500 transition-all uppercase outline-none"/>
                                <button type="button" onClick={addOption} className="w-10 h-10 bg-emerald-500/20 border border-emerald-500/30 text-emerald-400 rounded-xl flex items-center justify-center hover:bg-emerald-500 hover:text-white transition-all">
                                    <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4" strokeWidth="3"/></svg>
                                </button>
                            </div>
                        </div>
                        <div className="flex flex-wrap gap-2">
                            {question.options.map((opt, oi) => (
                                <div key={oi} className={`group/opt flex items-center gap-2 pl-3 pr-1 py-1 rounded-lg border transition-all ${question.failure_values.includes(opt) ? 'bg-red-500/10 border-red-500/30 text-red-500' : 'bg-white/5 border-white/10 text-white/40'}`}>
                                    <span onClick={() => toggleFailure(opt)} className="text-[9px] font-black uppercase cursor-pointer">{opt}</span>
                                    <button type="button" onClick={() => removeOption(oi)} className="w-5 h-5 flex items-center justify-center hover:text-red-500 transition-colors">
                                        <svg className="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" strokeWidth="3"/></svg>
                                    </button>
                                </div>
                            ))}
                            {question.options.length === 0 && <div className="text-[8px] font-black text-white/10 italic p-2">Sin opciones definidas</div>}
                        </div>
                    </div>
                )}

                {/* NUMERICOS */}
                {question.type === 'number' && (
                    <div className="lg:w-80 grid grid-cols-2 gap-4 pt-2">
                         <div className="relative group">
                            <span className="absolute -top-2.5 left-4 bg-[#11111d] px-2 text-[7px] font-black text-white/20 uppercase tracking-[0.3em] italic z-10">Mínimo</span>
                            <input type="number" value={question.min_value || ''} onChange={e => update(index, 'min_value', e.target.value)} className="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-xs text-white outline-none"/>
                        </div>
                        <div className="relative group">
                            <span className="absolute -top-2.5 left-4 bg-[#11111d] px-2 text-[7px] font-black text-white/20 uppercase tracking-[0.3em] italic z-10">Máximo</span>
                            <input type="number" value={question.max_value || ''} onChange={e => update(index, 'max_value', e.target.value)} className="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-xs text-white outline-none"/>
                        </div>
                    </div>
                )}
            </div>

            {/* CONTROLES CARD */}
            <div className="absolute top-6 right-6 opacity-0 group-hover/card:opacity-100 transition-all flex border border-white/5 rounded-xl bg-black/40 divide-x divide-white/5 overflow-hidden">
                <button type="button" onClick={() => move(index, 'up')} className="w-9 h-10 flex items-center justify-center hover:bg-white/5 text-white/20 hover:text-white transition-all"><svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 15l7-7 7 7" strokeWidth="2"/></svg></button>
                <button type="button" onClick={() => move(index, 'down')} className="w-9 h-10 flex items-center justify-center hover:bg-white/5 text-white/20 hover:text-white transition-all"><svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" strokeWidth="2"/></svg></button>
                <button type="button" onClick={() => remove(index)} className="w-9 h-10 flex items-center justify-center hover:bg-white/5 text-red-500/40 hover:text-red-500 transition-all"><svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" strokeWidth="2"/></svg></button>
            </div>
        </div>
    );
}
