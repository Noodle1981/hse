import { Head, useForm, Link, usePage } from '@inertiajs/react';
import AuthenticatedLayout from '../../../Layouts/AuthenticatedLayout';
import { useState } from 'react';

export default function Show({ finding }) {
    const { auth } = usePage().props;
    
    const { data, setData, post, processing, reset } = useForm({
        estado_nuevo: finding.estado,
        comentario: '',
    });

    const [showUpdateModal, setShowUpdateModal] = useState(false);

    const isTecnico = auth?.user?.rol === 'tecnico' || auth?.user?.rol === 'Técnico';
    const updateRoute = isTecnico ? 'tecnico.hallazgos.update' : 'gestor.hallazgos.update';
    const backRoute = isTecnico ? 'tecnico.hallazgos.index' : 'gestor.hallazgos.index';

    const submitUpdate = (e) => {
        e.preventDefault();
        post(route(updateRoute, finding.id), {
            onSuccess: () => {
                setShowUpdateModal(false);
                reset('comentario');
            }
        });
    };

    const getStatusBadge = (st) => {
        const colors = {
            'pendiente': 'border-red-500/30 text-red-500 bg-red-500/5 pulse',
            'en_proceso': 'border-orange-500/30 text-orange-400 bg-orange-500/5',
            'resueltos': 'border-emerald-500/30 text-emerald-400 bg-emerald-500/5',
            'cerrado': 'border-white/10 text-white/40 bg-white/5',
        };
        return colors[st] || 'border-white/5 text-white/20';
    };

    return (
        <AuthenticatedLayout title="Descriptor Táctico de Hallazgo">
            <Head title={`Hallazgo: ${finding.titulo}`} />

            <div className="px-4 py-8 lg:p-12 text-white selection:bg-red-500/30">
                <div className="max-w-7xl mx-auto space-y-10 animate-fade-in-up">
                    
                    {/* CABECERA CRÍTICA */}
                    <div className="bg-[#16162a]/60 backdrop-blur-xl rounded-[3rem] border border-white/5 p-10 shadow-3xl flex flex-col xl:flex-row justify-between items-center gap-10 relative overflow-hidden">
                        <div className={`absolute inset-x-0 top-0 h-1.5 ${finding.criticidad === 'critico' ? 'bg-red-600' : 'bg-orange-500'}`}></div>
                        
                        <div className="flex items-center gap-8">
                            <Link href={route(backRoute)} className="w-14 h-14 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center text-white/20 hover:text-white transition-all">
                                <svg className="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="3" d="M15 19l-7-7 7-7"/></svg>
                            </Link>
                            <div>
                                <div className="flex items-center gap-4 mb-3">
                                    <div className={`px-4 py-1 border rounded-full text-[9px] font-black uppercase tracking-widest italic ${getStatusBadge(finding.estado)}`}>
                                        {finding.estado.replace('_', ' ')}
                                    </div>
                                    <span className="text-[10px] font-black text-red-500 uppercase tracking-widest italic">{finding.criticidad} severity</span>
                                </div>
                                <h1 className="text-3xl font-black uppercase italic tracking-tighter leading-none">{finding.titulo}</h1>
                            </div>
                        </div>

                        <div className="flex items-center gap-4">
                            <button 
                                onClick={() => setShowUpdateModal(true)}
                                className="px-10 py-5 bg-white/5 border border-white/10 text-white rounded-[2rem] text-[11px] font-black uppercase tracking-[0.2em] italic hover:bg-white/10 transition-all shadow-xl active:scale-95"
                            >
                                Gestionar Estado
                            </button>
                        </div>
                    </div>

                    <div className="grid grid-cols-1 xl:grid-cols-12 gap-10">
                        
                        {/* COLUMNA IZQUIERDA: DETALLE Y EVIDENCIA */}
                        <div className="xl:col-span-7 space-y-10">
                            
                            {/* Descripción */}
                            <div className="bg-[#16162a]/40 backdrop-blur-md rounded-[3rem] border border-white/5 p-10 shadow-2xl space-y-8">
                                <div className="space-y-4">
                                    <h4 className="text-[10px] font-black text-white/20 uppercase tracking-[0.4em] italic mb-6 flex items-center gap-3">
                                        <span className="w-1.5 h-4 bg-red-500 rounded-full"></span>
                                        Análisis de Desviación
                                    </h4>
                                    <p className="text-lg font-bold text-white/80 uppercase italic leading-relaxed">
                                        {finding.descripcion}
                                    </p>
                                </div>

                                <div className="grid grid-cols-1 md:grid-cols-2 gap-8 pt-8 border-t border-white/5">
                                    <div className="space-y-1">
                                        <div className="text-[9px] font-black text-white/20 uppercase italic tracking-widest">Sede Detectada</div>
                                        <div className="text-sm font-black text-white italic uppercase tracking-wider">{finding.sede?.nombre_sede}</div>
                                    </div>
                                    <div className="space-y-1">
                                        <div className="text-[9px] font-black text-white/20 uppercase italic tracking-widest">Plazo de Corrección</div>
                                        <div className="text-sm font-black text-red-500 italic uppercase tracking-wider">
                                            {new Date(finding.plazo_correccion).toLocaleDateString()}
                                            <span className="text-[9px] ml-2 opacity-40">(Expira pronto)</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {/* Galería de Evidencias */}
                            <div className="bg-[#16162a]/40 backdrop-blur-md rounded-[3rem] border border-white/5 p-10 shadow-2xl">
                                <h4 className="text-[10px] font-black text-white/20 uppercase tracking-[0.4em] italic mb-8">Evidencia Fotográfica</h4>
                                <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    {finding.fotos?.map((foto, i) => (
                                        <div key={foto.id} className="relative group rounded-[2rem] overflow-hidden border border-white/5 aspect-video bg-black/40">
                                            <img src={`/storage/${foto.ruta_archivo}`} className="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" alt={`Evidencia ${i}`} />
                                            <div className="absolute inset-x-0 bottom-0 p-6 bg-gradient-to-t from-black/80 to-transparent">
                                                <div className="text-[9px] font-black text-white uppercase italic tracking-widest opacity-60">Fase: {foto.tipo}</div>
                                            </div>
                                        </div>
                                    ))}
                                    {finding.fotos?.length === 0 && (
                                        <div className="col-span-2 py-20 text-center border-2 border-dashed border-white/5 rounded-[2rem] opacity-20 italic text-[10px] uppercase font-black tracking-widest">
                                            No se han cargado evidencias visuales
                                        </div>
                                    )}
                                </div>
                            </div>

                        </div>

                        {/* COLUMNA DERECHA: TIMELINE DE SEGUIMIENTO */}
                        <div className="xl:col-span-5">
                            <div className="bg-[#11111d]/60 backdrop-blur-xl border border-white/5 rounded-[3rem] p-10 shadow-3xl space-y-10 min-h-[600px] relative overflow-hidden">
                                <h4 className="text-[10px] font-black text-white/20 uppercase tracking-[0.4em] italic mb-10 flex items-center justify-between">
                                    Línea de Tiempo de Resolución
                                    <span className="w-2 h-2 rounded-full bg-emerald-500"></span>
                                </h4>

                                <div className="space-y-12 relative">
                                    {/* Línea vertical del timeline */}
                                    <div className="absolute left-6 top-0 bottom-0 w-1 bg-white/5 rounded-full"></div>

                                    {finding.seguimientos?.map((seg, i) => (
                                        <div key={seg.id} className="relative pl-16 group transition-all">
                                            {/* El punto del timeline */}
                                            <div className={`absolute left-[1.125rem] top-1 w-2.5 h-2.5 rounded-full border-2 border-[#11111d] z-10 ${i === 0 ? 'bg-red-500' : 'bg-white/20'}`}></div>
                                            
                                            <div className="flex flex-col gap-2">
                                                <div className="flex items-center justify-between">
                                                    <div className="text-[9px] font-black text-white/30 uppercase tracking-widest italic">{new Date(seg.fecha_seguimiento).toLocaleString()}</div>
                                                    <div className="text-[8px] font-black text-blue-400 uppercase italic bg-blue-500/10 px-2 py-0.5 rounded-lg border border-blue-500/20">{seg.user?.name}</div>
                                                </div>
                                                <div className="flex items-center gap-3 text-[10px] font-black italic uppercase">
                                                    <span className="text-white/20">{seg.estado_anterior}</span>
                                                    <svg className="w-3 h-3 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M13 7l5 5m0 0l-5 5m5-5H6" strokeWidth="2.5"/></svg>
                                                    <span className="text-white">{seg.estado_nuevo}</span>
                                                </div>
                                                <p className="text-[11px] font-bold text-white/40 uppercase italic leading-relaxed mt-2 bg-white/5 p-4 rounded-xl border border-white/5 group-hover:border-white/10 transition-colors">
                                                    {seg.comentario}
                                                </p>
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            {/* MODAL DE ACTUALIZACIÓN */}
            {showUpdateModal && (
                <div className="fixed inset-0 z-50 flex items-center justify-center p-4 animate-fade-in shadow-2xl">
                    <div className="absolute inset-0 bg-black/95 backdrop-blur-2xl" onClick={() => setShowUpdateModal(false)}></div>
                    <div className="bg-[#16162a] border border-white/10 rounded-[3rem] p-10 max-w-xl w-full relative z-10 shadow-3xl">
                        <div className="absolute inset-x-0 top-0 h-1.5 bg-red-600"></div>
                        <h2 className="text-2xl font-black text-white italic uppercase tracking-tighter mb-8 flex items-center gap-4">
                            <span className="w-2 h-10 bg-red-600 rounded-full"></span>
                            Gestionar Estado
                        </h2>

                        <form onSubmit={submitUpdate} className="space-y-8">
                            <div className="relative group">
                                <span className="absolute -top-2.5 left-4 bg-[#11111d] px-2 text-[8px] font-black text-red-400 uppercase tracking-[0.3em] italic z-10">Nuevo Estado Operativo</span>
                                <select id="field_2" name="field_2" 
                                    value={data.estado_nuevo}
                                    onChange={e => setData('estado_nuevo', e.target.value)}
                                    className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-red-500 transition-all [color-scheme:dark] italic uppercase"
                                >
                                    <option value="pendiente">PENDIENTE</option>
                                    <option value="en_proceso">EN PROCESO / CORRECCIÓN</option>
                                    <option value="resuelto">RESUELTO / VERIFICADO</option>
                                    <option value="cerrado">CERRADO (HISTORIAL)</option>
                                </select>
                            </div>

                            <div className="relative group">
                                <span className="absolute -top-2.5 left-4 bg-[#11111d] px-2 text-[8px] font-black text-red-400 uppercase tracking-[0.3em] italic z-10">Comentario de Acción</span>
                                <textarea id="field_1" name="field_1" 
                                    value={data.comentario}
                                    onChange={e => setData('comentario', e.target.value)}
                                    rows="4"
                                    required
                                    className="w-full bg-black/40 border border-white/10 rounded-[2rem] px-8 py-6 text-sm font-bold text-white focus:ring-2 focus:ring-red-500 transition-all uppercase placeholder-white/5 italic"
                                    placeholder="DETALLE LAS ACCIONES REALIZADAS..."
                                ></textarea>
                            </div>

                            <div className="flex gap-4 pt-4">
                                <button type="button" onClick={() => setShowUpdateModal(false)} className="flex-1 py-5 bg-white/5 text-white/40 hover:text-white rounded-[2rem] text-[11px] font-black uppercase tracking-widest italic transition-all">Cancelar</button>
                                <button 
                                    type="submit" 
                                    disabled={processing}
                                    className="flex-[2] py-5 bg-red-600 text-white rounded-[2rem] text-[11px] font-black uppercase tracking-[0.2em] italic transition-all shadow-xl hover:brightness-110 active:scale-95 disabled:opacity-50"
                                >
                                    {processing ? 'Actualizando...' : 'GUARDAR SEGUIMIENTO'}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            )}

            <style>{`
                .animate-fade-in { animation: fade-in 0.4s ease-out forwards; }
                .animate-fade-in-up { animation: fade-in-up 0.5s ease-out forwards; }
                @keyframes fade-in { from { opacity: 0; } to { opacity: 1; } }
                @keyframes fade-in-up { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
                .pulse { animation: pulse_red 2s infinite; }
                @keyframes pulse_red {
                    0% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.4); }
                    70% { box-shadow: 0 0 0 10px rgba(239, 68, 68, 0); }
                    100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
                }
            `}</style>
        </AuthenticatedLayout>
    );
}
