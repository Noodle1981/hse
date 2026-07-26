import React from 'react';
import { Head, router } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';

export default function Index({ catalog, selectedKey, selectedTutorial, contentHtml }) {
    const handleSelectTutorial = (key) => {
        router.get(route('tutorials.index'), { tutorial: key }, { preserveState: true, replace: true });
    };

    const getPilarBadge = (pilar) => {
        switch (pilar) {
            case 'admin':
                return <span className="px-3 py-1 bg-amber-500/10 border border-amber-500/30 text-amber-400 text-[8px] font-black uppercase tracking-widest italic rounded-lg">ADMINISTRACIÓN</span>;
            case 'gestor':
                return <span className="px-3 py-1 bg-[#F69239]/10 border border-[#F69239]/30 text-[#F69239] text-[8px] font-black uppercase tracking-widest italic rounded-lg">GESTIÓN HYS</span>;
            case 'tecnico':
                return <span className="px-3 py-1 bg-indigo-500/10 border border-indigo-500/30 text-indigo-400 text-[8px] font-black uppercase tracking-widest italic rounded-lg">INSPECCIÓN CAMPO</span>;
            default:
                return <span className="px-3 py-1 bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-[8px] font-black uppercase tracking-widest italic rounded-lg">PROCEDIMIENTO</span>;
        }
    };

    return (
        <AuthenticatedLayout>
            <Head title="Centro de Ayuda y Tutoriales" />

            <div className="min-h-screen bg-[#11111D] text-white p-4 sm:p-6 lg:p-8 space-y-8 animate-fade-in">
                
                {/* Header Principal HUD */}
                <div className="bg-[#16162a]/80 backdrop-blur-xl border border-white/10 rounded-[2.5rem] p-6 sm:p-10 shadow-2xl relative overflow-hidden">
                    <div className="absolute inset-0 bg-gradient-to-r from-[#F69239]/10 via-transparent to-indigo-500/10 pointer-events-none" />
                    
                    <div className="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                        <div className="space-y-2">
                            <div className="flex items-center gap-3">
                                <span className="w-2.5 h-8 bg-[#F69239] rounded-full shadow-[0_0_12px_rgba(246,146,57,0.5)]" />
                                <h1 className="text-2xl sm:text-3xl font-black text-white italic uppercase tracking-tighter">
                                    Centro de Ayuda & Manuales
                                </h1>
                            </div>
                            <p className="text-xs font-bold text-indigo-100/40 uppercase tracking-[0.2em] italic ml-5">
                                Guías de Uso, Protocolos de Operación y Manuales Descargables en PDF
                            </p>
                        </div>

                        {selectedTutorial && (
                            <a
                                href={route('tutorials.pdf', selectedTutorial.key)}
                                target="_blank"
                                rel="noopener noreferrer"
                                className="group relative px-6 py-3.5 bg-[#F69239] text-white text-[10px] font-black uppercase italic tracking-widest rounded-xl hover:brightness-110 active:scale-95 transition-all shadow-lg shadow-[#F69239]/20 overflow-hidden inline-flex items-center justify-center gap-2 self-start md:self-auto"
                            >
                                <div className="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000" />
                                <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="2.5">
                                    <path strokeLinecap="round" strokeLinejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                                </svg>
                                Descargar PDF Oficial
                            </a>
                        )}
                    </div>
                </div>

                {/* Grid Layout: Catálogo + Visor de Contenido */}
                <div className="grid grid-cols-1 lg:grid-cols-12 gap-8">
                    
                    {/* Columna Izquierda: Lista de Tutoriales */}
                    <div className="lg:col-span-4 space-y-4">
                        <div className="flex items-center justify-between px-2">
                            <h2 className="text-xs font-black text-white/40 uppercase tracking-[0.3em] italic">
                                Manuales Disponibles ({catalog.length})
                            </h2>
                        </div>

                        <div className="space-y-3">
                            {catalog.map((item) => {
                                const isSelected = item.key === selectedKey;
                                return (
                                    <button
                                        key={item.key}
                                        onClick={() => handleSelectTutorial(item.key)}
                                        className={`w-full text-left p-5 rounded-[2rem] border transition-all duration-300 relative overflow-hidden group ${
                                            isSelected
                                                ? 'bg-[#16162a] border-[#F69239]/50 shadow-xl shadow-[#F69239]/10'
                                                : 'bg-[#16162a]/50 border-white/5 hover:border-white/20 hover:bg-[#16162a]/80'
                                        }`}
                                    >
                                        {isSelected && (
                                            <div className="absolute left-0 top-0 bottom-0 w-1.5 bg-[#F69239]" />
                                        )}
                                        
                                        <div className="flex items-start justify-between gap-3 mb-2">
                                            <span className="text-[9px] font-black text-white/40 uppercase italic tracking-widest">
                                                {item.code}
                                            </span>
                                            {getPilarBadge(item.pilar)}
                                        </div>

                                        <h3 className={`text-sm font-black italic uppercase tracking-tight mb-1 transition-colors ${
                                            isSelected ? 'text-[#F69239]' : 'text-white group-hover:text-[#F69239]'
                                        }`}>
                                            {item.title}
                                        </h3>

                                        <p className="text-[10px] font-bold text-indigo-100/50 italic line-clamp-2 leading-relaxed">
                                            {item.subtitle}
                                        </p>

                                        <div className="mt-3 flex items-center justify-between pt-3 border-t border-white/5 text-[9px] font-black uppercase italic tracking-widest">
                                            {item.available ? (
                                                <span className="text-emerald-400 flex items-center gap-1">
                                                    <span className="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse" />
                                                    Disponible
                                                </span>
                                            ) : (
                                                <span className="text-white/30 italic">Próximo Sprint</span>
                                            )}
                                            
                                            <span className="text-white/40 group-hover:text-white transition-colors">
                                                Ver Manual →
                                            </span>
                                        </div>
                                    </button>
                                );
                            })}
                        </div>
                    </div>

                    {/* Columna Derecha: Visor del Manual */}
                    <div className="lg:col-span-8">
                        <div className="bg-[#16162a]/90 backdrop-blur-xl border border-white/10 rounded-[2.5rem] p-6 sm:p-10 shadow-2xl relative min-h-[600px] flex flex-col justify-between">
                            
                            {selectedTutorial ? (
                                <div>
                                    {/* Cabecera del Documento Seleccionado */}
                                    <div className="border-b border-white/10 pb-6 mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                        <div>
                                            <div className="flex items-center gap-2 mb-2">
                                                <span className="text-[9px] font-black text-[#F69239] uppercase tracking-widest italic bg-[#F69239]/10 px-3 py-1 rounded-md border border-[#F69239]/20">
                                                    {selectedTutorial.code}
                                                </span>
                                                {getPilarBadge(selectedTutorial.pilar)}
                                            </div>
                                            <h2 className="text-xl sm:text-2xl font-black text-white italic uppercase tracking-tight">
                                                {selectedTutorial.title}
                                            </h2>
                                            <p className="text-xs font-bold text-indigo-100/40 uppercase tracking-wider italic mt-1">
                                                {selectedTutorial.subtitle}
                                            </p>
                                        </div>

                                        <a
                                            href={route('tutorials.pdf', selectedTutorial.key)}
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            className="px-4 py-2.5 bg-white/5 border border-white/10 hover:bg-white/10 text-white text-[9px] font-black uppercase italic tracking-widest rounded-xl transition-all inline-flex items-center gap-2 shrink-0 self-start"
                                        >
                                            <svg className="w-4 h-4 text-[#F69239]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            PDF
                                        </a>
                                    </div>

                                    {/* Contenido HTML del Markdown */}
                                    <div 
                                        className="prose prose-invert max-w-none prose-headings:font-black prose-headings:uppercase prose-headings:italic prose-h1:text-xl prose-h1:text-[#F69239] prose-h2:text-lg prose-h2:text-white prose-h3:text-sm prose-h3:text-indigo-300 prose-p:text-xs prose-p:text-indigo-100/70 prose-p:leading-relaxed prose-li:text-xs prose-li:text-indigo-100/70 prose-strong:text-white prose-blockquote:border-l-[#F69239] prose-blockquote:bg-black/30 prose-blockquote:p-4 prose-blockquote:rounded-r-xl prose-blockquote:text-indigo-200 prose-code:text-[#F69239] prose-code:bg-black/40 prose-code:px-2 prose-code:py-0.5 prose-code:rounded-md prose-table:text-xs prose-th:text-[#F69239] prose-th:uppercase prose-td:border-white/10"
                                        dangerouslySetInnerHTML={{ __html: contentHtml }}
                                    />
                                </div>
                            ) : (
                                <div className="flex-1 flex flex-col items-center justify-center text-center p-12 text-white/20 italic font-black uppercase text-sm tracking-widest">
                                    Seleccione un manual del catálogo lateral para visualizar sus instrucciones
                                </div>
                            )}

                            {/* Footer del Visor */}
                            <div className="mt-12 pt-6 border-t border-white/5 flex items-center justify-between text-[9px] font-black text-white/30 uppercase tracking-widest italic">
                                <span>XamaSafe • Sistema de Gestión de Higiene y Seguridad</span>
                                <span>Documento Oficial Controlado</span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </AuthenticatedLayout>
    );
}
