import { Head, Link, usePage } from '@inertiajs/react';
import AuthenticatedLayout from '../../../Layouts/AuthenticatedLayout';
import { useState } from 'react';

export default function Show({ inspection }) {
    const { auth } = usePage().props;
    const checklist = inspection.checklist;
    const answers = inspection.answers || [];

    const getAnswerValue = (qId) => {
        const ans = answers.find(a => a.dynamic_question_id === qId);
        if (!ans) return 'N/A';
        try {
            const parsed = JSON.parse(ans.value);
            return Array.isArray(parsed) ? parsed.join(', ') : parsed;
        } catch (e) {
            return ans.value;
        }
    };

    return (
        <AuthenticatedLayout title="Detalle de Protocolo">
            <Head title={`Detalle Inspección: ${checklist?.title}`} />

            <div className="px-4 sm:px-6 lg:px-8 py-8 text-white selection:bg-orange-500/30">
                <div className="max-w-[100rem] mx-auto space-y-8 animate-fade-in">
                    
                    {/* HUB DE CABECERA */}
                    <div className="bg-[#16162a]/60 backdrop-blur-xl rounded-[3rem] border border-white/5 p-10 shadow-2xl flex flex-col lg:flex-row items-center justify-between gap-8 relative overflow-hidden animate-fade-in-down">
                        <div className="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-transparent via-blue-500 to-transparent"></div>
                        
                        <div className="flex items-center gap-6">
                            <Link href={route('tecnico.inspections.history')} className="w-12 h-12 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center text-white/40 hover:text-white transition-all shadow-lg">
                                <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="3" d="M15 19l-7-7 7-7"/></svg>
                            </Link>
                            <div>
                                <h1 className="text-2xl font-black uppercase italic tracking-tighter leading-none">{checklist?.title}</h1>
                                <p className="text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic mt-2">Certificado por {auth.user.name} el {new Date(inspection.completed_at).toLocaleDateString()}</p>
                            </div>
                        </div>

                        <div className="flex items-center gap-4">
                            <a href={route('gestor.inspections.pdf.preview', { id: inspection.id })} target="_blank" className="px-10 py-5 bg-blue-600 text-white rounded-[2rem] text-[11px] font-black uppercase tracking-[0.2em] italic transition-all shadow-xl shadow-blue-600/20 hover:brightness-110 active:scale-95">Ver Certificado PDF</a>
                        </div>
                    </div>

                    <div className="grid grid-cols-1 lg:grid-cols-12 gap-8">
                        
                        {/* COLUMNA IZQUIERDA: RESUMEN DE RESPUESTAS */}
                        <div className="lg:col-span-8 space-y-12">
                            {checklist?.sections?.map((section, si) => (
                                <div key={section.id} className="bg-[#16162a]/40 backdrop-blur-md rounded-[3rem] border border-white/5 overflow-hidden shadow-2xl animate-fade-in-up">
                                    <div className="px-10 py-6 border-b border-white/5 bg-white/5">
                                        <h3 className="text-sm font-black text-white italic uppercase tracking-[0.3em] flex items-center gap-3">
                                            <span className="w-1.5 h-6 bg-blue-500 rounded-full"></span>
                                            {si + 1}. {section.title}
                                        </h3>
                                    </div>
                                    <div className="p-10 space-y-8">
                                        {section.questions?.map((q, qi) => (
                                            <div key={q.id} className="group border-b border-white/5 pb-8 last:border-0 last:pb-0">
                                                <div className="flex justify-between items-start gap-10">
                                                    <div className="space-y-4 flex-1">
                                                        <div className="flex items-start gap-3">
                                                            <span className="text-[10px] font-black text-white/20 italic mt-1">{si+1}.{qi+1}</span>
                                                            <label className="text-sm font-black text-white italic uppercase tracking-tight">{q.label}</label>
                                                        </div>
                                                        <div className="pl-6 text-xl font-black text-blue-400 italic uppercase">
                                                            {getAnswerValue(q.id)}
                                                        </div>
                                                    </div>
                                                    {/* Badge de tipo */}
                                                    <div className="text-[8px] font-black text-white/10 uppercase tracking-widest border border-white/5 px-2 py-1 rounded-lg italic text-right">
                                                        MOD: {q.type.toUpperCase()}
                                                    </div>
                                                </div>
                                            </div>
                                        ))}
                                    </div>
                                </div>
                            ))}
                        </div>

                        {/* COLUMNA DERECHA: METADATOS Y FIRMAS */}
                        <div className="lg:col-span-4 space-y-8">
                            
                            <div className="bg-[#11111d]/60 border border-white/5 rounded-[3rem] p-10 space-y-8 shadow-2xl animate-fade-in-up">
                                <div className="space-y-8">
                                    <div className="relative group">
                                        <span className="absolute -top-2.5 left-4 bg-[#11111d] px-2 text-[8px] font-black text-orange-400 uppercase tracking-[0.3em] italic z-10">Unidad de Despliegue</span>
                                        <div className="bg-white/5 p-6 rounded-2xl border border-white/5">
                                            <div className="text-lg font-black italic uppercase text-white">{inspection.sede?.nombre_sede}</div>
                                            <div className="text-[9px] font-black text-white/20 uppercase tracking-widest mt-1 italic">{inspection.sede?.empresa?.nombre_fantasia}</div>
                                        </div>
                                    </div>

                                    <div className="bg-black/20 rounded-[2.5rem] p-8 space-y-6 border border-white/5">
                                        <div className="space-y-4">
                                            <div className="text-[9px] font-black text-white/20 uppercase tracking-widest italic flex items-center gap-2">
                                                <span className="w-2 h-2 rounded-full bg-emerald-500"></span>
                                                Firma Responsable: {inspection.supervisor_name}
                                            </div>
                                            <div className="bg-white/5 p-4 rounded-xl border border-white/10 h-32 flex items-center justify-center overflow-hidden">
                                                <img src={inspection.supervisor_signature} alt="Firma Responsable" className="max-h-full brightness-0 invert opacity-60" />
                                            </div>
                                        </div>

                                        <div className="space-y-4 border-t border-white/5 pt-6">
                                            <div className="text-[9px] font-black text-white/20 uppercase tracking-widest italic flex items-center gap-2">
                                                <span className="w-2 h-2 rounded-full bg-blue-500"></span>
                                                Tu Firma: {inspection.user?.name}
                                            </div>
                                            <div className="bg-white/5 p-4 rounded-xl border border-white/10 h-32 flex items-center justify-center overflow-hidden">
                                                <img src={inspection.technician_signature} alt="Firma Técnico" className="max-h-full brightness-0 invert opacity-60" />
                                            </div>
                                        </div>
                                    </div>

                                    {inspection.notes && (
                                        <div className="relative group">
                                            <span className="absolute -top-2.5 left-4 bg-[#11111d] px-2 text-[8px] font-black text-white/20 uppercase tracking-[0.3em] italic z-10">Tus Observaciones</span>
                                            <div className="bg-white/5 p-6 rounded-2xl border border-white/5 text-[10px] font-bold text-white/40 uppercase italic leading-relaxed">
                                                {inspection.notes}
                                            </div>
                                        </div>
                                    )}
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <style>{`
                .animate-fade-in { animation: fade-in 0.4s ease-out forwards; }
                .animate-fade-in-down { animation: fade-in-down 0.6s ease-out forwards; }
                .animate-fade-in-up { animation: fade-in-up 0.5s ease-out forwards; }
                @keyframes fade-in { from { opacity: 0; } to { opacity: 1; } }
                @keyframes fade-in-down { from { opacity: 0; transform: translateY(-30px); } to { opacity: 1; transform: translateY(0); } }
                @keyframes fade-in-up { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
            `}</style>
        </AuthenticatedLayout>
    );
}
