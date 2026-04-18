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
    }, [report.id]);

    const startDrawing = (e) => {
        const canvas = canvasRef.current;
        const ctx = canvas.getContext('2d');
        const rect = canvas.getBoundingClientRect();
        const x = (e.clientX || (e.touches && e.touches[0].clientX)) - rect.left;
        const y = (e.clientY || (e.touches && e.touches[0].clientY)) - rect.top;
        ctx.beginPath();
        ctx.moveTo(x, y);
        setIsDrawing(true);
    };

    const draw = (e) => {
        if (!isDrawing) return;
        const canvas = canvasRef.current;
        const ctx = canvas.getContext('2d');
        const rect = canvas.getBoundingClientRect();
        const x = (e.clientX || (e.touches && e.touches[0].clientX)) - rect.left;
        const y = (e.clientY || (e.touches && e.touches[0].clientY)) - rect.top;
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
            pending: 'bg-red-500/10 text-red-500 border-red-500/20 pulse',
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
            header={<h2 className="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Consola de Redacción</h2>}
        >
            <Head title="Redactor Maestro" />

            <div className="py-6 bg-[#11111d] min-h-screen pb-40">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-12">
                    
                    {/* STICKY HUD CONTEXTUAL */}
                    <div className="sticky top-4 z-40 bg-ep-dark/60 backdrop-blur-3xl p-6 rounded-[2.5rem] border border-white/10 shadow-[0_20px_50px_rgba(0,0,0,0.5)] flex flex-wrap items-center justify-between gap-6 transition-all border-l-8 border-l-ep-primary">
                        <div className="flex items-center gap-6">
                            <Link href={route('master-reports.index')} className="p-4 bg-white/5 rounded-2xl border border-white/10 text-white/30 hover:text-ep-primary hover:bg-white/10 transition-all">
                                <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="3" d="M15 19l-7-7 7-7"/></svg>
                            </Link>
                            <div>
                                <h1 className="text-xl font-black text-white uppercase italic tracking-tighter leading-none">{report.titulo}</h1>
                                <div className="flex items-center gap-4 mt-2">
                                    <span className="text-[10px] font-black text-ep-primary uppercase italic tracking-widest flex items-center gap-2">
                                        <div className="w-1.5 h-1.5 bg-ep-primary rounded-full animate-pulse"></div>
                                        {report.sede.nombre_sede}
                                    </span>
                                    <span className="text-[10px] font-black text-indigo-100/20 uppercase italic tracking-widest flex items-center gap-2">
                                        <svg className="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2v12a2 2 0 002 2z" strokeWidth="2"/></svg>
                                        {report.periodo}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div className="flex items-center gap-8">
                             <div className="flex flex-col items-end">
                                <span className="text-[9px] font-black text-indigo-100/20 uppercase tracking-[0.2em] italic leading-none mb-2">Progreso Plan Anual</span>
                                <div className="flex items-center gap-4">
                                    <div className="w-40 h-2.5 bg-white/5 rounded-full overflow-hidden border border-white/5 p-0.5">
                                        <div className="h-full bg-ep-primary shadow-[0_0_15px_rgba(246,146,57,0.8)] transition-all duration-1000 rounded-full" style={{ width: `${periodData.annual_plan_status?.percentage || 0}%` }}></div>
                                    </div>
                                    <span className="text-sm font-black text-white italic">{periodData.annual_plan_status?.percentage || 0}%</span>
                                </div>
                            </div>
                            <div className="w-px h-10 bg-white/10 mx-2"></div>
                            <button onClick={() => submit(false)} disabled={processing} className="px-8 py-4 bg-white/5 hover:bg-white/10 text-white text-[10px] font-black uppercase tracking-widest italic rounded-[1.2rem] border border-white/10 transition-all shadow-xl">
                                {processing ? 'GUARDANDO...' : 'GUARDAR AVANCE'}
                            </button>
                        </div>
                    </div>

                    {/* SECCIÓN 0: PLAN ANUAL TÁCTICO */}
                    <div className="bg-ep-dark/40 backdrop-blur-md p-10 sm:p-14 rounded-[3.5rem] border border-white/5 shadow-2xl relative overflow-hidden group">
                        <div className="absolute top-0 right-0 p-10 opacity-5 group-hover:opacity-10 transition-opacity">
                            <svg className="w-32 h-32 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" strokeWidth="1"/></svg>
                        </div>
                        <div className="flex items-center gap-8 mb-12">
                            <div className="w-16 h-16 bg-white/5 rounded-2xl flex items-center justify-center text-ep-primary font-black italic text-3xl border border-white/10 shadow-inner group-hover:scale-110 transition-all duration-500">P</div>
                            <div>
                                <h3 className="text-3xl font-black text-white uppercase italic tracking-tighter">Cumplimiento Plan Anual PPD</h3>
                                <p className="text-[10px] font-black text-indigo-100/20 uppercase tracking-[0.3em] mt-1 italic">Estado de objetivos programados para el periodo</p>
                            </div>
                        </div>

                        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            {periodData.annual_plan_status?.programmed?.map((prog, idx) => (
                                <div key={idx} className={`p-6 rounded-2xl border transition-all ${prog.status === 'completed' ? 'border-emerald-500/20 bg-emerald-500/5' : 'bg-black/30 border-white/5 opacity-40 hover:opacity-100'}`}>
                                    <div className="flex justify-between items-start mb-3">
                                        <span className="px-2 py-0.5 bg-white/5 rounded-md text-[7px] font-black text-white/30 uppercase tracking-widest">{prog.category}</span>
                                        <StatusBadge status={prog.status} />
                                    </div>
                                    <div className="text-xs font-black text-white uppercase italic tracking-tight mb-2 leading-tight h-8 line-clamp-2">{prog.activity}</div>
                                    {prog.executed_at ? (
                                        <div className="flex items-center gap-2 mt-4 text-[9px] font-bold text-emerald-400 uppercase italic">
                                            <div className="w-1 h-1 bg-emerald-400 rounded-full"></div>
                                            {prog.executed_at}
                                        </div>
                                    ) : (
                                        <div className="mt-4 text-[9px] font-bold text-white/10 uppercase italic">Pendiente de ejecución</div>
                                    )}
                                </div>
                            ))}
                        </div>
                    </div>

                    {/* SECCIÓN 1: BITÁCORA DE VISITAS */}
                    <div className="bg-ep-dark/40 backdrop-blur-md p-10 sm:p-14 rounded-[3.5rem] border border-white/5 shadow-2xl relative overflow-hidden group">
                        <div className="flex items-center gap-8 mb-12">
                            <div className="w-16 h-16 bg-white/5 rounded-2xl flex items-center justify-center text-white font-black italic text-3xl border border-white/10 shadow-inner group-hover:bg-ep-primary/20 transition-all">1</div>
                            <div>
                                <h3 className="text-3xl font-black text-white uppercase italic tracking-tighter">Bitácora de Visitas Técnicas</h3>
                                <p className="text-[10px] font-black text-indigo-100/20 uppercase tracking-[0.3em] mt-1 italic">Certificados de asistencia registrados ({periodData.visits?.length || 0})</p>
                            </div>
                        </div>

                        <div className="space-y-6 mb-12">
                            {periodData.visits?.map((visit, idx) => (
                                <div key={idx} className="p-8 bg-black/40 rounded-[2rem] border border-white/5 flex flex-col md:flex-row gap-10 hover:border-ep-primary/20 transition-all group/item">
                                    <div className="flex-shrink-0 text-center md:text-left min-w-[150px]">
                                        <div className="text-2xl font-black text-ep-primary italic leading-none">{new Date(visit.visit_date).toLocaleDateString()}</div>
                                        <div className="text-[10px] font-black text-indigo-100/30 uppercase tracking-[0.2em] mt-3">{visit.user?.nombres}</div>
                                        <span className="inline-block mt-4 px-3 py-1 bg-white/5 rounded-lg text-[8px] font-black text-white/20 uppercase tracking-widest">CERTIFICADO OK</span>
                                    </div>
                                    <div className="flex-grow">
                                        <p className="text-sm italic text-indigo-100/50 leading-relaxed mb-6 border-l-2 border-white/5 pl-6">{visit.general_observations || 'Sin observaciones generales registradas.'}</p>
                                        <div className="flex flex-wrap gap-3">
                                            {visit.checklist_completions?.map((cc, i) => (
                                                <span key={i} className="px-4 py-1.5 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-[10px] font-black uppercase italic rounded-xl">
                                                    INSPECCIÓN: {cc.assignment?.checklist?.title}
                                                </span>
                                            ))}
                                            {visit.trainings?.map((t, i) => (
                                                <span key={i} className="px-4 py-1.5 bg-blue-500/10 border border-blue-500/20 text-blue-400 text-[10px] font-black uppercase italic rounded-xl">
                                                    ACCIÓN FORMATIVA: {t.title}
                                                </span>
                                            ))}
                                        </div>
                                    </div>
                                </div>
                            ))}
                            {periodData.visits?.length === 0 && (
                                <div className="py-16 text-center bg-black/20 rounded-[2.5rem] border border-dashed border-white/10">
                                    <p className="text-[11px] font-black text-white/5 uppercase tracking-[0.4em] italic leading-none">Cero visitas detectadas en el periodo</p>
                                </div>
                            )}
                        </div>

                        <div className="space-y-4">
                            <label className="block text-[10px] font-black text-indigo-100/40 uppercase tracking-[0.4em] italic mb-3 px-8">Observaciones Técnicas de Gestión de Campo</label>
                            <textarea 
                                value={data.observaciones_visitas}
                                onChange={e => setData('observaciones_visitas', e.target.value)}
                                rows={5}
                                className="w-full bg-black/60 border-white/10 rounded-[2.5rem] px-10 py-8 text-white text-sm italic font-medium focus:ring-2 focus:ring-ep-primary transition-all placeholder-white/5 outline-none border"
                                placeholder="..."
                            />
                        </div>
                    </div>

                    {/* SECCIÓN 2: INSPECCIONES FUERA DE PLAN */}
                    <div className="bg-ep-dark/40 backdrop-blur-md p-10 sm:p-14 rounded-[3.5rem] border border-white/5 shadow-2xl relative overflow-hidden group">
                         <div className="flex items-center gap-8 mb-12">
                            <div className="w-16 h-16 bg-white/5 rounded-2xl flex items-center justify-center text-white font-black italic text-3xl border border-white/10 shadow-inner group-hover:bg-emerald-500/20 transition-all">2</div>
                            <div>
                                <h3 className="text-3xl font-black text-white uppercase italic tracking-tighter">Protocolos Ad-Hoc / Fuera de Plan</h3>
                                <p className="text-[10px] font-black text-indigo-100/20 uppercase tracking-[0.3em] mt-1 italic">Inspecciones técnicas no rutinarias ({periodData.adhoc_activities?.length || 0})</p>
                            </div>
                        </div>

                        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
                            {periodData.adhoc_activities?.map((adhoc, idx) => (
                                <div key={idx} className="p-6 rounded-2xl border border-white/5 bg-black/40 hover:border-emerald-500/30 transition-all group/item flex flex-col justify-between h-32">
                                    <div className="font-black text-white uppercase italic text-xs mb-2 leading-tight group-hover:text-emerald-400 transition-colors uppercase">{adhoc.activity}</div>
                                    <div className="flex justify-between items-end border-t border-white/5 pt-4 mt-auto">
                                        <span className="text-[8px] font-black text-indigo-100/10 uppercase tracking-widest italic">{adhoc.category}</span>
                                        <span className="text-[9px] font-black text-emerald-500/40 uppercase italic">{adhoc.date}</span>
                                    </div>
                                </div>
                            ))}
                            {periodData.adhoc_activities?.length === 0 && (
                                <div className="col-span-full py-16 text-center bg-black/20 rounded-[2.5rem] border border-dashed border-white/10">
                                    <p className="text-[11px] font-black text-white/5 uppercase tracking-[0.4em] italic leading-none">Sin protocolos ad-hoc registrados</p>
                                </div>
                            )}
                        </div>

                        <div className="space-y-4">
                            <label className="block text-[10px] font-black text-indigo-100/40 uppercase tracking-[0.4em] italic mb-3 px-8">Conclusiones sobre Inspecciones Realizadas</label>
                            <textarea 
                                value={data.observaciones_checklists}
                                onChange={e => setData('observaciones_checklists', e.target.value)}
                                rows={4}
                                className="w-full bg-black/60 border-white/10 rounded-[2.5rem] px-10 py-8 text-white text-sm italic font-medium focus:ring-2 focus:ring-emerald-500/30 transition-all placeholder-white/5 outline-none border"
                                placeholder="..."
                            />
                        </div>
                    </div>

                    {/* SECCIÓN 3: SINIESTRALIDAD Y RIESGOS */}
                    <div className="bg-ep-dark/40 backdrop-blur-md p-10 sm:p-14 rounded-[3.5rem] border border-white/5 shadow-2xl relative overflow-hidden group">
                         <div className="flex items-center gap-8 mb-12">
                            <div className="w-16 h-16 bg-white/5 rounded-2xl flex items-center justify-center text-white font-black italic text-3xl border border-white/10 shadow-inner group-hover:bg-red-500/20 transition-all">3</div>
                            <div>
                                <h3 className="text-3xl font-black text-white uppercase italic tracking-tighter">Análisis de Riesgos y Siniestralidad</h3>
                                <p className="text-[10px] font-black text-indigo-100/20 uppercase tracking-[0.3em] mt-1 italic">Eventos críticos detectados durante el periodo</p>
                            </div>
                        </div>

                        <div className="grid grid-cols-1 lg:grid-cols-2 gap-10 mb-12">
                            <div className="bg-black/40 p-10 rounded-[3rem] border border-white/5 border-l-4 border-l-yellow-600/50 relative">
                                <h4 className="text-[10px] font-black text-yellow-500 uppercase tracking-[0.4em] mb-8 italic">Desviaciones Críticas ({periodData.hallazgos?.length || 0})</h4>
                                <div className="space-y-4 max-h-[25rem] overflow-y-auto pr-6 custom-scrollbar">
                                    {periodData.hallazgos?.map((h, i) => (
                                        <div key={i} className="bg-white/5 p-6 rounded-2xl border border-white/5 hover:bg-white/10 transition-all">
                                            <div className="flex justify-between items-start mb-3">
                                                <span className="text-xs font-black text-white italic uppercase tracking-tight leading-tight">{h.titulo}</span>
                                                <span className="px-2 py-0.5 bg-yellow-500/10 border border-yellow-500/20 text-yellow-500 text-[7px] font-black uppercase rounded-md italic">{h.criticidad}</span>
                                            </div>
                                            <p className="text-[11px] italic text-white/30 mb-0 leading-relaxed line-clamp-2">{h.descripcion}</p>
                                        </div>
                                    ))}
                                    {periodData.hallazgos?.length === 0 && <p className="text-[10px] font-black text-white/5 uppercase italic text-center py-10">Cero hallazgos detectados</p>}
                                </div>
                            </div>
                            <div className="bg-black/40 p-10 rounded-[3rem] border border-white/5 border-l-4 border-l-red-600/50 relative">
                                <h4 className="text-[10px] font-black text-red-500 uppercase tracking-[0.4em] mb-8 italic">Siniestros e Incidentes ({periodData.incidentes?.length || 0})</h4>
                                <div className="space-y-4 max-h-[25rem] overflow-y-auto pr-6 custom-scrollbar">
                                    {periodData.incidentes?.map((inc, i) => (
                                        <div key={i} className="bg-white/5 p-6 rounded-2xl border border-white/5 hover:bg-white/10 transition-all border-l-2 border-l-red-500">
                                            <div className="flex justify-between items-start mb-3">
                                                <span className="text-xs font-black text-red-500 italic uppercase tracking-widest leading-none">{inc.tipo.replace('_', ' ')}</span>
                                                <span className="text-[8px] font-black text-white/10 uppercase italic">{new Date(inc.fecha_ocurrencia).toLocaleDateString()}</span>
                                            </div>
                                            <p className="text-[11px] italic text-white/30 mb-0 leading-relaxed line-clamp-2">{inc.descripcion}</p>
                                        </div>
                                    ))}
                                    {periodData.incidentes?.length === 0 && <p className="text-[10px] font-black text-white/5 uppercase italic text-center py-10">ZONA SEGURA: Cero incidentes</p>}
                                </div>
                            </div>
                        </div>

                        <div className="space-y-4">
                            <label className="block text-[10px] font-black text-indigo-100/40 uppercase tracking-[0.4em] italic mb-3 px-8">Comentarios de Seguridad y Salud Ocupacional</label>
                            <textarea 
                                value={data.observaciones_hallazgos_incidentes}
                                onChange={e => setData('observaciones_hallazgos_incidentes', e.target.value)}
                                rows={4}
                                className="w-full bg-black/60 border-white/10 rounded-[2.5rem] px-10 py-8 text-white text-sm italic font-medium focus:ring-2 focus:ring-red-500/30 transition-all placeholder-white/5 outline-none border"
                                placeholder="..."
                            />
                        </div>
                    </div>

                    {/* SECCIÓN 4: RESUMEN EJECUTIVO (PANEL FINAL) */}
                    <div className="bg-[#141421] p-12 sm:p-20 rounded-[4rem] shadow-[0_0_80px_rgba(0,0,0,0.6)] border-t-8 border-ep-primary relative overflow-hidden group">
                        <div className="absolute inset-0 bg-gradient-to-b from-ep-primary/5 to-transparent pointer-events-none opacity-50"></div>
                        <div className="flex items-center mb-12 pb-8 border-b border-white/10 relative z-10">
                            <div className="w-20 h-20 bg-ep-primary rounded-3xl flex items-center justify-center text-white font-black text-4xl shadow-[0_0_30px_rgba(246,146,57,0.4)] rotate-3">4</div>
                            <div className="ml-10">
                                <h3 className="text-4xl font-black text-white uppercase tracking-tight italic">Conclusiones Finales</h3>
                                <p className="text-[10px] font-black text-ep-primary uppercase tracking-[0.5em] mt-2 italic">Resumen Ejecutivo de Auditoría</p>
                            </div>
                        </div>
                        <textarea 
                            value={data.resumen_ejecutivo}
                            onChange={e => setData('resumen_ejecutivo', e.target.value)}
                            rows={10}
                            className="w-full bg-black/60 border-2 border-white/5 rounded-[3.5rem] text-white placeholder-white/5 shadow-inner focus:ring-4 focus:ring-ep-primary/30 text-xl font-bold p-12 italic leading-relaxed transition-all relative z-10 outline-none scrollbar-hide"
                            placeholder="Redacte aquí la visión técnica consolidada del periodo auditado..."
                        />
                         {errors.resumen_ejecutivo && <div className="mt-6 px-10 text-red-500 text-xs font-black uppercase italic tracking-[0.2em] animate-pulse">{errors.resumen_ejecutivo}</div>}
                    </div>

                    {/* SECCIÓN 5: FIRMA DIGITAL */}
                    <div className="bg-ep-dark/40 backdrop-blur-md p-10 sm:p-14 rounded-[4rem] border border-white/5 shadow-2xl relative overflow-hidden group">
                        <div className="flex items-center mb-12 pb-8 border-b border-white/10">
                            <div className="w-16 h-16 bg-white/5 rounded-2xl flex items-center justify-center text-white font-black text-3xl border border-white/10 shadow-inner group-hover:bg-ep-primary/20 transition-all italic">5</div>
                            <div className="ml-8">
                                <h3 className="text-3xl font-black text-white uppercase italic tracking-tight">Validación Táctica</h3>
                                <p className="text-[10px] font-black text-white/20 uppercase tracking-[0.4em] mt-1">Sello Digital del Especialista</p>
                            </div>
                        </div>

                        <div className="grid grid-cols-1 md:grid-cols-2 gap-16 items-center">
                            <div>
                                <p className="text-[11px] text-white/30 italic font-medium leading-relaxed uppercase tracking-[0.2em] mb-10">
                                    Utilice el panel táctico derecho para registrar su rúbrica digital. Este trazo será incrustado en el documento PDF final como evidencia de validación técnica autorizada.
                                </p>
                                {auth.user.firma && (
                                     <div className="p-10 bg-black/40 rounded-[2.5rem] border border-white/5 inline-block opacity-20 hover:opacity-100 transition-opacity duration-700">
                                        <span className="block text-[8px] font-black text-indigo-100/20 uppercase tracking-[0.5em] mb-6 italic text-center">Firma de Perfil Autorizada</span>
                                        <img src={`/storage/${auth.user.firma}`} className="h-28 brightness-0 invert opacity-60 mx-auto" />
                                    </div>
                                )}
                            </div>
                            
                            <div className="relative group/canvas">
                                <div className="bg-black shadow-[inset_0_0_60px_rgba(0,0,0,0.9)] border-2 border-dashed border-white/10 rounded-[4rem] overflow-hidden group-focus-within/canvas:border-ep-primary/50 transition-all duration-700">
                                    <canvas 
                                        ref={canvasRef}
                                        onMouseDown={startDrawing}
                                        onMouseMove={draw}
                                        onMouseUp={stopDrawing}
                                        onMouseLeave={stopDrawing}
                                        onTouchStart={startDrawing}
                                        onTouchMove={draw}
                                        onTouchEnd={stopDrawing}
                                        className="w-full h-64 cursor-crosshair"
                                    />
                                    <button 
                                        type="button" 
                                        onClick={clearSignature}
                                        className="absolute top-10 right-10 p-4 bg-red-600/10 border border-red-500/30 rounded-2xl text-red-500 hover:bg-red-600 hover:text-white transition-all backdrop-blur-md shadow-2xl"
                                        title="Limpiar panel"
                                    >
                                        <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="3" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </div>
                                <div className="absolute inset-x-0 bottom-6 flex justify-center pointer-events-none">
                                    <p className="text-[9px] text-white/5 font-black uppercase tracking-[0.6em] italic animate-pulse">Panel Táctico Activo</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* BOTONERA DE ACCIÓN GLOBAL */}
                    <div className="fixed bottom-10 left-0 right-0 z-50 px-8 pointer-events-none">
                        <div className="max-w-7xl mx-auto flex justify-center pointer-events-auto">
                            <div className="bg-ep-dark/80 backdrop-blur-3xl p-5 rounded-[2.8rem] border border-white/10 shadow-[0_-30px_60px_rgba(0,0,0,0.7)] flex items-center gap-8">
                                <button 
                                    onClick={() => submit(false)}
                                    disabled={processing}
                                    className="px-12 py-5 bg-white/5 hover:bg-white/10 text-white text-[11px] font-black uppercase tracking-[0.2em] italic rounded-2xl border border-white/10 transition-all shadow-xl"
                                >
                                    GUARDAR BORRADOR
                                </button>
                                <button 
                                    onClick={() => submit(true)}
                                    disabled={processing}
                                    className="h-20 px-16 bg-ep-primary text-white text-sm font-black uppercase tracking-[0.3em] italic rounded-3xl shadow-[0_0_40px_rgba(246,146,57,0.3)] hover:scale-105 active:scale-95 transition-all flex items-center gap-5 disabled:opacity-50 disabled:grayscale"
                                >
                                    <svg className="w-8 h-8 drop-shadow-lg" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="2.5"><path d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
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
                .pulse { animation: pulse 2s infinite; }
                @keyframes pulse { 0% { opacity: 1; } 50% { opacity: 0.4; } 100% { opacity: 1; } }
                .scrollbar-hide::-webkit-scrollbar { display: none; }
            `}} />
        </AuthenticatedLayout>
    );
}
