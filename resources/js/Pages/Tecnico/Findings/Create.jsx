import { Head, useForm, usePage, Link } from '@inertiajs/react';
import AuthenticatedLayout from '../../../Layouts/AuthenticatedLayout';
import { useState } from 'react';

export default function Create({ sedes, visit_id, initial_sede_id }) {
    const { auth } = usePage().props;
    
    // Si viene de una visita, pre-seleccionar la sede usando el ID resuelto desde la visita
    const initialSede = initial_sede_id || '';

    const { data, setData, post, processing, errors } = useForm({
        titulo: '',
        descripcion: '',
        ubicacion_especifica: '',
        sede_id: initialSede || '',
        criticidad: 'medio',
        plazo_correccion: new Date(Date.now() + 15 * 24 * 60 * 60 * 1000).toISOString().split('T')[0], // 15 días por defecto
        fotos: [],
        visit_certificate_id: visit_id || null,
    });

    const [currentStep, setCurrentStep] = useState(1);
    const [previews, setPreviews] = useState([]);

    const handleFileChange = (e) => {
        const newFiles = Array.from(e.target.files);
        if (newFiles.length === 0) return;

        // Acumular archivos en useForm
        const updatedFiles = [...data.fotos, ...newFiles];
        setData('fotos', updatedFiles);

        // Acumular previews
        const newPreviews = newFiles.map(file => URL.createObjectURL(file));
        setPreviews(prev => [...prev, ...newPreviews]);
    };

    const removeFoto = (index) => {
        const updatedFiles = data.fotos.filter((_, idx) => idx !== index);
        setData('fotos', updatedFiles);

        if (previews[index]) {
            URL.revokeObjectURL(previews[index]);
        }
        const updatedPreviews = previews.filter((_, idx) => idx !== index);
        setPreviews(updatedPreviews);
    };

    const submit = (e) => {
        e.preventDefault();
        const isTecnico = auth?.user?.is_technician;
        post(route(isTecnico ? 'tecnico.hallazgos.store' : 'gestor.hallazgos.store'));
    };

    return (
        <AuthenticatedLayout title="Descriptor de Hallazgos Operativos">
            <Head title="Reportar Hallazgo" />

            <div className="px-4 py-8 lg:p-12 text-white selection:bg-red-500/30">
                <div className="max-w-4xl mx-auto space-y-10 animate-fade-in-up">
                    
                    {/* HUB DE CABECERA CON INDICADOR DE PASOS */}
                    <div className="bg-[#16162a]/60 backdrop-blur-xl rounded-[3rem] border border-white/5 p-10 shadow-3xl relative overflow-hidden">
                        <div className="absolute inset-x-0 top-0 h-1.5 bg-gradient-to-r from-red-600 via-orange-500 to-red-600"></div>
                        
                        <div className="flex justify-between items-center relative z-10">
                            <div>
                                <h1 className="text-2xl font-black uppercase italic tracking-tighter leading-none">Reporte de Desviación</h1>
                                <p className="text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic mt-3">Fase {currentStep} de 3 — Protocolo de Inspección</p>
                            </div>
                            <div className="flex gap-2">
                                {[1, 2, 3].map(step => (
                                    <div key={step} className={`w-3 h-3 rounded-full transition-all duration-500 ${currentStep >= step ? 'bg-red-500 scale-125' : 'bg-white/10'}`}></div>
                                ))}
                            </div>
                        </div>
                    </div>

                    <form onSubmit={submit} className="space-y-8">
                        
                        {/* PASO 1: DEFINICIÓN Y ORIGEN */}
                        {currentStep === 1 && (
                            <div className="bg-[#16162a]/40 backdrop-blur-md rounded-[3rem] border border-white/5 p-10 shadow-2xl space-y-10 animate-fade-in">
                                 <div className="space-y-8">
                                     {visit_id ? (
                                         <div className="space-y-3 group">
                                             <label className="text-[10px] font-black text-red-400/60 uppercase tracking-[0.25em] italic block pl-1">
                                                 Sede de la Visita Activa
                                             </label>
                                             <div className="w-full bg-red-500/5 border border-red-500/20 rounded-2xl px-6 py-5 flex items-center justify-between shadow-[inset_0_0_20px_rgba(239,68,68,0.05)]">
                                                 <div>
                                                     <div className="text-lg font-black text-white italic uppercase tracking-tight">
                                                         {sedes.find(s => s.id === parseInt(data.sede_id))?.nombre_sede || 'Sede Seleccionada'}
                                                     </div>
                                                     <div className="text-[10px] font-black text-red-400/60 uppercase tracking-[0.2em] italic mt-1">
                                                         {sedes.find(s => s.id === parseInt(data.sede_id))?.empresa?.nombre_fantasia || 'Empresa'}
                                                     </div>
                                                 </div>
                                                 <span className="px-3 py-1 bg-red-500/10 border border-red-500/20 rounded-full text-[8px] font-black text-red-400 uppercase tracking-widest italic animate-pulse">
                                                     📍 Fijo por Visita
                                                 </span>
                                             </div>
                                             <input type="hidden" name="sede_id" value={data.sede_id} />
                                         </div>
                                     ) : (
                                         <div className="space-y-3 group">
                                             <label className="text-[10px] font-black text-red-400/60 uppercase tracking-[0.25em] italic block pl-1">
                                                 Sede o Instalación
                                             </label>
                                             <select id="field_8" name="field_8" 
                                                 value={data.sede_id}
                                                 onChange={e => setData('sede_id', e.target.value)}
                                                 required
                                                 className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-5 text-lg font-black text-white focus:ring-2 focus:ring-red-500/50 transition-all [color-scheme:dark] italic uppercase"
                                             >
                                                 <option value="">SELECCIONE UBICACIÓN...</option>
                                                 {sedes.map(s => (
                                                     <option key={s.id} value={s.id}>{s.nombre_sede} ({s.empresa?.nombre_fantasia})</option>
                                                 ))}
                                             </select>
                                         </div>
                                     )}

                                     <div className="space-y-3 group">
                                         <label className="text-[10px] font-black text-red-400/60 uppercase tracking-[0.25em] italic block pl-1">
                                             Título de la Desviación
                                         </label>
                                         <input id="field_7" name="field_7" 
                                             type="text" 
                                             value={data.titulo}
                                             onChange={e => setData('titulo', e.target.value)}
                                             required
                                             className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-5 text-xl font-black text-white focus:ring-2 focus:ring-red-500/50 transition-all uppercase placeholder-white/5 italic"
                                             placeholder="EJ: FALTA DE SEÑALIZACIÓN EN SALIDA DE EMERGENCIA..."
                                         />
                                     </div>

                                     <div className="space-y-3 group">
                                         <label className="text-[10px] font-black text-red-400/60 uppercase tracking-[0.25em] italic block pl-1">
                                             Ubicación Específica (Tag / Sector)
                                         </label>
                                         <input id="field_6" name="field_6" 
                                             type="text" 
                                             value={data.ubicacion_especifica}
                                             onChange={e => setData('ubicacion_especifica', e.target.value)}
                                             className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-5 text-lg font-black text-white focus:ring-2 focus:ring-red-500/50 transition-all uppercase placeholder-white/5 italic"
                                             placeholder="EJ: PLANTA BAJA / PASILLO B / SECTOR DE CALDERAS..."
                                         />
                                     </div>
                                 </div>

                                <div className="pt-6 flex justify-end">
                                    <button 
                                        type="button" 
                                        onClick={() => setCurrentStep(2)}
                                        disabled={!data.titulo || !data.sede_id}
                                        className="px-16 py-5 bg-red-600 text-white rounded-[2rem] text-[11px] font-black uppercase tracking-[0.2em] italic transition-all shadow-xl shadow-red-600/20 hover:brightness-110 active:scale-95 disabled:opacity-20"
                                    >
                                        Siguiente Paso
                                    </button>
                                </div>
                            </div>
                        )}

                        {/* PASO 2: CLASIFICACIÓN Y DESCRIPCIÓN */}
                        {currentStep === 2 && (
                            <div className="bg-[#16162a]/40 backdrop-blur-md rounded-[3rem] border border-white/5 p-10 shadow-2xl space-y-10 animate-fade-in">
                                <div className="space-y-8">
                                    <div className="space-y-3 group">
                                        <label className="text-[10px] font-black text-red-400/60 uppercase tracking-[0.25em] italic block pl-1">
                                            Descripción Técnica del Hallazgo
                                        </label>
                                        <textarea id="field_4" name="field_4" 
                                            value={data.descripcion}
                                            onChange={e => setData('descripcion', e.target.value)}
                                            rows="5"
                                            required
                                            className="w-full bg-black/40 border border-white/10 rounded-[2.5rem] px-8 py-6 text-sm font-bold text-white focus:ring-2 focus:ring-red-500/50 transition-all uppercase placeholder-white/5 italic"
                                            placeholder="DESCRIBA EN DETALLE LA ANOMALÍA DETECTADA..."
                                        ></textarea>
                                    </div>

                                    <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
                                        <div className="space-y-3 group">
                                            <label className="text-[10px] font-black text-red-400/60 uppercase tracking-[0.25em] italic block pl-1">
                                                Nivel de Criticidad
                                            </label>
                                            <select id="field_3" name="field_3" 
                                                value={data.criticidad}
                                                onChange={e => setData('criticidad', e.target.value)}
                                                className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-5 text-sm font-black text-white focus:ring-2 focus:ring-red-500 transition-all [color-scheme:dark] italic uppercase"
                                            >
                                                <option value="bajo" className="text-emerald-500">🟢 BAJO (Mejora)</option>
                                                <option value="medio" className="text-yellow-500">🟡 MEDIO (Desviación)</option>
                                                <option value="alto" className="text-orange-500">🟠 ALTO (Riesgo)</option>
                                                <option value="critico" className="text-red-500">🔴 CRÍTICO (Peligro Inminente)</option>
                                            </select>
                                        </div>
                                        <div className="space-y-3 group">
                                            <label className="text-[10px] font-black text-red-400/60 uppercase tracking-[0.25em] italic block pl-1">
                                                Plazo Límite de Corrección
                                            </label>
                                            <input id="field_2" name="field_2" 
                                                type="date" 
                                                value={data.plazo_correccion}
                                                onChange={e => setData('plazo_correccion', e.target.value)}
                                                required
                                                className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-5 text-sm font-black text-white focus:ring-2 focus:ring-red-500 transition-all [color-scheme:dark]"
                                            />
                                        </div>
                                    </div>
                                </div>

                                <div className="pt-6 flex justify-between">
                                    <button type="button" onClick={() => setCurrentStep(1)} className="px-10 py-5 text-[10px] font-black uppercase text-white/20 hover:text-white transition-all italic">← Volver</button>
                                    <button 
                                        type="button" 
                                        onClick={() => setCurrentStep(3)}
                                        disabled={!data.descripcion}
                                        className="px-16 py-5 bg-red-600 text-white rounded-[2rem] text-[11px] font-black uppercase tracking-[0.2em] italic transition-all shadow-xl shadow-red-600/20 hover:brightness-110 active:scale-95 disabled:opacity-20"
                                    >
                                        Siguiente Paso
                                    </button>
                                </div>
                            </div>
                        )}

                        {/* PASO 3: EVIDENCIA FOTOGRÁFICA */}
                        {currentStep === 3 && (
                            <div className="bg-[#16162a]/40 backdrop-blur-md rounded-[3rem] border border-white/5 p-10 shadow-2xl space-y-10 animate-fade-in">
                                <div className="space-y-8">
                                    <div className="text-center p-12 border-2 border-dashed border-white/5 rounded-[3rem] bg-black/20 hover:border-red-500/30 transition-all group relative cursor-pointer">
                                        <input id="field_1" name="field_1" 
                                            type="file" 
                                            multiple 
                                            onChange={handleFileChange}
                                            className="absolute inset-0 opacity-0 cursor-pointer"
                                        />
                                        <div className="w-20 h-20 bg-red-500/10 rounded-full flex items-center justify-center mx-auto mb-6 text-red-500 group-hover:scale-110 transition-transform">
                                            <svg className="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" strokeWidth="2"/></svg>
                                        </div>
                                        <div className="text-sm font-black uppercase italic tracking-widest text-white">Subir Evidencia Fotográfica</div>
                                        <div className="text-[10px] font-bold text-white/20 uppercase tracking-[0.3em] mt-2 italic">Seleccione o tome fotos de la desviación</div>
                                    </div>

                                    {previews.length > 0 && (
                                        <div className="grid grid-cols-2 md:grid-cols-4 gap-4 animate-fade-in">
                                            {previews.map((url, i) => (
                                                <div key={i} className="relative aspect-square rounded-2xl overflow-hidden border border-white/10 bg-black/40 group">
                                                    <img src={url} className="w-full h-full object-cover" alt={`Preview ${i}`} />
                                                    <button 
                                                        type="button"
                                                        onClick={() => removeFoto(i)}
                                                        className="absolute top-2 right-2 w-8 h-8 rounded-xl bg-red-600/90 text-white flex items-center justify-center text-[10px] font-black shadow-lg hover:bg-red-500 hover:scale-110 active:scale-95 transition-all z-20"
                                                        title="Eliminar foto"
                                                    >
                                                        ✕
                                                    </button>
                                                </div>
                                            ))}
                                        </div>
                                    )}
                                </div>

                                <div className="pt-6 flex justify-between">
                                    <button type="button" onClick={() => setCurrentStep(2)} className="px-10 py-5 text-[10px] font-black uppercase text-white/20 hover:text-white transition-all italic">← Volver</button>
                                    <button 
                                        type="submit" 
                                        disabled={processing}
                                        className="px-20 py-6 bg-red-600 text-white rounded-[2rem] text-[12px] font-black uppercase tracking-[0.3em] italic transition-all shadow-2xl shadow-red-600/40 hover:brightness-110 active:scale-95 disabled:opacity-20 flex items-center gap-3"
                                    >
                                        {processing ? 'Certificando Hallazgo...' : 'FINALIZAR Y REPORTAR'}
                                    </button>
                                </div>
                            </div>
                        )}

                    </form>
                </div>
            </div>

            <style>{`
                .animate-fade-in { animation: fade-in 0.4s ease-out forwards; }
                .animate-fade-in-up { animation: fade-in-up 0.5s ease-out forwards; }
                @keyframes fade-in { from { opacity: 0; } to { opacity: 1; } }
                @keyframes fade-in-up { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
            `}</style>
        </AuthenticatedLayout>
    );
}
