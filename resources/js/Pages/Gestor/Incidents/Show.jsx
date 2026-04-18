import { Head, Link } from '@inertiajs/react';
import AuthenticatedLayout from '../../../Layouts/AuthenticatedLayout';

export default function Show({ incident }) {
    const getGravityBadge = (sev) => {
        const colors = {
            'leve': 'bg-emerald-500/10 text-emerald-500 border-emerald-500/20',
            'moderado': 'bg-yellow-500/10 text-yellow-500 border-yellow-500/20',
            'grave': 'bg-orange-500/10 text-orange-500 border-orange-500/20 shadow-[0_0_15px_rgba(249,115,22,0.3)]',
            'fatal': 'bg-red-500/10 text-red-500 border-red-500/20 shadow-[0_0_15px_rgba(239,68,68,0.3)]',
        };
        return colors[sev] || 'bg-white/5 text-white/40 border-white/10';
    };

    return (
        <AuthenticatedLayout title="Análisis de Incidente">
            <Head title={`Incidente #${incident.id} - ${incident.titulo}`} />

            <div className="px-4 py-8 lg:p-12 text-white selection:bg-red-500/30">
                <div className="max-w-7xl mx-auto space-y-8 animate-fade-in">
                    
                    {/* CABECERA TÁCTICA */}
                    <div className="flex flex-col lg:flex-row gap-8 items-start lg:items-center justify-between">
                        <div className="flex items-center gap-6">
                            <Link 
                                href={route('gestor.incidentes.index')}
                                className="w-12 h-12 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center text-white/40 hover:text-white hover:bg-white/10 transition-all hover:-translate-x-1"
                            >
                                <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2.5" d="M15 19l-7-7 7-7"/></svg>
                            </Link>
                            <div>
                                <div className="flex items-center gap-3 mb-1">
                                    <span className={`px-3 py-0.5 rounded-full text-[8px] font-black uppercase tracking-[0.2em] italic border ${getGravityBadge(incident.gravedad)}`}>
                                        {incident.gravedad}
                                    </span>
                                    <span className="text-[10px] font-black text-white/20 uppercase tracking-widest italic tracking-[0.3em]">ID: #{incident.id}</span>
                                </div>
                                <h1 className="text-4xl font-black uppercase italic tracking-tighter leading-none">{incident.titulo}</h1>
                            </div>
                        </div>

                        <div className="flex items-center gap-3">
                            <a 
                                href={route('gestor.incidentes.pdf', incident.id)}
                                className="flex items-center gap-3 px-8 py-4 bg-red-600 hover:bg-red-700 text-white rounded-[1.5rem] text-xs font-black uppercase italic tracking-widest shadow-lg shadow-red-600/20 transition-all active:scale-95"
                            >
                                <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2.5" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                Descargar Reporte PDF
                            </a>
                        </div>
                    </div>

                    <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        
                        {/* COLUMNA IZQUIERDA: DETALLES CRÍTICOS */}
                        <div className="lg:col-span-2 space-y-8">
                            
                            {/* CAJA DE ANÁLISIS */}
                            <div className="bg-[#16162a]/40 backdrop-blur-xl border border-white/5 rounded-[2.5rem] p-10 space-y-8 relative overflow-hidden group">
                                <div className="absolute top-0 right-0 p-10 opacity-[0.03] group-hover:opacity-[0.07] transition-opacity pointer-events-none">
                                    <svg className="w-32 h-32" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
                                </div>

                                <div className="grid grid-cols-1 md:grid-cols-2 gap-10">
                                    <div className="space-y-6">
                                        <div>
                                            <h3 className="text-[10px] font-black text-white/30 uppercase tracking-[0.3em] italic mb-3 flex items-center gap-2">
                                                <div className="w-1 h-1 bg-red-500 rounded-full"></div>
                                                Descripción del Evento
                                            </h3>
                                            <p className="text-sm font-medium text-white/80 leading-relaxed italic">{incident.descripcion}</p>
                                        </div>
                                        <div>
                                            <h3 className="text-[10px] font-black text-white/30 uppercase tracking-[0.3em] italic mb-3 flex items-center gap-2">
                                                <div className="w-1 h-1 bg-blue-500 rounded-full"></div>
                                                Medidas Tomadas
                                            </h3>
                                            <p className="text-sm font-medium text-white/80 leading-relaxed italic">{incident.medidas_tomadas}</p>
                                        </div>
                                    </div>

                                    <div className="space-y-6 bg-white/[0.02] rounded-3xl p-8 border border-white/5">
                                        <div className="grid grid-cols-2 gap-6">
                                            <div>
                                                <div className="text-[8px] font-black text-white/20 uppercase tracking-widest mb-1">Personas Afectadas</div>
                                                <div className="text-2xl font-black italic">{incident.personas_afectadas}</div>
                                            </div>
                                            <div>
                                                <div className="text-[8px] font-black text-white/20 uppercase tracking-widest mb-1">Días Perdidos</div>
                                                <div className="text-2xl font-black italic text-red-500">{incident.dias_perdidos}</div>
                                            </div>
                                            <div>
                                                <div className="text-[8px] font-black text-white/20 uppercase tracking-widest mb-1">Tipo de Evento</div>
                                                <div className="text-xs font-black uppercase italic text-blue-400">{incident.tipo.replace('_', ' ')}</div>
                                            </div>
                                            <div>
                                                <div className="text-[8px] font-black text-white/20 uppercase tracking-widest mb-1">Fecha Ocurrencia</div>
                                                <div className="text-xs font-black uppercase italic">{new Date(incident.fecha_ocurrencia).toLocaleDateString()}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {/* GALERÍA DE EVIDENCIAS */}
                            <div className="space-y-6">
                                <h2 className="text-lg font-black uppercase italic tracking-tight flex items-center gap-3">
                                    <svg className="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    Evidencias Fotográficas
                                </h2>

                                <div className="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                    {incident.fotos?.map((foto, idx) => (
                                        <div key={idx} className="group relative aspect-video bg-black/40 rounded-3xl overflow-hidden border border-white/5 hover:border-red-500/30 transition-all shadow-xl">
                                            <img 
                                                src={`/storage/${foto.ruta_archivo}`} 
                                                alt={`Evidencia ${idx + 1}`}
                                                className="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                                            />
                                            <div className="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end p-6">
                                                <p className="text-[10px] font-black uppercase italic tracking-widest">{foto.descripcion || 'Sin descripción'}</p>
                                            </div>
                                        </div>
                                    ))}
                                    {(!incident.fotos || incident.fotos.length === 0) && (
                                        <div className="col-span-full py-16 text-center bg-white/[0.02] border border-dashed border-white/10 rounded-3xl italic text-white/20 font-black uppercase tracking-widest">
                                            No se adjuntaron evidencias gráficas
                                        </div>
                                    )}
                                </div>
                            </div>
                        </div>

                        {/* COLUMNA DERECHA: CONTEXTO ORIGEN */}
                        <div className="space-y-8">
                            
                            {/* UBICACIÓN */}
                            <div className="bg-[#16162a]/40 backdrop-blur-md border border-white/5 rounded-[2rem] p-8 space-y-6">
                                <h3 className="text-[10px] font-black text-white/30 uppercase tracking-[0.3em] italic leading-none">Contexto de Ubicación</h3>
                                <div className="space-y-4">
                                    <div>
                                        <div className="text-[8px] font-black text-white/20 uppercase tracking-widest mb-1">Cliente / Empresa</div>
                                        <div className="text-xs font-black uppercase italic">{incident.sede?.empresa?.nombre}</div>
                                    </div>
                                    <div>
                                        <div className="text-[8px] font-black text-white/20 uppercase tracking-widest mb-1">Sede / Planta</div>
                                        <div className="text-xs font-black uppercase italic">{incident.sede?.nombre_sede}</div>
                                    </div>
                                    {incident.ubicacion_especifica && (
                                        <div>
                                            <div className="text-[8px] font-black text-white/20 uppercase tracking-widest mb-1">Punto Específico</div>
                                            <div className="text-xs font-black uppercase italic text-yellow-500">{incident.ubicacion_especifica}</div>
                                        </div>
                                    )}
                                </div>
                            </div>

                            {/* RESPONSABLE / ORIGEN */}
                            <div className="bg-[#16162a]/40 backdrop-blur-md border border-white/5 rounded-[2rem] p-8 space-y-6">
                                <h3 className="text-[10px] font-black text-white/30 uppercase tracking-[0.3em] italic leading-none">Responsable del Reporte</h3>
                                <div className="flex items-center gap-4">
                                    <div className="w-12 h-12 rounded-2xl bg-red-500/10 border border-red-500/20 flex items-center justify-center text-red-500 font-black italic">
                                        {incident.user?.name?.charAt(0) || 'U'}
                                    </div>
                                    <div>
                                        <div className="text-xs font-black uppercase italic tracking-tight">{incident.user?.name}</div>
                                        <div className="text-[8px] font-black text-white/20 uppercase italic tracking-widest">Técnico Operativo</div>
                                    </div>
                                </div>
                            </div>

                            {/* VÍNCULO A HALLAZGO SI EXISTE */}
                            {incident.hallazgo && (
                                <Link 
                                    href={route('gestor.hallazgos.show', incident.hallazgo.id)}
                                    className="block bg-orange-500/10 border border-orange-500/20 rounded-[2rem] p-8 space-y-4 hover:bg-orange-500/20 transition-all animate-pulse"
                                >
                                    <div className="flex items-center justify-between">
                                        <h3 className="text-[10px] font-black text-orange-500 uppercase tracking-[0.2em] italic">Desviación Relacionada</h3>
                                        <svg className="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2.5" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                    </div>
                                    <div className="text-sm font-black uppercase italic tracking-tighter leading-tight text-white/90">{incident.hallazgo.titulo}</div>
                                    <div className="text-[8px] font-black text-orange-500/60 uppercase italic tracking-widest">ESTO PODRÍA HABERSE EVITADO</div>
                                </Link>
                            )}

                        </div>
                    </div>
                </div>
            </div>

            <style>{`
                .animate-fade-in { animation: fade-in 0.8s ease-out; }
                @keyframes fade-in { from { opacity: 0; transform: scale(0.98); } to { opacity: 1; transform: scale(1); } }
            `}</style>
        </AuthenticatedLayout>
    );
}
