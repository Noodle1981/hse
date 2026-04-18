import { Head, useForm, Link } from '@inertiajs/react';
import AuthenticatedLayout from '../../../Layouts/AuthenticatedLayout';

export default function Builder({ sedes }) {
    const form = useForm({
        title: '',
        description: '',
        duration: 30,
        type: 'Seguridad e Higiene',
    });

    const submit = (e) => {
        e.preventDefault();
        form.post(route('gestor.trainings.store'));
    };

    const trainingTypes = [
        'Seguridad e Higiene',
        'Prevención de Incendios',
        'Primeros Auxilios',
        'Ergonomía',
        'Uso de EPP',
        'Riesgo Eléctrico',
        'Protocolo COVID-19',
        'Otro'
    ];

    return (
        <AuthenticatedLayout title="Constructor de Formación">
            <Head title="Nueva Plantilla de Capacitación" />

            <div className="px-4 sm:px-6 lg:px-8 py-8 text-white selection:bg-orange-500/30">
                <div className="max-w-4xl mx-auto space-y-8 animate-fade-in-up">
                    
                    <div className="bg-[#16162a]/40 backdrop-blur-md rounded-[3rem] border border-white/5 shadow-3xl overflow-hidden relative">
                        <div className="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-transparent via-orange-500 to-transparent"></div>
                        
                        <div className="px-10 py-8 border-b border-white/5 bg-white/5 flex items-center justify-between">
                            <h3 className="text-xl font-black text-white italic uppercase tracking-widest flex items-center gap-4">
                                <span className="w-2 h-8 bg-orange-500 rounded-full shadow-[0_0_15px_rgba(246,146,57,0.5)]"></span>
                                Diseño de Plantilla Maestro
                            </h3>
                            <Link href={route('gestor.trainings.index')} className="text-[10px] font-black text-white/20 hover:text-white uppercase tracking-[0.3em] font-black italic transition-all">Cancelar x</Link>
                        </div>

                        <form onSubmit={submit} className="p-10 space-y-8">
                            <div className="space-y-6">
                                <div className="relative group">
                                    <span className="absolute -top-2.5 left-4 bg-[#11111d] px-2 text-[8px] font-black text-orange-400 uppercase tracking-[0.3em] italic z-10">Título del Tema</span>
                                    <input 
                                        type="text" 
                                        value={form.data.title}
                                        onChange={e => form.setData('title', e.target.value)}
                                        required
                                        className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-orange-500/50 transition-all uppercase italic"
                                        placeholder="EJ: PREVENCIÓN DE CAÍDAS EN ALTURA..."
                                    />
                                    {form.errors.title && <div className="text-red-500 text-[9px] mt-1 ml-4 font-black uppercase italic">{form.errors.title}</div>}
                                </div>

                                <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div className="relative group">
                                        <span className="absolute -top-2.5 left-4 bg-[#11111d] px-2 text-[8px] font-black text-orange-400 uppercase tracking-[0.3em] italic z-10">Categoría Técnica</span>
                                        <select 
                                            value={form.data.type}
                                            onChange={e => form.setData('type', e.target.value)}
                                            className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-orange-500/50 transition-all [color-scheme:dark] italic uppercase"
                                        >
                                            {trainingTypes.map(type => (
                                                <option key={type} value={type}>{type}</option>
                                            ))}
                                        </select>
                                    </div>
                                    <div className="relative group">
                                        <span className="absolute -top-2.5 left-4 bg-[#11111d] px-2 text-[8px] font-black text-orange-400 uppercase tracking-[0.3em] italic z-10">Duración Estimada (MIN)</span>
                                        <input 
                                            type="number" 
                                            value={form.data.duration}
                                            onChange={e => form.setData('duration', e.target.value)}
                                            className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-orange-500/50 transition-all italic"
                                        />
                                    </div>
                                </div>

                                <div className="relative group">
                                    <span className="absolute -top-2.5 left-4 bg-[#11111d] px-2 text-[8px] font-black text-orange-400 uppercase tracking-[0.3em] italic z-10">Descriptor Operativo</span>
                                    <textarea 
                                        value={form.data.description}
                                        onChange={e => form.setData('description', e.target.value)}
                                        rows="4"
                                        className="w-full bg-black/40 border border-white/10 rounded-[2rem] px-8 py-6 text-sm font-bold text-white focus:ring-2 focus:ring-orange-500/50 transition-all uppercase placeholder-white/5 italic"
                                        placeholder="DETALLE LOS OBJETIVOS Y CONTENIDOS DE LA CAPACITACIÓN..."
                                    ></textarea>
                                </div>
                            </div>

                            <div className="pt-8 flex justify-end">
                                <button 
                                    type="submit" 
                                    disabled={form.processing}
                                    className="px-16 py-5 bg-orange-600 text-white rounded-[2rem] text-[11px] font-black uppercase tracking-[0.2em] italic transition-all shadow-xl shadow-orange-600/20 hover:brightness-110 active:scale-95 disabled:opacity-50"
                                >
                                    {form.processing ? 'Registrando...' : 'Desplegar Plantilla'}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <style>{`
                .animate-fade-in-up { animation: fade-in-up 0.5s ease-out forwards; }
                @keyframes fade-in-up { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
            `}</style>
        </AuthenticatedLayout>
    );
}
