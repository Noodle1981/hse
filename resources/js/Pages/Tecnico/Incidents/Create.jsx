import { Head, useForm, router, Link } from '@inertiajs/react';
import AuthenticatedLayout from '../../../Layouts/AuthenticatedLayout';
import { useState, useRef } from 'react';

export default function Create({ sedes, visit_id }) {
    const { data, setData, post, processing, errors } = useForm({
        titulo: '',
        tipo: 'incidente',
        gravedad: 'leve',
        fecha_ocurrencia: new Date().toISOString().split('T')[0],
        sede_id: '',
        descripcion: '',
        medidas_tomadas: '',
        personas_afectadas: 0,
        dias_perdidos: 0,
        ubicacion_especifica: '',
        visit_certificate_id: visit_id || null,
        fotos: [],
    });

    const [previews, setPreviews] = useState([]);
    const fileInputRef = useRef();

    const handleFileChange = (e) => {
        const files = Array.from(e.target.files);
        setData('fotos', [...data.fotos, ...files]);

        const newPreviews = files.map(file => URL.createObjectURL(file));
        setPreviews([...previews, ...newPreviews]);
    };

    const removeFoto = (index) => {
        const newFotos = [...data.fotos];
        newFotos.splice(index, 1);
        setData('fotos', newFotos);

        const newPreviews = [...previews];
        newPreviews.splice(index, 1);
        setPreviews(newPreviews);
    };

    const submit = (e) => {
        e.preventDefault();
        post(route('tecnico.incidentes.store'));
    };

    const gravityOptions = [
        { id: 'leve', label: 'LEVE', color: 'bg-emerald-500', desc: 'Sin lesiones o daños menores' },
        { id: 'moderado', label: 'MODERADO', color: 'bg-yellow-500', desc: 'Lesiones leves / Daños materiales' },
        { id: 'grave', label: 'GRAVE', color: 'bg-orange-500', desc: 'Lesiones serias / Tiempo perdido' },
        { id: 'fatal', label: 'FATAL', color: 'bg-red-600', desc: 'Pérdida de vida o incapacidad' },
    ];

    return (
        <AuthenticatedLayout title="Reportar Incidente">
            <Head title="Nuevo Reporte de Incidente" />

            <div className="min-h-screen bg-[#11111d] text-white selection:bg-red-500/30 pb-20">
                <div className="max-w-4xl mx-auto px-4 py-8 lg:py-12 space-y-10 animate-fade-in">
                    
                    {/* CABECERA */}
                    <div className="flex items-center gap-6">
                        <div className="w-16 h-16 rounded-3xl bg-red-600 flex items-center justify-center text-white shadow-xl shadow-red-600/20">
                            <svg className="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        </div>
                        <div>
                            <h1 className="text-3xl font-black uppercase italic tracking-tighter leading-none">Reportar<br/>Incidente Crítico</h1>
                            <p className="text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic mt-1">Protocolo de seguridad inmediata</p>
                        </div>
                    </div>

                    <form onSubmit={submit} className="space-y-8">
                        
                        {/* SECCIÓN 1: DATOS GENERALES */}
                        <div className="bg-[#16162a]/40 backdrop-blur-xl border border-white/5 rounded-[2.5rem] p-8 lg:p-10 space-y-8 shadow-2xl">
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div className="md:col-span-2 space-y-2">
                                    <label className="text-[10px] font-black text-white/30 uppercase tracking-[0.3em] italic ml-2">Título del Incidente</label>
                                    <input id="field_8" name="field_8" 
                                        type="text" 
                                        value={data.titulo}
                                        onChange={e => setData('titulo', e.target.value)}
                                        placeholder="EJ: CAÍDA DE ALTURA EN GALPÓN A..."
                                        className="w-full bg-black/40 border border-white/5 rounded-2xl px-6 py-4 text-xs font-bold text-white placeholder-white/10 focus:ring-2 focus:ring-red-600 transition-all uppercase italic"
                                        required
                                    />
                                    {errors.titulo && <p className="text-red-500 text-[10px] font-bold mt-1 ml-2 uppercase italic">{errors.titulo}</p>}
                                </div>

                                <div className="space-y-2">
                                    <label className="text-[10px] font-black text-white/30 uppercase tracking-[0.3em] italic ml-2">Sede / Ubicación</label>
                                    <select id="field_7" name="field_7" 
                                        value={data.sede_id}
                                        onChange={e => setData('sede_id', e.target.value)}
                                        className="w-full bg-black/40 border border-white/5 rounded-2xl px-6 py-4 text-xs font-bold text-white focus:ring-2 focus:ring-red-600 transition-all uppercase italic [color-scheme:dark]"
                                        required
                                    >
                                        <option value="">SELECCIONAR SEDE...</option>
                                        {sedes.map(s => (
                                            <option key={s.id} value={s.id}>{s.nombre_sede} ({s.empresa?.nombre})</option>
                                        ))}
                                    </select>
                                    {errors.sede_id && <p className="text-red-500 text-[10px] font-bold mt-1 ml-2 uppercase italic">{errors.sede_id}</p>}
                                </div>

                                <div className="space-y-2">
                                    <label className="text-[10px] font-black text-white/30 uppercase tracking-[0.3em] italic ml-2">Fecha del Evento</label>
                                    <input id="field_6" name="field_6" 
                                        type="date" 
                                        value={data.fecha_ocurrencia}
                                        onChange={e => setData('fecha_ocurrencia', e.target.value)}
                                        className="w-full bg-black/40 border border-white/5 rounded-2xl px-6 py-4 text-xs font-bold text-white focus:ring-2 focus:ring-red-600 transition-all uppercase italic [color-scheme:dark]"
                                        required
                                    />
                                </div>
                            </div>

                            {/* SELECTOR DE TIPO Y GRAVEDAD */}
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-8 pt-4 border-t border-white/5">
                                <div className="space-y-4">
                                    <label className="text-[10px] font-black text-white/30 uppercase tracking-[0.3em] italic ml-2">Tipo de Suceso</label>
                                    <div className="grid grid-cols-3 gap-3">
                                        {['accidente', 'incidente', 'cuasi_accidente'].map(t => (
                                            <button
                                                key={t}
                                                type="button"
                                                onClick={() => setData('tipo', t)}
                                                className={`px-4 py-3 rounded-xl text-[9px] font-black uppercase italic tracking-widest border transition-all ${
                                                    data.tipo === t 
                                                    ? 'bg-blue-600/20 border-blue-500 text-blue-400' 
                                                    : 'bg-white/5 border-white/5 text-white/40 hover:bg-white/10'
                                                }`}
                                            >
                                                {t.replace('_', ' ')}
                                            </button>
                                        ))}
                                    </div>
                                </div>

                                <div className="space-y-4">
                                    <label className="text-[10px] font-black text-white/30 uppercase tracking-[0.3em] italic ml-2 text-red-500">Nivel de Gravedad</label>
                                    <div className="grid grid-cols-4 gap-2">
                                        {gravityOptions.map(opt => (
                                            <button
                                                key={opt.id}
                                                type="button"
                                                onClick={() => setData('gravedad', opt.id)}
                                                title={opt.desc}
                                                className={`h-12 rounded-xl border transition-all flex flex-col items-center justify-center gap-1 ${
                                                    data.gravedad === opt.id 
                                                    ? `${opt.color} border-white shadow-lg scale-105` 
                                                    : 'bg-white/5 border-white/5 opacity-40 hover:opacity-100'
                                                }`}
                                            >
                                                <span className="text-[8px] font-black text-white italic">{opt.label}</span>
                                            </button>
                                        ))}
                                    </div>
                                    <p className="text-[8px] font-black text-white/20 uppercase tracking-widest italic text-center">
                                        {gravityOptions.find(o => o.id === data.gravedad).desc}
                                    </p>
                                </div>
                            </div>
                        </div>

                        {/* SECCIÓN 2: DETALLES OPERATIVOS */}
                        <div className="bg-[#16162a]/40 backdrop-blur-xl border border-white/5 rounded-[2.5rem] p-8 lg:p-10 space-y-8 shadow-2xl">
                            <div className="space-y-2">
                                <label className="text-[10px] font-black text-white/30 uppercase tracking-[0.3em] italic ml-2">Descripción Detallada</label>
                                <textarea id="field_5" name="field_5" 
                                    rows="4"
                                    value={data.descripcion}
                                    onChange={e => setData('descripcion', e.target.value)}
                                    placeholder="DESCRIBE EXACTAMENTE QUÉ SUCEDIÓ, CÓMO Y DÓNDE..."
                                    className="w-full bg-black/40 border border-white/5 rounded-[2rem] px-8 py-6 text-xs font-bold text-white placeholder-white/5 focus:ring-2 focus:ring-red-600 transition-all uppercase italic resize-none"
                                    required
                                ></textarea>
                            </div>

                            <div className="space-y-2">
                                <label className="text-[10px] font-black text-white/30 uppercase tracking-[0.3em] italic ml-2 text-emerald-500">Medidas Preventivas Tomadas</label>
                                <textarea id="field_4" name="field_4" 
                                    rows="3"
                                    value={data.medidas_tomadas}
                                    onChange={e => setData('medidas_tomadas', e.target.value)}
                                    placeholder="ACCIONES INMEDIATAS REALIZADAS PARA MITIGAR EL RIESGO..."
                                    className="w-full bg-black/40 border border-white/5 rounded-[2rem] px-8 py-6 text-xs font-bold text-white placeholder-white/5 focus:ring-2 focus:ring-emerald-600 transition-all uppercase italic resize-none"
                                    required
                                ></textarea>
                            </div>

                            <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div className="space-y-2">
                                    <label className="text-[10px] font-black text-white/30 uppercase tracking-[0.3em] italic ml-2">Personas Afectadas</label>
                                    <input id="field_3" name="field_3" 
                                        type="number" 
                                        min="0"
                                        value={data.personas_afectadas}
                                        onChange={e => setData('personas_afectadas', e.target.value)}
                                        className="w-full bg-black/40 border border-white/5 rounded-2xl px-6 py-4 text-xs font-bold text-white focus:ring-2 focus:ring-red-600 transition-all italic"
                                        required
                                    />
                                </div>
                                <div className="space-y-2">
                                    <label className="text-[10px] font-black text-white/30 uppercase tracking-[0.3em] italic ml-2 text-red-500">Días Perdidos Estimados</label>
                                    <input id="field_2" name="field_2" 
                                        type="number" 
                                        min="0"
                                        value={data.dias_perdidos}
                                        onChange={e => setData('dias_perdidos', e.target.value)}
                                        className="w-full bg-black/40 border border-white/5 rounded-2xl px-6 py-4 text-xs font-bold text-white focus:ring-2 focus:ring-red-600 transition-all italic"
                                    />
                                </div>
                            </div>
                        </div>

                        {/* SECCIÓN 3: EVIDENCIA VISUAL */}
                        <div className="bg-[#16162a]/40 backdrop-blur-xl border border-white/5 rounded-[2.5rem] p-8 lg:p-10 space-y-8 shadow-2xl">
                             <div className="flex items-center justify-between px-2">
                                <label className="text-[10px] font-black text-white/30 uppercase tracking-[0.3em] italic">Evidencias Fotográficas</label>
                                <button 
                                    type="button"
                                    onClick={() => fileInputRef.current.click()}
                                    className="px-6 py-3 bg-white/5 hover:bg-white/10 border border-white/10 rounded-2xl text-[9px] font-black uppercase tracking-widest italic transition-all"
                                >
                                    + AÑADIR FOTOS
                                </button>
                                <input id="field_1" name="field_1" 
                                    type="file" 
                                    multiple 
                                    accept="image/*"
                                    ref={fileInputRef}
                                    onChange={handleFileChange}
                                    className="hidden"
                                />
                            </div>

                            <div className="grid grid-cols-2 sm:grid-cols-3 gap-4">
                                {previews.map((src, idx) => (
                                    <div key={idx} className="relative aspect-square rounded-3xl overflow-hidden border border-white/10 group">
                                        <img src={src} className="w-full h-full object-cover" />
                                        <button 
                                            type="button"
                                            onClick={() => removeFoto(idx)}
                                            className="absolute top-2 right-2 w-8 h-8 rounded-full bg-red-600 text-white flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity"
                                        >
                                            ✕
                                        </button>
                                    </div>
                                ))}
                                {previews.length === 0 && (
                                    <div 
                                        onClick={() => fileInputRef.current.click()}
                                        className="col-span-full py-12 border-2 border-dashed border-white/5 rounded-[2rem] flex flex-col items-center justify-center gap-4 cursor-pointer hover:bg-white/[0.02] transition-colors"
                                    >
                                        <svg className="w-8 h-8 text-white/10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        <span className="text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic">Toca para subir evidencias</span>
                                    </div>
                                )}
                            </div>
                        </div>

                        {/* BOTÓN FINAL */}
                        <div className="flex flex-col gap-4">
                            <button
                                type="submit"
                                disabled={processing}
                                className="w-full py-6 bg-red-600 hover:bg-red-700 disabled:opacity-50 text-white rounded-[2rem] text-sm font-black uppercase italic tracking-[0.3em] shadow-2xl shadow-red-600/30 transition-all hover:-translate-y-1 active:scale-95 flex items-center justify-center gap-4"
                            >
                                {processing ? 'PROCESANDO REPORTE CRÍTICO...' : (
                                    <>
                                        <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2.5" d="M5 13l4 4L19 7"/></svg>
                                        FIRMAR Y ENVIAR REPORTE
                                    </>
                                )}
                            </button>
                            <Link 
                                href={visit_id ? route('tecnico.visits.in-progress', visit_id) : route('tecnico.incidentes.index')}
                                className="w-full py-4 text-center text-white/20 hover:text-white transition-colors text-[10px] font-black uppercase tracking-[0.4em] italic"
                            >
                                Descartar y volver
                            </Link>
                        </div>

                    </form>
                </div>
            </div>

            <style>{`
                .animate-fade-in { animation: fade-in 0.6s ease-out; }
                @keyframes fade-in { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
            `}</style>
        </AuthenticatedLayout>
    );
}
