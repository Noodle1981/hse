import { router, Head, Link, useForm, usePage } from '@inertiajs/react';
import AuthenticatedLayout from '../../../Layouts/AuthenticatedLayout';
import { useState, useEffect } from 'react';

export default function Builder({ checklist, categories }) {
    const { auth, flash } = usePage().props;
    
    // Estado local para subcategorías (depende de la categoría seleccionada)
    const [availableSubcategories, setAvailableSubcategories] = useState([]);

    // Estados para Modales
    const [showCategoryModal, setShowCategoryModal] = useState(false);
    const [showSubcategoryModal, setShowSubcategoryModal] = useState(false);

    // Formularios para Modales
    const categoryForm = useForm({
        code: '',
        name: '',
        description: '',
        color: '#f69239',
        icon: 'tag',
    });

    const subcategoryForm = useForm({
        category_id: '',
        code: '',
        name: '',
        description: '',
    });

    const submitCategory = (e) => {
        e.preventDefault();
        categoryForm.post(route('gestor.categories.save', { redirect_back: 1 }), {
            preserveScroll: true,
            onSuccess: () => {
                setShowCategoryModal(false);
                categoryForm.reset();
            }
        });
    };

    const submitSubcategory = (e) => {
        e.preventDefault();
        subcategoryForm.post(route('gestor.categories.subcategories.save', { category: subcategoryForm.data.category_id }), {
            preserveScroll: true,
            onSuccess: () => {
                setShowSubcategoryModal(false);
                subcategoryForm.reset();
            }
        });
    };
    
    // Formulario principal
    const form = useForm({
        id: checklist?.id || null,
        title: checklist?.title || '',
        description: checklist?.description || '',
        category_id: checklist?.category_id || '',
        subcategory_id: checklist?.subcategory_id || '',
        registry_code: checklist?.registry_code || '',
        is_active: checklist ? checklist.is_active : true,
        sections: (checklist?.sections && checklist.sections.length > 0) ? checklist.sections.map(s => ({
            id: s.id,
            tempId: null,
            title: s.title,
            description: s.description || '',
        })) : [
            {
                id: null,
                tempId: 'temp_default_section',
                title: 'Inspección General',
                description: 'Sección principal para las preguntas.',
            }
        ],
        questions: checklist?.questions?.map(q => {
            let parsedOptions = [];
            if (Array.isArray(q.options)) parsedOptions = q.options;
            else if (typeof q.options === 'string') {
                try { parsedOptions = JSON.parse(q.options) || []; } catch(e) { parsedOptions = []; }
            }

            let parsedFailures = [];
            if (Array.isArray(q.failure_values)) parsedFailures = q.failure_values;
            else if (typeof q.failure_values === 'string') {
                try { parsedFailures = JSON.parse(q.failure_values) || []; } catch(e) { parsedFailures = []; }
            }

            const hasSections = checklist?.sections && checklist.sections.length > 0;
            let sectionId = q.section_id;
            let sectionTempId = null;

            if (!sectionId) {
                if (hasSections) {
                    sectionId = checklist.sections[0].id;
                } else {
                    sectionTempId = 'temp_default_section';
                }
            }

            return {
                id: q.id,
                label: q.label,
                type: q.type,
                options: parsedOptions,
                min_value: q.min_value,
                max_value: q.max_value,
                failure_values: parsedFailures,
                failure_logic: q.failure_logic || 'fail_if_selected',
                is_required: q.is_required === 1 || q.is_required === true,
                help_text: q.help_text || '',
                section_id: sectionId,
                section_tempId: sectionTempId,
            };
        }) || [],
    });

    // Sincronizar subcategorías cuando cambia la categoría
    useEffect(() => {
        if (form.data.category_id) {
            const cat = categories.find(c => c.id == form.data.category_id);
            setAvailableSubcategories(cat?.subcategories || []);
            subcategoryForm.setData('category_id', form.data.category_id);
        } else {
            setAvailableSubcategories([]);
            subcategoryForm.setData('category_id', '');
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
        <AuthenticatedLayout title="Constructor de Checklists">
            <Head title={checklist ? 'Editar Checklist' : 'Nuevo Checklist'} />

            <div className="px-4 sm:px-6 lg:px-8 py-8 text-white selection:bg-orange-500/30">
                <form onSubmit={submit} className="max-w-[100rem] mx-auto space-y-8">
                    
                    {/* ERRORES DE VALIDACIÓN GENERALES */}
                    {Object.keys(form.errors).length > 0 && (
                        <div className="bg-red-500/10 border border-red-500/30 text-red-500 p-6 rounded-[2rem] animate-fade-in-down">
                            <h4 className="font-black uppercase tracking-widest text-[10px] mb-2 italic flex items-center gap-2">
                                <span className="w-2 h-2 bg-red-500 rounded-full animate-ping"></span>
                                ⚠️ Se detectaron errores de validación:
                            </h4>
                            <ul className="list-disc pl-6 text-xs font-bold space-y-1 italic uppercase">
                                {Object.entries(form.errors).map(([key, value]) => (
                                    <li key={key}>{value}</li>
                                ))}
                            </ul>
                        </div>
                    )}
                    
                    {/* HUB DE CONFIGURACIÓN BASE */}
                    <div className="bg-[#16162a]/40 backdrop-blur-md rounded-[3rem] border border-white/5 p-10 shadow-2xl relative overflow-hidden animate-fade-in-down">
                        <div className="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-transparent via-orange-500 to-transparent"></div>
                        
                        <div className="flex flex-col lg:flex-row gap-10">
                            <div className="flex-1 space-y-8">
                                <div className="flex items-center gap-4 border-b border-white/5 pb-6">
                                    <Link href={route('gestor.checklists.index')} className="w-10 h-10 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center text-white/40 hover:text-white transition-all">
                                        <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="3" d="M15 19l-7-7 7-7"/></svg>
                                    </Link>
                                    <h3 className="text-xl font-black italic uppercase tracking-widest">{checklist ? 'Editar Checklist' : 'Diseño de Nuevo Checklist'}</h3>
                                </div>

                                <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div className="flex flex-col gap-1.5">
                                        <label htmlFor="title" className="pl-3 text-[8px] font-black text-orange-400 uppercase tracking-[0.3em] italic">Título de Checklist</label>
                                        <input id="title" name="title" type="text" value={form.data.title} onChange={e => form.setData('title', e.target.value)} required 
                                            className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-orange-500/50 transition-all uppercase"/>
                                        {form.errors.title && <span className="text-red-500 text-[9px] font-black italic ml-4 mt-1 block">{form.errors.title}</span>}
                                    </div>
                                    <div className="flex flex-col gap-1.5">
                                        <label htmlFor="registry_code" className="pl-3 text-[8px] font-black text-orange-400 uppercase tracking-[0.3em] italic">Código Registral</label>
                                        <input id="registry_code" name="registry_code" type="text" value={form.data.registry_code} onChange={e => form.setData('registry_code', e.target.value)} 
                                            className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-orange-500/50 transition-all uppercase"/>
                                        {form.errors.registry_code && <span className="text-red-500 text-[9px] font-black italic ml-4 mt-1 block">{form.errors.registry_code}</span>}
                                    </div>
                                </div>

                                <div className="flex flex-col gap-1.5">
                                    <label htmlFor="description" className="pl-3 text-[8px] font-black text-orange-400 uppercase tracking-[0.3em] italic">Alcance / Descripción</label>
                                    <textarea id="description" name="description" value={form.data.description} onChange={e => form.setData('description', e.target.value)} rows="2"
                                        className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-orange-500/50 transition-all uppercase"></textarea>
                                    {form.errors.description && <span className="text-red-500 text-[9px] font-black italic ml-4 mt-1 block">{form.errors.description}</span>}
                                </div>
                            </div>

                            <div className="lg:w-80 space-y-6">
                                <div className="bg-white/5 rounded-[2.5rem] p-8 border border-white/5 space-y-6">
                                    <div className="flex flex-col gap-1.5">
                                        <label htmlFor="category_id" className="pl-3 text-[8px] font-black text-orange-400 uppercase tracking-[0.3em] italic">Clasificación Principal</label>
                                        <div className="flex gap-2">
                                            <select id="category_id" name="category_id" value={form.data.category_id} onChange={e => form.setData('category_id', e.target.value)} 
                                                className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-xs font-bold text-white focus:ring-2 focus:ring-orange-500/50 transition-all [color-scheme:dark] italic uppercase">
                                                <option value="">SELECCIONE CATEGORÍA...</option>
                                                {categories.map(cat => <option key={cat.id} value={cat.id}>{cat.name}</option>)}
                                            </select>
                                            <button type="button" onClick={() => setShowCategoryModal(true)} className="px-4 bg-orange-600 text-white rounded-2xl font-black text-lg hover:brightness-110 active:scale-95 transition-all shadow-[0_0_15px_rgba(246,146,57,0.3)] flex items-center justify-center">+</button>
                                        </div>
                                        {form.errors.category_id && <span className="text-red-500 text-[9px] font-black italic ml-4 mt-1 block">{form.errors.category_id}</span>}
                                    </div>
                                    <div className="flex flex-col gap-1.5">
                                        <label htmlFor="subcategory_id" className="pl-3 text-[8px] font-black text-orange-400 uppercase tracking-[0.3em] italic">Sub-Clasificación</label>
                                        <div className="flex gap-2">
                                            <select id="subcategory_id" name="subcategory_id" value={form.data.subcategory_id} onChange={e => form.setData('subcategory_id', e.target.value)} 
                                                className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-xs font-bold text-white focus:ring-2 focus:ring-orange-500/50 transition-all [color-scheme:dark] italic uppercase"
                                                disabled={!form.data.category_id}>
                                                <option value="">SELECCIONE SUBCATEGORÍA...</option>
                                                {availableSubcategories.map(sub => <option key={sub.id} value={sub.id}>{sub.name}</option>)}
                                            </select>
                                            <button type="button" onClick={() => form.data.category_id ? setShowSubcategoryModal(true) : alert('Seleccione una categoría primero')} className={`px-4 text-white rounded-2xl font-black text-lg transition-all flex items-center justify-center ${form.data.category_id ? 'bg-orange-600 hover:brightness-110 active:scale-95 shadow-[0_0_15px_rgba(246,146,57,0.3)]' : 'bg-white/10 cursor-not-allowed text-white/30'}`}>+</button>
                                        </div>
                                        {form.errors.subcategory_id && <span className="text-red-500 text-[9px] font-black italic ml-4 mt-1 block">{form.errors.subcategory_id}</span>}
                                    </div>

                                    <div className="flex items-center gap-4 bg-black/20 px-6 py-4 rounded-2xl border border-white/5">
                                        <label className="inline-flex items-center cursor-pointer group">
                                            <input id="is_active" name="is_active" type="checkbox" checked={form.data.is_active} onChange={e => form.setData('is_active', e.target.checked)} className="sr-only peer" />
                                            <div className="relative w-11 h-6 bg-white/5 border border-white/10 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white/20 after:border-white/10 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500/50"></div>
                                            <span className="ms-3 text-[10px] font-black text-white/40 uppercase tracking-widest italic group-hover:text-emerald-400 transition-colors">Estado Checklist</span>
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
                                <h4 className="text-[12px] font-black text-white/40 uppercase tracking-[0.5em] italic">Esqueleto de Checklist</h4>
                                <div className="h-px w-32 bg-white/5"></div>
                            </div>
                            <div className="flex gap-4">
                                <button type="button" onClick={addSection} className="px-8 py-3 bg-orange-600 text-white rounded-xl text-[9px] font-black uppercase tracking-widest italic transition-all shadow-lg hover:brightness-110">
                                    Inyectar Sección
                                </button>
                            </div>
                        </div>

                        {/* LISTADO DE SECCIONES Y PREGUNTAS */}
                        <div className="space-y-12 pb-32">
                            {/* Bloques de Secciones */}
                            {form.data.sections.map((section, sectionIndex) => (
                                <div key={section.tempId || section.id} className="bg-[#16162a]/60 backdrop-blur-md rounded-[2.5rem] border border-white/5 overflow-hidden animate-fade-in-up">
                                    <div className="bg-white/5 px-8 py-6 flex items-center justify-between">
                                        <div className="flex-1 flex gap-6 items-center">
                                            <div className="w-10 h-10 rounded-xl bg-orange-500/20 flex items-center justify-center text-[11px] font-black text-orange-400 italic border border-orange-500/20">{sectionIndex + 1}</div>
                                            <input id={`section_title_${sectionIndex}`} name={`section_title_${sectionIndex}`}
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
                            {form.processing ? 'GUARDANDO...' : 'GUARDAR CHECKLIST'}
                        </button>
                    </div>

                </form>

                {/* MODALES PARA CATEGORÍAS */}
                {showCategoryModal && (
                    <div className="fixed inset-0 z-[100] flex items-center justify-center bg-black/80 backdrop-blur-sm p-4">
                        <div className="bg-[#16162a] border border-white/10 rounded-[2rem] w-full max-w-lg p-8 shadow-2xl relative">
                            <div className="absolute top-4 right-4">
                                <button onClick={() => setShowCategoryModal(false)} className="w-8 h-8 flex items-center justify-center text-white/40 hover:text-white bg-white/5 rounded-full hover:bg-white/10 transition-colors">
                                    <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" strokeWidth="2"/></svg>
                                </button>
                            </div>
                            <h3 className="text-xl font-black italic uppercase text-white mb-6">Nueva Categoría</h3>
                            <form onSubmit={submitCategory} className="space-y-6">
                                <div className="flex flex-col gap-1.5">
        <label className="pl-3 text-[8px] font-black text-orange-400 uppercase tracking-[0.3em] italic">Código</label>
                                    <input type="text" value={categoryForm.data.code} onChange={e => categoryForm.setData('code', e.target.value)} required className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm text-white focus:ring-2 focus:ring-orange-500/50 outline-none" placeholder="Ej. SEG-01"/>
                                </div>
                                <div className="flex flex-col gap-1.5">
        <label className="pl-3 text-[8px] font-black text-orange-400 uppercase tracking-[0.3em] italic">Nombre</label>
                                    <input type="text" value={categoryForm.data.name} onChange={e => categoryForm.setData('name', e.target.value)} required className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm text-white focus:ring-2 focus:ring-orange-500/50 outline-none" placeholder="Ej. Seguridad"/>
                                </div>
                                <div className="flex gap-4">
                                    <div className="flex-1 flex flex-col gap-1.5">
        <label className="pl-3 text-[8px] font-black text-orange-400 uppercase tracking-[0.3em] italic">Color Hex</label>
                                        <input type="text" value={categoryForm.data.color} onChange={e => categoryForm.setData('color', e.target.value)} className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm text-white focus:ring-2 focus:ring-orange-500/50 outline-none"/>
                                    </div>
                                </div>
                                <button type="submit" disabled={categoryForm.processing} className="w-full py-4 bg-orange-600 text-white rounded-2xl font-black uppercase tracking-widest text-sm hover:brightness-110 transition-all shadow-[0_0_15px_rgba(246,146,57,0.3)]">
                                    {categoryForm.processing ? 'Guardando...' : 'Crear Categoría'}
                                </button>
                            </form>
                        </div>
                    </div>
                )}

                {showSubcategoryModal && (
                    <div className="fixed inset-0 z-[100] flex items-center justify-center bg-black/80 backdrop-blur-sm p-4">
                        <div className="bg-[#16162a] border border-white/10 rounded-[2rem] w-full max-w-lg p-8 shadow-2xl relative">
                            <div className="absolute top-4 right-4">
                                <button onClick={() => setShowSubcategoryModal(false)} className="w-8 h-8 flex items-center justify-center text-white/40 hover:text-white bg-white/5 rounded-full hover:bg-white/10 transition-colors">
                                    <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" strokeWidth="2"/></svg>
                                </button>
                            </div>
                            <h3 className="text-xl font-black italic uppercase text-white mb-6">Nueva Sub-Clasificación</h3>
                            <form onSubmit={submitSubcategory} className="space-y-6">
                                <div className="flex flex-col gap-1.5">
        <label className="pl-3 text-[8px] font-black text-orange-400 uppercase tracking-[0.3em] italic">Código</label>
                                    <input type="text" value={subcategoryForm.data.code} onChange={e => subcategoryForm.setData('code', e.target.value)} required className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm text-white focus:ring-2 focus:ring-orange-500/50 outline-none" placeholder="Ej. SUB-01"/>
                                </div>
                                <div className="flex flex-col gap-1.5">
        <label className="pl-3 text-[8px] font-black text-orange-400 uppercase tracking-[0.3em] italic">Nombre</label>
                                    <input type="text" value={subcategoryForm.data.name} onChange={e => subcategoryForm.setData('name', e.target.value)} required className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm text-white focus:ring-2 focus:ring-orange-500/50 outline-none" placeholder="Ej. Maquinaria Pesada"/>
                                </div>
                                <button type="submit" disabled={subcategoryForm.processing} className="w-full py-4 bg-orange-600 text-white rounded-2xl font-black uppercase tracking-widest text-sm hover:brightness-110 transition-all shadow-[0_0_15px_rgba(246,146,57,0.3)]">
                                    {subcategoryForm.processing ? 'Guardando...' : 'Crear Sub-Clasificación'}
                                </button>
                            </form>
                        </div>
                    </div>
                )}
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
                        <div className="flex-1 flex flex-col gap-1.5">
        <label htmlFor={`question_label_${index}`} className="pl-3 text-[7px] font-black text-orange-400/40 uppercase tracking-[0.3em] italic">Enunciado / Reactivo</label>
                            <input id={`question_label_${index}`} name={`question_label_${index}`} type="text" value={question.label} onChange={e => update(index, 'label', e.target.value)} placeholder="¿QUÉ SE DEBE VERIFICAR?" className="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-sm font-bold text-white focus:ring-1 focus:ring-orange-500 transition-all uppercase placeholder-white/5"/>
                        </div>
                    </div>
                    
                    <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div className="flex flex-col gap-1.5">
                            <label htmlFor={`question_type_${index}`} className="pl-3 text-[7px] font-black text-orange-400/40 uppercase tracking-[0.3em] italic">Tipo de Respuesta</label>
                            <select id={`question_type_${index}`} name={`question_type_${index}`} value={question.type} onChange={e => update(index, 'type', e.target.value)} className="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-xs font-bold text-white focus:ring-1 focus:ring-orange-500 transition-all [color-scheme:dark] italic uppercase">
                                <option value="text">Texto Corto</option>
                                <option value="textarea">Texto Extendido</option>
                                <option value="select">Lista Desplegable</option>
                                <option value="radio">Opción Única (Radio)</option>
                                <option value="checkbox">Opción Múltiple (Check)</option>
                                <option value="number">Numérico</option>
                                <option value="date">Fecha / Calendario</option>
                                <option value="time">Hora</option>
                                <option value="datetime">Fecha y Hora</option>
                            </select>
                        </div>
                        <div className="flex flex-col gap-1.5">
                            <label htmlFor={`question_help_${index}`} className="pl-3 text-[7px] font-black text-orange-400/40 uppercase tracking-[0.3em] italic">Ayuda al Operador</label>
                            <input id={`question_help_${index}`} name={`question_help_${index}`} type="text" value={question.help_text || ''} onChange={e => update(index, 'help_text', e.target.value)} placeholder="GUÍA RÁPIDA..." className="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-[10px] font-bold text-white/60 focus:ring-1 focus:ring-orange-500 transition-all uppercase placeholder-white/5"/>
                        </div>
                        <div className="flex items-center gap-4 bg-black/20 px-6 py-2 rounded-xl border border-white/5">
                            <label className="inline-flex items-center cursor-pointer group">
                                <input id={`question_required_${index}`} name={`question_required_${index}`} type="checkbox" checked={question.is_required} onChange={e => update(index, 'is_required', e.target.checked)} className="sr-only peer" />
                                <div className="relative w-8 h-4 bg-white/5 border border-white/10 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[1px] after:start-[1px] after:bg-white/20 after:border-white/10 after:border after:rounded-full after:h-3.5 after:w-3.5 after:transition-all peer-checked:bg-orange-500/50"></div>
                                <span className="ms-3 text-[8px] font-black text-white/20 uppercase tracking-widest italic group-hover:text-orange-400 transition-colors">Obligatorio</span>
                            </label>
                        </div>
                    </div>
                </div>

                {/* OPCIONES (Solo si es select/radio/checkbox) */}
                {(['select', 'radio', 'checkbox'].includes(question.type)) && (
                    <div className="lg:w-80 space-y-4 pt-2">
                        <div className="flex flex-col gap-1.5">
        <label htmlFor={`question_optionInput_${index}`} className="pl-3 text-[7px] font-black text-emerald-400 uppercase tracking-[0.3em] italic">Inyectar Opción</label>
                            <div className="flex gap-2">
                                <input id={`question_optionInput_${index}`} name={`question_optionInput_${index}`} type="text" value={optionInput} onChange={e => setOptionInput(e.target.value)} onKeyDown={e => e.key === 'Enter' && (e.preventDefault(), addOption())} className="flex-1 bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-xs font-bold text-white focus:ring-1 focus:ring-emerald-500 transition-all uppercase outline-none"/>
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
                    <div className="lg:w-80 space-y-2 pt-2">
                        <div className="grid grid-cols-2 gap-4">
                             <div className="flex flex-col gap-1.5">
                                <label htmlFor={`question_min_${index}`} className="pl-3 text-[7px] font-black text-white/20 uppercase tracking-[0.3em] italic">Mínimo</label>
                                <input id={`question_min_${index}`} name={`question_min_${index}`} type="number" value={question.min_value || ''} onChange={e => update(index, 'min_value', e.target.value)} className="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-xs text-white outline-none"/>
                            </div>
                            <div className="flex flex-col gap-1.5">
                                <label htmlFor={`question_max_${index}`} className="pl-3 text-[7px] font-black text-white/20 uppercase tracking-[0.3em] italic">Máximo</label>
                                <input id={`question_max_${index}`} name={`question_max_${index}`} type="number" value={question.max_value || ''} onChange={e => update(index, 'max_value', e.target.value)} className="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-xs text-white outline-none"/>
                            </div>
                        </div>
                        <p className="text-[8px] text-indigo-300/60 font-medium pl-2 italic leading-relaxed">
                            💡 Dejá ambos campos vacíos para permitir cualquier número libre (ej: lecturas de kilometraje histórico, horómetro, etc.) sin generar fallas.
                        </p>
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
