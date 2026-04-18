import { router, Head, useForm, usePage } from '@inertiajs/react';
import AuthenticatedLayout from '../../../Layouts/AuthenticatedLayout';
import { useState } from 'react';

export default function Start({ sedes }) {
    const { auth, flash } = usePage().props;
    
    const [searchTerm, setSearchTerm] = useState('');

    const form = useForm({
        sede_id: '',
    });

    const filteredSedes = sedes.filter(sede => 
        sede.nombre_sede.toLowerCase().includes(searchTerm.toLowerCase()) ||
        sede.empresa.nombre_fantasia.toLowerCase().includes(searchTerm.toLowerCase())
    );

    const submit = (e) => {
        e.preventDefault();
        form.post(route('gestor.operations.visits.store'));
    };

    return (
        <AuthenticatedLayout title="Zona de Despliegue">
            <Head title="Iniciar Visita" />

            <div className="px-4 sm:px-6 lg:px-8 py-8 text-white selection:bg-orange-500/30">
                <div className="max-w-4xl mx-auto space-y-8 animate-fade-in-up">
                    
                    <div className="bg-[#16162a]/40 backdrop-blur-md rounded-[3rem] border border-white/5 shadow-2xl relative overflow-hidden">
                        <div className="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-transparent via-orange-500 to-transparent"></div>
                        
                        <div className="px-10 py-8 border-b border-white/5 bg-white/5 flex items-center justify-between">
                            <h3 className="text-xl font-black text-white italic uppercase tracking-widest flex items-center gap-4">
                                <span className="w-2 h-8 bg-orange-500 rounded-full"></span>
                                Lanzador de Visita Táctica
                            </h3>
                            <button onClick={() => window.history.back()} className="text-[10px] font-black text-white/20 hover:text-white uppercase tracking-widest italic transition-all">Regresar</button>
                        </div>

                        <form onSubmit={submit} className="p-10 space-y-8">
                            <div className="space-y-6">
                                <div className="relative group">
                                    <span className="absolute -top-2.5 left-4 bg-[#11111d] px-2 text-[8px] font-black text-orange-400 uppercase tracking-[0.3em] italic z-10">Buscador de Objetivo</span>
                                    <input 
                                        type="text" 
                                        value={searchTerm}
                                        onChange={e => setSearchTerm(e.target.value)}
                                        placeholder="FILTRAR SEDE O EMPRESA..."
                                        className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-orange-500/50 transition-all placeholder-white/5 italic uppercase"
                                    />
                                </div>

                                <div className="space-y-4 max-h-[400px] overflow-y-auto pr-4 custom-scrollbar">
                                    {filteredSedes.map(sede => (
                                        <label 
                                            key={sede.id} 
                                            className={`flex items-center justify-between p-6 rounded-2xl border cursor-pointer transition-all ${form.data.sede_id == sede.id ? 'bg-orange-500/20 border-orange-500 shadow-[0_0_20px_rgba(246,146,57,0.2)]' : 'bg-black/20 border-white/5 hover:border-white/10'}`}
                                            onClick={() => form.setData('sede_id', sede.id)}
                                        >
                                            <div className="flex items-center gap-4">
                                                <div className={`w-12 h-12 rounded-xl flex items-center justify-center font-black italic shadow-lg ${form.data.sede_id == sede.id ? 'bg-orange-500 text-white' : 'bg-white/5 text-white/40'}`}>
                                                    {sede.empresa.nombre_fantasia.substring(0, 1)}
                                                </div>
                                                <div>
                                                    <div className={`text-sm font-black italic uppercase transition-colors ${form.data.sede_id == sede.id ? 'text-white' : 'text-white/60'}`}>{sede.nombre_sede}</div>
                                                    <div className="text-[9px] font-black text-white/20 uppercase tracking-widest italic">{sede.empresa.nombre_fantasia}</div>
                                                </div>
                                            </div>
                                            <input 
                                                type="radio" 
                                                name="sede" 
                                                value={sede.id} 
                                                checked={form.data.sede_id == sede.id}
                                                onChange={() => {}}
                                                className="sr-only" 
                                            />
                                            {form.data.sede_id == sede.id && (
                                                <div className="w-6 h-6 rounded-full bg-orange-500 flex items-center justify-center">
                                                    <svg className="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="3" d="M5 13l4 4L19 7"/></svg>
                                                </div>
                                            )}
                                        </label>
                                    ))}
                                    {filteredSedes.length === 0 && (
                                        <div className="py-20 text-center opacity-20">
                                            <p className="text-xs font-black uppercase tracking-widest italic">No se encontraron objetivos en este cuadrante</p>
                                        </div>
                                    )}
                                </div>
                            </div>

                            <div className="pt-8 flex justify-end">
                                <button 
                                    type="submit" 
                                    disabled={form.processing || !form.data.sede_id}
                                    className="px-16 py-5 bg-orange-600 text-white rounded-[2rem] text-[11px] font-black uppercase tracking-[0.2em] italic transition-all shadow-xl shadow-orange-600/20 hover:brightness-110 active:scale-95 disabled:opacity-30 disabled:cursor-not-allowed"
                                >
                                    {form.processing ? 'Lanzando...' : 'Iniciar Despliegue'}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <style>{`
                .animate-fade-in-up { animation: fade-in-up 0.5s ease-out forwards; }
                @keyframes fade-in-up { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
                .custom-scrollbar::-webkit-scrollbar { width: 4px; }
                .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
                .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.05); border-radius: 10px; }
            `}</style>
        </AuthenticatedLayout>
    );
}
