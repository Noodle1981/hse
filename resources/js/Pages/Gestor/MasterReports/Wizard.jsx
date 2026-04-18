import React, { useRef, useState, useEffect } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, useForm, Link } from '@inertiajs/react';

export default function Wizard({ auth, report, periodData }) {
    const canvasRef = useRef(null);
    const [isDrawing, setIsDrawing] = useState(false);
    const [hasSignature, setHasSignature] = useState(false);

    const { data, setData, put, processing, errors } = useForm({
        resumen_ejecutivo: report.resumen_ejecutivo || '',
        observaciones_visitas: report.observaciones_visitas || '',
        observaciones_checklists: report.observaciones_checklists || '',
        observaciones_capacitaciones: report.observaciones_capacitaciones || '',
        observaciones_hallazgos_incidentes: report.observaciones_hallazgos_incidentes || '',
        observaciones_matriz: report.observaciones_matriz || '',
        datos_anexo: report.datos_anexo || {},
        finalize: false,
    });

    const submit = (finalize = false) => {
        data.finalize = finalize;
        put(route('master-reports.update', report.id));
    };

    // Canvas Logic for Signature
    useEffect(() => {
        if (canvasRef.current) {
            const canvas = canvasRef.current;
            const ctx = canvas.getContext('2d');
            const rect = canvas.getBoundingClientRect();
            canvas.width = rect.width;
            canvas.height = rect.height;
            ctx.strokeStyle = '#F69239'; // Primary Orange
            ctx.lineWidth = 3;
            ctx.lineCap = 'round';
            ctx.lineJoin = 'round';
            
            if (report.datos_anexo?.firma_tecnico) {
                const img = new Image();
                img.onload = () => ctx.drawImage(img, 0, 0);
                img.src = report.datos_anexo.firma_tecnico;
                setHasSignature(true);
            }
        }
    }, []);

    const startDrawing = (e) => {
        const canvas = canvasRef.current;
        const ctx = canvas.getContext('2d');
        const rect = canvas.getBoundingClientRect();
        const x = (e.clientX || e.touches[0].clientX) - rect.left;
        const y = (e.clientY || e.touches[0].clientY) - rect.top;
        ctx.beginPath();
        ctx.moveTo(x, y);
        setIsDrawing(true);
    };

    const draw = (e) => {
        if (!isDrawing) return;
        const canvas = canvasRef.current;
        const ctx = canvas.getContext('2d');
        const rect = canvas.getBoundingClientRect();
        const x = (e.clientX || e.touches[0].clientX) - rect.left;
        const y = (e.clientY || e.touches[0].clientY) - rect.top;
        ctx.lineTo(x, y);
        ctx.stroke();
    };

    const stopDrawing = () => {
        if (!isDrawing) return;
        setIsDrawing(false);
        setHasSignature(true);
        const signatureBase64 = canvasRef.current.toDataURL('image/png');
        setData('datos_anexo', { ...data.datos_anexo, firma_tecnico: signatureBase64 });
    };

    const clearSignature = () => {
        const canvas = canvasRef.current;
        const ctx = canvas.getContext('2d');
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        setHasSignature(false);
        setData('datos_anexo', { ...data.datos_anexo, firma_tecnico: null });
    };

    const StatusBadge = ({ status }) => {
        const config = {
            completed: 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
            pending: 'bg-yellow-500/10 text-yellow-500 border-yellow-500/20',
        };
        return (
            <span className={`px-2 py-0.5 rounded-md text-[8px] font-black uppercase tracking-widest border ${config[status] || config.pending}`}>
                {status === 'completed' ? 'REALIZADO' : 'PENDIENTE'}
            </span>
        );
    };

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={<h2 className="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Redacción de Informe Maestro</h2>}
        >
            <Head title="Redactor Maestro" />

            <div className="py-6 bg-[#11111d] min-h-screen pb-40">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-12">
                    
                    {/* Floating Header HUD */}
                    <div className="sticky top-4 z-40 bg-ep-dark/80 backdrop-blur-2xl p-6 rounded-[2rem] border border-white/10 shadow-2xl flex flex-wrap items-center justify-between gap-6">
                        <div className="flex items-center gap-6">
                            <Link href={route('master-reports.index')} className="p-3 bg-white/5 rounded-2xl border border-white/10 text-indigo-100/50 hover:text-ep-primary transition-all">
                                <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M15 19l-7-7 7-7"/></svg>
                            </Link>
                            <div>
                                <h1 className="text-xl font-black text-white uppercase italic tracking-tighter leading-none">{report.titulo}</h1>
                                <div className="flex items-center gap-4 mt-2">
                                    <span className="text-[10px] font-black text-ep-primary uppercase italic tracking-widest flex items-center gap-1">
                                        <svg className="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        {report.sede.nombre_sede}
                                    </span>
                                    <span className="text-[10px] font-black text-indigo-100/30 uppercase italic tracking-widest flex items-center gap-1">
                                        <svg className="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        {report.periodo}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div className="flex items-center gap-8">
                             <div className="flex flex-col items-end">
                                <span className="text-[9px] font-black text-indigo-100/20 uppercase tracking-widest italic leading-none mb-1">Ejecución Plan Anual</span>
                                <div className="flex items-center gap-3">
                                    <div className="w-32 h-2 bg-white/5 rounded-full overflow-hidden border border-white/5">
                                        <div className="h-full bg-ep-primary shadow-[0_0_10px_rgba(246,146,57,0.5)] transition-all duration-1000" style={{ width: `${periodData.annual_plan_status?.percentage || 0}%` }}></div>
                                    </div>
                                    <span className="text-sm font-black text-white italic">{periodData.annual_plan_status?.percentage || 0}%</span>
                                </div>
                            </div>
                            <button onClick={() => submit(false)} disabled={processing} className="px-6 py-3 bg-white/5 hover:bg-white/10 text-white text-[10px] font-black uppercase tracking-widest italic rounded-xl border border-white/10 transition-all">
                                Guardar Borrador
                            </button>
                        </div>
                    </div>

                    {/* SECTION: ANNUAL PLAN COMPLIANCE */}
                    <div className="bg-ep-dark/40 backdrop-blur-md p-8 sm:p-12 rounded-[3rem] border border-white/5 shadow-2xl relative overflow-hidden group">
                        <div className="absolute inset-0 bg-gradient-to-r from-ep-primary/5 via-transparent to-transparent opacity-50"></div>
                        <div className="flex items-center gap-6 mb-10 pb-6 border-b border-white/5">
                            <div className="w-14 h-14 bg-white/5 rounded-2xl flex items-center justify-center text-white font-black italic text-2xl border border-white/10 shadow-inner group-hover:bg-ep-primary/20 transition-all">0</div>
                            <h3 className="text-2xl font-black text-white uppercase italic tracking-tighter">Estado de Plan Anual PPD</h3>
                        </div>

                        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            {periodData.annual_plan_status?.programmed?.map((prog, idx) => (
                                <div key={idx} className={`p-5 rounded-2xl border transition-all ${prog.status === 'completed' ? 'border-emerald-500/20 bg-emerald-500/5' : 'bg-black/40 border-white/5 opacity-60'}`}>
                                    <div className="flex justify-between items-start mb-2">
                                        <span className="text-[8px] font-black text-indigo-100/30 uppercase tracking-widest">{prog.category}</span>
                                        <StatusBadge status={prog.status} />
                                    </div>
                                    <div className="text-xs font-black text-white uppercase italic tracking-tight mb-2 line-clamp-1">{prog.activity}</div>
                                    {prog.executed_at && (
                                        <div className="flex items-center gap-2 mt-4 text-[9px] font-bold text-emerald-400 uppercase italic">
                                            <svg className="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
                                            {prog.executed_at} por {prog.technician}
                                        </div>
                                    )}
                                </div>
                            ))}
                        </div>
                    </div>

                    {/* SECTION 1: VISITS */}
                    <div className="bg-ep-dark/40 backdrop-blur-md p-8 sm:p-12 rounded-[3rem] border border-white/5 shadow-2xl relative overflow-hidden group">
                        <div className="flex items-center gap-6 mb-10 pb-6 border-b border-white/5">
                            <div className="w-14 h-14 bg-white/5 rounded-2xl flex items-center justify-center text-white font-black italic text-2xl border border-white/10 shadow-inner group-hover:bg-ep-primary/20 transition-all">1</div>
                            <h3 className="text-2xl font-black text-white uppercase italic tracking-tighter">Visitas Técnicas de Campo ({periodData.visits?.length || 0})</h3>
                        </div>

                        <div className="space-y-4 mb-10">
                            {periodData.visits?.map((visit, idx) => (
                                <div key={idx} className="p-6 bg-black/40 rounded-3xl border border-white/5 flex flex-col md:flex-row gap-8">
                                    <div className="flex-shrink-0 text-center md:text-left min-w-[120px]">
                                        <div className="text-xl font-black text-ep-primary italic leading-none">{new Date(visit.visit_date).toLocaleDateString()}</div>
                                        <div className="text-[10px] font-bold text-indigo-100/30 uppercase tracking-widest mt-2">{visit.user?.nombres}</div>
                                    </div>
                                    <div className="flex-grow">
                                        <p className="text-xs italic text-indigo-100/40 leading-relaxed mb-4">{visit.general_observations}</p>
                                        <div className="flex flex-wrap gap-2">
                                            {visit.checklist_completions?.map((cc, i) => (
                                                <span key={i} className="px-3 py-1 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-[10px] font-black uppercase italic rounded-lg">
                                                    {cc.assignment?.checklist?.title}
                                                </span>
                                            ))}
                                            {visit.trainings?.map((t, i) => (
                                                <span key={i} className="px-3 py-1 bg-blue-500/10 border border-blue-500/20 text-blue-400 text-[10px] font-black uppercase italic rounded-lg">
                                                    Formación: {t.title}
                                                </span>
                                            ))}
                                        </div>
                                    </div>
                                </div>
                            ))}
                        </div>

                        <div className="space-y-4">
                            <label className="block text-[10px] font-black text-indigo-100/40 uppercase tracking-[0.3em] italic mb-2 px-6">Análisis de la Gestión de Visitas</label>
                            <textarea 
                                value={data.observaciones_visitas}
                                onChange={e => setData('observaciones_visitas', e.target.value)}
                                rows={4}
                                className="w-full bg-black/40 border-white/10 rounded-[2rem] px-8 py-6 text-indigo-100 text-sm italic font-medium focus:ring-2 focus:ring-ep-primary transition-all placeholder-white/5 outline-none border"
                                placeholder="Redacte aquí sus comentarios técnicos sobre las visitas realizadas..."
                            />
                        </div>
                    </div>

                    {/* SECTION 2: ADHOC ACTIVITIES */}
                    <div className="bg-ep-dark/40 backdrop-blur-md p-8 sm:p-12 rounded-[3rem] border border-white/5 shadow-2xl relative overflow-hidden group">
                         <div className="flex items-center gap-6 mb-10 pb-6 border-b border-white/5">
                            <div className="w-14 h-14 bg-white/5 rounded-2xl flex items-center justify-center text-white font-black italic text-2xl border border-white/10 shadow-inner group-hover:bg-emerald-500/20 transition-all">2</div>
                            <h3 className="text-2xl font-black text-white uppercase italic tracking-tighter">Protocolos e Inspecciones Ad-Hoc ({periodData.adhoc_activities?.length || 0})</h3>
                        </div>

                        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-10">
                            {periodData.adhoc_activities?.map((adhoc, idx) => (
                                <div key={idx} className="p-5 rounded-2xl border border-white/5 bg-black/40 hover:border-emerald-500/30 transition-all group/item">
                                    <div className="font-black text-white uppercase italic text-[11px] mb-2 leading-none group-hover:text-emerald-400 transition-colors">{adhoc.activity}</div>
                                    <div className="text-[9px] font-black text-indigo-100/20 uppercase tracking-widest italic">Finalizado: {adhoc.date}</div>
                                </div>
                            ))}
                            {periodData.adhoc_activities?.length === 0 && (
                                <div className="col-span-full p-10 text-center bg-black/20 rounded-2xl border border-white/5">
                                    <p className="text-[10px] font-black text-indigo-100/10 uppercase tracking-widest italic">Sin inspecciones fuera de contrato en este periodo</p>
                                </div>
                            )}
                        </div>

                        <div className="space-y-4">
                            <label className="block text-[10px] font-black text-indigo-100/40 uppercase tracking-[0.3em] italic mb-2 px-6">Comentarios sobre Inspecciones</label>
                            <textarea 
                                value={data.observaciones_checklists}
                                onChange={e => setData('observaciones_checklists', e.target.value)}
                                rows={4}
                                className="w-full bg-black/40 border-white/10 rounded-[2rem] px-8 py-6 text-indigo-100 text-sm italic font-medium focus:ring-2 focus:ring-emerald-500/30 transition-all placeholder-white/5 outline-none border"
                                placeholder="..."
                            />
                        </div>
                    </div>

                    {/* SECTION 4: SAFETY CRITICAL */}
                    <div className="bg-ep-dark/40 backdrop-blur-md p-8 sm:p-12 rounded-[3rem] border border-white/5 shadow-2xl relative overflow-hidden group">
                         <div className="flex items-center gap-6 mb-10 pb-6 border-b border-white/5">
                            <div className="w-14 h-14 bg-white/5 rounded-2xl flex items-center justify-center text-white font-black italic text-2xl border border-white/10 shadow-inner group-hover:bg-red-500/20 transition-all">3</div>
                            <h3 className="text-2xl font-black text-white uppercase italic tracking-tighter">Análisis Crítico de Seguridad y Siniestralidad</h3>
                        </div>

                        <div className="grid grid-cols-1 lg:grid-cols-2 gap-10 mb-10">
                            <div className="bg-black/40 p-10 rounded-[2.5rem] border border-white/5 border-l-4 border-l-yellow-500/50 shadow-2xl">
                                <h4 className="text-[10px] font-black text-yellow-500 uppercase tracking-[0.3em] mb-8 italic">Desviaciones y Hallazgos ({periodData.hallazgos?.length || 0})</h4>
                                <div className="space-y-6 max-h-[30rem] overflow-y-auto pr-4 custom-scrollbar">
                                    {periodData.hallazgos?.map((h, i) => (
                                        <div key={i} className="bg-white/5 p-6 rounded-2xl border border-white/5 group transition-all hover:bg-white/10">
                                            <div className="flex justify-between items-start mb-4">
                                                <span className="text-xs font-black text-white italic uppercase tracking-tight">{h.titulo}</span>
                                                <span className="px-2 py-0.5 bg-yellow-500/10 border border-yellow-500/20 text-yellow-500 text-[8px] font-black uppercase rounded-lg italic">{h.criticidad}</span>
                                            </div>
                                            <p className="text-[11px] italic text-indigo-100/40 mb-4 leading-relaxed">{h.descripcion}</p>
                                        </div>
                                    ))}
                                </div>
                            </div>
                            <div className="bg-black/40 p-10 rounded-[2.5rem] border border-white/5 border-l-4 border-l-red-500/50 shadow-2xl">
                                <h4 className="text-[10px] font-black text-red-500 uppercase tracking-[0.3em] mb-8 italic">Accidentes e Incidentes ({periodData.incidentes?.length || 0})</h4>
                                <div className="space-y-6 max-h-[30rem] overflow-y-auto pr-4 custom-scrollbar">
                                    {periodData.incidentes?.map((inc, i) => (
                                        <div key={i} className="bg-white/5 p-6 rounded-2xl border border-white/5 group transition-all hover:bg-white/10">
                                            <div className="flex justify-between items-start mb-4">
                                                <span className="text-xs font-black text-red-500 italic uppercase tracking-widest leading-none">{inc.tipo.replace('_', ' ')}</span>
                                                <span className="text-[9px] font-black text-indigo-100/20 uppercase italic tracking-tighter">{new Date(inc.fecha_ocurrencia).toLocaleDateString()}</span>
                                            </div>
                                            <p className="text-[11px] italic text-indigo-100/40 mb-4 leading-relaxed">{inc.descripcion}</p>
                                        </div>
                                    ))}
                                </div>
                            </div>
                        </div>

                        <div className="space-y-4">
                            <label className="block text-[10px] font-black text-indigo-100/40 uppercase tracking-[0.3em] italic mb-2 px-6">Análisis de Desvíos y Eventos</label>
                            <textarea 
                                value={data.observaciones_hallazgos_incidentes}
                                onChange={e => setData('observaciones_hallazgos_incidentes', e.target.value)}
                                rows={4}
                                className="w-full bg-black/40 border-white/10 rounded-[2rem] px-8 py-6 text-indigo-100 text-sm italic font-medium focus:ring-2 focus:ring-red-500/30 transition-all placeholder-white/5 outline-none border"
                                placeholder="..."
                            />
                        </div>
                    </div>

                    {/* SECTION 5: EXECUTIVE SUMMARY */}
                    <div className="bg-ep-dark p-10 sm:p-20 rounded-[3.5rem] shadow-[0_0_50px_rgba(0,0,0,0.5)] border-t-8 border-ep-primary relative overflow-hidden group">
                        <div className="absolute inset-0 bg-gradient-to-b from-ep-primary/5 to-transparent pointer-events-none opacity-50"></div>
                        <div className="flex items-center mb-10 pb-6 border-b border-white/10 relative z-10">
                            <div className="w-16 h-16 bg-ep-primary rounded-2xl flex items-center justify-center text-white font-black text-3xl shadow-[0_0_20px_rgba(246,146,57,0.4)] rotate-3">4</div>
                            <h3 className="text-3xl font-black text-white uppercase tracking-tight italic ml-8">Conclusiones - Resumen Ejecutivo</h3>
                        </div>
                        <textarea 
                            value={data.resumen_ejecutivo}
                            onChange={e => setData('resumen_ejecutivo', e.target.value)}
                            rows={10}
                            className="w-full bg-black/40 border-2 border-white/5 rounded-[3rem] text-white placeholder-white/5 shadow-inner focus:ring-4 focus:ring-ep-primary/50 text-xl font-bold p-10 italic leading-relaxed transition-all relative z-10 outline-none"
                            placeholder="Consolide aquí la visión técnica final..."
                        />
                         {errors.resumen_ejecutivo && <div className="mt-4 px-6 text-red-500 text-xs font-black uppercase italic tracking-widest animate-pulse">{errors.resumen_ejecutivo}</div>}
                    </div>

                    {/* SECTION 6: SIGNATURE */}
                    <div className="bg-ep-dark/40 backdrop-blur-md p-8 sm:p-12 rounded-[3.5rem] border border-white/5 shadow-2xl relative overflow-hidden group">
                        <div className="flex items-center mb-10 pb-6 border-b border-white/10">
                            <div className="w-14 h-14 bg-white/5 rounded-2xl flex items-center justify-center text-white font-black text-2xl border border-white/10 shadow-inner group-hover:bg-ep-primary/20 transition-all italic">5</div>
                            <h3 className="text-2xl font-black text-white uppercase italic tracking-tight ml-8">Validación y Firma Especialista</h3>
                        </div>

                        <div className="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
                            <div>
                                <p className="text-xs text-indigo-100/30 italic font-medium leading-relaxed uppercase tracking-widest mb-8">
                                    Dibuje su rúbrica en el panel digital adjunto para validar el presente informe consolidado. Su firma será adjuntada al PDF final de auditoría.
                                </p>
                                {auth.user.firma && (
                                     <div className="p-6 bg-black/20 rounded-3xl border border-white/10 inline-block opacity-40 hover:opacity-100 transition-opacity">
                                        <span className="block text-[8px] font-black text-indigo-100/20 uppercase tracking-widest mb-4">Registro de Perfil</span>
                                        <img src={`/storage/${auth.user.firma}`} className="h-24 brightness-0 invert opacity-60" />
                                    </div>
                                )}
                            </div>
                            
                            <div className="relative">
                                <div className="bg-black shadow-[inset_0_0_40px_rgba(0,0,0,0.8)] border-2 border-dashed border-white/10 rounded-[3rem] overflow-hidden group-focus-within:border-ep-primary/50 transition-all">
                                    <canvas 
                                        ref={canvasRef}
                                        onMouseDown={startDrawing}
                                        onMouseMove={draw}
                                        onMouseUp={stopDrawing}
                                        onMouseLeave={stopDrawing}
                                        onTouchStart={startDrawing}
                                        onTouchMove={draw}
                                        onTouchEnd={stopDrawing}
                                        className="w-full h-56 cursor-crosshair"
                                    />
                                    <button 
                                        type="button" 
                                        onClick={clearSignature}
                                        className="absolute top-6 right-6 p-3 bg-red-600/10 border border-red-500/30 rounded-2xl text-red-500 hover:bg-red-500 hover:text-white transition-all backdrop-blur-md"
                                    >
                                        <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="3" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </div>
                                <p className="text-[8px] text-indigo-100/10 font-black uppercase tracking-[0.4em] italic mt-4 text-center">Panel Táctil Digital Activo</p>
                            </div>
                        </div>
                    </div>

                    {/* ACTIONS */}
                    <div className="fixed bottom-10 left-0 right-0 z-50 px-8 pointer-events-none">
                        <div className="max-w-7xl mx-auto flex justify-center pointer-events-auto">
                            <div className="bg-ep-dark/90 backdrop-blur-3xl p-4 rounded-[2.5rem] border border-white/10 shadow-[0_-20px_50px_rgba(0,0,0,0.5)] flex items-center gap-6">
                                <button 
                                    onClick={() => submit(false)}
                                    disabled={processing}
                                    className="px-10 py-5 bg-white/5 hover:bg-white/10 text-white text-[11px] font-black uppercase tracking-widest italic rounded-2xl border border-white/10 transition-all"
                                >
                                    Guardar Borrador
                                </button>
                                <button 
                                    onClick={() => submit(true)}
                                    disabled={processing || !hasSignature}
                                    className="h-20 px-16 bg-ep-primary text-white text-base font-black uppercase tracking-[0.2em] italic rounded-[1.5rem] shadow-2xl shadow-ep-primary/30 hover:scale-105 active:scale-95 transition-all flex items-center gap-4 disabled:opacity-50 disabled:grayscale"
                                >
                                    <svg className="w-8 h-8 group-hover:animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="2.5"><path d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    GENERAR INFORME FINAL
                                </button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <style dangerouslySetInnerHTML={{ __html: `
                .custom-scrollbar::-webkit-scrollbar { width: 4px; }
                .custom-scrollbar::-webkit-scrollbar-track { background: rgba(255,255,255,0.02); }
                .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.05); border-radius: 10px; }
                .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.1); }
                .cursor-crosshair { cursor: crosshair; }
                [color-scheme="dark"] { color-scheme: dark; }
            `}} />
        </AuthenticatedLayout>
    );
}
