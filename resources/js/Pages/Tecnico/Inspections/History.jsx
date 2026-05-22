import { router, Head, Link, usePage } from '@inertiajs/react';
import AuthenticatedLayout from '../../../Layouts/AuthenticatedLayout';
import { useState, useEffect } from 'react';

export default function History({ inspections, filters, empresas = [] }) {
    const { auth } = usePage().props;
    
    const [empresa, setEmpresa] = useState(filters.empresa || '');
    const [sede, setSede] = useState(filters.sede || '');
    const [fechaInicio, setFechaInicio] = useState(filters.fecha_inicio || '');
    const [fechaFin, setFechaFin] = useState(filters.fecha_fin || '');

    const updateFilters = () => {
        router.get(route('tecnico.inspections.history'), { 
            empresa, 
            sede, 
            fecha_inicio: fechaInicio, 
            fecha_fin: fechaFin 
        }, {
            preserveState: true,
            replace: true,
        });
    };

    useEffect(() => {
        const timeout = setTimeout(() => {
            if (
                empresa !== (filters.empresa || '') || 
                sede !== (filters.sede || '') || 
                fechaInicio !== (filters.fecha_inicio || '') ||
                fechaFin !== (filters.fecha_fin || '')
            ) {
                updateFilters();
            }
        }, 500);
        return () => clearTimeout(timeout);
    }, [empresa, sede, fechaInicio, fechaFin]);

    // Compute available sedes based on selected empresa
    const availableSedes = empresa 
        ? (empresas.find(e => e.id.toString() === empresa.toString())?.sedes || [])
        : empresas.flatMap(e => e.sedes || []);

    const handleEmpresaChange = (val) => {
        setEmpresa(val);
        if (val) {
            const selectedEmp = empresas.find(emp => emp.id.toString() === val.toString());
            const hasSede = selectedEmp?.sedes.some(s => s.id.toString() === sede.toString());
            if (!hasSede) {
                setSede('');
            }
        }
    };

    const handleSedeChange = (val) => {
        setSede(val);
        if (val && !empresa) {
            const foundEmp = empresas.find(emp => emp.sedes.some(s => s.id.toString() === val.toString()));
            if (foundEmp) {
                setEmpresa(foundEmp.id.toString());
            }
        }
    };

    return (
        <AuthenticatedLayout title="Bóveda de Certificados">
            <Head title="Mis Inspecciones" />

            <div className="px-4 sm:px-6 lg:px-8 py-8 text-white selection:bg-orange-500/30">
                <div className="max-w-[100rem] mx-auto space-y-8 animate-fade-in">
                    
                    {/* HUB DE BÚSQUEDA */}
                    <div className="bg-[#16162a]/40 backdrop-blur-md rounded-[2.5rem] border border-white/5 p-6 shadow-2xl relative overflow-hidden animate-fade-in-down">
                        <div className="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-orange-500/30 to-transparent"></div>
                        
                        <div className="flex flex-col gap-6 relative z-10 pt-4">
                            <span className="absolute -top-10 left-6 bg-[#11111d] px-3 py-1 rounded-t-lg text-[9px] font-black text-orange-400 uppercase tracking-[0.3em] italic z-10 border border-b-0 border-white/5">Filtrar Mis Protocolos</span>
                            <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6 w-full">
                                <div className="flex flex-col gap-2">
                                    <label htmlFor="empresa" className="text-[10px] font-black text-orange-400 uppercase tracking-widest italic pl-2 leading-none">
                                        Empresa
                                    </label>
                                    <select id="empresa" name="empresa" 
                                        value={empresa}
                                        onChange={e => handleEmpresaChange(e.target.value)}
                                        className="w-full bg-[#11111d] border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-orange-500/50 transition-all italic uppercase cursor-pointer"
                                    >
                                        <option value="" className="bg-[#11111d] text-white">TODAS LAS EMPRESAS</option>
                                        {empresas.map(emp => (
                                            <option key={emp.id} value={emp.id} className="bg-[#11111d] text-white">
                                                {emp.nombre.toUpperCase()}
                                            </option>
                                        ))}
                                    </select>
                                </div>

                                <div className="flex flex-col gap-2">
                                    <label htmlFor="sede" className="text-[10px] font-black text-orange-400 uppercase tracking-widest italic pl-2 leading-none">
                                        Sede / Ubicación
                                    </label>
                                    <select id="sede" name="sede" 
                                        value={sede}
                                        onChange={e => handleSedeChange(e.target.value)}
                                        className="w-full bg-[#11111d] border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-orange-500/50 transition-all italic uppercase cursor-pointer"
                                    >
                                        <option value="" className="bg-[#11111d] text-white">TODAS LAS SEDES</option>
                                        {availableSedes.map(s => (
                                            <option key={s.id} value={s.id} className="bg-[#11111d] text-white">
                                                {s.localidad 
                                                    ? `${s.nombre_sede.toUpperCase()} - ${s.localidad.toUpperCase()}`
                                                    : s.nombre_sede.toUpperCase()
                                                }
                                            </option>
                                        ))}
                                    </select>
                                </div>

                                <div className="flex flex-col gap-2">
                                    <label htmlFor="fecha_inicio" className="text-[10px] font-black text-orange-400 uppercase tracking-widest italic pl-2 leading-none">
                                        Fecha Desde
                                    </label>
                                    <input id="fecha_inicio" name="fecha_inicio" 
                                        type="date" 
                                        value={fechaInicio}
                                        onChange={e => setFechaInicio(e.target.value)}
                                        className="w-full bg-[#11111d] border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-orange-500/50 transition-all italic uppercase [color-scheme:dark]"
                                    />
                                </div>

                                <div className="flex flex-col gap-2">
                                    <label htmlFor="fecha_fin" className="text-[10px] font-black text-orange-400 uppercase tracking-widest italic pl-2 leading-none">
                                        Fecha Hasta
                                    </label>
                                    <input id="fecha_fin" name="fecha_fin" 
                                        type="date" 
                                        value={fechaFin}
                                        onChange={e => setFechaFin(e.target.value)}
                                        className="w-full bg-[#11111d] border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-orange-500/50 transition-all italic uppercase [color-scheme:dark]"
                                    />
                                </div>
                            </div>

                            <div className="flex items-center justify-end">
                                <span className="text-[10px] font-black text-white/20 uppercase tracking-widest italic">Protocolos Certificados: {inspections.total}</span>
                            </div>
                        </div>
                    </div>

                    {/* Tabla de Historial */}
                    <div className="bg-[#16162a]/60 backdrop-blur-md rounded-[3rem] border border-white/5 shadow-2xl relative overflow-hidden animate-fade-in-up">
                        <div className="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-orange-500/20 to-transparent"></div>
                        
                        <div className="overflow-x-auto">
                            <table className="w-full border-collapse min-w-[1000px]">
                                <thead>
                                    <tr className="bg-black/20 text-left">
                                        <th className="px-10 py-6 text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic leading-none">Fecha / Hora</th>
                                        <th className="px-6 py-6 text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic leading-none">Tipo de Matriz</th>
                                        <th className="px-6 py-6 text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic leading-none">Sede / Unidad</th>
                                        <th className="px-10 py-6 text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic leading-none text-right">Comandos</th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-white/5">
                                    {inspections.data.map(ins => (
                                        <tr key={ins.id} className="group hover:bg-white/5 transition-all duration-300">
                                            <td className="px-10 py-6">
                                                <div className="text-sm font-black text-white italic uppercase tracking-tighter">
                                                    {new Date(ins.completed_at).toLocaleDateString()}
                                                </div>
                                                <div className="text-[9px] font-black text-white/20 uppercase tracking-widest mt-1 italic">
                                                    {new Date(ins.completed_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})} HRS
                                                </div>
                                            </td>
                                            <td className="px-6 py-6">
                                                <div className="flex items-center gap-4">
                                                    <div className="w-10 h-10 rounded-xl bg-orange-500/10 border border-orange-500/20 flex items-center justify-center text-orange-500">
                                                        <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" strokeWidth="2"/></svg>
                                                    </div>
                                                    <div>
                                                        <div className="text-sm font-black text-white italic uppercase group-hover:text-orange-400 transition-colors">{ins.checklist?.title}</div>
                                                        <div className="text-[9px] font-black text-white/20 uppercase tracking-widest mt-1 italic">Protocolo: {ins.checklist?.registry_code || 'S/N'}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td className="px-6 py-6">
                                                <div className="text-sm font-black text-white/60 italic uppercase tracking-wider">{ins.sede?.nombre_sede}</div>
                                                <div className="text-[9px] font-black text-white/20 uppercase tracking-widest mt-1 italic">Región Operativa</div>
                                            </td>
                                            <td className="px-10 py-6 text-right">
                                                <div className="flex items-center justify-end gap-3 transition-all duration-300">
                                                    {ins?.id && (
                                                        <a href={route('gestor.inspections.pdf.preview', { id: ins.id })} target="_blank" className="h-10 w-10 bg-red-500/10 border border-red-500/20 text-red-500 hover:bg-red-500 hover:text-white rounded-xl flex items-center justify-center transition-all shadow-lg">
                                                            <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" strokeWidth="2"/></svg>
                                                        </a>
                                                    )}
                                                </div>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>

                        {/* Pagination */}
                        <div className="px-10 py-6 border-t border-white/5 bg-black/20 flex items-center justify-between font-black italic text-[10px]">
                            <div className="text-white/20">Matrices Cerradas: {inspections.total}</div>
                            <div className="flex gap-2">
                                {inspections.links.map((link, i) => (
                                    <Link 
                                        key={i}
                                        href={link.url || '#'}
                                        dangerouslySetInnerHTML={{ __html: link.label }}
                                        className={`px-4 py-2 rounded-xl border ${link.active ? 'bg-orange-500/10 border-orange-500/30 text-orange-400' : 'bg-white/5 border-white/5 text-white/20 hover:text-white'} ${!link.url && 'opacity-20 cursor-not-allowed'}`}
                                    />
                                ))}
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
