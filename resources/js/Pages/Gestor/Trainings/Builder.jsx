import { Head, useForm, Link } from '@inertiajs/react';
import AuthenticatedLayout from '../../../Layouts/AuthenticatedLayout';
import { useState, useEffect } from 'react';

export default function Builder({ sedes = [], empresas = [], tecnicos = [], checklists = [] }) {
    const form = useForm({
        title: '',
        description: '',
        duration: 60,
        type: 'presencial',
        empresa_id: '',
        sede_id: '',
        user_id: '',
        trainer_name: '',
        dynamic_checklist_id: '',
        date: '',
        status: 'scheduled',
        is_template: false,
    });

    const [filteredSedes, setFilteredSedes] = useState([]);

    useEffect(() => {
        if (form.data.empresa_id) {
            setFilteredSedes(sedes.filter(s => s.empresa_id == form.data.empresa_id));
        } else {
            setFilteredSedes([]);
        }
    }, [form.data.empresa_id]);

    const submit = (e) => {
        e.preventDefault();
        form.post(route('gestor.trainings.store'));
    };

    return (
        <AuthenticatedLayout title="Terminal de Inyección">
            <Head title="Nueva Capacitación" />

            <div className="px-4 sm:px-6 lg:px-8 py-10 text-white selection:bg-orange-500/30">
                <div className="max-w-5xl mx-auto space-y-10 animate-fade-in">
                    
                    <form onSubmit={submit} className="space-y-10">
                        {/* MÓDULO 1: IDENTIDAD & CONTENIDOS */}
                        <div className="bg-[#16162a]/40 backdrop-blur-md rounded-[3.5rem] border border-white/5 p-10 shadow-3xl relative overflow-hidden group">
                            <div className="absolute inset-0 bg-gradient-to-br from-indigo-500/5 to-transparent"></div>
                            <h3 className="text-xl font-black text-white italic mb-10 uppercase tracking-[0.3em] relative z-10 flex items-center gap-4">
                                <span className="w-2 h-8 bg-indigo-500 rounded-full shadow-[0_0_15px_rgba(99,102,241,0.5)]"></span>
                                Identidad & Contenidos
                            </h3>
                            
                            <div className="grid grid-cols-1 md:grid-cols-6 gap-8 relative z-10">
                                <div className="md:col-span-6 space-y-3 relative group">
                                    <span className="absolute -top-2.5 left-6 bg-[#11111d] px-2 text-[9px] font-black text-indigo-400 uppercase tracking-[0.4em] italic z-10">Protocolo Técnico Asociado</span>
                                    <select 
                                        value={form.data.dynamic_checklist_id}
                                        onChange={e => form.setData('dynamic_checklist_id', e.target.value)}
                                        className="w-full bg-black/40 border border-white/10 rounded-2xl px-8 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-indigo-500/50 transition-all [color-scheme:dark] italic uppercase"
                                    >
                                        <option value="">SIN PROTOCOLO ASOCIADO</option>
                                        {checklists.map(cl => (
                                            <option key={cl.id} value={cl.id}>{cl.title}</option>
                                        ))}
                                    </select>
                                </div>

                                <div className="md:col-span-6 space-y-3 relative group">
                                    <span className="absolute -top-2.5 left-6 bg-[#11111d] px-2 text-[9px] font-black text-white/30 uppercase tracking-[0.4em] italic z-10">Título Operativo</span>
                                    <input 
                                        type="text" 
                                        value={form.data.title}
                                        onChange={e => form.setData('title', e.target.value)}
                                        required
                                        className="w-full bg-black/40 border border-white/10 rounded-2xl px-8 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-indigo-500/50 transition-all uppercase italic"
                                        placeholder="EJ: SEGURIDAD EN ALTURAS, PREVENCIÓN DE INCENDIOS..."
                                    />
                                    {form.errors.title && <div className="text-red-500 text-[10px] mt-1 ml-6 font-black italic">{form.errors.title}</div>}
                                </div>

                                <div className="md:col-span-6 space-y-3 relative group">
                                    <span className="absolute -top-2.5 left-6 bg-[#11111d] px-2 text-[9px] font-black text-white/30 uppercase tracking-[0.4em] italic z-10">Objetivos Estratégicos</span>
                                    <textarea 
                                        value={form.data.description}
                                        onChange={e => form.setData('description', e.target.value)}
                                        rows="3"
                                        className="w-full bg-black/40 border border-white/10 rounded-3xl px-8 py-6 text-sm font-bold text-white focus:ring-2 focus:ring-indigo-500/50 transition-all uppercase italic placeholder-white/5"
                                        placeholder="DEFINA LOS CONTENIDOS TÁCTICOS A TRATAR..."
                                    ></textarea>
                                </div>
                            </div>
                        </div>

                        {/* MÓDULO 2: DESPLIEGUE & RESPONSABLES */}
                        <div className="bg-[#16162a]/40 backdrop-blur-md rounded-[3.5rem] border border-white/5 p-10 shadow-3xl relative overflow-hidden group">
                            <div className="absolute inset-0 bg-gradient-to-br from-orange-500/5 to-transparent"></div>
                            <h3 className="text-xl font-black text-white italic mb-10 uppercase tracking-[0.3em] relative z-10 flex items-center gap-4">
                                <span className="w-2 h-8 bg-orange-500 rounded-full shadow-[0_0_15px_rgba(246,146,57,0.5)]"></span>
                                Despliegue & Responsables
                            </h3>
                            
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-8 relative z-10">
                                <div className="space-y-3 relative group">
                                    <span className="absolute -top-2.5 left-6 bg-[#11111d] px-2 text-[9px] font-black text-orange-400 uppercase tracking-[0.4em] italic z-10">Empresa</span>
                                    <select 
                                        value={form.data.empresa_id}
                                        onChange={e => form.setData('empresa_id', e.target.value)}
                                        required
                                        className="w-full bg-black/40 border border-white/10 rounded-2xl px-8 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-orange-500/50 transition-all [color-scheme:dark] italic uppercase"
                                    >
                                        <option value="">ELEGIR EMPRESA...</option>
                                        {empresas.map(emp => (
                                            <option key={emp.id} value={emp.id}>{emp.nombre}</option>
                                        ))}
                                    </select>
                                </div>

                                <div className="space-y-3 relative group">
                                    <span className="absolute -top-2.5 left-6 bg-[#11111d] px-2 text-[9px] font-black text-orange-400 uppercase tracking-[0.4em] italic z-10">Sede de Despliegue</span>
                                    <select 
                                        value={form.data.sede_id}
                                        onChange={e => form.setData('sede_id', e.target.value)}
                                        disabled={!form.data.empresa_id}
                                        required
                                        className={`w-full bg-black/40 border border-white/10 rounded-2xl px-8 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-orange-500/50 transition-all [color-scheme:dark] italic uppercase ${!form.data.empresa_id ? 'opacity-20 cursor-not-allowed' : ''}`}
                                    >
                                        <option value="">ELEGIR SEDE...</option>
                                        {filteredSedes.map(s => (
                                            <option key={s.id} value={s.id}>{s.nombre_sede}</option>
                                        ))}
                                    </select>
                                </div>

                                <div className="space-y-3 relative group">
                                    <span className="absolute -top-2.5 left-6 bg-[#11111d] px-2 text-[9px] font-black text-orange-400 uppercase tracking-[0.4em] italic z-10">Responsable Técnico</span>
                                    <select 
                                        value={form.data.user_id}
                                        onChange={e => form.setData('user_id', e.target.value)}
                                        required
                                        className="w-full bg-black/40 border border-white/10 rounded-2xl px-8 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-orange-500/50 transition-all [color-scheme:dark] italic uppercase"
                                    >
                                        <option value="">ASIGNAR TÉCNICO...</option>
                                        {tecnicos.map(t => (
                                            <option key={t.id} value={t.id}>{t.apellidos}, {t.nombres}</option>
                                        ))}
                                    </select>
                                </div>

                                <div className="space-y-3 relative group">
                                    <span className="absolute -top-2.5 left-6 bg-[#11111d] px-2 text-[9px] font-black text-white/30 uppercase tracking-[0.4em] italic z-10">Instructor Externo</span>
                                    <input 
                                        type="text" 
                                        value={form.data.trainer_name}
                                        onChange={e => form.setData('trainer_name', e.target.value)}
                                        className="w-full bg-black/40 border border-white/10 rounded-2xl px-8 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-orange-500/50 transition-all uppercase italic"
                                        placeholder="NOMBRE DEL CAPACITADOR (OPCIONAL)..."
                                    />
                                </div>
                            </div>
                        </div>

                        {/* MÓDULO 3: CRONOGRAMA & MODALIDAD */}
                        <div className="bg-[#16162a]/40 backdrop-blur-md rounded-[3.5rem] border border-white/5 p-10 shadow-3xl relative overflow-hidden group">
                            <div className="absolute inset-0 bg-gradient-to-br from-amber-500/5 to-transparent"></div>
                            <h3 className="text-xl font-black text-white italic mb-10 uppercase tracking-[0.3em] relative z-10 flex items-center gap-4">
                                <span className="w-2 h-8 bg-amber-500 rounded-full shadow-[0_0_15px_rgba(245,158,11,0.5)]"></span>
                                Cronograma & Modalidad
                            </h3>
                            
                            <div className="grid grid-cols-1 md:grid-cols-4 gap-8 relative z-10">
                                <div className="space-y-3 relative group">
                                    <span className="absolute -top-2.5 left-6 bg-[#11111d] px-2 text-[9px] font-black text-amber-500 uppercase tracking-[0.4em] italic z-10">Modalidad</span>
                                    <select 
                                        value={form.data.type}
                                        onChange={e => form.setData('type', e.target.value)}
                                        className="w-full bg-black/40 border border-white/10 rounded-2xl px-8 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-amber-500/50 transition-all [color-scheme:dark] italic uppercase"
                                    >
                                        <option value="presencial">PRESENCIAL</option>
                                        <option value="virtual">VIRTUAL</option>
                                    </select>
                                </div>

                                <div className="space-y-3 relative group">
                                    <span className="absolute -top-2.5 left-6 bg-[#11111d] px-2 text-[9px] font-black text-amber-500 uppercase tracking-[0.4em] italic z-10">Fecha / Hora</span>
                                    <input 
                                        type="datetime-local" 
                                        value={form.data.date}
                                        onChange={e => form.setData('date', e.target.value)}
                                        required
                                        className="w-full bg-black/40 border border-white/10 rounded-2xl px-8 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-amber-500/50 transition-all [color-scheme:dark] italic uppercase"
                                    />
                                </div>

                                <div className="space-y-3 relative group">
                                    <span className="absolute -top-2.5 left-6 bg-[#11111d] px-2 text-[9px] font-black text-amber-500 uppercase tracking-[0.4em] italic z-10">Duración (MIN)</span>
                                    <input 
                                        type="number" 
                                        value={form.data.duration}
                                        onChange={e => form.setData('duration', e.target.value)}
                                        className="w-full bg-black/40 border border-white/10 rounded-2xl px-8 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-amber-500/50 transition-all italic uppercase"
                                    />
                                </div>

                                <div className="space-y-3 relative group">
                                    <span className="absolute -top-2.5 left-6 bg-[#11111d] px-2 text-[9px] font-black text-amber-500 uppercase tracking-[0.4em] italic z-10">Estado</span>
                                    <select 
                                        value={form.data.status}
                                        onChange={e => form.setData('status', e.target.value)}
                                        className="w-full bg-black/40 border border-white/10 rounded-2xl px-8 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-amber-500/50 transition-all [color-scheme:dark] italic uppercase"
                                    >
                                        <option value="scheduled">PROGRAMADA</option>
                                        <option value="completed">FINALIZADA</option>
                                        <option value="cancelled">CANCELADA</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        {/* COMANDOS DE ACCIÓN */}
                        <div className="flex items-center justify-end gap-6 pt-10 border-t border-white/5 mt-10">
                            <Link href={route('gestor.trainings.index')} 
                                  className="px-10 py-5 bg-white/5 border border-white/10 text-white/20 hover:text-white rounded-[2rem] text-[10px] font-black uppercase tracking-widest italic transition-all group/back">
                                <span className="group-hover/back:-translate-x-2 transition-transform inline-block">Abortar Misión</span>
                            </Link>
                            <button 
                                type="submit" 
                                disabled={form.processing}
                                className="px-16 py-5 bg-orange-600 text-white rounded-[2rem] text-[11px] font-black uppercase tracking-[0.2em] italic transition-all shadow-3xl shadow-orange-600/30 hover:brightness-110 active:scale-95 disabled:opacity-50 group/save relative overflow-hidden"
                            >
                                <div className="absolute inset-0 bg-white/20 translate-y-14 group-hover/save:translate-y-0 transition-transform duration-500"></div>
                                <span className="relative z-10 flex items-center gap-3">
                                    <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="3"><path d="M5 13l4 4L19 7"/></svg>
                                    {form.processing ? 'Registrando...' : 'Inyectar Capacitación'}
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <style>{`
                .animate-fade-in { animation: fade-in 0.4s ease-out forwards; }
                @keyframes fade-in { from { opacity: 0; } to { opacity: 1; } }
                input[type="datetime-local"]::-webkit-calendar-picker-indicator {
                    filter: invert(1) brightness(1.5);
                    cursor: pointer;
                    opacity: 0.3;
                    transition: opacity 0.3s;
                }
                input[type="datetime-local"]::-webkit-calendar-picker-indicator:hover { opacity: 1; }
            `}</style>
        </AuthenticatedLayout>
    );
}

