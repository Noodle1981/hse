import React from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, useForm, Link } from '@inertiajs/react';
import VisitsLogSection from './Partials/VisitsLogSection';
import SignatureAndReviewSection from './Partials/SignatureAndReviewSection';

export default function Wizard({ auth, report, periodData }) {
    const { data, setData, put, processing, errors, transform } = useForm({
        resumen_ejecutivo: report.resumen_ejecutivo || '',
        observaciones_visitas: report.observaciones_visitas || '',
        observaciones_checklists: report.observaciones_checklists || '',
        observaciones_capacitaciones: report.observaciones_capacitaciones || '',
        observaciones_hallazgos_incidentes: report.observaciones_hallazgos_incidentes || '',
        observaciones_matriz: report.observaciones_matriz || '',
        datos_anexo: report.datos_anexo || {},
        observacion_revision: report.observacion_revision || '',
        action: '',
    });

    const isReadOnly = !auth.user.is_technician || ['finalizado', 'aprobado'].includes(report.estado);

    const submit = (finalize = false) => {
        transform(d => ({ ...d, finalize }));
        put(route('master-reports.update', report.id));
    };

    const handleApprove = () => {
        transform(d => ({ ...d, action: 'approve' }));
        put(route('master-reports.update', report.id));
    };

    const handleObserve = () => {
        if (!data.observacion_revision?.trim()) {
            alert('Por favor, redacte una observación sobre qué debe revisar o corregir el técnico antes de devolver el informe.');
            return;
        }
        transform(d => ({ ...d, action: 'observe' }));
        put(route('master-reports.update', report.id));
    };

    return (
        <AuthenticatedLayout user={auth.user} header={<h2 className="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Consola de Redacción</h2>}>
            <Head title="Redactor Maestro" />
            <div className="py-6 bg-[#11111d] min-h-screen pb-40">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-12">
                    <div className="sticky top-4 z-40 bg-ep-dark/60 backdrop-blur-3xl p-6 rounded-[2.5rem] border border-white/10 shadow-[0_20px_50px_rgba(0,0,0,0.5)] flex flex-wrap items-center justify-between gap-6 border-l-8 border-l-ep-primary">
                        <div className="flex items-center gap-6">
                            <Link href={route('master-reports.index')} className="p-4 bg-white/5 rounded-2xl border border-white/10 text-white/30 hover:text-ep-primary hover:bg-white/10 transition-all">
                                <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="3" d="M15 19l-7-7 7-7" /></svg>
                            </Link>
                            <div>
                                <h1 className="text-xl font-black text-white uppercase italic tracking-tighter leading-none">{report.titulo}</h1>
                                <div className="flex items-center gap-4 mt-2">
                                    <span className="text-[10px] font-black text-ep-primary uppercase italic tracking-widest flex items-center gap-2">
                                        <div className="w-1.5 h-1.5 bg-ep-primary rounded-full animate-pulse"></div>
                                        {report.sede.nombre_sede}
                                    </span>
                                    <span className="text-[10px] font-black text-indigo-100/20 uppercase italic tracking-widest">{report.periodo}</span>
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
                            {!isReadOnly ? (
                                <button onClick={() => submit(false)} disabled={processing} className="px-8 py-4 bg-white/5 hover:bg-white/10 text-white text-[10px] font-black uppercase tracking-widest italic rounded-[1.2rem] border border-white/10 transition-all shadow-xl">
                                    {processing ? 'GUARDANDO...' : 'GUARDAR AVANCE'}
                                </button>
                            ) : (
                                <span className={`px-5 py-3 rounded-xl text-[9px] font-black uppercase tracking-[0.15em] italic border ${
                                    report.estado === 'aprobado' ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20' :
                                    report.estado === 'finalizado' ? 'bg-blue-500/10 text-blue-400 border-blue-500/20' :
                                    'bg-yellow-500/10 text-yellow-400 border-yellow-500/20'
                                }`}>
                                    {report.estado === 'aprobado' ? 'APROBADO' : report.estado === 'finalizado' ? 'EN REVISIÓN' : 'EN REDACCIÓN'}
                                </span>
                            )}
                        </div>
                    </div>

                    {report.observacion_revision && (
                        <div className="bg-red-500/10 border-2 border-red-500/20 backdrop-blur-md p-10 sm:p-14 rounded-[3.5rem] shadow-2xl">
                            <div className="flex items-start gap-8">
                                <div className="w-16 h-16 bg-red-500/10 border border-red-500/20 rounded-2xl flex items-center justify-center text-red-500 text-3xl font-black italic flex-shrink-0">⚠️</div>
                                <div className="flex-grow">
                                    <h3 className="text-xl font-black text-white uppercase italic tracking-tight mb-2">Correcciones Solicitadas por el Gestor</h3>
                                    <div className="bg-black/40 rounded-[2rem] p-8 border border-white/5 shadow-inner">
                                        <p className="text-sm text-indigo-100/70 italic leading-relaxed font-semibold">{`"${report.observacion_revision}"`}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    )}

                    <div className="bg-ep-dark/40 backdrop-blur-md p-10 sm:p-14 rounded-[3.5rem] border border-white/5 shadow-2xl">
                        <div className="flex items-center gap-8 mb-12">
                            <div className="w-16 h-16 bg-white/5 rounded-2xl flex items-center justify-center text-ep-primary font-black italic text-3xl border border-white/10">P</div>
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
                                        <span className={`px-2 py-0.5 rounded-md text-[8px] font-black uppercase tracking-widest border ${prog.status === 'completed' ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20' : 'bg-red-500/10 text-red-500 border-red-500/20'}`}>
                                            {prog.status === 'completed' ? 'REALIZADO' : 'PENDIENTE'}
                                        </span>
                                    </div>
                                    <div className="text-xs font-black text-white uppercase italic tracking-tight mb-2 leading-tight h-8 line-clamp-2">{prog.activity}</div>
                                    <div className={`mt-4 text-[9px] font-bold uppercase italic ${prog.executed_at ? 'text-emerald-400' : 'text-white/10'}`}>
                                        {prog.executed_at || 'Pendiente de ejecución'}
                                    </div>
                                </div>
                            ))}
                        </div>
                    </div>

                    <VisitsLogSection visits={periodData.visits} value={data.observaciones_visitas} onChange={v => setData('observaciones_visitas', v)} isReadOnly={isReadOnly} />

                    <div className="bg-ep-dark/40 backdrop-blur-md p-10 sm:p-14 rounded-[3.5rem] border border-white/5 shadow-2xl">
                        <div className="flex items-center gap-8 mb-12">
                            <div className="w-16 h-16 bg-white/5 rounded-2xl flex items-center justify-center text-white font-black italic text-3xl border border-white/10">2</div>
                            <div>
                                <h3 className="text-3xl font-black text-white uppercase italic tracking-tighter">Protocolos Ad-Hoc / Fuera de Plan</h3>
                                <p className="text-[10px] font-black text-indigo-100/20 uppercase tracking-[0.3em] mt-1 italic">Inspecciones técnicas no rutinarias ({periodData.adhoc_activities?.length || 0})</p>
                            </div>
                        </div>
                        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
                            {periodData.adhoc_activities?.map((adhoc, idx) => (
                                <div key={idx} className="p-6 rounded-2xl border border-white/5 bg-black/40 flex flex-col justify-between h-32">
                                    <div className="font-black text-white uppercase italic text-xs mb-2 leading-tight">{adhoc.activity}</div>
                                    <div className="flex justify-between items-end border-t border-white/5 pt-4 mt-auto">
                                        <span className="text-[8px] font-black text-indigo-100/10 uppercase tracking-widest italic">{adhoc.category}</span>
                                        <span className="text-[9px] font-black text-emerald-500/40 uppercase italic">{adhoc.date}</span>
                                    </div>
                                </div>
                            ))}
                        </div>
                        <label className="block text-[10px] font-black text-indigo-100/40 uppercase tracking-[0.4em] italic mb-3 px-8">Conclusiones sobre Inspecciones Realizadas</label>
                        <textarea id="field_3" name="field_3" value={data.observaciones_checklists} onChange={e => setData('observaciones_checklists', e.target.value)} rows={4} readOnly={isReadOnly} className="w-full bg-black/60 border-white/10 rounded-[2.5rem] px-10 py-8 text-white text-sm italic font-medium border read-only:opacity-60" placeholder="..." />
                    </div>

                    <div className="bg-ep-dark/40 backdrop-blur-md p-10 sm:p-14 rounded-[3.5rem] border border-white/5 shadow-2xl">
                        <div className="flex items-center gap-8 mb-12">
                            <div className="w-16 h-16 bg-white/5 rounded-2xl flex items-center justify-center text-white font-black italic text-3xl border border-white/10">3</div>
                            <div>
                                <h3 className="text-3xl font-black text-white uppercase italic tracking-tighter">Análisis de Riesgos y Siniestralidad</h3>
                                <p className="text-[10px] font-black text-indigo-100/20 uppercase tracking-[0.3em] mt-1 italic">Eventos críticos detectados durante el periodo</p>
                            </div>
                        </div>
                        <div className="grid grid-cols-1 lg:grid-cols-2 gap-10 mb-12">
                            <div className="bg-black/40 p-10 rounded-[3rem] border border-white/5 border-l-4 border-l-yellow-600/50">
                                <h4 className="text-[10px] font-black text-yellow-500 uppercase tracking-[0.4em] mb-8 italic">Desviaciones Críticas ({periodData.hallazgos?.length || 0})</h4>
                                <div className="space-y-4 max-h-[25rem] overflow-y-auto pr-6">
                                    {periodData.hallazgos?.map((h, i) => (
                                        <div key={i} className="bg-white/5 p-6 rounded-2xl border border-white/5">
                                            <div className="flex justify-between items-start mb-3">
                                                <span className="text-xs font-black text-white italic uppercase">{h.titulo}</span>
                                                <span className="px-2 py-0.5 bg-yellow-500/10 text-yellow-500 text-[7px] font-black uppercase rounded-md">{h.criticidad}</span>
                                            </div>
                                            <p className="text-[11px] italic text-white/30 line-clamp-2">{h.descripcion}</p>
                                        </div>
                                    ))}
                                </div>
                            </div>
                            <div className="bg-black/40 p-10 rounded-[3rem] border border-white/5 border-l-4 border-l-red-600/50">
                                <h4 className="text-[10px] font-black text-red-500 uppercase tracking-[0.4em] mb-8 italic">Siniestros e Incidentes ({periodData.incidentes?.length || 0})</h4>
                                <div className="space-y-4 max-h-[25rem] overflow-y-auto pr-6">
                                    {periodData.incidentes?.map((inc, i) => (
                                        <div key={i} className="bg-white/5 p-6 rounded-2xl border border-white/5 border-l-2 border-l-red-500">
                                            <div className="flex justify-between items-start mb-3">
                                                <span className="text-xs font-black text-red-500 italic uppercase">{inc.tipo.replace('_', ' ')}</span>
                                                <span className="text-[8px] font-black text-white/10 uppercase">{new Date(inc.fecha_ocurrencia).toLocaleDateString()}</span>
                                            </div>
                                            <p className="text-[11px] italic text-white/30 line-clamp-2">{inc.descripcion}</p>
                                        </div>
                                    ))}
                                </div>
                            </div>
                        </div>
                        <label className="block text-[10px] font-black text-indigo-100/40 uppercase tracking-[0.4em] italic mb-3 px-8">Comentarios de Seguridad y Salud Ocupacional</label>
                        <textarea id="field_2" name="field_2" value={data.observaciones_hallazgos_incidentes} onChange={e => setData('observaciones_hallazgos_incidentes', e.target.value)} rows={4} readOnly={isReadOnly} className="w-full bg-black/60 border-white/10 rounded-[2.5rem] px-10 py-8 text-white text-sm italic font-medium border read-only:opacity-60" placeholder="..." />
                    </div>

                    <div className="bg-[#141421] p-12 sm:p-20 rounded-[4rem] shadow-[0_0_80px_rgba(0,0,0,0.6)] border-t-8 border-ep-primary">
                        <div className="flex items-center mb-12 pb-8 border-b border-white/10">
                            <div className="w-20 h-20 bg-ep-primary rounded-3xl flex items-center justify-center text-white font-black text-4xl rotate-3">4</div>
                            <div className="ml-10">
                                <h3 className="text-4xl font-black text-white uppercase tracking-tight italic">Conclusiones Finales</h3>
                                <p className="text-[10px] font-black text-ep-primary uppercase tracking-[0.5em] mt-2 italic">Resumen Ejecutivo de Auditoría</p>
                            </div>
                        </div>
                        <textarea id="field_1" name="field_1" value={data.resumen_ejecutivo} onChange={e => setData('resumen_ejecutivo', e.target.value)} rows={10} readOnly={isReadOnly} className="w-full bg-black/60 border-2 border-white/5 rounded-[3.5rem] text-white text-xl font-bold p-12 italic leading-relaxed outline-none read-only:opacity-60" placeholder="Redacte aquí la visión técnica consolidada del periodo auditado..." />
                        {errors.resumen_ejecutivo && <div className="mt-6 px-10 text-red-500 text-xs font-black uppercase italic">{errors.resumen_ejecutivo}</div>}
                    </div>

                    <SignatureAndReviewSection
                        auth={auth}
                        report={report}
                        data={data}
                        setData={setData}
                        errors={errors}
                        processing={processing}
                        onSubmit={submit}
                        onApprove={handleApprove}
                        onObserve={handleObserve}
                    />
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
